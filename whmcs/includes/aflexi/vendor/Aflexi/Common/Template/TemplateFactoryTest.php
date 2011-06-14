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
 
# namespace Aflexi\Common\Template;

/**
 * Test for Aflexi_Common_Template_TemplateFactory.
 * 
 * @author yclian
 * @since 2.5
 * @version 2.5.20100603
 */
class Aflexi_Common_Template_TemplateFactoryTest extends Aflexi_Common_Test_AbstractTest{
    
    var $factory;
    
    function setUp(){
        $this->factory = new Aflexi_Common_Core_StubTemplateFactory();
        Aflexi_Common_Template_TemplateFactory::setInstance($this->factory);
    }
    
    function testGetInstance(){
        $this->assertEquals($this->factory, Aflexi_Common_Template_TemplateFactory::getInstance());
    }
    
    function testRenderTemplate(){
        $this->assertEquals('foobar', Aflexi_Common_Template_TemplateFactory::getInstance()->renderTemplate('foobar', array()));
    }
}

class Aflexi_Common_Core_StubTemplateFactory extends Aflexi_Common_Template_TemplateFactory{
    
    function renderTemplate($template, array $context = array()){
        return 'foobar';
    }
} 

?>