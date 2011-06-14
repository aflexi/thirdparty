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
 
/**
 * Helper methods supporting the internal classes.
 * 
 * @author yclian
 * @since 2.8.20100922
 * @version 2.9.20101020
 */
final class Aflexi_CdnEnabler_Cpanel_Utils{
    
    /**
     * System root path.
     * 
     * @var string
     */
    private static $rootPath = NULL;
    
    /**
     * Get the runtime user, from $_ENV['REMOTE_USER'].
     * 
     * @return string|NULL
     */
    static function getUserName(){
        return isset($_ENV['REMOTE_USER']) ? $_ENV['REMOTE_USER'] : NULL;
    }

    
    /**
     * Get the user role, i.e. OPERATOR or PUBLISHER. A cPanel CDN Enabler 
     * instance has to run under one of these roles.
     * 
     * @global $cpanel_user_role To be used by tests, as constant can never be
     * 	disposed in the same runtime (test suite) with multiple tests.
     * @uses CPANEL_USER_ROLE
     * @return string|NULL
     */
    static function getUserRole(){
        
        global $cpanel_user_role;
        
        if(isset($cpanel_user_role)){
            return $cpanel_user_role;
        }
        if(defined('CPANEL_USER_ROLE')){
            return CPANEL_USER_ROLE;
        }
        
        return NULL;
    }
    
    /**
     * Get the root path, i.e. {/path/to/sandbox}/usr/local/cpanel/path/to/this/script.
     * 
     * @return string Filesystem root path, if we are not under a sandbox 
     *  an empty string is returned.
     */
    static function getRootPath(){
        
        if(!self::$rootPath){
            $matches = array();
            preg_match('#(.*)/usr/local/cpanel#', dirname(__FILE__), $matches);
            self::$rootPath = isset($matches[1]) ? $matches[1] : '';
        }
        
        return self::$rootPath;
    }
    
    /**
     * Check if the current user has the given access.
     * 
     * @param $access
     * @return bool
     */
    static function hasAccess($access = 'all'){
        $rt = Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript(
            'acl_has_access.pl', 
            <<<JSON
{
    "privilege": "$access"
}
JSON
        );
        return $rt['result'] == 1;
    }
    
    /**
     * Check if the current user has root access.
     * 
     * @return bool
     */
    static function hasRoot(){
        $rt = Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript(
            'acl_has_root.pl'
        );
        return $rt['result'] == 1;
    }
    
    /**
     * Set the system root path. Typically used by tests.
     * 
     * @param string $rootPath
     */
    static function setRootPath($rootPath){
        self::$rootPath = $rootPath;
    }
}

?>