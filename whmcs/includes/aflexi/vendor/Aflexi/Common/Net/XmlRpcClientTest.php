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

class Aflexi_Common_Net_XmlRpcClientTest extends Aflexi_Common_Test_AbstractTest{
    
    var $client;
    
    function setUp(){
        $this->client = new Aflexi_Common_Net_XmlRpcClient_Stub();
    }
    
    function testExecute(){
        $rt = $this->client->execute('system.listMethods', array());
        $this->assertEquals('system.listMethods', $rt['method']);
        $this->assertEquals(array(), $rt['args']);
    }
}

class Aflexi_Common_Net_XmlRpcClient_Stub extends Aflexi_Common_Net_XmlRpcClient{
    
    public $response;
    
    function doExecute($method, array $args){
        return array('method' => $method, 'args' => $args);
    }
}

?>