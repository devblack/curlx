<?php
require_once('../CurlX.php');

$CurlX = new CurlX;
/**
 * Custom request with DELETE METHOD
 * @param url      : Require true
 * @param method   : Require true
 * @param data     : Require false
 * @param headers  : Require false
 * @param cookie   : Require false
 * @param server   : Require false
 * 
 */

// Custom request structure with HTTPAUTH
$CurlX::Custom('https://luminati.io/api/count_available_ips?customer=CUSTOMER&zone=ZONE', 'GET', NULL, ['Authorization: Bearer API_TOKEN']);
/**
 * Set the CURLOPT_HTTPAUTH MODE
 * MODES:
 *  CURLAUTH_BASIC
 *  CURLAUTH_BEARER
 * MORE IN https://curl.haxx.se/libcurl/c/CURLOPT_HTTPAUTH.html
 */
$CurlX::SetOpt([CURLOPT_HTTPAUTH => CURLAUTH_BEARER]);
// send the Custom request and catch in $resp
$resp = $CurlX::Run();
// Print the object data or if you are working is develop mode, you can use Debug()
// To print the body response: $resp->body;
print_r($resp);

/**
 * Debug() : support pretty print to html and json
 * @param true = json print
 * @param false = html print
 * 
 * $CurlX::Debug(true);
 */
