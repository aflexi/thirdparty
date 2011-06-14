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
 
/**
 * An extension of LogicException thrown when the application is not in the 
 * appropriate state for the requested operation, might have been invoked at an 
 * illegal or inappropriate time.
 * 
 * @author yclian
 * @since ~1.x
 * @version 2.3.20100508
 * @see http://java.sun.com/javase/6/docs/api/java/lang/IllegalStateException.html	
 */
class Aflexi_Common_Lang_IllegalStateException extends LogicException{
}

?>