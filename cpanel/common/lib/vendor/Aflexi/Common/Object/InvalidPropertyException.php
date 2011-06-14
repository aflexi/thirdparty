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
 * Thrown when referring to an invalid property of an object.
 *
 * @since 2.4
 * @version 2.4.20100512
 * @author yclian
 */
class Aflexi_Common_Object_InvalidPropertyException extends InvalidArgumentException{
    
    protected $object;
    protected $propertyName;

    function __construct($object, $propertyName = NULL, $message = '', $code = Aflexi_Common_Exception::CLIENT_INVALID_ARGUMENT){
        
        if(empty($message)){
            parent::__construct(sprintf("Property%s is invalid for object of type '%s'", empty($propertyName) ? '' : " '$propertyName'", gettype($object)), $code);
        } else{
            parent::__construct($message, $code);
        }
        
        $this->object = $object;
        $this->propertyName = $propertyName;
    }
    
    function getObject(){
        return $this->object;
    }
    
    function getPropertyName(){
        return $this->propertyName;
    }
}

?>