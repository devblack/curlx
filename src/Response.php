<?php

namespace Thevenrex\Curlx;

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