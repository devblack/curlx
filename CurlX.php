<?php
/**
 * CurlX Lib - v0.0.2
 *
 * COD3D BY D3VBL4CK
 */
class CurlX
{
    /**
     * private access to response
     * @var response
     */
    private static $response;

    /**
     * public access to info args of requests
     * @var info
     */
    private static $info;

    /**
     * private and global var
     * @var ch
     */
    private static $ch;

    /**
     * private cookie-file var
     * @var cookiefile
     */
    private static $cookiefile;

    /**
     * requests error number, example: 5
     * @var error_code
     */
    private static $error_code;

    /**
     * requests error string, example: CURLE_COULDNT_RESOLVE_PROXY
     * @var error_string
     */
    private static $error_string;

    /**
     * Default Options for request structure
     */
    private static $default = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_CONNECTTIMEOUT => 70,
        CURLOPT_TIMEOUT        => 70,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_VERBOSE => 1
    ];

    /**
     * function prepare, basic curl_init for requests
     * @param url
     * @return void
     */
    private static function prepare(string $url) {
        self::$ch = curl_init($url);
    }

    /**
     * function header, set a header for request structure
     * @param header
     * @return void
     */
    private static function header(array $header) {
        curl_setopt(self::$ch, CURLOPT_HTTPHEADER, $header);
    }

    /**
     *  function tunnel, support HTTP/S, SOCKS4, SOCKS5 for request structure
     * @param args
     * @return void
     */
    private static function tunnel(array $args) {
        curl_setopt_array(self::$ch, [
            CURLOPT_PROXY => "{$args['TYPE']}://{$args['SERVER']}",
            CURLOPT_HTTPPROXYTUNNEL => true
        ]);
    }

    /**
     * function luminati, set a luminati auto-router proxy configuration
     * @param args
     * @return void
     */
    private static function luminati(array $args) {
        curl_setopt_array(self::$ch, [
            CURLOPT_PROXY => 'http://zproxy.lum-superproxy.io:22225',
            CURLOPT_PROXYUSERPWD => "{$args['USERNAME']}-route_err-pass_dyn-country-{$args['COUNTRY']}-session-{$args['SESSION']}:{$args['PASSWORD']}"
        ]);
    }

    /**
     * function apify, set a apify auto-router proxy configuration
     * @param args
     * @return void
     */
    private static function apify(array $args) {
        curl_setopt_array(self::$ch, [
            CURLOPT_PROXY => 'http://proxy.apify.com:8000',
            CURLOPT_PROXYUSERPWD => "auto:{$args['PASSWORD']}"
        ]);
    }

    /**
     * function RandProxy, get a rand proxy from proxies.txt
     * @return proxy
     */
    public static function RandProxy() {
        $proxy = file("proxies.txt");
        return $proxy[rand(0, (count($proxy) - 1))];
    }

    /**
     * function AutoRouter, detect the tunnel configuration
     * @param args
     * @return void
     */
    private static function AutoRouter($args) {
        switch (strtoupper($args['METHOD'])) {
            case 'TUNNEL':
                self::tunnel($args);
            break;
            case 'LUMINATI':
                self::luminati($args);
            break;
            case 'APIFY':
                self::apify($args);
            break;
        }
    }

    /**
     * function cookies, created a file in the temp dir and saved it
     * @param file
     * @return void
     */
    private static function cookies(string $file) {
        self::$cookiefile = sys_get_temp_dir."/$file.txt";
        curl_setopt_array(self::$ch, [
            CURLOPT_COOKIEJAR => self::$cookiefile,
            CURLOPT_COOKIEFILE => self::$cookiefile
        ]);
    }

    /**
     * function deleteCookie, this function delete the current cookie file of the current request
     * @return bool
     */
    public static function deleteCookie() {
        unlink(self::$cookiefile);
    }

    /**
     * Check parameters
     */
    private static function CheckParam(array $headers=NULL, string $cookie=NULL, array $server=NULL) {
        if (!empty($headers) && is_array($headers))
            self::header($headers);

        if (!empty($cookie))
            self::cookies($cookie);

        if (!empty($server) && is_array($server))
            self::AutoRouter($server);
    }

    /**
     * function get, this function send a get request with custom headers, cookies and server tunnel
     *
     * @param url
     * @param headers
     * @param cookie
     * @param server
     *
     * @return response|error_string
     */
    public static function get(string $url, array $headers=NULL, string $cookie=NULL, array $server=NULL) {
        $options = array_replace(self::$default, [
            CURLOPT_USERAGENT => self::userAgent()
        ]);

        self::prepare($url);

        curl_setopt_array(self::$ch, $options);

        self::CheckParam($headers, $cookie, $server);

        return self::run();
    }

    /**
     * function post, this function send a post request with custom post data, headers, cookies and server tunnel
     *
     * @param url
     * @param data
     * @param headers
     * @param cookie
     * @param server
     *
     * @return response|error_string
     */
    public static function post(string $url, mixed $data=NULL, array $headers=NULL, string $cookie=NULL, array $server=NULL) {
        $options = array_replace(self::$default,[
            CURLOPT_USERAGENT      => self::userAgent(),
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => is_array($data) ? json_encode($data) : $data
        ]);

        self::prepare($url);

        curl_setopt_array(self::$ch, $options);

        self::CheckParam($headers, $cookie, $server);

        return self::run();
    }

    /**
     * function put, this function send a post request with custom post data, headers, cookies and server tunnel
     *
     * @param url
     * @param data
     * @param headers
     * @param cookie
     * @param server
     *
     * @return response|error_string
     */
    public static function put(string $url, mixed $data=NULL, array $headers=NULL, string $cookie=NULL, array $server=NULL) {
        $options = array_replace(self::$default, [
            CURLOPT_USERAGENT      => self::userAgent(),
            CURLOPT_CUSTOMREQUEST  => "PUT",
            CURLOPT_POSTFIELDS     => is_array($data) ? json_encode($data) : $data,
        ]);

        self::prepare($url);

        curl_setopt_array(self::$ch, $options);

        self::CheckParam($headers, $cookie, $server);

        return self::run();
    }

    /**
     * function delete, this function send a post request with custom post data, headers, cookies and server tunnel
     *
     * @param url
     * @param data
     * @param headers
     * @param cookie
     * @param server
     *
     * @return response|error_string
     */
    public static function delete(string $url, mixed $data=NULL, array $headers=NULL, string $cookie=NULL, array $server=NULL) {
        $options = array_replace(self::$default, [
            CURLOPT_USERAGENT      => self::userAgent(),
            CURLOPT_CUSTOMREQUEST  => 'DELETE',
            CURLOPT_POSTFIELDS     => is_array($data) ? json_encode($data) : $data,
        ]);

        self::prepare($url);

        curl_setopt_array(self::$ch, $options);

        self::CheckParam($headers, $cookie, $server);

        return self::run();
    }

    /**
     * function run, send the request structure
     * @return response|error_string
     */
    private static function run() {
        self::$response = curl_exec(self::$ch);
        self::$info = curl_getinfo(self::$ch);

        // Request failed
        if (self::$response === FALSE) {
            self::$error_code = curl_errno(self::$ch);
            self::$error_string = curl_error(self::$ch);

            curl_close(self::$ch);

            return (object) [
                'status_code' => self::$info['http_code'],
                'headers'  => self::$info,
                'body'     => self::$error_string,
                'errno'    => self::$error_code,
                'error'    => self::$error_string
            ];
        } else {
            curl_close(self::$ch);

            return (object) [
                'status_code' => self::$info['http_code'],
                'headers'  => self::$info,
                'body'     => self::$response
            ];
        }
    }

    /**
     * function debug, this function show all data process|errors of the request
     * @return information|errors
     */
    public static function debug() {
        echo "=============================================<br/>\n";
        echo "<h2>REQUESTS DEBUG</h2>\n";
        echo "=============================================<br/>\n";
        echo "<h3>Response</h3>\n";
        echo "<code>" . nl2br(htmlentities(self::$response)) . "</code><br/>\n\n";

        if (self::$error_string) {
            echo "=============================================<br/>\n";
            echo "<h3>Errors</h3>";
            echo "<strong>Code:</strong> " . self::$error_code . "<br/>\n";
            echo "<strong>Message:</strong> " . self::$error_string . "<br/>\n";
        }

        echo "=============================================<br/>\n";
        echo "<h3>Info</h3>";
        echo "<pre>";
        print_r(self::$info);
        echo "</pre>";
    }

    /**
     * function ParseString, this function can split a string by two strings
     * @return string
     */
    public static function ParseString(string $string, string $start, string $end) {
        return explode($end, explode($start, $string)[1])[0];
    }

    /**
     * function userAgent, this function return a random user agent
     * @return string
     */
    private static function userAgent() {
        $uas = [
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.246",
            "Mozilla/5.0 (X11; CrOS x86_64 8172.45.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.64 Safari/537.36",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/601.3.9 (KHTML, like Gecko) Version/9.0.2 Safari/601.3.9",
            "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36",
            "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:15.0) Gecko/20100101 Firefox/15.0.1",
            "Mozilla/5.0 (Linux; Android 7.0; SM-G892A Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/60.0.3112.107 Mobile Safari/537.36",
            "Mozilla/5.0 (X11; Linux i686; rv:64.0) Gecko/20100101 Firefox/64.0",
            "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0",
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36",
            "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36",
            "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36",
            "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:63.0) Gecko/20100101 Firefox/63.0",
            "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.10; rv:62.0) Gecko/20100101 Firefox/62.0",
            "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; Avant Browser; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)",
            "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; Avant Browser; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.0.04506; .NET CLR 3.5.21022; InfoPath.2)",
            "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; Avant Browser; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30618; InfoPath.1)",
            "Mozilla/5.0 (compatible; MSIE 9.0; AOL 9.7; AOLBuild 4343.19; Windows NT 6.1; WOW64; Trident/5.0; FunWebProducts)",
            "Mozilla/4.0 (compatible; MSIE 8.0; AOL 9.7; AOLBuild 4343.27; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)",
            "Mozilla/4.0 (compatible; MSIE 8.0; AOL 9.7; AOLBuild 4343.21; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET4.0C; .NET4.0E)",
            "Mozilla/5.0 (Windows NT 6.2) AppleWebKit/535.7 (KHTML, like Gecko) Comodo_Dragon/16.1.1.0 Chrome/16.0.912.63 Safari/535.7",
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/51.0.2704.79 Safari/537.36 Edge/14.14931",
            "Opera/9.80 (X11; Linux i686; Ubuntu/14.10) Presto/2.12.388 Version/12.16",
            "Opera/9.80 (Macintosh; Intel Mac OS X 10.14.1) Presto/2.12.388 Version/12.16",
            "Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14",
            "Mozilla/5.0 (Windows; U; Windows NT 6.1; x64; fr; rv:1.9.1.1) Gecko/20090722 Firefox/3.5.1 Orca/1.2 build 2",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A",
            "Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2",
            "Mozilla/5.0 (Linux; U; Android 4.0.3; ko-kr; LG-L160L Build/IML74K) AppleWebkit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30",
            "Mozilla/5.0 (Linux; U; Android 4.0.3; de-ch; HTC Sensation Build/IML74K) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30",
            "Mozilla/5.0 (Linux; U; Android 2.3; en-us) AppleWebKit/999+ (KHTML, like Gecko) Safari/999.9",
            "Mozilla/5.0 (BlackBerry; U; BlackBerry 9900; en) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.1.0.346 Mobile Safari/534.11+",
            "Mozilla/5.0 (BlackBerry; U; BlackBerry 9860; en-US) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.0.0.254 Mobile Safari/534.11+"
        ];
        return $uas[array_rand($uas)];
    }
}

new CurlX;
/***
 * COD3D BY D3VBL4CK
 */
