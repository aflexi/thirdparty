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
 
# namespace Aflexi\Common\Lang;

/**
 * Test for Aflexi_Common_Lang_DebugUtils.
 * 
 * @author yclian
 * @since 2.5
 * @version 2.5.20100625
 */
class Aflexi_Common_Lang_DebugUtilsTest extends Aflexi_Common_Test_AbstractTest{
    
    function testGetCallingFunction(){
        $callingFunction = Aflexi_Common_Lang_DebugUtils::getCallingFunction();
        $this->assertEquals('ReflectionMethod->invoke', $callingFunction);
    }
}

?>