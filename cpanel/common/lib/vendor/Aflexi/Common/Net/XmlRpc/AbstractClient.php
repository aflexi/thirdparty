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
 
require_once dirname(__FILE__).'/Exception.php';

abstract class Aflexi_Common_Net_XmlRpc_AbstractClient{
    
    /**
     * @var Aflexi_Common_Log_Logger
     */
    private static $logger;
    
    static function initializeStatic(){
        self::$logger = Aflexi_Common_Log_LoggerFactory::getLogger(__CLASS__);
    }
    
    protected $client;
    protected $serverUri;
    protected $namespace = '';
    
    /**
     * @param string $serverUri The target server URI.
     * @param string $namespace[optional] The target namespace. If not provided,
     *  __call() cannot be supported.
     */
    function __construct($serverUri, $namespace = NULL){
        $this->serverUri = $serverUri;
        $this->namespace = $namespace;
        $this->doInitialize();
    }
    
    /**
     * Inspired by PEAR's XML_RPC2 to support "$namespace.$method" call via 
     * $method().
     * 
     * @param $name
     * @param $args
     */
    function __call($name, $args){
        if(!empty($this->namespace)){
            return $this->execute($this->namespace.'.'.$name, $args);
        } else{
            throw new Aflexi_Common_Lang_UnsupportedOperationException("__call is not supported if 'namespace' is not set");
        }
    }
    
    protected abstract function doInitialize();
    
    /**
     * Execute the remote invocation.
     * 
     * @param string $method
     * @param array $args
     * @return
     * @throws Aflexi_Common_Net_XmlRpc_Exception
     */
    function execute($method, array $args){
        
        $rt;
        
        if(self::$logger->isDebugEnabled()){
            self::$logger->debug("Executing {$method} with arguments: ".$this->exportArgs($args));
        }
        
        $rt = $this->doExecute($method, $args);
        
        return $rt;
    }
    
    private function exportArgs(array $args){
        // We assume if args matching this, they are passwords, so we will filter them.
        if(isset($args[0]) && isset($args[1]) && is_string($args[0]) && is_string($args[1])){
            $args[1] = preg_replace('/./', 'x', $args[1]);
        }
        return var_export($args, TRUE);
    }
    
    /**
     * Exact implementation. Shall handle fault and rethrow with 
     * Aflexi_Common_Net_XmlRpc_Exception.
     * 
     * @see #execute()
     * @param string $method
     * @param array $args
     * @throws Aflexi_Common_Net_XmlRpc_Exception
     */
    protected abstract function doExecute($method, array $args);
}

Aflexi_Common_Net_XmlRpc_AbstractClient::initializeStatic();

?>