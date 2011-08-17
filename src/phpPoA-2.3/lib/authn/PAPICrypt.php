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
 * @filesource
 */

/**
 * Cryptographic routines for PAPI protocol v1.
 */
class PAPICrypt {

    // asymetric
    protected $pubkey;
    protected $pubkeylen;
    protected $privkey;
    protected $privkeylen;

    // symmetric
    protected $symmetric;

    /**
     * 
     */
    public function __construct($symmetric = "", $pubkey = "", $privkey = "", $passphrase = "") {
        if (!empty($symmetric)) $this->setSymmetricKey($symmetric);

        if (!empty($pubkey)) $this->setPublicKey($pubkey);

        if (!empty($privkey)) $this->setPrivateKey($privkey, $passphrase);
    }

    /**
     * 
     */
    public function setSymmetricKey($symmetric) {
        $this->symmetric = $symmetric;
    }

    /**
     * 
     */
    public function setPublicKey($pubkey) {
        // configure pubkey
        $this->pubkey = file_get_contents($pubkey);
        if (!$this->pubkey) {
            throw new PoAException('pubkey-error', E_USER_ERROR, array($pubkey));
        }
 
        // calculate key length
        if (function_exists("openssl_pkey_get_details")) {
            $resource = openssl_pkey_get_public($this->pubkey);
            if (!$resource) {
                throw new PoAException('pubkey-error', E_USER_ERROR, array($pubkey));
            }
            $details = openssl_pkey_get_details($resource);
            $this->pubkeylen = $details['bits'];
        } else {
            $key = new RSAPublicKey($this->pubkey);
            $this->pubkeylen = $key->getBits();
        }
   }

    /**
     * 
     */
    public function setPrivateKey($privkey, $passphrase = "") {
        // configure privkey
        $this->privkey = file_get_contents($privkey);
        if (!$this->privkey) {
            throw new PoAException('privkey-error', E_USER_ERROR, array($privkey));
        }

        // calculate key length
        if (function_exists("openssl_pkey_get_details")) {
            $resource = openssl_pkey_get_private($this->privkey, $passphrase);
            if (!$resource) {
                throw new PoAException('privkey-error', E_USER_ERROR, array($privkey));
            }
            $details = openssl_pkey_get_details($resource);
            $this->privkeylen = $details['bits'];
        } else {
            $key = new RSAPrivateKey($this->privkey);
            $this->privkeylen = $key->getBits();
        }
    }

    /**
     * 
     */
    protected function initializeAES() {
        $resource = false;

        // configure
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv     = mcrypt_create_iv(mcrypt_enc_get_iv_size($module), MCRYPT_RAND);
        $key    = substr($this->symmetric, 0, mcrypt_enc_get_key_size($module));

        // load
        if (mcrypt_generic_init($module, $key, $iv) === 0) {
            $resource = $module;
        }

        return $resource;
    }

    /**
     * 
     */
    protected function initialize3DES() {
        $resource = false;

        // configure
        $module = mcrypt_module_open('tripledes', '', 'ecb', '');
        $iv     = mcrypt_create_iv(mcrypt_enc_get_iv_size($module), MCRYPT_RAND);
        $key    = substr($this->symmetric, 0, mcrypt_enc_get_key_size($module));

        // load
        if (mcrypt_generic_init($module, $key, $iv) === 0) {
            $resource = $module;
        }

        return $resource;
    }

    /**
     * 
     */
    protected function endSymmetric($module) {
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
    }

    /**
     * 
     */
    public function encryptAES($input) {  
        $result = '';
        $module = $this->initializeAES();

        // encrypt the text
        if ($module) {
            $result = mcrypt_generic($module, $input);
            $this->endSymmetric($module);
        }

        // encode and return
        return base64_encode($result);
    }

    /**
     * 
     */
    public function decryptAES($input) {
        $result = "";
        $module = $this->initializeAES();

        // decrypt the text
        if ($module) {
            $result = mdecrypt_generic($module, base64_decode($input));
            $this->endSymmetric($module);
        }

        // trim and return
        return trim($result);
    }

    /**
     * 
     */
    public function encrypt3DES($input) {
        $result = "";
        $module = $this->initialize3DES();

        // encrypt the text
        if ($module) {
            $result = mcrypt_generic($module, $input);
            $this->endSymmetric($module);
        }

        // encode and return
        return base64_encode($result);
    }

    /**
     * 
     */
    public function decrypt3DES($input) {
        $result = "";
        $module = $this->initialize3DES();

        // decrypt the text
        if ($module) {
            $result = mdecrypt_generic($module, base64_decode($input));
            $this->endSymmetric($module);
        }

        // trim and return
        return trim($result);
    }

    /**
     * 
     */
    public function encrypt($input) {
        $result = '';

        // get the block size
        $block_size = $this->privkeylen / 8;

        // get total no. of blocks
        $block_total = ceil(strlen($input) / $block_size);

        // encrypt all blocks
        for ($i = 0; $i < $block_total; $i++) {
            $encrypted = '';
            $index = $i * $block_size;
            $block = substr($input, $index, $block_size);
            if (!openssl_private_encrypt($block, $encrypted, $this->privkey)) continue;
            $result .= $encrypted;
        }

        // encode and return
        return base64_encode($result);
    }

    /**
     * 
     */
    public function decrypt($input) {
        $result = '';
        $input = base64_decode($input);


        // get the block size
        $block_size = $this->pubkeylen / 8;

        // get expected no. of blocks
        $block_total = ceil(strlen($input) / $block_size);

        // decrypt all blocks
        for ($i = 0; $i < $block_total; $i++) {
             $decrypted = '';
             $index = $i * $block_size;
             $block = substr($input, $index, $block_size);
             if (!openssl_public_decrypt($block, $decrypted, $this->pubkey)) continue;
             $result .= $decrypted;
        }

        return $result;
    }

}

?>
