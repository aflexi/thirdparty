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
 
/**
 * Bootstrap for CdnEnabler.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.8.20100907
 */
class Aflexi_CdnEnabler{
    
    /**
     * @var Aflexi_Common_Config_Config
     */
    private static $config;
    
    /**
     * @var Aflexi_Common_Log_Logger
     */
    private static $logger;
    
    /**
     * Time in second, precision to micro.
     * 
     * @var float
     */
    private static $timeStart = 0;
    
    static function bootstrap(){
        
        self::$timeStart = microtime(TRUE);
        
        self::registerShutdown();
        self::registerIncludePaths();
        self::registerCommon();
        
        self::initializeConfig();
        self::initializeLogger();
        
        self::$logger->info(__CLASS__.' initialized ('.self::getMilliTimeDiff().'ms)');
    }
    
    static function shutdown(){
        self::$logger->info(__CLASS__.' shutdown ('.self::getMilliTimeDiff().'ms)');
    }
    
    /**
     * Get the reference to application config.
     * 
     * @return Aflexi_Common_Config_Config
     */
    static function & getConfig(){
        return self::$config;
    }
    
    private static function getMilliTimeDiff(){
        return round((microtime(TRUE) - self::$timeStart) * 1000, 4);
    }
    
    private static function registerShutdown(){
        register_shutdown_function(array('Aflexi_CdnEnabler', 'shutdown'));
    }
    
    private static function registerIncludePaths(){
        set_include_path(
            realpath(dirname(__FILE__).'/..').PATH_SEPARATOR.
            realpath(dirname(__FILE__).'/../../vendor').PATH_SEPARATOR.
            get_include_path()
        );
    }
    
    private static function registerCommon(){
        require_once "Aflexi/Autoloader.php";
        Aflexi_Autoloader::setUseClassPaths(TRUE, dirname(__FILE__).'/..');
        require_once "Aflexi/Common.php";
    }
    
    private static function initializeConfig(){
        self::$config = new Aflexi_Common_Config_YamlConfig();
        self::$config->read(dirname(__FILE__).'/CdnEnabler/config.yml', 'app');
    }
    
    private static function initializeLogger(){
        Aflexi_Common_Log_LoggerFactory::setLoggerClass('Aflexi_Common_Log_PearLogger');
        Aflexi_Common_Log_PearLogger::setHandler(self::$config['app']['log_handler']);
        Aflexi_Common_Log_PearLogger::setStorage(
            self::$config['app']['app_home'].DIRECTORY_SEPARATOR.
            self::$config['app']['log_storage']
        );
        self::$logger = Aflexi_Common_Log_LoggerFactory::getLogger(__CLASS__);
    }
}

Aflexi_CdnEnabler::bootstrap();

?>