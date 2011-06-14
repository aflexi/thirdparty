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
 
# namespace Aflexi\Common\Security;

/**
 * Exception thrown due to authorization issue, such as unauthorized access or 
 * invalid request.
 * 
 * @author yclian
 * @since 2.4
 * @version 2.5.20100628
 */
class Aflexi_Common_Security_AuthorizationException extends Aflexi_Common_Exception{

    function __construct($message = '', $code = self::CLIENT_PERMISSION_DENIED){
        if(empty($message)){
            parent::__construct('Could not perform operation, not authenticated or authorized');
        } else{
            parent::__construct($message, $code);
        }
    }
}

?>