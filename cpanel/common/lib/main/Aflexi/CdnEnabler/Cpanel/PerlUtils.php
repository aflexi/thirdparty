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
 
# namespace Aflexi\CdnEnabler\Cpanel;

/**
 * Re-usable functions to support any other classes or scripts in this package.
 * 
 * @author yclian
 * @since 2.4
 * @version 2.4.20100530
 */
final class Aflexi_CdnEnabler_Cpanel_PerlUtils{
    
    /**
     * @var string
     * @see #setScriptDir()
     */
    private static $scriptDir = NULL;
    
    /**
     * Execute a cPanel Perl script in the "Support/Perl" directory. A wrapper
     * to Aflexi_Extra_Util_PerlUtils.
     * 
     * e.g. 
     *  execScript('acl_has_root.pl');
     *  execScript('acl_has_access.pl', '{"privilege": "ALL"}');
     * 
     * @param string $scriptArg Filename of the script and further command-line argu-
     *  ments.
     * @param string $scriptContent[optional] Content to be redirected to the STDIN.
     *  Use the $scriptArg otherwise.
     * @param string $scriptDir[optional] Custom script dir.
     * @return mixed Result (originally JSON) returned by script. Usually an array
     *  with key/value pairs but may vary. 
     * @see Aflexi_Extra_Util_PerlUtils
     */
    static function execScript($scriptArg, $scriptContent = NULL, $scriptDir = NULL){
        
        if(is_null($scriptDir)){
            $scriptDir = self::$scriptDir;
        }
        
        return json_decode(Aflexi_Extra_Util_PerlUtils::exec("$scriptDir/$scriptArg", $scriptContent), TRUE);
    }
    
    /**
     * Override the default script dir.
     * 
     * @param string $scriptDir
     */
    static function setScriptDir($scriptDir){
        self::$scriptDir = $scriptDir;
    }
    
    /**
     * Reset the script dir to its default. Used internally (including tests).
     */
    static function resetScriptDir(){
        self::setScriptDir(dirname(__FILE__).'/Support/Perl');
    }
    
    /**
     * Call the XML-API of cPanel. Not functioning yet as I oppose storing the
     * user password anywhere at this stage - XmlApi requires username/password.
     * 
     * @since 2.4
     * @version 2.4.20100531
     * @see http://docs.cpanel.net/twiki/bin/view/ApiDocs/Api1/WebHome
     * @see http://docs.cpanel.net/twiki/bin/view/ApiDocs/Api2/WebHome
     * @see docs.cpanel.net/twiki/bin/view/AllDocumentation/AutomationIntegration/CallingAPIFunctions
     * @see http://sdk.cpanel.net/lib/xmlapi/php/
     * @see http://forums.cpanel.net/f42/xml-api-php-class-version-1-0-a-136449.html
     */
    static function execXmlApi(){
        throw new Aflexi_Common_Lang_UnsupportedOperationException();
    }
}

Aflexi_CdnEnabler_Cpanel_PerlUtils::resetScriptDir();

?>