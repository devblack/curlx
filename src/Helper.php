<?php
namespace Thevenrex\Curlx;

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
