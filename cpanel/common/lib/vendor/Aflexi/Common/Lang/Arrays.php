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
 
# namespace Aflexi\Common\Lang;

/**
 * Utility methods for array.
 * 
 * @author yclian
 * @since 2.5
 * @version 2.6.20100715
 */
final class Aflexi_Common_Lang_Arrays{
    
    /**
     * Get an element of an array. Useful in writing one-liner.
     * 
     * @param $array
     * @param $key
     * @see http://stackoverflow.com/questions/1182452/any-way-to-access-array-directly-after-method-callhttp://stackoverflow.com/questions/1182452/any-way-to-access-array-directly-after-method-call
     */
    static function get(array $array, $key, $defaultValue = NULL){
        if(array_key_exists($key, $array)){
            return $array[$key];
        } else{
            return $defaultValue;
        }
    }
    /**
     * Determine if a given value is an associative array.
     * 
     * @param $array
     * @see http://www.php.net/manual/en/function.is-array.php#98305
     */
    static function isAssociative($array){
        
        // Taken from note contributed by JTS
        return is_array($array) && (
            count($array) == 0 || 
            0 !== count(
                array_diff_key(
                    $array, array_keys(array_keys($array))
                )
            )
        );
    }
    
    /**
     * Cast a multi-dimensional associative array to an object.
     * 
     * @param $assoc
     * @return mixed An object if input is an associative array, itself otherwise.
     */
    static function toObject($assoc){
        
        $rt;
        
        if(!self::isAssociative($assoc)){
            return $assoc; 
        }
        
        $rt = new stdClass();
        if(count($assoc) > 0){
            foreach($assoc as $k => $v){
                $rt->$k = self::toObject($v);
            }
            return $rt;
        } else{
            return (object) $assoc;
        }
    }
    
    /**
     * Rebuild an (new) array, with index of element extracted from the element. An
     * element has to be either an array or object.
     * 
     * A repeated key will simply be replacing an existing element in the re-
     * turning array, thus there's no guarantee a same-size array will be re-
     * turned.
     * 
     * @since 2.6
     * @version 2.6.20100715
     * @param $array
     * @param mixed $key int or string of the elemenet - its index or property.
     * @return array
     */
    static function rebuildKeys(array $array, $key){
        
        $rt = array();
        
        foreach($array as $e){
            if(is_array($e)){
                $rt[$e[$key]] = $e;
            } else if(is_object($e)){
                $rt[$e->{$key}] = $e;
            } else{
                throw new InvalidArgumentException('Expected element to be an array or object');
            }
        }
        
        return $rt;
    }
}

if(!function_exists('is_assoc')){
    
    /**
     * @see Aflexi_Common_Lang_Arrays#isAssociative()
     * @param $array
     * @return bool
     */
    function is_assoc($array) {
        return Aflexi_Common_Lang_Arrays::isAssociative($array);
    }
}

if(!function_exists('array_get')){
    
    /**
     * @see Aflexi_Common_Lang_Arrays#get()
     * @param $array
     * @param $key
     * @param $default_value
     * @return mixed
     */
    function array_get($array, $key, $default_value = NULL) {
        return Aflexi_Common_Lang_Arrays::get($array, $key, $default_value);
    }
}

if(!function_exists('array_rebuild_keys')){
    
    /**
     * @since 2.6
     * @version 2.6.20100715
     * @see Aflexi_Common_Lang_Arrays#rebuildKeys()
     * @param $array
     * @param $key
     */
    function array_rebuild_keys($array, $key){
        return Aflexi_Common_Lang_Arrays::rebuildKeys($array, $key);
    }
}

if(!function_exists('assoc_to_object')){
    
    /**
     * @see Aflexi_Common_Lang_Arrays#toObject()
     * @param $assoc
     * @return mixed
     */
    function assoc_to_object($assoc){
        return Aflexi_Common_Lang_Arrays::toObject($assoc);
    }
}

?>
