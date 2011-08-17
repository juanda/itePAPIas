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
 * This hook is executed right after retrieving the current URI, the params (both GET and POST) and
 * the arrays of allowed and denied patterns that will be checked inmediately.
 * It can be used to alter parameters and the URL, and also to configure the filters on runtime.
 * The hook receives the URI string, an array of parameters, the allowed and the denied patterns.
 * Functions for this hook must be defined like this:
 *
 * function queryBeforeFilterHook(&$uri, &$params, &$allowed, &$denied);
 *
 * Please bear in mind that hooks must return TRUE or they'll keep other hooks from executing.
 */
define("QUERY_BEFORE_FILTERS", "QUERY_BEFORE_FILTERS");

/**
 * Authorization engine that works by checking the query string of the request.
 * PLEASE NOTE THAT THIS ENGINE SILENTLY IGNORES BOTH USER AND ATTRIBUTES.
 * PLEASE NOTE THAT THIS ENGINE WORKS ONLY FOR WEB-BASED APPLICATIONS.
 * @package phpPoA2
 * @subpackage QueryFilterAuthorizationEngine
 */
class QueryFilterAuthzEngine extends AuthorizationEngine {

    protected $valid_hooks = array(QUERY_BEFORE_FILTERS);

    /**
     * PLEASE NOTE THAT THIS ENGINE SILENTLY IGNORES BOTH USER AND ATTRIBUTES.
     */
    public function isAuthorized($user, $attrs) {
        $params = $this->getQueryParams();
        $default = $this->cfg->getDefaultBehaviour();
        $allowed = $this->cfg->getAllowedPatterns();
        $denied  = $this->cfg->getDeniedPatterns();

        // run hook before checking patterns
        $args = array($params, $allowed, $denied);
        $this->runHooks(QUERY_BEFORE_FILTERS, $args);
        $params = $args[0];
        $allowed = $args[1];
        $denied = $args[2];

        $allowed_match = $this->matches($params, $allowed);
        $denied_match  = $this->matches($params, $denied);

        // check matches giving priority to the default setting
        $order = array($default, !$default);
        foreach ($order as $option) {
            if ($option) { // check allowed parameters
                if ($allowed_match) {
                    trigger_error(PoAUtils::msg('allowed-param-match', array($allowed_match)), E_USER_WARNING);
                    return true;
                }
            } else { // check denied parameters
                if ($denied_match) {
                    trigger_error(PoAUtils::msg('denied-param-match', array($denied_match)), E_USER_WARNING);
                    return false;
                }
            }
        }

        // default response
        trigger_error(PoAUtils::msg('authz-default-fallback'), E_USER_NOTICE);
        return $default;
    }

    public function getAuthorizedList() {
        $this->registerHandler();
        $list = $this->cfg->getAllowedPatterns();
        $this->clean();
        return $list;
    }

    public function authorize($user, $attrs, $ref, $expires = 0) {
        return false;
    }

    public function revoke($mail) {
        return false;
    }

    /**
     * Get all the input received for the current request.
     * @return data An array containing all parameters received as input.
     */
    private function getQueryParams() {
        // Full URI
        $uri = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

        // GET queries
        $data = explode("&", $_SERVER['QUERY_STRING']);

        // POST queries
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $str = file_get_contents(STDIN);

            $post = array();
            if ($str) {
                $post = explode("&". $str);
            }

            $data = array_merge($data, $post);
        }

        // convert to associative array
        $result['&REQUEST_URI'] = $uri;
        foreach ($data as $item) {
            list($key, $value) = explode("=", $item);
            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Returns the parameter (or parameters) that matched a list of patterns.
     * @param params An array of parameters to check.
     * @param patterns An array of patterns to be matched with.
     * @return The names of the parameters matched, comma separated if more than one.
     * False otherwise.
     */
    private function matches($params, $patterns) {
        $match = false;
        foreach ($patterns as $key => $value) {
            if (is_numeric($key) && is_array($value)) { // must match a bunch of options
                $partial_match = true;
                $matches = array();
                foreach ($value as $name => $pattern) {
                    if (!isset($params[$name])) { // parameter not set, skip this option
                        $partial_match = false;
                        break;
                    }

                    // convert parameter to array for easy handling
                    $param = $params[$name];
                    if (!is_array($params[$name])) {
                        $param = array($params[$name]);
                    }

                    // convert pattern to array for easy handling
                    $pats = $pattern;
                    if (!is_array($pattern)) {
                        $pats = array($pattern);
                    }

                    // check if any of the possible values match
                    $some_val_matches = false;
                    foreach ($param as $item) {
                        foreach ($pats as $pat) {
                            if (preg_match('/'.$pat.'/', $item)) {
                                // parameter matches, continue
                                $some_val_matches = true;
                                break;
                            }
                        }
                        if ($some_val_matches) break;
                    }
                    if (!$some_val_matches) {
                        $partial_match = false;
                        break;
                    }

                    $matches[] = $name;
                }
                if ($partial_match) {
                    $match = implode(",", $matches);
                }

            } else { // match just one parameter
                if (is_numeric($key))
                    $key = "&REQUEST_URI"; // try to match the complete URI

                if (!isset($params[$key])) // parameter not set, skip this option
                    continue;

                // convert parameter to array for easy handling
                $param = $params[$key];
                if (!is_array($params[$key])) {
                    $param = array($params[$key]);
                }

                // convert pattern to array for easy handling
                $pats = $value;
                if (!is_array($value)) {
                    $pats = array($value);
                }

                // check if any of the possible values match
                foreach ($param as $item) {
                    foreach ($pats as $pattern) {
                        if (preg_match('/'.$pattern.'/', $item)) {
                            // parameter matches, stop searching
                            $match = $key;
                            break;
                        }
                    }
                    if ($match) break;
                }
            }
        }
        return $match;
    }

}

?>
