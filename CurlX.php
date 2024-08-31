<?php

class Helper {
    public function parseHeaders(string $raw) : array
    {
        $raw = preg_split('/\r\n/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        $http_headers = [];

        for ($i = 1; $i < count($raw); $i++) {
            if (str_contains($raw[$i], ':')) {
                list($key, $value) = explode(':', $raw[$i], 2);
                $key = trim($key);
                $value = trim($value);
                isset($http_headers[$key]) ? $http_headers[$key] .= ',' . $value : $http_headers[$key] = $value;
            }
        }

        return [$raw['0'] ??= $raw['0'], $http_headers];
    }

    public function parseArray(array $raw) : array
    {
        if (array_key_exists('request_header', $raw)) {
            list($scheme, $headers) = $this->parseHeaders($raw['request_header']);
            $nh['scheme'] = $scheme;
            $nh += $headers;
            $raw['request_header'] = $nh;
        }

        return $raw;
    }

    public function parseHeadersHandle($raw) : array
    {
        if (empty($raw)) {
            return [];
        }

        list($scheme, $headers) = $this->parseHeaders($raw);
        $request_headers['scheme'] = $scheme;
        unset($headers['request_header']);

        foreach ($headers as $key => $value) {
            $request_headers[$key] = $value;
        }

        return $request_headers;
    }
}


class CurlException extends Exception {}

class Response {

    function __construct(
        private readonly bool  $success = false,
        private readonly int   $status_code = 200,
        private readonly array $headers = [],
        private                $body = null,
        private                $reason = null
    ) {}

    public function isSuccess(): int
    {
        return $this->success;
    }

    public function getStatusCode(): int
    {
        return $this->status_code;
    }

    public function getHeaders(): array {
        return $this->headers;
    }

    public function getBody(): ?string {
        return $this->body;
    }

    public function getReason(): ?string {
        return $this->reason;
    }
}

class CurlX extends Helper
{
    private array $default = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLINFO_HEADER_OUT    => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ];

    private string $cacheDir = '';

    private CurlHandle $ch;

    private array|false $info;
    private stdClass $callback;

    private string $cookieFile = '';
    private string $userAgent = 'CurlX v2.0.0b (Created by @d3vbl4ck)';

    private int $error_code;
    private string $error_string;

    private bool|string $body;

    public function __construct(array $config = []) {
        $this->default =  array_replace($this->default, $config);
    }

    public function prepareHandle(string $url): void
    {
        // start curlHandle
        $this->ch = curl_init($url);
        $this->setOpt($this->default);
    }

    public function setOpt(array $option) : void
    {
        curl_setopt_array($this->ch, $option);
    }

    private function setHeader(array $header) : void
    {
        $this->setOpt([CURLOPT_HTTPHEADER => $header]);
    }

    private function tunnel(array $args) : void
    {
        $this->setOpt([
            CURLOPT_PROXY => $args['server'],
            CURLOPT_HTTPPROXYTUNNEL => true
        ]);
    }

    private function proxyAuth(array $args) : void
    {
        $this->setOpt([
            CURLOPT_PROXY => $args['server'],
            CURLOPT_PROXYUSERPWD => $args['auth']
        ]);
    }

    /**
     * @param array $args
     * @return void
     * @throws CurlException
     */
    private function autoRouter(array $args): void
    {
        $args = array_change_key_case($args);

        match($args['method']) {
            'tunnel' => $this->tunnel($args),
            'custom' => $this->proxyAuth($args),
            default => throw new CurlException('Invalid proxy router.')
        };
    }

