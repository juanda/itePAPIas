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
 * Main configurator for the phpPoA.
 * @package phpPoA2
 */
class PoAConfigurator extends GenericConfigurator {

    protected $mandatory_options = array('LogFile',
                                         'LogLevel');

    public function __construct($site, $file = null) {
        
        global $poa_messages;

        // defaults
        $this->cfg["ROOT_DIR"] = dirname(__FILE__);
        $this->cfg['Language'] = "en_EN";
        include_once $this->cfg["ROOT_DIR"]."/messages/messages-".$this->cfg['Language'].".php";
        $file_path = "PoA.conf";

        // configure        
       // $v = get_cfg_var('poa_conf_file');
        $v = POA_CONF_FILE;
        
        //$v = dirname(__FILE__).'/PoA.conf';
       
        if ($file) {
            $file_path = $file;
        } else if (!empty($v)) {
            $file_path = $v;            
        }
        parent::__construct($file_path, $site);

        // load internationalized messages
        foreach (glob($this->cfg["ROOT_DIR"]."/messages/messages-*.php") as $filename) {
            //include_once $this->cfg["ROOT_DIR"]."/messages/messages-en_EN.php";
            if (PoAUtils::lang_code($filename) === $this->cfg['Language']) {
                include_once $filename;
            }
        }

        // check configuration
        $this->validate();        
    }

    public function getLogFile() {
        return $this->cfg['LogFile'];
    }

    public function isDebug() {
        return @($this->cfg['Debug'] === true);
    }

    public function getLanguage() {
        return @$this->cfg['Language'];
    }

    public function getLogLevel() {
        return $this->cfg['LogLevel'];
    }

    public function getNoAuthErrorURL() {
        return @$this->cfg['NoAuthErrorURL'];
    }

    public function getSystemErrorURL() {
        return @$this->cfg['SystemErrorURL'];
    }

    public function getInviteErrorURL() {
        return @$this->cfg['InviteErrorURL'];
    }

    public function getAuthnEngine() {
        return @$this->cfg['AuthnEngine'];
    }

    public function getAuthzEngines() {
        if (!@is_array($this->cfg['AuthzEngines'])) {
            return (isset($this->cfg['AuthzEngines'])) ? array($this->cfg['AuthzEngines']) : array();
        }

        return @$this->cfg['AuthzEngines'];
    }

    public function getAuthnEngineConfFile() {
        return @$this->cfg['AuthnEngineConfFile'];
    }

    public function getAuthzEngineConfFile($engine) {
        return @$this->cfg['AuthzEnginesConfFiles'][$engine];
    }

    public function getAuthzLevels() {
        if (!@is_array($this->cfg['AuthzLevels'])) {
            return (isset($this->cfg['AuthzLevels'])) ? array($this->cfg['AuthzLevels']) : array();
        }

        return @$this->cfg['AuthzLevels'];
    }

}

?>
