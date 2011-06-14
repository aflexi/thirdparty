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
 
require_once dirname(__FILE__).'/AbstractClient.php';

/**
 * Wrapper to different extensions of Aflexi_Common_Net_XmlRpc_AbstractClient,
 * using the decorator pattern.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.8.20100923
 */
class Aflexi_Common_Net_XmlRpc_Client extends Aflexi_Common_Net_XmlRpc_AbstractClient{
    
    /**
     * @var Aflexi_Common_Log_Logger
     */
    private static $logger;
    /**
     * @var string
     */
    private static $delegateClass = 'Pear';
    
    static function initializeStatic(){
        self::$logger = Aflexi_Common_Log_LoggerFactory::getLogger(__CLASS__);
    }
    
    /**
     * Set the class name of the delegate. Support also short name, e.g. 'Pear'
     * for 'Aflexi_Common_Net_XmlRpc_ClientPear'.
     * 
     * @param string $delegateClass
     */
    static function setDelegateClass($delegateClass){
        self::$delegateClass = $delegateClass;
    }
    
    /**
     * @var Aflexi_Common_Net_XmlRpc_AbstractClient
     */
    private $delegate = NULL;
    
    function __construct($serverUri, $namespace = NULL, $delegate = NULL){
        $this->createDelegate($delegate, $serverUri, $namespace);
    }
    
    /**
     * Set (typically replace) the underlying delegate. Helpful for testing 
     * purpose.
     * 
     * @param Aflexi_Common_Net_XmlRpc_AbstractClient $delegate
     * @return void
     */
    function setDelegate(Aflexi_Common_Net_XmlRpc_AbstractClient $delegate){
        $this->delegate = $delegate;
    }
    
    /**
     * Instatiate the delegate, followed by the standard constructor arguments.
     * 
     * @param mixed $delegate
     * @param mixed $serverUri
     * @param mixed $namespace
     * @throws Aflexi_Common_Net_XmlRpc_Exception
     */
    private function createDelegate($delegate, $serverUri, $namespace){
        
        if(is_null($delegate)){
            $delegate = $this->resolveDelegateClass();
        }
        
        if(is_string($delegate) && $this->validateDelegateClass($delegate)){
            $class = new ReflectionClass($delegate);
            $this->delegate = $class->newInstance($serverUri, $namespace);
        } else{
            if(is_object($delegate) && 
               $delegate instanceof Aflexi_Common_Net_XmlRpc_AbstractClient){
               $this->delegate = $delegate;
            }
        }

        if(empty($this->delegate)){
            throw new Aflexi_Common_Net_XmlRpc_Exception("Delegate has to be an object or class");
        }
    }
    
    /**
     * Given delegate class set via #setDelegateClass, resolve its full class
     * name.
     *
     * @return string The full delegate class name.
     * @throws Aflexi_Common_Net_XmlRpc_Exception
     */
    private function resolveDelegateClass(){
        
        $delegateClass = NULL;
        
        // Try it as suffix, e.g. Pear -> ClientPear.
        $delegateClass = __CLASS__.self::$delegateClass;
        
        // Otherwise, take the entire as full class name.
        if(!class_exists($delegateClass, TRUE)){
            if(class_exists(self::$delegateClass, TRUE)){
                $delegateClass = self::$delegateClass;
            }
        }
        
        if(is_null($delegateClass)){
            throw new Aflexi_Common_Net_XmlRpc_Exception("Could not find or use class '{$delegateClass}' as client");
        } else{
            return $delegateClass;
        }
    }
    
    private function validateDelegateClass($class){
        $parents = class_parents($class, TRUE);
        return in_array('Aflexi_Common_Net_XmlRpc_AbstractClient', $parents);
    }
    
    /*
     * Delegated functions
     * -------------------------------------------------------------------------
     */
    
    function execute($method, array $args){
        return $this->delegate->execute($method, $args);
    }
    
    /*
     * Not used
     * -------------------------------------------------------------------------
     */
    
    protected function doExecute($method, array $args){}
    protected function doInitialize(){}
}

Aflexi_Common_Net_XmlRpc_Client::initializeStatic();
Aflexi_Common_Net_XmlRpc_Client::setDelegateClass('Pear');

?>
