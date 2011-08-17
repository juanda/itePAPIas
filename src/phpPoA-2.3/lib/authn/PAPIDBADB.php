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
 * PAPI binding to a Berkeley DB backend.
 * @package phpPoA2
 * @subpackage PAPIAuthenticationEngine
 */
class PAPIDBADB extends DBADB implements PAPIDB {

    protected $context;

    public function replaceContents($key, $get, $post, $request, $query, $method, $input, $hli) {
        $this->context['timestamp'] = time();
        $this->context['GET'] = $get;
        $this->context['POST'] = $post;
        $this->context['REQUEST'] = $request;
        $this->context['QUERY_STRING'] = $query;
        $this->context['REQUEST_METHOD'] = $method;
        $this->context['PHP_INPUT'] = $input;
        $this->context['HLI'] = $hli;

        $result = @dba_replace($key, serialize($this->context), $this->db);
        dba_sync($this->db);
        return $result;
    }

    public function fetch($key) {
        $raw = @dba_fetch($key, $this->db);

        if (!$raw) return false;

        $this->context = unserialize($raw);

        return $this->context;
    }

    public function purge($gap) {
        $time = time();
        $key = @dba_firstkey($this->db);
        $result = 0;

        // iterate over database
        while ($key != false) {
            $entry = unserialize(@dba_fetch($key, $this->db));
            if ($entry['timestamp'] < $time - $gap) {
                if (@dba_delete($key, $this->db))
                    $result++;
            }
            $key = @dba_nextkey($this->db);
        }

        return $result;
    }

}

?>
