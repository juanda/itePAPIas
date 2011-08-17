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
 * Configurator for the Invitation based authorization engine.
 * @package phpPoA2
 * @subpackage InviteAuthorizationEngine
 */
class InviteConfigurator extends AuthorizationConfigurator {

    protected $mandatory_options = array('InvitesDBType',
                                         'AuthorizedDBType',
                                         'AdminEmail',
                                         'InviteURL',
                                         'InviteText',
                                         'InviteSubject',
                                         'UniqueAttributes');

    /**
     * Returns 
     * @return string 
     */
    public function getAdminEmail() {
        return $this->cfg['AdminEmail'];
    }

    /**
     * Returns 
     * @return string 
     */
    public function getInviteText() {
        return $this->cfg['InviteText'];
    }

    /**
     * Returns 
     * @return string 
     */
    public function getInviteSubject() {
        return $this->cfg['InviteSubject'];
    }

    /**
     * Returns 
     * @return array 
     */
    public function getUniqueAttributes() {
        if (!is_array($this->cfg['UniqueAttributes'])) {
            return array($this->cfg['UniqueAttributes']);
        }
        return $this->cfg['UniqueAttributes'];
    }

    /*
     * Returns 
     * @return string
     */
    public function getEmailVerifyAttribute() {
        if (isset($this->cfg['EmailVerifyAttribute'])) {
            return $this->cfg['EmailVerifyAttribute'];
        }
        return "";
    }

    /*
     * Returns 
     * @return string 
     */
    public function getEmailVerifyRegEx() {
        if (isset($this->cfg['EmailVerifyRegEx'])) {
            return $this->cfg['EmailVerifyRegEx'];
        }
        return "(.*)";
    }

    /*
     * Returns 
     * @return string 
     */
    public function getEmailVerifyAlgRegEx() {
        if (isset($this->cfg['EmailVerifyAlgRegEx'])) {
            return $this->cfg['EmailVerifyAlgRegEx'];
        }
        return "";
    }

    /*
     * Returns 
     * @return boolean 
     */
    public function doEmailVerify() {
        if (isset($this->cfg['EmailVerify'])) {
            return $this->cfg['EmailVerify'] === true;
        }
        return false; // do not verify by default
    }

    /**
     * Returns 
     * @return string 
     */
    public function getInviteURL() {
        return $this->cfg['InviteURL'];
    }

    /**
     * Returns 
     * @return integer 
     */
    public function getInvitesDBType() {
        return $this->cfg['InvitesDBType'];
    }

    /**
     * Returns 
     * @return string 
     */
    public function getInvitesDBFile() {
        return @$this->cfg['InvitesDBFile'];
    }

    /**
     * Returns 
     * @return string
     */
    public function getInvitesDBHost() {
        return @$this->cfg['InvitesDBHost'];
    }

    /**
     * Returns 
     * @return string 
     */
    public function getInvitesDBUser() {
        return @$this->cfg['InvitesDBUser'];
    }

    /**
     * Returns 
     * @return string 
     */
    public function getInvitesDBPassword() {
        return @$this->cfg['InvitesDBPassword'];
    }

    /**
     * Returns 
     * @return string
     */
    public function getInvitesDBName() {
        return @$this->cfg['InvitesDBName'];
    }

    /**
     * Returns 
     * @return integer 
     */
    public function getAuthorizedDBType() {
        return $this->cfg['AuthorizedDBType'];
    }

    /**
     * Returns 
     * @return string 
     */
    public function getAuthorizedDBFile() {
        return @$this->cfg['AuthorizedDBFile'];
    }

    /**
     * Returns 
     * @return string 
     */
    public function getAuthorizedDBHost() {
        return @$this->cfg['AuthorizedDBHost'];
    }

    /**
     * Returns 
     * @return string 
     */
    public function getAuthorizedDBUser() {
        return @$this->cfg['AuthorizedDBUser'];
    }

    /**
     * Returns 
     * @return string
     */
    public function getAuthorizedDBPassword() {
        return @$this->cfg['AuthorizedDBPassword'];
    }

    /**
     * Returns 
     * @return string 
     */
    public function getAuthorizedDBName() {
        return @$this->cfg['AuthorizedDBName'];
    }

}

?>
