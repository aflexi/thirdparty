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
 * Simplest implementation of Logger, using {@code println()}. This Logger does
 * support all log levels and this behaviour can't be configured.
 *
 * @author yclian
 * @since 2.3
 * @version 2.3.20100410
 */
class Aflexi_Common_Log_LoggerSimple extends Aflexi_Common_Log_AbstractLogger {

    public function isDebugEnabled(){
        return TRUE;
    }

    public function debug($message) {
       printf("DEBUG| %s\n", $this->formatMessage($message));
    }
    
    public function isErrorEnabled(){
        return TRUE;
    }

    public function error($message) {
        printf("ERROR| %s\n", $this->formatMessage($message));
    }
    
    public function isInfoEnabled(){
        return TRUE;
    }

    public function info($message) {
        printf("INFO| %s\n", $this->formatMessage($message));
    }
    
    public function isWarnEnabled(){
        return TRUE;
    }

    public function warn($message) {
        printf("WARN| %s\n", $this->formatMessage($message));
    }
}
?>
