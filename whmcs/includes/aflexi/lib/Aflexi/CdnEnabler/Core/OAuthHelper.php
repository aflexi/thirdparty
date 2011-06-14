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
 require_once dirname(__FILE__) . '/../../../../vendor/OAuth/OAuthStore.php';
require_once dirname(__FILE__) . '/../../../../vendor/OAuth/OAuthRequester.php';

class Aflexi_CdnEnabler_Core_OAuthHelper {
    static function register($username, $authkey, $portalUrl, $appType = 'standard', $callBackUrl = '') {
        $ch = curl_init($portalUrl);
        $post = array(
            'generate' => 'yespl0x1',
            'oauth_application_title' => 'whmcs',
            'oauth_application_type' => $appType,
            'application_uri' => $callBackUrl,
            'auth_username' => $username,
            'auth_secret' =>  $authkey
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($ch);

        $result = @json_decode($result, TRUE);
        if (is_null($result)) {
            $result =  array();
        }

        return $result;
    }

    static function request($consumer_key, $consumer_secret, $portal_url) {
        $store = OAuthStore::instance("2Leg", array(
            'consumer_key' => $consumer_key,
            'consumer_secret' => $consumer_secret
        ));
        $request = new OAuthRequester(
            $portal_url,
            'POST',
            NULL
        );
        $response = NULL;

        try{
            $response = $request->doRequest();
        } catch(OAuthException2 $oae){
        }
        
        if($response && $response['code'] == 200){
            $response_body = array();
            parse_str($response['body'], $response_body);
            return $response_body['oauth_token'];
        } else{
            throw new RuntimeException("Could not request for OAuth token due to error. Check configuration or connection.");
        }
    }
}
?>