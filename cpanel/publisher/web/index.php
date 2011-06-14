<?php
error_reporting(E_ERROR);
define('CPANEL_AFX_LIB_PARENT', dirname(__FILE__).'/includes');

set_include_path(
    CPANEL_AFX_LIB_PARENT.'/main:'.
    CPANEL_AFX_LIB_PARENT.'/vendor:'.
    get_include_path()
);

try{
    require_once CPANEL_AFX_LIB_PARENT.'/main/Aflexi/CdnEnabler/Cpanel/Publisher/Mvc.php';
    $front = new Aflexi_CdnEnabler_Cpanel_Publisher_Mvc();
//    $logger = Aflexi_Common_Log_LoggerFactory::getLogger(__CLASS__);
//    $logger->setStorage(dirname(__FILE__) . '/../app.log');
    unset($_ENV['REMOTE_USER']);
    $front->initialize()->getFrontController()->dispatch();

} catch(Exception $e){
    $_REQUEST['error_message'] = $e->getMessage();
    include dirname(__FILE__).'/error.php';
}

?>