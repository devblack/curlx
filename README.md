CurlX - A curl basic syntax for PHP
================

CurlX is a HTTP basic library written in PHP, for human beings and has no dependencies, working with PHP 5.6+.

Basic get request with custom headers
--------

```php
$headers = array(
    'Host: api.myip.com',
    'my-custom-header: my-header-value'
);
$response = CurlX::get("https://api.myip.com/", $headers);

var_dump($response->status_code);
// int(200)

var_dump($response->headers['content-type']);
// string(24) "text/html; charset=UTF-8"

var_dump($response->body);
// string(51) "[...]"
```

Requests allows you to send **GET** and **POST** HTTP requests. You can add headers, form data, multipart files,
and parameters with simple arrays, and access the response data in the same way.


Features
--------

- International Domains and URLs
- Custom Tunnel Server with Proxy, Socket, Luminati, Apify


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