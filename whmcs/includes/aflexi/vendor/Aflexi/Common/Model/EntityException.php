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
 
# namespace Aflexi\Common\Model;

/**
 * Exception thrown after data access operation (not before, validation error 
 * shall be an instance of InvalidArgumentException, etc.) due to invalid attri-
 * butes in entity or business logic violation with it (or more entities).
 * 
 * @author yclian
 * @since 2.5
 * @version 2.5.20100628
 */
class Aflexi_Common_Model_EntityException extends Aflexi_Common_Exception {
    
    function __construct($message = '', $code = self::GENERAL_UNKNOWN_ERROR){
        parent::__construct($message, $code);
    }
}

?>