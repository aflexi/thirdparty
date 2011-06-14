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
 
// Functions
// -----------------------------------------------------------------------------

/**
 * Define CPANEL_* constant from the environment variable of identical key.
 * 
 * @param string $key
 * @param string $default
 * @author yclian
 * @since 2.5
 * @version 2.5.20100603
 */
function afx_define_constant($key, $default){
    if(!isset($_ENV[$key]) || !empty($_ENV[$key])){
        $_ENV[$key] = $default;
    }
    define($key, $_ENV[$key]);
}

/**
 * @return boolean TRUE if this is the setup page.
 * @since 2.5
 * @version 2.5.20100607
 */
function afx_is_setup(){
    return $_SERVER['PHP_SELF'] == '/aflexi/setup.php';
}

/**
 * Return the sandbox path.
 * 
 * @author yclian
 * @since 2.5
 * @version 2.5.20100608
 */
function afx_sandbox_path(){
    $matches = array();
    $full_path = dirname(__FILE__);
    
    preg_match('#(.*)/usr/local/cpanel#', $full_path, $matches);
    
    return isset($matches[1]) ? $matches[1] : '';
}

/**
 * Detect if we're in a sandbox environment.
 * 
 * @author yclian
 * @since 2.5
 * @version 2.6.1.20100802
 */
function afx_sandbox_is_enabled(){
    // NOTE [yclian 20100802] Instead of detecting path to determine sandbox, we
    // now check if /var/cpanel/aflexi/sandbox.yml exists.
    // $path = afx_sandbox_path();
    // return !empty($path);
    return file_exists(CPANEL_AFX_DATA.'/sandbox.yml');
}

// Security functions
// -----------------------------------------------------------------------------

/**
 * Check if remote user has cPanel's root access, else exit with 403 error. 
 * 
 * NOTE [yclian 20100607] We need a container to hold the states.
 * 
 * @global $afx_template
 */
function afx_security_check_root(){
    
    global $afx_template;
    
    $afx_acl = new AfxCpanel_Core_CpanelAccessControlHelper();
    
    if(!$afx_acl->hasRoot()){
        afx_error_send_403();
    }
}


// Error functions
// -----------------------------------------------------------------------------

/**
 * Log an error to /var/cpanel/aflexi/error.log.
 * 
 * @since 2.5
 * @version 2.5.20100704
 * @param string $message The error message, without an ending line-break.
 * @return bool TRUE if successfully logged.
 */
function afx_error_log($message){
    return error_log("$message\n", 3, CPANEL_DATA.'/aflexi/error.log');
}

/**
 * Render variables used in error templates, contains the following:
 * 
 *  - error_status
 *  - server_signature
 * 
 * @return array
 */
function afx_error_template_vars(){
    
    global $afx_http_last_header;
    
    return array(
        'error_status' => $afx_http_last_header ? $afx_http_last_header : '500 Internal Server Error',
	// We have to use $afx_http_last_header to hold state. As, 
	// HttpResponse::getHeader('Status') comes with pecl_http which
	// people can hardly get it installed.
        'server_signature' => $_SERVER['SERVER_SIGNATURE']
    );
}

/**
 * Send 403 status and render its error page.
 * 
 * @global $afx_template
 * @param array $context
 * @author yclian
 * @since 2.5
 * @version 2.5.20100609
 */
function afx_error_send_403($context = array()){
    
    global $afx_template;
    
    http_set_response_header('403 Forbidden');
    echo $afx_template->renderTemplate(
        'error.xml',
        array_merge(
            afx_error_template_vars(),
            $context
        )
    );
    exit(1);
}

/**
 * Send a specific status code and render its error page. Used with Exception.
 * 
 * @global $afx_template
 * @param Exception $e
 * @param string $status_code
 * @param array $context
 * @author yclian
 * @since 2.5
 * @version 2.5.20100609
 */
function afx_error_send_exception(Exception $e, $status_code = NULL, $context = array()){
    
    global $afx_template;
    
    if(is_null($status_code)){
        $status_code = '500 Internal Error';
    }
    
    if(!array_key_exists('error_description', $context)){
        $context['error_description'] = "<p>Could not process page due to exception: ".
                                        "<strong><tt>".get_class($e)." - {$e->getMessage()}</tt></strong>. ".
                                        "Please contact the administrator to ensure that the plugin is correctly configured.</p>\n".
                                        "<pre>{$e->getTraceAsString()}</pre>";
    }
    
    http_set_response_header($status_code);
    echo $afx_template->renderTemplate(
        'error.xml',
        array_merge(
            afx_error_template_vars(),
            $context
        )
    );
    exit(1);
}

/**
 * Load one or more array, with the options to write empty file if doesn't 
 * exist and global variables {global_var_name} and ${global_var_name.'_path'}.
 * 
 * @param mixed $config_path String or associative array. The latter will cause 
 * recursive config loading with its key being the $global_var_name.
 * @param bool $write_empty[optional]
 * @param string $global_var_name[optional]
 */
function afx_config_load($config_path, $write_empty = TRUE, $global_var_name = NULL){
    
    $rt;
    
    if(is_assoc($config_path)){
        
        $rt = array();
        
        foreach($config_path as $name => $config_path_single){
            $rt[$name] = afx_config_load($config_path_single, $write_empty, $name);
        }
        
        return $rt;
        
    } else{
    
        if(file_exists($config_path)){
            $rt = Aflexi_Common_Yaml_YamlUtils::read($config_path);
        } else{
            $rt = array();
            if($write_empty){
                Aflexi_Common_Yaml_YamlUtils::write($config_path, array());
            }
        }
        
        if(!is_null($global_var_name)){
            
            global ${$global_var_name};
            global ${$global_var_name.'_path'};
            
            ${$global_var_name} = $rt;
            ${$global_var_name.'_path'} = $config_path;
        }
        
        return $rt;
    
    }
}

?>
