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

define('PAPI_DB_TABLE', 'requests');

/**
 * MySQL database frontend for the PAPI Authentication Engine.
 * @package phpPoA2
 * @subpackage PAPIAuthenticationEngine
 */
class PAPIMySQLDB extends GenericMySQLDB implements PAPIDB {

    protected $context;
    protected $prefix = "papi_";

    public function __construct($cfg) {
        // initialize SQL queries
        $this->create_sql  = "CREATE TABLE IF NOT EXISTS ##PREFIX##".PAPI_DB_TABLE." (`key` VARCHAR(255) NOT NULL, ".
                             "`TIMESTAMP` TEXT, `GET` TEXT, `POST` TEXT, `REQUEST` TEXT, ".
                             "`QUERY_STRING` TEXT, `REQUEST_METHOD` TEXT, `PHP_INPUT` TEXT, `HLI` TEXT, ".
                             "PRIMARY KEY (`key`))";
        $this->search_sql  = "SELECT * FROM ##PREFIX##".PAPI_DB_TABLE." WHERE `key` = '##KEY##'";
        $this->insert_sql  = "INSERT INTO ##PREFIX##".PAPI_DB_TABLE." (`key`, `TIMESTAMP`, `GET`, `POST`, `REQUEST`, `QUERY_STRING`,".
                             "`REQUEST_METHOD`, `PHP_INPUT`, `HLI´) VALUES ('";
        $this->update_sql  = "UPDATE ##PREFIX##".PAPI_DB_TABLE." SET ";
        $this->delete_sql  = "DELETE FROM ##PREFIX##".PAPI_DB_TABLE." WHERE `key` = '##KEY##'";

        parent::__construct($cfg);
    }

    public function replaceContents($key, $get, $post, $request, $query, $method, $input, $hli) {
        // serialize input
        $this->context['GET'] = serialize($get);
        $this->context['POST'] = serialize($post);
        $this->context['REQUEST'] = serialize($request);
        $this->context['QUERY_STRING'] = serialize($query);
        $this->context['REQUEST_METHOD'] = serialize($method);
        $this->context['PHP_INPUT'] = serialize($input);
        $this->context['HLI'] = $hli;

        // prepare the SQL queries
        $this->insert_sql .= $key."', '".time()."', '".$this->context['GET']."', '".$this->context['POST']."', '".$this->context['REQUEST'].
                             "', '".$this->context['QUERY_STRING']."', '".$this->context['REQUEST_METHOD']."', '".
                             $this->context['PHP_INPUT']."', '".$this->context['HLI']."')";
        $this->update_sql .= "TIMESTAMP='".time()."', GET='".$this->context['GET']."', POST='".$this->context['POST']."', REQUEST='".
                             $this->context['REQUEST']."', QUERY_STRING='".$this->context['QUERY_STRING']."', REQUEST_METHOD='".
                             $this->context['REQUEST_METHOD']."', PHP_INPUT='".$this->context['PHP_INPUT']."', HLI='".
                             $this->context['HLI']."' WHERE key = '##KEY##'";

        // perform replace
        return $this->replace($key, "");
    }

    public function fetch($key) {
        // retrieve the row
        $row = parent::fetch($key);

        // unserialize
        $this->context['timestamp'] = $row['TIMESTAMP'];
        $this->context['GET'] = unserialize($row['GET']);
        $this->context['POST'] = unserialize($row['POST']);
        $this->context['REQUEST'] = unserialize($row['REQUEST']);
        $this->context['QUERY_STRING'] = unserialize($row['QUERY_STRING']);
        $this->context['REQUEST_METHOD'] = unserialize($row['REQUEST_METHOD']);
        $this->context['PHP_INPUT'] = unserialize($row['PHP_INPUT']);
        $this->context['HLI'] = $row['HLI'];

        return $this->context;
    }

}

?>
