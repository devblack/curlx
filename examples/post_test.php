<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Thevenrex\Curlx\CurlX;
use Thevenrex\Curlx\Exceptions\CurlException;

$CurlX = new CurlX();


// generate a new unique id for file name
$cookie = uniqid();

// Send POST request and catch it in $resp
try {
    $response = $CurlX->post(
        url:'https://httpbin.org/post',
        data:'my_post_data_id=666&extraID=777',
        headers: ['Host: httpbin.org', 'Origin: https://httpbin.org/'],
        cookie: $cookie
    );
    $CurlX->deleteCookie();
    var_dump($response->getBody());
} catch (Exception $e) {
    echo $e->getMessage();
}
