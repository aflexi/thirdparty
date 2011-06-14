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
 * Plugin to forward to respective module/controller/action based on query string
 * 
 * @author yingfan
 * @since 2.10
 * @version 2.10.20101111
 */
class Aflexi_CdnEnabler_Cpanel_Operator_Mvc_QueryStringForwarder extends Zend_Controller_Plugin_Abstract {
    /**
     * @var bool
     */
    private $isForwarding = FALSE;
    
    /**
     * Detects if module,controller, or action key exists in request object
     * If so, set isForwarding boolean to TRUE, to signify forwarding to be done
     * (non-PHPdoc)
     * @see Zend_Controller_Plugin_Abstract::routeStartup()
     */
    function routeStartup(Zend_Controller_Request_Abstract $request){
        if (isset($_REQUEST['module']) || isset($_REQUEST['controller']) || isset($_REQUEST['action'])) {
            $this->isForwarding = TRUE;
        }
    }
    
    /**
     * Detects if isForwarding is set to TRUE
     * If so, call forwarding function
     * (non-PHPdoc)
     * @see Zend_Controller_Plugin_Abstract::routeShutdown()
     */
    function routeShutdown(Zend_Controller_Request_Abstract $request){
        if($this->isForwarding) {
            $this->forwardByQueryString($request);
        }
    }

    /**
     * 
     * Forward module/controller/action based on query string
     * @param Zend_Controller_Request_Abstract $request
     */
    private function forwardByQueryString(Zend_Controller_Request_Abstract $request){
        if (isset($_REQUEST['module'])) {
            $request->setModuleName($_REQUEST['module']);
        }
        
        if (isset($_REQUEST['controller'])) {
            $request->setControllerName($_REQUEST['controller']);
        }
        
        if (isset($_REQUEST['action'])) {
            $request->setActionName($_REQUEST['action']);
        }
        
        $request->setParams(array());
    }
}
