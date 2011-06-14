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
 require_once('Twig/Autoloader.php');
Twig_Autoloader::register();

# namespace Aflexi\CdnEnabler\Cpanel\Mvc;

require_once dirname(__FILE__).'/../Publisher/Bootstrap.php';
Aflexi_CdnEnabler::fastBoot(new Aflexi_CdnEnabler_Cpanel_Publisher_Bootstrap());

/**
 * Front controller for publisher/CPanel.
 * 
 * @author yingfan
 * @since 2.10.20101110
 * @version 2.10.20101110
 */
class Aflexi_CdnEnabler_Cpanel_Publisher_Mvc extends Aflexi_Common_Mvc_ZendTwig{
    
    /**
     * @var Aflexi_CdnEnabler_Cpanel_Container
     */
    private $container;
    
    /**
     * @return Aflexi_CdnEnabler_Cpanel_Mvc_OperatorFront
     */
    function initialize(){
        
        $this->mvcDir = realpath(dirname(__FILE__).'/../../../../../web/publisher');
        $this->twigLoader = new Twig_Loader_Filesystem(array(
            $this->mvcDir
        ));
        $this->twigOptions = array(
            // Use for debugging, printing out the context.
            'debug' => TRUE,
            // Use this if you do not while NULL values to be breaking the
            // compiler (during debug). See Twig_Environment class for more
            // details.
            // 'strict_variables' => FALSE,
            'cache' => CPANEL_AFX_DATA.'/cache/templates',
        );
        
        parent::initialize();
        
        $this->container = Aflexi_CdnEnabler_Cpanel_Container::getInstance();
        $this->registerPlugins();
        
        return $this;
    }
    
    function configureViewRenderer(){
        parent::configureViewRenderer();
        $this->viewRenderer->setViewSuffix('tpl');
    }
    
    private function registerPlugins(){
        
        $queryStringForwarder = new Aflexi_CdnEnabler_Cpanel_Operator_Mvc_QueryStringForwarder();
        $this->getFrontController()->registerPlugin($queryStringForwarder);
        
    }
}
