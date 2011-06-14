<?php
function aflexi_ConfigOptions() {
	# Should return an array of the module options for each product - maximum of 24
    $configarray = array(
	 "Package name" => array( "Type" => "text", "Size" => "25", )
	);
	return $configarray;
}

function aflexi_CreateAccount($params) {
    require(dirname(__FILE__) . '/../../../includes/aflexi/bootstrap.php');
    
    if (isset($userHelper)) {    

        # ** The variables listed below are passed into all module functions **

        $serviceid = $params["serviceid"]; # Unique ID of the product/service in the WHMCS Database
        $pid = $params["pid"]; # Product/Service ID
        $producttype = $params["producttype"]; # Product Type: hostingaccount, reselleraccount, server or other
        $domain = $params["domain"];
	    $username = $params["username"];
	    $password = $params["password"];
        $clientsdetails = $params["clientsdetails"]; # Array of clients details - firstname, lastname, email, country, etc...
        $customfields = $params["customfields"]; # Array of custom field values for the product
        $configoptions = $params["configoptions"]; # Array of configurable option values for the product

        # Product module option settings from ConfigOptions array above
        $packageName = $params["configoption1"];
        $configoption2 = $params["configoption2"];
        $configoption3 = $params["configoption3"];
        $configoption4 = $params["configoption4"];

        # Additional variables if the product/service is linked to a server
        $server = $params["server"]; # True if linked to a server
        $serverid = $params["serverid"];
        $serverip = $params["serverip"];
        $serverusername = $params["serverusername"];
        $serverpassword = $params["serverpassword"];
        $serveraccesshash = $params["serveraccesshash"];
        $serversecure = $params["serversecure"]; # If set, SSL Mode is enabled in the server config

        # Code to perform action goes here...
        $successful = TRUE;
        try {
            $aflexiPackages = $packageHelper->getPackages();
            $aflexiUsers = $userHelper->getUsers();
            
            $details = getUserByHostingId($serviceid);
            $auto_populate = $config->get('populate_domain');
            

            $packageDetails = getProductByHostingId($serviceid);


            //Use the package name specified in the module options, instead of the product name itself
            if (!empty($packageName)) {
                $packageDetails['name'] = $packageName;
            }

            if (empty($username)) {
                if (!empty($details['email'])) {
                    $username = emailToUserName($details['email']);
                }
            }


            if (!empty($username) && !empty($password) && !empty($details['email'])) {
                $username .= "_{$serviceid}";
                updateUsername($serviceid, $username);
                    
                // [yasir 20110609] Update domain, using value of username.
                if( $auto_populate == "yes"){
                    updateDomain($serviceid, $username);
                }
                //Create package
                if (!isset($aflexiPackages[$packageDetails['name']])) {
                    $aflexiPackageId = $packageHelper->createPackage($packageDetails['name']);
                }
                else {
                    $aflexiPackageId = $aflexiPackages[$packageDetails['name']]['id'];
                }
                $package->set($username, $packageDetails['name']);

                if (!isset($aflexiUsers[$username . '/' . $userHelper->getOperator()->id])) {
                    $userId = $userHelper->createUser(
                        $details['email'],
                        $details['firstname'] . ', ' . $details['lastname'],
                        $password,
                        $aflexiPackageId,
                        $username
                    );

                    $publisher->set($username, $password);
                }
                else {
                    if ($aflexiUsers[$username . '/' . $userHelper->getOperator()->id]['publisherLinkStatus'] != 'ACTIVE') {
                        $userHelper->activateUser($aflexiUsers[$username . '/' . $userHelper->getOperator()->id]['publisherLink']);
                    }


                    $userHelper->updatePublisher(
                        $aflexiUsers[$username . '/' . $userHelper->getOperator()->id]['publisherLink'],
                        $aflexiPackageId
                    );

                    $package->set($username, $product['name']);
                }

            }
            else {
                throw new Exception('Username, password, or email is empty');
            }
        }
        catch (Exception $e) {
            $successful = FALSE;
            $errorMsg = $e->getMessage();
        }
	    

	    if ($successful) {
		    $result = "success";
	    } else {
		    $result = "Error - $errorMsg";
	    }
	
	}
	else {
	    $result = "Error - Failed to bootstrap Aflexi CDN Plugin";
	}
	return $result;
}

function aflexi_SuspendAccount($params) {
    require(dirname(__FILE__) . '/../../../includes/aflexi/bootstrap.php');

    $serviceid = $params["serviceid"];
    $pid = $params["pid"];

	# Code to perform action goes here...
    $successful = TRUE;
    if (isset($userHelper)) {
        try {
            $user = getUserByHostingId($serviceid);
            $aflexiUsers = $userHelper->getUsers();
            $operator = $userHelper->getOperator();

            if (isset($aflexiUsers[$user['username'] . '/' . $operator->id])) {
                $userHelper->suspendUser($aflexiUsers[$user['username'] . '/' . $operator->id]['publisherLink']);
            }
        }
        catch (Exception $e) {
            $successful = FALSE;
            $errorMsg = $e->getMessage();
        }
    }

    if ($successful) {
		$result = "success";
	} else {
		$result = "Error - $errorMsg";
	}
	return $result;
}

