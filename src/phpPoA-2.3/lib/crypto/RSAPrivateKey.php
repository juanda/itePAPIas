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
 * Class to manage private keys.
 *
 * @package phpPoA2
 * @subpackage crypto
 */
class RSAPrivateKey {

    protected $pem;
    protected $der;
    protected $modulus;
    protected $bits;
    protected $public_exponent;
    protected $private_exponent;
    protected $prime1;
    protected $prime2;
    protected $exponent1;
    protected $exponent2;
    protected $coefficient;

    /**
     *  Build a new private key from its PEM representation.
     */
    public function __construct($pem = '') {
        if (!empty($pem)) {
            $this->fromPEM($pem);
        }
    }

    /**
     * Build the private key from its DER representation.
     */
    public function fromDER($der) {
        $this->der = $der;

        // calculate pem
        $this->pem = "-----BEGIN RSA PRIVATE KEY-----\n";
        $this->pem .= wordwrap(base64_encode($der), 64, "\n", true)."\n";
        $this->pem .= "-----END RSA PRIVATE KEY-----\n";

        // fill in the rest of the object
        $this->decode();
    }

    /**
     * Build the private key from its PEM representation.
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
     * Extract private key details from its DER representation.
     */
    protected function decode() {
        $buffer = $this->der;

        // decode root
        $asn = new ASN1();
        $asn->decode($buffer);

        // get main sequence
        $items = $asn->getValues();

        // get the key values
        $this->modulus = $items[1]->getValue();
        $this->public_exponent = $items[2]->getInteger();
        $this->private_exponent = $items[3]->getValue();
        $this->prime1 = $items[4]->getValue();
        $this->prime2 = $items[5]->getValue();
        $this->exponent1 = $items[6]->getValue();
        $this->exponent2 = $items[7]->getValue();
        $this->coefficient = $items[8]->getValue();

        // compute bits
        $this->bits = strlen($this->modulus) * 8;
    }

    /**
     * Build a new private key represented by it DER and PEM formats
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
     * Get the public exponent of the key.
     */
    public function getPublicExponent() {
        return $this->public_exponent;
    }

    /**
     * Get the private exponent of the key.
     */
    public function getPrivateExponent() {
        return $this->private_exponent;
    }

    /**
     * Get the length of the key in bits.
     */
    public function getBits() {
        return $this->bits;
    }

    /**
     * Get the prime 1 of the key.
     */
    public function getPrime1() {
        return $this->prime1;
    }

    /**
     * Get the prime 2 of the key.
     */
    public function getPrime2() {
        return $this->prime2;
    }

    /**
     * Get the exponent 1 of the key.
     */
    public function getExponent1() {
        return $this->exponent1;
    }

    /**
     * Get the exponent 2 of the key.
     */
    public function getExponent2() {
        return $this->exponent2;
    }

    /**
     * Get the coefficient of the key.
     */
    public function getCoefficient() {
        return $this->coefficient;
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
        $this->public_exponent = $exponent;
    }

    /**
     * Set the private exponent of the key.
     */
    public function setPrivateExponent($exponent) {
        $this->private_exponent = $exponent;
    }

    /**
     * Set the prime 1 of the key.
     */
    public function setPrime1($prime) {
        $this->prime1 = $prime;
    }

    /**
     * Set the prime 2 of the key.
     */
    public function setPrime2($prime) {
        $this->prime2 = $prime;
    }

    /**
     * Set the exponent 1 of the key.
     */
    public function setExponent1($exponent) {
        $this->exponent1 = $exponent;
    }

    /**
     * Set the exponent 2 of the key.
     */
    public function setExponent2($exponent) {
        $this->exponent2 = $exponent;
    }

    /**
     * Set the coefficient of the key.
     */
    public function setCoefficient($coefficient) {
        $this->coefficient = $coefficient;
    }

}

?>
