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

# namespace Aflexi;
 
/**
 * Autoloads Aflexi classes, based on the Zend's class naming standard.
 * 
 * Usage Notes:
 * 
 *  1. This autoloader is distributed with aflexi-common. If you are using 
 *     classes of aflexi-common, simply include Aflexi/Common.php, as this
 *     autoloader will be registered automatically.
 *  2. Otherwise, if you are extending and using classes under the Aflexi_* 
 *     namespace, turn on 'useClassPaths' before bootstrap, e.g.
 *     
 *          require_once "Aflexi/Autoloader.php";
 *          Aflexi_Autoloader::setUseClassPaths(TRUE, dirname(__FILE__).'/..');
 *          require_once "Aflexi/Common.php";
 * 
 * @author yclian
 * @since 2.7
 * @version 2.8.20100910
 */
class Aflexi_Autoloader{
    
    /**
     * Flag to keep track if we have already registered. We do not want to reg-
     * ister twice.
     * 
     * @var bool
     */
    private static $registered = FALSE;
    
    /**
     * @var array
     */
    private static $classPaths = NULL;
    
    /**
     * Register Aflexi_Autoloader as an SPL autoloader.
     */
    static function register(){
        
        if (self::$registered){
          return;
        }
        
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(__CLASS__, 'autoload'), TRUE);
        self::$registered = TRUE;
    }
    
    static function unregister(){
        spl_autoload_unregister(array(__CLASS__, 'autoload'));
        self::$registered = FALSE;
    }
    
    /**
     * Autoload a given class.
     * 
     * @param string $class Class name. Autoloaded if it starts with 'Aflexi_'.
     * @return bool TRUE if it is autoloaded.
     */
    static function autoload($class){
        
        $found = FALSE;
        
        // 'strpos' is faster than 'preg_match'.
        if (0 !== strpos($class, 'Aflexi_')) {
            return FALSE;
        }

        if (file_exists(
            $file = dirname(__FILE__).'/../'.str_replace('_', '/', $class).'.php'
        )){
                $found = TRUE;
        } else{
            if(self::isUseClassPaths()){
                foreach(self::$classPaths as $classPath){
                    if(file_exists(
                        $file = "$classPath/".str_replace('_', '/', $class).'.php'
                    )){
                        $found = TRUE;
                        break;
                    }
                }
            }
        }
        
        if($found){
            require_once $file;
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * Check if this autoloader is using the extended class-path searching.
     * 
     * @return bool
     */
    static function isUseClassPaths(){
        return !empty(self::$classPaths);
    }
    
    /**
     * Extend class loading to specified search path.
     * 
     * @param bool $useClassPaths
     * @param mixed $classPaths[optional] A string or an array. If not 
     *  provided, include paths will be used instead.
     */
    static function setUseClassPaths($useClassPaths, $classPaths = NULL){
        
        if($useClassPaths){
            self::setClassPaths($classPaths);
        } else{
            self::$classPaths = NULL;
        }
    }
    
    /**
     * Append to existing class paths. This affects the autoload before and af-
     * ter it is registered.
     * 
     * @since 2.8.20100910
     * @param mixed $classPaths String or an array of string.
     * @param bool $append[optional] Default is to always append.
     * @return void
     */
    static function setClassPaths($classPaths, $append = TRUE){
        
        if(is_null($classPaths)){
            $classPaths = explode(PATH_SEPARATOR, get_include_path());
        } else{
            if(is_string($classPaths)){
                $classPaths = array($classPaths);
            } else if(is_array($classPaths)){
                $classPaths = $classPaths;
            } else{
                throw new InvalidArgumentException("'classPaths' must be a string or an array");
            }
        }
        
        if($append){
            if(!is_array(self::$classPaths)){
                self::$classPaths = array();
            }
            // PHP's array_push can't push elements of an array without 
            // traversing them. Use array_splice instead:
            // array_splice($array, count($array), 0, $otherArray);
            array_splice(self::$classPaths, count(self::$classPaths), 0, $classPaths);
        } else{
            self::$classPaths = $classPath;
        }
    }
}

?>