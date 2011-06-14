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

require_once dirname(__FILE__).'/AbstractTestSuite.php';

/**
 * Suite for unit tests.
 * 
 * @author yclian
 * @since 2.8.20100917
 * @version 2.8.20101013
 */
abstract class Aflexi_Common_Test_AbstractUnitTestSuite extends Aflexi_Common_Test_AbstractTestSuite{

    function isTestFile(SplFileInfo $file){
        return parent::isTestFile($file) &&
            !preg_match('/^.+((Functional)|(Integration))Test\.php$/', $file->getFilename())
        ;
    }
}

?>