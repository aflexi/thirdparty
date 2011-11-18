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
 
# namespace Aflexi\CdnEnabler\Cpanel;

/**
 * @author yclian
 * @auhtor yasir
 * @since 2.8
 * @version 2.16.20110530
 */
class Aflexi_CdnEnabler_Cpanel_PackageHelper implements Aflexi_Common_Object_Initializable, 
                                                        Aflexi_CdnEnabler_Package_Helper,
                                                        Aflexi_CdnEnabler_Package_EventListener{
    
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
    
    /**
     * Path to local CDN packages storage.
     */
    private $ymlPackages = '';
    
    /**
     * @var Aflexi_CdnEnabler_Cpanel_UserHelper
     */
    private $userHelper;
    
    function __construct(){
        $this->ymlPackages = CPANEL_AFX_DATA.'/operator/packages.yml';
    }
    
    function setConfig(Aflexi_CdnEnabler_Cpanel_Config $config){
        $this->config = $config;
    }
    
    function setXmlRpcClient(Aflexi_Common_Net_XmlRpc_AbstractClient $xmlRpcClient){
        $this->xmlRpcClient = $xmlRpcClient;
    }
    
    function setUserHelper(Aflexi_CdnEnabler_Cpanel_UserHelper $userHelper = NULL) {
        $this->userHelper = $userHelper;
    }
    
    function initialize(){
    }
    
    function getPackage($id){
        $packages = Aflexi_Common_Lang_Arrays::get(
            $this->nativeGetPackages(),
            'packages',
            array()
        );
        return array_key_exists($id, $packages) ? $packages[$id] : NULL;
    }
    
    function getPackages($cdnEnabledOnly = FALSE){
        
        $rt;

        $nativePackages = $this->nativeGetPackages();
        $rt = array();
        
        if($cdnEnabledOnly){
            foreach ($nativePackages['packages'] as $packageName => $package){
                if(array_key_exists('FEATURELIST', $package)){
                    $packageFeatureList = $package['FEATURELIST'];
                    if(@$nativePackages['feature_lists'][$packageFeatureList]['cdn']){
                        $rt[$packageName] = $package;
                    }
                } else{
                    if(self::$logger->isWarnEnabled()){
                        self::$logger->warn("Could not find 'FEATURELIST in package '{$packageName}'");
                    }
                }
            }
        } else{
            $rt = $nativePackages['packages'];
        }
        
        return $rt;
    }
    
    function getCdnPackage($id){

        $filter = array('user' => $this->config['operator']['auth']['username']);

        if(is_numeric($id)){
            $filter = array_merge(array('id' => (int) $id), $filter);
        }else{
            $filter = array_merge(array('name' => $id), $filter);
        }
        
        $rt = $this->xmlRpcClient->execute('bandwidthPackage.get', array(
            $this->config['operator']['auth']['username'],
            $this->config['operator']['auth']['key'],
            $filter
        ));
        $rt = $rt['results'];
        
        return !empty($rt) ? $rt[0] : NULL;
    }
    
    function getCdnPackages(){
        
        $rt = array();
        
        $results = $this->xmlRpcClient->execute('bandwidthPackage.get', array(
            $this->config['operator']['auth']['username'],
            $this->config['operator']['auth']['key'],
            array(
                'user' => $this->config['operator']['auth']['username']
            ),
            array(
                'extract' => array(
                    'name' => TRUE,
                    'id' => TRUE,
                    'bandwidthLimit' => TRUE
                )
            )
        ));
        $results = $results['results'];
        foreach($results as $package){
            $rt[$package['name']] = $package;
        }
        
        return $rt;
    }

    /**
     * No effective way so far to handle this.
     *
     * @see api/Aflexi/CdnEnabler/Aflexi_CdnEnabler_PackageHelper::isCdnEnabled()
     */
    function isCdnEnabled($id){

        $nativePackage = $this->nativeGetPackage($id);

        if($nativePackage){
            if(@$nativePackage['feature_list']['cdn']){
                return TRUE;
            }
            return FALSE;
        }
        return NULL;
    }

    function setCdnEnabled($id, $enabled = TRUE, $byPackage = TRUE){


        if($byPackage){
            $nativePackage = $this->nativeGetPackage($id);
            $id = $nativePackage['package']['FEATURELIST'];
        }

        $this->nativeUpdateFeatureList(
            $id,
            array(
                'cdn' => $enabled ? 1 : 0
            ),
            TRUE
        );

    }

    function setCdnEnabledAllFeatures($enabled = TRUE){

        $features = $this->nativeGetFeatures();

        foreach($features['result'] as $featureId){
            if(!in_array($featureId, array('disabled'))){
                $this->nativeUpdateFeatureList(
                    $featureId,
                    array(
                        'cdn' => $enabled ? 1 : 0
                    ),
                    TRUE
                );
            }

        }


    }

    function getSyncStatuses($cdnPackages = NULL){

        $rt = array(
            'synced' => array(),
            'unsynced' => array(),
            'unqualified' => array()
        );

        if(is_null($cdnPackages)){
            $cdnPackages = $this->getCdnPackages();
        }

        $nPackages = $this->nativeGetPackages();
        foreach($nPackages['packages'] as $packageName => $package){

            $feature = $nPackages['feature_lists'][
                $package['FEATURELIST']
            ];

            if(@$feature['cdn']){
                if(array_key_exists($packageName, $cdnPackages)){
                    $rt['synced'] []= $packageName;
                } else{
                    $rt['unsynced'] []= $packageName;
                }

            } else{
                $rt['unqualified'] []= $packageName;
            }
        }

        return $rt;
    }

    function syncPackages($filters = NULL, &$outCdnPackages = NULL){

        if($this->userHelper->getSharingPackage()){
            // NOTE [yasir 20110907] If sharing package is set, then all Feature must be CDN enabled.
            $this->setCdnEnabledAllFeatures(TRUE);
        }


        if(is_null($outCdnPackages)){
            $outCdnPackages = $this->getLocalCdnPackages();
        }

        /*
         * Used only if logging INFO is on.
         */
        $startTime = 0;
        $statuses;
        $queueCreate;
        $queueUpdate;
        $packages;
        $cdnPackages;
        $rt = array(
            'created' => array(),
            // This update is not updating the remote but local.
            'updated' => array()
        );

        $cdnPackages = $this->getCdnPackages();
        $statuses = $this->getSyncStatuses($cdnPackages);
        $packages = $this->getPackages();

        if(!is_null($filters)){
            $queueCreate = array_intersect($statuses['unsynced'], $filters);
            $queueUpdate = array_intersect($statuses['synced'], $filters);
            $queueCreate = $filters;
        } else{
            $queueCreate = $statuses['unsynced'];
            $queueUpdate = $statuses['synced'];
        }

        if(self::$logger->isInfoEnabled()){

            $numQueueCreate = sizeof($queueCreate);
            $numQueueUpdate = sizeof($queueUpdate);

            self::$logger->info("Creating $numQueueCreate packages and updating $numQueueUpdate existing local packages..");
            $startTime = microtime(TRUE);
        }

        $rt['created'] = $this->syncPackagesCdnCreate($queueCreate, $outCdnPackages, $packages);
        $rt['updated'] = $this->syncPackagesCdnUpdate($queueUpdate, $outCdnPackages, $cdnPackages);

        $this->syncPackagesDb($outCdnPackages);

        if(self::$logger->isInfoEnabled()){
            $elapsed = round((microtime(TRUE) - $startTime) * 1000, 4);
            self::$logger->info("Synchronization completed within {$elapsed}ms");
        }

        return $rt;
    }

    function onPackageCreated($package){}

    function onPackageUpdated($package1, $package2){}

    function onPackageDeleted($package1){}

    protected function getLocalCdnPackages(){
        if(file_exists($this->ymlPackages)){
            return Aflexi_Common_Yaml_Utils::read($this->ymlPackages);
        } else{
            return array();
        }
    }

    /**
     * @param array $filters
     * @param array $outCdnPackages
     * @param array $packages
     * @return int
     */
    protected function syncPackagesCdnCreate($filters, &$outCdnPackages, $localPackages){

        $rt = 0;

        if(self::$logger->isDebugEnabled()){
            self::$logger->debug('Creating CDN packages: '.var_export($filters, TRUE));
        }

        foreach($localPackages as $packageName => $package){

            if(
                // Only if packageName is in filters
                in_array($packageName, $filters) &&
                // Do not re-create if it's already in $outCdnPackages
                !array_key_exists($packageName, $outCdnPackages)
            ){
                // We are not using helper.request here because it's a bad idea
                // to send multiple writing calls as one.
                $cdnPackageId = $this->remoteCreatePackage($packageName, $package);
                // NOTE [yclian 20100927] Not reading other properties such as
                // bandwidth as defaults are used and users may change.
                // Services like bandwidth limit shall pull the latest from
                // remote before using the data.
                $outCdnPackages[$packageName] = array(
                    'id' => $cdnPackageId,
                    'name' => $packageName,
                    'type' => 'PUBLISHER_LINK',
                    'canHaveHttpPull' => (bool) TRUE,
                    'canHaveHttpPush' => (bool) false,
                    'canHaveLive' => (bool) false,
                    'canHaveVod' => (bool) false
                );
                $rt++;
            }
        }


        if(self::$logger->isDebugEnabled()){
            self::$logger->debug("Created $rt CDN packages");
        }

        return $rt;
    }

    /**
     * @param string $packageName
     * @param array $package Local package
     * @return int CDN package ID.
     */
    protected function remoteCreatePackage($packageName, $package = NULL){

        return $this->xmlRpcClient->execute('bandwidthPackage.create', array(
            $this->config['operator']['auth']['username'],
            $this->config['operator']['auth']['key'],
            array(
                'name' => $packageName,
                'user' => array(
                    'id' => $this->userHelper->getOperatorId()
                ),
                'dedicatedBandwidth' => FALSE,
                // NOTE [yasir 20110530] The threshold limit is change to be -1, for unlimited
                //'bandwidthLimit' => 0,
                // FIXME [yasir 20110530] Enable diskSpaceLimit, when it is already supported for cpanel/whm.
                //'diskSpaceLimit' => 0,
                'bandwidthLimit' => -1,
                'speedLimit' => 0,
                'resourceLimit' => 99,
                'streamingAllowed' => FALSE,
                'sslEnabled' => FALSE,
                'servingAllLocations' => TRUE,
                'type' => 'PUBLISHER_LINK',
            )
        ));
    }

    protected function syncPackagesCdnUpdate($filters, &$outCdnPackages, $remotePackages){

        $rt = 0;

        if(self::$logger->isDebugEnabled()){
            self::$logger->debug('Updating local CDN packages: '.var_export($filters, TRUE));
        }

        foreach($remotePackages as $remotePackageName => $remotePackage){

            if(in_array($remotePackageName, $filters)){
                $outCdnPackages[$remotePackageName] = array_intersect_key($remotePackage, array_flip(array(
                    'id',
                    'name',
                    'type',
                    'bandwidthLimit',
                    'dedicatedBandwidth'
                )));
            }

            $rt++;
        }

        if(self::$logger->isDebugEnabled()){
            self::$logger->debug("Updated $rt local CDN packages");
        }

        return $rt;
    }

    protected function syncPackagesDb($outCdnPackages){
        Aflexi_Common_Yaml_Utils::write($this->ymlPackages, $outCdnPackages);
    }

    /**
     * @return array an associative array with keys: 'packages', 'feature_descriptions', 'feature_sets'.
     */
    protected function nativeGetPackages(){
        return Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript('package_list.pl');
    }

    /**
     * @return array an associative array with keys: 'feature', 'feature'.
     */
    protected function nativeGetFeatures(){
        return Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript('features_list.pl');
    }

    /**
     * @param string $id Package name
     * @return array An associative array consists of 'package' and 'feature'.
     */
    protected function nativeGetPackage($id){

        $rt = NULL;
        $nativePackages = $this->nativeGetPackages();

        // If package of ID exists
        if(array_key_exists($id, $nativePackages['packages'])){

            $rt = array();
            $rt['package'] = $nativePackages['packages'][$id];

            if(array_key_exists($rt['package']['FEATURELIST'], $nativePackages['feature_lists'])){
                $rt['feature_list'] = $nativePackages['feature_lists'][
                    $rt['package']['FEATURELIST']
                ];
            }
        }

        return $rt;
    }

    protected function nativeUpdateFeatureList($id, $changes, $useMerge){
        Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript(
            'featurelist_update.pl',
            json_encode(
                array(
                    'feature_list_name' => $id,
                    'feature_list' => $changes,
                    'use_merge' => $useMerge ? 1 : 0
                )
            )
        );
        return TRUE;
    }
    
    /**
     * @author Nicolas Yip
     * @since V 2.11 [20101229]
     * Finds deleted cPanel bandwidth native packages.
     */
    function detectDeletedPackages () {
    	$nativePackages = $this->nativeGetPackages();
    	$cachedPackages = $this->getLocalCdnPackages();
    	$nativeDeletedPackages = array_diff_assoc($nativePackages['packages'], $cachedPackages);
    	return $nativeDeletedPackages;
    }

    /**
     * Get the package name with standard suffix.
     * It will be {hostname}_cpanel_package.
     *
     * @author yasir
     * @return string|boolean
     */
    function getSharingPackageName($enable = FALSE){

        if($enable){

            $packageName = $this->userHelper->getCpanelHostname().'_cpanel_package';

            if(!$this->getCdnPackage($packageName)){
                // If not found, create one.
                $this->remoteCreatePackage($packageName);
            }

            return $packageName;
        }

        return FALSE;
    }
}

Aflexi_CdnEnabler_Cpanel_PackageHelper::initializeStatic();
