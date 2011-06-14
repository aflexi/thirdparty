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
 * Utility for reflection related operations.
 *
 * @author yclian
 * @since 2.3
 * @version 2.4.20100512
 */
final class Aflexi_Common_Lang_ReflectionUtils{

    private static $regexPseudoTypes = '^(unknown_type)|(mixed)|(number)|(void)$';
    
    private static $regexDocParam = '^\s+[*]\s+@param\s+[[:alnum:]]+\s+\$%param_name%.*$';
    private static $regexDocReturn = '^\s+[*]\s+@return.*$';
    private static $regexDocField = '^\s+[*]\s+@var.*$';

    /**
     * We are calling it 'property' but not 'field' here so that it is aligned
     * with ReflectionProperty.
     *  
     * @param $field
     * @param $key
     */
    static function getPropertyType(ReflectionProperty $field){
        
        $rt;
        $doc;
        $docMatches;
        
        $rt = NULL;
        $doc = $field->getDocComment();
        $docMatches = self::getColumnFromComment($doc, self::$regexDocField, 2);

        if(!empty($docMatches)){
            
            $foundType;

            $foundType = $docMatches[0];    
            if(self::isValidType($foundType)){
                $rt = $foundType;
            }
        }
        
        return $rt;
    }
    
    /**
     * Get the argument type of a method by evaluating its signature or doc-
     * umentation comment.
     * 
     * @param ReflectionMethod $method
     * @param string $key
     * @return string 
     */
    static function getMethodArgumentType(ReflectionMethod $method, $key){

        $rt;
        $params;
        $targetParam;
        $doc;

        if(!is_int($key) && !is_string($key)){
            throw new InvalidArgumentException('Expected int or string for argument $key');
        }

        $rt = NULL;
        $params = $method->getParameters();
        $targetParam = self::getReflectionParameterByKey($params, $key);
        
        if($targetParam){
            // Extract from definition
            if($targetParam->isArray()){
                $rt = 'array';
            } else if(!is_null($targetParam->getClass())){
                $rt = $targetParam->getClass()->getName();
            } else{
                // If can't determine from method signature, we extract from comment but only if key is a name
                if(is_string($key)){
    
                    $regexDocParam;
                    $docMatches;
    
                    $regexDocParam = strtr(self::$regexDocParam, array('%param_name%' => $key));
    
                    $docMatches = self::getColumnFromComment($method->getDocComment(), $regexDocParam, 2);
    
                    if(!empty($docMatches)){
    
                        $foundType;
    
                        if(sizeof($docMatches) >= 1){
                            if(sizeof($docMatches) > 1){
                                $logger = Aflexi_Common_Log_LoggerFactory::getLogger(__CLASS__);
                                if($logger->isWarnEnabled()){
                                    $logger->warn("Found more than one matching @param annotation in method '{$method->getDeclaringClass()->getName()}.{$method->getName()}', using the first one");
                                }
                            }
    
                            $foundType = $docMatches[0];
    
                            if(self::isValidType($foundType)){
                                $rt = $foundType;
                            }
                        }
                    }
                }
            }
        } else{
            throw new InvalidArgumentException("Given key '{$key}' does not match with any arguments in method '{$method->getName()}'");
        }
        
        return $rt;
    }

    /**
     * Get the return type of a given method by evaluating its documentation 
     * comment.
     * 
     * @param ReflectionMethod $method
     * @return string
     */
    static function getMethodReturnType(ReflectionMethod $method){

        $rt;
        $doc;
        $docMatches;

        $rt = NULL;
        $doc = $method->getDocComment();

        if(!empty($doc)){

            $docMatches = self::getColumnFromComment($doc, self::$regexDocReturn, 2);

            if(!empty($docMatches)){

                $foundType;

                if(sizeof($docMatches) >= 1){

                    if(sizeof($docMatches) > 1){
                        $logger = Aflexi_Common_Log_LoggerFactory::getLogger(__CLASS__);
                        if($logger->isWarnEnabled()){
                            $logger->warn("Found more than one @return annotation in method '{$method->getDeclaringClass()->getName()}.{$method->getName()}', using the first one");
                        }
                    }

                    $foundType = $docMatches[0];

                    // Not mixed, unknown_type, or invalid type.
                    if(self::isValidType($foundType)){
                        $rt = $foundType;
                    }
                }
            }
        }

        return $rt;
    }


    /**
     * Invoke a method via reflection.
     *
     * TODO [yclian 20100512] Legacy, not tested.
     *
     * @param $obj The object or class name (string) if it's a static function.
     * @param $methodName
     * @param $args
     * @param $ignoreAccessibility Ignore the accessibility, this is only supported from PHP 5.3.2 onwards.
     * @return unknown_type
     */
    static function invoke($obj, $methodName, $args = array(), $ignoreAccessibility = false){
        $targetObj = is_string($obj) ? NULL : $obj;
        $method = new ReflectionMethod($obj, $methodName);
        if($ignoreAccessibility){
            $method->setAccessible(true);
        }
        return $method->invokeArgs($targetObj, $args);
    }

    /*
     * Helpers -----------------------------------------------------------------
     */

    /**
     * Check if given type is a not pseudotype and is a valid type.
     * 
     * TODO [yclian 20100513] Examine if it is really needed for the pseudo-
     * type checking, as the latter check may have covered it (and faster too).
     * 
     * @param string $type
     * @see http://php.net/manual/en/language.pseudo-types.php
     */
    private static function isValidType($type){
        return !preg_match('/'.self::$regexPseudoTypes.'/', $type) && Aflexi_Common_Lang_ClassUtils::isValidType($type, TRUE);
    }

    /**
     * Get an array of extracted column value of matching rows.
     *
     * @param string $rowRegex
     * @param int $columnIndex
     */
    private static function getColumnFromComment($comment, $rowRegex, $columnIndex){

        $rt;
        $logger;
        $matches;
        
        if(!preg_match('/'.$rowRegex.'/m', $comment,  $matches)){
            return array();
        }

        $rt = array();

        foreach($matches as $match){
            $tokens = preg_split('/[\s]+/', $match, -1, PREG_SPLIT_NO_EMPTY);
            if(isset($tokens[$columnIndex])){
                $rt []= $tokens[$columnIndex];
            }
        }

        return $rt;
    }

    /**
     * Given an array of ReflectionParameter, get the one with matching posi-
     * tion or name.
     *
     * @param array $params
     * @param mixed $key string or int.
     * @return ReflectionParameter
     */
    private static function getReflectionParameterByKey(array $params, $key){
        
        foreach($params as $param){
            // Fooling the IDE for auto-complete, as PHP doesn't have generic
            // and thus we never know the element type of the array from meta.
            $param = $param instanceof ReflectionParameter ? $param : $param;

            if((is_int($key) && $param->getPosition() == $key) || $param->getName() == $key){
                return $param;
            }
        }

        return NULL;
    }
}

?>