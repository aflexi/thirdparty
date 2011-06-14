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
 
# namespace Aflexi\Common\Util;

require_once 'Spyc/spyc.php';

/**
 * Utility to parse/serialize/read/write YAML data.
 * 
 * @author yclian
 * @since 2.5
 * @version 2.5.20100603
 */
final class Aflexi_Common_Yaml_Utils{
    
    /**
     * Parse a YAML string to array.
     * 
     * @param $yamlString
     * @return array
     */
    static function parse($yamlString){
        return Spyc::YAMLLoadString($yamlString);
    }
    
    /**
     * Serialize a given array to YAML.
     * 
     * @param array $data
     * @return string
     */
    static function serialize(array $data){
        return Spyc::YAMLDump($data, 4, 0);
    }
    
    /**
     * Read a YAML file and parse it to array.
     * 
     * @param string $filePath
     * @return array
     */
    static function read($filePath){
        return self::parse(file_get_contents($filePath));
    }
    
    /**
     * Write given content to a YAML file.
     * 
     * @param string $filePath The target file path.
     * @param mixed $data YAML string or PHP array that will be written to YAML format.
     * @see http://php.net/manual/en/wrappers.php.php 
     * @return string The YAML data.
     */
    static function write($filePath, $data){
        
        if(is_string($data)){
            // To validate YAML, the best I can do so far is to ensure that no 
            // indentation is using the tab character.
            if(preg_match('/^\s*\t/m', $data)){
                throw new InvalidArgumentException('Given string is not YAML');
            }
        }
        
        if(is_array($data)){
            $data = self::serialize($data);
        }
        
        file_put_contents($filePath, $data, LOCK_EX);
        return $data;
    }
}

?>
