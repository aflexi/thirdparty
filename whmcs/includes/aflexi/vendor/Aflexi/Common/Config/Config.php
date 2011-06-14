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
 
# namespace Aflexi\Common\Config;

/**
 * Placeholder for configurations.
 * 
 * @author yclian
 * @since 2.7
 * @version 2.8.20100908
 */
interface Aflexi_Common_Config_Config{
    
    const DEFAULT_NAMESPACE = '';
    
    /**
     * @param string $key
     * @param mixed $default
     * @param string $namespace
     * @return mixed
     */
    function get($key, $default = NULL, $namespace = self::DEFAULT_NAMESPACE);
    
    /**
     * @param string $key
     * @param mixed $value
     * @param string $namespace
     * @return mixed Previous value or NULL.
     */
    function set($key, $value, $namespace = self::DEFAULT_NAMESPACE);
    
    /**
     * Read configurations from given source to this object.
     * 
     * @param mixed $source
     * @param string $namespace[optional] If provided, configurations are stored under specied namespace.
     * @param bool $merge[optional] If provided, read configurations are merged with the existing runtime values.
     * @return array Associative array.
     */
    function read($source, $namespace = self::DEFAULT_NAMESPACE, $merge = TRUE);
    
    /**
     * Write configurations to given destination.
     * 
     * @param mixed $destination
     * @param mixed $namespace[optional] Only entries under this namespace will 
     *  be written. Default namespace used if not provided.
     * @return void
     */
    function write($destination, $namespace = self::DEFAULT_NAMESPACE);
}

?>