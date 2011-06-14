<?php

/**
 * Event listener interface for host's user activities and changes.
 * 
 * @author yclian
 * @since 2.9.20101012
 * @version 2.9.20101012
 */
interface Aflexi_CdnEnabler_User_EventListener{
    
    /**
     * @param mixed $user
     * @return void
     */
    function onUserCreated($user);
    
    /**
     * @param mixed $user1
     * @param mixed $user2
     * @return void
     */
    function onUserUpdated($user1, $user2);
    
    /**
     * @param mixed $user1
     * @return void
     */
    function onUserDeleted($user1 = null);
    
    /**
     * @param mixed $user
     * @param mixed $package1
     * @param mixed $package2
     * @return void
     */
    function onUserXgraded($user, $package1, $package2);
}

?>