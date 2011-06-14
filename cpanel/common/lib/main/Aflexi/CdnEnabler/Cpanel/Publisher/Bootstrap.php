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
 * @author
 * @since 2.8
 * @version 2.13.20110315
 */
final class Aflexi_CdnEnabler_Cpanel_Publisher_Bootstrap extends Aflexi_CdnEnabler_Cpanel{
    
    /**
     * @var Aflexi_CdnEnabler_Cpanel_Container
     */
    private $container;
    
    protected function doPrepare(){
        parent::doPrepare();
        $this->registerConstant('CPANEL_USER_ROLE', 'PUBLISHER');
    }
    
    protected function doBoot(){
        // NOTE [yasir 20110316] Enable doBoot to enable logger for publisher
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
    }
}

?>
