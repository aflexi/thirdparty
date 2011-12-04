
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
 class Settings_SyncController extends Zend_Controller_Action{
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
        $afx_template_context = array();
        $afx_errors = array();

        if($this->getRequest()->isPost() && $this->getRequest()->getParam('submit')){
            $results;
            $afx_operator;
            $globalConfig;
            
            $results = array(
                'packages' => array(
                    'synced' => 0,
                    'created' => 0
                ),
                'users' => array(
                    'synced' => 0,
                    'created' => 0
                ),
            );
            
            
            switch(@$_REQUEST['sync-type']){
                case 1:{
                    $results['packages'] = $this->container->getPackageHelper()->syncPackages();
                    break;
                }
                case 2:{

                    // Enable sharing package and get packageName
                    $this->container->getUserHelper()->onUserUpgradePackage();
                    $results['users'] = $this->container->getUserHelper()->syncUsers();
                    $this->container->getUserHelper()->onUserDeleted();
                    break;
                } 
                default:{

                    $results['packages'] = $this->container->getPackageHelper()->syncPackages();
                    // NOTE [yclian 20100729] Yes, I know afx_xmlrpc_get_packages() 
                    // is being called twice as afx_whm_sync_packages() is calling 
                    // it too.
                    $this->container->getUserHelper()->onUserUpgradePackage();
                    $results['users'] = $this->container->getUserHelper()->syncUsers();


                    $this->container->getUserHelper()->onUserDeleted();
                    break;
                }
            }

        }

        
        $afx_template_context = array_merge(
            $afx_template_context,
            array(
                'sync_results' => $results,
                'errors' => $afx_errors
            )
        );
        $this->view->assign($afx_template_context);
    }
}
?>