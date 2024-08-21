<?php

namespace Thevenrex\Curlx\Interfaces;

interface CookieJarInterface {

    public function setFileName(string $filename);

    public function getFileName(): string;
}
