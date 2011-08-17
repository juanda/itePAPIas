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
 * Abstract authentication engine.
 * @abstract
 * @package phpPoA2
 */
abstract class AuthenticationEngine extends GenericEngine {

    protected $engine_type = "Authn";

    /**
     * Trigger the authentication of the user in the current context. May perform
     * HTTP redirections or any other procedure to gather the authentication status
     * if the user, so don't expect it to return control once called.
     * @return boolean AUTHN_SUCCESS (true) or AUTHN_FAILED (false).
     */
    public abstract function authenticate();

    /**
     * Check the current authentication status withing this context. Does not
     * trigger any authentication procedure.
     * @return boolean AUTHN_SUCCESS (true) or AUTHN_FAILED (false).
     */
    public abstract function isAuthenticated();

    /**
     * Get an associative array with the set of common attributes for the current
     * user. May trigger some attribute recollection.
     * @return array An associative array with all the attributes gathered by default.
     */
    public abstract function getAttributes();

    /**
     * Get an specific attribute for the current user, by specifying its name and
     * namespace. May trigger some attribute query procedure.
     * @param name The name of the attribute.
     * @param namespace The namespace of the attribute.
     * @return string|array the value or an array of values for the required attribute, or false
     * if anything went wrong.
     */
    public abstract function getAttribute($name, $namespace = "");

    /**
     * Perform a logout (locally or global).
     * @param slo Whether to perform the logout globally (Single Log Out) or not. Defaults to local.
     * @return void
     */
    public abstract function logout($slo = false);

}

?>
