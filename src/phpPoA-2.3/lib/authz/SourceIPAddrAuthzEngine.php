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
 * This hook is executed right after retrieving source address and
 * the arrays of allowed and denied patterns that will be checked inmediately.
 * It can be used to alter the source address, and also to configure the filters on runtime.
 * The hook receives the source IP address, the allowed and the denied patterns.
 * Functions for this hook must be defined like this:
 *
 * function ipBeforeFilterHook(&$ipaddr, &$allowed, &$denied);
 *
 * Please bear in mind that hooks must return TRUE or they'll keep other hooks from executing.
 */
define("SOURCEADDR_BEFORE_FILTERS", "SOURCEADDR_BEFORE_FILTERS");

/**
 * Authorization engine that works by checking the source IP address of the request.
 * PLEASE NOTE THAT THIS ENGINE SILENTLY IGNORES BOTH USER AND ATTRIBUTES.
 * PLEASE NOTE THAT THIS ENGINE WORKS ONLY FOR WEB-BASED APPLICATIONS.
 * @package phpPoA2
 * @subpackage SourceIPAddrAuthorizationEngine
 */
class SourceIPAddrAuthzEngine extends AuthorizationEngine {

    protected $valid_hooks = array(SOURCEADDR_BEFORE_FILTERS);

    /**
     * PLEASE NOTE THAT THIS ENGINE SILENTLY IGNORES BOTH USER AND ATTRIBUTES.
     */
    public function isAuthorized($user, $attrs) {
        $default = $this->cfg->getDefaultBehaviour();

        // proxy support
        $src_addr = (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

        // check if there are IP filters
        $allowed = $this->cfg->getAllowed();
        $denied = $this->cfg->getDenied();

        // run hook before checking patterns
        $args = array($src_addr, $allowed, $denied);
        $this->runHooks(SOURCEADDR_BEFORE_FILTERS, $args);
        $src_addr = $args[0];
        $allowed = $args[1];
        $denied = $args[2];

        $allowed_match = $this->matches($src_addr, $allowed);
        $denied_match  = $this->matches($src_addr, $denied);

        // check matches giving priority to the default setting
        $order = array($default, !$default);
        foreach ($order as $option) {
            if ($option) { // check allowed attributes
                if ($allowed_match) {
                    trigger_error(PoAUtils::msg('source-ip-allowed', array($src_addr, $allowed_match)), E_USER_WARNING);
                    return true;
                }
            } else { // check denied attributes
                if ($denied_match) {
                    trigger_error(PoAUtils::msg('source-ip-denied', array($src_addr, $denied_match)), E_USER_WARNING);
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
        $list = $this->cfg->getAllowed();
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
     * Check if an IP address matches the current allowed patterns.
     * @param ip The IP address.
     * @param patterns An array of patterns to be matched with.
     * @return The matched pattern. False otherwise.
     */
    private function matches($addr, $patterns) {
        // setup filtering criteria
        $search = array("/\./",
                        "/\.0/",
                        // IPv6 support
                        "/(:0){1,7}/",
                        "/^::/",
                        "/::$/");
        $replace = array("\.",
                         ".\d{1,3}",
                         // IPv6 support
                         "::",
                         "(([0-9a-fA-F]{1,4})){1,7}\:",
                         "(\:([0-9a-fA-F]{1,4})){1,7}");

        foreach ($patterns as $pattern) {
            if (is_array($pattern)) continue; // arrays are not supported, just single strings

            // transform from network notation to regular expression
            $mask = preg_replace($search, $replace, $pattern);

            if (preg_match("/".$mask."/i", $addr)) {
                return $pattern;
            }
        }
        return false;
    }

}

?>
