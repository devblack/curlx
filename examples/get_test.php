<?php

require_once('../CurlX.php');

$CurlX = new CurlX;
/**
 * GET request
 * @param url      : Require true
 * @param headers  : Require false
 * @param cookie   : Require false
 * @param server   : Require false
 */

// generate a new unique id for file name
$cookie = uniqid();
// basic proxy server configuration
$server = [
    'METHOD' => 'TUNNEL',
    'SERVER' => $CurlX::GetRandVal('../proxies.txt')
];
// Send GET request and catch it in $resp
$resp = $CurlX::Get('https://httpbin.org/get', ['my-custom-header: my-custom-value'], $cookie, $server);
/**
 * After all process you can delete the cookie file with DeleteCookie(); ONLY IF YOU USE THE COOKIE PARAM
 */
$CurlX::DeleteCookie();
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
