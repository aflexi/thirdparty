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
 
# namespace Aflexi\Common\Test;

require_once dirname(__FILE__).'/../Lang/Requires.php';

Aflexi_Common_Lang_Requires::requirePhpUnit();

/**
 * Base class for all test cases. This class doesn't do much as of 2.3, refer
 * to its child base classes for other specific testing purposes.
 * 
 * @author yclian
 * @since 2.3
 * @version 2.3.20100508.
 */
abstract class Aflexi_Common_Test_AbstractTest extends PHPUnit_Framework_TestCase{

    /**
     * Extend your base class from AbstractMockTestCase instead, not this.
     * 
     * @param  string  $className
     * @param  array   $methods All methods shall be mocked if not specified.
     * @param  array   $arguments
     * @param  string  $mockClassName
     * @param  boolean $callOriginalConstructor
     * @param  boolean $callOriginalClone
     * @param  boolean $callAutoload
     * @return object
     * 
     * @see PHPUnit_Framework_TestCase#getMock()
     */
    protected function getMock($className, $methods = array(), array $arguments = array(), $mockClassName = '', $callOriginalConstructor = TRUE, $callOriginalClone = TRUE, $callAutoload = TRUE){
        return parent::getMock($className, $methods, $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload);
    }
}

?>