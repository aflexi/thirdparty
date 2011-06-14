<?php

if (file_exists($filename = dirname(__FILE__) . '/../../../includes/aflexi/bootstrap.php')) {
    require_once($filename);
}

if  (
        (!$config->isTableSetup()) || 
        (!isset($userHelper)) ||
        ((isset($_GET['action'])) && (trim(strtolower($_GET['action'])) == 'setup')) 
    ) {
    //Access the setup page, either when table is not setup, or via action=setup query string
    //Setup aflexi cdn
    include('aflexiSetupCdn.php');
}
elseif ((isset($_GET['action'])) && (trim(strtolower($_GET['action'])) == 'showpublishers')) {
    //Show Synchronized Publishers
    include('aflexiShowPublishers.php');
}
elseif ((isset($_GET['action'])) && (trim(strtolower($_GET['action'])) == 'listpackages')) {
    //Show Aflexi Packages
    include('aflexiListPackages.php');
}
elseif ((isset($_GET['action'])) && (trim(strtolower($_GET['action'])) == 'editpackage')) {
    //Edit aflexi package (via iframe)
    include('aflexiEditPackage.php');
}
elseif ((isset($_GET['action'])) && (trim(strtolower($_GET['action'])) == 'callback')) {
    //Edit aflexi package (via iframe)
    include('aflexiCallback.php');
}
else {
    //Sync interface
    include('aflexiSync.php');
}
?>
