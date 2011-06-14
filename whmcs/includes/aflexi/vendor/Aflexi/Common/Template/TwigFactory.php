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
 
# namespace Aflexi\Common\Template;

require_once 'Twig/Autoloader.php';
Twig_Autoloader::register();

/**
 * Factory class used for bootstrapping Twig, registering its components and 
 * providing accessors to them.
 * 
 * @author yclian
 * @since 2.5
 * @version 2.5.20100603
 */
class Aflexi_Common_Template_TwigFactory extends Aflexi_Common_Template_TemplateFactory{
        
    /**
     * @var Twig_Loader_Filesystem
     */
    private $fs;
    /**
     * @var Twig_Environment
     */
    private $environment;
    
    function __construct($templateDir, $cacheDir = NULL){
        $this->initializeTwig($templateDir, $cacheDir);
    }
    
    private function initializeTwig($templateDir, $cacheDir = NULL){
        
        if(empty($cacheDir)){
            $cacheDir = $this->getDefaultCacheDir();
        }
        
        $this->fs = new Twig_Loader_Filesystem(array(
            $templateDir, 
            $templateDir.'/base'
        ));
        $this->environment = new Twig_Environment(
            $this->fs,
            array(
                // Use for debugging, printing out the context.
                // 'debug' => TRUE,
                // Use this if you do not while NULL values to be breaking the
                // compiler (during debug). See Twig_Environment class for more
                // details.
                // 'strict_variables' => FALSE,
                //'cache' => $cacheDir,
            ) 
        );
        $this->environment->addExtension(new Aflexi_Common_Template_Twig_StringExtension());
    }
    
    /**
     * @return string
     */
    private function getDefaultCacheDir(){
        $cacheDir = sys_get_temp_dir().'/twig-cache';
        if(!file_exists($cacheDir)){
            mkdir($cacheDir);
        }
        return $cacheDir;
    }
    
    /**
     * @return Twig_Environment
     */
    function getEnvironment(){
        return $this->environment;
    }
    
    
    function renderTemplate($template, array $context = array()){
        $template = $this->getEnvironment()->loadTemplate($template);
        return $template->render($context);
    }
}

?>
