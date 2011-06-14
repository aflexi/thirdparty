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
 
# namespace Aflexi/Common/IO;

require_once dirname(__FILE__).'/IoException.php';

/**
 * Exception thrown when a file is not found.
 *
 * @author yclian
 * @since 2.3
 * @version 2.3.20100412
 */
class Aflexi_Common_IO_FileNotFoundException extends Aflexi_Common_IO_IoException{

}

?>