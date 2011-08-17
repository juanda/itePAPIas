<?php
/**
 * @copyright Copyright 2005-2011 RedIRIS, http://www.rediris.es/
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
 * @package phpPoA2
 * @author Jaime Perez <jaime.perez@rediris.es>
 * @filesource
 */

/**
 * The OID for public keys.
 */
define("PUBLIC_KEY_OID", "1.2.840.113549.1.1.1");

/**
 * Class to manage public keys.
 *
 * @package phpPoA2
 * @subpackage crypto
 */
class RSAPublicKey {

    protected $pem;
    protected $der;
    protected $modulus;
    protected $exponent;
    protected $bits;

    /**
     *  Build a new public key from its PEM representation.
     */
    public function __construct($pem = '') {
        if (!empty($pem)) {
            $this->fromPEM($pem);
        }
    }

    /**
     * Build the public key from its DER representation.
     */
    public function fromDER($der) {
        $this->der = $der;

        // calculate pem
        $this->pem = "-----BEGIN PUBLIC KEY-----\n";
        $this->pem .= wordwrap(base64_encode($der), 64, "\n", true)."\n";
        $this->pem .= "-----END PUBLIC KEY-----\n";

        // fill in the rest of the object
        $this->decode();
    }

    /**
     * Build the public key from its PEM representation.
     */
    protected function fromPEM($pem) {
        $this->pem = $pem;

        // recover base64 encoded text from PEM
        $lines = explode("\n", $pem);
        unset($lines[(count($lines) -1)]);
        unset($lines[0]);

        // decode text to get DER format
        $this->der = base64_decode(implode($lines));

        // fill in the rest of the object
        $this->decode();
    }

    /**
     * Extract public key details from its DER representation.
     */
    protected function decode() {
        $buffer = $this->der;

        // decode root
        $asn = new ASN1();
        $asn->decode($buffer);

        // get main sequence
        $items = $asn->getValues();

        // get the key info
        $keydata = $items[1]->getValue();
        $key = new ASN1();
        $key->decode($keydata);
        $data = $key->getValues();

        // get modulus
        $this->modulus = $data[0]->getValue();

        // get public exponent
        $this->exponent = $data[1]->getValue();

        // compute bits
        $this->bits = strlen($this->modulus) * 8;
    }

    /**
     * Build a new public key represented by it DER and PEM formats
     * from its modulus and public exponent.
     */
    public function encode() {
        // TODO
    }

    /**
     * Get the PEM representation of the key.
     */
    public function getPEM() {
        return $this->pem;
    }

    /**
     * Get the DER representation of the key.
     */
    public function getDER() {
        return $this->der;
    }

    /**
     * Get the modulus of the key.
     */
    public function getModulus() {
        return $this->modulus;
    }

    /**
     * Get the exponent of the key.
     */
    public function getExponent() {
        return $this->exponent;
    }

    /**
     * Get the length of the key in bits.
     */
    public function getBits() {
        return $this->bits;
    }

    /**
     * Set the modulus of the key.
     */
    public function setModulus($modulus) {
        $this->modulus = $modulus;
    }

    /**
     * Set the public exponent of the key.
     */
    public function setPublicExponent($exponent) {
        $this->exponent = $exponent;
    }
}

?>
