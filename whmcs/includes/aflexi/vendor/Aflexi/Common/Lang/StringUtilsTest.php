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
 * Test for Aflexi_Common_Lang_StringUtils.
 * 
 * @author ija
 * @since 2.7
 * @version 2.7.20100805
 */
class Aflexi_Common_Lang_StringUtilsTest extends Aflexi_Common_Test_AbstractTest{
    
    function testToLowerCaseFirstCharacter() {
        $this->assertEquals('sUPERMAN', Aflexi_Common_Lang_StringUtils::toLowerCaseFirstCharacter('SUPERMAN'));
        $this->assertEquals('superman', Aflexi_Common_Lang_StringUtils::toLowerCaseFirstCharacter('superman'));
        $this->assertEquals('s Uperman', Aflexi_Common_Lang_StringUtils::toLowerCaseFirstCharacter('S Uperman'));
        $this->assertEquals(' SUPERMAN', Aflexi_Common_Lang_StringUtils::toLowerCaseFirstCharacter(' SUPERMAN'));
    }
}

?>