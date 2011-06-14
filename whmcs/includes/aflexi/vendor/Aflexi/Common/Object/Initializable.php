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
 * Constructor can be really bad sometimes as it makes mocking harder, it makes 
 * overriding and dynamic instatiation impossible in some cases due to its 
 * strong coupling with initialization code.
 * 
 * This interface decouples the consturction of object and initialization of
 * its state. It is encouraged to be implemented by all stateful objects.
 * 
 * Any code such as factory shall honour the interface while handling 
 * initialization.
 *
 * @author yclian
 * @since 2.3
 * @version 2.3.20100410
 */
interface Aflexi_Common_Object_Initializable{

    function initialize();
}

?>