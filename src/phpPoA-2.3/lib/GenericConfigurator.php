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
 * Generic configuration class.
 * @abstract
 * @package phpPoA2
 */
abstract class GenericConfigurator {

    protected $cfg = array();
    protected $mandatory_options = array();

    /**
     * Main constructor.
     * @param file The file which stores the configuration.
     * @param site The section of the configuration that applies for the current site.
     * @throws PoAException if any error occurs.
     */
    public function __construct($file, $site) {
        // configure        
        if (is_readable($file)) {
            $this->configure($file, $site);
        } else {
            throw new PoAException('config-not-found', CONFIG_ERR, array($file));
        }
        // check configuration
        $this->validate();
    }

    /**
     * Read the configuration from the specified file and section.
     * @param file The file which stores the configuration.
     * @param site The section of the configuration that applies for the current site.
     * @throws PoAException if any error occurs.
     */
    protected function configure($file, $site) {
        // read config from file        
        include $file;

        // determine the configuration variable name by means of this class name
        $name = strtolower(str_replace("Configurator", "", get_class($this)))."_cfg";
        $set = eval("return isset(\$".$name.");");
        if (!$set) {
            throw new PoAException('config-err-php', CONFIG_ERR, array($name, $site));
        }

        // merge configurations
        $ev = eval("return array_merge(\$this->cfg, $".$name.");");
        if (is_array($ev)) $this->cfg = $ev;
        $ev = eval("return @array_merge(\$this->cfg, $".$name."[\"".$site."\"]);");
        if (is_array($ev)) $this->cfg = $ev;
        unset($this->cfg[$site]);
    }

    /**
     * Check all mandatory attributes are set.
     * @param mandatory Optional. An array of mandatory attributes that should be searched within the current configuration.
     * @return boolean true if success.
     * @throws PoAException if any error occurs.
     */
    public function validate($mandatory = null) {
        $options = $this->mandatory_options;
        if ($mandatory) $options = $mandatory;

        // validate mandatory params
        foreach ($options as $option) {
            if (!isset($this->cfg[$option])) {
                throw new PoAException('config-param-err', CONFIG_ERR, array($option));
            }
        }
        return true;
    }

}

?>
