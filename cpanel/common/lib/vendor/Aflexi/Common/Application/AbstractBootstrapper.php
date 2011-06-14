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
 
require_once 'Aflexi/Common/Application/Bootstrapper.php';

/**
 * An abstract implementation of Aflexi_Common_Application_Bootstrapper.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.8.20100929
 */
abstract class Aflexi_Common_Application_AbstractBootstrapper implements Aflexi_Common_Application_Bootstrapper{
    
    private $prepared;
    private $booted;
    
    function prepare(){
        
        if($this->prepared){
            $this->throwApplicationException('Bootstrapper \''.__CLASS__.'\' has already been prepared');
        }
        
        $this->doPrepare();
        
        $this->prepared = TRUE;
        return $this;
    }
    
    /**
     * @return void
     */
    protected abstract function doPrepare();
    
    function boot(){
        
        if(!$this->prepared){
            $this->throwApplicationException('Bootstrapper \''.__CLASS__.'\' has not yet been prepared');
        }
        
        if($this->booted){
            $this->throwApplicationException('Bootstrapper \''.__CLASS__.'\' has already been booted');
        }
        
        $this->doBoot();
        
        $this->booted = TRUE;
        return $this;
    }
    
    /**
     * @return void
     */
    protected abstract function doBoot();
    
    private function throwApplicationException($message){
        require_once dirname(__FILE__).'/Common/Application/Exception.php';
        throw new Aflexi_Common_Application_Exception($message);
    }
}
