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

/**
 * Aflexi_Common_Config_Config that uses the PHP session as storage.
 * 
 * @see sfSessionStorage
 * @author yclian
 * @since 2.9
 * @version 2.9.20101018
 */
class Aflexi_Common_Config_SessionConfig extends Aflexi_Common_Config_AbstractConfig{
    
    function __construct($options = array()){
        parent::__construct(array_merge(
            array(
                self::OPTION_USE_SHUTDOWN => TRUE
            ),
            $options
        ));
    }
    
    protected function configure(){
        if(!session_id()){
            if(!@session_start()){
                // TODO [yclian 20101019] Shall we pass silently or throw an 
                // exception if ession can't be started?
            }
        }
    }
    
    function shutdown(){
        @session_write_close();
    }
    
    protected function doRead($source){
        return isset($_SESSION[$source]) ? $_SESSION[$source] : array();
    }
    
    protected function doWrite($destination, array $data){
        $_SESSION[$destination] = $data;
    }
}

?>