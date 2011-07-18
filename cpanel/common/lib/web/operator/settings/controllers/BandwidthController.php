
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
 class Settings_BandwidthController extends Zend_Controller_Action{
    /**
     * @var Aflexi_CdnEnabler_Cpanel_Container
     */
    protected $container;

    /**
     * @var Aflexi_CdnEnabler_Cpanel_Config
     */
    protected $config;
    
    
    function init() {
        $this->container = Aflexi_CdnEnabler_Cpanel_Container::getInstance();
        $this->config = $this->container->getConfig();
        
        $this->container->getSecurityHelper()->hasRoot();
    }
    
    function indexAction() {
        //Forwarding to list
        $this->_forward('list');
    }
    
    function listAction() {
        $afx_template_context = array(); 
        $params = array();
        $afx_errors = array();
        $bandwidthUsage = array();
        $userList = array();

        $cdnUsers = $this->container->getUserHelper()->getCdnUsers();
        $cpUsers = $this->container->getUserHelper()->getSyncStatuses($cdnUsers);
        $cpPackages = $this->container->getPackageHelper()->getPackages();
        $cdnPackages = $this->container->getPackageHelper()->getCdnPackages();


        $bandwidthUsage = $this->container->getBandwidthHelper()->getCdnBandwidthUsage(1, NULL, NULL, $cdnUsers);
        foreach ($cpUsers as $type=>$users) {
            foreach ($users as $username=>$user) {
                $userList[$username] = array('name' => $username);
                $userList[$username]['bandwidthUsage'] = $this->container->getBandwidthHelper()->getBandwidthUsage($username);
                if (isset($cdnUsers[$username])) {
                    $userList[$username]['cdnBandwidthUsage'] = $bandwidthUsage[$cdnUsers[$username]['id']];
                }
            }
        }
        
        ksort($cp_users['synced']);
        ksort($cp_users['unsynced_unsuspend']);
        ksort($cp_users['unsynced_unsuspend']);
        ksort($cp_users['unsynced_create']);
        ksort($cp_users['unsynced_delete']);
        
        $afx_template_context = array_merge(
            $afx_template_context,
            array(
                'users' => $cpUsers,
                'packages' => $cpPackages,
                'cdnPackages' => $cdnPackages,
                'bandwidth_used' => $bandwidthUsage,
                'userList' => $userList,
                'params' => $params,
                'errors' => $afx_errors
            )
        );
        $this->view->assign($afx_template_context);
    }
}
?>