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
 * Class that automatically redirects to error pages if any error is detected.
 * @package phpPoA2
 */
class AutoPoA extends PoA {

    public function authenticate() {
        if (!parent::authenticate()) {
            header("Location: ".$this->cfg->getNoAuthErrorURL());
            exit;
        }
        return true;
    }

    public function isAuthorized($user, $attrs, $engine = null) {
        if (!parent::isAuthorized($user, $attrs, $engine)) {
            header("Location: ".$this->cfg->getNoAuthErrorURL());
            exit;
        }
        return true;
    }

}

?>
