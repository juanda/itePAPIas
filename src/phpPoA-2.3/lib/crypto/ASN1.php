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
 * 
 * Mostly based on code by A. Oliinyk.
 */

/**
 * Basic tags.
 */
define("TAG_BER", 0x00);
define("TAG_BOOLEAN", 0x01);
define("TAG_INTEGER", 0x02);
define("TAG_BITSTRING", 0x03);


/**
 * Constructed tags.
 */
define("TAG_SEQUENCE", 0x30);

/**
 * This class implements ASN.1 encoding.
 * Please, be aware that this class is INCOMPLETE, as it
 * is intended for BER/DER encoding primarily.
 *
 * @package phpPoA2
 * @subpackage crypto
 */
class ASN1 {

    public $tag;
    public $value;

    /**
     * Build a new ASN1 from its tag and value.
     */
    public function __construct($tag = 0x00, $value = '') {
        $this->tag = $tag;
        $this->value = $value;
    }

    /**
     * Encode this object into ASN.1.
     */
    public function encode() {
        // set the tag
        $encoded = $this->tag;

        /*
         * Write the length of the contents.
         * Refer to ITU-T X.609 section 8.1.3 for details on how length must be encoded.
         */
        $len = strlen($this->value);
        if ($len < 127) { // store in one byte
            $encoded .= $this->writeByte($len);
        } else { // store the length in separate bytes
            $len = $this->int2bin($len);

            // write long length
            // first bit must be 1
            $encoded .= $this->writeByte(strlen($len) + 128);

            // write the bytes with real length
            $encoded .= $len;
        }

        // finally write the value
        $encoded .= $this->value;

        return $encoded;
    }

    /**
     * Decode an object in ASN.1 format.
     */
    public function decode(&$buffer) {
        // get ASN.1 tag
        $this->tag = $this->readByte($buffer);

        // get the length of de data
        $byte = $this->readByte($buffer);

        /*
         * Read the real length.
         * Refer to ITU-T X.609 section 8.1.3 for details on how length must be encoded.
         */
        // short length, stored in one byte
        if ($byte < 128) {
            $len = $byte;
        } else if ($byte === 128) {
            // indefinite length, read until two zero bytes are found
            $len = 0;
        } else if ($byte < 255) {
            // long length, from 1 up to 127 next bytes
            $len = $this->bin2int($this->readBytes($buffer, $byte - 128));
        } else {
            // 255 found, reserved value
            throw new Exception("Long length of 0x7f cannot be used. Reserved value.");
        }

        // read the value
        if ($len) {
            $this->value = $this->readBytes($buffer, $len);
        } else {
            // read until two zero bytes are found
            $this->value = '';
            while(true) {
                $this->value .= $this->readByte($buffer);
                $len = strlen($this->value);
                if ($len > 1 && $this->value{$len -2} === 0 && $this->value{$len -1} === 0) {
                    // end-of-contents octets found, trim them
                    $this->value = substr($this->value, 0, $len - 2);
                    break;
                }
            } 
        }

    }

    /**
     * Get the tag of this ASN.1.
     */
    public function getTag() {
        return $this->tag;
    }

    /**
     * Get the binary value of this ASN.1.
     */
    public function getValue() {
        if (!ord($this->value{0})) {
            // if the first byte is null, remove it
            return substr($this->value, 1);
        }

        return $this->value;
    }

    /**
     * Set the binary value of this ASN.1.
     */
    public function setValue($value) {
        if (strlen($value) > 1) {
            $byte = ord($value{0});

            /*
             * It is mandatory in ASN.1 to precede data by a null byte
             * if the first bit of the data is set.
             */
            if ($byte & 0x80) {
                $value = chr(0x00).$value;
            }
        }
        $this->value = $value;
    }

    /**
     * Get the integer value of this ASN.1.
     */
    public function getInteger() {
        return $this->bin2int($this->getValue());
    }

    /**
     * Set the value of this ASN.1 from an integer.
     */
    public function setInteger($int) {
        $this->setValue($this->int2bin($int));
    }

    /**
     * Get the sequence of values stored in this ASN.1.
     */
    public function getValues() {
        $result = array();
        $values = $this->value;

        while (strlen($values)) {
            $asn = new ASN1();
            $asn->decode($values);
            $result[] = $asn;
        }

        return $result;
    }

    /**
     * Set the value of this ASN.1 to be a sequence of values.
     */
    public function setValues($values) {
        $result = '';

        foreach ($values as $value) {
            $result .= $value->encode();
        }

        $this->value = $result;
    }

    /*********************
     * PROTECTED METHODS *
     *********************/

    /**
     * Read n bytes from a buffer and mov the internal pointer.
     */
    protected function readBytes(&$buffer, $length) {
        $result = substr($buffer, 0, $length);
        $buffer = substr($buffer, $length);
        return $result;
    }

    /**
     * Read first byte from the buffer and move the internal pointer.
     */
    protected function readByte(&$buffer) {
        return ord($this->readBytes($buffer, 1));
    }

    /**
     * Write bytes to a buffer.
     */
    protected function writeBytes(&$buffer, $bytes) {
        for ($i = 0; $i < strlen($bytes); $i++) {
            $this->writeByte($buffer, $bytes{$i});
        }
    }

    /**
     * Write a byte to a buffer.
     */
    protected function writeByte(&$buffer, $byte) {
        $buffer .= chr($byte);
    }

    /**
     * Decode an integer from its binary representation.
     */
    protected function bin2int($bin) {
        $result = 0;
        $len = strlen($bin);

        for ($i = 0; $i < $len; $i++) {
            $byte = $this->readByte($bin);
            $result += $byte << (($len - $i - 1) * 8);
        }

        return $result;
    }

    /**
     * Encode an integer in binary representation.
     */
    protected function int2bin($int) {
        $result = '';

        do {
            $byte = $int % 256;
            $result = chr($byte).$result;

            $int = ($int - $byte) / 256;
        } while ($int > 0);

        return $result;
    }

}

?>
