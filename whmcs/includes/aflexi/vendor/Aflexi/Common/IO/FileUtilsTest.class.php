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
 
# namespace Aflexi\Common\IO;

/**
 * Test for Aflexi_Common_IO_FileUtils.
 *
 * Unlike the other test cases that we will use Requires, this one is used by
 * Requires class, therefore we are not including into the suite.
 *
 * @author yclian
 * @since 2.3
 * @version 2.3.20100411
 */
class Aflexi_Common_IO_FileUtilsTest extends Aflexi_Common_Test_AbstractTest{

    function testScanFilesByFilter(){
        $this->assertEquals(1, sizeof(Aflexi_Common_IO_FileUtils::scanFiles(dirname(__FILE__), '2\.txt$')));
        $this->assertEquals(2, sizeof(Aflexi_Common_IO_FileUtils::scanFiles(dirname(__FILE__), '.*FileUtilsTest-sample.*')));
        $this->assertEquals(3, sizeof(Aflexi_Common_IO_FileUtils::scanFiles(dirname(__FILE__), '.*FileUtilsTest.*')));
    }
}

?>
