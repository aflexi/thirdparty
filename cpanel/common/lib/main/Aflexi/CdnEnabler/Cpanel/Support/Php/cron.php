#!/usr/bin/php
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
 define('CPANEL_AFX_LIB_PARENT', dirname(__FILE__).'/../../../../../../../../3rdparty/aflexi/lib');

set_include_path(
    CPANEL_AFX_LIB_PARENT.'/main:'.
    CPANEL_AFX_LIB_PARENT.'/vendor:'.
    get_include_path()
);

require_once dirname(__FILE__).'/../../Operator/Bootstrap.php';
Aflexi_CdnEnabler::fastBoot(new Aflexi_CdnEnabler_Cpanel_Operator_Bootstrap());

$_ENV['REMOTE_USER'] = 'root';

class Aflexi_CdnEnabler_Cpanel_Support_Php_Cron {
    /**
     * @var Aflexi_CdnEnabler_Cpanel_Container
     */
    private $container;

    function __construct() {
        $this->container = Aflexi_CdnEnabler_Cpanel_Container::getInstance();
    }
    
    function sync() {
        $this->syncPackages();
        $this->syncUsers();
        $this->syncDomains();
        // [yasir 20110324] Disable this one, since it is not using anymore
        // and GM has problem on createTempBandwitdh
        // $this->syncBandwidth();
    }
    
    protected function syncPackages() {
        $this->container->getPackageHelper()->syncPackages();
    }
    
    protected function syncUsers() {
        $this->container->getUserHelper()->syncUsers();

        $this->container->getUserHelper()->onUserUpgradePackage();

        $this->container->getUserHelper()->onUserDeleted();
    }
    
    protected function syncDomains() {
        $domains = $this->container->getDomainHelper()->getDomains();
        $cdnUsers = $this->container->getUserHelper()->getCdnUsers();
        
        foreach ($cdnUsers as $user) {
            if (isset($domains[$user['name']])) {
                $this->container->getDomainHelper()->setDomains(
                    $user['id'],
                    json_encode($domains[$user['name']])
                );
            }
        }
    }
    
    protected function syncBandwidth() {
        $cpUsers = $this->container->getUserHelper()->getUsers();
        $cdnUsers = $this->container->getUserHelper()->getCdnUsers();
        
        foreach ($cpUsers as $user) {
            if ((isset($cdnUsers[$user['USER']])) && ($user['BWLIMIT'] != $cdnUsers[$user['USER']]['bandwidthPackage']['bandwidthLimit'])) {
                $this->container->getUserHelper()->createCdnTempBandwidth($cdnUsers[$user['USER']]['id'], $user['BWLIMIT']);
            }
        }
    }
}

$cron = new Aflexi_CdnEnabler_Cpanel_Support_Php_Cron();
$cron->sync();
?>