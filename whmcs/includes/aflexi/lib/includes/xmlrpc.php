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
 * Collection of reusable XML-RPC functions.
 * 
 * @author yclian
 * @since 2.5
 * @version 2.5.20100729
 */

/**
 * Create a new XML-RPC client instance.
 * 
 * @global $afx_config_main Used if $url is unspecified.
 * @param string $url[optional] If NULL is specified, the default core service URL will be used.
 * @return AfxCpanel_Util_XmlRpcClient
 */
function afx_xmlrpc_client($url = NULL){
    
    global $afx_config_main;
    
    if(is_null($url)){
        $url = $afx_config_main['xmlrpc']['uri']['core'];
    }
    
    return new Aflexi_Common_Net_XmlRpcClient($url);
}

/**
 * Create a publisher. Called by an operator.
 *
 * @global $afx_config_whm
 * @global $afx_xmlrpc
 * @param string $email
 * @param string $name
 * @param string $password
 * @param id $packageId
 * @return array
 */
function afx_xmlrpc_create_publisher($email, $name, $password, $packageId){
    
    global $afx_config_whm;
    global $afx_xmlrpc;
        
    return $afx_xmlrpc->execute('user.create', array(
        @$afx_config_whm['auth']['username'],
        @$afx_config_whm['auth']['key'],
        array(
            'role' => 'PUBLISHER',
            'email' => $email,
            'name' => $name,
            'password' => $password,
            'status' => 'ACTIVE'
        ),
        array(
            'id' => $packageId
        )
    ));
}

/**
 * Fetch the operator as defined in $afx_config_whm.
 * 
 * @global $afx_xmlrpc
 * @global $afx_config_whm
 * @return array
 */
function afx_xmlrpc_get_operator(){
    
    global $afx_config_whm;
    global $afx_xmlrpc;
    
    return $afx_xmlrpc->execute('user.getByUsername', array(
        @$afx_config_whm['auth']['username'],
        @$afx_config_whm['auth']['key'],
        @$afx_config_whm['auth']['username']
    ));
}

/**
 * Get publisher links associated with operator.
 *
 * @global $afx_config_whm
 * @global $afx_xmlrpc
 * @global $afx_operator
 * @return array
 */
function afx_xmlrpc_get_publishers(){
    
    global $afx_config_whm;
    global $afx_xmlrpc;
    global $afx_operator;
    
    $results;
    $rt = array();
    
    $results = $afx_xmlrpc->execute('publisherLink.get', array(
        @$afx_config_whm['auth']['username'],
        @$afx_config_whm['auth']['key'],
        array(
            'operator' => (int) $afx_operator['id'],
            'status' => 'ACTIVE'
        ),
        array(
            'disjunction' => FALSE
        )
    ));
    
    $results = $results['results'];
    foreach($results as $publisher){
        // NOTE [yclian 20100719] The WS is not returning 'username' yet, as of
        // 2.6.
        $publisher['publisher']['username'] = "{$publisher['publisher']['email']}/{$publisher['publisher']['agent']['id']}";
        // NOTE [yclian 20100728] We map by his cpanel's username.
        $rt[$publisher['publisher']['name']] = $publisher['publisher'];
    }
    return $rt;
}

/**
 * Create a package with some default/unlimited values.
 *
 * @global $afx_config_whm
 * @global $afx_xmlrpc
 * @global $afx_operator
 * @author yclian
 * @since 2.5
 * @version 2.5.20100612
 */
function afx_xmlrpc_create_package($name){
    
    global $afx_config_whm;
    global $afx_xmlrpc;
    global $afx_operator;
    
    return $afx_xmlrpc->execute('bandwidthPackage.create', array(
        @$afx_config_whm['auth']['username'],
        @$afx_config_whm['auth']['key'],
        array(
            'name' => $name,
            'user' => array(
                'id' => $afx_operator['id']
            ),
            'dedicatedBandwidth' => FALSE,
            'bandwidthLimit' => 0,
            'diskSpaceLimit' => 0,
            'speedLimit' => 0,
            'resourceLimit' => 99,
            'streamingAllowed' => FALSE,
            'sslEnabled' => FALSE,
            'servingAllLocations' => TRUE,
            'type' => 'PUBLISHER_LINK',
        )
    ));
}

/**
 * Fetch packages and features. Refer to package-list.pl.
 *
 * @author yclian
 * @since 2.5
 * @version 2.5.20100612
 */
function afx_xmlrpc_get_packages(){
    
    global $afx_config_whm;
    global $afx_xmlrpc;
    
    $results;
    $rt;
    
    $results = $afx_xmlrpc->execute('bandwidthPackage.get', array(
        @$afx_config_whm['auth']['username'],
        @$afx_config_whm['auth']['key'],
        array(
            'user' => @$afx_config_whm['auth']['username']
        )
    ));
    $rt = array();
    
    $results = $results['results'];
    foreach($results as $package){
        $rt[$package['name']] = $package;
    }
    return $rt;
}

function afx_xmlrpc_get_prefs_publisher(){
    
    global $afx_xmlrpc;
    global $afx_config_whm;
    global $afx_operator;
    global $afx_publisher;
        
    $rt = $afx_xmlrpc->execute('userPreference.get', array(
        $afx_operator['email'].'?su='.$afx_publisher['email'].'/'.$afx_operator['id'],
        @$afx_config_whm['auth']['key'],
        array(
            'user' => $afx_publisher['id']
        )
    ));
    
    return $rt;
}

function afx_xmlrpc_set_prefs_publisher(array $preferences){
    
    global $afx_xmlrpc;
    global $afx_config_whm;
    global $afx_operator;
    global $afx_publisher;
    
    foreach($preferences as $k => $v){
        $afx_xmlrpc->execute('userPreference.createOrUpdate', array(
            $afx_operator['email'].'?su='.$afx_publisher['email'].'/'.$afx_operator['id'],
            @$afx_config_whm['auth']['key'],
            array(
                'key' => $k,
                'value' => $v,
                'user' => array(
                    'id' => $afx_publisher['id']
                )
            )
        ));
    }
}

?>