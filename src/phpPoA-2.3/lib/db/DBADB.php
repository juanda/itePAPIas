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
 * Berkeley DB backend.
 * @package phpPoA2
 * @subpackage GenericDatabaseHandlers
 */
class DBADB extends GenericDB {

    protected $mandatory_options = array("DBFile");

    protected function configure() {
        if (!extension_loaded("dba")) {
            trigger_error(PoAUtils::msg('extension-required', array("dba")), E_USER_ERROR);
        }
        parent::configure();
    }

    public function open() {
        $this->db = @dba_open($this->cfg->getDBFile(), "cl", "db4");
        return ($this->db) ? true : false;
    }

    public function check($key) {
        return @dba_exists($key, $this->db);
    }

    public function replace($key, $value) {
        $result = @dba_replace($key, $value, $this->db);
        dba_sync($this->db);
        return $result;
    }

    public function fetch($key) {
        return @dba_fetch($key, $this->db);
    }

    public function fetch_all() {
        $list = array();
        $key = @dba_firstkey($this->db);
        while ($key) {
            $list[$key] = @dba_fetch($key, $this->db);
            $key = @dba_nextkey($this->db);
        }
        return $list;
    }

    public function delete($key) {
        $result = @dba_delete($key, $this->db);
        dba_optimize($this->db);
        dba_sync($this->db);
        return $result;
    }

    public function close() {
        return @dba_close($this->db);
    }
}

?>
