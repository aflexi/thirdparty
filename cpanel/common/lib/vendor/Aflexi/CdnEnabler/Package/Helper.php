<?php

/**
 * Helper to provide control and information over packages.
 * 
 * @author yclian
 * @since 2.7
 * @version 2.10.20101013
 */
interface Aflexi_CdnEnabler_Package_Helper{
    
    /*
     * Local
     * -------------------------------------------------------------------------
     */
    
    /**
     * Get package of given name.
     * 
     * @param $id
     * @return mixed The native package object.
     */
    function getPackage($id);
    
    /**
     * @param bool $cdnEnabledOnly
     * @return array An associative array of native packages, indexed by 
     * 	package ID. 
     */
    function getPackages($cdnEnabledOnly = FALSE);
    
    /**
     * @param mixed $id
     * @return bool|NULL TRUE or FALSE if specified package is or is not CDN 
     *  enabled. NULL if package doesn't exist.
     */
    function isCdnEnabled($id);
    
    /**
     * @param mixed $id
     * @param bool $enabled
     * @return void
     */
    function setCdnEnabled($id, $enabled = TRUE);
    
    /*
     * Remote
     * -------------------------------------------------------------------------
     */
    
    /**
     * @param mixed $id
     * @return array An associative array representing CDN package.
     */
    function getCdnPackage($id);
    
    /**
     * @param mixed $id
     * @return array An array of CDN packages.
     */
    function getCdnPackages();
    
    /*
     * Sync
     * -------------------------------------------------------------------------
     */
    
    /**
     * Get statuses of local packages, categorized by 'synced', 'unsynced' and
     * 'unqualified'.
     * 
     * @param array $cdnPackages If provided, the function will not attempt to 
     *  retrieve CDN packages from the remote side.
     * @return array
     */
    function getSyncStatuses($cdnPackages = NULL);
    
    /**
     * One-Way synchronize local packages to remote end. Copy of data will also 
     * be written locally.
     * 
     * @param array $filters[optional] Names of packages to be included.
     * @param array &$outCdnPackages[optional] Associative array of CDN package name to 
     *  CDN package. Synchronized CDN packages will be written to this array, 
     *  if provided. These are also the copy of data to be stored locally.
     * @return array array('created'  => $created_count, 'updated' => $updated_count))
     */
    function syncPackages($filters = NULL, &$outCdnPackages = NULL);
}

?>