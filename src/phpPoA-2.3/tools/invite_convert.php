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
 * Invites DB converter tool from phpPoA1 to phpPoA2.
 */

define('ROOT', 'PATH_TO_YOUR_DB_DIR');

header("Content-type: text/plain");
header("Cache-Control: no-cache, must-revalidate");

$input = $_GET['i'];
$output = $_GET['o'];

$db_i = dba_open(ROOT.$input, "r", "db4");
$db_o = dba_open(ROOT.$output, "c", "db4");

$key = dba_firstkey($db_i);

while ($key) {
    $data = explode("##", dba_fetch($key, $db_i));
    var_dump($key);

    $new['email'] = $data[0];
    $new['since'] = time();
    $new['expires'] = 0;

    var_dump($new);

    dba_replace($key, serialize($new), $db_o);

    $key = dba_nextkey($db_i);
}

dba_close($db_i);
dba_close($db_o);

?>
