<?php

require_once('Twig/Autoloader.php');
Twig_Autoloader::register();

/**
 * Extension of Aflexi_Common_Mvc_Zend to support Twig_Adapter_Zend for 
 * templating.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.8.20011027
 */
abstract class Aflexi_Common_Mvc_ZendTwig extends Aflexi_Common_Mvc_Zend{
    
    /**
     * @var Zend_Controller_Action_Helper_ViewRenderer
     */
    protected $viewRenderer;
    
    /**
     * @var Zend_View_Interface
     */
    protected $view;
    
    /**
     * @var array
     */
    protected $twigOptions = array();
    
    /**
     * @var Twig_Loader_Filesystem
     */
    protected $twigLoader = NULL;
    
    /**
     * @return Aflexi_Common_Mvc_ZendTwig
     */
    function initialize(){
        parent::initialize();
        $this->configureViewRenderer();
        return $this;
    }
    
    protected function configureViewRenderer(){
        
        $this->view = new Aflexi_Common_Mvc_ZendTwig_View(new Twig_Environment(
            $this->twigLoader ? $this->twigLoader : new Twig_Loader_Filesystem(array()),
            $this->twigOptions
        ));
        $this->viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer($this->view);
        
        //$this->viewRenderer->setViewBasePathSpec($this->viewsDir)
                         //->setViewScriptPathSpec(':module/:controller/:action.:suffix')
                         //->setViewScriptPathNoControllerSpec(':action.:suffix')
                         //;
        Zend_Controller_Action_HelperBroker::addHelper($this->viewRenderer);
    }
    
    /**
     * @return Zend_Controller_Action_Helper_ViewRenderer
     */
    function getViewRenderer(){
        return $this->viewRenderer;
    }
}

/**
 * Extension of Twig_Adapter_Zend that overrides certain behaviours, such as:
 * 
 *  - Honouring paths already set by Twig_Loader_Filesystem. The original 
 *    behaviour is to reset it.
 * 
 * @author yclian
 * @since 2.10.20101027
 */
class Aflexi_Common_Mvc_ZendTwig_View extends Twig_Adapter_Zend{

    /**
     * Override to honour paths provided by loader. 
     * 
     * @see Twig_Adapter_Zend::setScriptPath()
     */
    function setScriptPath($path){
        
        if(is_string($path)){
            $path = array($path);
        }
        
        // NOTE [yclian 20101027] We know that loader is definitely 
        // Twig_Loader_Filesystem.
        $existingPaths = $this->_twig->getLoader()->getPaths();
        if(empty($existingPaths)){
            $existingPaths = array();
        }
        
        // $path shall be given higher precendence over base paths. And the 
        // greater index it is, the higher precendence (thus reversed loop).
        for($i = sizeof($path) - 1; $i >= 0; $i--){
            array_unshift($existingPaths, $path[$i]);
        }
        $path = $existingPaths;
        
        return parent::setScriptPath($path);
        
    }
}

?>