<?php

# namespace Aflexi;

require_once 'Aflexi/Common/Application/AbstractBootstrapper.php';

/**
 * Bootstrap for CdnEnabler.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.8.20100929
 */
class Aflexi_CdnEnabler extends Aflexi_Common_Application_AbstractBootstrapper{
    
    /**
     * @var Aflexi_CdnEnabler
     */
    private static $instance = NULL;
    
    /**
     * @var bool
     */
    private $prepared;
    
    /**
     * @var bool
     */
    private $booted;
    
    /**
     * @var Aflexi_Common_Config_Config
     */
    private $config;
    
    /**
     * @var Aflexi_Common_Log_Logger
     */
    private static $logger;
    
    /**
     * Time in second, precision to micro.
     * 
     * @var float
     */
    private $timeStart = 0;
    
    /**
     * Convenient method to prepare the application, used if you want to keep 
     * the second bootstrap phase later.
     * 
     * @see #bootstrap()
     * @param $instance
     * @since 2.8
     * @version 2.8.20100930
     */
    static function fastPrepare(Aflexi_CdnEnabler $instance = NULL){
        if(!self::$instance){
            if(!$instance){
                $instance = new Aflexi_CdnEnabler();
            }
            $instance->prepare();
        } else{
            $class = __CLASS__;
            throw new Aflexi_Common_Lang_IllegalStateException("A '$class' instance has already been set");
        }
        self::$instance = $instance;
        return $instance;
    }
    
    /**
     * Convenient method to prepare and boot the application.
     * 
     * @param Aflexi_CdnEnabler $instance
     * @return Aflexi_CdnEnabler The $instance itself.
     */
    static function fastBoot(Aflexi_CdnEnabler $instance = NULL){
        return self::fastPrepare($instance)->boot();
    }
    
    /**
     * @return Aflexi_CdnEnabler
     */
    static function getInstance(){
        return self::$instance;
    }
    
    protected function doPrepare(){
        
        $this->timeStart = microtime(TRUE);
        
        $this->registerShutdown();
        // $this->registerIncludePaths();
        $this->registerCommon();
    }
    
    function boot(){
        parent::boot();
        if(self::$logger && self::$logger->isInfoEnabled()){
            self::$logger->info(__CLASS__.' booted, in \''.dirname(__FILE__).'\'. ('.$this->getMilliTimeDiff().'ms)');
        }
        return $this;
    }
    
    protected function doBoot(){
        $this->initializeConfig();
        $this->initializeLogger();
    }
    
    function shutdown(){
        
        $this->doShutdown();
        
        if(self::$logger && self::$logger->isInfoEnabled()){
            self::$logger->info(__CLASS__.' shutdown ('.$this->getMilliTimeDiff().'ms)');
        }
    }
    
    protected function doShutdown(){}
    
    protected function registerIncludePaths(){
        set_include_path(
            realpath(dirname(__FILE__).'/..').PATH_SEPARATOR.
            realpath(dirname(__FILE__).'/../../vendor').PATH_SEPARATOR.
            get_include_path()
        );
    }
    
    /**
     * Get the reference to application config.
     * 
     * @return Aflexi_Common_Config_Config
     */
    function getConfig(){
        return $this->config;
    }
    
    private function getMilliTimeDiff(){
        return round((microtime(TRUE) - $this->timeStart) * 1000, 4);
    }
    
    protected function registerShutdown(){
        register_shutdown_function(array($this, 'shutdown'));
    }
    
    protected function registerCommon(){
        require_once "Aflexi/Autoloader.php";
        Aflexi_Autoloader::setUseClassPaths(TRUE, realpath(dirname(__FILE__).'/..'));
        require_once "Aflexi/Common.php";
    }
    
    protected function initializeConfig(){
        $this->config = new Aflexi_Common_Config_YamlConfig();
        $this->config->read(dirname(__FILE__).'/CdnEnabler/config.yml', 'app');
    }
    
    protected function initializeLogger(){
        $filename = $this->config['app']['app_home'].DIRECTORY_SEPARATOR. $this->config['app']['log_storage'];
        if(($this instanceof Aflexi_CdnEnabler_Cpanel_Publisher_Bootstrap) || @$_ENV['CPANEL_USER_ROLE'] == "PUBLISHER"){
            $logDir = isset($_ENV['HOME']) ? $_ENV['HOME'] : '/tmp';
            $filename = "{$logDir}/{$this->config['app']['log_storage']}";
        }
        Aflexi_Common_Log_LoggerFactory::setLoggerClass('Aflexi_Common_Log_LoggerPear');
        Aflexi_Common_Log_LoggerPear::setHandler($this->config['app']['log_handler']);
        Aflexi_Common_Log_LoggerPear::setStorage($filename);
        Aflexi_Common_Log_LoggerPear::setLevel(constant('PEAR_LOG_'.
            $this->config['app']['log_level']
        ));
        self::$logger = Aflexi_Common_Log_LoggerFactory::getLogger(__CLASS__);
    }
}

?>
