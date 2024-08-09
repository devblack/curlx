<?php

require_once('../CurlX.php');

$CurlX = new CurlX(
    // cypher for cloudflare tls negotiation
    [
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_SSL_CIPHER_LIST => "ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384"
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
    //echo $response->getBody();
} catch (Exception $e) {
    echo $e->getMessage();
}
