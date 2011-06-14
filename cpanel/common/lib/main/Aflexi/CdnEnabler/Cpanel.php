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
 
# namespace Aflexi\CdnEnabler;

set_include_path(
    realpath(dirname(__FILE__).'/../..').':'.
    realpath(dirname(__FILE__).'/../../../vendor').':'.
    get_include_path()
);

require_once 'Aflexi/CdnEnabler.php';

/**
 * Bootstrap file for cPanel CDN Enabler. It handles the basic such as 
 * CdnEnabler bootstrap, include paths, etc.
 *  
 * Role-specific bootstrap (including loggers) is handled in either of:
 * 
 *  - Aflexi_CdnEnabler_Cpanel_Operator
 *  - Aflexi_CdnEnabler_Cpanel_Publisher
 * 
 * @author yclian
 * @since 2.8.20100909
 * @version 2.8.20100909
 */
class Aflexi_CdnEnabler_Cpanel extends Aflexi_CdnEnabler{
    
    protected function doPrepare(){
        
        parent::doPrepare();
        
        $this->registerClassPaths();
        $this->registerConstants();
    }
    
    private function registerClassPaths(){
        require_once 'Aflexi/Autoloader.php';
        Aflexi_Autoloader::setClassPaths(realpath(dirname(__FILE__).'/../..'));
    }
    
    private function registerConstants(){
        
        $rootPath = Aflexi_CdnEnabler_Cpanel_Utils::getRootPath();
        
        self::registerConstant('CPANEL_HOME', "$rootPath/usr/local/cpanel");
        self::registerConstant('CPANEL_DATA', "$rootPath/var/cpanel");
        
        self::registerConstant('CPANEL_AFX_HOME', CPANEL_HOME.'/3rdparty/aflexi');
        self::registerConstant('CPANEL_AFX_LIB', CPANEL_AFX_HOME.'/lib/main');
        self::registerConstant('CPANEL_AFX_DATA', CPANEL_DATA.'/aflexi');
    }
    
    /**
     * Define a constant, support values predefined in environment variables.
     * 
     * @param string $key
     * @param string $default
     */
    protected function registerConstant($key, $default){
        if(!isset($_ENV[$key])){
            $_ENV[$key] = $default;
        }
        define($key, $_ENV[$key]);
    }
}

?>
