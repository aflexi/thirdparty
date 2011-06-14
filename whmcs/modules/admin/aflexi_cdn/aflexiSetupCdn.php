<?php
$errors = array();
$context = array();
$params = array();

try {
    $errors = array();

    installAflexiEmailTemplate();

    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $authkey = $_POST['auth_key'];
        $populate_enabled = isset($_POST['populate_domain']) ? 'yes' :  'no';

        if (verifyCredential($afx_xmlrpc, $username, $authkey)) {

            $callbackUrl = "http://" . $_SERVER['SERVER_NAME'] . substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], 'addonmodules.php'));

            $oauth = Aflexi_CdnEnabler_Core_OAuthHelper::register(
                $username,
                $authkey,
                $config->get('integration_operator') . '/oauth/register?format=json',
                'standard',
                $callbackUrl
            );
            if (!empty($oauth)) {
                $config->set('auth_username', $username);
                $config->set('auth_key', $authkey);
                $config->set('populate_domain', $populate_enabled);
                $config->set('oauth_consumer_key', $oauth['consumer_key']);
                $config->set('oauth_consumer_secret', $oauth['consumer_secret']);
                header('Location: addonmodules.php?module=aflexi_cdn');
            }
            
            else {
                $errors[] = "OAuth registration failed. Please try again";
            }
        }
        else {
            $errors[] = 'Invalid credential. Please try again.';
        }
    }
    
    $params['username'] = $config->get('auth_username');
    $params['auth_key'] = $config->get('auth_key');
    $params['populate_domain'] =  $config->get('populate_domain') == 'yes' ? 'checked' : '';
}
catch (Aflexi_Common_Net_XmlRpcException $xpe) {
    $errors[] =  $xpe->getMessage();
}

if (!empty($errors)) $context['errors'] = $errors;
$context['params'] = $params;

echo $afx_template->renderTemplate('setupAflexiCdn.html', $context);


?>