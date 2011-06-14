<?php

/**
 * Helper to provide control and information over users.
 * 
 * @author yingfan
 * @since 2.10
 * @version 2.10.20101013
 */
interface Aflexi_CdnEnabler_User_Helper{
    
    /*
     * Local
     * -------------------------------------------------------------------------
     */
    
    /**
     * Get user of given name.
     * 
     * @param $id
     * @return mixed The native package object.
     */
    function getUser($name);
    
    /**
     * @param bool $cdnEnabledOnly
     * @return array An associative array of native packages, indexed by 
     * 	package ID. 
     */
    function getUsers($cdnEnabledOnly = FALSE);
    
    /**
     * @param mixed $id
     * @return bool|NULL TRUE or FALSE if specified user is or is not CDN 
     *  enabled. NULL if user doesn't exist.
     */
    function isCdnEnabled($name);
    
    /**
     * @param mixed $id
     * @param bool $enabled
     * @return void
     */
    function setCdnEnabled($name, $enabled = TRUE);
    
    /*
     * Remote
     * -------------------------------------------------------------------------
     */
    
    /**
     * @param mixed $id
     * @return array An associative array representing CDN user.
     */
    function getCdnUser($name);
    
    /**
     * @param mixed $id
     * @return array An array of CDN users.
     */
    function getCdnUsers();
    
    /*
     * Sync
     * -------------------------------------------------------------------------
     */
    
    /**
     * Get statuses of local users, categorized by 'synced', 'unsynced' and
     * 'unqualified'.
     * 
     * @param array $cdnUsers If provided, the function will not attempt to 
     *  retrieve CDN users from the remote side.
     * @return array
     */
    function getSyncStatuses($cdnPackages = NULL);
    
    /**
     * One-Way synchronize local users to remote end. Copy of data will also 
     * be written locally.
     * 
     * @param array $filters[optional] Names of users to be included.
     * @param array &$outCdnUsers[optional] Associative array of CDN user name to 
     *  CDN user. Synchronized CDN packages will be written to this array, 
     *  if provided. These are also the copy of data to be stored locally.
     * @return array array('created'  => $created_count, 'updated' => $updated_count))
     */
    function syncUsers($filters = NULL, &$outCdnUsers = NULL);
}

?>