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
 * User helper for Whmcs
 * @author yingfan
 *
 */
class Aflexi_CdnEnabler_Core_XmlRpcUserHelper extends Aflexi_CdnEnabler_Core_XmlRpcHelper implements Aflexi_CdnEnabler_Core_UserHelper {
    function getUser($id) {
        
    }

    function getUsers($cdnEnabledOnly = FALSE) {
        $results = array();
        $rt = array();
        
        $results = $this->xmlRpcClient->execute('publisherLink.get', array(
            $this->getUserName(),
            $this->getAuthKey(),
            array(
                'operator' => (int) $this->getOperator()->id
            ),
            array(
                'disjunction' => FALSE
            )
        ));
        
        $results = $results['results'];
        foreach($results as $publisher) {
            $publisher['publisher']['publisherLink'] = "{$publisher['publisher']['id']},{$publisher['operator']['id']}";
            $publisher['publisher']['publisherLinkStatus'] = $publisher['status'];
            // NOTE [yclian 20100728] We map by his cpanel's username.
            $rt[$publisher['publisher']['username']] = $publisher['publisher'];
        }
        return $rt;
    }
    
    function createUser($email, $name, $password, $packageId, $username='') {
        return $this->xmlRpcClient->execute('user.create', array(
            $this->getUserName(),
            $this->getAuthKey(),
            array(
                'role' => 'PUBLISHER',
                'email' => $email,
                'name' => $name,
                'username' => $username,
                'password' => $password,
//                'sendEmail' => TRUE,
//                'includePassword' => TRUE,
                'status' => 'ACTIVE'
            ),
            array(
                'id' => $packageId
            )
        ));
    }
    
    function suspendUser($publisherLinkId) {
        list($publisherId, $operatorId) = explode(',', $publisherLinkId);
        $publisherLink = array(
            'publisher' => array('id' => (int) $publisherId),
            'operator' => array('id' => (int) $operatorId),
            'status' => 'SUSPENDED'
        );
        
        return $this->xmlRpcClient->execute('publisherLink.update', array(
            $this->getUserName(),
            $this->getAuthKey(),
            $publisherLinkId,
            $publisherLink
        ));
    }

    function activateUser($publisherLinkId) {
        list($publisherId, $operatorId) = explode(',', $publisherLinkId);
        $publisherLink = array(
            'publisher' => array('id' => (int) $publisherId),
            'operator' => array('id' => (int) $operatorId),
            'status' => 'ACTIVE'
        );
        
        return $this->xmlRpcClient->execute('publisherLink.update', array(
            $this->getUserName(),
            $this->getAuthKey(),
            $publisherLinkId,
            $publisherLink
        ));
    }
    
    function updatePublisher($publisherLinkId, $packageId) {
        list($publisherId, $operatorId) = explode(',', $publisherLinkId);
        $publisherLink = array(
            'publisher' => array('id' => (int) $publisherId),
            'operator' => array('id' => (int) $operatorId),
            'bandwidthPackage' => array(
                'id' => (int) $packageId,
                'user' => array('id' => (int) $publisherId),
                'type' => 'PUBLISHER_LINK'
            )
        );
        
        return $this->xmlRpcClient->execute('publisherLink.update', array(
            $this->getUserName(),
            $this->getAuthKey(),
            $publisherLinkId,
            $publisherLink
        ));
    }

    function resetPassword($userId, $oldPassword, $newPassword) {
        return $this->xmlRpcClient->execute('security.update', array(
            $this->getUserName(),
            $this->getAuthKey(),
            (int) $userId,
            $oldPassword,
            $newPassword
        ));
    }

    function deletePublisherLink($publisherId, $operatorId) {
        return $this->xmlRpcClient->execute('publisherLink.delete', array(
            $this->getUserName(),
            $this->getAuthKey(),
            "{$publisherId},{$operatorId}",
        ));
    }

    function getThemability() {
        $result = $this->xmlRpcClient->execute('themability.get', array(
            $this->getUserName(),
            $this->getAuthKey(),
            array('operator' => (int) $this->operator->id)
        ));
        return @$result['results'][0];
    }

    function getCdnUser($id) {
        
    }

    function getCdnUsers() {
        
    }

    function isCdnEnabled($id) {
        
    }

    function setCdnEnabled($id, $enabled = TRUE) {
        
    }
    
    function handlePackageChanged($id, $package1, $package2) {
        
    }
}
?>