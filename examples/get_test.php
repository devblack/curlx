<?php

require_once('../CurlX.php');

$CurlX = new CurlX();

// generate a new unique id for file name
$cookie = new CookieJar();

// Send GET request and catch it in $resp
try {
    $CurlX->get(
        url: 'https://httpbin.org/cookies/set/test/cookie',
        headers: ['my-custom-header: set-my-cookies'],
        cookie: $cookie
    );

    $response = $CurlX->get(
        'https://httpbin.org/cookies',
        ['my-custom-header: get-my-cookies'],
        $cookie
    );
    // $CurlX->deleteCookie();
    // Print the object data or if you are working is developing mode, you can use debug()
    // To print the body response: $resp->body;
    $CurlX->deleteCookie();
    //$CurlX->debug();
    var_dump($response->body);
} catch (Exception $e) {
    echo $e->getMessage();
}
