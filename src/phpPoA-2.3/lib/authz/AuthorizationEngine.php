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

abstract class AuthorizationEngine extends GenericEngine {

    protected $engine_type = "Authz";

    /**
     * Check authorization for the specified user.
     * @param user The string that identifies the user.
     * @param attrs All attributes related to the user.
     * @return boolean AUTHZ_SUCCESS if the user is authorized, AUTHZ_FAILED
     * in any other case.
     */
    public abstract function isAuthorized($user, $attrs);

    /**
     * @return array
     */
    public abstract function getAuthorizedList();

    /**
     * @return boolean
     */
    public abstract function authorize($user, $attrs, $ref, $expires = 0);

    /**
     * @return boolean
     */
    public abstract function revoke($mail);

}

?>
