<?php
/**
 * @copyright Copyright 2005-2010 RedIRIS, http://www.rediris.es/
 *
 * This file is part of phpPoA2.
 *
 * phpPoA2 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * phpPoA2 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with phpPoA2. If not, see <http://www.gnu.org/licenses/>.
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @version 2.0
 * @author Jaime Perez <jaime.perez@rediris.es>
 * @filesource
 */

/**
 * @ignore
 */
set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__).
                                    PATH_SEPARATOR.dirname(__FILE__)."/messages".
                                    PATH_SEPARATOR.dirname(__FILE__)."/lib".
                                    PATH_SEPARATOR.dirname(__FILE__)."/lib/db".
                                    PATH_SEPARATOR.dirname(__FILE__)."/lib/crypto".
                                    PATH_SEPARATOR.dirname(__FILE__)."/lib/authn".
                                    PATH_SEPARATOR.dirname(__FILE__)."/lib/authz");

require_once("definitions.php");
require_once("PoAUtils.php");
require_once("PoAEventHandler.php");
include_once("AutoPoA.php");
include_once("LitePoA.php");


define('POA_CONF_FILE', dirname(__FILE__).'/PoA.conf');

/**
 * Standard class that implements all the functionallity of the phpPoA.
 * @package phpPoA2
 */
class PoA {
    protected $local_site;
    protected $cfg;
    protected $log;
    protected $authn_engine;
    protected $attributes;
    protected $authz_engines;
    protected $db_manager;
    protected $autoload;
    protected $handler;

    /**
     * Main constructor. Configures the PoA and performs initialization.
     * @param site The identifier to determine which configuration to apply.
     * @param file The path to the configuration file.
     */
    public function __construct($site, $file = null) {
        $this->local_site = $site;

        // manage generic session
        if (!isset($_COOKIE[$site.'_session'])) {
            $id = mt_rand();
            @setcookie($site.'_session', $id);
            $_COOKIE[$site.'_session'] = $id;
        }

        $this->handler = new PoAEventHandler($site);

        // register autoload function
        spl_autoload_register(array($this->handler,"autoloadHandler"));

        // configure
        try {           
            $this->cfg = new PoAConfigurator($site, $file);

        } catch (Exception $e) { // unrecoverable!!        
            // we have no logging, so do our best here
            // put a message in the error log and in STDOUT and exit
            error_log($e);
            $this->handler->abort(E_USER_ERROR, $e);
        }

        // initialize logger
        $this->log = new PoALog($this->cfg->getLogLevel(), $this->cfg->getLogFile());

        // initialize error handling
        $this->handler->setDebug($this->cfg->isDebug());
        $this->handler->setLogger($this->log);
        set_exception_handler(array($this->handler, "exceptionHandler"));
        set_error_handler(array($this->handler, "errorHandler"));

        // load authentication engine
        $engine = $this->cfg->getAuthnEngine();
        if (class_exists($engine)) {
            
            $this->authn_engine = new $engine($this->cfg->getAuthnEngineConfFile(), $site);
            $this->authn_engine->setHandler($this->handler);
        }

        // load authorization engines
        $engines = $this->cfg->getAuthzEngines();
        foreach ($engines as $engine) {
            $this->authz_engines[$engine] = new $engine($this->cfg->getAuthzEngineConfFile($engine), $site);
            $this->authz_engines[$engine]->setHandler($this->handler);
        }

        $this->clean();
    }

    protected function clean() {
        // clean
        restore_exception_handler();
        restore_error_handler();
    }

    /**
     * Attach a hook object to the appropriate entry point of the available
     * authentication or authorization engines.
     * @param name The name of the hook. Refer to each individual engine
     * for a complete list of available hooks.
     * @param hook A hook object with the function or method to attach.
     * @return true if the hook was successfully attached, false otherwise.
     */
    public function addHook($name, $hook) {
        // register autoload function
        set_exception_handler(array($this->handler, "exceptionHandler"));
        set_error_handler(array($this->handler, "errorHandler"));

        // add hook for authentication engine
        $result = $this->authn_engine->addHook($name, $hook);

        // add hook for authorization engines
        foreach ($this->authz_engines as $engine) {
            $result |= $engine->addHook($name, $hook);
        }

        $this->clean();
        return $result;
    }

