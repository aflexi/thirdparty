<?php

/**
 * Helper to provide control and information over bandwidth usage.
 * 
 * @author yclian
 * @since 2.10.20101013
 */
interface Aflexi_CdnEnabler_Bandwidth_Helper{
    
    /**
     * If bandwidth limit is shared, bandwidth check will be done against on 
     * the sum of non-CDN usage (traditional hosting traffic, e.g. HTTP, FTP, 
     * etc.) and CDN usage.
     * 
     * @param mixed $packageId
     * @return bool
     */
    function isBandwidthLimitShared($packageId);
    
    /**
     * @param mixed $publisherId
     * @param bool $resolvePackageLimit
     * @return float Limit in GB.
     */
    function getBandwidthLimit($publisherId, $resolvePackageLimit = TRUE);
    
    /**
     * @param mixed $packageId
     * @return float Limit in GB.
     */
    function getPackageBandwidthLimit($packageId);
    
    /**
     * @param mixed $publisherId
     * @param bool $resolvePackageLimit
     * @return float Limit in GB.
     */
    function getCdnBandwidthLimit($publisherId, $resolvePackageLimit = TRUE);
    
    /**
     * @param mixed $packageId
     * @return float Limit in GB.
     */
    function getCdnPackageBandwidthLimit($packageId);
    
    /**
     * Get shared bandwidth usage of current month. If bandwidth limit is not 
     * shared, CDN usage shall be returned.
     * 
     * @param mixed $publisherId
     * @param int $year (optional) Specific year.
     * @param int $month (optional) Specific month.
     * @return float Usage in GB.
     */
    function getSharedBandwidthUsage($publisherId, $year = NULL, $month = NULL);
    
    /**
     * Get non-CDN bandwidth usage.
     * 
     * @param mixed $publisherId
     * @param int $year (optional) Specific year.
     * @param int $month (optional) Specific month.
     * @return float Usage in GB.
     */
    function getBandwidthUsage($publisherId, $year = NULL, $month = NULL);
    
    /**
     * @param mixed $publisherId
     * @param int $year (optional) Specific year.
     * @param int $month (optional) Specific month.
     * @return float Usage in GB.
     */
    function getCdnBandwidthUsage($publisherId, $year = NULL, $month = NULL);
    
    /**
     * Set bandwidth limit scheme.
     * 
     * @see #isBandwidthLimitShared()
     * @param mixed $packageId
     * @param bool $shared
     * @return void
     */
    function setBandwidthLimitShared($packageId, $shared = TRUE);
    
    /**
     * Set bandwidth limit for publisher if limit is not equivalent with 
     * package's limit. Otherwise, it shall cancel publisher's temp bandwidth 
     * entries and set the package's limit.
     * 
     * @param mixed $publisherId
     * @param float $limit
     * @return void
     */
    function setCdnBandwidthLimit($publisherId, $limit);
    
    /**
     * @param mixed $packageId
     * @param float $limit
     * @return void
     */
    function setCdnPackageBandwidthLimit($packageId, $limit);
    
    /**
     * Synchronize host's bandwidth limits to Aflexi CDN. If shared scheme is 
     * used, publisher-level limit (to publisher's temporary bandwidth) and 
     * bandwidth-level limit have to be copied over. Otherwise, do nothing.
     * 
     * During this process, limits shall also be checked to perform any 
     * necessary suspension.
     * 
     * @param array $filters (optional) Name of publishers to be excluded.
     * @param array &$outCdnBandwidthData (optional) Associative array of 
     * 	publisher name to bandwidth data (limit, usage, scheme, etc.)
     * @return array array('suspended' => 'created_count');
     */
    function syncBandwidthData($filters = NULL, &$outCdnBandwidthData = NULL);
}
