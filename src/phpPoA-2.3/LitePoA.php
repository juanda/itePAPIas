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
 * Class that embbeds all functionallity (authentication, attribute collection and authorization) in one method,
 * just like older versions of phpPoA.
 * 
 * @package phpPoA2
 * @deprecated This class is for backwards compatibility. Please avoid using it.
 */
class LitePoA extends AutoPoA {

    protected $handler;

    /**
     * Set an attribute handler to build identifiers for the users
     * according to their attributes. Method must receive a hash of
     * attributes ("name" => "value").
     * @param handler The name of the function.
     */
    public function setIDBuilder($handler) {
        $this->handler = $handler;
    }

    /**
     * A shortcut for the whole authentication and authorization process.
     * User will be authenticated, his attributes collected and then he will
     * be checked for authorization with the identifier built with the ID
     * builder function.
     * @return A hash 
     * @deprecated This method is for backwards compatibility. Please avoid using it.
     */
    public function checkAccess() {
        $hash['authnStatus'] = $this->authenticate();
        $hash['attributes'] = $this->getAttributes();
        $hash['authzStatus'] = $this->isAuthorized("", $hash['attributes']);
        return $hash;
    }
}


?>
