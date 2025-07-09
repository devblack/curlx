<?php
require_once('../CurlX.php');

$CurlX = new CurlX();

// Custom request structure with HTTPAUTH
try {
    $CurlX->custom(
        url: 'https://luminati.io/api/count_available_ips?customer=CUSTOMER&zone=ZONE',
        data: NULL, // your data
        headers: ['Authorization: Bearer API_TOKEN'], // headers
        method: 'GET' // your request method. GET by default
    );

    /**
     * Set the CURLOPT_HTTPAUTH MODE
     * MODES:
     *  CURLAUTH_BASIC
     *  CURLAUTH_BEARER
     * MORE IN https://curl.haxx.se/libcurl/c/CURLOPT_HTTPAUTH.html
     */
    $CurlX->setOpt([CURLOPT_HTTPAUTH => CURLAUTH_BEARER]);
    // send the Custom request and catch in $resp
    $response = $CurlX->run();
    // Print the object data or if you are working is develop mode, you can use Debug()
    // To print the body response: $resp->body;
    var_dump($response->body);
} catch (CurlException $e) {
    echo $e->getMessage();
}