    /**
     * Remove a hook from the specified entry point of the available
     * authentication or authorization engines.
     * @param name The name of the hook. Refer to each individual engine
     * for a complete list of available hooks.
     * @param hook The hook object which shall be removed.
     * @return true if the hook was successfully removed, false otherwise.
     */
    public function removeHook($name, $hook) {
        // register autoload function
        set_exception_handler(array($this->handler, "exceptionHandler"));
        set_error_handler(array($this->handler, "errorHandler"));

        // remove hook from authentication engine
        $result = $this->authn_engine->removeHook($name, $hook);

        // remove hook from authorization engines
        foreach ($this->authz_engines as $engine) {
            $result |= $engine->removeHook($name, $hook);
        }

        $this->clean();
        return $result;
    }

    /****************************
     * Authentication interface *
     ****************************/

    /**
     * Perform a federated login for the user.
     * @return AUTHN_SUCCESS if authentication succeeds, AUTHN_FAILED in
     * any other case.
     */
    public function authenticate() {
        // register autoload function
        set_exception_handler(array($this->handler, "exceptionHandler"));
        set_error_handler(array($this->handler, "errorHandler"));

        // check if we have an authentication engine configured
        if (empty($this->authn_engine)) {
            trigger_error(PoAUtils::msg('authn-engine-err', array()), E_USER_WARNING);
            $this->clean();
            return AUTHN_FAILED;
        }

        trigger_error(PoAUtils::msg("authenticating-via", array($this->cfg->getAuthnEngine())));

        $result = false;
        try {
            $result = $this->authn_engine->authenticate();
        } catch (PoAException $e) {
            trigger_error($e, E_USER_WARNING);
        }
        if ($result) {
            trigger_error(PoAUtils::msg('authn-success', array($this->cfg->getAuthnEngine())), E_USER_WARNING);
        } else {
            trigger_error(PoAUtils::msg('authn-err', array()), E_USER_WARNING);
        }

        $this->clean();
	return $result;
    }

    /**
     * Query the current status of the user in the federation.
     * @return AUTHN_SUCCESS if authentication succeeded, AUTHN_FAILED in
     * any other case.
     */
    public function isAuthenticated() {
        // register autoload function
        set_exception_handler(array($this->handler, "exceptionHandler"));
        set_error_handler(array($this->handler, "errorHandler"));

        // check if we have an authentication engine configured
        if (empty($this->authn_engine)) {
            trigger_error(PoAUtils::msg('authn-engine-err', array()), E_USER_WARNING);
            $this->clean();
            return AUTHN_FAILED;
        }

        trigger_error(PoAUtils::msg("check-authn-status", array($this->cfg->getAuthnEngine())));

        $result = $this->authn_engine->isAuthenticated();
        if ($result) {
            trigger_error(PoAUtils::msg('authn-success', array($this->cfg->getAuthnEngine())), E_USER_WARNING);
        } else {
            trigger_error(PoAUtils::msg('authn-err', array()), E_USER_WARNING);
        }

        $this->clean();
	return $result;
    }

    /**
     * Retrieve the attributes provided by the user when logged in.
     * @return an associative array containing all attributes.
     */
    public function getAttributes() {
        // register autoload function
        set_exception_handler(array($this->handler, "exceptionHandler"));
        set_error_handler(array($this->handler, "errorHandler"));

        // check if we have an authentication engine configured
        if (empty($this->authn_engine)) {
            trigger_error(PoAUtils::msg('authn-engine-err', array()), E_USER_WARNING);
            $this->clean();
            return array();
        }

        $this->clean();
        return $this->authn_engine->getAttributes();
    }

