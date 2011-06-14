<?php
//Start Hook area
add_hook("InvoicePaid", 1, "aflexi_invoice_paid_sync");
add_hook("ClientEdit", 1, "aflexi_client_edit_sync");
add_hook("ClientClose", 1, "aflexi_client_close_or_delete_sync");
add_hook("ClientDelete", 1, "aflexi_client_close_or_delete_sync");

add_hook("ManualRefund", 1, "aflexi_manual_refund_sync");
add_hook("InvoiceUnpaid", 1, "aflexi_invoice_unpaid_or_cancelled_or_refund_sync");
add_hook("InvoiceCancelled", 1, "aflexi_invoice_unpaid_or_cancelled_or_refund_sync");
add_hook("InvoiceRefunded", 1, "aflexi_invoice_unpaid_or_cancelled_or_refund_sync");

//End Hook area



//Start Hook Action
function aflexi_invoice_paid_sync($invoiceId) {
    if (is_array($invoiceId)) $invoiceId = $invoiceId['invoiceid'];
    require_once(dirname(__FILE__) . '/../aflexi/bootstrap.php');
    
    if (isset($userHelper)) {    
        $products = getInvoiceProductId($invoiceId);
        $cdnProducts = getAddOnProductId();
        
        if (isAddOnProduct($products, $cdnProducts)) {
            $aflexiPackages = $packageHelper->getPackages();
            $aflexiUsers = $userHelper->getUsers();

            $details = getInvoiceDetails($invoiceId);
            $username = empty($details['username']) ? emailToUserName($details['email']) : $details['username'];

            //Create package
            $details['name'] = empty($details['name']) ? 'default' : $details['name'];
            if (!isset($aflexiPackages[$details['name']])) {
                $aflexiPackageId = $packageHelper->createPackage($details['name']);
            }
            else {
                $aflexiPackageId = $aflexiPackages[$details['name']]['id'];
            }

            foreach ($products as $productId) {
                triggerCpanelCdn($productId);
            }
            
            //Create user
            if (!isset($aflexiUsers[$username . '/' . $userHelper->getOperator()->id])) {
                $password = generatePassword($username);
                $userId = $userHelper->createUser(
                    $details['email'],
                    $details['firstname'] . ', ' . $details['lastname'],
                    $password,
                    $aflexiPackageId,
                    $username
                );
                
                $publisher->set($username, $password);
                $package->set($username, $details['name']);
            }
            else {
                //Update user (actually publisherLink) status to ACTIVE, if previously not active
                if ($aflexiUsers[$username . '/'  . $userHelper->getOperator()->id]['publisherLinkStatus'] != 'ACTIVE') {
                    $userHelper->activateUser($aflexiUsers[$username . '/' . $userHelper->getOperator()->id]['publisherLink']);
                }
                
                //Update to 'new' package if package name is changed
                $localUserPackages = $package->getAll();
                if ($localUserPackages[$username] != $product['name']) {
                    $userHelper->updatePublisher(
                        $aflexiUsers[$username . '/' . $userHelper->getOperator()->id]['publisherLink'],
                        $aflexiPackageId
                    );
                    
                    $package->set($username, $details['name']);
                }
            }
        }
    }
}

function aflexi_client_edit_sync($params) {
    require_once(dirname(__FILE__) . '/../aflexi/bootstrap.php');
    
    if (isset($userHelper)) {
        $aflexiUsers = $userHelper->getUsers();
        
        if (($params['firstname'] != $params['olddata']['firstname']) ||
            ($params['lastname'] != $params['olddata']['lastname']) ||
            ($params['email'] != $params['olddata']['email'])) {
            
            if (isset($aflexiUsers[emailToUserName($params['olddata']['email']) . '/' . $userHelper->getOperator()->id])) {
                $user = 
                    $userHelper->getOperator()->email . '?su=' .         //operator email
                    emailToUserName($params['olddata']['email']) . '/' .                  //publisher email
                    $userHelper->getOperator()->id;                     //operator id

                $userHelper->updatePublisher(
                    $aflexiUsers[emailToUserName($params['olddata']['email']) . '/' . $userHelper->getOperator()->id]['id'],
                    array(
                        'email' => $params['email'],
                        'name' => $params['firstname'] . ', ' . $params['lastname']
                    ),
                    $user
                );
            }
        }
        
        if (($params['status'] != 'Active') &&
        ('Active' == $params['olddata']['status'])) {
            $userHelper->suspendUser($aflexiUsers[emailToUserName($params['email']) . '/' . $userHelper->getOperator()->id]['publisherLink']);
        }
        elseif (($params['status'] == 'Active') &&
        ('Active' != $params['olddata']['status'])) {
            $userHelper->activateUser($aflexiUsers[emailToUserName($params['email']) . '/' . $userHelper->getOperator()->id]['publisherLink']);
        }
    }
}

