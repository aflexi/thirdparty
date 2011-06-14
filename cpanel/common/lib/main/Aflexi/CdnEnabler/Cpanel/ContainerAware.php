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
 
# namespace Aflexi\CdnEnabler\Cpanel;

/**
 * Interface to be implemented by classes that have dependency to 
 * Aflexi_CdnEnabler_Cpanel_Container, typically to access the objects 
 * registered by the container.
 * 
 * @author yclian
 * @since 2.9
 * @version 2.9.20101001
 */
interface Aflexi_CdnEnabler_Cpanel_ContainerAware{
    
    /**
     * Set the Aflexi_CdnEnabler_Cpanel_Container.  This call shall be made
     * during object initialization (after construction).
     *  
     * @param $container
     * @return void
     */
    function setContainer(Aflexi_CdnEnabler_Cpanel_Container $container);
}

?>