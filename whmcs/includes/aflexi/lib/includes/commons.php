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
 * HTTP_METH_GET.
 * @see http://www.php.net/manual/en/http.constants.php
 */
if(!defined('HTTP_METH_GET')){
    define('HTTP_METH_GET', 1);
}

/**
 * HTTP_METH_POST.
 * @see http://www.php.net/manual/en/http.constants.php
 */
if(!defined('HTTP_METH_POST')){
    define('HTTP_METH_POST', 3);
}

/**
 * @return boolean TRUE if it's a GET method.
 * @since 2.5
 * @version 2.5.20100607
 */
function http_is_method_get(){
    return isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'get'; 
}

/**
 * @return boolean TRUE if it's a POST method.
 * @since 2.5
 * @version 2.5.20100607
 */
function http_is_method_post(){
    return isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post'; 
}

if(!function_exists('http_redirect')){
    
    /**
     * Redirect to the given URL. Identical with (but not fully supported like)
     * pecl_http's. 
     * 
     * @param string $url
     * @param array $params
     * @param bool $session Not supported.
     * @param int $status Not supported.
     * @see http://php.net/manual/en/function.http-redirect.php
     */
    function http_redirect($url, $params = array(), $session = FALSE, $status = 301){
        
        $status_str = '';
        
        switch($status){
            case '301':
            default:{
                $status_str = '301 Moved Permanently';
                break;
            }
        }
        
        if(strpos($url, '?')){
            $url = "$url&".http_build_query($params);
        } else{
            $url = "$url?".http_build_query($params);
        }
        
        header("HTTP/1.1 $status_str");
        header("Location: $url");
        exit;
    }
}

/**
 * Simplfieid version of http_post_fields.
 * 
 * @param string $url
 * @param array $data
 * @param array $info
 * @return mixed The response body or FALSE if there's an error.
 * @see http://www.php.net/manual/en/function.http-post-fields.php
 */
function http_post_fields_simple($url, $data = array(), &$info = array()){
    
    $ch;
    $rt;
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    
    $rt = curl_exec($ch);
    $info = curl_getinfo($ch);
    $info['content'] = $rt;
    $info['errno'] = curl_errno($ch);
    $info['errmsg'] = curl_error($ch);
    
    curl_close($ch);
    
    return $rt;
}


/**
 * Set HTTP status code. This method is different with http_send_status as it
 * also registers the 'Status' header.
 * 
 * @param string $status Status code.
 * @param int $version HTTP version.
 * @return void
 * @since 2.5
 * @version 2.5.20100607
 * @see http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 */
function http_set_response_header($status, $version = 1.1){
    
    global $afx_http_last_header;
    
    $afx_http_last_header = $status;
    
    header("HTTP/$version $status");
    header("Status: $status");
}

/**
 * @return string Server host (or virtual host) or NULL if could not detect.
 * @since 2.5
 * @version 2.5.20100607
 * @see http://php.net/manual/en/reserved.variables.php
 */
function server_host(){
    return isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : NULL;
}

if(!function_exists('is_assoc')){
    
    /**
     * Determine if a given value is an associative array.
     * 
     * @param $array
     * @see http://www.php.net/manual/en/function.is-array.php#98305
     */
    function is_assoc($array){
        return is_array($array) && (
                count($array) == 0 || 
                0 !== count(
                    array_diff_key(
                        $array, array_keys(array_keys($array))
                    )
                )
            );
    }
}

/**
 * Get the request URI.
 * 
 * @param bool $relative
 */
function http_get_request_uri($relative = FALSE){
    
    $rt;
    
    $relative_uri = $_SERVER['REQUEST_URI'];
    
    if($relative){
        $rt = $relative_uri;
    } else{
        
        $rt = (@$_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
        
        $rt .= ($_SERVER['SERVER_PORT'] == '80') ? 
            "{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}":
            "{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$_SERVER['REQUEST_URI']}";
        
    }
    
    return $rt;
}

?>
