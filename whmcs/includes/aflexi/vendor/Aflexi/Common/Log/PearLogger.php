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

require_once 'Log.php';

/**
 * A port to PEAR/Log.
 * 
 * Introduced (since 2.7.20100901) so that we can write less code to support 
 * different logging choices. It basically also marks the end of writing our 
 * own implementation and practically we shall be improving this class to be 
 * more configurable. 
 * 
 * @author yclian
 * @since 2.7
 * @version 2.7.20100901
 */
class Aflexi_Common_Log_PearLogger extends Aflexi_Common_Log_AbstractLogger{
    
    /**
     * Name. Used for file name, table name, etc. This is not the same with 
     * ident.
     */
    const OPTION_NAME = 'name';
    
    private static $storage = __CLASS__;
    private static $handler = 'console';
    private static $level = PEAR_LOG_INFO;
    
    /**
     * @var Log
     */
    private $delegate = NULL;
    
    function __construct($options = array()){
        if(is_null($this->delegate)){
            $this->delegate = Log::singleton(
                self::$handler,
                self::$storage,
                __CLASS__,
                array(),
                self::$level
            );
        }
    }
    
    static function initializeStatic(){
    }
    
    /**
     * Set the default handler for all instances of this class. Refer to PEAR's
     * Log documentation for details.
     * 
     * @param $handler
     */
    static function setHandler($handler){
        self::$handler = $handler;
    }
    
    /**
     * Set the default level for all instances of this class.
     */
    static function setLevel($level){
        self::$level = $level;
    }
    
    /**
     * Set the storage of the log. Depend on the handler, this is usually
     * optional.
     * 
     * @param string $storage
     */
    static function setStorage($storage){
        self::$storage = $storage;
    }

    function getName() {
        return __CLASS__;
    }
    
    function isDebugEnabled(){
        return (bool) $this->delegate->_isMasked(PEAR_LOG_DEBUG);
    }

    function debug($message) {
       $this->delegate->log($message, PEAR_LOG_DEBUG);
    }
    
    function isErrorEnabled(){
        return (bool) $this->delegate->_isMasked(PEAR_LOG_ERR);
    }

    function error($message) {
       $this->delegate->log($message, PEAR_LOG_ERR);
    }
    
    function isInfoEnabled(){
        return (bool) $this->delegate->_isMasked(PEAR_LOG_INFO);
    }

    function info($message) {
       $this->delegate->log($message, PEAR_LOG_INFO);
    }
    
    function isWarnEnabled(){
        return (bool) $this->delegate->_isMasked(PEAR_LOG_WARNING);
    }

    function warn($message) {
       $this->delegate->log($message, PEAR_LOG_WARNING);
    }
}

Aflexi_Common_Log_PearLogger::initializeStatic();

?>