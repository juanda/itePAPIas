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
 * Default assertion delimiters for standard PAPI 1.5 protocol.
 */
define("ATTR_SEPARATOR", ",");
define("VALUE_SEPARATOR", "|");
define("NAMEVALUE_SEPARATOR", "=");

/**
 * Supported database types.
 */
define('PAPI_DBA', 'PAPIDBADB');
define('PAPI_MYSQL', 'PAPIMySQLDB');
define('PAPI_SESSION', 'PAPISessionDB');

/**
 * Supported namespaces for attributes.
 */
define('NS_PAPI_PROTOCOL', 'urn:mace:rediris.es:papi:protocol');
define('NS_PAPI_ATTRIBUTES', 'urn:mace:rediris.es:papi:attributes');

/**
 * Prefix for operational attributes inside the protocol and special
 * attributes names.
 */
define('PROTO_ATTR_PREFIX', '_papi_');
define('PROTO_ATTR_AS_ID', '__asid');
define('PROTO_ATTR_KEY', '__key');
define('PROTO_ATTR_EXPIRE_TIME', '__expiretime');

/**
 * Default timeout for stored requests.
 */
define('REQUEST_LIFETIME', 300); // 5 minutes

/**
 * This hook is executed at the end of the method that returns the URL where to redirect a user.
 * It can be used to alter parameters in the URL. The hook receives an array of parameters which
 * should be directly modified. Functions for this hook must be defined like this:
 *
 * function redirectURLFinishHook(&$params);
 *
 * Please bear in mind that hooks must return TRUE or they'll keep other hooks from executing.
 */
define("PAPI_REDIRECT_URL_FINISH", "PAPI_REDIRECT_URL_FINISH");

/**
 * This hook is executed when a valid response is found from the AS/GPoA and the original request
 * of the user is about to be restored. It receives an array with the main PHP global variables
 * of the original context. Functions for this hook must be defined like this:
 *
 * function restoreOriginalRequestHook(&$env);
 *
 * Please bear in mind that hooks must return TRUE or they'll keep other hooks from executing. 
 */
define("PAPI_RESTORE_ORIGINAL_REQUEST", "PAPI_RESTORE_ORIGINAL_REQUEST");

/**
 * This hook is executed when a valid response is found from the AS/GPoA and the engine is about
 * to end the authentication result. It receives a boolean value that determines if the URL should
 * be cleaned by means of a redirection to the initial URL. Functions for this hook must be
 * defined like this:
 *
 * function cleanURLHook(&$clean);
 *
 * Please bear in mind that hooks must return TRUE or they'll keep other hooks from executing. 
 */
define("PAPI_CLEAN_URL", "PAPI_CLEAN_URL");

/**
 * This hook is executed when returning the attributes found for a user with getAttributes()
 * method. It receives a string with the attributes and the array that results of proccessing
 * the string. Functions for this hook must be defined like this:
 *
 * function attributeParser($assertion, &$attributes);
 *
 * Please bear in mind that hooks must return TRUE or they'll keep other hooks from executing.
 */
define("PAPI_ATTRIBUTE_PARSER", "PAPI_ATTRIBUTE_PARSER");

/**
 * Authentication engine for the PAPI 1.5 protocol.
 * PLEASE NOTE THAT THIS ENGINE WORKS ONLY FOR WEB-BASED APPLICATIONS.
 * @package phpPoA2
 * @subpackage PAPIAuthenticationEngine
 */
class PAPIAuthnEngine extends AuthenticationEngine {

    protected $assertion;
    protected $status;
    protected $expiration_time = false;
    protected $attributes;
    protected $as_id;
    protected $key;
    protected $lkey;
    protected $pkey;
    protected $global_expire_time;
    protected $db;
    protected $id;
    protected $cfg;
    protected $crypto;
    protected $clean_url = true;
    protected $skip_redirection = false;
    protected $cookie_name = "PAPILcook_";
    protected $enforcing = true;
    protected $opoa = "http";
    protected $valid_hooks = array(PAPI_REDIRECT_URL_FINISH,
                                   PAPI_RESTORE_ORIGINAL_REQUEST,
                                   PAPI_CLEAN_URL,
                                   PAPI_ATTRIBUTE_PARSER);

