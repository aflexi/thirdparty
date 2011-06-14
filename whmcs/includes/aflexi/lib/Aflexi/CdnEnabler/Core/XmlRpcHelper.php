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
 
/**
 * 
 * Enter description here ...
 * @author yingfan
 *
 */
abstract class Aflexi_CdnEnabler_Core_XmlRpcHelper {
    protected $xmlRpcClient;
    protected $userName;
    protected $authKey;
    protected $operator;
    
    function __construct($userName = '', $authKey = '', $xmlRpcClient = NULL) {
        $this->initialize($userName, $authKey, $xmlRpcClient);
    }
    
    function initialize($userName = '', $authKey = '', $xmlRpcClient = NULL) {
        $this->setXmlRpcClient($xmlRpcClient);
        
        $this->userName = $userName;
        $this->authKey = $authKey;
        
        $user = $this->xmlRpcClient->execute('user.get', array(
            $userName,
            $authKey,
            array(
                'self' => TRUE
            )
        ));
        
        if (!empty($user['results'])) {
            $this->operator = (object) $user['results'][0];
        }
    }
    
    function setXmlRpcClient($xmlRpcClient = NULL) {
        if (is_null($xmlRpcClient)) {
            $xmlRpcClient = afx_xmlrpc_client();
        }
        $this->xmlRpcClient = $xmlRpcClient;
    }
    
    function getUserName() {
        return $this->userName;
    }
    
    function getAuthKey() {
        return $this->authKey;
    }
    
    function getOperator() {
        return $this->operator;
    }
}
?>