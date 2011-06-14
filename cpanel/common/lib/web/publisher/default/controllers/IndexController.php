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
 
class IndexController extends Zend_Controller_Action{
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
    }

    function indexAction(){
        $afx_template_context = array();

        $afx_oauth_request = $this->container->getOAuthHelper()->request(array(
            'key' => $this->config['publisher']['oauth_key'],
            'secret' => $this->config['publisher']['oauth_secret']
        ));
        $afx_oauth_token = $afx_oauth_request->oauth_token;
        
        $usernameUrl = urlencode($this->config['publisher']['username']);
        
        $redirect = '';
        if ($this->getRequest()->getParam('redirect')) {
            $redirect = $this->getRequest()->getParam('redirect');
        }

        // TODO [yasir 20110309] Updating application_uri of OAuth should be done oauth/update, $store->updateConsumer(,, TRUE)
        $afx_url = "{$this->config['global']['portal']['url']['mini_publisher']}/{$redirect}?app=cpanel&oauth_token={$afx_oauth_token}&auth_username={$usernameUrl}";

        $cpanelProtocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';

        $cpanel_uri = array(
            'application_uri' => isset($_SERVER['HTTP_HOST']) ? isset($_SERVER['SERVER_PORT'])? "{$cpanelProtocol}{$_SERVER['HTTP_HOST']}:{$_SERVER['SERVER_PORT']}/" :  "{$cpanelProtocol}{$_SERVER['HTTP_HOST']}/" : ''
        );

        if(!empty($cpanel_uri['application_uri'])){
                $afx_url .= "&oauth_cow=".urlencode(json_encode($cpanel_uri));
        }
        
        $cp_domains = $this->container->getDomainHelper()->getDomains();
        
        // If you are having some domains, we will always update the list.
        if(key_exists($_ENV['REMOTE_USER'], $cp_domains)){
            
            $cp_domains_for_users = $cp_domains[$_ENV['REMOTE_USER']];
            sort($cp_domains_for_users, SORT_STRING);
            
            // FIXME [yclian 20100712] We can't import this as we do not have XML-
            // RPC password. We will have to improve with an XML-RPC proxy or JSON-
            // proxy at portal.
            // afx_xmlrpc_set_prefs_publisher(array(
            //    'resource.domains' => implode(',', $cp_domains_for_users)
            //));
        }
        
        $afx_template_context = array_merge(
            $afx_template_context,
            array(
                'first_time' => empty($afx_config_cpanel) ? TRUE : FALSE,
                'errors' => $afx_errors,
                'iframe_url' => $afx_url
            )
        );
        $this->view->assign($afx_template_context);

    }
    
}

?>
