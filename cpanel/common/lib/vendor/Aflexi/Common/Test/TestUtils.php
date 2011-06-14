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

/**
 * Various test helper functions.
 *
 * @author yclian
 * @since 2.2
 * @version 2.2.20100406
 */
final class Aflexi_Common_Test_TestUtils{

    /**
     * Given a path to directory, recursively collect files and add them to given suite.
     *
     * @param PHPUnit_Framework_TestSuite $suite
     * @param string $dir
     * @param callback $callback[optional] Check against given SplFileInfo 
     *  object. If not provided, #isTestFile() is used.
     */
    static function addTestsToSuite(PHPUnit_Framework_TestSuite &$suite, $dir, $callback = NULL){
        
        $testFiles = self::getTestFiles($dir, $callback);
        
        array_walk(
            $testFiles,
            array(__CLASS__, 'addTestToSuite'),
            $suite
        );
    }
    
    private static function addTestToSuite(SplFileInfo &$testFile, $index, PHPUnit_Framework_TestSuite $suite){
        $suite->addTestFile($testFile->getPathname());
    }
    
    /**
     * Get an array of qualified test files in a directory, checked with 
     * provided $callback.
     * 
     * @param string $dir
     * @param callback $callback[optional] Check against given SplFileInfo 
     *  object. If not provided, #isTestFile() is used.
     * @return array
     */
    static function getTestFiles($dir, $callback = NULL){
        
        $rt = array();
        
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::SELF_FIRST);
        
        foreach($files as $file){
            
            if($file->isFile()){
                
                if(is_null($callback)){
                    $callback = array(__CLASS__, 'isTestFile');
                }
                if(call_user_func($callback, $file)){
                    $rt []= $file;
                }
            }
        }
        
        return $rt;
    }

    /**
     * Check if a file is a test file, by detecting <tt>*Test.*php</tt> or 
     * <tt>*TestCase.*php</tt>.
     * 
     * This test is used as the default callback in #addTestsToSuite() and 
     * #getTestFiles().
     * 
     * @param SplFileInfo $file
     * @return bool
     */
    static function isTestFile(SplFileInfo $file){
        return
            // Classes in .*/Test are base classes, not tests. This is to avoid 
            // loading abstract test cases.
            !preg_match('|.*/Test|', $file->getPathname()) &&
            // PHP file ends with Test.php, TestCase.php, Test.class.php, 
            // TestCase.class.php.
            preg_match('/^.+((Test\.php)|(TestCase\.php)|(Test\.class\.php)|(TestCase\.class\.php))$/', $file->getFilename());
    }
}

?>