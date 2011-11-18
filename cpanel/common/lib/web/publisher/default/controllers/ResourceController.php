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
 
class ResourceController extends Zend_Controller_Action{
    /**
     * @var Aflexi_CdnEnabler_Cpanel_Container
     */
    protected $container;
    
    /**
     * @var Aflexi_CdnEnabler_Cpanel_Config
     */
    protected $config;

    /**
     * @var Aflexi_Common_Log_Logger
     */
    private static $logger;
    
    function init() {
        $this->container = Aflexi_CdnEnabler_Cpanel_Container::getInstance();
        $this->config = $this->container->getConfig();
        $this->initializeStatic();
    }

    function initializeStatic(){
        self::$logger = Aflexi_Common_Log_LoggerFactory::getLogger(__CLASS__);
    }

    function createcallbackAction() {
        $afx_template_context = array();
        
        $callback = @json_decode($this->getRequest()->getParam('callback_result'));
        $name = @$callback->publishedName;
        $cname = @$callback->cname;
        
        if ($cname{strlen($cname) - 1} == '.') {
            $cname = substr($cname, 0, strlen($cname) - 1);
        }
        
        if(self::$logger->isDebugEnabled()){
           self::$logger->debug("Callback create resource with {$this->config['global']['integration']['cname']['auto_cname']} mode and published name".var_export(
                array(
                    'published_name'=>$name,
                    'cname' => $cname
                ), TRUE));
        }

        if ($name && $cname) {
            $this->container->getDomainHelper()->addCName($name, $cname);
        }
        
        $afx_template_context['redirect'] = urlencode('resources.html');
        
        $this->view->assign($afx_template_context);
    }

    /**
     * deleteCallback is triggered when resourceDeleted
     * This callback to delete cname in cpanel
     * @author yasir
     * @since: 2.12.20110315
     * @version: 2.12.20110315
     * @return
     */
    function deletecallbackAction(){
        $afx_template_context = array();
        $callback = @json_decode($this->getRequest()->getParam('callback_result'));
        $cname = @$callback->cname;

        if ($cname{strlen($cname) - 1} == '.') {
            $cname = substr($cname, 0, strlen($cname) - 1);
        }
        if(self::$logger->isDebugEnabled()){
           self::$logger->debug("Callback delete resource with {$this->config['global']['integration']['cname']['auto_cname']} mode and published name".var_export($cname, TRUE));
        }

        if($cname){
            $this->container->getDomainHelper()->deleteCName($cname);
        }

        $afx_template_context['redirect'] = urlencode('resources.html');

        $this->view->assign($afx_template_context);

    }
}

?>
