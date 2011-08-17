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
 * A simple class to write log messages to a file.
 * @package phpPoA2
 */
class PoALog {
    private $level;
    private $file;

    /**
     * Build a new logger.
     * @param level The level of this log.
     * @param file The file where to write messages.
     */
    public function __construct($level, $file) {
        $this->level = $level;
        if (!file_exists($file)) {
            if (!$tmp = @fopen($file, 'a')) {
                error_log(PoAUtils::msg('cannot-open-log', array($file)));
            }
            @fclose($tmp);
        }
        if (!is_writable($file)) {
            error_log(PoAUtils::msg('cannot-write-log', array($file)));
        }
        $this->file = $file;
    }

    /**
     * Write a message to the log file if the current log level allows it.
     * @param msg The message to write.
     * @param level The level of the message.
     */
    public function write($msg, $level = E_USER_NOTICE) {
        if ($level <= $this->level) {
            $file = @fopen($this->file, 'a');
            if (!$b = @fwrite($file, "[".date("D M d H:i:s Y")."] ".$msg."\n")) {
                error_log(PoAUtils::msg('cannot-write-log', array($this->file)));
            }
            @fclose($this->file);
        }
    }

    /**
     * Get the current log level.
     * @return integer The constant describing the current log level.
     */
    public function getLogLevel() {
        return $this->level;
    }
}

?>
