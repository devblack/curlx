<?php

require_once('../CurlX.php');

$CurlX = new CurlX(
    [
        CURLOPT_SSL_OPTIONS => CURLSSLOPT_NATIVE_CA,
    ]
);

// generate a new unique id for file name
$cookie = uniqid();

try {
    $response = $CurlX->get(
        url: 'https://babydogearmy.org',
        cookie: $cookie
    );

    //$CurlX->deleteCookie();
    $CurlX->debug();
    //echo $response->body;
} catch (Exception $e) {
    echo $e->getMessage();
}
