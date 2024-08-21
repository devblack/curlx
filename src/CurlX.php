<?php

namespace Thevenrex\Curlx;

use CurlHandle;
use stdClass;
use Closure;
use Thevenrex\Curlx\Exceptions\CurlException;

use Thevenrex\Curlx\Interfaces\CookieJarInterface;

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
        // set the current dir
        $this->cacheDir = dirname(__FILE__);

        # check if the dir exits, if not create it
        if (!is_dir($this->cacheDir . '/Cache/')) {
            mkdir($this->cacheDir . '/Cache/', 0755);
        }

        if($file_name instanceof CookieJarInterface) 
            $file = $file_name->getFileName();
        
            $file_name
                ->setFileName($this->cacheDir . '/Cache/curlX_' . $file . '.txt')
                ->save();

        // PHP7.4+
        $this->cookieFile = sprintf("%s/Cache/curlX_%s.txt", $this->cacheDir, $file);
        // check if the dir is writable
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