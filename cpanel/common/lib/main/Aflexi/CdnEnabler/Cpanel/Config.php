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
 
# namespace Aflexi\CdnEnabler\Cpanel;

/**
 * Config files loader.
 * 
 * NOTE [yclian 20101020] Write notes on lazy load and session.
 * 
 * @uses CPANEL_AFX_DATA
 * @uses CPANEL_USER_ROLE
 * @author yclian
 * @since 2.8.20100917
 * @version 2.9.20101019
 */
class Aflexi_CdnEnabler_Cpanel_Config extends Aflexi_CdnEnabler_Config{
    
    protected function getChildConfigMappings(){
        $mappings = parent::getChildConfigMappings();
        // Share YamlConfig reference, any will do.
        $mappings['sandbox'] = $mappings[self::NAMESPACE_GLOBAL];
        return $mappings;
    }
    
    function configure(){
        parent::configure();
        $this->registerSources();
    }
    
    /**
     * Overriding to handle some extra pre-processing on certain configs.
     * 
     * @since 2.9.20101019
     */
    function read($source, $namespace = self::DEFAULT_NAMESPACE, $merge = TRUE){
        
        if($namespace == self::NAMESPACE_RUNTIME){
            if(!isset($_SESSION[__CLASS__])){
                $this->preReadRuntimeConfig();
            }
        }
        
        parent::read($source, $namespace, $merge);
    }
    
    function write($destination, $namespace = self::DEFAULT_NAMESPACE){
        
        parent::write($destination, $namespace);
    
        if($namespace == self::NAMESPACE_RUNTIME){
            if(isset($_SESSION[__CLASS__])){
                $this->postWriteRuntimeConfig();
            }
        }
    }
    
    /**
     * We support the following sources:
     * 
     * 	- runtime - $_SESSION[__CLASS__], i.e. $_SESSION['Aflexi_CdnEnabler_Cpanel_Config'].
     * 		- preferences - array()
     *  - global
     *  - operator
     *  - publisher
     */
    private function registerSources(){
        
        $this->sources[self::NAMESPACE_RUNTIME] = __CLASS__;
        $this->sources[self::NAMESPACE_GLOBAL] = CPANEL_AFX_DATA.'/config.yml';
        $this->readGlobalConfig();
        $this->readSandboxConfig();
        
        switch(Aflexi_CdnEnabler_Cpanel_Utils::getUserRole()){
            case 'OPERATOR':{
                $this->sources[self::NAMESPACE_OPERATOR] = CPANEL_AFX_DATA.'/operator/config.yml';
                break;
            }
            case 'PUBLISHER':{
                $userName = Aflexi_CdnEnabler_Cpanel_Utils::getUserName();
                $this->sources[self::NAMESPACE_PUBLISHER] = "/home/{$userName}/.cdn";
                break;
            }
            default:{
                // By right, we should throw an exception here. But am simply 
                // lazy to think about what'll happen to tests matching either 
                // cases above.
            }
        }
    }
    
    private function readGlobalConfig(){
        $this->read($this->sources[self::NAMESPACE_GLOBAL], self::NAMESPACE_GLOBAL);
    }
    
    private function readSandboxConfig(){
        
        // Load sandbox values, later gotta destroy them.
        $this->read(CPANEL_AFX_DATA."/sandbox.yml", 'sandbox');
        
        // Merge with global. Make sure you are pulling the REAL arrays.
        $this->configs[self::NAMESPACE_GLOBAL][self::NAMESPACE_GLOBAL] = array_merge(
            $this->configs[self::NAMESPACE_GLOBAL][self::NAMESPACE_GLOBAL],
            $this->configs['sandbox']['sandbox']
        );
        
        // User mocking
        if($user = $this->get('user', NULL, self::NAMESPACE_GLOBAL)){
            switch(Aflexi_CdnEnabler_Cpanel_Utils::getUserRole()){
                case 'OPERATOR':{
                    $_ENV['REMOTE_USER'] = $user['operator'];
                    break;
                }
                case 'PUBLISHER':{
                    $_ENV['REMOTE_USER'] = $user['publisher'];
                    break;
                }
            }
        }
        
        // Destroy
        // NOTE [yclian 20101019] Low level call with $configs[$e]->configs[$e],
        // otherwise won't work.
        unset($this->configs[self::NAMESPACE_GLOBAL]->configs[self::NAMESPACE_GLOBAL]['user']);
        unset($this->configs['sandbox']->configs['sandbox']);
        unset($this->configs['sandbox']);
    }
    
    /**
     * Invoked before runtime config is being read. This function initializes 
     * certain values in the $_SESSION variable.
     */
    private function preReadRuntimeConfig(){
    
        switch(Aflexi_CdnEnabler_Cpanel_Utils::getUserRole()){
            case 'OPERATOR':{
                $prefs = $this->getXmlRpcClient()->execute('prefs.get', array(
                    // This enables lazy load for operator config.
                    $this['operator']['auth']['username'],
                    $this['operator']['auth']['key'],
                ));
                $_SESSION[__CLASS__]['prefs'] = $prefs;
                break;
            }
            default:{
            }
        }
    }
    
    /**
     * Invoked after runtime config is being written. This function flushes 
     * the session values to designated persistent targets.
     */
    private function postWriteRuntimeConfig(){
        
        switch(Aflexi_CdnEnabler_Cpanel_Utils::getUserRole()){
            case 'OPERATOR':{
                
                $prefs;
                
                $prefs = $_SESSION[__CLASS__]['prefs'];
                
                // We will only write if prefs is NOT empty. So what is we want 
                // to remove? We are handling existing or new configs only.
                if(!empty($prefs)){
                    $this->getXmlRpcClient()->execute('prefs.set', array(
                        $this['operator']['auth']['username'],
                        $this['operator']['auth']['key'],
                        $prefs
                    ));
                }
                break;
            }
            default:{
            }
        }
    }
    
    /**
     * XML-RPC client used internally for reading Aflexi's data. Note that, 
     * this function shall only be called after 'global' and 'operator' config 
     * have been loaded.
     * 
     * @return Aflexi_Common_Net_XmlRpc_Client
     */
    private function getXmlRpcClient(){
        
        /*assert(
            in_array(self::NAMESPACE_GLOBAL, $this->loaded) &&
            in_array(self::NAMESPACE_OPERATOR, $this->loaded)
        );*/
        $rt;
        
        $uri = $this[self::NAMESPACE_GLOBAL]['xmlrpc']['uri']['core'];
        $rt = new Aflexi_Common_Net_XmlRpc_Client($uri);
        return $rt;
    }
}

?>