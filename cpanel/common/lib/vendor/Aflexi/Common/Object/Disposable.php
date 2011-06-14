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
 
# namespace Aflexi\Common\Object\Disposable;

/**
 * No good scenario how we can use this yet. PHP has a {@code __destruct()} 
 * function that shall work just fine. Maybe we can argue that, you want 
 * to separate business/application logic from object de-referencing (namely,
 * calling the destructor).
 * 
 * @author yclian
 * @since 2.3
 * @version 2.3.20100410
 */
interface Aflexi_Common_Object_Disposable{

    /**
     * @return mixed The object itself, chaining subroutine calls.
     */
    function dispose();
}

?>