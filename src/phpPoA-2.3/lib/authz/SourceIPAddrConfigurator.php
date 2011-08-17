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
 * Configurator class for the Source IP Address Authorization Engine.
 * @package phpPoA2
 * @subpackage SourceIPAddrAuthorizationEngine
 */
class SourceIPAddrConfigurator extends AuthorizationConfigurator {

    protected $mandatory_options = array("AllowFrom", "DenyFrom");

    /**
     * Returns an array of IP addresses or networks that are authorized.
     * @return array The array of IP addresses authorized.
     */
    public function getAllowed() {
        if (is_array($this->cfg['AllowFrom']))
            return $this->cfg['AllowFrom'];
        else
            return array($this->cfg['AllowFrom']);
    }

    /**
     * Returns an array of IP addresses or networks that are not authorized.
     * @return array The array of IP addresses not authorized.
     */
    public function getDenied() {
        if (is_array($this->cfg['DenyFrom']))
            return $this->cfg['DenyFrom'];
        else
            return array($this->cfg['DenyFrom']);
    }

}

?>
