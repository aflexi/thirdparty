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
 class Aflexi_CdnEnabler_Cpanel_SecurityHelper {
    function hasRoot() {
        if(!$this->_hasRoot()){
            throw new Aflexi_Common_Security_AuthorizationException();
        }
        return TRUE;
    }
    
    protected function _hasRoot($user = NULL){
        return Aflexi_CdnEnabler_Cpanel_Utils::hasRoot();
    }
}
?>
