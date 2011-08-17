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
 * @subpackage AttributeFilterAuthorizationEngine
 */

/**
 * Configurator for the Attribute Filter Authorization Engine.
 * @package phpPoA2
 * @subpackage AttributeFilterAuthorizationEngine
 */
class AttributeFilterConfigurator extends AuthorizationConfigurator {

    protected $mandatory_options = array("Allowed", "Denied");

    /**
     * Returns an array of attributes that would be allowed if their values match
     * any of the patterns for each attribute.
     * @return array The array of attribute patterns allowed.
     */
    public function getAllowedAttributes() {
        return $this->cfg['Allowed'];
    }

    /**
     * Returns an array of attributes that would be denied if their values match
     * any of the patterns for each attribute.
     * @return array The array of attribute patterns denied.
     */
    public function getDeniedAttributes() {
        return $this->cfg['Denied'];
    }

}

?>