    public function configure($file,$section) {
        parent::configure($file, $section);

        // check requirements
        // check mcrypt extension
        if (!extension_loaded("mcrypt")) {
            trigger_error(PoAUtils::msg('extension-required', array("mcrypt")), E_USER_ERROR);
        }

        // set id
        $this->id = $section;

        // set cookie name
        $this->cookie_name .= $section;
        if (strpos($this->cookie_name, '.') != false) {
            $this->cookie_name = str_replace ( '.', '_' , $this->cookie_name );
        }

        // initialize cryptographic engine
        $this->crypto = new PAPICrypt($this->cfg->getLKey(), $this->cfg->getPubKeyFile());

        // set default OPOA
        if (isset($_SERVER['HTTPS'])) $this->opoa .= "s";
        $this->opoa .= "://".$_SERVER['SERVER_NAME'].$this->cfg->getLocation();

        // configure DB
        $db_t = $this->cfg->getDBType();
        if (class_exists($db_t, true)) {
            $this->db = new $db_t($this->cfg);
        }
    }

    public function authenticate() {
        // PAPI authentication protocol v1.0

        $action = @array_key_exists('ACTION', $_REQUEST) ? $_REQUEST['ACTION'] : "";
        $auth = (isset($_COOKIE[$this->cookie_name])) ? $this->testCookie() : false;

        // check if we have a cookie or coming back from AS/GPoA
        if ($action === "CHECKED" && !$auth) { // GPoA/AS response
            $data = $_REQUEST['DATA'];
            $key = $this->testResponse($data, $this->pkey);
            if (!$key) {
                $this->status = AUTHN_FAILED;
                $this->dirty = false;
                return AUTHN_FAILED;
            }
            $request = $this->loadRequest($key);
            if (!$request) {
                trigger_error(PoAUtils::msg('unknown-request', array()), E_USER_WARNING);
                $this->status = AUTHN_FAILED;
                $this->dirty = false;
                return AUTHN_FAILED;
            }
            $this->deleteRequest($key);
            if ($key) { // the request was validated
                if ($this->skip_redirection) {
                    $this->status = AUTHN_SUCCESS;
                    return AUTHN_SUCCESS;
                }

                // set a new cookie
                $c = $this->getNewCookie($this->assertion);
                if (setcookie($this->cookie_name, $c, 0, $this->cfg->getLocation(), $this->cfg->getCookieDomain(), 0)) {
                    $_COOKIE[$this->cookie_name] = $c;

                    // run hooks
                    $arg = array($this->clean_url);
                    $this->runHooks(PAPI_CLEAN_URL, $arg);
                    $this->clean_url = $arg[0];

                    // finish it off
                    if (!$this->clean_url) {
                        $this->status = AUTHN_SUCCESS;
                        return AUTHN_SUCCESS;
                    } else {
                        if ($_SERVER["REQUEST_METHOD"] === "POST") {
                            $inputs = "";

                            // build HTML with an input element for each element in the query string
                            parse_str($_SERVER["QUERY_STRING"], $params);
                            foreach ($params as $name => $value) {
                                $inputs .= "<input name='".$name."' type='hidden' value='".$value."' />";
                            }
                            $inputs .= "<input type='submit' value='".PoAUtils::msg('continue', array())."' />";

                            // print a HTML form to continue
?>
<html>
 <head>
  <title>phpPoA2 transaction in progress...</title>
 </head>
 <body onload="document.forms[0].submit();">
  <form action="<?php echo $_SERVER['SCRIPT_NAME'];?>" method="post">
   <?php echo $inputs; ?>
  </form>
 </body>
</html>
<?php
                            die(0);
                        } else {
                            // build redirect URL
                            $protocol = (!empty($_SERVER['HTTPS'])) ? "https://" : "http://";
                            $url = $protocol.$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'];
                            $url .= substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "ACTION=CHECKED") -1);
                            $this->redirect($url);
                        }
                    }
                } else { // can't set the cookie
                    trigger_error(PoAUtils::msg('cannot-set-cookie', array()), E_USER_WARNING);
                }
            } else { // the request is invalid!
                trigger_error(PoAUtils::msg('authn-err', array()), E_USER_WARNING);
            }
            $this->status = AUTHN_FAILED;
            return AUTHN_FAILED;
        } else if (!$auth) { // first time browser access (w/o cookie)
            if ($this->skip_redirection) {
                $this->status = AUTHN_FAILED;
                throw new PoAException('cookie-not-found', E_USER_ERROR, array($this->cookie_name));
            }
            trigger_error(PoAUtils::msg('cookie-not-found', array($this->cookie_name)), E_USER_WARNING);
            $this->deleteCookie();
            $this->redirect();
        } else { // valid access with cookie, update it!
            $c = $this->getNewCookie($this->assertion);
            if (setcookie($this->cookie_name, $c, 0, $this->cfg->getLocation(), $this->cfg->getCookieDomain(), 0)) {
                $_COOKIE[$this->cookie_name] = $c;
                // if no request found, assume this is a visit to a previously stored URL
                // (reloaded by the user or in the browser favorites/history).
                // Rebuild the request without the ACTION and DATA parameters

                // set protocol
                $protocol = (!empty($_SERVER['HTTPS'])) ? "https://" : "http://";                

                // rebuild location
                $re[] = "/ACTION=[^&]*&?/";
                $re[] = "/DATA=[^&]*&?/";
                $location = $protocol.$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].
                            rtrim(preg_replace($re,"",$_SERVER["REQUEST_URI"]),"&?");

                // run hooks
                $arg = array($this->clean_url);
                $this->runHooks(PAPI_CLEAN_URL, $arg);
                $this->clean_url = $arg[0];

                if ((isset($_REQUEST["ACTION"]) || isset ($_REQUEST["DATA"])) && !$this->skip_redirection && $this->clean_url) {
                    $this->redirect($location);
                }

            } else { // set cookie failed!
                trigger_error(PoAUtils::msg('cannot-set-cookie', array()), E_USER_WARNING);
            }

            $this->status = AUTHN_SUCCESS;
            return AUTHN_SUCCESS;
        }

        $this->status = AUTHN_FAILED;
        return AUTHN_FAILED;
    }

    public function isAuthenticated() {
        if ($this->isSafe()) {
            return $this->status;
        }

        $result = (isset($_COOKIE[$this->cookie_name])) ? $this->testCookie() : false;

        return $result;
    }

    public function getAttributes() {
        // avoid parsing the assertion again and again
        if (empty($this->attributes)) {
            $attrs = explode(ATTR_SEPARATOR, $this->assertion);
            foreach ($attrs as $attr) {
                @list($name, $value) = explode(NAMEVALUE_SEPARATOR, $attr);

                // discard operational attributes
                if (strpos($name, PROTO_ATTR_PREFIX) == 1) continue;

                if (!empty($value)) {
                    $values = explode(VALUE_SEPARATOR, $value);
                    if (count($values) > 1) {
                        $this->attributes[$name] = $values;
                    } else {
                        $this->attributes[$name] = $value;
                    }
                }
            }
        }

        $arg = array($this->assertion, $this->attributes);
        $this->runHooks(PAPI_ATTRIBUTE_PARSER, $arg);
        $this->attributes = $arg[1];

        return $this->attributes;
    }

    public function getAttribute($name, $namespace = NS_PAPI_ATTRIBUTES) {
        switch ($namespace) {
            case NS_PAPI_ATTRIBUTES:
                if (empty($this->attributes)) {
                    $this->getAttributes();
                }
                $attr = $this->attributes[$name];
                if (!isset($this->attributes[$name])) {
                    // attribute query
                    $attr = $this->attributeQuery($name);
                }
                break;
            case NS_PAPI_PROTOCOL:
                switch ($name) {
                    case PROTO_ATTR_AS_ID:
                        $attr = $this->as_id;
                        break;
                    case PROTO_ATTR_KEY:
                        $attr = $this->key;
                        break;
                    case PROTO_ATTR_EXPIRE_TIME:
                        $attr = $this->expiration_time;
                        break;
                    default:
                        $attr = @$this->attributes['_papi_'.$name];
                }
        }
        return $attr;
    }

    public function logout($slo = false) {
        // first check if we really need to logout!
        if (!$this->isAuthenticated()) {
            trigger_error(PoAUtils::msg('already-logged-out', array()), E_USER_NOTICE);
            return true;
        }

        // local logout only
        if (!$slo) {
            $this->deleteCookie();
            trigger_error(PoAUtils::msg('local-logout-success', array()), E_USER_NOTICE);
            return true;
        }

        // check configuration
        $rtype = $this->cfg->getRedirectType();
        if ($rtype === AS_T) {
            // configuration error, redirection type must be GPOA_T and the
            throw new PoAException('slo-conf-error', E_USER_ERROR, array());
        }

        // single logout
        $action = (array_key_exists('ACTION', $_REQUEST)) ? $_REQUEST['ACTION'] : "";
        if ($action === "PAPILOGOUT") { // single logout
            // GPoA asks for logout!
            $this->deleteCookie();
            trigger_error(PoAUtils::msg('slo-logout', array()), E_USER_NOTICE);
            $location = $this->getSingleLogoutResponseLocation();
        } else { // logout is triggered from the application
            trigger_error(PoAUtils::msg('slo-requested', array()), E_USER_NOTICE);
            $location = $this->getSingleLogoutLocation();
        }
        $this->redirect($location);
    }

    protected function attributeQuery($name) {
        // attribute query protocol
        //TODO: define attr query protocol and implement!
    }

    // PAPI SPECIFIC METHODS

    /**
     * Check if a cookie is valid.
     * @param cookie The cookie.
     * @return boolean true if the cookie is valid, false otherwise.
     */
    protected function testCookie($name = "") {
        if (empty($name)) {
            $name = $this->cookie_name;
        }
        $cookie = $_COOKIE[$name];

        if (empty($cookie)) {
            trigger_error(PoAUtils::msg('empty-cookie-err', array($name)), E_USER_WARNING);
            return false;
        }

        $now = time();
        // extract the contents from the cookie
        $newsource = $this->crypto->decryptAES($cookie);
        list($timestamp, $this->global_expire_time, $location, $id, $this->as_id, $this->assertion) = explode(":", $newsource, 6);

        $this->expiration_time = ($this->global_expire_time < ($timestamp + $this->cfg->getCookieTimeout()))
                                 ? $this->global_expire_time : $timestamp + $this->cfg->getCookieTimeout();

        // check the cookie
        if ($location != $this->cfg->getLocation()) {
            trigger_error(PoAUtils::msg('cookie-location-err', array($cookie)), E_USER_WARNING);

            return false;
        }
        if ($id != $this->id) {
            trigger_error(PoAUtils::msg('cookie-service-err', array($cookie)), E_USER_WARNING);

            return false;
        }
        if (($this->global_expire_time < $now) or ($timestamp + $this->cfg->getCookieTimeout() < $now)) {
            trigger_error(PoAUtils::msg('cookie-expired-err', array($cookie)), E_USER_WARNING);
            
            return false;
        }
        trigger_error(PoAUtils::msg('valid-cookie', array()), E_USER_NOTICE);
        return true;
    }

    /**
     * Delete the current cookie, if any.
     * @return true
     */
    protected function deleteCookie() {
        /*
         * This is a hack to make cookie deletion work with firefox browers.
         * Firefox won't delete a cookie unless it is set with exactly the same
         * way it was originally set, except the expiration time. So we have to
         * set the contents, location and domain, and then set an expiration date
         * in the past.
         */
        setcookie($this->cookie_name,
                  @$_COOKIE[$this->cookie_name],
                  time()-3600,
                  $this->cfg->getLocation(),
                  $this->cfg->getCookieDomain(), 0);
        unset($_COOKIE[$this->cookie_name]);
    }

    /**
     * Check the response from the AS/GPoA.
     * @param data The data received.
     * @param key The public key of the AS/GPoA.
     * @return boolean true if valid, false else.
     */
    protected function testResponse($data, $pubkey) {
        // decrypt data
        $newsource = $this->crypto->decrypt($data);

        if ($newsource === false) {
            // Cannot decrypt!
            trigger_error(PoAUtils::msg('cannot-decrypt', array()), E_USER_WARNING);
            return false;
        }

        // check the assertion
        if (empty($newsource)) { // empty assertion
            trigger_error(PoAUtils::msg('empty-response-err', array()), E_USER_WARNING);
            return false;
        }

        // parse data
        $response = explode(":", $newsource);
        $this->key = array_pop($response);
        $current_time = array_pop($response);
        $this->global_expire_time = array_pop($response);
        $rest = implode(":", $response);
        $rest_a = explode("@", $rest);
        $this->as_id = array_pop($rest_a);
        $this->assertion = implode("@", $rest_a);

        $this->expiration_time = ($this->global_expire_time < ($current_time + $this->cfg->getCookieTimeout()))
                                 ? $this->global_expire_time : $current_time + $this->cfg->getCookieTimeout();


        if ($this->assertion === "ERROR") { // AS/GPoA error response, authentication failed!
            $this->deleteRequest($this->key);
            trigger_error(PoAUtils::msg('authn-error', array()), E_USER_WARNING);
            return false;
        }

        // check timestamps
        if ($this->global_expire_time < time()) { // globally expired
            $this->deleteRequest($this->key);
            trigger_error(PoAUtils::msg('expired-response', array()), E_USER_WARNING);
            return false;
        }
        if ($current_time + $this->cfg->getCookieTimeout() < time()) { // expired
            $this->deleteRequest($this->key);
            trigger_error(PoAUtils::msg('expired-response', array()), E_USER_WARNING);
            return false;
        }

        // the response is OK
        trigger_error(PoAUtils::msg('valid-response', array($this->assertion)), E_USER_WARNING);
        return $this->key;
    }

    /** 
     * Redirect user browser to the appropriate URL for authentication.
     * WARNING: This method ends execution.
     * @param location If set, the location where to redirect the user. If not, defaults are used.
     * @return void This method does not return!
     */
    protected function redirect($location = "") {
        if (!empty($location)) {
            $l = $location;
        } else {
            $l = $this->getRedirectLocation();
        }

        if (!$l) {
            throw new PoAException('cannot-redirect', E_USER_ERROR, array());
        }
        header("HTTP/1.1 302 Found");
        header("Location: ".$l);
        trigger_error(PoAUtils::msg('redirecting', array($l)), E_USER_WARNING);
        die(0);
    }

    /**
     * Retrieve the URL where to redirect a user to perform a single logout.
     * @return string The appropriate URL where to redirect the browser, false if error.
     */
    protected function getSingleLogoutLocation() {
        // set protocol
        $protocol = (!empty($_SERVER['HTTPS'])) ? "https://" : "http://";

        // build URL
        $url = $protocol.$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
        $c_url = $this->cfg->getLogoutURL();
        if (!empty($c_url)) {
            $url = $c_url;
        }

        $params = array('ACTION' => 'PAPISIGNOFFREQ',
                        'DATA' => 'DUMMY',
                        'POA' => $this->cfg->getID(), // TODO
                        'URL' => $url);

        $sep = (strstr("?", $this->cfg->getRedirectURL())) ? "&" : "?";
        return $this->cfg->getRedirectURL().$sep.http_build_query($params);
    }

    /**
     * Retrieve the URL where to redirect a user once he has successfully logged out.
     * @return string The appropriate URL where to redirect the browser, false if error.
     */
    protected function getSingleLogoutResponseLocation() {
        // set protocol
        $protocol = (!empty($_SERVER['HTTPS'])) ? "https://" : "http://";

        $params = array('ACTION' => 'PAPILOGGEDOUT',
                        'DATA' => 'DUMMY',
                        'POA' => $this->cfg->getID()); // TODO

        $sep = (strstr("?", $this->cfg->getRedirectURL())) ? "&" : "?";
        return $this->cfg->getRedirectURL().$sep.http_build_query($params);
    }

    /**
     * Retrieve the URL where to redirect a user and store his request.
     * @return string The appropriate URL where to redirect the browser, false if error.
     */
    protected function getRedirectLocation() {
        // initialize key identifier
        $key = mt_rand();

        // set protocol
        $protocol = (!empty($_SERVER['HTTPS'])) ? "https://" : "http://";

        // build URL
        $url = $protocol.$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];

        // check if the request hast to be sent to a GPoA or an AS
        if ($this->cfg->getRedirectType() === AS_T) { // AS
            $params = array('ATTREQ' => 'poaid',
                            'PAPIPOAREF' => $key,
                            'PAPIPOAURL' => $url);
        } else { // GPoA
            $params = array('ACTION' => 'CHECK',
                            'DATA' => $key,
                            'URL' => $url);
            // check PAPIHLI
            $hli = $this->cfg->getHomeLocatorID();
            if (!empty($hli)) {
                $params['PAPIHLI'] = $hli;
            }

            // check PAPIOPOA
            $params['POA'] = $this->opoa; // TODO
            $params['PAPIOPOA'] = $this->opoa;
            $id = $this->cfg->getID();
            if (!empty($id)) {
                $params['POA'] = $id; // TODO
            }

            // check friendly name TODO
            $fname = $this->cfg->getFriendlyName();
            if (!empty($fname)) {
                $params['POADISPLAYNAME'] = $fname;
            }

            // check logout URL TODO
            $logout = $this->cfg->getLogoutURL();
            if (!empty($logout)) {
                $params['LSOURL'] = base64_encode($logout);
            }
        }

        $arg = array($params);
        $this->runHooks(PAPI_REDIRECT_URL_FINISH, $arg);
        $params = $arg[0];
        $hli = @$params['PAPIHLI'];

        // save the current request
        $saved = $this->saveRequest($key, $hli);
        if (!$saved) {
            trigger_error(PoAUtils::msg('cannot-save-request', array()), E_USER_WARNING);
            return false;
        }

        $sep = (strstr("?", $this->cfg->getRedirectURL())) ? "&" : "?";
        return $this->cfg->getRedirectURL().$sep.http_build_query($params);
    }

    /**
     * Save a request to the request database. The request includes: $_REQUEST, $_GET, $_POST,
     * $_SERVER['QUERY_STRING'], $_SERVER['REQUEST_METHOD'] and php://input.
     * @param key The key identifier for this request.
     * @param hli The home locator identifier that should be used for this request.
     * @return string|boolean The key to retrieve later this request from the database, false if error.
     */
    protected function saveRequest($key, $hli) {
        // open database
        $id = $this->db->open();
        if (!$id) {
            trigger_error(PoAUtils::msg('cannot-open-req-db', array()), E_USER_ERROR);
            return false;
        }

        // perform db maintenance
        $purged = $this->db->purge($this->cfg->getRequestLifetime());
        if ($purged > 0) {
            trigger_error(PoAUtils::msg('req-db-purged', array($purged)), E_USER_NOTICE);
        }

        // create/replace entry for random key
        // marcoscm: Some clients don't rely *only* on $_REQUEST (2), but on $_GET (0), $_POST (1),
        // $_SERVER["QUERY_STRING"] (3), $_SERVER["REQUEST_METHOD"] (4) or and php://input (5)
        $ok = $this->db->replaceContents($key, $_GET, $_POST, $_REQUEST, $_SERVER["QUERY_STRING"],
                                         $_SERVER["REQUEST_METHOD"], file_get_contents("php://input"), $hli);
        $this->db->close();
        return (!$ok) ? false : $key;
    }

    /**
     * Load a request from the request database.
     * @param key The key that identifies the request.
     * @return hash The request associated with that key, false if error.
     */
    protected function loadRequest($key) {
        global $HTTP_RAW_POST_DATA;

        // open database
        $id = $this->db->open();
        if (!$id) {
            trigger_error(PoAUtils::msg('cannot-open-db', array()), E_USER_ERROR);
            return false;
        }

        // search for key
        $request = $this->db->fetch($key);
        if (!$request) {
            $this->db->close();
            trigger_error(PoAUtils::msg('cannot-fetch-key', array($key)), E_USER_WARNING);
        }
        $this->db->close();

        // run hook
        $arg = array($request);
        $this->runHooks(PAPI_RESTORE_ORIGINAL_REQUEST, $arg);
        $request = $arg[0];

        // check if HLI matches with AS ID
        if (!empty($request['HLI']) && $this->as_id != $request['HLI']) {
            trigger_error(PoAUtils::msg('as-id-error', array($this->as_id, $request['HLI'])), E_USER_ERROR);
        }

        // reload original context
        $_GET = $request["GET"];
        $_POST = $request["POST"];
        $_REQUEST = $request["REQUEST"];
        $_SERVER["QUERY_STRING"] = $request["QUERY_STRING"];
        $_SERVER["REQUEST_METHOD"] = $request["REQUEST_METHOD"];
        $HTTP_RAW_POST_DATA = $request["PHP_INPUT"];

        return $request;
    }

    /**
     * Delete a request from the request database.
     * @param key The key that identifies the request.
     * @return boolean true if success, false in any other case.
     */
    protected function deleteRequest($key) {
        // open database
        $id = $this->db->open();
        if (!$id) {
            trigger_error(PoAUtils::msg('cannot-open-db', array()), E_USER_ERROR);
            return false;
        }

        // search and delete key
        $request = false;
        if ($this->db->check($key)) {
            $request = $this->db->delete($key);
        } else {
            $this->db->close();
            trigger_error(PoAUtils::msg('cannot-del-key', array($key)), E_USER_WARNING);
        }
        $this->db->close();

        return $request;
    }

    /**
     * Generate a new cookie for the current user.
     * @return string The cookie conveniently encrypted with our own key.
     */
    protected function getNewCookie() {
        $expiration = ((time() + $this->cfg->getCookieTimeout()) < $this->global_expire_time)
                      ? time() + $this->cfg->getCookieTimeout() : $this->global_expire_time;

        $content = time().":".$expiration.":".$this->cfg->getLocation().":".$this->id.":".$this->as_id.":".$this->assertion;
        return $this->crypto->encryptAES($content);
    }

    /**
     * Determines if it's safe to assume the user as authenticated. 
     * @return boolean true if the user still has a valid session, false otherwise.
     */
    protected function isSafe() {
        if (!$this->expiration_time) return false;

        return $this->expiration_time > time();
    }

}

?>