    /**
     * Get the value (or values) of an attribute, if present.
     * @param name The name of the attribute.
     * @param namespace The namespace of the attribute, if any.
     * @return the attribute value or an array containing all values.
     * Null in any other case.
     */
    public function getAttribute($name, $namespace) {
        // register autoload function
        set_exception_handler(array($this->handler, "exceptionHandler"));
        set_error_handler(array($this->handler, "errorHandler"));

        // check if we have an authentication engine configured
        if (empty($this->authn_engine)) {
            trigger_error(PoAUtils::msg('authn-engine-err', array()), E_USER_WARNING);
            $this->clean();
            return null;
        }

        $this->clean();
        return $this->authn_engine->getAttribute($name, $namespace);
    }

    /**
     * Remove the user's session and trigger a logout for the specified authentication
     * protocol.
     * @param slo Whether to perform a Single Log Out or a local logout.
     * @return true if success, false in any other case.
     */
    public function logout($slo) {
        // register autoload function
        set_exception_handler(array($this->handler, "exceptionHandler"));
        set_error_handler(array($this->handler, "errorHandler"));

        // check if we have an authentication engine configured
        if (empty($this->authn_engine)) {
            trigger_error(PoAUtils::msg('authn-engine-err', array()), E_USER_WARNING);
            $this->clean();
            return AUTHN_FAILED;
        }

        $this->clean();
        return $this->authn_engine->logout($slo);
    }

    /***************************
     * Authorization interface *
     ***************************/

    /**
     * Perform authorization for the a given subject.
     * Multiple authorization engines are supported, so
     * authorization will succeed if any of these succeeds.
     * @param user The subject queried.
     * @param attrs The attributes of the user.
     * @param engine The authorization engine(s) to use. All engines are used if none specified.
     * If more than one engine should be checked then this must be an array.
     * @return AUTHZ_SUCCESS if any of the supported (or selected) engines succeeds or if no
     * authorization engine is configured. AUTHZ_FAILED if all the engines fail.
     */
    public function isAuthorized($user, $attrs, $engine = null) {
        // register autoload function
        set_exception_handler(array($this->handler, "exceptionHandler"));
        set_error_handler(array($this->handler, "errorHandler"));

        // bypass if no authorization engine
        if (empty($engine) && empty($this->authz_engines))
            return true;

        $result = false;
        // check specific engines
        if (!empty($engine)) {
            $engines = $engine;
            if (!is_array($engine)) $engines = array($engine);

            // iterate over engines
            foreach ($engines as $e) {
                if (!isset($this->authz_engines[$e])) {
                    trigger_error(PoAUtils::msg("authz-engine-err", array($e)), E_USER_ERROR);
                }
                trigger_error(PoAUtils::msg("query-authz-via", array($e)));
                $result |= $this->authz_engines[$e]->isAuthorized($user, $attrs);
            }
        // check all configured engines
        } else {
            trigger_error(PoAUtils::msg("query-authz", array()));
            // iterate over engines
            foreach ($this->authz_engines as $e) {
                $result |= $e->isAuthorized($user, $attrs);
            }
        }

        if ($result) {
            trigger_error(PoAUtils::msg('user-authz-ok', array($user)), E_USER_WARNING);
        } else {
            trigger_error(PoAUtils::msg('user-authz-err', array($user)), E_USER_WARNING);
        }

        $this->clean();
        return $result;
    }

    /**
     * Authorize a given subject with the data retrieved from federated login.
     * Multiple authorization engines are supported, so
     * authorization will be done in all of them.
     * @param user The subject of authorization.
     * @param attrs The attributes of the user.
     * @param reference An internal reference that may be valuable for the engine, tipically
     * referring to a previous invitation or similar.
     * @param expires The time (POSIX) when authorization will expire. Use 0 if authorization
     * should never expire. Defaults to 0.
     * @param engine The authorization engine(s) to use. All engines are used if none specified.
     * If more than one engine should be checked then this must be an array.
     * @return AUTHZ_SUCCESS if any of the supported engines succeeds or if no
     * authorization engine is configured. AUTHZ_FAILED if all the engines fail.
     */
    public function authorize($user, $attrs, $reference = null, $expires = 0, $engine = null) {
        // register autoload function
        set_exception_handler(array($this->handler, "exceptionHandler"));
        set_error_handler(array($this->handler, "errorHandler"));

        $result = false;
        // check specific engines
        if (!empty($engine)) {
            $engines = $engine;
            if (!is_array($engine)) $engines = array($engine);
 
            // iterate over engines
            foreach ($engines as $e) {
                if (!isset($this->authz_engines[$e])) {
                    trigger_error(PoAUtils::msg("authz-engine-err", array($e)), E_USER_ERROR);
                }
                trigger_error(PoAUtils::msg("authorize-user-via", array($user, $e)));
                $result |= $this->authz_engines[$e]->authorize($user, $attrs, $reference, $expires);
            }
        // check all configured engines
        } else {
            // iterate over engines
            foreach ($this->authz_engines as $name => $e) {
                trigger_error(PoAUtils::msg("authorize-user-via", array($user, $name)));
                $result |= $e->authorize($user, $attrs, $reference, $expires);
            }
        }

        $this->clean();
        return $result;
    }

