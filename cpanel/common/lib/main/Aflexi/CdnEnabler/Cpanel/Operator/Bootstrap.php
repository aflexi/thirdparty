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
 
require_once dirname(__FILE__).'/../../Cpanel.php';

/**
 * Bootstrap for operators, handle also domain logics specific to them.
 * 
 * @author yclian
 * @since 2.8
 * @version 2.8.20100929
 */
final class Aflexi_CdnEnabler_Cpanel_Operator_Bootstrap extends Aflexi_CdnEnabler_Cpanel{
    
    /**
     * @var Aflexi_CdnEnabler_Cpanel_Container
     */
    private $container;
    
    protected function doPrepare(){
        parent::doPrepare();
        $this->registerConstant('CPANEL_USER_ROLE', 'OPERATOR');
    }
    
    protected function doBoot(){
        parent::doBoot();
        $this->registerContainer();
        $this->loadConfig();
    }
    
    private function registerContainer(){
        $this->container = Aflexi_CdnEnabler_Cpanel_Container::getInstance();
    }
    
    private function loadConfig(){
        
        $config = $this->container->getConfig();
        $xmlRpcClient = $this->container->getXmlRpcClient();
        
        if(isset($config['operator'])){
            
            $operator = NULL;
        
            try{
                $operator = $xmlRpcClient->execute('user.getByUsername', array(
                    @$config['operator']['auth']['username'],
                    @$config['operator']['auth']['key'],
                    @$config['operator']['auth']['username'],
                ));
            } catch(Aflexi_Common_Net_XmlRpc_Exception $xre){
                throw new Aflexi_CdnEnabler_Cpanel_Exception("Operator unauthorized or it does not exist");
            }
            
            $config->setRuntime('operator', $operator);
        }
    }
}

?>
