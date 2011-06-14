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
 
# namespace Aflexi\Common\Lang;

/**
 * Collection of string utility methods.
 * 
 * @author yclian
 * @since 2.6
 * @version 2.6.20100716
 */
final class Aflexi_Common_Lang_Strings{
    
    /**
     * Returns a string with the first character of str, lowercased.
     * 
     * @param string $str
     * @return string
     */
    static function toLowerCaseFirstCharacter($str){
        return strtolower(substr($str, 0, 1)).substr($str, 1);
    }
}

if(!function_exists('lcfirst')){
    
    /**
     * @see Aflexi_Common_Lang_Strings#toLowerCaseFirstCharacter()
     * @param string $str
     * @return string     
     */
    function lcfirst($str){
        return Aflexi_Common_Lang_Strings::toLowerCaseFirstCharacter($str);
    }
}

?>