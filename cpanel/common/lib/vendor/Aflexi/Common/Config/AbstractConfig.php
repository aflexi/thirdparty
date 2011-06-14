<?php

require_once 'Aflexi/Common/Lang/Strings.php';

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
 
# namespace Aflexi\Common\Config;

/**
 * Abstract implementation of Aflexi_Common_Config_Config.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.8.20100922
 */
abstract class Aflexi_Common_Config_AbstractConfig implements Aflexi_Common_Config_Config, ArrayAccess, IteratorAggregate{
    
    const OPTION_USE_SHUTDOWN = 'useShutdown';
    
    static function initializeStatic(){
    }
    
    /**
     * @var array
     */
    protected $configs = array();
    
    /**
     * @var array
     */
    protected $options = array();
    
    /**
     * Default constructor. Extending classes are advised to maintain this 
     * convention.
     * 
     * @param array $options (optional) Options enable you to customize the 
     * 	behaviour of this object.
     */
    function __construct($options = array()){
        $this->options = array_merge(
            array(
                self::OPTION_USE_SHUTDOWN => FALSE
            ),
            $options
        );
        $this->preConfigure();
        $this->configure();
    }
    
    private function preConfigure(){
        if($this->options[self::OPTION_USE_SHUTDOWN]){
            register_shutdown_function(array($this, 'shutdown'));
        }
    }
    
    /**
     * Template function to be overriden. This function is called post 
     * construction.
     */
    protected function configure(){}
    
    /**
     * Support magic functions:
     * 
     * 	- exportXxx()
     *  - getXxx()
     *  - readXxx()
     *  - setXxx()
     *  - writeXxx()
     * 
     * @param string $method
     * @param string $args
     */
    function __call($method, $args){
        
        if(strpos($method, 'export') === 0){
            if(sizeof($args) == 0){
                return $this->export(lcfirst(substr($method, 6)));
            } else if(sizeof($args) == 1){
                return $this->export(lcfirst(substr($method, 6)), $args[0]);
            }
        }
        
        if(strpos($method, 'get') === 0){
            if(sizeof($args) == 1){
                return $this->get($args[0], NULL, lcfirst(substr($method, 3)));
            } else if(sizeof($args) == 2){
                return $this->get($args[0], $args[1], lcfirst(substr($method, 3)));
            }
        }
        
        if(strpos($method, 'read') === 0){
            if(sizeof($args) == 1){
                return $this->read($args[0], lcfirst(substr($method, 4)));
            } else if(sizeof($args) == 2){
                return $this->read($args[0], lcfirst(substr($method, 4)), $args[1]);
            }
        }
    
        if(strpos($method, 'set') === 0){
            if(sizeof($args) == 2){
                return $this->set($args[0], $args[1], lcfirst(substr($method, 3)));
            }
        }
        
        if(strpos($method, 'write') === 0){
            if(sizeof($args) == 1){
                return $this->write($args[0], lcfirst(substr($method, 5)));
            }
        }
        
        throw new InvalidArgumentException("Could not find handler for method '{$method}' with argument size of ".sizeof($args));
    }
    
    function export($namespace = NULL, &$var = FALSE){
        
        $rt;
        
        if(is_null($namespace)){
            $rt = $this->configs;
        } else{
            if(array_key_exists($namespace, $this->configs)){
                $rt = $this->configs[$namespace];
            } else{
                $rt = array();
            }
        }
        
        if($var === FALSE){
            return $rt;
        } else{
            $var = $rt;
        }
    }
    
    function get($key, $default = NULL, $namespace = self::DEFAULT_NAMESPACE){
        
        if(array_key_exists($namespace, $this->configs)){
            
            if(array_key_exists($key, $this->configs[$namespace])){
                return $this->configs[$namespace][$key];
            }
        }
        
        return $default;
    }
    
    function set($key, $value, $namespace = self::DEFAULT_NAMESPACE){
        
        $rt = NULL;
        
        if(!array_key_exists($namespace, $this->configs)){
            $this->configs[$namespace] = array();
        }
        
        if(array_key_exists($key, $this->configs[$namespace])){
            $rt = $this->configs[$namespace][$key];
        }
        
        $this->configs[$namespace][$key] = $value;
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
        if(!$merge || !isset($this->configs[$namespace])){
            $this->configs[$namespace] = array();
        }
        
        $this->configs[$namespace] = array_merge(
            $this->configs[$namespace],
            $data
        );
        
        return $this->configs[$namespace];
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
        
        if(!isset($this->configs[$namespace])){
            throw new InvalidArgumentException("Namespace '$namespace' doesn't exist in config");
        }
        
        $this->doWrite($destination, $this->configs[$namespace]);
    }
    
    protected abstract function doWrite($destination, array $data);
    
    /**
     * Template shutdown action. Enabled via OPTION_USE_SHUTDOWN.
     * 
     * @see http://php.net/manual/en/function.register-shutdown-function.php
     */
    function shutdown(){}
    
    /*
     * SPL Array Overloading
     * -------------------------------------------------------------------------
     */

    function offsetExists($offset){
        return array_key_exists($offset, $this->configs);
    }

    function offsetGet($offset){
        return $this->configs[$offset];
    }

    function offsetSet($offset, $value){
        $this->configs[$offset] = $value;
    }

    function offsetUnset($offset){
        if(array_key_exists($offset, $this->configs)){
            unset($this->configs[$offset]);
        }
    }
    
    function getIterator(){
        return new ArrayIterator($this->configs);
    }
}

Aflexi_Common_Config_AbstractConfig::initializeStatic();

?>
