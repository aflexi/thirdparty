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
 * Test for Aflexi_Common_Config_YamlConfig.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.8.20100908
 */
class Aflexi_Common_Config_YamlConfigTest extends Aflexi_Common_Test_AbstractTest{
    
    /**
     * @var Aflexi_Common_Config_YamlConfig
     */
    private $_;
    
    function setUp(){
        $this->_ = new Aflexi_Common_Config_YamlConfig();
    }
    
    function testWrite(){
        
        $this->_->set('key1', 'value1', 'namespace');
        $this->_->set('key2', 'value2', 'namespace');
        $this->_->set('key3', array('value3'), 'namespace');
        $this->_->set('key4', array('key4' => 'value4'), 'namespace');
        
        $file = tempnam('/tmp', 'Aflexi_Common_Config_YamlConfigTest');
        $this->_->write($file, 'namespace');
        
        // Read from namespace to namespace2, through file.
        $this->_->read($file, 'namespace2');
        $this->assertTrue(isset($this->_['namespace2']));
        $this->assertTrue(isset($this->_['namespace2']['key1']));
        $this->assertTrue(isset($this->_['namespace2']['key2']));
        $this->assertTrue(isset($this->_['namespace2']['key3']));
        $this->assertTrue(isset($this->_['namespace2']['key3'][0]));
        $this->assertTrue(isset($this->_['namespace2']['key4']));
        $this->assertTrue(isset($this->_['namespace2']['key4']['key4']));
        
        unlink($file);
    }
}

?>