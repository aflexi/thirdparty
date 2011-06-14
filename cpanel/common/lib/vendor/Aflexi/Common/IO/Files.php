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
 
# namespace Aflexi\Common\IO;

/**
 * Utils for file- or network- I/O operations.
 *
 * @author yclian
 * @since 2.2
 * @version 2.2.20100405
 */
final class Aflexi_Common_IO_Files{

    /**
     * @var Logger
     */
    static $logger;

    /**
     * We are using lazy-instantiation here but not the global context after
     * the FileUtils class block, as we may not have loaded the logging api
     * and classes yet.
     *
     * @return Logger
     */
    private static function getLogger(){
        if(is_null(self::$logger)){
            require_once dirname(__FILE__).'/../Log/LoggerFactory.php';
            self::$logger = Aflexi_Common_Log_LoggerFactory::getLogger();
        }
        return self::$logger;
    }

    /**
     * Read specified $len of string from a given file $path.
     *
     * @param $path
     * @param $len Length in bytes. If 0 or less, file size will be used.
     * @return string
     */
    public static function readStringFromFile($path, $len = 0){

        // TODO [yclian 20100407] Move the impl to use file_get_contents().

        $rt = '';

        $fh = fopen($path, 'r');
        if($len <= 0){
            $len = filesize($path);
        }
        $rt = fread($fh, $len);
        fclose($fh);
        return $rt;
    }

    /**
     * Given a directory, scan for files matching the specified filters.
     *
     * @param baseDir The directory where scanning shall start from. Relative path is supported, e.g. '.' for working directory, adviced to use absolute path.
     * @param $includeFilter Regex to match against the full-path filename.
     * @param $excludeFilter Regex to match against the full-path filename for exclusion. This check runs after inclusion.
     * @return array A string array of file names.
     */
    public static function scanFiles($baseDir, $includeFilter = '.*', $excludeFilter = NULL){

        $rt;
        $files;

        if(empty($baseDir) || !is_string($baseDir)){
            throw new InvalidArgumentException('$baseDir is not specified or is not a valid string');
        }

        $rt = array();
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir), RecursiveIteratorIterator::SELF_FIRST);
        foreach($files as $file){
            $pathName;
            // NOTE [yclian 20100411] Putting instanceof makes the IDE to do casting and auto-complete
            if($file->isFile() && $file instanceof SplFileInfo){
                $pathName = $file->getPathname();
                if(preg_match('/'.$includeFilter.'/', $pathName)){
                    if($excludeFilter == NULL || !preg_match('/'.$excludeFilter.'/', $pathName)){
                        self::getLogger()->debug(sprintf('Detected matching file: %s', $pathName));
                        $rt []= $pathName;
                    }
                }
            }
        }
        return $rt;
    }
}

?>
