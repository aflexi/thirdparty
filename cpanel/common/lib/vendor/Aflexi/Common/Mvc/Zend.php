<?php

require_once('Zend/Loader/Autoloader.php');
Zend_Loader_Autoloader::getInstance();

/**
 * Basic MVC set-up with Zend's Controller/View/Filter. 
 * 
 * This object acts as a front controller too with its #run() function.
 * 
 * @see http://framework.zend.com/manual/en/zend.controller.modular.html
 * @author yclian
 * @since 2.8
 * @version 2.8.20100921
 */
abstract class Aflexi_Common_Mvc_Zend implements Aflexi_Common_Object_Initializable{
    
    /**
     * @var Zend_Controller_Front
     */
    protected $frontController;
    
    protected $mvcDir = NULL;
    
    /**
     * @return Aflexi_Common_Mvc_Zend
     */
    function initialize(){
        $this->configureFrontController();
        return $this;
    }
    
    protected function configureFrontController(){
        $this->frontController = Zend_Controller_Front::getInstance();
        $this->frontController
            ->throwExceptions(FALSE)
            ->addModuleDirectory($this->mvcDir)
            //->setControllerDirectory("{$this->mvcDir}/default", 'default')
            //->setParam('useDefaultControllerAlways', FALSE);
            ->setDefaultModule('default')
        ;
    }
    
    /**
     * @return Zend_Controller_Front
     */
    function getFrontController(){
        return $this->frontController;
    }
    
    function run(){
        return $this->frontController->dispatch();
    }
}

?>