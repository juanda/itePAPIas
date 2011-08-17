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
 * @author Candido Rodriguez <candido.rodriguez@rediris.es>
 * @filesource
 */

/**
 * Session database backend.
 * WARNING: please note that PHP only allows one session at a time, so using
 * this backend will break any applications that make use of sessions beneath.
 * @package phpPoA2
 * @subpackage GenericDatabaseHandlers
 */
class SessionDB extends GenericDB {

    protected $session_name = "session_db";

    public function open() {
        return function_exists("session_start");
    }

    public function check($key) {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (array_key_exists($this->session_name, $_SESSION)) {
            $data = $_SESSION[$this->session_name];
            return array_key_exists($key, $data);
        }
        return false;
    }

    public function replace($key, $value) {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!array_key_exists($this->session_name, $_SESSION)) {
            $_SESSION[$this->session_name] = array();
        }
        $_SESSION[$this->session_name][$key] = $value;
        return true;
    }

    public function fetch($key) {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (array_key_exists($this->session_name, $_SESSION)) {
            return $_SESSION[$this->session_name][$key];
        }
        return false;
    }

    public function fetch_all() {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (array_key_exists($this->session_name, $_SESSION)) {
            return $_SESSION[$this->session_name];
        }
        return false;
    }

    public function delete($key) {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (array_key_exists($this->session_name, $_SESSION)) {
            if (array_key_exists($key, $_SESSION[$this->session_name])) {
                unset($_SESSION[$this->session_name][$key]);
                return true;
            }
            else {
                return false;
            }
        }
        return false;
    }

    public function close() {
        return true;
    }
}

?>
