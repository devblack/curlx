CurlX v2.0.0b
================

CurlX is an HTTP basic library written in PHP for human beings and has no dependencies, working with PHP 8.2+.

![](https://i.imgur.com/AVwS6kZ.png)

CurlX allows you to send **GET**, **POST**, **PUT**, **DELETE** AND MORE HTTP METHODS. You can add headers, form data, json data,
and parameters with simple arrays, and access the response data in the same way. You can add an HTTP TUNNEL with PROXY, server ROTATIONS like [LUMINATI][], [APIFY][], [IPVANISH][].

[LUMINATI]: https://luminati.io/
[APIFY]: https://apify.com/
[IPVANISH]: https://www.ipvanish.com/

GET, POST AND CUSTOM Syntax
--------

```php
# GET
$CurlX->get("https://api.myip.com/");

# POST
$CurlX->post("https://api.myip.com/", "my_form_id=test&hello=mom");

# CUSTOM
$CurlX->custom("https://api.myip.com/", "HEAD");
$CurlX->run();
```

HTTP TUNNEL SYNTAX
--------

```php
# PROXY (http/s, socks4, socks5)
$server = [
    "method" => "tunnel",
    "server" => "47.254.145.99:3128"
];

# LIMINATI valid syntax example
$session => mt_rand();
$server = [
    "method" => "custom",
    "server" = "http://zproxy.lum-superproxy.io:22225",
    "auth" => "lum-customer-hl_876f552a-zone-static-route_err-pass_dyn-country-RU-session-$session:my_ultra_secret_password"
];

# APIFY valid syntax example
$server = [
    "method" => "custom",
    "server" = "http://proxy.apify.com:8000",
    "auth" => "auto:my_ultra_secret_password"
];

# IPVANISH valid syntax example
$server = [
    "method" => "CUSTOM",
    "server" => "akl-c12.ipvanish.com:1080",
    "auth"   => "my_zone_customer_id:my_zone_customer_password"
];
```

GET SYNTAX
--------

```php
# Simple GET
$test0 = $CurlX->get("http://httpbin.org/get");

# GET with Custom Headers
$headers = array(
    "Host: api.myip.com",
    "my-custom-header: my-header-value"
);

$test1 = $CurlX->get("http://httpbin.org/get", $headers);

# GET with Headers and Cookie File
$cookie = uniqid();

$test2 = $CurlX->get("http://httpbin.org/get", $headers, $cookie);

#GET with Headers, Cookie and Proxy Tunnel
$server = [
    "method" => "tunnel",
    "server" => "47.254.145.99:3128"
];

$test3 = $CurlX->get("http://httpbin.org/get", $headers, $cookie, $server);

# After all requests were complete, you can delete the cookie file, Only when you use the $cookie parameter.
$CurlX->deleteCookie();

# Response status of the request
var_dump($test3->isSuccess());
// bool(true)

# Status code of the request
var_dump($test3->getStatusCode());
// int(200)

# Content type of the request
var_dump($test3->getHeaders()["response"]["content-type"]);
// string(24) "text/html; charset=UTF-8"

# Body response of the request
var_dump($test3->getBody());
// string(51) "{...}"
```

POST SYNTAX
--------

```php
#Simple POST with NULL data
$test0 = $CurlX->post("http://httpbin.org/post");

#POST with Data-form and Custom Headers
$headers = array(
    "Host: httpbin.org",
    "my-custom-header: my-header-value"
);
$test1 = $CurlX->post("http://httpbin.org/post", "test_ID=666&hello=mom", $headers);

#POST with Json-Data and Custom Headers
$data = array(
    "hello" => "mom",
    "key" => "value"
);
$test2 = $CurlX->post("http://httpbin.org/post", $data, $headers);

#POST with Custom-Data, Custom Headers, and Cookie
$cookie = uniqid();
$test3 = $CurlX->post("http://httpbin.org/post", $data, $headers, $cookie);

#POST with Json-Data, Custom Headers, Cookie and Proxy Tunnel
$server = [
    "method" => "tunnel",
    "server" => "47.254.145.99:3128"
];
$test4 = $CurlX->post("http://httpbin.org/post", $data, $headers, $cookie, $server);
#After all requests were complete, you can delete the cookie file, Only when you use the $cookie parameter.
$CurlX->deleteCookie();

# Response status of the request
var_dump($test3->isSuccess());
// bool(true)

# Status code of the request
var_dump($test4->getStatusCode());
// int(200)

# Content type of the request
var_dump($test4->getHeaders()["response"]["content-type"]);
// string(24) "..."

# Body response of the request
var_dump($test4->getBody());
// string(51) "{...}"
```

Other functions
--------

```php
    // Set a custom option to current CURL structure
    $CurlX->setOpt([CURLOPT_HTTPAUTH => CURLAUTH_BEARER]);

    /**
     * Show all data process|errors of the request
     * 
     * debug(): now supports cli and json print
     * 
     * Recommended for develop work space
    */
    $CurlX->debug();
```

More?
--------
- More examples in [examples][] dir.

[examples]: https://github.com/devblack/curlx/tree/master/examples

Features
--------

- International Domains and URLs
- Custom Tunnel Http with Proxy, Socks, Luminati, Apify, IpVanish
- Cookie data re-utilization
- Custom HTTP METHODS


Installation
------------

### Install source from GitHub
To install the source code:

    $ git clone https://github.com/devblack/curlx.git

And include it in your scripts:

    require_once "CurlX.php";
    $CurlX = new CurlX();


### Install source from zip/tarball
Alternatively, you can fetch a [tarball][] or [zipball][]:

    $ curl -L https://github.com/devblack/curlx/tarball/master | tar xzv
    (or)
    $ wget https://github.com/devblack/curlx/tarball/master -O - | tar xzv

[tarball]: https://github.com/devblack/curlx/tarball/master
[zipball]: https://github.com/devblack/curlx/zipball/master


Contribute
----------

1. Check for open issues or open a new issue for a feature request or a bug
2. Fork [the repository][] on GitHub to start making your changes to the
    `master` branch (or branch off of it)
3. Write a test which shows that the bug was fixed or that the feature works as expected
4. Send a pull request and bug me until I merge it

[the repository]: https://github.com/devblack/curlx
