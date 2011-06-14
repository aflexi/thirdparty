<?php
$publishers = $publisher->getAll();
$packages = $package->getAll();
$userDetails = getUserDetails();

foreach ($publishers as $publisher => &$value) {
    $value['package'] = $packages[$publisher];
    
    $value['detail'] = @$userDetails[$publisher];
}

$context['publishers'] = $publishers;

echo $afx_template->renderTemplate('showPublishers.html', $context);

function getUserDetails(){
    $query = "
        SELECT username, firstname, lastname, tblclients.id
        FROM tblclients, tblhosting, mod_aflexicdn_publisher
        WHERE tblhosting.username = mod_aflexicdn_publisher.key
        AND tblhosting.userId = tblclients.id
    ";
    $result = mysql_query($query) or die($query);

    $products = array();
    while ($row = mysql_fetch_assoc($result)) {
        $products[$row['username']] = $row;
    }


    return $products;
}
?>