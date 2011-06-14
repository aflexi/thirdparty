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
 
# namespace Aflexi\Common\Log;

/**
 * Logger interface adhering to the log levels defined in SLF4J's API, to be
 * implemented to hide the underneath logger implementation, e.g. to avoid
 * having our code to explicitly use Symfony's logger (sfLogger).
 *
 * Reasons:
 *
 *  - In some helper and util classes that are meant to be framework-neutral,
 *    when logging is required, having sfLogger in their code is a bad
 *    coupling example.
 *
 * @see <a href="http://www.slf4j.org/api/org/slf4j/Logger.html">Logger javadoc</a>
 * @author yclian
 * @since 2.3
 * @version 2.3.20100415
 */
interface Aflexi_Common_Log_Logger{

    /**
     * Return the name of this logger instance.
     *
     * @return string
     */
    function getName();
    
    /**
     * @return bool {@code true} if ERROR logging is enabled. A good practice is to check against this setting before logging the message.
     */
    function isErrorEnabled();

    /**
     * Log an ERROR message.
     * @param $message
     */
    function error($message);
    
    /**
     * @return bool {@code true} if DEBUG logging is enabled. A good practice is to check against this setting before logging the message.
     */
    function isDebugEnabled();

    /**
     * Log a DEBUG message.
     * @param $message
     */
    function debug($message);
    
    /**
     * @return bool {@code true} if WARN logging is enabled. A good practice is to check against this setting before logging the message.
     */
    function isWarnEnabled();

    /**
     * Log a WARN message.
     * @param $message
     */
    function warn($message);
    
    /**
     * @return bool {@code true} if INFO logging is enabled. A good practice is to check against this setting before logging the message.
     */
    function isInfoEnabled();

    /**
     * Log an INFO message.
     * @param $message
     */
    function info($message);
}

?>