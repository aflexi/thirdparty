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
 
# namespace Aflexi\Common\Yaml;

/**
 * Test for Aflexi_Common_Util_YamlUtils.
 * 
 * @author yclian
 * @since 2.5
 * @version 2.5.20100603
 */
class Aflexi_Common_Yaml_YamlUtilsTest extends Aflexi_Common_Test_AbstractTest{
    
    var $file1;
    var $array1;
    
    function setUp(){
        $this->file1 = dirname(__FILE__)."/YamlUtilsTest-data-1.txt";
        $this->array1 = array(
            'XX' => 'xx',
            'YY' => array(
                'yy' => 'YYyy'
            )
        );
    }
    
    function testParse(){
        
        $rt;
        
        $rt = Aflexi_Common_Yaml_YamlUtils::parse(file_get_contents($this->file1));
        $this->assertTrue(array_key_exists('all', $rt));
        $this->assertTrue(array_key_exists('AA', $rt['all']));
        $this->assertTrue(array_key_exists('aa', $rt['all']['AA']));
    }
    
    function testSerialize(){
        
        $rt;
        
        $rt = Aflexi_Common_Yaml_YamlUtils::serialize($this->array1);
        $this->assertTrue(preg_match('/XX: xx/', $rt) > 0);
        $this->assertTrue(preg_match('/yy: YYyy/', $rt) > 0);
    }
    
    function testRead(){
        
        $rt;
        
        $rt = Aflexi_Common_Yaml_YamlUtils::read($this->file1);
        $this->assertTrue(array_key_exists('all', $rt));
        $this->assertTrue(array_key_exists('AA', $rt['all']));
        $this->assertTrue(array_key_exists('aa', $rt['all']['AA']));
    }
    
    function testWrite(){
        
        $writtenFile = sys_get_temp_dir().'/YamlUtilsTest-testWrite-'.time().'.txt';
        $rt;
        
        $rt = Aflexi_Common_Yaml_YamlUtils::write($writtenFile, $this->array1);
        $this->assertEquals($rt, file_get_contents($writtenFile));
    }
}

?>