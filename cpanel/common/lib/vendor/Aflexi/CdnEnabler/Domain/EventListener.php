<?php

/**
 * Event listener interface for host's domain activities and changes.
 * 
 * Domain is a publisher's entity.
 * 
 * @author yclian
 * @since 2.9.20101012
 * @version 2.9.20101012
 */
interface Afleix_CdnEnabler_Domain_EventListener{
    
    /**
     * @param mixed $user
     * @param int|float $before
     * @param string $domain
     * @return void
     */
    function onDomainCreated($user, $domain);
    
    /**
     * @param mixed $user
     * @param string $domain
     * @return void
     */
    function onDomainDeleted($user, $domain);
    
}

?>