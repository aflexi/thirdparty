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
 
# namespace Aflexi\Common\Log;

/**
 * Test for Aflexi_Common_Log_LoggerFactory.
 * 
 * @author yclian
 * @since 2.3
 * @version 2.3.20100412
 */
class Aflexi_Common_Log_LoggerFactoryTest extends PHPUnit_Framework_TestCase{
    
    function setUp(){
        Aflexi_Common_Log_LoggerFactory::setLoggerClass('Aflexi_Common_Log_SimpleLoggerStub');
    }
    
    public function testGetLogger(){
        $logger = Aflexi_Common_Log_LoggerFactory::getLogger();
        $this->assertNotNull($logger);
    }
}

class Aflexi_Common_Log_SimpleLoggerStub extends Aflexi_Common_Log_SimpleLogger{
} 

?>