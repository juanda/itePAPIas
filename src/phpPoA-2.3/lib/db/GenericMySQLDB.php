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
 * Generic MySQL database class.
 * @abstract
 * @package phpPoA2
 * @subpackage GenericDatabaseHandlers
 */
abstract class GenericMySQLDB extends GenericDB {

    protected $mandatory_options = array("DBHost",
                                         "DBUser",
                                         "DBPassword",
                                         "DBName");
    protected $create_sql, $search_sql, $search_all_sql, $update_sql, $insert_sql, $delete_sql;
    protected $prefix;

    protected function configure() {
        if (!extension_loaded("mysql")) {
            trigger_error(PoAUtils::msg('extension-required', array("mysql")), E_USER_ERROR);
        }
        parent::configure();
    }

    function open() {
        $this->prefix = $this->cfg->getDBPrefix();
        $this->db = @mysql_connect($this->cfg->getDBHost(), $this->cfg->getDBUser(), $this->cfg->getDBPassword());
        if (!$this->db) return false;
        if (!@mysql_select_db($this->cfg->getDBName(), $this->db)) return false;
        $this->create_sql = str_replace("##PREFIX##", $this->prefix, $this->create_sql);
        if (!@mysql_query($this->create_sql, $this->db)) return false;
        return true;
    }

    function getError() {
        return @mysql_error($this->db);
    }

    function check($key) {
        $this->search_sql = str_replace(array("##KEY##", "##PREFIX##"), array($key, $this->prefix), $this->search_sql);
        $res = @mysql_query($this->search_sql, $this->db);
        return (@mysql_fetch_assoc($res)) ? true : false;
    }

    function replace($key, $value) {
        if ($this->check($key, $this->db)) {
            $this->update_sql = str_replace(array("##KEY##", "##VALUE##", "##PREFIX##"), array($key, $value, $this->prefix), $this->update_sql);
            return (@mysql_query($this->update_sql, $this->db)) ? true : false;
        } else {
            $this->insert_sql = str_replace(array("##KEY##", "##VALUE##", "##PREFIX##"), array($key, $value, $this->prefix), $this->insert_sql);
            return (@mysql_query($this->insert_sql, $this->db)) ? true : false;
        }
    }

    function fetch($key) {
        $this->search_sql = str_replace(array("##KEY##", "##PREFIX##"), array($key, $this->prefix), $this->search_sql);
        $res = @mysql_query($this->search_sql, $this->db);
        return @mysql_fetch_assoc($res);
    }

    function fetch_all() {
        $this->search_all_sql = str_replace("##PREFIX##", $this->prefix, $this->search_all_sql);
        $res = @mysql_query($this->search_all_sql, $this->db);
        return @mysql_fetch_assoc($res);
    }

    function delete($key) {
        $this->delete_sql = str_replace(array("##KEY##", "##PREFIX##"), array($key, $this->prefix), $this->delete_sql);
        return (@mysql_query($this->delete_sql, $this->db)) ? true : false;
    }

    function close() {
        @mysql_close($this->db);
    }

}

?>
