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
 
# namespace Aflexi\Common\Test\Net\XmlRpc;

/**
 * Very-very basic mocking for Aflexi_Common_Net_XmlRpc_AbstractClient. Use the 
 * mock*() functions to mock or reset states.
 * 
 * @author yclian
 * @since 2.8.20100923
 * @version 2.8.20100923
 */
class Aflexi_Common_Test_Net_XmlRpc_ClientMock extends Aflexi_Common_Net_XmlRpc_AbstractClient{
    
    var $responses = array();
    
    protected function doInitialize(){
    }
    
    protected function doExecute($method, array $args){
        if(!empty($this->responses[$method])){
            $rt = array_shift($this->responses[$method]);
            return $rt;
        } else{
            throw new Aflexi_Common_Lang_IllegalStateException("Mock has not yet been defined for '{$method}', use 'setPostExecute()'");
        }
    }
    
    /**
     * Mock the return value after the execution of specified method, by sequence.
     * 
     * @param string $method
     * @param mixed $rt
     */
    function mockPostExecute($method, $rt){
        if(!isset($this->responses[$method])){
            $this->responses[$method] = array();
        }
        $this->responses[$method] []= $rt;
    }
    
    /**
     * Reset states of this object.
     */
    function mockReset(){
        $this->responses = array();
    }
}

?>