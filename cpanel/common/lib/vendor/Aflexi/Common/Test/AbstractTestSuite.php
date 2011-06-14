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

require_once 'Aflexi/Common/Test/TestUtils.php';

/**
 * Abstract test suite.
 * 
 * @author yclian
 * @since 2.7
 * @version 2.7.20100824
 */
abstract class Aflexi_Common_Test_AbstractTestSuite extends PHPUnit_Framework_TestSuite{
    
    function __construct($theClass = '', $name = ''){
        parent::__construct($theClass, $name);
        $this->addTestsToSuite();
    }
    
    /**
     * @since 2.9.20101013
     */
    private function addTestsToSuite(){
        
        $testDir = $this->getTestDir();
        
        if(!is_null($testDir)){
            Aflexi_Common_Test_TestUtils::addTestsToSuite(
                $this,
                $testDir,
                array($this, 'isTestFile')
            );
        }
    }
    
    /**
     * Determine if this is a test file recognized by this test suite. Will 
     * be invoked by #addTestsToSuite() during construction.
     * 
     * @since 2.9.20101013
     * @return bool
     */
    function isTestFile(SplFileInfo $file){
        return Aflexi_Common_Test_TestUtils::isTestFile($file);
    }
    
    /**
     * The target test directory that shall be scanned during construction time. 
     * 
     * Override this to return NULL if you prefer no action. You may also 
     * override it to the desired directory based on your requirements or 
     * environments.
     * 
     * @since 2.9.20101013
     * @return string
     */
    protected function getTestDir(){
        $class = new ReflectionClass(get_class($this));
        return dirname($class->getFileName());
    }
}

?>