<?php

/*
 * LICENSE AGREEMENT
 * -----------------------------------------------------------------------------
 * Copyright (c) 2010 Aflexi Sdn. Bhd.
 * 
 * This file is part of Aflexi_Common.
 * 
 * Aflexi_Common is published under the terms of the Open Software License 
 * ("OSL") v. 3.0. For the full copyright and license information, please view 
 * the LICENSE file that was distributed with this source code.
 * -----------------------------------------------------------------------------
 */
 /**
 * 
 * Model to deal with config table
 * @author yingfan
 * @since V2.8, 20100907
 *
 */
class Aflexi_CdnEnabler_Model_ConfigTable extends Aflexi_CdnEnabler_Model_Table {    
    public function __construct() {
        $this->setTableName('mod_aflexicdn');
        parent::__construct();
    }

    public function upgradeSchema($currentSchema = 0) {
        parent::upgradeSchema($currentSchema);
        if (20110310153500 > $currentSchema) {
            $this->initializeTable();
        }
    }

    /**
     *
     * Check if table is setup with authentication stuff
     */
    public function isTableSetup() {
        $query = "SELECT `key` FROM `{$this->tableName}` WHERE `key`='auth_key' OR `key`='oauth_consumer_key' ";
        $result = mysql_query($query) or die(mysql_error());
        return (mysql_num_rows($result) == 2);
    }

    public function initializeTable() {
        $this->set('integration_operator', 'http://portal.aflexi.net/mini_operator.php');
        $this->set('integration_publisher', 'http://portal.aflexi.net/mini_publisher.php');
    }
}
?>
