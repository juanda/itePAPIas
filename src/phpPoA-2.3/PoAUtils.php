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

class PoAUtils {

    /**
     * Return the internationalized message identified by the code 's'.
     * @param s The identifier of the message.
     * @param args An array of arguments that the message expects.
     * @return string The human readable message already translated.
     */
    public static function msg($s, $args = array()) {
        global $poa_messages;

        return vsprintf($poa_messages[$s], $args);
    }

    /**
     * Return the language code identifying applicable to a messages file.
     * @param filename The name of the file.
     * @return string The internationalization code corresponding with the file.
     */
    public static function lang_code($filename) {
        $pat[] = '/^.*\/messages-/';
        $pat[] = '/\.php/';
        $code = preg_replace($pat, '', $filename);
        return $code;
    }

}

?>
