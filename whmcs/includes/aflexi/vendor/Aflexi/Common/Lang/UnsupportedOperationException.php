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
 * Thrown if a method is unsupported - not when it's unimplemented, but for a 
 * defined abstract method or overriden method, the implementing class does
 * not support it.
 * 
 * @author yclian
 * @since ~1.0
 * @version 2.5.20100625
 */
class Aflexi_Common_Lang_UnsupportedOperationException extends Exception{
}

?>