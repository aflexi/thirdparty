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
 * Implementation of Aflexi_CdnEnabler_Bandwidth_Helper.
 * 
 * @author yclian
 * @since 2.9.20101013
 * @version 2.9.20101014
 */
class Aflexi_CdnEnabler_Cpanel_BandwidthHelper implements Aflexi_Common_Object_Initializable,
                                                          Aflexi_CdnEnabler_Bandwidth_Helper,
                                                          Aflexi_CdnEnabler_Bandwidth_EventListener{

    /**
     * User preference per package, flag to indicate if the package shall use
     * dedicated scheme. FALSE by default.
     */
    const PREF_BANDWIDTH_LIMIT_SHARED_PREFIX = 'i9n.bandwidthLimitShared.';
    
    const CONF_NAMESPACE_BANDWIDTH_PACKAGES = 'bandwidth-packages';
    const CONF_NAMESPACE_BANDWIDTH_PUBLISHERS = 'bandwidth-publishers';
    
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
     * Path to local CDN bandwidth data. e.g. /var/cpanel/aflexi/operator/
     * 	bandwidth-packages.yml.
     * 
     * e.g. array(
     *     'package-1' => array(
     *         'dedicated' => false
     *     )
     * )
     * @var string
     */
    private $ymlPackageBandwidthData = '';
    
    /**
     * Path to local CDN bandwidth data. e.g. /var/cpanel/aflexi/operator/
     * 	bandwidth-publishers.yml.
     * 
     * e.g. array(
     *     'publisher-1' => array(
     *         'packageLimit' => 2345.678,
     *         'limit' => 2345.678,
     *         'used' => 1234.567
     *     )
     * )
     * @var string
     */
    private $ymlPublisherBandwidthData = '';
     /**
     * @var Aflexi_CdnEnabler_Cpanel_UserHelper
     */
    private $userHelper = NULL;
    
    function __construct(){
        $this->ymlPackageBandwidthData = CPANEL_AFX_DATA.'/operator/bandwidth-packages.yml';
        $this->ymlPublisherBandwidthData = CPANEL_AFX_DATA.'/operator/bandwidth-publishers.yml';
    }
    
    function setConfig(Aflexi_CdnEnabler_Cpanel_Config $config){
        $this->config = $config;
    }
    
    function setXmlRpcClient(Aflexi_Common_Net_XmlRpc_AbstractClient $xmlRpcClient){
        $this->xmlRpcClient = $xmlRpcClient;
    }
    
    function initialize(){
        $this->setUserHelper();
    }

    function setUserHelper(Aflexi_CdnEnabler_Cpanel_UserHelper $userHelper = NULL) {
        if (!$this->userHelper) {
            $this->userHelper = new Aflexi_CdnEnabler_Cpanel_UserHelper();
            $this->userHelper->setConfig($this->config);
            $this->userHelper->setXmlRpcClient($this->xmlRpcClient);

            if (file_exists($this->config->getSource('operator'))) {
                $this->userHelper->initialize();

                $runtimeConfig = $this->config['runtime'];
                $runtimeConfig['operator'] = $this->userHelper->getCdnSelfUser();
                $this->config['runtime'] = $runtimeConfig;
            }
        }
        return $this->userHelper;
    }
    
    /**
     * NOTE [yclian 20101016] To maintain the original behaviour (of other 
     * 	helpers, e.g. package helper), we will always perform an XML-RPC call
     * 	but not to rely on the synced cache. (It's a design issue to run things
     * 	either on cache or always on latest, ours is the latter and our sync 
     * 	is simply to FIX the broken stuff).
     * 
     * @see Aflexi_CdnEnabler_Bandwidth_Helper::isBandwidthLimitShared()
     */
    function isBandwidthLimitShared($packageId){
        
        $rt = FALSE;
        $prefs = $this->config['runtime']['prefs'];
        $targetPref = self::PREF_BANDWIDTH_LIMIT_SHARED_PREFIX."$packageId";
        
        // FALSE by default.
        return isset($prefs[$targetPref]) && $prefs[$targetPref] || FALSE;
    }
    
    /**
     * @see Aflexi_CdnEnabler_Bandwidth_Helper::getBandwidthLimit()
     * @param mixed $publisherId
     * @param bool $resolvePackageLimit Not used, already resolved by cPanel.
     * @return float
     */
    function getBandwidthLimit($publisherId, $resolvePackageLimit = TRUE){
        
        $rt = $this->getBandwidthLimits();
        if(array_key_exists($publisherId, $rt)){
            $rt = $rt[$publisherId];
        } else{
            $rt = 0;
        }
        
        return $this->bytesToGb($rt);
    }
    
    private function getBandwidthLimits(){
        $rt = $this->nativeGetBandwidthLimits();
        $rt = $rt['bandwidth_limits'];
        return $rt;
    }
    
    function getPackageBandwidthLimit($packageId){
        
        $rt = $this->getPackageBandwidthLimits();
        if(array_key_exists($packageId, $rt)){
            $rt = $rt[$packageId];
        } else{
            $rt = 0;
        }
        
        return $this->bytesToGb($rt);
    }
    
    private function getPackageBandwidthLimits(){
        $rt = $this->nativeGetPackageBandwidthLimits();
        $rt = $rt['bandwidth_limits'];
        return $rt;
    }
    
    function getCdnBandwidthLimit($publisherId, $resolvePackageLimit = TRUE){}
    
    function getCdnPackageBandwidthLimit($packageId){}
    
    function getSharedBandwidthUsage($publisherId, $year = NULL, $month = NULL){}
    
    function getBandwidthUsage($publisherId, $year = NULL, $month = NULL){
        
        $rt = array();
        
        if(is_null($year)){
            list($month, $year) = $this->getCurrentMonthYear();
        }
        
        $rt = $this->nativeGetBandwidthUsage($publisherId, $year, $month);
        $rt = $rt['bandwidth_usage'];
        return $this->bytesToGb($rt);
    }

    function getCdnBandwidthUsage($publisherId , $year = NULL, $month = NULL, $cdnUsers= NULL){
        return $this->getUserSummaries($cdnUsers);
    }
    
    function setBandwidthLimitShared($packageId, $shared = TRUE){
        
        $runtimeConfig = $this->config['runtime'];
        $runtimeConfig['prefs'][self::PREF_BANDWIDTH_LIMIT_SHARED_PREFIX."$packageId"] = $shared;
        $this->config['runtime'] = $runtimeConfig;
        $this->config->write($this->config->getSource('runtime'), 'runtime');
    }
    
    function setCdnBandwidthLimit($publisherId, $limit){}
    
    function setCdnPackageBandwidthLimit($packageId, $limit){}
    
    function syncBandwidthData($filters = NULL, &$outCdnBandwidthData = NULL){}
    
    function onUserBandwidthUpdated($user, $before, $after){}
    
    function onPackageBandwidthUpdated($package, $before, $after){}
    
    private function bytesToGb($int){
        if($int == 0){
            return floatval(0.0);
        } else{
            return floatval($int) / (1024 * 1024 * 1024);
        }
    }
    
    /**
     * @return array array(0 => $month, 1 = $year)
     */
    private function getCurrentMonthYear(){
        $monthYear = date('n/Y');
        $monthYear = explode('/', $monthYear, 2);
        assert(sizeof($monthYear) == 2);
        self::$logger->isDebugEnabled() && print 'aha' && self::$logger->debug("Resolved current month and year as ".var_export($monthYear, true));
        return $monthYear;
    }
    
    /*
     * Native calls
     * ------------------------------------------------------------------------
     */
    
    /**
     * @return array array('bandwidth_limit' => array('user1' => bandwidth_limit1, ...))
     */
    private function nativeGetBandwidthLimits(){
        return Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript('user_bwlimit_list.pl');
    }
    
    /**
     * @return array array('bandwidth_limit' => array('package1' => bandwidth_limit1, ...))
     */
    private function nativeGetPackageBandwidthLimits(){
        return Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript('package_bwlimit_list.pl');
    }
    
    private function nativeGetBandwidthUsage($publisherId, $year, $month){
        return Aflexi_CdnEnabler_Cpanel_PerlUtils::execScript(
            'user_bwusage_get.pl',
            json_encode(
                array(
                    'user_name' => $publisherId,
                    'month' => $month,
                    'year' => $year
                )
            )
        );
    }

    private function getUserSummaries($users = NULL) {
        $struct = array(
            'summary' => 'DAILY',
            'type' => 'HTTP',
            'startDate' => XML_RPC2_Value::createFromNative(date('Ymd\TH:i:s', strtotime('Y-m-01 00:00:00')), 'datetime'),
            'endDate' => XML_RPC2_Value::createFromNative(date('Ymd\TH:i:s', strtotime('Y-m-t 23:59:59')), 'datetime'),
            'resultFormat' => 'DETAIL'
        );
        $totalHttpResult = $this->getSummaries('Operator', $struct);

        $struct['type'] = 'RTMP';
        $totalRtmpResult = $this->getSummaries('Operator', $struct);

        $userUsage = array();
        if(is_null($users)){
            $users = $this->userHelper->getCdnUsers();
        }
        foreach ($users as $user) {
            $result = self::filterResult($totalHttpResult , $level=4 , array($user['id']));
            $result = self::rebuildResult($result);
            $result = self::sanitizeData($result);
            $userUsage[$user['id']] = $result[-1][2];

            $result = self::filterResult($totalRtmpResult , $level=4 , array($user['id']));
            $result = self::rebuildResult($result);
            $result = self::sanitizeData($result);
            $userUsage[$user['id']] += $result[-1][2];
        }

        return $userUsage;
    }


    private function getPackageSummaries() {
        $struct = array(
            'summary' => 'DAILY',
            'type' => 'HTTP',
            'startDate' => XML_RPC2_Value::createFromNative(date('Ymd\TH:i:s', strtotime('Y-m-01 00:00:00')), 'datetime'),
            'endDate' => XML_RPC2_Value::createFromNative(date('Ymd\TH:i:s', strtotime('Y-m-t 23:59:59')), 'datetime'),
            'resultFormat' => 'DETAIL'
        );
        $totalHttpResult = $this->getSummaries('Operator', $struct);

        $struct['type'] = 'RTMP';
        $totalRtmpResult = $this->getSummaries('Operator', $struct);

        $packages = array();
        $users = $this->userHelper->getCdnUsers();
        foreach ($users as $user) {
            $result = self::filterResult($totalHttpResult , $level=4 , array($user['id']));
            $result = self::rebuildResult($result);
            $result = self::sanitizeData($result);
            @$packages[$user['bandwidthPackage']['name']] = $result[-1][2];

            $result = self::filterResult($totalRtmpResult , $level=4 , array($user['id']));
            $result = self::rebuildResult($result);
            $result = self::sanitizeData($result);
            @$packages[$user['bandwidthPackage']['name']] += $result[-1][2];
        }

        return $packages;
    }

	private function getSummaries($role='OPERATOR' , $struct=array()) {
        $result = $this->xmlRpcClient->execute('stats.getSummaries', array(
            $this->config['operator']['auth']['username'],
            $this->config['operator']['auth']['key'],
            'groupBy' . $role,
            $struct
        ));
		return $result;
	}

	/**
	 * Custom filter for advanced reporting data
	 * TODO: Actually not sure if it is a good idea to do recursive
	 * @param array $data , result
	 * @param integer $level, level of array, to match the IDs
	 * @param array $match, IDs to match
	 * @return array, filtered result
	 */
	static function filterResult($data=array() , $level=1 , $match=array()) {
		$result = array();
		foreach ($data as $k=>$v) {
			if ( ($level == 1) && (in_array($k , $match)) ) {
				$result[$k] = $v;
			}
			elseif ($level > 1) {
				if (is_array($v)) $tmp = self::filterResult($v , $level-1 , $match);
				if (!empty($tmp)) $result[$k] = $tmp;
			}
		}
		return $result;
	}

	/**
	 * Rebuild result with correct sum for total and 1st level (timestamp)
	 * @param array $data , result
	 * @return array, result with new sum
	 */
	static function rebuildResult($data) {
		$result = array();
		$result[-1][-1][-1][-1] = array(0,0,0);
		foreach ($data as $k=>$v) {
			if ($k != -1) {
				$result[$k][-1][-1][-1] = self::buildSum($v);
				$result[-1][-1][-1][-1][0] += $result[$k][-1][-1][-1][0];
				$result[-1][-1][-1][-1][1] += $result[$k][-1][-1][-1][1];
				$result[-1][-1][-1][-1][2] += $result[$k][-1][-1][-1][2];
			}
		}
		return $result;
	}

	/**
	 * Crawl all nodes in array to get sum
	 * TODO: Actually not sure if it is a good idea to do recursive
	 * @param array $data, result
	 * @return array, sum for cached,not cached and total
	 */
	private static function buildSum($data=array()) {
		$sum = array(0,0,0);
		$keys = array_keys($data);
		if (isset($data[0]) && isset($data[1]) && isset($data[2])) {
			$sum[0] += $data[0];
			$sum[1] += $data[1];
			$sum[2] += $data[2];
		}
		else {
			foreach ($data as $k=>$v) {
				if (($k != -1) || ((count($v) >= 1) && !isset($v[0]) && !isset($v[1]) && !isset($v[2])) ) {
					$result = self::buildSum($v);
					$sum[0] += $result[0];
					$sum[1] += $result[1];
					$sum[2] += $result[2];
				}
			}
		}
		return $sum;
	}

	/**
	 * Sanitizes result for wanted data only
	 * @param array $data
	 * @return array
	 */
	static function sanitizeData($data = array()) {
		$depth = self::searchDepth($data) - 1;
		foreach ($data as $timestamp=>&$value) {
			$node = $value;
			for ($x=1; $x<$depth; ++$x) {
				$node = $node[-1];
			}
			$value = $node;
		}
		return $data;
	}

	/**
	 * Search array structure for the deepest depth
	 * @param array $data
	 * @param int $depth, not used in calling
	 * @return int, depth
	 */
	private static function searchDepth($data = array(), $depth = 1) {
		$newDepth = 0;
		foreach ($data as $key=>$value) {
			if (is_array($value)) {
				$tmp = self::searchDepth($value , $depth+1);
				if ($tmp > $newDepth) $newDepth = $tmp;
			}
		}
		if ($newDepth > $depth) $depth = $newDepth;
		return $depth;
	}
}

Aflexi_CdnEnabler_Cpanel_BandwidthHelper::initializeStatic();
