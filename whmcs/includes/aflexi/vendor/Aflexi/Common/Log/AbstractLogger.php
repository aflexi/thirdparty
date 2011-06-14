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

/**
 * Abstract implementation of Aflexi_Common_Log_Logger.
 * 
 * @author yclian
 * @since 2.3
 * @version 2.3.20100415
 */
abstract class Aflexi_Common_Log_AbstractLogger implements Aflexi_Common_Log_Logger{
    
    /**
     * Name of this logger.
     * @var string
     */
    protected $name = '';
    
    /**
     * Params: name, message.
     * 
     * @var string
     */
    protected static $messageFormat = '{%s} %s';
    
    /**
     * Standard method to be used by loggers to format a standard message that 
     * looks like '%whatever_from_symfony% {sampleComponent} Sample Message'.  
     *
     * @return string
     */
    protected function formatMessage($message){  
        $name = $this->getName();
        if(empty($name)){
            $name = 'unknown';
        }
        return sprintf(self::$messageFormat, $name, $message);
    }
    
    function getName(){
        return $this->name;
    }
    
    function setName($name){
        $this->name = $name;
    }
}

?>
