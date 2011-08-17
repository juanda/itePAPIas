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
 * Hook class that allows to run a function or a method specified by their name.
 * @package phpPoA2
 */
class Hook {

    protected $_hook;
    protected $_callable_name;
    protected $_name;

    /**
     * Build a new hook.
     * @param hook The name of a function or an array specifying a class and its method.
     * @return boolean true if everything is ok.
     * @throws PoAException if something goes wrong.
     */
    public function __construct($hook) {
        $printable = str_replace(array("\n", "\r", "\t", " "), "", print_r($hook, true));

        // perform some sanity checks
        // empty function
        if (empty($hook))
            throw new PoAException('hook-error', E_USER_ERROR, array($printable));

        // if array, check no. of elements
        if (is_array($hook) && (count($hook) != 2)) {
            throw new PoAException('hook-error', E_USER_ERROR, array($printable));

        // if array and no. of elements is ok, check it 
        } else if (is_array($hook)) {
            $class = $hook[0];
            $method = $hook[1];

            // not an object and the class does not exist
            if (!is_object($class) && !class_exists($class)) {
                throw new PoAException('hook-error', E_USER_ERROR, array($printable));

            // not an object, but the class exists
            } else if (!is_object($class)) {
                $object = new $class();

                // is there such method in the class?
                if (!is_callable(array($object, $method)))
                    throw new PoAException('hook-error', E_USER_ERROR, array($printable));

                $this->_hook = array($object, $method);
                $this->_name = $class."::".$method;

            // an object, is there such method in the class?
            } else if (!is_callable(array($class, $method))) {
                throw new PoAException('hook-error', E_USER_ERROR, array($printable));

            // an object, everything ok
            } else {
                $this->_hook = array($class, $method);
                $this->_name = get_class($class)."::".$method;
            }
            $this->_callable_name = '$this->_hook[0]->'.$method;

        // a function, check if we can call it
        } else if (!is_callable($hook)) {
            throw new PoAException('hook-error', E_USER_ERROR, array($printable));
        } else {
            $this->_hook = $hook;
            $this->_callable_name = $hook;
            $this->_name = $hook;
        }

        return true;
    }

    /**
     * Run the hook with the specified params.
     * @param args An array with all the params the function receives.
     * @return boolean The return value of the function. Please, bear in mind that the function
     * must return true if hooks proccessing should stop, false in any other case.
     */
    public function run(&$args) {
        // build params
        $i = 0;
        $input = "";
        while ($i < count($args)) {
            $input .= "\$args[".$i."],";
            $i++;
        }
        $input = trim($input, ",");
        $call = "return ".$this->_callable_name."(".$input.");";
        return eval($call);
    }

    /**
     * Get the name of the hook.
     * @return string The name.
     */
    public function getName() {
        return $this->_name;
    }

}

?>
