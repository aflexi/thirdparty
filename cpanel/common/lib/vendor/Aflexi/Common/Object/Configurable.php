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
 * This interface imposes a key/value based configuration on an object. It 
 * intends to replace the reliance of object configuration via constructor, 
 * identical goal with Aflexi_Common_Object_Initializable.
 *
 * @author yclian
 * @since 2.8.20100928
 * @version 2.8.20100928
 */
interface Aflexi_Common_Object_Configurable{

    /**
     * @param $assoc
     * @return mixed The object itself, chaining subroutine calls.
     */
    function configure(array $assoc = array());
}

?>