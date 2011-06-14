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
 * Debugging related methods.
 * 
 * @author yclian
 * @since 2.5
 * @version 2.5.20100625
 */
final class Aflexi_Common_Lang_DebugUtils{

    /**
     * Get the name of the calling function, you can specify offset to navigate
     * the call tree.
     * 
     * @param int $offset Offset to navigate the call tree. -1 for callee, 
     */
    static function getCallingFunction($offset = 0){

        $trace;
        $caller = NULL;

        $trace = debug_backtrace(FALSE);
        if(sizeof($trace) > 2 + $offset){
            $caller = $trace[2 + $offset];
        }
        
        if(is_null($caller)){
            return NULL;
        } else if(array_key_exists('class', $caller)){            
            // TODO [yclian 20100625] Shall detect ReflectionMethod->invoke. If 
            // it is, then we shall move the offset until we find the right method.
            return "{$caller['class']}{$caller['type']}{$caller['function']}";
        } else{
            return $caller['function'];
        }
    }
}

?>