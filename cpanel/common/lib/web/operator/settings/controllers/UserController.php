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
 class Settings_UserController extends Zend_Controller_Action{
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
        $cp_user_helper;
        $cp_package_helper;
        $afx_template_context = array();
         
        $cp_packages;
        $cp_users;
        /** 
         * cPanel users with CDN enabled
         * 
         * @var array
         */
        $cp_users_cdn = array();
        
        $afx_packages;
        $afx_users;
        
        $afx_users = $this->container->getUserHelper()->getCdnUsers();
        $cp_users = $this->container->getUserHelper()->getSyncStatuses($afx_users);
        
        ksort($cp_users['synced']);
        ksort($cp_users['unsynced_unsuspend']);
        ksort($cp_users['unsynced_unsuspend']);
        ksort($cp_users['unsynced_create']);
        ksort($cp_users['unsynced_delete']);
//        ksort($cp_users['unqualified']);
        
        $afx_template_context = array_merge(
            $afx_template_context,
            array(
                'users' => $cp_users,
                'users_ext' => $afx_user,
                'errors' => $afx_errors
            )
        );
        $this->view->assign($afx_template_context);
    }
}
?>
