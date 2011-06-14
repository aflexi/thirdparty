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
 * Aflexi_Common_Model_EntityException for unfound Entity.
 * 
 * @author yclian
 * @since 2.5
 * @version 2.5.20100625
 */
class Aflexi_Common_Model_EntityNotFoundException extends Aflexi_Common_Model_EntityException{

    function __construct($type = NULL, $id = NULL, $message = '', $code = self::CLIENT_ENTITY_NOT_FOUND){
        if(empty($message)){
            parent::__construct(sprintf(
                "Could not find Entity%s with key %s", 
                empty($type) ? '' : " '$type'",
                empty($id) ? 'unknown' : $id
            ), $code);
        } else{
            parent::__construct($message, $code);
        }
    }
}

?>