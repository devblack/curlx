<?php

namespace Thevenrex\Curlx\CookieJar;

use Thevenrex\Curlx\Interfaces\CookieJarInterface;

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