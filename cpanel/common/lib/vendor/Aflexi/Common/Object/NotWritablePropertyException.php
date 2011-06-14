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
 
# namespace Aflexi\Common\Object;

/**
 * Thrown when a property exists but could not be written. 
 *
 * @since 2.4
 * @version 2.4.20100513
 * @author yclian
 */
class Aflexi_Common_Object_NotWritablePropertyException extends InvalidPropertyException{

    function __construct($object, $propertyName = NULL, $message = '', $code = Aflexi_Common_Exception::CLIENT_INVALID_ARGUMENT){
        if(empty($message)){
            parent::__construct($object, $propertyName, sprintf("Property%s exists in object '%s' but not writable", empty($propertyName) ? '' : " '$propertyName'",  gettype($object)), $code);
        } else{
            parent::__construct($object, $propertyName, $message, $code);
        }
    }
}

?>