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
 
# namespace Aflexi\Common\Lang;

// Note [yclian 20100824] Loading manually as autoloader may not be registered
// yet when 'Requires' is used.
require_once dirname(__FILE__).'/../IO/FileNotFoundException.php';
require_once dirname(__FILE__).'/../IO/FileUtils.php';

/**
 * Helper class for 'require'-ing dependencies.
 *
 * @author yclian
 * @since 2.2
 * @version 2.2.20100406
 */
final class Aflexi_Common_Lang_Requires{

    /*
     * Load all the common PHPUnit files.
     */
    public static function requirePhpUnit(){
        require_once 'PHPUnit/Framework.php';
    }


    /*
     * Load all the core Symfony classes.
     *
     * An example of custom class loader. We shall create two class loaders, one
     * for pre-5.3 class files, another for the new ones.
     */
    public static function requireSymfony(){
        require_once('symfony/autoload/sfCoreAutoload.class.php');
        sfCoreAutoload::register();
        // This is needed for sfContext initialization - it requries a project instance.
        require_once(PROJECT_HOME.'/config/ProjectConfiguration.class.php');
    }

    /**
     * Load all XML-RPC common classes.
     */
    public static function requireXmlRpc(){
        require_once('XML/RPC2/Client.php');
        require_once('XML/RPC2/Value.php');
    }

    /**
     * Load multiple PHP files. Please beware of the sequences, e.g. if a class is dependent by other classes, it shall be loaded explicitly first.
     *
     * @param $paths One or more string to a file or directory.
     */
    public static function requireFiles($paths){

        if(!is_string($paths) && !is_array($paths)){
            throw new InvalidArgumentException('$paths is neither a string nor array');
        }

        $arrayPaths = array();

        if(is_string($paths)){
            $arrayPaths = $paths;
        } else if(is_array($paths)){
            $arrayPaths = $paths;
        }

        foreach($arrayPaths as $path){
            $pathCheck = new SplFileInfo($path);
            if($pathCheck->isFile()){
                require_once($path);
            } else if($pathCheck->isDir()){
                $files = FileUtils::scanFiles($path, '\.php$');
                foreach($files as $file){
                    require_once($file);
                }
            } else{
                if(!file_exists($path)){
                    throw new FileNotFoundException("Path '$path' does not exist");
                } else{
                    // It could be symbolic link, we do not support it.
                }
            }
        }
    }
}

?>
