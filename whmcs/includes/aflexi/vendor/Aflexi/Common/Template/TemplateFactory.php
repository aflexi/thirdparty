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
 
# namespace Aflexi\Common\Template;

/**
 * Factory class used for bootstrapping the templating component, registering 
 * its components and providing accessors to them.
 * 
 * @author yclian
 * @since 2.5
 * @version 2.5.20100603
 */
class Aflexi_Common_Template_TemplateFactory{
    
    /**
     * @var Aflexi_Common_Template_TemplateFactory
     */
    private static $instance;
    
    /**
     * Render template (of given name or path).
     * 
     * @param string $template
     * @param array $context
     * @return string
     */
    function renderTemplate($template, array $context = array()){
        return self::$instance->renderTemplate($template, $context);
    }
    
    /**
     * @return Aflexi_Common_Template_TemplateFactory
     */
    static function getInstance(){
        return self::$instance;
    }
    
    static function setInstance(Aflexi_Common_Template_TemplateFactory $instance){
        self::$instance = $instance;
    }
}

?>