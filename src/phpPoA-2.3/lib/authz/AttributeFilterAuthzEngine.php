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
 * This hook is executed right after retrieving the arrays of allowed and denied attributes that
 * will be checked inmediately.
 * It can be used to configure the filters on runtime and modify the user's attributes.
 * The hook receives the attributes, and the allowed and denied attribute arrays.
 * Functions for this hook must be defined like this:
 *
 * function attributeBeforeFilterHook(&$attrs, &$allowed, &$denied);
 *
 * Please bear in mind that hooks must return TRUE or they'll keep other hooks from executing.
 */
define("ATTRIBUTE_BEFORE_FILTERS", "ATTRIBUTE_BEFORE_FILTERS");

/**
 * Authorization engine that works by checking the attributes of the user. The first match of an
 * attribute against one of the filters will trigger the authorization result, no matter if it's
 * positive or negative.
 * @package phpPoA2
 * @subpackage AttributeFilterAuthorizationEngine
 */
class AttributeFilterAuthzEngine extends AuthorizationEngine {

    protected $valid_hooks = array(ATTRIBUTE_BEFORE_FILTERS);

    public function isAuthorized($user, $attrs) {
        $default = $this->cfg->getDefaultBehaviour();
        $allowed = $this->cfg->getAllowedAttributes();
        $denied  = $this->cfg->getDeniedAttributes();

        // run hook before checking patterns
        $args = array($attrs, $allowed, $denied);
        $this->runHooks(ATTRIBUTE_BEFORE_FILTERS, $args);
        $attrs = $args[0];
        $allowed = $args[1];
        $denied = $args[2];

        $allowed_match = $this->matches($attrs, $allowed);
        $denied_match  = $this->matches($attrs, $denied);

        // check matches giving priority to the default setting
        $order = array($default, !$default);
        foreach ($order as $option) {
            if ($option) { // check allowed attributes
                if ($allowed_match) {
                    trigger_error(PoAUtils::msg('allowed-attr-match', array($allowed_match)), E_USER_WARNING);
                    return true;
                }
            } else { // check denied attributes
                if ($denied_match) {
                    trigger_error(PoAUtils::msg('denied-attr-match', array($denied_match)), E_USER_WARNING);
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
        $list = $this->cfg->getAllowedAttributes();
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
     * Returns the attribute (or attributes) that matched a list of patterns.
     * @param attrs An associative array of attributes to check.
     * @param patterns An associative array of attributes and their patterns.
     * @return The names of the attributes matched, comma separated if more than one.
     * False otherwise.
     */
    private function matches($attrs, $patterns) {
        $match = false;
        foreach ($patterns as $key => $value) {
            if (is_numeric($key) && is_array($value)) { // must match a bunch of options
                $partial_match = true;
                $matches = array();
                foreach ($value as $name => $pattern) {
                    if (!isset($attrs[$name])) { // attribute not set, skip this option
                        $partial_match = false;
                        break;
                    }

                    // convert attribute to array for easy handling
                    $attr = $attrs[$name];
                    if (!is_array($attrs[$name])) {
                        $attr = array($attrs[$name]);
                    }

                    // convert pattern to array for easy handling
                    $pats = $pattern;
                    if (!is_array($pattern)) {
                        $pats = array($pattern);
                    }

                    // check if any of the possible values match
                    $some_val_matches = false;
                    foreach ($attr as $item) {
                        foreach ($pats as $pat) {
                            if (preg_match('/^'.$pat.'$/', $item)) {
                                // attribute matches, continue
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

            } else { // match just one attribute
                if (!isset($attrs[$key])) // attribute not set, skip this option
                    continue;

                // convert attribute to array for easy handling
                $attr = $attrs[$key];
                if (!is_array($attrs[$key])) {
                    $attr = array($attrs[$key]);
                }

                // convert pattern to array for easy handling
                $pats = $value;
                if (!is_array($value)) {
                    $pats = array($value);
                }

                // check if any of the possible values match
                foreach ($attr as $item) {
                    foreach ($pats as $pattern) {
                        if (preg_match('/^'.$pattern.'$/', $item)) {
                            // attribute matches, stop searching
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
