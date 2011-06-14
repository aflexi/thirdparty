<?php

/**
 *
 * Configure email template
 * @author yasir
 * @since 2.16.20110609
 * @version 2.16.20110609
 */

$theme = $userHelper->getThemability();
if(!empty($theme['portalUrl']) && ($theme['portalUrl'] != 'http://portal.aflexi.net')){
          $portalUrl = $theme['portalUrl'];
}else{
    if(!empty($theme['portalName'])){
        $portalUrl = "http://portal.aflexi.net/p/".$theme['portalName'];
     }else{
        $portalUrl = "http://portal.aflexi.net/p/".$userHelper->getOperator()->id;
    }
}
?>