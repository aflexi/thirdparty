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
 * Test for Aflexi_Common_Config_AbstractConfig.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.8.20100907
 */
class Aflexi_Common_Config_AbstractConfigTest extends Aflexi_Common_Test_AbstractTest{
    
    /**
     * @var Aflexi_Common_Config_AbstractConfig
     */
    private $_;
    
    function setUp(){
        $this->_ = new Aflexi_Common_Config_AbstractConfig_Stub();
    }
    
    function testSet(){
        
    }
    
    function testRead(){
        $rt = $this->_->read('foo', 'bar');
        $this->assertFalse(empty($rt));
        $this->assertEquals('pop', $this->_->get('loli', NULL, 'bar'));
        $this->assertNull($this->_->get('loli', NULL, 'foo'));
    }
    
    function testWrite(){
        $this->_->set('key', 'value', 'namespace');
        $this->_->write('destination1', 'namespace');
        $this->assertEquals('destination1', $this->_->destination);
        $this->assertEquals('value', $this->_->data['key']);
    }
    
    function testWriteInvalidNamespace(){
        
        $this->_->set('key', 'value', 'namespace');
        
        try{
            $this->_->write('destination2', 'foo');
            $this->fail('Expected InvalidArgumentException');
        } catch(InvalidArgumentException $iae){
        }
    }
    
    function testSplArrayOverloading(){
        
        $this->_->set('key', 'value', 'namespace');
        
        // Indirect modification of overloaded element. Not supported as value
        // is returned, not reference.
        // $this->_['namespace']['key2'] = 'value2';
        $arr = $this->_['namespace'];
        $arr['key2'] = 'value2';
        $this->_['namespace'] = $arr;
        
        // ArrayAccess
        $this->assertTrue(isset($this->_['namespace']));
        $this->assertTrue(isset($this->_['namespace']['key2']));
        $this->assertEquals('value', $this->_['namespace']['key']);
        
        // Iterator
        $i = 0;
        foreach($this->_ as $k => $v){
            $i++;
        }
        $this->assertTrue($i > 0);
        
        // ArrayAccess, after unset.
        unset($this->_['namespace']);
        $this->assertFalse(isset($this->_['namespace']));
    }
}

class Aflexi_Common_Config_AbstractConfig_Stub extends Aflexi_Common_Config_AbstractConfig{
    
    var $destination;
    var $data;
    
    protected function doRead($source){
        return array(
            'foo' => 'bar',
            'loli' => 'pop'
        );
    }
    
    protected function doWrite($destination, array $data){
        // State holding, tests can use this to check if arg is correctly passed.
        $this->destination = $destination;
        $this->data = $data;
    }
}

?>