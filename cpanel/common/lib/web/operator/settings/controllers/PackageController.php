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
 class Settings_PackageController extends Zend_Controller_Action{
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
    
    /**
     * 
     * package/index would routes to package/list
     */
    function indexAction() {
        //Forwarding to list
        $this->_forward('list');
    }
    
    /**
     * 
     * UI for listing packages, and enabling feature with CDN 
     */
    function listAction() {
        $afx_template_context = array();
        $cp_package_helper;
        $afx_operator;
        $afx_packages;
        $cp_packages;
        $cp_features;
        $afx_packages_sync_results;
        $afx_users_sync_results;
        
        $afx_operator = $this->container->getUserHelper()->getCdnSelfUser();
        $afx_packages = $this->container->getPackageHelper()->getCdnPackages();
        
        $cp_packages = $this->container->getPackageHelper()->getSyncStatuses($afx_packages);
        
        $cp_feature_packages = array(
            'synced' => $this->getFeatureToPackages($cp_packages['synced']),
            'unsynced' => $this->getFeatureToPackages($cp_packages['unsynced']),
            'unqualified' => $this->getFeatureToPackages($cp_packages['unqualified']),
        );

        if($this->getRequest()->isPost() && $this->getRequest()->getParam('submit')){
            if($this->getRequest()->getParam('feature-lists')){
                
                $afx_packages_synced;
                $cp_packages_filter;
                
                $afx_packages_synced = $afx_packages;
                $cp_packages_filter = array();
                
                foreach($_REQUEST['feature-lists'] as $cp_featurelist){
                    // Update in cPanel first.
                    $this->container->getPackageHelper()->setCdnEnabled(
                        $cp_featurelist,
                        // TODO [yclian 20100723] We have the flexibility here. 
                        // We will support disabling from this form next time.
                       $_REQUEST['submit'],
                       FALSE
                    );
                    
                    // We will push to sync if they are in unqualified. This safe-check is needed in case user submits twice - 
                    // then you will hit into index problem.
                    if(isset($cp_feature_packages['unqualified'][$cp_featurelist])){
                        $cp_packages_filter = array_merge(
                            $cp_packages_filter,
                            array_keys($cp_feature_packages['unqualified'][$cp_featurelist])
                        );
                    }
                }

                // Here we sync. If things broken, just repair manually.
                $afx_packages_sync_results = $this->container->getPackageHelper()->syncPackages($cp_packages_filter, $afx_packages_synced);
                // All existing and newly synced packages will have their publishers synced.
                //$afx_users_sync_results = $this->container->getUserHelper()->syncUsers();
                // Loop, unset, and set.
                
                foreach($this->getRequest()->getParam('feature-lists') as $cp_featurelist){
                    
                    // If something is being synced, it must be in the unqualified list.
                    // So we unset it from unqualified, and put into synced.
                    
                    // NOTE [yclian 20100728] This prevents someone who reloads twice to see
                    // undefined index error.
                    if(array_key_exists($cp_featurelist, $cp_feature_packages['unqualified'])){
                        $cp_feature_packages['synced'][$cp_featurelist] = $cp_feature_packages['unqualified'][$cp_featurelist];
                        unset($cp_feature_packages['unqualified'][$cp_featurelist]);
                    }
                }
            }
            
            if(empty($afx_errors)){
                $afx_template_context['info'] = '<p>Your configurations are successfully stored. There are '.
                    "<strong>${afx_packages_sync_results['synced']} new CDN package(s)</strong> created ".
                    "and <strong>${afx_packages_sync_results['updated']}</strong> updated. ".  
                    'You may now want to <a href="user-list.php">view users with CDN access</a>.</p>';
            }
        } else{
            // TODO [yclian 20100726] What exactly shall we do here?
            // if(sizeof($cp_feature_packages['unsynced']) + sizeof($cp_feature_packages['unqualified']) > 0){
            // }
        } // end_if
        
        ksort($cp_feature_packages['synced']);
        ksort($cp_feature_packages['unsynced']);
        ksort($cp_feature_packages['unqualified']);
        // TODO [yclian 20100729] We need ext here.
        
        // Regardless of POST or GET, the form shall always be displayed.
        $afx_template_context = array_merge(
            $afx_template_context,
            array(
                'feature_packages' => $cp_feature_packages,
                'afxPackages' => $afx_packages,
                'errors' => $afx_errors
            )
        );
        $this->view->assign($afx_template_context);
    }
    
    /**
     * 
     * Edit CDN package via iframe to mini app
     */
    function editAction() {
        $afx_errors = array();
        $afx_template_context = array();
        
        $callback_url = $this->getUri();
        $callback_url = substr($callback_url, 0, strpos($callback_url, '=edit')).
            '=editCallback';
        $callback_url = urlencode($callback_url);
        
        $id = $this->getRequest()->getParam('id');
        
        $afx_url = $this->container->getOAuthHelper()->getIframeUrl(
            urlencode($this->config['operator']['auth']['username']),
            $this->config['operator']['oauth']['key'],
            $this->config['operator']['oauth']['secret_key'],
            "/package/edit/id/{$id}",
            "&callback_url={$callback_url}"
        );

        $afx_template_context = array_merge(
            $afx_template_context,
            array(
                'first_time' => empty($afx_config_cpanel) ? TRUE : FALSE,
            	'iframe_url' => $afx_url,
                'errors' => $afx_errors
            )
        );
        $this->view->assign($afx_template_context);
            
    }
    
    function editcallbackAction() {
        $afx_template_context = array();
        $this->view->assign($afx_template_context);
    }
    
    
    /**
     * Given an array of cPanel packages, group them by their feature.
     * 
     * @param array $cp_packages
     * @since 2.6
     * @version 2.6.20100720
     */
    protected function getFeatureToPackages(array $cp_packages){
        
        $rt = array();
        
        foreach($cp_packages as $cp_package_name){
            
            $cp_package = $this->container->getPackageHelper()->getPackage($cp_package_name);
            
            $cp_feature_name = $cp_package['FEATURELIST'];
            
            if(!isset($rt[$cp_feature_name])){
               $rt[$cp_feature_name] = array();
            }
            
            $rt[$cp_feature_name][$cp_package_name] = $cp_package;
        }
        
        return $rt;
    }
    
    protected function getUri() {
        $rt;
        
        $relative_uri = $_SERVER['REQUEST_URI'];
        
            
        $rt = (@$_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
        
        $rt .= ($_SERVER['SERVER_PORT'] == '80') ? 
            "{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}":
            "{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$_SERVER['REQUEST_URI']}";
            
        return $rt;
    }
}
?>