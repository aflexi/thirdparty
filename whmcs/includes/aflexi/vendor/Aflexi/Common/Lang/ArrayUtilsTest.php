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
 * Test for Aflexi_Common_Lang_ArrayUtils.
 * 
 * @author yclian
 * @since 2.5
 * @version 2.5.20100615
 */
class Aflexi_Common_Lang_ArrayUtilsTest extends Aflexi_Common_Test_AbstractTest{
    
    function testGet(){
        
        $a = array(0 => 'foo');
        $this->assertEquals('foo', Aflexi_Common_Lang_ArrayUtils::get($a, 0));
        
        $a = array('foo' => 'bar');
        $this->assertEquals('bar', Aflexi_Common_Lang_ArrayUtils::get($a, 'foo'));
        
        $this->assertEquals('bar', Aflexi_Common_Lang_ArrayUtils::get($a, 'bar', 'bar'));
    }
    
    function testIsAssociative(){
        
        $a = array(0 => 'foo');
        $this->assertFalse(Aflexi_Common_Lang_ArrayUtils::isAssociative($a));
        
        $a = array('foo' => 'bar');
        $this->assertTrue(Aflexi_Common_Lang_ArrayUtils::isAssociative($a));
    }
    
    function testToObject(){
        
        $a = array(
            'id' => 1,
            'name' => 'yclian',
            'array' => array(1, 2, 3, 4, 5),
            'assoc' => array(
                ' ' => 'space',
                'array' => array(
                    'foo' => 'bar'
                ),
                // This will override the same name entry.
                'array' => array(11, 22, 33),
            )
        );
        
        $a = Aflexi_Common_Lang_ArrayUtils::toObject($a);
        $this->assertEquals(11, $a->assoc->array[0]);
    }
    
    function testRebuildKeys(){
        
        $a = array(
            array(
                'id' => 3
            ),
            array(
                'id' => 5
            )
        );
        
        $a = Aflexi_Common_Lang_ArrayUtils::rebuildKeys($a, 'id');
        $this->assertTrue(sizeof($a) == 2);
        $this->assertTrue(array_key_exists(3, $a));
        $this->assertTrue(array_key_exists(5, $a));
    }
}

?>