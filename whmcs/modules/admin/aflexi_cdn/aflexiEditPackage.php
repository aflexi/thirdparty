<?php
$context['packageId'] = $packageId = (int) $_REQUEST['id'];
$oauth_token = Aflexi_CdnEnabler_Core_OAuthHelper::request(
    $config->get('oauth_consumer_key'),
    $config->get('oauth_consumer_secret'),
    $config->get('integration_operator') . '/oauth/request'
);

$context['iframe_url'] = $config->get('integration_operator') . "/package/edit/id/{$packageId}?app=whmcs&oauth_token={$oauth_token}&auth_username=" . $config->get('auth_username');


echo $afx_template->renderTemplate('editPackage.html', $context);
?>