<?php
/**
 * CurlX Lib - v0.0.3
 * @author devblack
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
     * @var cookie_file
     */
    private static $cookie_file=NULL;

    /**
     * requests error number, example: 5
     * @var error_code
     */
    private static $error_code;

    /**
     * requests error string, example: Connection closed after connect.
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
        CURLOPT_CONNECTTIMEOUT => 60,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_VERBOSE => 1
    ];

    /**
     * Basic curl_init for request structure
     * 
     * @param url
     * 
     * @return void
     */
    private static function Prepare(string $url) : void 
    {
        self::$ch = curl_init($url);
        self::SetOpt(self::$default);
    }

    /**
     * Set options to current curl structure
     * 
     * @param args
     * 
     * @return void
     */
    public static function SetOpt(array $option) : void 
    {
        curl_setopt_array(self::$ch, $option);
    }

    /**
     * Set a header to current curl structure
     * 
     * @param header
     * 
     * @return void
     */
    private static function Header(array $header) : void
    {
        self::SetOpt([CURLOPT_HTTPHEADER => $header]);
    }

    /**
     *  Set a proxy tunnel configuration to current curl structure
     *  Support: HTTP/S, SOCKS4, SOCKS5
     * 
     * @param args
     * 
     * @return void
     */
    private static function Tunnel(array $args) : void 
    {
        self::SetOpt([
            CURLOPT_PROXY => $args['SERVER'],
            CURLOPT_HTTPPROXYTUNNEL => true
        ]);
    }

    /**
     * Set configuration to proxy rotation in current curl structure
     * 
     * @param args
     * 
     * @return void
     */
    private static function Luminati(array $args) : void 
    {
        self::SetOpt([
            CURLOPT_PROXY => 'http://zproxy.lum-superproxy.io:22225',
            CURLOPT_PROXYUSERPWD => "{$args['USERNAME']}-route_err-pass_dyn-country-{$args['COUNTRY']}-session-{$args['SESSION']}:{$args['PASSWORD']}"
        ]);
    }

    /**
     * Set configuration to proxy rotation in current curl structure
     * 
     * @param args
     * 
     * @return void
     */
    private static function Apify(array $args) : void 
    {
        self::SetOpt([
            CURLOPT_PROXY => 'http://proxy.apify.com:8000',
            CURLOPT_PROXYUSERPWD => "auto:{$args['PASSWORD']}"
        ]);
    }

    /**
     * Set configuration to proxy rotation in current curl structure
     * 
     * @param args
     * 
     * @return void
     */
    private static function Ipvanish(array $args) : void 
    {
        self::SetOpt([
            CURLOPT_PROXY => "{$args['SERVER']}:1080",
            CURLOPT_PROXYUSERPWD => $args['AUTH']
        ]);
    }

    /**
     * Detect the tunnel configuration
     * 
     * @param args
     * 
     * @return void
     */
    private static function AutoRouter($args) : void 
    {
        switch (strtoupper($args['METHOD'])) {
            case 'TUNNEL':
                self::Tunnel($args);
            break;
            case 'LUMINATI':
                self::Luminati($args);
            break;
            case 'APIFY':
                self::Apify($args);
            break;
            case 'IPVANISH':
                self::Ipvanish($args);
            break;
        }
    }

    /**
     * Created a file in the temp dir and import to current curl structure
     * 
     * @param file
     * 
     * @return void
     */
    private static function SetCookie(string $file) : void 
    {
        if (empty(self::$cookie_file)) {
            self::$cookie_file = sprintf("%s/curlX_%s.txt", sys_get_temp_dir(), $file);
        }
        
        self::SetOpt([
            CURLOPT_COOKIEJAR => self::$cookie_file,
            CURLOPT_COOKIEFILE => self::$cookie_file
        ]);
    }

    /**
     * Delete the current cookie file in curl structure
     * 
     * @return void
     */
    public static function DeleteCookie() : void 
    {
        unlink(self::$cookie_file);
    }

    /**
     * Check parameters for curl structure
     * 
     * @param headers
     * @param cookie
     * @param server
     * 
     * @return void
     */
    private static function CheckParam(array $headers=NULL, string $cookie=NULL, array $server=NULL) : void 
    {
        if (!empty($headers) && is_array($headers))
            self::Header($headers);

        if (!empty($cookie))
            self::SetCookie($cookie);

        if (!empty($server) && is_array($server))
            self::AutoRouter($server);
    }

    /**
     * Send a GET request method with custom headers, cookies and server tunnel
     *
     * @param url
     * @param headers
     * @param cookie
     * @param server
     *
     * @return response|error_string
     */
    public static function Get(string $url, array $headers=NULL, string $cookie=NULL, array $server=NULL) : object
    {
        self::Prepare($url);
        self::SetOpt([CURLOPT_USERAGENT => self::userAgent()]);
        self::CheckParam($headers, $cookie, $server);
        return self::Run();
    }

    /**
     * Send a POST request method with custom post data, headers, cookies and server tunnel
     *
     * @param url
     * @param data
     * @param headers
     * @param cookie
     * @param server
     *
     * @return response|error_string
     */
    public static function Post(string $url, $data=NULL, array $headers=NULL, string $cookie=NULL, array $server=NULL) : object
    {
        self::Prepare($url);
        self::SetOpt([
            CURLOPT_USERAGENT      => self::userAgent(),
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => self::DataType($data)
        ]);
        self::CheckParam($headers, $cookie, $server);
        return self::Run();
    }

    /**
     * Send a CUSTOM request method with data, headers, cookies and server tunnel
     *
     * @param url
     * @param method
     * @param data
     * @param headers
     * @param cookie
     * @param server
     *
     * @return response|error_string
     */
    public static function Custom(string $url, string $method='GET', $data=NULL, array $headers=NULL, string $cookie=NULL, array $server=NULL) : void 
    {
        self::Prepare($url);
        self::SetOpt([
            CURLOPT_USERAGENT      => self::userAgent(),
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => self::DataType($data)
        ]);
        self::CheckParam($headers, $cookie, $server);
    }

    /**
     * Send the request structure
     * @return response|error_string
     */
    public static function Run() : object
    {
        self::$response = curl_exec(self::$ch);
        self::$info = curl_getinfo(self::$ch);

        // Request failed
        if (self::$response === FALSE) {
            self::$error_code = curl_errno(self::$ch);
            self::$error_string = curl_error(self::$ch);

            curl_close(self::$ch);

            return (object) [
                'success' => false,
                'code' => self::$info['http_code'],
                'headers'  => self::$info,
                'body'     => 'Request problem, use Debug() for more information.',
                'errno'    => self::$error_code,
                'error'    => self::$error_string
            ];
        } else {
            curl_close(self::$ch);

            return (object) [
                'success' => true,
                'code' => self::$info['http_code'],
                'headers'  => self::$info,
                'body'     => self::$response
            ];
        }
    }

    /**
     * Show all data process|errors of the request
     * @return information|errors
     */
    public static function Debug(bool $pretty=false) 
    {
        if ($pretty) {
            header('Content-Type: application/json');
            echo json_encode([
                'curlx_debug' => [
                    'information' => self::$info,
                    'errors' => [
                        'errnum' => self::$error_code,
                        'errstr' => self::$error_string
                    ],
                    'response' => self::$response
                ]
            ]);
        } else {
            echo sprintf("=============================================<br/>\n<h2>CURLX DEBUG</h2>\n=============================================<br/>\n<h3>Response</h3>\n<code>%s</code><br/>\n\n", nl2br(htmlentities(self::$response)));
            if (self::$error_string) {
                echo sprintf("=============================================<br/>\n<h3>Errors</h3>\n<strong>Code: </strong>%d<br/>\n<strong>Message: </strong>%d<br/>\n", self::$error_code, self::$error_string);
            }
            echo sprintf("=============================================<br/>\n<h3>Information</h3><pre>%s</pre>", print_r(self::$info, true));
        }
    }

    /**
     * Detect data type
     * @param data
     * @return string
     */
    private static function DataType($data) 
    {
        if (empty($data)) {
            return false;
        } elseif (is_array($data) || is_object($data)) {
            return json_encode($data);
        } else {
            return $data;
        }
    }

    /**
     * Can split a string by two specify strings
     * @param str
     * @param start
     * @param end
     * @return string
     */
    public static function ParseString(string $str, string $start, string $end) : string 
    {
        return explode($end, explode($start, $str)[1])[0];
    }

    /**
     * Remove all spaces from any string
     * @param str
     * @return string
     */
    public static function CleanString(string $str) : string 
    {
        return preg_replace('/\s+/', '', $str);
    }

    /**
    * Get a rand value from specify file.txt
    * @param file
    * @return string
    */
    public static function GetRandVal(string $file) : string 
    {
        $_ = file($file);
        return self::CleanString($_[rand(0, (count($_) - 1))]);
    }

    /**
     * return a random user agent
     * @return string
     */
    private static function UserAgent() : string 
    {
        $uas = [
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:83.0) Gecko/20100101 Firefox/83.0",
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:81.0) Gecko/20100101 Firefox/81.0",
            "Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:80.0) Gecko/20100101 Firefox/80.0",
            "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:80.0) Gecko/20100101 Firefox/80.0",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:81.0) Gecko/20100101 Firefox/81.0",
            "Mozilla/5.0 (X11; Linux x86_64; rv:80.0) Gecko/20100101 Firefox/80.0",
            "Mozilla/5.0 (X11; Linux x86_64; rv:75.0) Gecko/20100101 Firefox/75.0",
            "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:79.0) Gecko/20100101 Firefox/79.0",
            "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:77.0) Gecko/20100101 Firefox/77.0",
            "Mozilla/5.0 (X11; U; Linux i686; fr; rv:1.8) Gecko/20060110 Debian/1.5.dfsg-4 Firefox/1.5",
            "Mozilla/5.0 (Android 10; Mobile; rv:79.0) Gecko/79.0 Firefox/79.0",
            "Mozilla/5.0 (Android 9; Mobile; rv:68.6.0) Gecko/68.6.0 Firefox/68.6.0",
            "Mozilla/5.0 (Android 7.1.1; Mobile; rv:68.0) Gecko/68.0 Firefox/68.0",
            "Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_2 like Mac OS X) AppleWebKit/603.2.4 (KHTML, like Gecko) FxiOS/7.5b3349 Mobile/14F89 Safari/603.2.4",
            "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1.1 Safari/605.1.15",
            "Mozilla/5.0 (iPhone; CPU iPhone OS 13_3_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.5 Mobile/15E148 Snapchat/10.77.5.59 (like Safari/604.1)",
            "Mozilla/5.0 (iPhone; CPU iPhone OS 13_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/80.0.3987.95 Mobile/15E148 Safari/604.1"
        ];
        return $uas[array_rand($uas)];
    }
}

/***
 * COD3D BY D3VBL4CK
 */
