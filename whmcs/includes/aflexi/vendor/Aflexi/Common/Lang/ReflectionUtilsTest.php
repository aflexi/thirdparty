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
 * Test for Aflexi_Common_Lang_ReflectionUtils.
 * 
 * @author yclian
 * @since 2.4
 * @version 2.4.20100512
 */
class Aflexi_Common_Lang_ReflectionUtilsTest extends Aflexi_Common_Test_AbstractTest{
    
    function testGetPropertyType(){
        $c = new ReflectionClass('Aflexi_Common_Lang_ReflectionUtilsTestSampleClassA');
        $this->assertEquals('int', Aflexi_Common_Lang_ReflectionUtils::getPropertyType($c->getProperty('primitive')));
        $this->assertEquals('Aflexi_Common_Lang_ReflectionUtilsTestSampleClassA', Aflexi_Common_Lang_ReflectionUtils::getPropertyType($c->getProperty('class')));
        $this->assertNull(Aflexi_Common_Lang_ReflectionUtils::getPropertyType($c->getProperty('pseudo')));
        $this->assertNull(Aflexi_Common_Lang_ReflectionUtils::getPropertyType($c->getProperty('invalid')));
    }
        
    function testGetMethodReturnType(){
        
        $c = new ReflectionClass('Aflexi_Common_Lang_ReflectionUtilsTestSampleClassA');
        
        $this->assertEquals('bool', Aflexi_Common_Lang_ReflectionUtils::getMethodReturnType($c->getMethod('primitiveRt')));
        $this->assertEquals('Exception', Aflexi_Common_Lang_ReflectionUtils::getMethodReturnType($c->getMethod('classRt')));
        $this->assertNull(Aflexi_Common_Lang_ReflectionUtils::getMethodReturnType($c->getMethod('mixedRt')));
        $this->assertNull(Aflexi_Common_Lang_ReflectionUtils::getMethodReturnType($c->getMethod('unknownRt')));
        $this->assertNull(Aflexi_Common_Lang_ReflectionUtils::getMethodReturnType($c->getMethod('invalidRt')));
    }
    
    function testGetMethodArgumentType(){
        
        $c = new ReflectionClass('Aflexi_Common_Lang_ReflectionUtilsTestSampleClassB');
        
        $this->assertEquals('Aflexi_Common_Lang_ReflectionUtilsTestSampleClassA', Aflexi_Common_Lang_ReflectionUtils:: getMethodArgumentType($c->getMethod('hasType'), 0));
        $this->assertEquals('Aflexi_Common_Lang_ReflectionUtilsTestSampleClassA', Aflexi_Common_Lang_ReflectionUtils:: getMethodArgumentType($c->getMethod('hasType'), 'a'));
        try{
            $this->assertNull(Aflexi_Common_Lang_ReflectionUtils:: getMethodArgumentType($c->getMethod('hasType'), 'b'));
            $this->fail('Expected InvalidArgumentException');
        } catch(InvalidArgumentException $iae){
        }try{
            $this->assertNull(Aflexi_Common_Lang_ReflectionUtils:: getMethodArgumentType($c->getMethod('hasInvalidType'), 'a'));
            $this->fail('Expected ReflectionException due to object/class handling');
        } catch(ReflectionException $re){
        }
        $this->assertEquals('string', Aflexi_Common_Lang_ReflectionUtils:: getMethodArgumentType($c->getMethod('hasDocType'), 'a'));
        $this->assertNull(Aflexi_Common_Lang_ReflectionUtils:: getMethodArgumentType($c->getMethod('hasDocPseudoType'), 'a'));
        $this->assertNull(Aflexi_Common_Lang_ReflectionUtils:: getMethodArgumentType($c->getMethod('hasNothingAtAll'), 'a'));
    }
}

class Aflexi_Common_Lang_ReflectionUtilsTestSampleClassA{
    
    /**
     * @var int
     */
    var $primitive;
    
    /**
     * @var Aflexi_Common_Lang_ReflectionUtilsTestSampleClassA
     */
    var $class;
    
    /**
     * @var mixed;
     */
    var $pseudo;
    
    /**
     * @var invalid_type;
     */
    var $invalid;
    
    /**
     * Some comment here.
     * 
     * @return bool
     */
    function primitiveRt(){        
    }
    
    /**
     * Some comment here.
     * 
     * @return Exception
     */
    function classRt(){        
    }
    
    /**
     * Some comment here.
     * 
     * @return mixed
     */
    function mixedRt(){        
    }
    
    /**
     * Some comment here.
     * 
     * @return unknown_type
     */
    function unknownRt(){        
    }
    
    /**
     * Some comment here.
     * 
     * @return yclian_is_so_naked
     */
    function invalidRt(){        
    }
}

class Aflexi_Common_Lang_ReflectionUtilsTestSampleClassB{
    
    function hasType(Aflexi_Common_Lang_ReflectionUtilsTestSampleClassA $a){        
    }
    
    function hasInvalidType(invalid_type $a){        
    }
    
    /**
     * @param string $a
     */
    function hasDocType($a){        
    }
    
    /**
     * @param number $a
     */
    function hasDocPseudoType($a){
        
    }
    
    function hasNothingAtAll($a){
        
    }
}

?>