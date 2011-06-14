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
 
require_once 'XML/RPC2/Client.php';
require_once dirname(__FILE__).'/Client.php';

/**
 * Adapter wrapping the PEAR XML_RPC2 client.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.8.20101001
 */
class Aflexi_Common_Net_XmlRpc_ClientPear extends Aflexi_Common_Net_XmlRpc_AbstractClient{
    
    /**
     * @var XML_RPC2_Client
     */
    protected $client;
    
    protected function doInitialize(){
        $this->client = XML_RPC2_Client::create($this->serverUri, array(
            'prefix' => empty($this->namespace) ? '' : "{$this->namespace}."
        ));
    }
    
    protected function doExecute($method, array $args){
        
        try{
            
            
            // If namespace is already set, then, we have to strip it off so 
            // that call_user_func_array() won't make 'namespace.namespace.method'
            // call.
            if(!empty($this->namespace)){
                $method = substr($method, strpos($method, '.') + 1);
            }
            
            return call_user_func_array(
                array(
                    $this->client,
                    $method
                ),
                $args
            );
        } catch(XML_RPC2_FaultException $xrf){
            throw new Aflexi_Common_Net_XmlRpc_Exception($xrf->getFaultString(), $xrf->getFaultCode());
        }
    }
}
