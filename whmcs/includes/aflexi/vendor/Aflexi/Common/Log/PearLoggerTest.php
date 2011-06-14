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
 * Test for Aflexi_Common_Log_PearLogger.
 * 
 * @author yclian
 * @since 2.7
 * @version 2.7.20100906
 */
class Aflexi_Common_Log_PearLoggerTest extends Aflexi_Common_Test_AbstractTest{
    
    /**
     * @var Aflexi_Common_Log_PearLogger
     */
    private $_;
    
    function setUp(){
        /**
         * DEBUG off.
         */
        Aflexi_Common_Log_PearLogger::setLevel(PEAR_LOG_INFO);
        $this->_ = new Aflexi_Common_Log_PearLogger(array());
    }
    
    function tearDown(){
        Aflexi_Common_Log_PearLogger::setHandler('console');
    }
    
    function testIsDebugEnabledFalse(){
        $this->assertFalse($this->_->isDebugEnabled());
    }
    
    function testHandlerFile() {
        
        $logfile = tempnam(sys_get_temp_dir(), __CLASS__);
        
        Aflexi_Common_Log_PearLogger::setHandler('file');
        Aflexi_Common_Log_PearLogger::setStorage($logfile);
        $this->_ = new Aflexi_Common_Log_PearLogger();
        $this->_->info('TEST_HANDLER_FILE');
        $this->assertTrue((bool) strpos(file_get_contents($logfile), 'TEST_HANDLER_FILE'));
        unlink($logfile);
    }
}

?>