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
 
# namespace Aflexi\Common\Template\Twig;

/**
 * String extension for Twig.
 * 
 * Filter extensions:
 *  - match - Perform regex-based matching.
 * 
 * @author yclian
 * @since 2.5
 * @version 2.5.20100607
 */
class Aflexi_Common_Template_Twig_StringExtension extends Twig_Extension{
    
    function getFilters(){
        return array(
            'match' => new Twig_Filter_Function('Aflexi_Common_Template_Twig_StringExtension::filterMatch')
        );
    }
    
    function getName(){
        return 'string';
    }
    
    static function filterMatch($string, $regex){
        return preg_match($regex, $string) > 0;
    }
}

?>