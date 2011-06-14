<?php

# namespace Aflexi\CdnEnabler;

/**
 * Aflexi_Common_Config customized with support of namespace sources.
 * 
 * TODO [yclian 20101015] Some day, we shall pull sources up, together with 
 * lazy-load.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.9.20101019
 */
abstract class Aflexi_CdnEnabler_Config extends Aflexi_Common_Config_MultiConfig{
    
    const NAMESPACE_RUNTIME = 'runtime';
    const NAMESPACE_GLOBAL = 'global';
    const NAMESPACE_OPERATOR = 'operator';
    const NAMESPACE_PUBLISHER = 'publisher';
    
    /**
     * @var array Namespace to destination.
     */
    protected $sources = array();
    
    /**
     * @var array Array of namespace.
     * @since 2.9.20101018
     */
    protected $loaded = array();
    
    function __construct($options = array()){
        parent::__construct(array_merge(
            array(
                self::OPTION_CHILD_CONFIGS => $this->getChildConfigMappings()
            ),
            $options
        ));
    }

    /**
     * Overridable child config mappings.
     */
    protected function getChildConfigMappings(){
        
        $sessionConfig = new Aflexi_Common_Config_SessionConfig();
        $yamlConfig = new Aflexi_Common_Config_YamlConfig();
        
        return array(
            self::NAMESPACE_RUNTIME => $sessionConfig,
            self::NAMESPACE_GLOBAL => $yamlConfig,
            self::NAMESPACE_OPERATOR => $yamlConfig,
            self::NAMESPACE_PUBLISHER => $yamlConfig,
        );
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    function __call($method, $args){
        
        if(strpos($method, 'getSource') === 0){
            return $this->getSource(lcfirst(substr($method, 9)));
        }
        
        return parent::__call($method, $args);
    }
    
    /**
     * Get the source (where the configuration is stored) for given namespace.
     * 
     * @param string $namespace
     * @return mixed
     */
    function getSource($namespace = self::DEFAULT_NAMESPACE){
        if(array_key_exists($namespace, $this->sources)){
            return $this->sources[$namespace];
        } else{
            throw new InvalidArgumentException("Source is undefined for namespace '$namespace'");
        }
    }
    
    /**
     * @see Aflexi_Common_Config_AbstractConfig::get()
     * @since 2.9.20101015
     */
    function get($key, $default = NULL, $namespace = self::DEFAULT_NAMESPACE){
        $this->lazyLoad($namespace);
        return parent::get($key, $default, $namespace);
    }

    /**
     * @see Aflexi_Common_Config_AbstractConfig::set()
     * @since 2.9.20101015
     */
    function set($key, $value, $namespace = self::DEFAULT_NAMESPACE){
        $this->lazyLoad($namespace);
        return parent::set($key, $value, $namespace);
    }
    
    /**
     * Overriden so that it marks the loaded (for first-read) flag.
     * 
     * @see Aflexi_Common_Config_MultiConfig::read()
     */
    function read($source, $namespace = self::DEFAULT_NAMESPACE, $merge = TRUE){
        $rt = parent::read($source, $namespace, $merge);
        if(!in_array($namespace, $this->loaded)){
            $this->loaded []= $namespace;
        }
        return $rt;
    }
    
    /**
     * If config for this namespace is not loaded and source is defined, we 
     * load it.  (Use isset(), to handle NULL case. empty() is inappropriate as 
     * it will cause a loaded but empty config to be reloaded.
     * 
     * @since 2.9.20101015
     * @version 2.9.20101018
     */
    private function lazyLoad($namespace){
        // When it's not loaded..
        if(!in_array($namespace, $this->loaded) && isset($this->sources[$namespace])){
            $this->read($this->sources[$namespace], $namespace);
        }
    }
    
    /*
     * SPL Array Overloading
     * -------------------------------------------------------------------------
     */

    /**
     * @see Aflexi_Common_Config_MultiConfig::offsetGet()
     */
    function offsetGet($offset){
        $this->lazyLoad($offset);
        return parent::offsetGet($offset);
    }

    /**
     * @see Aflexi_Common_Config_MultiConfig::offsetSet()
     */
    function offsetSet($offset, $value){
        $this->lazyLoad($offset);
        parent::offsetSet($offset, $value);
    }
}

?>