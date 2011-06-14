<?php
$context = array();
$notice = array();

$products = getProducts();

$aflexiAddOnProducts = getAddOnProductId();

//Create Aflexi CDN Add-On if none found
if (empty($aflexiAddOnProducts)) {
    insertAflexiAddOn();
    $aflexiAddOnProducts = array();
}

foreach ($products as $id=>&$product) {
    $product['isCDN'] = in_array($id, $aflexiAddOnProducts) ? TRUE : FALSE;
}

if (isset($_POST['submit'])) {

    $postedProducts = $_POST['products'];
    //Update DB
    updateAddOn($postedProducts);
    
    $aflexiPackages = $packageHelper->getPackages();
    $aflexiUsers = $userHelper->getUsers();
    
    foreach ($products as $id=>&$product) {
        if (in_array($id, $postedProducts)) {
            //If product is turned on for CDN
            
            //Sync package
            $product['name'] = empty($product['name']) ? 'default' : $product['name'];
            if (!isset($aflexiPackages[$product['name']])) {
                $aflexiPackageId = $packageHelper->createPackage($product['name']);
            }
            else {
                $aflexiPackageId = $aflexiPackages[$product['name']]['id'];
            }

            triggerCpanelCdn($id);

            //Sync user(s)
            $usersOfProduct = getProductUsers($id, $userHelper->getOperator()->id);
            $localUserPackages = $package->getAll();
            
            foreach ($usersOfProduct as $email=>$user) {
                $username = empty($user['username']) ? emailToUserName($email) : $user['username'];

                if (!isset($aflexiUsers[$username .'/' . $userHelper->getOperator()->id])) {
                    $password = generatePassword($email);
                    $userId = $userHelper->createUser(
                        $email,
                        $user['firstname'] . ', ' . $user['lastname'],
                        $password ,
                        $aflexiPackageId,
                        $username
                    );
                    
                    $publisher->set($username, $password);
                    $package->set($username, $product['name']);
                }
                else {
                    //Update user (actually publisherLink) status to ACTIVE, if previously not active
                    if ($aflexiUsers[$username . '/' . $userHelper->getOperator()->id]['publisherLinkStatus'] != 'ACTIVE') {
                        $userHelper->activateUser($aflexiUsers[$username . '/' . $userHelper->getOperator()->id]['publisherLink']);
                    }
                    
                    //Update to 'new' package if package name is changed
                    if ($localUserPackages[$email] != $product['name']) {
                        $userHelper->updatePublisher(
                            $aflexiUsers[$username . '/' . $userHelper->getOperator()->id]['publisherLink'],
                            $aflexiPackageId
                        );
                        
                        $package->set($username, $product['name']);
                    }
                }
            }
            
            $product['isCDN'] = TRUE;
        }
        else {
            //If the product is turned off
            if ($product['isCDN']) {
                //From on
                
                //Suspend users
                $usersOfProduct = getProductUsers($id, $userHelper->getOperator()->id);
                foreach ($usersOfProduct as $email=>$user) {
                    $username = empty($user['username']) ? emailToUserName($email) : $user['username'];
                    if (isset($aflexiUsers[$username . '/' . $userHelper->getOperator()->id])) {
                        $password = generatePassword($email);
                        $userHelper->suspendUser($aflexiUsers[$username . '/' . $userHelper->getOperator()->id]['publisherLink']);
                    }
                }
            }
            $product['isCDN'] = FALSE;
        }
        
    }
    
//    $notice[] = sprintf(
//        "%d Users and %d packages has been synchronized",
//        $user_affected,
//        $package_affected
//    );
}

$context['products'] = $products;

echo $afx_template->renderTemplate('sync.html', $context);


//Start Helper Area

function insertAflexiAddOn() {
    $query = "
        INSERT INTO `tbladdons`
        SET
            `name`='Aflexi CDN',
            `description`='CDN',
            `billingcycle`='Free Account'
    ";
    mysql_query($query) or die(mysql_error());
    return mysql_insert_id();
}


function getProducts() {
    $query = "
        SELECT `id`, IF( `servertype` <> '' AND `configoption1` <> '', `configoption1` , `name` ) AS `name`, `servertype`, `configoption1`
        FROM `tblproducts`
        WHERE `serverType` != 'aflexi'
    ";
    $result = mysql_query($query) or die($query);
    
    $products = array();
    while ($row = mysql_fetch_assoc($result)) {
        $products[$row['id']] = $row;
    }
    
    return $products;
}

function updateAddOn($products = array(), $addOnName = 'Aflexi CDN') {
    if (!is_array($products)) {
        $products = array();
    }
    
    $products = implode(',', $products);
    $query = sprintf("
        UPDATE `tbladdons`
        SET `packages`='%s'
        WHERE `name`='%s'
    ", mysql_real_escape_string($products)
    , mysql_real_escape_string($addOnName));
    mysql_query($query) or die($query);
}

function getProductUsers($id = 0, $userId = 0) {
    $rt = array();
    
    $query = sprintf("
        SELECT `email`, `firstname`, `lastname`, `tblhosting`.`username` AS `username`
        FROM `tblorders` , `tblclients` , `tblinvoices` , `tblhosting`
        WHERE `tblclients`.`id` = `tblorders`.`userid`
            AND `tblinvoices`.`id` = `tblorders`.`invoiceid`
            AND `tblorders`.`id` = `tblhosting`.`orderid`
            AND  `tblhosting`.`packageid` = %d
            AND `tblinvoices`.`status` = 'Paid'
            AND `tblclients`.`status` = 'Active'
    " , $id);
    $result = mysql_query($query) or die($query);
    
    while ($row = mysql_fetch_assoc($result)) {
        $rt[$row['email']] = $row;
    }
    
    return $rt;
}

?>