    /**
     * Revoke authorization for a given subject identified by an e-mail.
     * @param mail The e-mail of the user.
     * @param engine The authorization engine(s) to use. All engines are used if none specified.
     * If more than one engine should be checked then this must be an array.
     * @return true if authorization is revoked correctly for all authorization
     * engines, false in any other case.
     */
    public function revoke($mail, $engine = null) {
        // register autoload function
        set_exception_handler(array($this->handler, "exceptionHandler"));
        set_error_handler(array($this->handler, "errorHandler"));

        $result = false;
        // check specific engines
        if (!empty($engine)) {
            $engines = $engine;
            if (!is_array($engine)) $engines = array($engine);

            // iterate over engines
            foreach ($engines as $e) {
                if (!isset($this->authz_engines[$e])) {
                    trigger_error(PoAUtils::msg("authz-engine-err", array($e)), E_USER_ERROR);
                }
                trigger_error(PoAUtils::msg("revoke-user-via", array($e)));
                $result |= $this->authz_engines[$e]->revoke($user);
            }
        // check all configured engines
        } else {
            trigger_error(PoAUtils::msg("revoke", array()));
            // iterate over engines
            foreach ($this->authz_engines as $e) {
                $result |= $e->revoke($user);
            }
        }

        $this->clean();
        return $result;
    }

    /**
     * Returns the authorization engines configured for the current PoA, or
     * the one specified.
     * @param engine The name of the authorization engine to retrieve.
     * If more than one engine should be returned then this must be an array.
     * @return The authorization engine(s) requested if it was previously configured.
     * If none was specified, all configured engines will be returned. An empty
     * array will be returned if no authorization engines were found.
     */
    public function getAuthorizationEngines($engine = null) {
        // register autoload function
        set_exception_handler(array($this->handler, "exceptionHandler"));
        set_error_handler(array($this->handler, "errorHandler"));

        $list = $this->authz_engines;
        // check specific engines
        if (!empty($engine)) {
            $list = array();
            $engines = $engine;
            if (!is_array($engine)) $engines = array($engine);

            // iterate over engines
            foreach ($engines as $e) {
                if (!isset($this->authz_engines[$e])) {
                    trigger_error(PoAUtils::msg("authz-engine-err", array($e)), E_USER_ERROR);
                }
                $list[$e] = $this->authz_engines[$e];
            }
        }

        $this->clean();
        return $list;
    }

    /**
     * Get the authorization levels that match for the user specified, according to the configuration.
     * An array with the names of the levels matching the user is returned. An empty array is
     * returned if no match is found.
     * @param user The user identifier.
     * @param attributes An array of attributes available for the user.
     * @return An array with the names of the levels matching the user, if any. An empty array will
     * be returned if no match. Exception will be raised if no levels are defined for this PoA.
     */
    public function getAuthorizationLevels($user, $attributes) {
        // register autoload function
        set_exception_handler(array($this->handler, "exceptionHandler"));
        set_error_handler(array($this->handler, "errorHandler"));

        $levels = $this->cfg->getAuthzLevels();
        $verified = array();
        if (empty($levels)) {
            trigger_error(PoAUtils::msg('authz-levels-err', array()), E_USER_ERROR);
        }

        foreach ($levels as $level) {
            // TODO
        }

        return $verified;
    }

}

?>
