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
     * @param $suite
     * @param $path
     * @param $callback optional callback to check against given SplFileInfo object.
     */
    public static function addTestsToSuite(PHPUnit_Framework_TestSuite &$suite, $path, callback $callback = NULL){

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
        foreach($files as $file){
            if($file->isFile()){
                
                $includeFile = self::isTestFile($file);
                if($includeFile && !empty($callback)){
                    $includeFile = $callback($file);
                }

                // Adding the test file
                if($includeFile){
                    $suite->addTestFile($file->getPathname());
                }
            }
        }
    }

    /**
     * Check if a file is a test file, by detecting <tt>*Test.*php</tt> or 
     * <tt>*TestCase.*php</tt>. Excluding classes in Aflexi/Common/Test. 
     * 
     * @param SplFileInfo $file
     * @return bool
     */
    public static function isTestFile(SplFileInfo $file){
        return
            // Classes in Aflexi/Common/Test are base classes, not tests.
            // This is to avoid loading abstract test cases.
            !preg_match('|Aflexi/Common/Test|', $file->getPathname()) &&
            // PHP file ends with Test.php, TestCase.php, Test.class.php, 
            // TestCase.class.php.
            preg_match('/^.+((Test\.php)|(TestCase\.php)|(Test\.class\.php)|(TestCase\.class\.php))$/', $file->getFilename());
    }
}

?>