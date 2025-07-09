<?php

require_once('../CurlX.php');

$CurlX = new CurlX();

// basic proxy server configuration
$server = [
    'METHOD' => 'TUNNEL',
    'SERVER' => '127.0.0.1:8889'
];

// Custom request structure
try {
    $CurlX->custom(
        url: 'https://host/users/666',
        data: 'params[name]=James',
        headers: ['custom-header: my-custom-value', 'foo: bar'],
        server: $server,
        method: 'PUT'
    );
    // send the Custom request and catch in $resp
    $resp = $CurlX->run();
    // Print the object data or if you are working is develop mode, you can use Debug()
    // To print the body response: $resp->body;
    print_r($resp->body);

} catch (CurlException $e) {
    echo $e->getMessage();
}