function aflexi_client_close_or_delete_sync($userId) {
    if (is_array($userId)) $userId = $userId['userid'];
    require_once(dirname(__FILE__) . '/../aflexi/bootstrap.php');
    if (isset($userHelper)) {
        $user = getUser($userId);
        $aflexiUsers = $userHelper->getUsers();

        if (isset($aflexiUsers[emailToUserName($user['email']) . '/' . $userHelper->getOperator()->id])) {
            $userHelper->suspendUser($aflexiUsers[emailToUserName($user['email']) . '/' . $userHelper->getOperator()->id]['publisherLink']);
        }

        $query = sprintf("
            SELECT `username`
            FROM `tblhosting`
            WHERE `userid` = %d
        ", $userId);
        $result = mysql_query($query) or die($query);
        while ($row = mysql_fetch_assoc($result)) {
            if (isset($aflexiUsers[$row['username'] . '/' . $userHelper->getOperator()->id])) {
                $userHelper->suspendUser($aflexiUsers[$row['username'] . '/' . $userHelper->getOperator()->id]['publisherLink']);
            }
        }
    }
}

function aflexi_invoice_unpaid_or_cancelled_or_refund_sync($invoiceId) {
    if (is_array($invoiceId)) $invoiceId = $invoiceId['invoiceid'];
    require_once(dirname(__FILE__) . '/../aflexi/bootstrap.php');
    
    if (isset($userHelper)) {    
        $products = getInvoiceProductId($invoiceId);
        $cdnProducts = getAddOnProductId();
        
        if (isAddOnProduct($products, $cdnProducts)) {
            $aflexiPackages = $packageHelper->getPackages();
            $aflexiUsers = $userHelper->getUsers();
            
            $details = getInvoiceDetails($invoiceId);
            $username = empty($details['username']) ? emailToUserName($details['email']) : $details['username'];

            //Suspend user
            if (isset($aflexiUsers[$username . '/' . $userHelper->getOperator()->id])) {
                $userHelper->suspendUser($aflexiUsers[$username . '/' . $userHelper->getOperator()->id]['publisherLink']);
            }
        }
    }
}
//End Hook Action



//Start Helper area
function getInvoiceDetails($invoiceId) {
    $rt = array();

    if (is_array($invoiceId)) {
        $invoiceId = $invoiceId['invoiceid'];
    }
    
    $query = sprintf("
        SELECT `email` , `firstname` , `lastname` , IF( `servertype` <> '' AND `configoption1` <> '', `configoption1` , `tblproducts`.`name` ) AS `name`, `tblhosting`.`username` AS `username`
        FROM `tblorders` , `tblclients` , `tblinvoices` , `tblproducts` , `tblhosting`
        WHERE `tblclients`.`id` = `tblorders`.`userid`
            AND `tblinvoices`.`id` = `tblorders`.`invoiceid`
            AND `tblorders`.`id` = `tblhosting`.`orderid`
            AND `tblproducts`.`id` = `tblhosting`.`packageid`
            AND `tblinvoices`.`id` = %d
    ", $invoiceId);
    $result = mysql_query($query) or die($query);
    
    if (mysql_num_rows($result) > 0) {
        $row = mysql_fetch_assoc($result);
        return $row;
    }
    return NULL;
}

function getUser($userId) {
    $query = sprintf("
        SELECT `firstname`, `lastname`, `email`
        FROM `tblclients`
        WHERE `tblclients`.`id` = %d
    ", $userId);
    $result = mysql_query($query) or die($query);
    
    if (mysql_num_rows($result) > 0) {
        $row = mysql_fetch_assoc($result);
        return $row;
    }
    return NULL;
}
//End Helper area
?>