    /**
     * @param string $file
     * @return void
     * @throws CurlException
     */
    private function setCookie(CookieJarInterface|string $file_name) : void
{
    $this->cacheDir = dirname(__FILE__);

    if (!is_dir($this->cacheDir . '/Cache/')) {
        mkdir($this->cacheDir . '/Cache/', 0755);
    }

    if ($file_name instanceof CookieJarInterface) {
        $file = $file_name->getFileName();
        $file_name
            ->setFileName($this->cacheDir . '/Cache/curlX_' . $file . '.txt')
            ->save();
    } else {
        $file = $file_name;
    }

    $this->cookieFile = sprintf("%s/Cache/curlX_%s.txt", $this->cacheDir, $file);
    if (!is_writable($this->cacheDir)) {
        throw new CurlException('The current directory is not writable, please add permissions 0755 to Cache dir and 0644 to CurlX.php');
    }

    $this->setOpt([
        CURLOPT_COOKIEJAR => $this->cookieFile,
        CURLOPT_COOKIEFILE => $this->cookieFile
    ]);
}

    /**
     * @return void
     * @throws CurlException
     */
    public function deleteCookie(): void
    {
        if (empty($this->cacheDir)) {
            throw new CurlException('Cookie function (setCookie) was not called!');
        }

        if (!is_file($this->cookieFile)) {
            throw new CurlException(sprintf("The filename: %s not exits in %s.", $this->cookieFile, $this->cacheDir));
        }

        unlink($this->cookieFile);
    }


    /**
     * @param $data
     * @return false|string
     */
    private function dataType($data): false|string
    {
        return match(gettype($data)) {
            'string' => $data,
            'array', 'object' => json_encode($data),
            default => false
        };
    }

    /**
     * @param array|null $headers
     * @param string|null $cookie
     * @param array|null $server
     * @return void
     * @throws CurlException
     */
    private function checkParams(?array $headers = null, string|CookieJarInterface $cookie = null, ?array $server = null): void
    {
        if (is_array($headers)) {
            $this->setHeader($headers);
        }

        if (isset($cookie)) {
            $this->setCookie($cookie);
        }

        if (is_array($server)) {
            $this->autoRouter($server);
        }
    }

    /**
     * @param string $url
     * @param array|null $headers
     * @param string|null $cookie
     * @param array|null $server
     * @return object
     * @throws Exception
     */
    public function get(string $url, ?array $headers=null, string|CookieJarInterface $cookie=null, ?array $server=null): object
    {
        $this->prepareHandle($url);

        $this->setOpt([CURLOPT_USERAGENT => $this->userAgent]);
        $this->checkParams($headers, $cookie, $server);
        return $this->run();
    }

    /**
     * @param string $url
     * @param string|array|null $data
     * @param array|null $headers
     * @param string|null $cookie
     * @param array|null $server
     * @return object
     * @throws Exception
     */
    public function post(string $url, string|array|null $data=null, ?array $headers=null, string|CookieJarInterface $cookie=null, ?array $server=null) : object
    {
        $this->prepareHandle($url);

        $this->setOpt([
            CURLOPT_USERAGENT      => $this->userAgent,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $this->dataType($data)
        ]);
        $this->checkParams($headers, $cookie, $server);
        return $this->run();
    }

    /**
     * @param string $url
     * @param string $method
     * @param string|array|null $data
     * @param array|null $headers
     * @param string|null $cookie
     * @param array|null $server
     * @return void
     * @throws CurlException
     */
    public function custom(string $url, string $method='GET', string|array|null $data=null, ?array $headers=null, string|CookieJarInterface $cookie=null, ?array $server=null) : void
    {
        $this->prepareHandle($url);

        $this->setOpt([
            CURLOPT_USERAGENT      => $this->userAgent,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => $this->dataType($data)
        ]);
        $this->checkParams($headers, $cookie, $server);
    }

    private function close(): void
    {
        unset($this->ch);
    }

