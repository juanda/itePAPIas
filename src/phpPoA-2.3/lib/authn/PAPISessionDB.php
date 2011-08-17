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
 * PAPI binding to a PHP Session DB backend.
 * WARNING: please note that PHP only allows one session at a time, so using
 * this backend will break any applications that make use of sessions beneath.
 * @package phpPoA2
 * @subpackage PAPIAuthenticationEngine
 */
class PAPISessionDB extends SessionDB implements PAPIDB {

    protected $context;

    public function replaceContents($key, $get, $post, $request, $query, $method, $input) {
        $this->context['GET'] = $get;
        $this->context['POST'] = $post;
        $this->context['REQUEST'] = $request;
        $this->context['QUERY_STRING'] = $query;
        $this->context['REQUEST_METHOD'] = $method;
        $this->context['PHP_INPUT'] = $input;

        return parent::replace($key, $this->context);
    }

    public function fetch($key) {
        $this->context = parent::fetch($key);

        return $this->context;
    }

}

?>
