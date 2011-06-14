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
 * Exception for all Data Access Object related.
 * 
 * @author hantu
 * @since 2.2
 * @version 2.5.20100628
 */
class Aflexi_Common_Model_DataAccessException extends Aflexi_Common_Exception {

    function __construct($message = '', $code = self::SERVER_DATA_ACCESS_EXCEPTION){
        parent::__construct($message, $code);
    }
}

?>