    public function run() : object
    {
        $this->makeStdClass();
        $this->setOpt([CURLOPT_HEADERFUNCTION => $this->fnHeader($this->callback)]);

        $this->body = curl_exec($this->ch);
        $this->info = curl_getinfo($this->ch);

        // Request failed
        if (!$this->body) {
            $this->error_code = curl_errno($this->ch);
            $this->error_string = curl_error($this->ch);

            $this->close();

            return new Response(
                success: false,
                status_code:  $this->info['http_code'],
                headers: [
                    'request'  => key_exists('request_header', $this->info) ? $this->parseHeadersHandle($this->info['request_header']) : [],
                    'response' => $this->callback->rawResponseHeaders
                ],
                body: 'Error code: ' . $this->error_code . ' / Message: '. $this->error_string
            );
        }

        $this->close();

        return new Response(
            success: true,
            status_code:  $this->info['http_code'],
            headers: [
                'request'  => $this->parseHeadersHandle($this->info['request_header']),
                'response' => $this->callback->rawResponseHeaders
            ],
            body: $this->body
        );
    }

    public function debug(): void
    {
        # check if is cli client
        if (php_sapi_name() === 'cli') {
            echo "=============================================\nCURLX DEBUG\n=============================================\n";
            echo "Response:\n" . $this->body . "\n\n";
            echo "=============================================\n";
            echo "Information:\n";
            echo print_r([
                    'request_headers' => $this->parseArray($this->info),
                    'response_headers' => $this->parseHeadersHandle($this->callback->rawResponseHeaders)
                ], true) . "\n";

            if (isset($this->error_string)) {
                echo "=============================================\n";
                echo "Errors\n";
                echo "Code: " . $this->error_code . "\n";
                echo "Message: " . $this->error_string . "\n";
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'curlx_debug' => [
                    'information' => [
                        'request_headers'  => $this->parseArray($this->info),
                        'response_headers' => $this->parseHeadersHandle($this->callback->rawResponseHeaders)
                    ],
                    'errors' => [
                        'errnum' => $this->error_code ?? '',
                        'errstr' => $this->error_string ?? ''
                    ],
                    'response' => $this->body
                ]
            ]);
        }
    }

    private function makeStdClass(): void
    {
        $this->callback = (object)['rawResponseHeaders' => ''];
    }

    private function fnHeader($cb): Closure
    {
        return function ($_, $header) use ($cb) {
            $cb->rawResponseHeaders .= $header;
            return strlen($header);
        };
    }
}

interface CookieJarInterface {

    public function setFileName(string $filename);

    public function getFileName(): string;
}

class CookieJar implements CookieJarInterface {

    protected string $banner = "# Netscape HTTP Cookie File\n# https://curl.se/docs/http-cookies.html\n# This file was generated by libcurl! Edit at your own risk.\n\n";

    private $cookies = [];

    protected $path;

    public function __construct(
        public string $filename = '',
    ) {
        $this->filename = uniqid();
    }

    public function setFileName(string $filename) {
        $this->filename = $filename;
        return $this;
    }

    public function getFileName(): string {
        return $this->filename;
    }

    public function add(Cookie $cookie) {
        $this->cookies[] = $cookie;
        return $this;
    }

    public function parseCookies() {
        $all_cookies = $this->banner;

        foreach($this->cookies as $cookie) {
            $all_cookies .= $cookie->get() . "\n";
        }

        return trim($all_cookies);
    }

    public function save() {
        file_put_contents(
            $this->filename,
            $this->parseCookies()
        );
    }

    public function delete() {
        if(file_exists($this->filename)) {
            unlink($this->filename);
        }
    }
}

class Cookie {

    public const HTTP_ONLY = '#HttpOnly_.';

    public function __construct(
        public string $domain = '',
        public string $includeSubDomains = 'TRUE',
        public string $path = '/',
        public string $httpOnly = 'FALSE',
        public string $expire = '',
        public string $name = '',
        public string $value = '',
    ) {
   
    }

    public function get() {
        $cookie = [
            'domain' => $this->httpOnly == 'TRUE' ? self::HTTP_ONLY . $this->domain : $this->domain,
            'includeSubDomains' => $this->includeSubDomains,
            'path' => $this->path,
            'httpOnly' => $this->httpOnly,
            'expire' => $this->expire,
            'name' => $this->name,
            'value' => $this->value
        ];

        return implode("\t", $cookie);
    }
}
