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
 
# namespace aflexi\portal\impl\core\log;

/**
 * A wrapper over sfLogger conforming to the Logger interface. You shall only 
 * use this given that you are in a Symfony 1 environment.
 *
 * @author yclian
 * @since 2.3
 * @version 2.3.20100410
 */
class Aflexi_Common_Log_LoggerSymfony extends Aflexi_Common_Log_AbstractLogger{

    /**
     * @var sfLogger
     */
    private $delegate = NULL;

    function initialize(){
        // Use the setDelegate() method. Otherwise, we will read from sfContext.
        if(!$this->delegate){
            if(!class_exists('sfContext') || !sfContext::hasInstance()){
                throw new IllegalStateException("A ready sfContext is expected");
            }
            $this->delegate = sfContext::getInstance()->getLogger();
        }
    }

    public function isDebugEnabled(){
        return $this->delegate->getLogLevel() >= sfLogger::DEBUG;
    }

    public function debug($message) {
        $this->delegate->debug($this->formatMessage($message));
    }

    public function isErrorEnabled(){
        return $this->delegate->getLogLevel() >= sfLogger::ERR;
    }

    public function error($message) {
        $this->delegate->err($this->formatMessage($message));
    }

    public function isInfoEnabled(){
        return $this->delegate->getLogLevel() >= sfLogger::INFO;
    }

    public function info($message) {
        $this->delegate->info($this->formatMessage($message));
    }

    public function isWarnEnabled(){
        return $this->delegate->getLogLevel() >= sfLogger::WARN;
    }

    public function warn($message) {
        $this->delegate->warning($this->formatMessage($message));
    }

    public function setDelegate(sfLogger $delegate){
        $this->delegate = $delegate;
    }

    /**
     * Wrap an sfLogger and return a SfLoggerAdapter instance.
     * 
     * @deprecated Use the Aflexi_Common_Log_LoggerFactory instead.
     * @param sfLogger $logger An sfLogger instance. If NULL is specified, it
     * will be read from the context instead.
     * @return Aflexi_Common_Log_LoggerSymfony
     */
    public static function wrap(sfLogger $logger = NULL){
        $class = __CLASS__;
        $rt = new $class('');
        if($logger != NULL){
            $rt->setDelegate($logger);
        }
        // NOTE [yclian 20100415] If NULL, the implementation will read sfLogger
        // out from context. See initialize().
        $rt->initialize();
        return $rt;
    }
}
?>
