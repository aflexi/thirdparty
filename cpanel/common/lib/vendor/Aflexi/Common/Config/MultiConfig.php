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
 
/**
 * Wrapper of multiple configuration files. Construct with 'childConfigs' 
 * option, with mappings of namespace to instance.
 * 
 * e.g.
 * 
 *       $this->_ = new Aflexi_Common_Config_MultiConfig(array(
 *           Aflexi_Common_Config_MultiConfig::OPTION_CHILD_CONFIGS => array(
 *               'session1' => $sessionConfig,
 *               'session2' => $sessionConfig,
 *               'yaml1' => $yamlConfig,
 *               'yaml2' => $yamlConfig
 *           )
 *       ));
 * 
 * @author yclian
 * @since 2.9.20101018
 * @version 2.9.20101018
 */
class Aflexi_Common_Config_MultiConfig extends Aflexi_Common_Config_AbstractConfig{
    
    /**
     * Used to initialize the supported configs.
     */
    const OPTION_CHILD_CONFIGS = 'childConfigs';
    
    protected function configure(){
        
        if(!array_key_exists(self::OPTION_CHILD_CONFIGS, $this->options)){
            throw new InvalidArgumentException('\''.self::OPTION_CHILD_CONFIGS.'\' is a required option');
        }
        
        foreach($this->options[self::OPTION_CHILD_CONFIGS] as $namespace => &$config){
            
            if($config instanceof Aflexi_Common_Config_Config == FALSE){
                throw new InvalidArgumentException('Child config has to be an instance of \'Aflexi_Common_Config_Config\'');
            }
            
            // So that we don't overwrite an initialized config. We have to set
            // this, otherwise, calling write() without doing this may break 
            // with a "namespace doesn't exist problem". 
            if(!isset($config[$namespace])){
                $config[$namespace] = array();
            }
            $this->configs[$namespace] = $config;
        }
    }
    
    /**
     * Validate if a namespace has already been registered before a function is invoked.
     * 
     * @param string $namespace
     * @return void
     * @throws Aflexi_Common_Lang_UnsupportedOperationException
     */
    private function validateChildConfigExists($namespace){
        if(!array_key_exists($namespace, $this->options[self::OPTION_CHILD_CONFIGS])){
            throw new Aflexi_Common_Lang_UnsupportedOperationException("Function is unsupported unless namespace '$namespace' defined in '".self::OPTION_CHILD_CONFIGS."' option");
        }
    }
    
    function get($key, $default = NULL, $namespace = self::DEFAULT_NAMESPACE){
        $this->validateChildConfigExists($namespace);
        return $this->configs[$namespace]->get($key, $default, $namespace);
    }
    
    function set($key, $value, $namespace = self::DEFAULT_NAMESPACE){
        $this->validateChildConfigExists($namespace);
        $this->configs[$namespace]->set($key, $value, $namespace);
    }
    
    function read($source, $namespace = self::DEFAULT_NAMESPACE, $merge = TRUE){
        $this->validateChildConfigExists($namespace);
        return $this->configs[$namespace]->read($source, $namespace, $merge);
    }
    
    function write($destination, $namespace = self::DEFAULT_NAMESPACE){
        $this->validateChildConfigExists($namespace);
        return $this->configs[$namespace]->write($destination, $namespace);
    }

    /**
	 * @deprecated
     */
    protected function doRead($source){}
    
    /**
	 * @deprecated
     */
    protected function doWrite($destination, array $data){}
    
    /*
     * SPL Array Overloading
     * -------------------------------------------------------------------------
     */

    function offsetExists($offset){
        return array_key_exists($offset, $this->configs) && array_key_exists($offset, $this->configs[$offset]);
    }

    function offsetGet($offset){
        return $this->configs[$offset][$offset];
    }

    function offsetSet($offset, $value){
        $this->configs[$offset][$offset] = $value;
    }

    function offsetUnset($offset){
        if(array_key_exists($offset, $this->configs)){
            
            if(array_key_exists($offset, $this->configs[$offset])){
                unset($this->configs[$offset][$offset]);
            }
            
            unset($this->configs[$offset]);
        }
    }
}
