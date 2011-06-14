<?php

/**
 * Event listener interface for host's package activities and changes.
 * 
 * @author yclian
 * @since 2.9.20101012
 * @version 2.9.20101012
 */
interface Aflexi_CdnEnabler_Package_EventListener{
    
    /**
     * @param mixed $package
     * @return void
     */
    function onPackageCreated($package);
    
    /**
     * Handle a package when it is being updated, e.g. being renamed, CDN 
     * feature disabled, etc.
     * 
     * @param mixed $package1
     * @param mixed $package2
     * @return void
     */
    function onPackageUpdated($package1, $package2);
    
    /**
     * @param mixed $package1
     * @return void
     */
    function onPackageDeleted($package1);
}

?>