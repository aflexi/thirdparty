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
 
class errorController extends Aflexi_Common_Mvc_Zend_Action{

    function errorAction(){
        $this->logger->isErrorEnabled() && $this->logger->error("Detected error while processing action '{$this->getRequest()->getActionName()}': {$this->getFirstErrorMessage()}");
    }
    
    private function getFirstErrorMessage(){
        
        $rt = "UNKNOWN";
        
        if($this->getResponse()->isException()){
            $rt = $this->getResponse()->getException();
            if(!empty($rt)){
                $rt = get_class($rt[0]).": {$rt[0]->getMessage()}";
            }
        }
        
        return $rt;
    }
}

?>