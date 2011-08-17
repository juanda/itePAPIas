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
 * Configurator for the query filter authorization engine.
 * @package phpPoA2
 * @subpackage QueryFilterAuthorizationEngine
 */
class QueryFilterConfigurator extends AuthorizationConfigurator {

    protected $mandatory_options = array("Allowed", "Denied");

    /**
     * Returns the patterns that match allowed URIs or query parameters.
     * @return array The array with the allowed patterns.
     */
    public function getAllowedPatterns() {
        return $this->cfg['Allowed'];
    }

    /**
     * Returns the patterns that match denied URIs or query parameters.
     * @return array The array with the denied patterns.
     */
    public function getDeniedPatterns() {
        return $this->cfg['Denied'];
    }

}

?>