function aflexi_UnsuspendAccount($params) {
    require(dirname(__FILE__) . '/../../../includes/aflexi/bootstrap.php');

    $serviceid = $params["serviceid"];
    $pid = $params["pid"];

	# Code to perform action goes here...
    $successful = TRUE;
    if (isset($userHelper)) {
        try {
            $user = getUserByHostingId($serviceid);
            $aflexiUsers = $userHelper->getUsers();
            $operator = $userHelper->getOperator();

            if (isset($aflexiUsers[$user['username'] . '/' . $operator->id])) {
                $userHelper->activateUser($aflexiUsers[$user['username'] . '/' . $operator->id]['publisherLink']);
            }
        }
        catch (Exception $e) {
            $successful = FALSE;
            $errorMsg = $e->getMessage();
        }
    }

    if ($successful) {
		$result = "success";
	} else {
		$result = "Error - $errorMsg";
	}
	return $result;
}


function aflexi_ChangePassword($params) {
    require(dirname(__FILE__) . '/../../../includes/aflexi/bootstrap.php');

    $serviceid = $params["serviceid"];
	$username = $params["username"];
	$password = $params["password"];

    $successful = TRUE;
    
    if (isset($userHelper)) {
        try {
            $users = $userHelper->getUsers();
            $operator = $userHelper->getOperator();
            if (isset($users[$username. '/' . $operator->id])) {
                $userHelper->resetPassword($users[$username. '/' . $operator->id]['id'], '', $password);
            }
        }
        catch (Exception $e) {
            $successful = FALSE;
            $errorMsg = $e->getMessage();
        }
    }
    
    if ($successful) {
		$result = "success";
	} else {
		$result = "Error - $errorMsg";
	}
	return $result;
}


function aflexi_TerminateAccount($params) {
    require(dirname(__FILE__) . '/../../../includes/aflexi/bootstrap.php');

    $serviceid = $params["serviceid"];
	$username = $params["username"];

    $successful = TRUE;

    if (isset($userHelper)) {
        try {
            $users = $userHelper->getUsers();
            $operator = $userHelper->getOperator();
            if (isset($users[$username. '/' . $operator->id])) {
                $userHelper->deletePublisherLink($users[$username. '/' . $operator->id]['id'] , $operator->id);
            }
        }
        catch (Exception $e) {
            $successful = FALSE;
            $errorMsg = $e->getMessage();
        }
    }

    if ($successful) {
		$result = "success";
	} else {
		$result = "Error - $errorMsg";
	}
	return $result;
}


function aflexi_ChangePackage($params) {
    require(dirname(__FILE__) . '/../../../includes/aflexi/bootstrap.php');

    $serviceid = $params["serviceid"];
	$username = $params["username"];

    $successful = TRUE;

    if (isset($userHelper)) {
        try {
            $users = $userHelper->getUsers();
            $operator = $userHelper->getOperator();
            $packages = $packageHelper->getPackages();
            $packageDetails = getProductByHostingId($serviceid);

            //Create package
            if (!isset($packages[$packageDetails['name']])) {
                $aflexiPackageId = $packageHelper->createPackage($packageDetails['name']);
            }
            else {
                $aflexiPackageId = $packages[$packageDetails['name']]['id'];
            }
            $package->set($username, $details['name']);

            if (isset($users[$username. '/' . $operator->id])) {
                $publisherLinkId = $users[$username. '/' . $operator->id]['id'] . ',' . $operator->id;
                $userHelper->updatePublisher($publisherLinkId , $aflexiPackageId);
            }
        }
        catch (Exception $e) {
            $successful = FALSE;
            $errorMsg = $e->getMessage();
        }
    }

    if ($successful) {
		$result = "success";
	} else {
		$result = "Error - $errorMsg";
	}
	return $result;
}


//Start Helper Area
function getUserByHostingId($id = 0) {
    $query = sprintf("
        SELECT *
        FROM `tblhosting`, `tblclients`
        WHERE `tblclients`.`id` = `tblhosting`.`userid`
            AND `tblhosting`.`id` = %d
    ", $id);
    $result = mysql_query($query) or die($query);
    
    if (mysql_num_rows($result) > 0) {
        $row = mysql_fetch_assoc($result);
        return $row;
    }
    return NULL;
}

function getProductByHostingId($id = 0) {
    $query = sprintf("
        SELECT *
        FROM `tblhosting`, `tblproducts`
        WHERE `tblproducts`.`id` = `tblhosting`.`packageid`
            AND `tblhosting`.`id` = %d
    ", $id);
    $result = mysql_query($query) or die($query);

    if (mysql_num_rows($result) > 0) {
        $row = mysql_fetch_assoc($result);
        return $row;
    }
    return NULL;
}


function updateUsername($id = 0, $username = '') {
    $query = sprintf("
        UPDATE `tblhosting`
        SET `username`='%s'
        WHERE `id` = %d
    ", $username, $id);
    $result = mysql_query($query) or die($query);
}

function updateDomain($id = 0, $username = '') {
    $query = sprintf("
        UPDATE `tblhosting`
        SET `domain`='%s'
        WHERE `id` = %d
    ", $username, $id);
    $result = mysql_query($query) or die($query);
}
//End Helper Area


?>