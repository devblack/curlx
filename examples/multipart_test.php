<?php

/**
 * @return object{"boundary": string, "body": string}
 * @throws Exception
 */
function multiPartForm(array $fields, array|null $files = null): object
{
    $boundary = '----Boundary' . md5(uniqid('', true));
    $eol = "\r\n";
    $body = '';

    foreach ($fields as $name => $value) {
        $body .= "--$boundary$eol";
        $body .= "Content-Disposition: form-data; name=\"$name\"$eol$eol";
        $body .= "$value$eol";
    }

    if ($files) {
        foreach ($files as $fieldName => $filePath) {
            if (!file_exists($filePath)) {
                throw new Exception("File not found: $filePath");
            }

            $filename = basename($filePath);
            $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
            $fileContent = file_get_contents($filePath);

            $body .= "--$boundary$eol";
            $body .= "Content-Disposition: form-data; name=\"$fieldName\"; filename=\"$filename\"$eol";
            $body .= "Content-Type: $mimeType$eol$eol";
            $body .= $fileContent . $eol;
        }
    }

    $body .= "--$boundary--$eol";

    return (object) [
      "boundary" => $boundary,
      "body" => $body
    ];
}

require_once('../CurlX.php');

$CurlX = new CurlX();

try {
    $multi = multipartForm(
        ['username' => 'dave', 'email' => 'dave@example.com'],
        //['my_upload_input' => 'X://files/images/hentai.png']
    );

    $response = $CurlX->post(
        url:'https://httpbin.org/post',
        data: $multi->body,
        headers: [
            'Host: httpbin.org',
            'Origin: https://httpbin.org/',
            "Content-Type: multipart/form-data; boundary=$multi->boundary",
            'Expect:' // prevent 100-continue delay
        ],
    );
    var_dump($response->body);
} catch (Exception $e) {
    echo $e->getMessage();
}
