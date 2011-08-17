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
 * Configurator class for the PAPI Authentication Engine.
 * @package phpPoA2
 * @subpackage PAPIAuthenticationEngine
 */
class PAPIConfigurator extends GenericConfigurator {

    protected $mandatory_options = array('Location',
                                         'CookieDomain',
                                         'LKey',
                                         'PubKeyFile',
                                         'CookieTimeout',
                                         'DBType',
                                         'RedirectURL',
                                         'RedirectType');

    /**
     * 
     * @return string 
     */
    public function getLocation() {
        return $this->cfg['Location'];
    }

    /**
     * 
     * @return string 
     */
    public function getCookieDomain() {
        return $this->cfg['CookieDomain'];
    }

    /**
     * 
     * @return string
     */
    public function getLKey() {
        return $this->cfg['LKey'];
    }

    /**
     * 
     * @return string 
     */
    public function getPubKeyFile() {
        return $this->cfg['PubKeyFile'];
    }

    /**
     * 
     * @return integer 
     */
    public function getCookieTimeout() {
        return $this->cfg['CookieTimeout'];
    }

    /**
     * 
     * @return integer 
     */
    public function getDBType() {
        return $this->cfg['DBType'];
    }

    /**
     * 
     * @return string 
     */
    public function getDBFile() {
        return @$this->cfg['DBFile'];
    }

    /**
     * 
     * @return string 
     */
    public function getDBHost() {
        return @$this->cfg['DBHost'];
    }

    /**
     * 
     * @return string 
     */
    public function getDBUser() {
        return @$this->cfg['DBUser'];
    }

    /**
     * 
     * @return string 
     */
    public function getDBPassword() {
        return @$this->cfg['DBPassword'];
    }

    /**
     * 
     * @return string 
     */
    public function getDBName() {
        return @$this->cfg['DBName'];
    }

    /**
     *
     * @return string 
     */
    public function getDBPrefix() {
        return @$this->cfg['DBPrefix'];
    }

    /**
     * 
     * @return string 
     */
    public function getRedirectURL() {
        return $this->cfg['RedirectURL'];
    }

    /**
     * 
     * @return integer 
     */
    public function getRedirectType() {
        switch ($this->cfg['RedirectType']) {
        case GPOA_T:
        case "GPoA":
        case "GPOA":
        case "GPOA_T":
        case "GPoA_T":
            return GPOA_T;
        case AS_T:
        case "AS":
        case "AS_T":
            return AS_T;
        }
    }

    /**
     * 
     * @return string
     */
    public function getHomeLocatorID() {
        return @$this->cfg['HomeLocatorID'];
    }

    /**
     * 
     * @return string
     */
    public function getID() {
        return @$this->cfg['ID'];
    }

    /**
     *
     * @return string
     */
    public function getFriendlyName() {
        return @$this->cfg['FriendlyName'];
    }

    /**
     * 
     * @return string
     */
    public function getLogoutURL() {
        return @$this->cfg['LogoutURL'];
    }

    /**
     *
     * @return seconds
     */
    public function getRequestLifetime() {
        if (is_numeric(@$this->cfg['RequestLifetime']))
            return @$this->cfg['RequestLifetime'];

        return REQUEST_LIFETIME; 
    }

}

?>
