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
 * @since 2.12
 * @version 2.12.20110127
 */
class Aflexi_CdnEnabler_Cpanel_StatsHelper implements Aflexi_Common_Object_Initializable {
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
     * @var Aflexi_CdnEnabler_Cpanel_UserHelper
     */
    private $userHelper = NULL;

    function __construct(){
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
            $this->userHelper->setConfig($this->getConfig());
            $xmlRpcClient = $this->getXmlRpcClient();
            $this->userHelper->setXmlRpcClient($xmlRpcClient);

            if (file_exists($this->config->getSource('operator'))) {
                $this->userHelper->initialize();

                $runtimeConfig = $this->config['runtime'];
                $runtimeConfig['operator'] = $this->userHelper->getCdnSelfUser();
                $this->config['runtime'] = $runtimeConfig;
            }
        }
        return $this->userHelper;
    }

    function getPackageSummaries() {
        $struct = array(
            'summary' => 'DAILY',
            'type' => 'HTTP',
            'startDate' => XML_RPC2_Value::createFromNative(date('Ymd\TH:i:s', strtotime('Y-m-01 00:00:00')), 'datetime'),
            'endDate' => XML_RPC2_Value::createFromNative(date('Ymd\TH:i:s', strtotime('Y-m-t 23:59:59')), 'datetime'),
            'resultFormat' => 'DETAIL'
        );
        $totalHttpResult = $this->getSummaries('OPERATOR', $struct);

        $struct['type'] = 'RTMP';
        $totalRtmpResult = $this->getSummaries('OPERATOR', $struct);

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
            array(
                'groupBy' . $role,
                $struct
            )
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
}

Aflexi_CdnEnabler_Cpanel_UserHelper::initializeStatic();

?>