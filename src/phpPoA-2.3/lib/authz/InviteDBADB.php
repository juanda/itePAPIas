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
 * Invites binding to a Berkeley DB backend.
 * @package phpPoA2
 * @subpackage InviteAuthorizationEngine
 */
class InviteDBADB extends DBADB implements InviteDB {

    protected $invite;
    protected $mandatory_options = array("InvitesDBFile");

    public function open() {
        $this->db = @dba_open($this->cfg->getInvitesDBFile(), "cl", "db4");
        return ($this->db) ? true : false;
    }

    public function replace_invite($key, $email, $expires) {
        $this->invite['email'] = $email;
        $this->invite['since'] = time();
        $this->invite['expires'] = $expires;

        return @dba_replace($key, serialize($this->invite), $this->db);
    }

    public function fetch($key) {
        $row = @dba_fetch($key, $this->db);

        if (!$row) return false;

        $this->invite = unserialize($row);

        return $this->invite;
    }

    public function fetch_all() {
        $list = parent::fetch_all();
        $result = array();

        foreach ($list as $key => $item) {
            $result[$key] = unserialize($item);
        }

        return $result;
    }

}

?>
