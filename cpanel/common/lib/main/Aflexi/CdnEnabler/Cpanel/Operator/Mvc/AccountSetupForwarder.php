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
 * Plugin to forward an Operator to /settings/account if runtime user can't be
 * detected.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.8.20101001
 */
class Aflexi_CdnEnabler_Cpanel_Operator_Mvc_AccountSetupForwarder extends Zend_Controller_Plugin_Abstract implements Aflexi_CdnEnabler_Cpanel_ContainerAware{
    
    /**
     * @var Aflexi_Common_Log_Logger
     */
    private static $logger = NULL;
    
    /**
     * @var Aflexi_CdnEnabler_Cpanel_Container
     */
    private $container = NULL;
	
    /**
     * Flag to indicate if the user (Operator) requires set up.
     * 
     * @var bool
     */
    private $setupRequired = FALSE;
    
    /**
     * @return void
     * @see impl/Aflexi/CdnEnabler/Cpanel/Aflexi_CdnEnabler_Cpanel_ContainerAware::setContainer()
     */
    function setContainer(Aflexi_CdnEnabler_Cpanel_Container $container){
        $this->container = $container;
    }
    
    /**
     * Check configurations to determine if a setup is required. routeStartup() 
     * is the first call processing the request, so we can detect this as early
     * as we can. 
     * 
     * @return void
     * @see Zend/Controller/Plugin/Zend_Controller_Plugin_Abstract::routeStartup()
     */
    function routeStartup(Zend_Controller_Request_Abstract $request){
          $config = $this->container->getConfig();
//        if(!$this->container->getConfig()->getRuntime('operator')){
          if (!file_exists($config->getSource('operator'))) {
            
            $logger = Aflexi_Common_Log_LoggerFactory::getLogger(__CLASS__);
            if($logger->isWarnEnabled()){
                $logger->warn("Could not detect operator in runtime configuration, account setup is required");
            }
            
            // If we are already in settings/account, we should mark this.
            // TODO [yclian 20101001] What shall the action be when settings 
            // are being saved?
            if($request->getModuleName() != 'settings' && 
               $request->getControllerName() != 'account'){
                $this->setupRequired = TRUE;
            }
        }
    }

    /**
     * Forward the request. It has to be done during routeShutdown() due to 
     * Zend's behaviour in overwriting these values in 
     * Zend_Controller_Router_Rewrite::route().
     * 
     * @return void
     * @see Zend/Controller/Plugin/Zend_Controller_Plugin_Abstract::routeShutdown()
     */
    function routeShutdown(Zend_Controller_Request_Abstract $request){
        if($this->setupRequired){
            $this->forwardToSetup($request);
        }
    }
    
    /**
     * If setup is required? This method is typicall called by other plugins or 
     * unit tests.
     * 
     * @return bool TRUE if setup is required.
     */
    function isSetupRequired(){
        return $this->setupRequired;
    }
    
    private function forwardToSetup(Zend_Controller_Request_Abstract $request){
        $request->setModuleName('settings');
        $request->setControllerName('account');
        $request->setActionName('index');
        $request->setParams(array());
    }
}
