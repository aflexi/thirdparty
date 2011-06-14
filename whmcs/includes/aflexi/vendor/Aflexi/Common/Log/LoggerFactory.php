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
 
# namespace Aflexi\Common\Log;

require_once dirname(__FILE__).'/SimpleLogger.php';

/**
 * A factory for code to obtain a Aflexi_Common_Log_Logger object, used 
 * internally.
 *
 * This abstraction is a solution to avoid our code to be tightly coupled with 
 * any specific framework or library.
 *
 * @since 2.3
 * @version 2.3.20100410
 * @author yclian
 */
class Aflexi_Common_Log_LoggerFactory{

    /**
     * List of loggers, mapped by name.
     *
     * @var array
     */
    private static $loggers = array();

    /**
     * Name of default logger class name. Must be an instance of Logger.
     * @var Aflexi_Common_Log_Logger
     */
    private static $loggerClassName;

    /**
     * Return a cached logger instance (instantiate and cache if not found). 
     * You must always call this function with the '&' sign, i.e. 
     * &Aflexi_Common_Log_LoggerFactory::getLogger(), else you will obtain a 
     * copy instead.
     *
     * @param string $name Name of logger, the common practice is to bind it 
     * with a class and thus the client class name, NULL for logger at global 
     * level. Note that, you can pass in anything here, if an object is provi-
     * ded, its class name will be taken. otherwise, the value will be casted 
     * to a string.
     * @param string $className[optional]
     * @param array $options Options used by the exact implementation for con-
     * struction and also defining behaviour of logger. Unsupported currently -
     * as I (YC) think having options in client's code is really ugly and un-
     * manageable. They should be set at either LoggerFactory (class-level) or 
     * the implementation Logger instead. 
     * @return Logger
     */
    public static function getLogger($name = NULL, $className = NULL, $options = array()){

        $nameStr;

        // TODO [yclian 20100415] Explain this, as well as update @param $name.
        if($name != NULL && !is_string($name)){
            if(is_object($name)){
                $nameStr = get_class($name);
            } else{
                $nameStr = (string) $name;
            }
        } else{
            $nameStr = $name;
        }

        // Look from the cache
        if(array_key_exists($nameStr, self::$loggers)){
            return self::$loggers[$nameStr];
        }

        $rt;

        if(empty($className)){
            if(!empty(self::$loggerClassName)){
                $className = self::$loggerClassName;
            } else{    
                // NOTE [ycian 20100504] hasInstance is added as a safe-guard as
                // there's a chance in an application (or test, most likely) that
                // SfLoggerAdapter IS in the path but no context has been
                // initialized yet.
                if(class_exists('Aflexi_Web_Log_SfLoggerAdapter') && sfContext::hasInstance()){
                    $className = 'Aflexi_Web_Log_SfLoggerAdapter';
                } else{
                    // Default implementation
                    $className = 'Aflexi_Common_Log_SimpleLogger';
                }
            }
        }

        $rt = new $className;

        // TODO [yclian 20100410] We shall support Configurable interface,
        // at both property and getter/setter level. Not urgent until we really
        // have such use case.

        if($rt instanceof Aflexi_Common_Log_Logger){
            $rt->setName($nameStr);
        }
        // Initializable. We avoid constructors.
        if($rt instanceof Aflexi_Common_Object_Initializable){
            $rt->initialize();
        }

        self::$loggers[$nameStr] = $rt;
        return $rt;
    }

    /**
     * Set the default Logger class, used if unspecified in {@code #getLogger()}.
     */
    public static function setLoggerClass($className){

        if(is_string($className) && class_exists($className)){

            $cls = new ReflectionClass($className);

            if($cls->implementsInterface('Aflexi_Common_Log_Logger')){
                self::$loggerClassName = $className;
                return;
            }
        }

        throw new InvalidArgumentException(sprintf('Class %s is not assignable to Aflexi_Common_Log_Logger class', $className));
    }
}

?>
