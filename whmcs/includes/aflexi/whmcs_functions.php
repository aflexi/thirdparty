<?php

/**
 * 
 * Generate random password 
 * @param string $key
 * @param int $length
 */
function generatePassword($key = '' , $length = 8) {
    $password = str_split($key . time());
    shuffle($password);
    $password = substr(base64_encode(implode('' , $password)) , 0 , $length);
    return $password;
}

/**
 * 
 * Return list of product Ids in invoice
 * @param int $invoiceId
 */
function getInvoiceProductId($invoiceId = 0) {
    if (is_array($invoiceId)) {
        $invoiceId = $invoiceId['invoiceid'];
    }
    
    $products = array();
    
    $query = "
        SELECT `packageid` 
        FROM `tblhosting`, `tblorders`
        WHERE `tblorders`.`id` = `tblhosting`.`orderid`
            AND `tblorders`.`invoiceid`=$invoiceId 
    ";
    $result = mysql_query($query) or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) {
        $products[] = $row['packageid'];
    }
    
    return $products;
}

/**
 * 
 * Return list of products tied to a given addOn name
 * @param string $addOnName
 */
function getAddOnProductId($addOnName = 'Aflexi CDN') {
    $packages = array();
    
    $query = sprintf("
        SELECT `packages`
        FROM `tbladdons`
        WHERE `name`='%s'
    ", mysql_real_escape_string($addOnName));
    $result = mysql_query($query) or die(mysql_error());
    if (mysql_num_rows($result)) {
        $packages = mysql_fetch_assoc($result);
        $packages = explode(',', $packages['packages']);
    }
    return $packages;
}

/**
 * 
 * Check if any product has given addOn
 * @param array $products
 * @param array $cdnProduct
 */
function isAddOnProduct($products = array(), $addOnProducts = array()) {
    $productCount = count($products);
    for ($x=0; $x<$productCount; ++$x) {
        if (in_array($products[$x] , $addOnProducts)) {
            return TRUE;
        }
    }
    return FALSE;
}

function verifyCredential($afx_xmlrpc, $username, $authkey) {
    try {
        $afx_operator = $afx_xmlrpc->execute('user.get', array(
            $username,
            $authkey,
            array(
                'self' => TRUE
            )
        ));
    }
    catch (Exception $e) {
        return FALSE;
    }

    if (count($afx_operator['results'])) {
        $afx_operator = $afx_operator['results'][0];
        if ($afx_operator['role'] == 'OPERATOR') {
            return $afx_operator;
        }
    }
    return FALSE;
}

//For PHP version < 5.2.1, needed by TwigFactory
//Grab from http://www.php.net/manual/en/function.sys-get-temp-dir.php#94119
//author php@ktools.eu
if ( !function_exists('sys_get_temp_dir')) {
    function sys_get_temp_dir() {
      if( $temp=getenv('TMP') )        return $temp;
      if( $temp=getenv('TEMP') )        return $temp;
      if( $temp=getenv('TMPDIR') )    return $temp;
      $temp=tempnam(__FILE__,'');
      if (file_exists($temp)) {
          unlink($temp);
          return dirname($temp);
      }
      return null;
    }
}

function emailToUserName($email) {
    return str_replace(
        array('+','.','@'),
        '_',
        $email
    );
}


function getCpanelServerDetails($productId = 0) {
    $query = sprintf("
        SELECT `tblservers`.`hostname` AS `hostname`, `tblservers`.`username` AS `username`, `tblservers`.`password` AS `password`, IF(`configoption1` <> '', `configoption1` , 'default' ) AS `name`
        FROM `tblproducts`, `tblhosting`
        LEFT OUTER JOIN `tblservers` ON `tblhosting`.`server` = `tblservers`.`id`
        WHERE `tblproducts`.`id` = `tblhosting`.`packageid`
            AND `tblservers`.`id` = `tblhosting`.`server`
            AND `tblproducts`.`id` = %d
            AND `tblproducts`.`serverType` = 'cpanel'
    ", $productId);
    $result = mysql_query($query) or die($query);

    $return = array();
    while ($row = mysql_fetch_assoc($result)) {
        $row['password'] = decrypt($row['password']);
        $return[] = $row;
    }
    return $return;
}

function getCpanelPackages($cpanelDetails) {
    $whmusername = @$cpanelDetails['username'];
    $whmpassword = @$cpanelDetails['password'];
    $whmhostname = @$cpanelDetails['hostname'];

    $query = "https://{$whmhostname}:2087/json-api/listpkgs";

    $curl = curl_init($query);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $header = array();
    $header[] = "Authorization: Basic " . base64_encode($whmusername.":".$whmpassword) . "\n\r";
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    $result = curl_exec($curl);
    curl_close($curl);

    $result = @json_decode($result, TRUE);
    return $result['package'];
}

function matchCpanelPackage($packages = array(), $name = '') {
    foreach ($packages as $package) {
        if (strtolower($package['name']) == strtolower($name)) {
            return $package;
        }
    }
    return NULL;
}

function createCpanelFeatureList($cpanelDetails = array(), $featureName = '') {
    $whmusername = @$cpanelDetails['username'];
    $whmpassword = @$cpanelDetails['password'];
    $whmhostname = @$cpanelDetails['hostname'];

    $s = file_get_contents("http://{$whmusername}:{$whmpassword}@{$whmhostname}:2086/scripts2/dofeaturemanager?action=addfeature&feature=" . urlencode($featureName));
    preg_match_all('/<input\s*type="checkbox"\s*(checked)?\s*name="([^"]+)"[^>]+>/' , $s , $matches);

    $ch = curl_init("http://{$whmusername}:{$whmpassword}@{$whmhostname}:2086/scripts2/savefeatures");
    $post = array(
        'feature' => $featureName
    );
    foreach ($matches[2] as $k=>$name) {
        if ($matches[1][$k] == 'checked') {
            $post[$name] = 1;
        }
    }
    $post['cdn'] = 1;
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_exec($ch);
}

function editCpanelPackage($cpanelDetails = array()) {
    $whmusername = @$cpanelDetails['username'];
    $whmpassword = @$cpanelDetails['password'];
    $whmhostname = @$cpanelDetails['hostname'];

    $query = "https://{$whmhostname}:2087/json-api/editpkg?name=" . urlencode($cpanelDetails['name']) . "&featurelist=" . urlencode($cpanelDetails['name']);

    $curl = curl_init($query);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $header = array();
    $header[] = "Authorization: Basic " . base64_encode($whmusername.":".$whmpassword) . "\n\r";
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    $result = curl_exec($curl);
    curl_close($curl);

    $result = @json_decode($result, TRUE);
    return $result;

}

function createCpanelPackage($cpanelDetails = array()) {
    $whmusername = @$cpanelDetails['username'];
    $whmpassword = @$cpanelDetails['password'];
    $whmhostname = @$cpanelDetails['hostname'];

    $query = "https://{$whmhostname}:2087/json-api/addpkg?name=" . urlencode($cpanelDetails['name']) . "&featurelist=" . urlencode($cpanelDetails['name']);

    $curl = curl_init($query);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $header = array();
    $header[] = "Authorization: Basic " . base64_encode($whmusername.":".$whmpassword) . "\n\r";
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    $result = curl_exec($curl);
    curl_close($curl);

    $result = @json_decode($result, TRUE);
    return $result;

}

function triggerCpanelCdn($productId = 0) {
    $cpanelDetails = getCpanelServerDetails($productId);
    foreach ($cpanelDetails as $cpanelDetail) {
        $cpanelPackages = getCpanelPackages($cpanelDetail);
        $matchedPackage = matchCpanelPackage($cpanelPackages, $cpanelDetail['name']);
        if (!empty($matchedPackage)) {
            if (strtolower($matchedPackage['FEATURELIST']) != strtolower($cpanelDetail['name'])) {
                createCpanelFeatureList($cpanelDetail, $matchedPackage['FEATURELIST']);
            }
            editCpanelPackage($cpanelDetail);
        }
        else {
            createCpanelFeatureList($cpanelDetail, $cpanelDetail['name']);
            createCpanelPackage($cpanelDetail);
        }
    }
}

function installAflexiEmailTemplate() {
/** $template = <<<EOF
{php}
    require(dirname(__FILE__) . '/../includes/aflexi/bootstrap.php');
    \$theme = \$userHelper->getThemability();
    \$portalUrl =
        (!empty(\$theme['portalUrl']) && (\$theme['portalUrl'] != 'http://portal.aflexi.net')) ?
        \$theme['portalUrl'] :
        "http://portal.aflexi.net/p/" . ((!empty(\$theme['portalName'])) ? \$theme['portalName'] : \$userHelper->getOperator()->id);

{/php}

<p>Hello {\$client_name} ({\$service_username})</p>

<p>Welcome to Aflexi. We are glad to have you to be a part of the Collaborative CDN! You can now access the portal at:</p>

<p><a href="{php}echo \$portalUrl;{/php}">{php}echo \$portalUrl;{/php}</a></p>


<p>
Below is your login credentials.
</p>

<p>
Login Id: {\$service_username}<br />
Password: {\$service_password}
</p>
EOF;
**/
//NOTE [yasir 20110609] The logic we move in separate file, for reduce encoding problme from text editor.
 $template = <<<EOF
{php}require(dirname(__FILE__) . '/../includes/aflexi/bootstrap.php'){/php}

{php}require(dirname(__FILE__) . '/../includes/aflexi/email_template_configure.php'){/php}

<p>Hello {\$client_name} ({\$service_username})</p>

<p>Welcome to Aflexi. We are glad to have you to be a part of the Collaborative CDN! You can now access the portal at:</p>

<p><a href="{php}echo \$portalUrl;{/php}">{php}echo \$portalUrl;{/php}</a></p>


<p>
Below is your login credentials.
</p>

<p>
Login Id: {\$service_username}<br />
Password: {\$service_password}
</p>
EOF;

    $query = sprintf("
        SELECT `id`
        FROM `tblemailtemplates`
        WHERE `name`='Aflexi CDN Welcome Mail'
    ");
    $result = mysql_query($query) or die($query);

    if (mysql_num_rows($result) == 0) {
        $query = sprintf("
            INSERT INTO `tblemailtemplates`
            SET
                `id`=NULL,
                `type`='product',
                `name`='Aflexi CDN Welcome Mail',
                `subject`='Welcome To Aflexi CDN',
                `message`='%s',
                `fromname`='notification',
                `fromemail`='notification@aflexi.net',
                `disabled`='',
                `custom`='1',
                `language`='',
                `copyto`='',
                `plaintext`=0
        ", mysql_real_escape_string($template));
        mysql_query($query) or die($query);
    }
    else {
        $query = sprintf("
            UPDATE `tblemailtemplates`
            SET `message`='%s'
            WHERE `name`='Aflexi CDN Welcome Mail'
        ", mysql_real_escape_string($template));
        mysql_query($query) or die($query);
    }
}
?>