<?php

namespace Thevenrex\Curlx\CookieJar;

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
