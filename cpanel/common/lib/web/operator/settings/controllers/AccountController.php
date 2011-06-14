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
 
class Settings_AccountController extends Zend_Controller_Action{
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

    function indexAction(){
        $afx_errors;
        $afx_template_context = array();
        $operatorConfig;
        
        if ($this->getRequest()->isPost()) {
            $post_username = $this->getRequest()->getParam('username');
            $post_auth_key = $this->getRequest()->getParam('auth_key');
            $post_whm_host = $this->getRequest()->getParam('whm_host');
            $post_cpanel_host = $this->getRequest()->getParam('cpanel_host');
            $post_cpanel_cname = $this->getRequest()->getParam('cpanel_cname');

            // [yasir 20110313] The values should not be reset.
            $afx_template_context['params']['username'] = $post_username;
            $afx_template_context['params']['auth_key'] = $post_auth_key;
            $afx_template_context['params']['whm_host'] = $post_whm_host;
            $afx_template_context['params']['cpanel_host'] = $post_cpanel_host;
            $afx_template_context['params']['cname'][$post_cpanel_cname] = "checked";

            if(empty($post_username) || empty($post_auth_key) || empty($post_whm_host) ||  empty($post_cpanel_host) || empty($post_cpanel_cname)){
                $afx_errors []= 'There are missing values in required field(s).';
            } else{
                $afx_operator;
                $afx_oauth_register;
                
                try{
                    $afx_operator = $this->container->getUserHelper()->getCdnSelfUser($post_username, $post_auth_key);
                    // FIXME [yclian 20100818] GM hasn't yet incorporated the fix
                    // to include 'username' in user's struct. 
                    if(!isset($afx_operator['username'])){
                        $afx_operator['username'] = !empty($afx_operator['agent']) ? "{$afx_operator['email']}/{$afx_operator['agent']['id']}" : $afx_operator['email'];
                    }
                    
                    $operatorConfig = $this->config['operator'];
                    $operatorConfig['auth'] = array(
                        'username' => $afx_operator['username'],
                        'key' => $post_auth_key
                    );
                    //Needs to update it before calling the OAuth register
                    $this->config['operator'] = $operatorConfig;
                    
                    //Append trailing slahes for URL
                    if ($post_whm_host{strlen($post_whm_host) -1 } != '/') {
                        $post_whm_host .= '/';
                    }
                    if ($post_cpanel_host{strlen($post_cpanel_host) -1 } != '/') {
                        $post_cpanel_host .= '/';
                    }
                    
                    //Update WHM/CPanel URLs
                    $globalConfig = $this->config['global'];
                    $globalConfig['integration']['url']['whm'] = $post_whm_host;
                    $globalConfig['integration']['url']['cpanel'] = $post_cpanel_host;

                    //Update Cpanel CNAME
                    $globalConfig['integration']['cname']['auto_cname'] = $post_cpanel_cname;

                    $this->config['global'] = $globalConfig;
                    // OAuth consumer
                    $afx_oauth_register = $this->container->getOAuthHelper()->register(
                        'standard'
                    );
                    $operatorConfig['oauth'] = array(
                        'key' => $afx_oauth_register->consumer_key,
                        'secret_key' => $afx_oauth_register->consumer_secret
                    );
                    
                    $this->config['operator'] = $operatorConfig;
                    
                } catch(Exception $e){
                    $afx_errors []= "Given credential rejected by server: {$e->getMessage()}";
                }
                
                // Success action
                if(empty($afx_errors)){
                    $this->config->write(
                        $this->config->getSource('operator'), 
                       'operator'
                    );
                    
                    $this->config->write(
                        $this->config->getSource('global'), 
                       'global'
                    );
                    
                    $afx_template_context['info'] = "<p>Your configurations are successfully stored and can manually be modified at <tt>{$afx_config_whm_path}</tt>. You may now want to <a href=\"/aflexi/index.php?module=settings&controller=package\">configure the packages</a>.</p>";
                }
            }
        }
        else{        
            $afx_template_context['params']['username'] = @$this->config['operator']['auth']['username'];
            $afx_template_context['params']['auth_key'] = @$this->config['operator']['auth']['key'];
            
            $serverHost = stripos($_SERVER_PROTOCOL['SERVER_PROTOCOL'], 'HTTPS') ? 'https://' : 'http://';
            $serverHost .= $_SERVER['SERVER_NAME'];


            $afx_template_context['params']['whm_host'] = @$this->config['global']['integration']['url']['whm'] ? $this->config['global']['integration']['url']['whm'] : "{$serverHost}:2086/";
            $afx_template_context['params']['cpanel_host'] = @$this->config['global']['integration']['url']['cpanel'] ? $this->config['global']['integration']['url']['cpanel'] : "{$serverHost}:2082/";
            $cpanel_auto_cname = @$this->config['global']['integration']['cname']['auto_cname'];
            
            if(empty($cpanel_auto_cname)){
                // By default enable the auto cname
                $cpanel_auto_cname = "enabled";
            }

            $afx_template_context['params']['cname'][$cpanel_auto_cname] = "checked";
        }
        
        
        $afx_template_context = array_merge(
            $afx_template_context,
            array(
                'portal' => $this->config['global']['portal']['url']['mini_operator'],
                'errors' => $afx_errors
            )
        );
        $this->view->assign($afx_template_context);
    }
}

?>
