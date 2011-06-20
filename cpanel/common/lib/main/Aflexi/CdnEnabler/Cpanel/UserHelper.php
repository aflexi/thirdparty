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
 * @author yingfan
 * @author yasir
 * @since 2.9
 * @version 2.13.20110225
 */
class Aflexi_CdnEnabler_Cpanel_UserHelper implements Aflexi_Common_Object_Initializable,
                                                     Aflexi_CdnEnabler_User_Helper,
                                                     Aflexi_CdnEnabler_User_EventListener {
    
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
     * 
     * Operator ID
     * @var integer
     */
    private $operatorId = 0;
    
    /**
     * 
     * Path to users config
     * @var string
     */
    private $configUsersPath = '';
    
    /**
     * 
     * Package Helper
     * @var Aflexi_CdnEnabler_Cpanel_PackageHelper
     */
    private $packageHelper;
    

    /**
     * @var Aflexi_CdnEnabler_Cpanel_OAuthHelper
     */
    private $oauthHelper;
    
    function __construct(){
    }
    
    function setConfig(Aflexi_CdnEnabler_Cpanel_Config $config){
        $this->config = $config;
    }
    
    function setXmlRpcClient(Aflexi_Common_Net_XmlRpc_AbstractClient $xmlRpcClient){
        $this->xmlRpcClient = $xmlRpcClient;
    }
    
    function getOperatorId() {
        return $this->operatorId;
    }
    
    function setOperatorId($id = NULL) {
        if (is_null($id)) {
            $rt = $this->getCdnSelfUser();
            
            $id = (int) $rt['id'];
        }
        
        $this->operatorId = $id;
        return $this->operatorId;
    }
    
    function setConfigUsersPath($configUsersPath = NULL){
        if (is_null($configUsersPath)) {
            $configUsersPath = CPANEL_AFX_DATA.'/operator/publishers.yml';
        }
        $this->configUsersPath = $configUsersPath;
        return $this->configUsersPath;
    }
    
    function getConfigUsersPath() {
        return $this->configUsersPath;
    } 
    
    function setPackageHelper(Aflexi_CdnEnabler_Cpanel_PackageHelper $packageHelper = NULL) {
        if (is_null($packageHelper)) {
            $packageHelper = new Aflexi_CdnEnabler_Cpanel_PackageHelper();
        }
        
        $this->packageHelper = $packageHelper;
        $this->packageHelper->setConfig($this->config);
        $this->packageHelper->setXmlRpcClient($this->xmlRpcClient);
    }
    
     function setOAuthHelper(Aflexi_CdnEnabler_Cpanel_OAuthHelper $oauthHelper = NULL) {
         if (is_null($oauthHelper)) {
            $oauthHelper = new Aflexi_CdnEnabler_Cpanel_OAuthHelper();
            $oauthHelper->setConfig($this->config);
            $oauthHelper->initialize();
        }
        $this->oauthHelper = $oauthHelper;
    }
    
    function initialize(){
        $this->setOperatorId();
        $this->setConfigUsersPath();
        $this->setPackageHelper();
        $this->setOAuthHelper();
    }
    
    function getUser($name){
        return $this->nativeGetUser($name);
    }
    
    function getUsers($cdnEnabledOnly = FALSE){
        return $this->nativeGetUsers();
    }
    
    function getCdnUser($id){
        $rt = $this->xmlRpcClient->execute('user.get', array(
            $this->config['operator']['auth']['username'],
            $this->config['operator']['auth']['key'],
            array(
                'id' => $id
            )
        ));
        $rt = $rt['results'];
        
        return !empty($rt) ? $rt[0] : NULL;
    }
    
    function getCdnSelfUser($userName = '', $authKey = '') {
        if (empty($userName)) {
            $userName = $this->config['operator']['auth']['username'];
        }
        
        if (empty($authKey)) {
            $authKey = $this->config['operator']['auth']['key'];
        }
        
        $rt = $this->xmlRpcClient->execute('user.get', array(
            $userName,
            $authKey,
            array(
                'self' => TRUE
            )
        ));
        $rt = $rt['results'];
        
        return !empty($rt) ? $rt[0] : NULL;
    }
    
    function getCdnUsers($packages = NULL) {
        $rt = array();
        $results = array();
        $index = 0;
        $filter = array('operator' => $this->getOperatorId());
        
        if(is_null($packages)){
            $packages = $this->packageHelper->getPackages(TRUE);
            if(!empty($packages)){
               $filter = array_merge($filter, array('bandwidthPackage.name' => array_keys($packages)));
            }
        }

        do{
            $temp = $this->xmlRpcClient->execute('publisherLink.get', array(
                $this->config['operator']['auth']['username'],
                $this->config['operator']['auth']['key'],
                $filter,
                array(
                    'firstResultIndex' => $index,
                    'maximumResults' => 50
                )
            ));
            $index += 50;


            $results = array_merge($results, $temp['results']);
        }while(count($temp['results']) >=50);

        foreach($results as $publisherLink){
            // Temporary Hack, until GM set user status based on publisherLink status.
//            if($publisherLink['status'] != 'DELETED'){
                // This is as well
                $publisherLink['publisher']['publisherLinkStatus'] = $publisherLink['status'];
                $publisherLink['publisher']['bandwidthPackage'] = $publisherLink['bandwidthPackage'];
                $rt[$publisherLink['publisher']['name']] = $publisherLink['publisher'];
//            }
        }
        
        return $rt;
    }
    
    function getSyncStatuses($cdnUsers = NULL) {
        /**
         * @var array
         */
        $cp_packages;
        $cdnCpanelUsers = array();
        $hostname;
        
        $rt = array(
            'synced' => array(),
            'unsynced_suspend' => array(),
            'unsynced_unsuspend' => array(),
            'unsynced_create' => array(),
            'unsynced_delete' => array(),
            'unqualified' => array()
        );
        
        if(is_null($cdnUsers)){
            $cdnUsers = $this->getCdnUsers();
        }

        $cp_publishers = $this->getUsers();
        $hostname = str_replace('-', '_', $this->getCpanelHostname());


        // [yasir 20110318] find how to run array_filter, with passing $this
        // This is realy bad, foreach() really bad, need to replace with something else,
        // TODO: The strip of property could be done beter may be using RPCX
        
        // Filter out non CDN cp_publishers,
        foreach($cp_publishers as $key => $cp_user){
            if(!$this->packageHelper->isCdnEnabled($cp_publishers[$key]['PLAN'])){
                unset($cp_publishers[$key]);
            }
        }

        // Filter out non cPanel user in CDN publishers
        foreach($cdnUsers as $user){
            if(
                // NOTE: This filter is not need anymore since, publisherLink.get already do filter based on the
                // packagename
                // $this->packageHelper->isCdnEnabled($user['bandwidthPackage']['name']) &&
                // This check is needed for operator running cPanel CDN Enabler on multiple machines.
                strpos($user['username'], $hostname) !== FALSE
            ){
                $cdnCpanelUsers[$user['name']] = array(
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'publisherLinkStatus' => $user['publisherLinkStatus'],
                    'id' => $user['id'],
                    'cpanel' =>array(
                        'PLAN'=> @$cp_publishers[$user['name']]['PLAN'],
                        'FEATURELIST' => @$cp_publishers[$user['name']]['FEATURELIST'],
                        'BWLIMIT' => @$cp_publishers[$user['name']]['BWLIMIT']  
                ));
                if(isset($cp_publishers[$user['name']]['SUSPENDED'])){
                    $cdnCpanelUsers[$user['name']]['cpanel']['SUSPENDED'] = $cp_publishers[$user['name']]['SUSPENDED'];
                }
            }
        }

        foreach($cdnCpanelUsers as $key ){
            if(isset($cp_publishers[$key['name']]['SUSPENDED'])){
                $rt['unsynced_suspend'][$key['name']] = $key;
            }elseif(@$key['publisherLinkStatus'] == 'SUSPENDED' && !isset($cp_publishers[$key['name']]['SUSPENDED'])){
                $rt['unsynced_unsuspend'][$key['name']] = $key;
            }elseif(array_key_exists($key['name'], $cp_publishers) && @$key['publisherLinkStatus'] == 'ACTIVE'){
                $rt['synced'][$key['name']] =$key;
            }
//            else{
//                $rt['unqualified'][$key['name']] = $key;
//            }
        }
        $rt['unsynced_create'] = array_diff_assoc($cp_publishers, $cdnCpanelUsers);
        $rt['unsynced_delete'] = array_diff_assoc($cdnCpanelUsers, $cp_publishers);

        return $rt;
    }
    
    function syncUsers($filters = NULL, &$outCdnUsers = NULL) {
        $rt;
        $cp_publishers_target_update;
        $cp_publishers_target_create;
        $cp_publishers_target_unsuspend;
        $cdn_packages;
        
        $rt = array(

            'updated' => 0,
            'unsynced_unsuspended' => 0,
            'unsynced_suspended' => 0,
            'unsynced_created' => 0,
            'unsynced_deleted' => 0
        );
        
        if(is_null($outCdnUsers)){
            $outCdnUsers = $this->getLocalCdnUsers();
        }

        $cdn_publishers = $this->getCdnUsers();
        $cp_publishers = $this->getSyncStatuses($cdn_publishers);
        $cdn_packages = $this->packageHelper->getCdnPackages();

        // If $cp_publishers_filter is provided, we will select publishers for
        // update and create.
        if(!is_null($filters)){
            
            $cp_publishers_target_update = array();
            $cp_publishers_target_create = array();
            
            $cp_publishers_target_update = array_intersect_key(
                $cp_publishers['synced'],
                array_flip($filters) 
            );
            $cp_publishers_target_create = array_intersect_key(
                $cp_publishers['unsynced'],
                array_flip($filters) 
            );
            
        } else{
            // NOTE [yasir 20110318], basically there are 3 event when we need to sync user
            // 1. unsynced_create : Just newly created cpanel user
            // 2. unsysnced_unsuspend : Old cpanel user, but just unsuspend in cpanel
            // 3. synced: not really a sync, but it does some updates on OAuth.
            $cp_publishers_target_update = isset($cp_publishers['synced'])?$cp_publishers['synced']:array();
            $cp_publishers_target_suspend = isset($cp_publishers['unsynced_suspend'])?$cp_publishers['unsynced_suspend']:array();
            $cp_publishers_target_unsuspend = isset($cp_publishers['unsynced_unsuspend'])?$cp_publishers['unsynced_unsuspend']:array();
            $cp_publishers_target_create = isset($cp_publishers['unsynced_create'])?$cp_publishers['unsynced_create']:array();
            $cp_publishers_target_delete = isset($cp_publishers['unsynced_delete'])?$cp_publishers['unsynced_delete']:array();
        }

        // Last round of filter, to slash out publishers not being in the specified
        // CDN packages.
        // [yasir 20110320] Everything has been done in getSyncUser()
        //$this->filterPublishers($cdn_packages, $cp_publishers_target_update);
        //$this->filterPublishers($cdn_packages, $cp_publishers_target_create);

        // [yasir 20110318]Cleaning publishers.yml, filtering out the not synced users.
        // Before any update, created, unsuspend process below
        $outCdnUsers = array_intersect_key( $outCdnUsers, $cp_publishers_target_update);
        
        $rt['updated'] += $this->updateCDNPublishers(
            $cp_publishers_target_update,
            $cdn_publishers,
            $outCdnUsers
        );
        
        $rt['unsynced_created'] += $this->createCdnPublishers(
            $cdn_packages,
            $cp_publishers_target_create,
            $outCdnUsers
        );

        $rt['unsynced_unsuspended'] += $this->unsuspendCdnPublishers(
            $cdn_packages,
            $cp_publishers_target_unsuspend,
            $outCdnUsers
        );

        $rt['unsynced_deleted'] += $this->deleteCdnPublishers(
            $cdn_packages,
            $cp_publishers_target_delete,
            $outCdnUsers
        );

        $rt['unsynced_suspended'] += $this->suspendCdnPublishers(
            $cdn_packages,
            $cp_publishers_target_suspend
        );

        $this->writeLocalPublishers($this->configUsersPath, $outCdnUsers);
        return $rt;
    }
    

    
    function updatePublisherPackage($publisherLinkId, $packageId) {
        list($publisherId, $operatorId) = explode(',', $publisherLinkId);
        $publisherLink = array(
            'publisher' => array('id' => (int) $publisherId),
            'operator' => array('id' => (int) $operatorId),
            'bandwidthPackage' => array(
                'id' => (int) $packageId,
                'user' => array('id' => (int) $publisherId),
                'type' => 'PUBLISHER_LINK'
            ),
            'status' => 'ACTIVE'
        );

        return $this->updatePublisherLink(
            $publisherId,
            $publisherLink
        );
    }
    
    function onUserUpgradePackage() {
            //Find out if user has made changes to package
        $cpUsers = $this->getUsers(TRUE);
        $cdnUsers = $this->getCdnUsers();
        $cdnPackages = $this->packageHelper->getCdnPackages();
        
        foreach ($cpUsers as $userName=>$cpUser) {
            if (
                (isset($cdnUsers[$userName])) &&
                ($cpUser['PLAN'] != $cdnUsers[$userName]['bandwidthPackage']['name'])
            ) {
                if ($this->isCdnEnabled($userName) && isset($cdnPackages[$cpUser['PLAN']])) {
                    $this->updatePublisherPackage(
                        $cdnUsers[$userName]['id'] . ',' . $this->getOperatorId(),
                        $cdnPackages[$cpUser['PLAN']]['id']
                    );
                }
                else {
                    //Suspend user
                    $this->updatePublisherLink(
                        $cdnUsers[$userName]['id'],
                        array(
                            'publisher' => array('id' => (int) $cdnUsers[$userName]['id']),
                            'operator' => array('id' => (int) $this->getOperatorId()),
                            'status' => 'SUSPENDED'
                        )
                    );
                }
            }
        }
    }
    
    function onUserCreated($user) {
    }

    function onUserDeleted($user1 = null) {

    }
    function onUserUpdated($user1, $user2) {
    }

    function onUserXgraded($user, $package1, $package2) {
    }

    
    protected function filterPublishers(array $cdn_packages, array &$cp_publishers){
        foreach($cp_publishers as $cp_publisher_name => $cp_publisher){
            
            $cdn_package = $cp_publisher['PLAN'];
            // NOTE [yasir 20110318] I think this filter could be done on getSyncUser()
            if(
                // No email.
                empty($cp_publisher['EMAIL'])
                // Not in CDN packages.
                || !array_key_exists($cp_publisher['PLAN'], $cdn_packages)
            ){
                unset($cp_publishers[$cdn_package]);
            }
        }
    }
    
    protected function updateCDNPublishers(array $cp_publishers, array $cdn_publishers, array &$cdn_publishers_local = array()){
        $rt = 0;
        
        foreach($cp_publishers as $cp_publisher_name => $cp_publisher){
            
            // NOTE [yclian 20100729] Originally, we are checking against $cdn_publishers_local,
            // this can be very bad, say, if publishers.yml is broken, things can
            // never be sync here.
            // if(array_key_exists($cp_publisher_name, $cdn_publishers_local)){
            // TODO [yclian 20100903] If a user has email changed in cPanel, the
            // current resync won't update that.

            // NOTE: This checking againts local file, may be unnecessary, all sync data, has been
            // verified in getSyncUsers
            if(array_key_exists($cp_publisher_name, $cdn_publishers)){
                
                $cdn_publisher = $cdn_publishers[$cp_publisher_name];
                
                // Retaining these properties from existing
                if(array_key_exists($cp_publisher_name, $cdn_publishers_local)){
                    $cdn_publisher_ext_props = array_intersect_key($cdn_publishers_local[$cp_publisher_name], array_flip(array(
                        'password',
                        // TODO [yclian 20100728] Need detection here. If the oauth keys are lost, we need to regenerate.
                        'oauth_key',
                        'oauth_secret',
                        // TODO [yclian 20100728] What if he changed package in Aflexi portal? Or cPanel? We one shall
                        // we give precedence? It shall really be cPanel.
                        'package'
                    )));
                } else{
                    
                    $cdn_publisher_ext_props = array();
                }
                
                // If neither oAuth information exists, we will regenerate.
                if(!isset($cdn_publisher_ext_props['oauth_key']) || !isset($cdn_publisher_ext_props['oauth_secret'])){
                    
                    $cdn_oauth = $this->registerOAuth($cp_publisher['username']);
                    
                    $cdn_publisher_ext_props = array_merge($cdn_publisher_ext_props, array(
                        'oauth_key' => $cdn_oauth->consumer_key,
                        'oauth_secret' => $cdn_oauth->consumer_secret
                    ));
                }
                
                // Merge the ratained with recently retrieved values.
                $cdn_publisher = array_merge($cdn_publisher_ext_props, $cdn_publisher);
                // Store it.
                $cdn_publishers_local[$cp_publisher_name] = $cdn_publisher;
                
                $rt++;
            }
        }
        
        return $rt;
    }
    
   /**
     * Create CDN users from $cp_publishers if they are not in $cdn_publishers.
     * 
     * @param array $cdn_packages
     * @param array $cp_publishers
     * @param array $cdn_publishers
     * @return int Number of created publishers.
     */
    protected function createCdnPublishers(array $cdn_packages, array $cp_publishers, array &$cdn_publishers = array()){

        $rt = 0;
        
        foreach($cp_publishers as $cp_publisher_name => $cp_publisher){
                // NOTE: [yasir 20110225] another checking in publishers.yml is not needed, and it could rise a problem
                // when a user that has been sycned and created from cpanel, then it deletes from portal
                // publishers.yml would not contain updated info, since the deleted user would remain there.
                //            if(!array_key_exists($cp_publisher_name, $cdn_publishers)){
                
                // TODO [yclian 20100719] If we want to support standalone, we gotta 
                // do something here - to pass the standalone package.
                
                // NOTE [yclian 20100728] This fixes the possibility of publisher-
                // package has not been created yet in Aflexi but we are forcing 
                // a user-sync.
                if(array_key_exists($cp_publisher['PLAN'], $cdn_packages)){
                    $cdn_publishers[$cp_publisher_name] = $this->createCdnPublisher($cp_publisher, $cdn_packages[$cp_publisher['PLAN']]);
                    $rt++;
                }
//            }
        }

        if(self::$logger->isInfoEnabled()){
            self::$logger->info("{$rt} publisherLinks are created");
        }


        
        return $rt;
    }

    /**
     * Active a SUSPENDED CDN users, if cPanel user with same username is ACTIVE
     *
     * @param array $cdn_packages
     * @param array $cp_publishers
     * @param array $cdn_publishers
     * @return int Number of created publishers.
     */
    protected function unsuspendCdnPublishers(array $cdn_packages, array $cp_publishers, array &$cdn_publishers = array()){

        $rt = 0;

        foreach($cp_publishers as $cp_publisher_name => $cp_publisher){

                // TODO [yclian 20100719] If we want to support standalone, we gotta
                // do something here - to pass the standalone package.

                // NOTE [yclian 20100728] This fixes the possibility of publisher-
                // package has not been created yet in Aflexi but we are forcing
                // a user-sync.
                if(array_key_exists($cp_publisher['cpanel']['PLAN'], $cdn_packages)){
                    $cp_publisher['secret'] = substr(md5(rand().time().$cp_publisher['username']), 24, 8);

                    // NOTE [yasir 20110320] Check how to reset or set a password
                    $this->updatePublisherLink(
                        $cp_publisher['id'],
                        array(
                            'publisher' => array('id' => (int) $cp_publisher['id']),
                            'operator' => array('id' => (int) $this->getOperatorId()),
                            'status' => 'ACTIVE',
                            'password' => $cp_publisher['secret']
                        )
                    );
                     $cdn_publishers[$cp_publisher_name] = $this->createPublisherEntry($cp_publisher, $cdn_packages);
                     $rt++;
                }
//            }
        }

        return $rt;
    }

    /**
     * Suspend CDN users, when a same user in cPanel is suspended
     *
     * @param array $cdn_packages
     * @param array $cp_publishers
     * @param array $cdn_publishers
     * @return int Number of created publishers.
     */
    protected function suspendCdnPublishers(array $cdn_packages, array $cp_publishers){

        $rt = 0;

        foreach($cp_publishers as $cp_publisher_name => $cp_publisher){
             $this->updatePublisherLink(
                $cp_publisher['id'],
                array(
                    'publisher' => array('id' => (int) $cp_publisher['id']),
                    'operator' => array('id' => (int) $this->getOperatorId()),
                    'status' => 'SUSPENDED'
                )
            );
            $rt++;
        }
        return $rt;
    }

    protected function deleteCdnPublishers(array $cdn_packages, array $cp_publishers){

        $rt = 0;

        foreach($cp_publishers as $cp_publisher_name => $cp_publisher){
            $this->deletePublisherLink($cp_publisher['id']);
            $rt++;
        }

        if(self::$logger->isInfoEnabled()){
            self::$logger->info("{$rt} publisherLinks are deleted");
        }

        return $rt;
    }


   /**
     * Create CDN publisher and return his data.
     * 
     * @global $afx_operator
     * @return array
     */
    protected function createCdnPublisher(array $cp_publisher, array $cdn_package){
        
        $rt = array();
        $cdn_publisher_id = NULL;
        $cdn_publisher_password = NULL;
        $cp_publisher['CDN_USERNAME'] = "{$this->getNormalizeUsername($cp_publisher['USER'])}/{$this->getOperatorId()}";
        $cdn_publisher_password = substr(md5(rand().time().$cp_publisher['USER']), 24, 8);
        $cdn_publisher_id = $this->createCdnUser(
            $cp_publisher['CONTACTEMAIL'],
            $cp_publisher['USER'],
            $cp_publisher['CDN_USERNAME'],
            $cdn_publisher_password,
            $cdn_package['id']
        );
        
        $cdn_oauth = $this->registerOAuth($cp_publisher['CDN_USERNAME']);
        
        $rt = array(
            'id' => $cdn_publisher_id,
            'username' => $cp_publisher['CDN_USERNAME'],
            'email' => $cp_publisher['CONTACTEMAIL'],
            'name' => $cp_publisher['USER'],
            'password' => $cdn_publisher_password,
            'oauth_key' => $cdn_oauth->consumer_key,
            'oauth_secret' => $cdn_oauth->consumer_secret,
            'package' => array(
                'id' => $cdn_package['id'],
                'name' => $cdn_package['name']
            )
        );
        
        return $rt;
    }

    /**
     * Create an entry for publisher.yml
     *
     * @autho yasir
     * @param array
     * @return void
     */
    protected function createPublisherEntry(array $publisher, $cdn_package){


        if(!empty($publisher) && !empty($cdn_package)){

            $cdn_oauth = $this->registerOAuth($publisher['username']);
            $rt = array(
                'id' => $publisher['id'],
                'username' => $publisher['username'],
                'email' => $publisher['email'],
                'name' => $publisher['name'],
                'password' => $publisher['secret'],
                'oauth_key' => $cdn_oauth->consumer_key,
                'oauth_secret' => $cdn_oauth->consumer_secret,
                'package' => array(
                    'id' => $cdn_package['id'],
                    'name' => $cdn_package['name']
                )

            );
            return $rt;
        }


    }

    /**     *
     * Creates temp bandwdith
     * @param int $userId
     * @param int $value
     */
    public function createCdnTempBandwidth($publisherId, $bandwidthValue) {
        $results = $this->xmlRpcClient->execute('publisherLink.createTempBandwidth', array(
            $this->config['operator']['auth']['username'],
            $this->config['operator']['auth']['key'],
            array(
                'operator' => array('id' => (int) $this->getOperatorId()),
                'publisher' => array('id' => (int) $publisherId),
                'bandwidthLimit' => (int) $bandwidthValue
            )
        ));
        
    }

    /**
     * Create XmlRpc call to create user
     * @param  $email
     * @param  $name
     * @param  $username
     * @param  $password
     * @param  $packageId
     * @return void
     */
    protected function createCdnUser($email, $name, $username, $password, $packageId){
        return $this->xmlRpcClient->execute('user.create', array(
            $this->config['operator']['auth']['username'],
            $this->config['operator']['auth']['key'],
            array(
                'role' => 'PUBLISHER',
                'email' => $email,
                'username' => $username,
                'name' => $name,
                'password' => $password,
                'status' => 'ACTIVE'
            ),
            array(
                'id' => $packageId
            )
        ));
    }

    /***
     * This function just is used to update a publisherLink, based on the publisherId
     * @param int $publisherId
     * @param array $options
     * @return void
     */
    protected function updatePublisherLink($publisherId = NULL, $options){
        $rt;
        if($publisherId){
            $rt = $this->xmlRpcClient->execute(
                'publisherLink.update',
                array(
                    $this->config['operator']['auth']['username'],
                    $this->config['operator']['auth']['key'],
                    $publisherId . ',' . $this->getOperatorId(),
                    $options));
        }
        if(self::$logger->isDebugEnabled()){
            self::$logger->debug('Publisher Id must be set a value');
        }
        return $rt;

    }

    /***
     * This function just is used to delete a publisherLink, based on the publisherId
     * @param int $publisherId
     * @param array $options
     * @return void
     */
    protected function deletePublisherLink($publisherId = NULL, $options = array()){
        $rt;
        if($publisherId){
            $rt = $this->xmlRpcClient->execute(
                'publisherLink.delete',
                array(
                    $this->config['operator']['auth']['username'],
                    $this->config['operator']['auth']['key'],
                    $publisherId . ',' . $this->getOperatorId()
                ));
        }

        return $rt;
    }


    /**
     * Call oauthelper to register oauth of publishers
     * @param  $cp_publisher
     * @return object|Response
     */
    protected function registerOAuth($username) {
        return $this->oauthHelper->register(
            'delegate',
            $username
        );
    }

    /**
     * Check a user either has CDN package or not
     * @param  $name
     * @return bool|null
     */
    function isCdnEnabled($name) {
        $nativeUser = $this->nativeGetUser($name);

        return $this->packageHelper->isCdnEnabled(@$nativeUser['PLAN']);
    }

    /**
     * Set CDN package of a user
     * @param  $name
     * @param bool $enabled
     * @return void
     */
    function setCdnEnabled($name, $enabled = TRUE) {
        $nativeUser = $this->nativeGetUser($name);

        if (isset($nativeUser['PLAN'])) {
            $this->packageHelper->setCdnEnabled($nativeUser['PLAN'], $enabled);
        }
    }


    /****
     * Read details of local publisher cached in publishers.yml
     *
     * @return array
     */
    protected function getLocalCdnUsers(){
        if(file_exists($this->configUsersPath)){
            return Aflexi_Common_Yaml_Utils::read($this->configUsersPath);
        } else{
            return array();
        }
    }

    /**
     * Write to publishers.yml
     * @global $afx_config_main
     * @param unknown_type $afx_config_users_path
     * @param unknown_type $cdn_publishers
     */
    public function writeLocalPublishers($afx_config_users_path, $cdn_publishers){

        $out = array();

        foreach($cdn_publishers as $cdn_publisher_name => $cdn_publisher){
            $out[$cdn_publisher_name] = array_intersect_key($cdn_publisher, array_flip(array(
                'id',
                'username',
                'email',
                'name',
                'password',
                'oauth_key',
                'oauth_secret',
                'package',
                'status'
            )));
        }
        // Write the publishers.yml.
        Aflexi_Common_Yaml_Utils::write($afx_config_users_path, $out);
        // Now, write the /home/user/.cdn file, if they have oauth_*.
        foreach($out as $user){
            $this->writeLocalHome($user);
        }
    }

    /**
     * Write to local home ~/.cdn
     * @param  $user
     * @return void
     */
    protected function writeLocalHome($user) {
        if(array_key_exists('oauth_key', $user) && array_key_exists('oauth_secret', $user)){

            $user_name = $user['name'];
            $user_home = "{$this->config['global']['system']['homes']}/$user_name";
            $cdn_file = "$user_home/.cdn";

            // Unlikely that the home dir doesn't exist. But we take care of
            // sandboxing here as well for this test.
            if(!file_exists($user_home)){
                mkdir($user_home, 0711, TRUE);
                @chown($user_home, $user_name);
            }

            Aflexi_Common_Yaml_Utils::write(
                $cdn_file,
                $user
            );
            chmod($cdn_file, 0600);
            @chown($cdn_file, $user_name);
        }
    }

    /**
     * Get normalize username would be : $username_$hostname
     * @param null $cp_name
     * @return string
     */
    protected function getNormalizeUsername($cp_name = NULL){

        // We should handle the null case
        if($cp_name){
            return $cp_name.'_'.str_replace('-', '_', $this->getCpanelHostname());
        }
        // We need to handle NULL case
        // Could i thrown an error here ?
        return '';
    }

    /***
     * Get current cpanel hostname
     * host_get.pl return cpanel scrupt Cpanel::Hostname::gethostname
     * @author yasir
     * @return void
     */
    protected function getCpanelHostname(){
        $rt = Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript(
            "host_get.pl"
        );
        return $rt['hostname'];
    }

    /**
     * Get a cpanel user
     * @param  string $name
     * @return array
     */
   protected function nativeGetUser($name) {

        $rt = Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript(
            "user_get.pl",
            json_encode(
                array(
                    'user_name' => $name
                )
            )
        );
        return $rt['user'];
    }
    
    /**
     * Get all cpanel users
     * @return array
     */
    protected function nativeGetUsers() {
        $rt = Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript(
            "user_get.pl"
        );
        return $rt['users'];
    }
}

Aflexi_CdnEnabler_Cpanel_UserHelper::initializeStatic();
