<?php

/*
 * LICENSE AGREEMENT
 * -----------------------------------------------------------------------------
 * Copyright (c) 2010 Aflexi Sdn. Bhd.
 * 
 * This file is part of Aflexi_Common.
 * 
 * Aflexi_Common is published under the terms of the Open Software License 
 * ("OSL") v. 3.0. For the full copyright and license information, please view 
 * the LICENSE file that was distributed with this source code.
 * -----------------------------------------------------------------------------
 */
 
# namespace Aflexi\Common\Security;

/**
 * Methods for security related operations, such as hashing, two-way crypt, 
 * etc.
 * 
 * Changes:
 *  - 20100622 - Added naming convention, methods shall start from digest*, rand*,
 *    encrypt*, decrypt*.
 * 
 * @author yclian
 * @since 1.0
 * @version 2.5.20100622
 */
final class Aflexi_Common_Security_SecurityUtils{
    
    /**
     * Get a random MD5 hash. Given a seed.
     * 
     * @since 2.5
     * @param string $seed
     * @return string
     */
    static function randMd5($seed = 'foobar'){
        return md5(''.$seed.time().mt_rand(0, mt_getrandmax()));
    }
    
    /**
     * Encrypt text to either binary data or base64-encoded text.
     *
     * @param $key
     * @param $input
     * @param $base64 If TRUE, base64-encoded text will be returned instead.
     * @return mixed
     */
    static function encryptAes($key, $input, $base64 = TRUE){
        
        $length;
        $encryptedData;
        
        $length = strlen($input);
        $input = $length.'|'.$input;
        $encryptedData = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $input, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND));
        
        if($base64){
            return base64_encode($encryptedData);
        } else{
            return $encryptedData;
        }
    }

    /**
     * Decrypt an AES-encrypted data.
     *
     * @since 2.5
     * @param string $key Secret key.
     * @param mixed $input Binary or base64-encoded string.
     * @param bool $base64 If set as TRUE, $input will be treated as base64-encoded string.
     * @return string
     */
    static function decryptAes($key, $input, $base64 = TRUE){
        
        $encryptedData;
        $decryptedData;
        $originalData;
        
        if($base64){
            $encryptedData = base64_decode($input);
        } else{
            $encryptedData = $input;
        }
        
        $descryptedData = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $encryptedData, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND));
        list($length, $paddedData) = explode('|', $descryptedData, 2);
        $originalData = substr($paddedData, 0, $length);
        return $originalData;
    }
}
