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
interface Aflexi_CdnEnabler_Core_UserHelper{

    function getUser($id);

    function getUsers($cdnEnabledOnly = FALSE);

    function getCdnUser($id);

    function getCdnUsers();

    function isCdnEnabled($id);

    function setCdnEnabled($id, $enabled = TRUE);

    /**
     * Logic to handle a user when he is upgraded or downgraded to another
     * package.
     *
     * @param $id
     * @param $package1
     * @param $package2
     */
    function handlePackageChanged($id, $package1, $package2);
}

?>