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
 * @package phpPoA2
 */

/**
 * Authentication succeeded.
 */
define('AUTHN_SUCCESS', true);

/**
 * Authentication failed.
 */
define('AUTHN_FAILED', false);

/**
 * Authorization succeeded.
 */
define('AUTHZ_SUCCESS', true);

/**
 * Authorization failed.
 */
define('AUTHZ_FAILED', false);

/**
 * Authentication failed error.
 */
define('NOAUTH_ERR', 0);

/**
 * A system related error.
 */
define('SYSTEM_ERR', 1);

/**
 * A configuration related error.
 */
define('CONFIG_ERR', 2);

/**
 * An invitation related error.
 */
define('INVITE_ERR', 3);

/**
 * An error triggered by the user.
 */
define('USER_ERR',   4);

/**
 * 'GPoA' type redirection in the PAPI 1.5 protocol.
 */
define('GPOA_T', 0);

/**
 * 'AS' type redirection in the PAPI 1.5 protocol.
 */
define('AS_T',   1);

?>
