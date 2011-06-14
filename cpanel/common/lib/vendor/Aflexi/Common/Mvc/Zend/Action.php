<?php

/**
 * Extended base action of Zend_Controller_Action.
 * 
 * It performs additional functions such as logging during action dispatch. It
 * also separates what extending *base* classes (e.g. preDispatch()) and action 
 * classes shall override (e.g. doPreDispatch()).
 * 
 * Given that if view exists, the following template variables will be auto-
 * populated:
 * 
 * 	- action
 * 	- request
 *  - error (if there's an exception)
 * 
 * @author yclian
 * @since 2.9.20101020
 * @version 2.9.20101020
 */
class Aflexi_Common_Mvc_Zend_Action extends Zend_Controller_Action{
    
    /**
     * @var Aflexi_Common_Log_Logger
     */
    protected $logger = NULL;
    
    /**
     * @var float
     */
    private $timeStarted;
    
    function preDispatch(){
        
        $class = get_class($this);
        
        $this->timeStarted = microtime(TRUE);
        $this->logger = Aflexi_Common_Log_LoggerFactory::getLogger($class);
        $this->logger->isDebugEnabled() && $this->logger->debug("Dispatching action '{$this->getRequest()->getActionName()}' ($class)..");
        
        $this->safeViewAssign('action', $this);
        $this->safeViewAssign('request', $this->getRequest());
        
        $this->doPreDispatch();
    }
    
    /**
     * Template method to be overriden. DO NOT override preDispatch()!
     */
    protected function doPreDispatch(){}
    
    function postDispatch(){
        
        $this->doPostDispatch();
        
        $class = get_class($this);
        
        if($this->getResponse()->isException()){
            if(!empty($this->view)){
                // Default template vars, if there're errors.
                $this->safeViewAssign('errors', $this->getResponse()->getException());
            }
        }
        
        $timeElapsed = round((microtime(TRUE) - $this->timeStarted) * 1000, 4);
        $this->logger->isDebugEnabled() && $this->logger->debug("Dispatched action '{$this->getRequest()->getActionName()}' ($class) within {$timeElapsed}ms");
    }
    
    /**
     * NOTE [yclian 20101020] Am unsure if there's a case that $this->view 
     * is empty, so, we will do an empty check.
     */
    private function safeViewAssign($spec, $value = null){
        if(!empty($this->view)){
            // Default template vars, if there're errors.
            $this->view->assign($spec, $value);
        }
    }
    
    /**
     * Template method to be overriden. DO NOT override postDispatch()!
     */
    protected function doPostDispatch(){}
}