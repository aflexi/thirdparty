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
 
# namespace Aflexi\CdnEnabler\Test;

require_once dirname(__FILE__).'/../CdnEnabler.php';
require_once 'Aflexi/Common/Test/AbstractTestSuite.php';

/**
 * Suite for unit tests.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.8.20100907
 */
class Aflexi_CdnEnabler_UnitTestSuite extends Aflexi_Common_Test_AbstractTestSuite{

    function __construct(){
        Aflexi_Common_Test_TestUtils::addTestsToSuite($this, dirname(__FILE__));
    }

    /**
     * Entry method required by PHPUnit.
     * 
     * @return Aflexi_Common_UnitTestSuite
     */
    public static function suite(){
        return new Aflexi_CdnEnabler_UnitTestSuite();
    }
}

?>