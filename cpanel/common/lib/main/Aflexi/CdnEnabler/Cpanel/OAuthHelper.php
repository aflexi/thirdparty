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
 require_once(dirname(__FILE__).'/../../../../vendor/Snoopy/Snoopy.class.php');
require_once dirname(__FILE__) . '/../../../../vendor/OAuth/OAuthStore.php';
require_once dirname(__FILE__) . '/../../../../vendor/OAuth/OAuthRequester.php';

class Aflexi_CdnEnabler_Cpanel_OAuthHelper {
    /**
     * @var Aflexi_CdnEnabler_Cpanel_Config
     */
    private $config;
    
    /**
     * @var Snoopy
     */
    protected $httpClient;

    /**
     * @var Aflexi_Common_Log_Logger
     */
    private static $logger;
    
    static function initializeStatic(){
        self::$logger = Aflexi_Common_Log_LoggerFactory::getLogger(__CLASS__);
    }
    
    
    function __construct() {
    }

    function setConfig(Aflexi_CdnEnabler_Cpanel_Config $config){
        $this->config = $config;
    }
    
    function setHttpClient($httpClient = NULL) {
        if (is_null($httpClient)) {
            $httpClient = new Snoopy();
        }
        $this->httpClient = $httpClient;
    }

    function initialize() {
        $this->setHttpClient();
    }

    /**
     * 
     * Register a user as oAuth consumer.
     * 
     * @since 2.5
     * @version 2.10.20101023
     * @param $app_type
     * @param $su
     * @param $portal_url
     * @return object Response, with attribute id, consumer_key, consumer_secret, user_id, application_*, ...
     * @throws RuntimeException
     */
    function register($app_type = 'standard', $su = NULL, $portal_url = NULL) {

        if(is_null($portal_url)){
            $portal_url = ($app_type == 'delegate') ?
                $this->config['global']['portal']['url']['mini_publisher']: 
                $this->config['global']['portal']['url']['mini_operator'];
        }

        if(isset($this->config['global']['system']['curl'])){
            $this->httpClient->curl_path = $this->config['global']['system']['curl'];
        }

        $callbackType = ($app_type == 'standard') ? 'whm' : 'cpanel';
        $request_params = array(
            'generate' => 'yespl0x1',
            'oauth_application_title' => 'cpanel',
            'oauth_application_type' => $app_type,
            'application_uri' => $this->config['global']['integration']['url'][$callbackType],
            'auth_username' => $this->config['operator']['auth']['username'],
            'auth_secret' =>  $this->config['operator']['auth']['key']
        );

        if(!is_null($su)){
            $request_params['auth_su'] = $su;
            $request_params['auth_su_mode'] = 'locked';
        }

        if(self::$logger->isDebugEnabled()){
            self::$logger->debug("Register OAuth @ {$portal_url}/oauth/register?format=json : ".var_export($request_params, TRUE));
        }
        
        $ok = $this->httpClient->submit("{$portal_url}/oauth/register?format=json", $request_params);

        if($ok && $this->httpClient->status == 200){
            $response = json_decode($this->httpClient->results);
            return $response;
        } else{
            // TODO [yclian 20100703] Can we give better error reporting here?
            throw new RuntimeException("Could not register or decode OAuth consumer due to error. Check configuration or connection.");
        }
    }

    /**
     * Request for an OAuth token.
     * 
     * @param array $oauth_pair Array with 'key' and 'secret' offsets.
     * @param string $portal_url
     * @return object Response, with attribute oauth_callback_accepted, oauth_token, oauth_token_secret, xoauth_token_ttl.
     * @throws RuntimeException
     */
    function request(array $oauth_pair, $portal_url = NULL){
        $store;
        
        if(is_null($portal_url)){
            $portal_url = $this->config['global']['portal']['url']['mini_operator'];
        }
        
        $store = OAuthStore::instance("2Leg", array(
            'consumer_key' => $oauth_pair['key'],
            'consumer_secret' => $oauth_pair['secret']
        ));
        $request = new OAuthRequester(
            "{$portal_url}/oauth/request",
            'POST',
            NULL
        );
        $response = NULL;
        
        try{
            $response = $request->doRequest();
        } catch(OAuthException2 $oae){
            // Allowing this exception thrown to the user may expose secrets.
            self::$logger->error($oae->getMessage());
        }
        if($response && $response['code'] == 200){
            $response_body = array();
            parse_str($response['body'], $response_body);
            return (object) $response_body;
        } else{
            throw new RuntimeException("Could not request for OAuth token due to error. Check configuration or connection.");
        }
    }
    
    /**
     * Render an IFRAME's URL.
     * 
     * @global array $afx_config_main Needed to render the mini app's URL.
     * @param $afx_username Owner of the OAuth credentials.
     * @param $oauth_key
     * @param $oauth_secret
     * @param $path
     * @param $query
     * @return string
     */
    function getIframeUrl($afx_username, $oauth_key, $oauth_secret, $url_path, $url_query = ''){
        $rt;
    
        $oauth_request = $this->request(array(
            'key' => $oauth_key,
            'secret' => $oauth_secret
        ));
        
        $oauth_token = $oauth_request->oauth_token;
        
        $rt = "{$this->config['global']['portal']['url']['mini_operator']}".
            "{$url_path}".
            "?app=cpanel&oauth_token={$oauth_token}&auth_username={$afx_username}".
            "{$url_query}";
        
        return $rt;
    }
}


Aflexi_CdnEnabler_Cpanel_OAuthHelper::initializeStatic();
?>