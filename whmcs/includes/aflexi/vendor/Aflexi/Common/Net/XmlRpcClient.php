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
 
# namespace Aflexi\Common\Net;

require_once('IXR/IXR.php');

/**
 * Lite XML-RPC client, using the Inutio XML-RPC library bundled with PHP. 
 * 
 * @author yclian
 * @since 2.4
 * @version 2.4.20100531
 */
class Aflexi_Common_Net_XmlRpcClient{
    
    const DEFAULT_SERVER_URI = 'http://api.aflexi.net/core/xmlrpc';
    
    private $delegate;
    
    function __construct($serverUri = NULL){
        
        if(is_null($serverUri)){
            $serverUri = self::DEFAULT_SERVER_URI;
        }
        
        $this->delegate = new IXR_Client($serverUri);
    }
    
    function execute($method, array $args){
        
        $rt;
        
        $rt = $this->doExecute($method, $args);
        
        return $rt;
    }
    
    /**
     * Invoke the remote XML-RPC method.
     * 
     * @param string $method
     * @param array $args
     * @throws Aflexi_Common_Util_XmlRpcException
     */
    protected function doExecute($method, array $args){
        
        $rt;
        
        if(!$this->doReflectionQuery($method, $args)){
            throw new Aflexi_Common_Net_XmlRpcException($this->delegate->getErrorMessage(), $this->delegate->getErrorCode());
        }
        
        return $this->delegate->getResponse();
    }
    
    /**
     * Reflective way of calling the query function. We have to do it this way 
     * as we are restricted by the behaviour of IXR - they are using var args, 
     * namely, they don't read an array (one parameter) of arguments but they 
     * derive from your method call for the parameters provided to it.
     *
     * @param string $method
     * @param array $args
     * @return mixed Return value from the XML-RPC server.
     */
    private function doReflectionQuery($method, array $args){
        
        $fullArgs;
        $class;
        
        $fullArgs = array_merge(array($method), $args);
        $class = new ReflectionClass('IXR_Client');
        
        return $class->getMethod('query')->invokeArgs($this->delegate, $fullArgs);
    }
}
