A curl basic library for PHP7.3+
================

CurlX is a HTTP basic library written in PHP, for human beings and has no dependencies, working with PHP 7.3+.

![](https://i.imgur.com/AVwS6kZ.png)

CurlX allows you to send **GET**, **POST**, **PUT**, **DELETE** AND MORE HTTP METHODS. You can add headers, form data, json data,
and parameters with simple arrays, and access the response data in the same way. You can add a HTTP TUNNEL with PROXY, server ROTATIONS like [LUMINATI][], [APIFY][], [IPVANISH][].

[LUMINATI]: https://luminati.io/
[APIFY]: https://apify.com/
[IPVANISH]: https://www.ipvanish.com/

GET, POST AND CUSTOM Syntax
--------

```php
# GET
$CurlX::Get('https://api.myip.com/');

# POST
$CurlX::Post('https://api.myip.com/', 'my_form_id=test&hello=mom');

# CUSTOM
$CurlX::Custom('https://api.myip.com/', 'HEAD');
$CurlX::Run();
```

HTTP TUNNEL SYNTAX - Proxy (http/s, socks4, socks5), Luminati, Apify, IpVanish
--------

```php
# Proxy (http/s, socks4, socks5)
$server = [
    'METHOD' => 'TUNNEL',
    'SERVER' => '47.254.145.99:3128'
];

#Luminati valid syntax
$server = [
    'METHOD' => 'LUMINATI',
    'USERNAME' => 'lum-customer-hl_876f552a-zone-static',#lum-customer-CUSTOMER-zone-static
    'PASSWORD' => 'my_ultra_secret_password',
    'COUNTRY' => 'RU',
    'SESSION' => mt_rand()
];

#Apify valid syntax
$server = [
    'METHOD' => 'APIFY',
    'PASSWORD' => 'my_ultra_secret_password'
];

#IpVanish valid syntax
$server = [
    'METHOD' => 'IPVANISH',
    'SERVER' => 'akl-c12.ipvanish.com',
    'AUTH'   => 'my_zone_customer_id:my_zone_customer_password'
];
```

GET syntax with Custom Headers, Cookie Name, Proxy Server
--------

```php
#Simple GET
$test0 = $CurlX::Get("http://httpbin.org/get");

#GET with Custom Headers
$headers = array(
    'Host: api.myip.com',
    'my-custom-header: my-header-value'
);
$test1 = $CurlX::Get("http://httpbin.org/get", $headers);

#GET with Headers and Cookie
$cookie = uniqid();
$test2 = $CurlX::Get("http://httpbin.org/get", $headers, $cookie);

#GET with Headers, Cookie and Proxy Tunnel
$server = [
    'METHOD' => "TUNNEL",
    'SERVER' => "47.254.145.99:3128"
];
$test3 = $CurlX::Get("http://httpbin.org/get", $headers, $cookie, $server);
#After all request was complete, you can delete the cookie file, Only when you use the $cookie parameter.
$CurlX::deleteCookie();

# Response status of the request
var_dump($test3->success);
// bool(true)

# Status code of the request
var_dump($test3->code);
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
$test0 = $CurlX::Post("http://httpbin.org/post");

#POST with Data-form and Custom Headers
$headers = array(
    'Host: httpbin.org',
    'my-custom-header: my-header-value'
);
$test1 = $CurlX::Post("http://httpbin.org/post", "test_ID=666&hello=mom", $headers);

#POST with Json-Data and Custom Headers
$data = array(
    'hello' => "mom",
    'key' => "value"
);
$test2 = $CurlX::Post("http://httpbin.org/post", $data, $headers);

#POST with Custom-Data, Custom Headers and Cookie
$cookie = uniqid();
$test3 = $CurlX::Post("http://httpbin.org/post", $data, $headers, $cookie);

#POST with Json-Data, Custom Headers, Cookie and Proxy Tunnel
$server = [
    'METHOD' => "TUNNEL",
    'SERVER' => "47.254.145.99:3128"
];
$test4 = $CurlX::Post("http://httpbin.org/post", $data, $headers, $cookie, $server);
#After all request was complete, you can delete the cookie file, Only when you use the $cookie parameter.
$CurlX::deleteCookie();

# Response status of the request
var_dump($test3->success);
// bool(true)

# Status code of the request
var_dump($test4->code);
// int(200)

# Content type of the request
var_dump($test4->headers['content-type']);
// string(24) "..."

# Body response of the request
var_dump($test4->body);
// string(51) "{...}"
```

Other functions
--------

```php
    // Get a rand line from text file
    $CurlX::GetRandVal('proxies.txt');
    // Output: 202.137.25.8:8080

    // Parse a string by two specify strings
    $str = "curlx is the best for web scraping";
    $CurlX::ParseString($str, "curlx", "scraping")
    // Output: is the best for web

    // Clean all spaces from a string
    $CurlX::CleanString($str);
    // Output: curlxisthebestforwebscraping

    /**
     * Show all data process|errors of the request
     * 
     * Debug() : now support pretty print to html and json
     * @param true = json response
     * @param false = html response
     * 
     * Recommended for develop work space
     * 
    */
    $CurlX::Debug(false);
```

More?
--------
- More examples in [TESTING][] dir.

[TESTING]: https://github.com/devblack/curlx/tree/master/testing

Features
--------

- International Domains and URLs
- Custom Tunnel Http with Proxy, Socks, Luminati, Apify, IpVanish
- Cookie data reutilization
- Custom HTTP METHODS


Installation
------------

### Install source from GitHub
To install the source code:

    $ git clone https://github.com/devblack/curlx.git

And include it in your scripts:

    require_once 'CurlX.php';
    $CurlX = new CurlX;


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