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
 
# namespace Aflexi\Common;

require_once dirname(__FILE__).'/Test/AbstractTestSuite.php';

/**
 * Suite for unit tests.
 * 
 * @author yclian
 * @since 2.7
 * @version 2.7.20100824
 */
class Aflexi_Common_UnitTestSuite extends Aflexi_Common_Test_AbstractTestSuite{

    function __construct(){
        Aflexi_Common_Test_TestUtils::addTestsToSuite($this, dirname(__FILE__));
    }

    /**
     * Entry method required by PHPUnit.
     * 
     * @return SymfonyActionTestSuite
     */
    public static function suite(){
        return new Aflexi_Common_UnitTestSuite();
    }
}

?>