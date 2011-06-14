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
 
# namespace aflexi\portal\api\lang\exception;

/**
 * Thrown if an argument is expected to have value but found NULL.
 * 
 * @author yclian
 * @since 2.3
 * @version 2.3.20100421
 */
class Aflexi_Common_Lang_NullArgumentException extends Exception{
    
    /**
     * @param $argName
     * @param $message[optional] If specified, default message constructed with $argName is replaced by this message.
     * @param $code[optional]
     */
    function __construct($argName, $message = NULL, $code = NULL){        
        if(empty($message)){
            parent::__construct(sprintf("Argument%s must not be null", empty($argName) ? '' : " $argName"), $code);
        } else{
            parent::__construct($message, $code);
        }
    }
}
?>