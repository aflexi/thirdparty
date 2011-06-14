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
 * Test for Aflexi_Common_Template_TwigFactory.
 * 
 * @author yclian
 * @since 2.5
 * @version 2.5.20100603
 */
class Aflexi_Common_Template_TwigFactoryTest extends Aflexi_Common_Test_AbstractTest{
    
    var $factory;
    
    function setUp(){
        $this->factory = new Aflexi_Common_Template_TwigFactory(dirname(__FILE__));
    }
    
    function testGetEnvironment(){
        $this->assertNotNull($this->factory->getEnvironment());
    }
    
    function testRenderTemplate(){
        $this->assertEquals(
            'This is a text file, that says HELLO WORLD.',
            $this->factory->renderTemplate(
                'TwigFactoryTest-helloworld.txt', 
                array(
                    'hello' => 'HELLO',
                    'world' => 'WORLD'
                )
            )
        );
    }
}

?>