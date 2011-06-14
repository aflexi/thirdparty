<?php
error_reporting(E_ERROR);
define('CPANEL_AFX_LIB_PARENT', dirname(__FILE__).'/../../../../../3rdparty/aflexi/lib');

set_include_path(
    CPANEL_AFX_LIB_PARENT.'/main:'.
    CPANEL_AFX_LIB_PARENT.'/vendor:'.
    get_include_path()
);

try{
    require_once CPANEL_AFX_LIB_PARENT.'/main/Aflexi/CdnEnabler/Cpanel/Operator/Mvc.php';
    $front = new Aflexi_CdnEnabler_Cpanel_Operator_Mvc();
    $front->initialize()->getFrontController()->dispatch();

} catch(Exception $e){
    $_REQUEST['error_message'] = $e->getMessage();
    include dirname(__FILE__).'/error.php';
}

?>