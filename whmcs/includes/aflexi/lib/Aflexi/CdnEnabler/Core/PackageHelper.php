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
 * @author yclian
 * @since 2.7
 * @version 2.7.20100826
 */
interface Aflexi_CdnEnabler_Core_PackageHelper{

    function getPackage($id);

    function getPackages($cdnEnabledOnly = FALSE);

    function getCdnPackage($id);

    function getCdnPackages();

    function isCdnEnabled($id);

    function setCdnEnabled($id, $enabled = TRUE);

    /**
     * Handle a package when it is being updated, e.g. being renamed, CDN
     * feature disabled, etc.
     *
     * @param $before
     * @param $after
     */
    function handlePackageChanged($before, $after);
}

?>