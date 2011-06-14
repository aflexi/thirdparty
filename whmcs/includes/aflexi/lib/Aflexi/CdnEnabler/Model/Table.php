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
abstract class Aflexi_CdnEnabler_Model_Table {
    const DB_SCHEMA = '20110310153500';
    
    protected $tableName = '';
    protected $configs = array();

    /**
     * Constructor. Creates table if not exists
     */
    public function __construct() {
        if (!$this->isTableExist()) {
            $this->createTable();
            $this->initializeTable();
        }
    }
    
    /**
     * 
     * Setter for tableName property
     * @param string $tableName
     */
    public function setTableName($tableName = '') {
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
     * Create table
     */
    public function createTable() {
        $query = "
            CREATE TABLE IF NOT EXISTS `{$this->tableName}` (
                `key` varchar(255) NOT NULL,
                `value` text NOT NULL,
                UNIQUE KEY `key` (`key`)
            )
        ";
        mysql_query($query) or die(mysql_error());
    }
    
    /**
     * 
     * Initialize table by populating default values
     */
    public function initializeTable() {
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
    
    public function getAll() {
        $rt = array();
        
        $query = "
            SELECT *
            FROM `{$this->tableName}`
        ";
        
        $result = mysql_query($query) or die(mysql_error());
        
        while ($row = mysql_fetch_assoc($result)) {
            $rt[$row['key']] = $row; 
        }
        
        return $rt;
    }

    public function del($key = '') {
        $query = sprintf("
            DELETE FROM `{$this->tableName}`
            WHERE `key` = '%s'
        ",  mysql_real_escape_string($key));

        mysql_query($query) or die(mysql_error());

        $result = mysql_affected_rows();
        return $result;
    }
    
    public function upgradeSchema($currentSchema = 0) {
        $result = $this->getAll();
        
        //Start schema as of 20101117115200
        if (20101117115200 > $currentSchema) {
            mysql_query("TRUNCATE TABLE `{$this->tableName}`") or die(mysql_error());
            mysql_query("ALTER TABLE `{$this->tableName}` DROP INDEX `key`, ADD UNIQUE `key` (`key`)") or die(mysql_error());

            foreach ($result as $key=>$row) {
                $this->set($key, $row['value']);
            }
            $currentSchema = 20101117115200;
        }
        //End schema as of 20101117115200
    }
}
?>