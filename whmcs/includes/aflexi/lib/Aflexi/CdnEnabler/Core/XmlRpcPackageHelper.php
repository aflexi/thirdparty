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
class Aflexi_CdnEnabler_Core_XmlRpcPackageHelper extends Aflexi_CdnEnabler_Core_XmlRpcHelper implements Aflexi_CdnEnabler_Core_PackageHelper {

    function getPackage($id) {
        
    }

    function getPackages($cdnEnabledOnly = FALSE) {
        $results;
        $rt;
        
        $results = $this->xmlRpcClient->execute('bandwidthPackage.get', array(
            $this->getUserName(),
            $this->getAuthKey(),
            array(
                'user' => $this->getUserName()
            )
        ));
        $rt = array();
        
        $results = $results['results'];
        foreach($results as $package){
            $rt[$package['name']] = $package;
        }
        return $rt;
    }

    function createPackage($name = ''){
        $name = empty($name) ? 'default' : $name;
        
        return $this->xmlRpcClient->execute('bandwidthPackage.create', array(
            $this->getUserName(),
            $this->getAuthKey(),
            array(
                'name' => $name,
                'user' => array(
                    'id' => $this->getOperator()->id
                ),
                'dedicatedBandwidth' => FALSE,
                'bandwidthLimit' => -1,
                'diskSpaceLimit' => 999999,
                'speedLimit' => 9999,
                'resourceLimit' => 99,
                'streamingAllowed' => FALSE,
                'sslEnabled' => FALSE,
                'servingAllLocations' => TRUE,
                'type' => 'PUBLISHER_LINK',
            )
        ));
    }
    
    function updatePackage($id, $name) {
        return $this->xmlRpcClient->execute('bandwidthPackage.update', array(
            $this->getUserName(),
            $this->getAuthKey(),
            $id,
            array(
                'name' => $name
            )
        ));
    }
    
    function getCdnPackage($id) {
        
    }

    function getCdnPackages() {
        
    }

    function isCdnEnabled($id) {
        
    }

    function setCdnEnabled($id, $enabled = TRUE) {
        
    }

    /**
     * Handle a package when it is being updated, e.g. being renamed, CDN
     * feature disabled, etc.
     *
     * @param $before
     * @param $after
     */
    function handlePackageChanged($before, $after) {
        
    }
}

?>
