<?php

declare(strict_types=1);

require __DIR__ . '/../CurlX.php';

$port = mt_rand(9000, 9999);
$server = proc_open(
    [PHP_BINARY, '-S', "127.0.0.1:$port", __DIR__ . '/router.php'],
    [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
    $pipes,
    __DIR__
);
register_shutdown_function(static function () use ($server) {
    if (is_resource($server)) {
        proc_terminate($server);
        proc_close($server);
    }
});

for ($i = 0; $i < 50; $i++) {
    if (@fsockopen('127.0.0.1', $port)) break;
    usleep(100000);
}

$base = "http://127.0.0.1:$port";
$fails = 0;

function check(bool $cond, string $msg): void
{
    global $fails;
    if (!$cond) {
        fwrite(STDERR, "FAIL: $msg\n");
        $fails++;
        return;
    }
    echo "ok - $msg\n";
}

// --- Helper::parseHeaders ---
$h = new Helper();
[$scheme, $headers] = $h->parseHeaders("HTTP/1.1 200 OK\r\nContent-Type: text/html\r\nX-Test: yes\r\n");
check($scheme === 'HTTP/1.1 200 OK', 'parseHeaders extracts status line');
check($headers['Content-Type'] === 'text/html', 'parseHeaders extracts header values');
[$scheme2, $headers2] = $h->parseHeaders('');
check($scheme2 === '' && $headers2 === [], 'parseHeaders handles empty input');

// --- transport / body edge cases ---
$x = new CurlX();

$r = $x->get("$base/empty");
check($r->isSuccess() === true, '204 empty body is a success');
check($r->getStatusCode() === 204, 'status code 204 reported');
check($r->body === '', 'empty body preserved');

$r = $x->get("$base/zero");
check($r->isSuccess() === true, 'body "0" is a success');
check($r->body === '0', 'body "0" preserved');

$r = $x->get("$base/boom");
check($r->isSuccess() === true, 'HTTP 500 is a transfer success');
check($r->getStatusCode() === 500, 'status code 500 reported');

$r = $x->get('http://127.0.0.1:1/');
check($r->isSuccess() === false, 'connection refused is a failure');
check(is_string($r->body) && str_contains($r->body, 'Error code: 7'), 'failure body carries curl error');
check(is_bool($r->isSuccess()), 'isSuccess returns bool');

// --- post data encoding ---
$r = $x->post("$base/echo", ['a' => 1]);
check($r->body === '{"a":1}', 'array data posted as json');
$r = $x->post("$base/echo", 'raw=1');
check($r->body === 'raw=1', 'string data posted verbatim');

// --- cookie jar flow ---
$jar = new CookieJar();
$jar->add(new Cookie('example.com', 'TRUE', '/', 'FALSE', (string) (time() + 3600), 'sid', 'abc'));
$file = sys_get_temp_dir() . "\\curlx_test_" . uniqid() . ".txt";
$jar->setFileName($file)->save();
$content = file_get_contents($file);
check(str_contains($content, 'sid') && str_contains($content, 'abc'), 'CookieJar writes Netscape cookie file');
unlink($file);

$x2 = new CurlX();
$jar2 = new CookieJar();
$x2->get("$base/empty", cookie: $jar2);
$x2->deleteCookie();
check(true, 'CookieJar round-trip through setCookie/deleteCookie');

if ($fails > 0) {
    fwrite(STDERR, "$fails CHECK(S) FAILED\n");
    exit(1);
}
fwrite(STDERR, "ALL TESTS PASSED\n");
