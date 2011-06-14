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
 * Class utilities.
 *
 * @since 2.4
 * @version 2.4.20100512
 * @author yclian
 */
final class Aflexi_Common_Lang_Classes{

    /**
     * Instatiate object of given type, and set constructor properties (by
     * name).
     *
     * @param $type
     * @param $properties
     * @param bool $strict Failed if extra properties are found. Currently not 
     * supported.
     */
    static function newInstance($type, array $properties = array(), $strict = TRUE){

        $rt;
        $cls;
        $constructor;
        $params;

        $cls = new ReflectionClass($type);
        $constructor = $cls->getConstructor();
        $args = array();

        if(is_null($constructor)){
            return new $type;
        } else if($constructor instanceof ReflectionMethod){

            if($constructor->getNumberOfParameters() > 0){

                foreach($constructor->getParameters() as $i => $param){

                    $name;
                    $value;
                    $hasValue;

                    if($param instanceof ReflectionParameter){
                        
                        $name = $param->getName();
                        $hasValue = isset($properties[$name]);
                        $value;

                        if($hasValue){
                            $value = $properties[$name];
                            self::validateMismatchedArray($param, $value);
                            self::validateMismatchedClass($param, $value);
                        } else{
                            self::validateOptional($param, FALSE);
                            $value = $param->getDefaultValue();
                        }
                        $args[$i] = $value;
                    } else{
                        // TODO [yclian 20100512] Add support for index-based args. We will check the number of params, then
                        // we will check if 0 to n indices are registered.
                        throw new UnexpectedValueException('Expected $param to be an instance of ReflectionParameter');
                    }
                }
                $rt = $cls->newInstanceArgs($args);
            } else{
                $rt = $cls->newInstanceArgs();
            }
            return $rt;
        } else{
            throw new UnexpectedValueException('Expected $constructor to be empty or an instance of ReflectionMethod');
        }
    }
    
    /**
     * Check if a class ($from) can be assigned to another. Technically, if it 
     * implements the interface or extends the class it is being assigned to.
     * 
     * @param mixed $from Class name, object or ReflectionClass.
     * @param mixed $to Class name or ReflectionClass.
     */
    static function isAssignable($from, $to){
        
        $fromCls;
        $toCls;
        
        if($from instanceof ReflectionClass){
            $fromCls = $from;
        } else if(is_object($from)){
            $fromCls = new ReflectionClass(gettype($from));
        } else if(is_string($from) && (class_exists($to) || interface_exists($to))){
            $fromCls = new ReflectionClass($from);
        }
        
        if($to instanceof ReflectionClass){
            $toCls = $to;
        } else if(is_string($to) && (class_exists($to) || interface_exists($to))){
            $toCls = new ReflectionClass($to);
        }
        
        if(empty($fromCls) || empty($toCls)){
            throw new InvalidArgumentException('Given $from and $to have to be string or a ReflectionClass of a defined class or interface');
        }
        
        // If class names are identical
        if($toCls->getName() == $fromCls->getName()){
            return TRUE;
        }
        // If the target class is an interface, either $from extends or implements
        // it.
        else if($toCls->isInterface()){
            if($fromCls->isInterface()){
                return $fromCls->isSubclassOf($toCls->getName());
            } else{
                return $fromCls->implementsInterface($toCls->getName());
            }
        }
        // If the target class is a class, we can only extend it. 
        else{
            if(!$fromCls->isInterface()){
                return $fromCls->isSubclassOf($toCls->getName());
            }
        }
        
        return FALSE;
    }
    
    /**
     * Check if a given type is a valid class or interface, optionally a valid
     * primitive type. 
     * 
     * @param string $type
     * @param bool $includePrimitive[optional] TRUE if primitive type shall be 
     * considered, FALSE by default.
     */
    static function isValidType($type, $includePrimitive = FALSE){
        
        // $foo is used in @settype, to tell if it's a valid type.
        $foo;
        
        if(@settype($foo, $type)){
            return TRUE;
        }
        
        return class_exists($type, TRUE)  || interface_exists($type,  TRUE);
    }
    
    /*
     * Helpers -----------------------------------------------------------------
     */
    
    private static function validateMismatchedArray(ReflectionParameter $param, $value){
        if($param->isArray() && !is_array($value)){
            throw new InvalidArgumentException(sprintf("Constructor argument '{$param->getName()}' type mismatched, required 'array' but %s' provided", gettype($value)));
        }
    }

    private static function validateMismatchedClass(ReflectionParameter $param, $value){        
        $cls = $param->getClass();
        if(!empty($cls) && !is_a($value, $cls->getName())){
            throw new InvalidArgumentException(sprintf("Constructor argument '{$param->getName()}' type mismatched, required '%s' but %s' provided", $param->getClass()->getName(), gettype($value)));
        }
    }

    private static function validateOptional(ReflectionParameter $param, $hasValue){
        if(!$param->isOptional() && !$hasValue){
            throw new InvalidArgumentException("Constructor argument '{$param->getName()}' is not optional and has no value specified");
        }
    }
}

?>