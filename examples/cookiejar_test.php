<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Thevenrex\Curlx\CurlX;
use Thevenrex\Curlx\Exceptions\CurlException;
use Thevenrex\Curlx\CookieJar\{
    Cookie,
    CookieJar
};

$CurlX = new CurlX();

$cookie = new CookieJar();
$cookie
    ->add(new Cookie('babydoge.com', 'FALSE', '/', 'TRUE', time() + 3600, 'test3', 'test4'))
    ->add(new Cookie(
        domain: 'babydoge.com',
        includeSubDomains: 'TRUE',
        path: '/',
        httpOnly: 'TRUE',
        expire: time() + 3600,
        name: 'test',
        value: 'value'
    ));

try {
    $response = $CurlX->get(
        url: 'https://babydogearmy.org',
        headers: ['my-custom-header: set-my-cookies'],
        cookie: $cookie
    );
    // $CurlX->deleteCookie();
    $CurlX->debug();
    // echo $response->getBody();

} catch (CurlException $e) {
    echo $e->getMessage();
}