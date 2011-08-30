<?php
/*
  This file is part of itePAPIas.
  Foobar is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  Foobar is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
<?php
namespace TeyDe\Papi\Core;

class PAPIASCrypto
{
    const PADDINGSIZE = 11;    

    public static function encrypt_AES($input, $key)
    {
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

        $key = substr($key, 0, mcrypt_enc_get_key_size($td));

        if (mcrypt_generic_init($td, $key, $iv) != -1)
        {
// Encrypt the text
            $crypttext = mcrypt_generic($td, $input);
            mcrypt_generic_deinit($td);
        }

        mcrypt_module_close($td);

// Encode the encrypted text
        $crypttext = base64_encode($crypttext);

        return $crypttext;
    }

    public static function decrypt_AES($input, $key)
    {

// Decode the encrypted text
        $input = base64_decode($input);

        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

        $key = substr($key, 0, mcrypt_enc_get_key_size($td));

        if (mcrypt_generic_init($td, $key, $iv) != -1)
        {
// Decrypt the text
            $decrypttext = mdecrypt_generic($td, $input);
            mcrypt_generic_deinit($td);
        }

        mcrypt_module_close($td);
        $decrypttext = trim($decrypttext);

        return $decrypttext;
    }

// 3DES Crypt Functions (Not used in phpPoA)

    public static function encrypt_3DES($input, $key)
    {

        $td = mcrypt_module_open('tripledes', '', 'ecb', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

        $key = substr($key, 0, mcrypt_enc_get_key_size($td));

        if (mcrypt_generic_init($td, $key, $iv) != -1)
        {
// Encrypt the text
            $crypttext = mcrypt_generic($td, $input);
            mcrypt_generic_deinit($td);
        }

        mcrypt_module_close($td);

// Encode the encrypted text
        $crypttext = base64_encode($crypttext);

        return $crypttext;
    }

    public static function decrypt_3DES($input, $key)
    {

// Decode the encrypted text
        $input = base64_decode($input);

        $td = mcrypt_module_open('tripledes', '', 'ecb', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

        $key = substr($key, 0, mcrypt_enc_get_key_size($td));

        if (mcrypt_generic_init($td, $key, $iv) != -1)
        {
// Decrypt the text
            $decrypttext = mdecrypt_generic($td, $input);
            mcrypt_generic_deinit($td);
        }

        mcrypt_module_close($td);
        $decrypttext = trim($decrypttext);

        return $decrypttext;
    }

// Openssl Crypt Functions (Not used in phpPoA)
//////////////////////////////////////////////////////////////////////////////////////
// Openssl Crypt Functions 
//////////////////////////////////////////////////////////////////////////////////////
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
// funcion openssl_encrypt (Not used in phpPoA)
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    public static function openssl_encrypt($in, $key, $key_bits = 0)
    {
// Get the byte size of data string
        $inputSize = strlen($in);

// Get details of the key
        $res = openssl_get_privatekey($key);
        if ($key_bits == 0)
        {
            $key_details = openssl_pkey_get_details($res);
        } else
        {
            $key_details = array('bits' => $key_bits);
        }

// Get the output block maximun size in Bytes
        $outputBlockSize = $key_details['bits'] / 8;

// Total number of blocks
        $inputBlockSize = $outputBlockSize - PAPIASCrypto::PADDINGSIZE;
        $numBlocks = ceil($inputSize / $inputBlockSize);

// Start to encrypt.
        $blockCount = 0;
        $cryptBuffer = array();

        while ($blockCount < $numBlocks)
        {
            $index = $blockCount * $inputBlockSize;
            $block = substr($in, $index, $inputBlockSize);
            openssl_private_encrypt($block, $crypttext, $key);
            $cryptBuffer[$blockCount] = $crypttext;
            $blockCount++;
        }
// Now joint the array with the blocks string encripted
        $cryptData = join("", $cryptBuffer);

        $base64CryptData = base64_encode($cryptData);

// Return the encrypted, joined and base64 encode data string.
        return $base64CryptData;
    }

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
// funcion openssl_decrypt
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
    public static function openssl_decrypt($in, $key, $error_log, $key_bits = 0)
    {
// Decode the base64 input string
        $in = base64_decode($in);

// Get the byte size of data string 
        $inputSize = strlen($in);

// Get details of the key
        $res = openssl_get_publickey($key);
        if ($key_bits == 0)
        {
            $key_details = openssl_pkey_get_details($res);
        } else
        {
            $key_details = array('bits' => $key_bits);
        }

// Get the output block maximun size in Bytes
        $outputBlockSize = $key_details['bits'] / 8;

//$inputBlockSize = $outputBlockSize - PADDINGSIZE;
        $inputBlockSize = $outputBlockSize;
        $numBlocks = ceil($inputSize / $inputBlockSize);

// Start to decrypt.
        $blockCount = 0;
        $decryptBuffer = array();



        while ($blockCount < $numBlocks)
        {
            $index = $blockCount * $inputBlockSize;
            $block = substr($in, $index, $inputBlockSize);
// Decrypt the text
            if (!openssl_public_decrypt($block, $decrypttext, $key))
            {
// Cannot decrypt!
                $error_message = date("d-M-Y H:i:s ") . "openssl_decrypt() Function: Cannot decrypt response, check GPoA public key.";
                error_log($error_message . "\n", 3, $error_log);
                return 0;
            }
            $decryptBuffer[$blockCount] = $decrypttext;
            $blockCount++;
        }

// Now joint the array with the blocks string encripted
        $decryptData = join("", $decryptBuffer);

// Return the base64 dencode, decrypted and joined data string.

        return $decryptData;
    }

}
?>
