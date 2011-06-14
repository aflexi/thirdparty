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
 * Test for Aflexi_Common_Lang_ClassUtils.
 * 
 * @author yclian
 * @since 2.4
 * @version 2.4.20100512
 */
class Aflexi_Common_Lang_ClassUtilsTest extends Aflexi_Common_Test_AbstractTest{
    
    public function testNewInstanceEmptyConstructor(){        
        $o = Aflexi_Common_Lang_ClassUtils::newInstance('Aflexi_Common_Lang_ClassUtilsTestSampleClass1');
        $this->assertTrue($o instanceof Aflexi_Common_Lang_ClassUtilsTestSampleClass1);
    }

    public function testNewInstanceNoOptionalArgs(){
        
        $o;
        
        try{
            $o = Aflexi_Common_Lang_ClassUtils::newInstance('Aflexi_Common_Lang_ClassUtilsTestSampleClass2', array(
                'a' => 'AAA'
            ));
            $this->fail('Missing argument $b');
        } catch(Exception $e){
            $this->assertTrue($e instanceof InvalidArgumentException);
        }
    
        try{
            $o = Aflexi_Common_Lang_ClassUtils::newInstance('Aflexi_Common_Lang_ClassUtilsTestSampleClass2', array(
                'a' => 'AAA',
                'b' => 'XXX' // Segmentation fault may occur but already handled in Aflexi_Common_Lang_ClassUtils.
            ));
            $this->fail('Mismatched argument $b');
        } catch(Exception $e){
            $this->assertTrue($e instanceof InvalidArgumentException);
        }
            
        
        $o = Aflexi_Common_Lang_ClassUtils::newInstance('Aflexi_Common_Lang_ClassUtilsTestSampleClass2', array(
            'a' => 'AAA',
            'b' => array()
        ));
        $this->assertTrue($o instanceof Aflexi_Common_Lang_ClassUtilsTestSampleClass2);
    }
    
    function testNewInstanceWithOptionalArgs(){
        
        $o;
        
        $o = Aflexi_Common_Lang_ClassUtils::newInstance('Aflexi_Common_Lang_ClassUtilsTestSampleClass3', array(
            'a' => 'AAA',
            'b' => array()
        ));
        $this->assertTrue($o instanceof Aflexi_Common_Lang_ClassUtilsTestSampleClass3);
        
        $o = Aflexi_Common_Lang_ClassUtils::newInstance('Aflexi_Common_Lang_ClassUtilsTestSampleClass3', array(
            'a' => 'AAA',
            'b' => array(),
            'c' => array('foo' => 'bar')
        ));
        $this->assertEquals('bar', $o->c['foo']);
    }
    
    function testIsAssignable(){
        $this->assertTrue(Aflexi_Common_Lang_ClassUtils::isAssignable('Aflexi_Common_Lang_ClassUtilsTestSampleClass3', 'Aflexi_Common_Lang_ClassUtilsTestSampleClass2'));
        $this->assertTrue(Aflexi_Common_Lang_ClassUtils::isAssignable('Aflexi_Common_Lang_ClassUtilsTestSampleClass4', 'Aflexi_Common_Lang_ClassUtilsTestSampleInterface1'));
        $this->assertFalse(Aflexi_Common_Lang_ClassUtils::isAssignable('Aflexi_Common_Lang_ClassUtilsTestSampleClass4', 'Aflexi_Common_Lang_ClassUtilsTestSampleClass1'));
    }
    
    function testIsValidType(){
        $this->assertTrue(Aflexi_Common_Lang_ClassUtils::isValidType('int', TRUE));
        $this->assertFalse(Aflexi_Common_Lang_ClassUtils::isValidType('yclian', TRUE));
        $this->assertFalse(Aflexi_Common_Lang_ClassUtils::isValidType('yclian', FALSE));
        $this->assertTrue(Aflexi_Common_Lang_ClassUtils::isValidType('Aflexi_Common_Lang_ClassUtilsTestSampleClass1', TRUE));
        $this->assertTrue(Aflexi_Common_Lang_ClassUtils::isValidType('Aflexi_Common_Lang_ClassUtilsTestSampleClass1', FALSE));
    }
}

/*
 * Classes and interfaces for constructor testing ------------------------------
 */

/**
 * Empty constructor.
 */
class Aflexi_Common_Lang_ClassUtilsTestSampleClass1{
    
} 

/**
 * No optional arguments.
 */
class Aflexi_Common_Lang_ClassUtilsTestSampleClass2{
    
    var $a;
    var $b;
    
    function __construct($a, array $b){
        $this->a = $a;
        $this->b = $b;
    }
}

/**
 * With optional arguments.
 */
class Aflexi_Common_Lang_ClassUtilsTestSampleClass3 extends Aflexi_Common_Lang_ClassUtilsTestSampleClass2{
    
    var $c;
    
    function __construct($a, array $b, array $c = array()){
        parent::__construct($a, $b);
        $this->c = $c;
    }
}

/*
 * Classes and interfaces for inheritance testing ------------------------------
 */
interface Aflexi_Common_Lang_ClassUtilsTestSampleInterface1{
    
}

interface Aflexi_Common_Lang_ClassUtilsTestSampleInterface2 extends Aflexi_Common_Lang_ClassUtilsTestSampleInterface1{
    
}

class Aflexi_Common_Lang_ClassUtilsTestSampleClass4 implements Aflexi_Common_Lang_ClassUtilsTestSampleInterface2{
    
}

?>