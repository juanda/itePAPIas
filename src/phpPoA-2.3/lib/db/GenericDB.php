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
 * @author Candido Rodriguez <candido.rodriguez@rediris.es>
 * @filesource
 */

/**
 * Provides the basic operations and defines the interface of any DB operating class.
 * @package phpPoA2
 * @subpackage GenericDatabaseHandlers
 */
abstract class GenericDB {

    protected $cfg;
    protected $error = null;
    protected $db;
    protected $mandatory_options = array();

    /**
     * Main constructor.
     * @param cfg A *Configurator object with the current configuration.
     */
    public function __construct($cfg) {
        if (!$cfg instanceof GenericConfigurator) {
            throw new PoAException('invalid-config', E_USER_ERROR, array());
        }
        $this->cfg = $cfg;
        $this->configure();
    }

    /**
     * Configure the database handler.
     * @return boolean true if success, PoAException if error.
     */
    protected function configure() {
        // validate mandatory params
        $this->cfg->validate($this->mandatory_options);
    }

    /**
     * Opens the database.
     * @return boolean true if success, false if error.
     */
    abstract public function open();

    /**
     * Closes the database.
     * @return boolean true if success, false if error.
     */
    abstract public function close();

    /**
     * Checks if the specified key exists in the database.
     * @param key The key to look for.
     * @return boolean true if the key exists, false otherwise.
     */
    abstract public function check($key);

    /**
     * Replaces the specified key with a new value. If the key does not
     * exist previously, it will be created.
     * @param key The key to replace.
     * @param value The new value for the specified key.
     * @return boolean true if success, false if error.
     */
    abstract public function replace($key, $value);

    /**
     * Gets the value of the specified key.
     * @param key The key to look for.
     * @return mixed|boolean The value for that key, false if it does not exist.
     */
    abstract public function fetch($key);

    /**
     * Gets all the contents stored in the database.
     * @return array An array with all the rows of the database.
     */
    abstract public function fetch_all();

    /**
     * Removes the specified key from the database.
     * @param key The key to remove.
     * @return boolean true if success, false if error.
     */
    abstract public function delete($key);
}

?>
