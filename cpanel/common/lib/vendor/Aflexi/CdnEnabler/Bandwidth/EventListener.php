<?php

/**
 * Event listener interface for host's bandwidth activities and changes.
 * 
 * @author yclian
 * @since 2.9.20101012
 * @version 2.9.20101012
 */
interface Aflexi_CdnEnabler_Bandwidth_EventListener{
    
    /**
     * @param mixed $user
     * @param int|float $before
     * @param int|float $after
     * @return void
     */
    function onUserBandwidthUpdated($user, $before, $after);
    
    /**
     * @param mixed $package
     * @param int|float $before In GB.
     * @param int|float $after In GB.
     * @return void
     */
    function onPackageBandwidthUpdated($package, $before, $after);
    
}

?>