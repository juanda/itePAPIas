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
 * @package phpPoA2
 */

/**
 * Class to handle events inside the library.
 */
class PoAEventHandler {

    private $debug = true;
    private $local_site;
    private $log;

    /**
     * Main constructor.
     */
    public function __construct($site, $log = null, $debug = false) {
        $this->local_site = $site;
        $this->log = $log;
        $this->debug = $debug;
    }

    /**
     * Set the logger.
     * @param log The logger to use.
     */
    public function setLogger($log) {
        $this->log = $log;
    }

    /**
     * Set debugging mode.
     * @param debug A boolean value telling whether to make debugging active or not.
     */
    public function setDebug($debug) {
        $this->debug = $debug;
    }

    /**
     * Finish execution and print an error message with the current backtrace.
     */
    public function abort($code, $message) {
        if ($code & (E_ERROR | E_USER_ERROR)) {
            header("Content-type: text/html; charset=utf-8\r\n");
            echo "<html><body>\n";
            echo "<h1>".PoAUtils::msg("fatal-error")."</h1>\n";
            echo "<p>".PoAUtils::msg("error-desc")."</p>\n";
            echo "<h4>".PoAUtils::msg("error-message").":</h4>\n<div style=\"background: #ffcccc; padding: 10px; margin: 10px\"><tt>".$message."</tt></div>\n";
            echo "<h4>".PoAUtils::msg("session-id").":</h4>\n<div style=\"background: #cccccc; padding: 10px; margin: 10px\"><tt>#".$_COOKIE[$this->local_site.'_session']."</tt></div>\n";
            echo "<h4>".PoAUtils::msg("backtrace").":</h4>\n<div style=\"background: #cccccc; padding: 10px; margin: 10px\"><pre style=\"overflow: auto\">";
            debug_print_backtrace();
            echo "</tt></pre>\n";
            echo "</body></html>";
            die($code);
        }
    }

    /**
     * Override __autoload standard function to allow loading classes dinamically.
     */
    function autoloadHandler($class_name) {
        // look for the class file in the path
        $include_path = get_include_path();
        $include_path_tokens = explode(':', $include_path);
        foreach($include_path_tokens as $prefix){
            $path = $prefix . '/' . $class_name . '.php';
            if (file_exists($path)){
                include_once $path;
                if (class_exists($class_name, false) ||
                    interface_exists($class_name, false)) {
                    return true;
                }
            }
        }
        trigger_error(PoAUtils::msg('class-not-found', array($class_name)), E_USER_WARNING);
        return false;
    }

    /**
     * A generic exception handler that logs the exception message.
     * @param exception The catched exception.
     */
    public function exceptionHandler($exception) {
        trigger_error($exception, E_USER_ERROR);
    }

    /**
     * A generic error handler that logs the error message.
     * @param code The error code.
     * @param message The error message.
     * @param file The file that triggered the error.
     * @param line The line of the file where the error was triggered.
     */
    public function errorHandler($code, $message, $file, $line) {
        // detect disabled error_reporting for the @ error control operator
        if (!error_reporting()) return;

        // save original message
        $original = $message;

        // add the script:line that raised the error
        if ($this->debug && isset($file) && isset($line)) {
            $message = "[".$this->local_site."] [".basename($file).":".$line."] ".$message;
        }

        // add client IP
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = trim(array_pop(explode(",", $_SERVER['HTTP_X_FORWARDED_FOR'])));
        }
        $message = "[client ".$ip."] ".$message;

        // add a description of the code
        switch ($code) {
            case E_ERROR:
            case E_USER_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
                $message = "[error] ".$message;
                break;
            case E_WARNING:
            case E_CORE_WARNING:
            case E_USER_WARNING:
            case E_COMPILE_WARNING:
                $message = "[warning] ".$message;
                break;
            default:
                $message = "[info] ".$message;
        }

        // finally add the session reference
        $message = "[#".$_COOKIE[$this->local_site."_session"]."] ".$message;

        // log the message
        $this->log->write($message, $code);

        // check if the error was critical and therefore we have to exit
        $this->abort($code, $original);

        // continue execution
        return;
    }

}

?>
