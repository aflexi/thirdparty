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
 
# namespace Aflexi\CdnEnabler\Cpanel;

/**
 * Simple container to components and objects.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.8.20100918
 */
class Aflexi_CdnEnabler_Cpanel_Container{
    
    private static $instance = NULL;
    
    /**
     * Get the singleton instance.
     * 
     * @return Aflexi_CdnEnabler_Cpanel_Container
     */
    static function getInstance(){
        if(is_null(self::$instance)){
            $class = __CLASS__;
            self::$instance = new $class();
        }
        return self::$instance;
    }
    
    /**
     * Set or unset (if NULL is provided) the singleton instance.
     * 
     * @param Aflexi_CdnEnabler_Cpanel_Container|NULL $instance
     * @return void
     */
    static function setInstance($instance){
        if(!is_null($instance)){
            $class = __CLASS__;
            if(!$instance instanceof $class){
                throw new InvalidArgumentException("Must be an instance of '$class'");
            }
        }
        self::$instance = $instance;
    }
    
    /**
     * @var Aflexi_CdnEnabler_Config
     */
    private $config = NULL;
    
    /**
     * @var Aflexi_CdnEnabler_Cpanel_PackageHelper
     */
    private $packageHelper = NULL;
    
    /**
     * 
     * @var Aflexi_CdnEnabler_Cpanel_UserHelper
     */
    private $userHelper;
    
    /**
     * @return Aflexi_CdnEnabler_Cpanel_SecurityHelper
     */
    
    private $securityHelper;
    
    /**
     * @var Aflexi_CdnEnabler_Cpanel_OAuthHelper
     */
    private $oAuthHelper;
    
    /**
     * @var Aflexi_CdnEnabler_Cpanel_BandwidthHelper
     */
    private $bandwidthHelper;
    
    /**
     * @var Aflexi_CdnEnabler_Cpanel_DomainHelper
     */
    private $domainHelper;
    
    function getConfig(){
        if(!$this->config){
            $this->config = new Aflexi_CdnEnabler_Cpanel_Config();
        }
        return $this->config;
    }
    
    function getPackageHelper(){
        if(!$this->packageHelper){
            $this->packageHelper = new Aflexi_CdnEnabler_Cpanel_PackageHelper();
            $this->packageHelper->setConfig($this->getConfig());
            $xmlRpcClient = $this->getXmlRpcClient();
            $this->packageHelper->setXmlRpcClient($xmlRpcClient);
            $this->packageHelper->setUserHelper($this->getUserHelper());
            $this->packageHelper->initialize();
        }
        return $this->packageHelper;
    }
    
    function getUserHelper() {
        if (!$this->userHelper) {
            $this->userHelper = new Aflexi_CdnEnabler_Cpanel_UserHelper();
            $this->userHelper->setConfig($this->getConfig());
            $xmlRpcClient = $this->getXmlRpcClient();
            $this->userHelper->setXmlRpcClient($xmlRpcClient);

            if (file_exists($this->config->getSource('operator'))) {
                $this->userHelper->initialize();

                $runtimeConfig = $this->config['runtime'];
                $runtimeConfig['operator'] = $this->userHelper->getCdnSelfUser();
                $this->config['runtime'] = $runtimeConfig;
            }
        }
        return $this->userHelper;
    }
    
    function getSecurityHelper() {
        if (!$this->securityHelper) {
            $this->securityHelper = new Aflexi_CdnEnabler_Cpanel_SecurityHelper();
        }
        return $this->securityHelper;
    }
    
    function getOAuthHelper() {
        if (!$this->oAuthHelper) {
            $this->oAuthHelper = new Aflexi_CdnEnabler_Cpanel_OAuthHelper();
            $this->oAuthHelper->setConfig($this->getConfig());
            $this->oAuthHelper->initialize();
        }
        return $this->oAuthHelper;
    }
    
    function getBandwidthHelper() {
        if (!$this->bandwidthHelper) {
            $this->bandwidthHelper = new Aflexi_CdnEnabler_Cpanel_BandwidthHelper();
            $this->bandwidthHelper->setConfig($this->getConfig());
            $xmlRpcClient = $this->getXmlRpcClient($this->config['global']['xmlrpc']['uri']['stats']);
            $this->bandwidthHelper->setXmlRpcClient($xmlRpcClient);
            $this->bandwidthHelper->initialize();
        }
        return $this->bandwidthHelper;
    }
    
    function getDomainHelper() {
        if (!$this->domainHelper) {
            $this->domainHelper = new Aflexi_CdnEnabler_Cpanel_DomainHelper();
            $this->domainHelper->setConfig($this->getConfig());
            $xmlRpcClient = $this->getXmlRpcClient();
            $this->domainHelper->setXmlRpcClient($xmlRpcClient);
            $this->domainHelper->initialize();
        }
        return $this->domainHelper;
    }
    
    /**
     * @return Aflexi_Common_Net_XmlRpc_Client A new XML-RPC client always.
     */
    function getXmlRpcClient($uri = NULL){
        
        if(!$uri){
            $config = $this->getConfig();
            $uri = $config['global']['xmlrpc']['uri']['core'];
        }
        
        return new Aflexi_Common_Net_XmlRpc_Client($uri);
    }
}

?>