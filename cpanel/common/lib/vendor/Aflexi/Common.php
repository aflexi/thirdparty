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

require_once 'Aflexi/Common/Application/AbstractBootstrapper.php';
 
/**
 * Bootstrap class to be imported to use this library.
 * 
 * @author yclian
 * @since 2.7
 * @version 2.8.20100929
 */
final class Aflexi_Common extends Aflexi_Common_Application_AbstractBootstrapper{
    
    /**
     * Convenient method to bootstrap (prepare and boot) the Aflexi_Common.
     * 
     * @return Aflexi_Common
     */
    static function bootstrap(){
        $instance = new Aflexi_Common();
        $instance->prepare()->boot();
        return $instance;
    }
    
    protected function doPrepare(){
        // $this->registerIncludePaths();
        $this->registerAutoloader();
    }
    
    protected function doBoot(){
        // Nothing to do.
    }
    
    private function registerIncludePaths(){
        set_include_path(
            realpath(dirname(__FILE__).'/..').PATH_SEPARATOR.
            realpath(dirname(__FILE__).'/../../vendor').PATH_SEPARATOR.
            get_include_path()
        );
    }
    
    private function registerAutoloader(){
        require_once dirname(__FILE__).'/Autoloader.php';
        Aflexi_Autoloader::register();
    }
}

Aflexi_Common::bootstrap();
