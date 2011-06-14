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
 
class Aflexi_CdnEnabler_Cpanel_DomainHelper implements Aflexi_Common_Object_Initializable {
    
    const PREF_PUBLISHER_DOMAINS_PREFIX = 'i9n.publisherDomains.';
    
    /**
     * @var Aflexi_Common_Log_Logger
     */
    private static $logger;
    
    static function initializeStatic(){
        self::$logger = Aflexi_Common_Log_LoggerFactory::getLogger(__CLASS__);
    }
    
    /**
     * @var Aflexi_CdnEnabler_Cpanel_Config
     */
    private $config = NULL;
    
    /**
     * @var Aflexi_Common_Net_XmlRpc_AbstractClient
     */
    private $xmlRpcClient = NULL;

    function __construct(){
    }
    
    function setConfig(Aflexi_CdnEnabler_Cpanel_Config $config){
        $this->config = $config;
    }
    
    function setXmlRpcClient(Aflexi_Common_Net_XmlRpc_AbstractClient $xmlRpcClient){
        $this->xmlRpcClient = $xmlRpcClient;
    }
    
    function initialize(){
    }
    
    function getDomains() {
        return $this->nativeGetDomains();
    }
    
    function getCpanelConfig() {
    	return $this->nativeGetCpanelConfig();
    }
    
    function setDomains($publisherId, $domains) {
        $runtimeConfig = $this->config['runtime'];
        $runtimeConfig['prefs'][self::PREF_PUBLISHER_DOMAINS_PREFIX."$publisherId"] = $domains;
        $this->config['runtime'] = $runtimeConfig;
        $this->config->write($this->config->getSource('runtime'), 'runtime');
    }

    /***
     * Get domain of current account based on published name that she/he create
     * @param string $publishedName
     * @return string domain
     */
    protected function getDomain(){
        return $this->nativeGetDomain();
    }

    /***
     * Remove CNAME based on the published name
     * @param  $name
     * @return
     */
    function deleteCName($name){
        $domain_line = $this->getDomainLineNumber($name);
        if(!empty($domain_line)){
             return $this->nativeRemoveCName($domain_line['domain'], $domain_line['line']);
        }
    }

    function addCName($name = '', $cName = '') {
        $rt = $this->config;
        $domain = $this->verifyDomain($name);
        if($rt['global']['integration']['cname']['auto_cname'] != "disabled"){
            if($rt['global']['integration']['cname']['auto_cname'] == "conditional" && !$this->veriyNs($domain)){
                // Checking enabled, and S is not verified)
                return FALSE;
            }
            return  $this->nativeAddCName($name, $cName, $domain);
        }
        return FALSE;
    }


    /**
     * Get line number of CNAME that wanted be deleted in /var/named/$domain.db file
     * @param  string
     * @return mixed
     */
    protected function getDomainLineNumber($name){
        $domains = $this->nativeGetZones();
        // TODO [yasir 20110316] Should improve this, use an array function to the do job
        foreach($domains[0]['zones'] as $k => $v){
            foreach($domains[0]['zones'][$k] as $k2 => $v2){
                if((strpos($v2, "CNAME") !== FALSE ) && (strpos($v2, $name) !== FALSE)){
                    return array("domain"=> $k, "line"=> (int)$k2+1);
                }
            }
        }
        if(self::$logger->isDebugEnabled()){
            self::$logger->debug("Get domain line numer : ".var_export($name, TRUE));
        }
    }

    protected function verifyDomain($name){
        $domains = $this->getDomains();
        foreach ($domains as $domain) {
            if (preg_match("/\.{$domain}$/", $name) > 0) {
                return $domain;
            }
        }
    }
    /**
     * To check either NS of the domain is pointing to one of cpanel NS.
     * @param  string $domain
     * @return boolean
     */
    protected function veriyNs($domain){
        $cpanelSettings = $this->getCpanelConfig();
        $nsRecord = dns_get_record($domain, DNS_NS);
        // NOTE: [yasir 20110303] Verify the way we do the  NS checking is correct
        foreach($nsRecord as $k){
            if(in_array($k['target'], $cpanelSettings)){
                return TRUE;
            }
        }
        if(self::$logger->isDebugEnabled()){
           self::$logger->debug("Failed to verify NS : ".var_export($domain, TRUE));
        }
        return FALSE;
    }

    protected function nativeAddCName($name='', $cName='', $domain) {
        if ($domain != '') {
            $zone_add_params = array(
                'domain' => $domain,
                'name' => $name.".",
                'cname' => $cName,
                'ttl' => 14400,
                'type' => 'CNAME'
                );
            $rt = Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript(
                'zone_add.pl',
                json_encode($zone_add_params)
            );
            if(self::$logger->isDebugEnabled()){
               self::$logger->debug("Executing zone_add.pl : ".var_export($zone_add_params, TRUE));
            }
            return $rt['result'];
        }
    }

    protected function nativeRemoveCName($domain, $line) {

        if ($domain != '') {
            $zone_delete_params = array(
                'domain' => $domain,
                'Line' => $line,
                );
            $rt = Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript(
                'zone_remove.pl',
                json_encode($zone_delete_params)
            );
            if(self::$logger->isDebugEnabled()){
               self::$logger->debug("Executing zone_remove.pl : ".var_export($zone_delete_params, TRUE));
            }
            return $rt['result'];
        }
    }

    protected function nativeGetZones() {
        $rt = Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript(
            'zone_get.pl'
        );
        return $rt['result'];
    }
    
    protected function nativeGetDomains() {
        $rt = Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript(
            'domain_list.pl'
        );
        return $rt['domains'];
    }
    
    protected function nativeGetCpanelConfig() {
        $rt = Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript(
            'whm_config_get.pl'
        );
        return $rt['result'];
    }

    /**
     * Get domain of current user
     */
    protected function nativeGetDomain() {
        $rt = Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript(
            'domain_get.pl'
        );
        return $rt['result'];
    }
}

Aflexi_CdnEnabler_Cpanel_DomainHelper::initializeStatic();