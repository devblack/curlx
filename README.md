CurlX - A curl basic library for PHP
================

CurlX is a HTTP basic library written in PHP, for human beings and has no dependencies, working with PHP 7.3+.

CurlX allows you to send **GET** and **POST** HTTP requests. You can add headers, form data, json data,
and parameters with simple arrays, and access the response data in the same way. You can add a HTTP TUNNEL with SOCKS, PROXY, [LUMINATI][] IP ROTATION and [APIFY][] IP ROTATION.

[LUMINATI]: https://luminati.io
[APIFY]: https://apify.com

GET and POST Syntax
--------

```php
# GET
CurlX::get("https://api.myip.com/", $headers, $cookie, $server);

# POST
CurlX::post("https://api.myip.com/", $data, $headers, $cookie, $server);
```

HTTP TUNNEL configuration - Proxy, Socks, Luminati Rotation, Apify Rotation
--------

```php
#Proxy valid syntax
$server = [
    'TYPE' => "PROXY",
    'PROXY_TYPE' => 'https',#Or http
    'PROXY' => "47.254.145.99:3128"
];

#Socks valid syntax
$server = [
    'TYPE' => "SOCKS",
    'SOCK_TYPE' => "socks5", #Or socks4
    'SOCK' => "192.169.215.124:3050"
];

#Luminati valid syntax
$server = [
    'TYPE' => "LUMINATI",
    'USERNAME' => "lum-customer-hl_876f552a-zone-static",#lum-customer-CUSTOMER-zone-static
    'PASSWORD' => "my_ultra_secret_password",
    'COUNTRY' => "RU",
    'SESSION' => mt_rand()
];

#Apify valid syntax
$server = [
    'TYPE' => "APIFY",
    'PASSWORD' => "my_ultra_secret_password"
];
```

GET syntax with Custom Headers, Cookie Name, Proxy Server
--------

```php
#Simple GET
$test0 = CurlX::get("https://api.myip.com/");

#GET with Custom Headers
$headers = array(
    'Host: api.myip.com',
    'my-custom-header: my-header-value'
);
$test1 = CurlX::get("https://api.myip.com/", $headers);

#GET with Headers and Cookie
$cookie = uniqid('my_cookie_');
$test2 = CurlX::get("https://api.myip.com/", $headers, $cookie);

#GET with Headers, Cookie and Proxy Tunnel
$server = [
    'TYPE' => "PROXY",
    'PROXY_TYPE' => 'https',#Or http
    'PROXY' => "47.254.145.99:3128"
];
$test3 = CurlX::get("https://api.myip.com/", $headers, $cookie, $server);
#After all request was complete, you can delete the cookie file, Only with you use the $cookie parameter.
CurlX::deleteCookie();

# Status code of the request
var_dump($test3->status_code);
// int(200)

# Content type of the request
var_dump($test3->headers['content-type']);
// string(24) "text/html; charset=UTF-8"

# Body response of the request
var_dump($test3->body);
// string(51) "{...}"
```

POST syntax with Custom Headers, Cookie Name, Proxy Server
--------

```php
#Simple POST with NULL data
$test0 = CurlX::post("http://httpbin.org/post");

#POST with Data-form and Custom Headers
$headers = array(
    'Host: httpbin.org',
    'my-custom-header: my-header-value'
);
$test1 = CurlX::post("http://httpbin.org/post", "bla bla", $headers);

#POST with Json-Data and Custom Headers
$data = array(
    'hello' => "mom",
    'key' => "value"
);
$test2 = CurlX::post("http://httpbin.org/post", $data, $headers);

#POST with Json-Data, Custom Headers and Cookie
$cookie = uniqid('my_cookie_');
$test3 = CurlX::post("http://httpbin.org/post", $data, $headers, $cookie);

#POST with Json-Data, Custom Headers, Cookie and Proxy Tunnel
$server = [
    'TYPE' => "PROXY",
    'PROXY_TYPE' => 'https',#Or http
    'PROXY' => "47.254.145.99:3128"
];
$test4 = CurlX::post("http://httpbin.org/post", $data, $headers, $cookie, $server);
#After all request was complete, you can delete the cookie file, Only with you use the $cookie parameter.
CurlX::deleteCookie();

# Status code of the request
var_dump($test4->status_code);
// int(200)

# Content type of the request
var_dump($test4->headers['content-type']);
// string(24) "..."

# Body response of the request
var_dump($test4->body);
// string(51) "{...}"
```

Features
--------

- International Domains and URLs
- Custom Tunnel Http with Proxy, Socks, Luminati, Apify
- Cookie data reutilization


Installation
------------

### Install source from GitHub
To install the source code:

    $ git clone https://github.com/devblack/curlx.git

And include it in your scripts:

    require_once 'CurlX.php';


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
2. Fork [the repository][] on Github to start making your changes to the
    `master` branch (or branch off of it)
3. Write a test which shows that the bug was fixed or that the feature works as expected
4. Send a pull request and bug me until I merge it

[the repository]: https://github.com/devblack/curlx