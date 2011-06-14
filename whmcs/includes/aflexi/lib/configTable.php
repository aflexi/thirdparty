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
class Aflexi_ConfigTable {
    protected $tableName = '';
    protected $configs = array();
    
    public function __construct() {
        $this->setTableName();
    }
    
    /**
     * 
     * Setter for tableName property
     * @param string $tableName
     */
    public function setTableName($tableName = 'mod_aflexicdn') {
        $this->tableName = $tableName;
    }
    
    /**
     * 
     * Getter for tableName property
     */
    public function getTableName() {
        return $this->tableName;
    }
    
    /**
     * 
     * Check if table exists
     */
    public function isTableExist() {
        $query = "SHOW TABLES LIKE '{$this->tableName}'";
        $result = mysql_query($query) or die(mysql_error());
        return (mysql_num_rows($result) > 0);
    }
    
    /**
     * 
     * Check if table is setup with authentication stuff
     */
    public function isTableSetup() {
        $query = "SELECT `key` FROM `{$this->tableName}` WHERE `key`='auth_key' ";
        $result = mysql_query($query) or die(mysql_error());
        return (mysql_num_rows($result) > 0);
    }
    
    
    /**
     * 
     * Create table
     */
    public function createTable() {
        $query = "
            CREATE TABLE IF NOT EXISTS `{$this->tableName}` (
                `key` varchar(255) NOT NULL,
                `value` text NOT NULL,
                KEY `key` (`key`)
            )
        ";
        mysql_query($query) or die(mysql_error());
    }
    
    /**
     * 
     * Initialize table by populating default values
     */
    public function initializeTable() {
        $query = "
            REPLACE INTO `{$this->tableName}`
            VALUES
            ('xmlrpc_core', 'http://api.aflexi.net/core/xmlrpc'),
            ('xmlrpc_stats', 'http://api.aflexi.net/stats/xmlrpc'),
            ('portal_public', 'http://portal.aflexi.net'),
            ('portal_mini', 'http://portal.aflexi.net/mini.php');
        ";
        mysql_query($query) or die(mysql_error());
    }
    
    /**
     * 
     * Update table (and cache) with key value pair
     * @param string $key
     * @param string $value
     */
    public function set($key='', $value='') {
        $query = "
            REPLACE INTO `{$this->tableName}`
            VALUES ('%s', '%s')
        ";
        mysql_query(sprintf(
            $query, 
            mysql_real_escape_string($key), 
            mysql_real_escape_string($value)
        )) or die(mysql_error());
        
        $this->configs[$key] = $value;
    }
    
    /**
     * 
     * Retrieve value of a given key from table (or cache) 
     * @param string $key
     */
    public function get($key = '') {
        if (isset($this->configs[$key])) {
            return $this->configs[$key];
        }
        
        $query = "
            SELECT `value`
            FROM `{$this->tableName}`
            WHERE `key`='%s'
        ";
        $result = mysql_query(sprintf(
            $query, 
            mysql_real_escape_string($key)
        )) or die(mysql_error());
        
        if (mysql_num_rows($result) > 0) {
            $row = mysql_fetch_assoc($result);
            $this->configs[$key] = $row['value'];
            return $row['value'];
        }
        return NULL;
    }
}
?>