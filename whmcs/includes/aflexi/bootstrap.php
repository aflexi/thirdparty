<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
require_once(dirname(__FILE__) . '/lib/Aflexi/CdnEnabler.php');
require_once('whmcs_functions.php');
require_once('includes/xmlrpc.php');
require_once('includes/functions.php');
require_once('includes/commons.php');

Aflexi_Common_Template_TemplateFactory::setInstance(
    new Aflexi_Common_Template_TwigFactory(dirname(__FILE__).'/templates')
);

$afx_template = Aflexi_Common_Template_TemplateFactory::getInstance();


$afx_template_context = array(
    'params' => $_REQUEST,
    'user' => isset($_ENV['REMOTE_USER']) ? $_ENV['REMOTE_USER'] : NULL,
    'form_action' => $_SERVER['PHP_SELF'],
    'info' => '',
    'errors' => array(),
);


$afx_config_main;
afx_config_load(array(
    'afx_config_main' => dirname(__FILE__).'/config.yml'
));


$afx_xmlrpc = afx_xmlrpc_client();

$config = new Aflexi_CdnEnabler_Model_ConfigTable();
$publisher = new Aflexi_CdnEnabler_Model_PublisherTable();
$package = new Aflexi_CdnEnabler_Model_PackageTable();

if ($config->get('db_schema') != Aflexi_CdnEnabler_Model_Table::DB_SCHEMA) {
    $config->upgradeSchema($config->get('db_schema'));
    $publisher->upgradeSchema($config->get('db_schema'));
    $package->upgradeSchema($config->get('db_schema'));
    $config->set('db_schema', Aflexi_CdnEnabler_Model_Table::DB_SCHEMA);
}

if (verifyCredential($afx_xmlrpc, $config->get('auth_username'), $config->get('auth_key'))) {
    $userHelper = new Aflexi_CdnEnabler_Core_XmlRpcUserHelper($config->get('auth_username'), $config->get('auth_key'), $afx_xmlrpc);
    $packageHelper = new Aflexi_CdnEnabler_Core_XmlRpcPackageHelper($config->get('auth_username'), $config->get('auth_key'), $afx_xmlrpc);
}
?>