<?php
require_once('../CurlX.php');

$CurlX = new CurlX();

// Custom request structure with HTTPAUTH
try {
    $CurlX->custom(
        url: 'https://luminati.io/api/count_available_ips?customer=CUSTOMER&zone=ZONE',
        method: 'GET', // your request method
        data: NULL, // your data
        headers: ['Authorization: Bearer API_TOKEN']
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
    // To print the body response: $resp->getBody;
    var_dump($response->getBody());
} catch (CurlException $e) {
    echo $e->getMessage();
}
