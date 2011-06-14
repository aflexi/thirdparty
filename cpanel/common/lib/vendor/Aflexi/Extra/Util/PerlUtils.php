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
 
# namespace Aflexi\Extra\Util;

/**
 * Utility for Perl script execution.
 * 
 * TODO [yclian 20100908] Shall create an aflexi-extra project but it's too 
 * much as a hassle now.
 * 
 * @author yclian
 * @since 2.4
 * @version 2.8.20100908
 */
final class Aflexi_Extra_Util_PerlUtils{
    
    /**
     * Location of perl executable, depends on distro. For instance,
     * 
     *  - cPanel's CentOS = /usr/local/bin/perl
     *  - Ubuntu = /usr/bin/perl
     * 
     * @var string
     */
    private static $perlPath = '/usr/local/bin/perl';
    
    static function getPerlPath(){
        return self::$perlPath;
    }
    
    /**
     * @param $path
     * @param $smart[optional] Guess the Perl path via 'which'm if given $path is empty. TRUE by default.
     */
    static function setPerlPath($path, $smart = TRUE){
        
        if(!empty($path)){
            self::$perlPath = $path;
        } else{
            $smartRt = trim(shell_exec("which perl"));
            if(!empty($smartRt)){
                self::$perlPath = $smartRt;
            } else{
                throw new InvalidArgumentException("Expected valid path to perl executable, enable \$smart if unsure");
            }
        }
    }
    
    /**
     * Execute a given Perl script path and return its result.
     * 
     * @param $script
     * @param $perl
     */
    static function exec($script, $input = NULL, $perl = NULL){
        
        $rt;
        
        if(empty($perl)){
            $perl = self::$perlPath;
        }
        
        // Well, someone can actually write an empty string to STDIN if they
        // really want to.
        // NOTE [yclian20100609] This works in prod but we will standardize to 
        // use the one below. It's broken in sandbox as it's not taking in $_ENV.
        //if(is_null($input)){
        //    $rt = shell_exec("{$perl} {$script}");
        //} else{
            $rt = self::openProcess("{$perl} {$script}", $input);
        //}
        
        return $rt;
    }
    
    /**
     * Given a script body, evaluate it with Perl.
     * 
     * @param $scriptBody
     * @param $perl
     * @see http://php.net/manual/en/function.proc-open.php
     * @see http://tldp.org/LDP/abs/html/exitcodes.html
     */
    static function execBody($scriptBody, $perl = NULL){
         return self::exec('', $scriptBody, $perl);
    }
    
    static function openProcess($command, $string){
        
        $process;
        $pipes;
        $exitCode = 0;
        $rt;
        $rtErr = 0;
        
        $pipes = array();
        
        $process = proc_open(
            "{$command}",
            array(            
             array('pipe', 'r'),
             array('pipe', 'w'),
             array('pipe', 'w')
            ),
            $pipes,
            NULL,
            $_ENV
        );
        
        if(is_resource($process)){
            
            fwrite($pipes[0], $string);
            fclose($pipes[0]);
            
            $rt = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            
            $rtErr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            
            $exitCode = proc_close($process);
            
        } else{
            throw new RuntimeException("Unable to open process for execution");
        }
        
        if($exitCode > 0){
            throw new RuntimeException("Execution completed with exit code '{$exitCode}': {$rtErr}");
        }
        
        return $rt;
    }
}

// Default initialization. Override it in your scripts for your own preferences.
Aflexi_Extra_Util_PerlUtils::setPerlPath(NULL, TRUE);

?>