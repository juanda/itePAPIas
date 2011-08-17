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
 * A generic engine class.
 * @abstract
 * @package phpPoA2
 */
abstract class GenericEngine {

    protected $cfg;
    protected $hooks = array();
    protected $valid_hooks = array();
    protected $engine_type;
    protected $handler;

    /**
     * Main constructor for the engine.
     * @param file The path to the configuration file. Can be in the include path.
     * @param section The section of the configuration file, if any.
     */
    public function __construct($file, $site) {
        $this->configure($file, $site);
    }

    /**
     * Configure the engine.
     * @param file The path to the configuration file. Can be in the include path.
     * @param section The section of the configuration file, if any.
     * @return void
     */
    public function configure($file,$section) {
        // search and initialize configurator
        $configurator = str_replace($this->engine_type."Engine", "", get_class($this))."Configurator";
        $this->cfg = new $configurator($file,$section);

         // initialize hooks
        foreach ($this->valid_hooks as $hook) $this->hooks[$hook] = array();
    }

    /**
     * Adds a function to the specified hook, which will be executed at some point of the code.
     * @param name The name of the hook.
     * @param hook A mixed object. Can be the name of a function (string) or
     * an array with two elements: the former, the name of a class or an object,
     * and the latter the name of the method.
     * @return boolean true if successful, false in any other case.
     */
    public function addHook($name, $hook) {
         // check if the hook exists
         if (!in_array($name, $this->valid_hooks)) return false;

         // check if its a hook
         if (!($hook instanceof Hook)) return false;

         // check if the hook is registered
         if (in_array($hook, $this->hooks[$name])) return false;

         trigger_error(PoAUtils::msg('add-hook', array($hook->getName(), $name)));
         $this->hooks[$name][] = $hook;
    }

    /**
     * Removes a function fromt he specified hook.
     * @param name The name of the hook.
     * @param hook A mixed object. Can be the name of a function (string) or
     * an array with two elements: the former, the name of a class or an object,
     * and the latter the name of the method.
     * @return boolean true if successful, false in any other case.
     */
    public function removeHook($name, $hook) {
         // check if the hook exists
         if (!in_array($name, $this->valid_hooks)) return false;

         // check if the hook is registered
         if (!in_array($hook, $this->hooks[$name])) return false;

         // search and remove
         $new = array();
         foreach ($this->hooks[$name] as $item) {
             if ($item != $hook) {
                 $new[] = $item;
             }
         }
         $this->hooks[$name] = $new;

         trigger_error(PoAUtils::msg('remove-hook', array($hook->getName(), $name)));
         return true;
    }

    /**
     * Run all hooks attached to an specific action.
     * @param hook The name of the hook.
     * @param params An array with all params (in order) that must be passed to the function.
     */
    protected function runHooks($hook, &$params) {
        // check if the hook exists
        if (!in_array($hook, $this->valid_hooks)) return false;

        foreach ($this->hooks[$hook] as $h) {
            trigger_error(PoAUtils::msg('running-hook', array($h->getName(), $hook)));
            if ($h->run($params)) break;
        }
    }

    /**
     * Set the event handler to the one specified.
     * @param handler The event handler to use.
     */
    public function setHandler($handler) {
        $this->handler = $handler;
    }

    /**
     * Register error and exception handlers for logging. Use it only for methods not declared
     * in the interface that could trigger errors.
     */
    protected function registerHandler() {
        // register autoload function
        set_exception_handler(array($this->handler, "exceptionHandler"));
        set_error_handler(array($this->handler, "errorHandler"));
    }

    /**
     * Unregister error and exception handlers. Use it only for methods not declared in the
     * interface that previously called registerHandler() method.
     */
    protected function clean() {
        // clean
        restore_exception_handler();
        restore_error_handler();
    }

}

?>
