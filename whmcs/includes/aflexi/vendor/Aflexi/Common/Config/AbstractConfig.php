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
 
# namespace Aflexi\CdnEnabler;

/**
 * Abstract implementation of Aflexi_Common_Config_Config.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.8.20100908
 */
abstract class Aflexi_Common_Config_AbstractConfig implements Aflexi_Common_Config_Config, ArrayAccess, IteratorAggregate{
    
    private $array = array();
    
    static function initializeStatic(){
    }
    
    function get($key, $default = NULL, $namespace = self::DEFAULT_NAMESPACE){
        
        if(array_key_exists($namespace, $this->array)){
            
            if(array_key_exists($key, $this->array[$namespace])){
                return $this->array[$namespace][$key];
            }
        }
        
        return $default;
    }
    
    function set($key, $value, $namespace = self::DEFAULT_NAMESPACE){
        
        $rt = NULL;
        
        if(!array_key_exists($namespace, $this->array)){
            $this->array[$namespace] = array();
        }
        
        if(array_key_exists($key, $this->array[$namespace])){
            $rt = $this->array[$namespace][$key];
        }
        
        $this->array[$namespace][$key] = $value;
        return $rt;
    }
    
    function read($source, $namespace = self::DEFAULT_NAMESPACE, $merge = TRUE){
        
        $data = NULL;
        
        if(empty($source)){
            throw new Aflexi_Common_Lang_NullArgumentException('source');
        }
        
        $data = $this->doRead($source, $namespace);
        
        // If not merging or the array is not initialized yet, we set it with 
        // a new array.
        if(!$merge || !isset($this->array[$namespace])){
            $this->array[$namespace] = array();
        }
        
        $this->array[$namespace] = array_merge(
            $this->array[$namespace],
            $data
        );
        
        return $this->array[$namespace];
    }
    
    protected abstract function doRead($source);
    
    /**
     * @param mixed $namespace Array is currently unsupported.
     * @see src/api/Aflexi/Common/Config/Aflexi_Common_Config_Config::write()
     */
    function write($destination, $namespace = self::DEFAULT_NAMESPACE){
        
        $data;
        
        if(empty($destination)){
            throw new Aflexi_Common_Lang_NullArgumentException('destination');
        }
        
        if(!is_string($namespace)){
            throw new InvalidArgumentException("'namespace' has to be a string");
        }
        
        if(!isset($this->array[$namespace])){
            throw new InvalidArgumentException("Namespace '$namespace' doesn't exist in config");
        }
        
        $this->doWrite($destination, $this->array[$namespace]);
    }
    
    protected abstract function doWrite($destination, array $data);
    
    /*
     * SPL Array Overloading
     * -------------------------------------------------------------------------
     */

    function offsetExists($offset){
        return array_key_exists($offset, $this->array);
    }

    function offsetGet($offset){
        return $this->array[$offset];
    }

    function offsetSet($offset, $value){
        $this->array[$offset] = $value;
    }

    function offsetUnset($offset){
        if(array_key_exists($offset, $this->array)){
            unset($this->array[$offset]);
        }
    }
    
    function getIterator(){
        return new ArrayIterator($this->array);
    }
}

Aflexi_Common_Config_AbstractConfig::initializeStatic();

?>