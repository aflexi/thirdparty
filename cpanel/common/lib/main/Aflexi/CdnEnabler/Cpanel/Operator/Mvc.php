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

require_once dirname(__FILE__).'/../Operator/Bootstrap.php';
Aflexi_CdnEnabler::fastBoot(new Aflexi_CdnEnabler_Cpanel_Operator_Bootstrap());

/**
 * Front controller for operator/WHM.
 * 
 * @author yclian
 * @since 2.8.20100928
 * @version 2.8.20100928
 */
class Aflexi_CdnEnabler_Cpanel_Operator_Mvc extends Aflexi_Common_Mvc_ZendTwig{
    
    /**
     * @var Aflexi_CdnEnabler_Cpanel_Container
     */
    private $container;
    
    /**
     * @return Aflexi_CdnEnabler_Cpanel_Mvc_OperatorFront
     */
    function initialize(){
        
        $this->mvcDir = realpath(dirname(__FILE__).'/../../../../../web/operator');
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
        
        $accountSetup = new Aflexi_CdnEnabler_Cpanel_Operator_Mvc_AccountSetupForwarder();
        $accountSetup->setContainer($this->container);
        
        $this->getFrontController()->registerPlugin($accountSetup);
        
        $queryStringForwarder = new Aflexi_CdnEnabler_Cpanel_Operator_Mvc_QueryStringForwarder();
        $this->getFrontController()->registerPlugin($queryStringForwarder);
        
    }
}
