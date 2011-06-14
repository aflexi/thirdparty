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
 * Common interface for a bootstrapper. A bootstrapper usually has two phases:
 * 
 *  - 'Prepare' phase, typically for resolving include paths and dependencies.
 *  - 'Boot' phase, where the application is to be made fully started up, as a
 *    result, container services and objects are created.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.8.20100929
 */
interface Aflexi_Common_Application_Bootstrapper{
    
    /**
     * @return Aflexi_Common_Application_Bootstrapper A self-reference.
     */
    function prepare();
    
    /**
     * @return Aflexi_Common_Application_Bootstrapper A self-reference.
     */
    function boot();
}
