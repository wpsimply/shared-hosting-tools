<?php

declare(strict_types=1);

const DOCROOT = '/home/user';
const BASE_URL = 'https://example.com';

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
if ($argc < 2) {
    fwrite(STDERR, "Usage: php opcache-invalidate.php file.php [file.php ...]\n");
    exit(1);
}
$files = [];
foreach (array_slice($argv, 1) as $file) {
    $path = realpath($file);
    if ($path === false || !is_file($path)) {
        fwrite(STDERR, "File not found: {$file}\n");
        exit(1);
    }
    $files[] = $path;
}
$token = bin2hex(random_bytes(32));
$name = 'opcache-'.bin2hex(random_bytes(16)).'.php';
$endpoint = rtrim(DOCROOT, '/').'/'.$name;
$source = '<?php $token = '.var_export($token, true).'; $files = '.var_export(array_unique($files), true).';'. <<<'PHP'

header('Cache-Control: no-store');
if (!is_string($_POST['token'] ?? null) || !hash_equals($token, $_POST['token'])) {
    http_response_code(404);
    exit;
}
header('Content-Type: application/json');
if (!function_exists('opcache_invalidate') || !ini_get('opcache.enable')) {
    http_response_code(503);
    exit(json_encode(['ok' => false, 'error' => 'OPcache unavailable']));
}
$results = [];
foreach ($files as $file) {
    $cached = opcache_is_script_cached($file);
    $results[$file] = opcache_invalidate($file, true) || !$cached;
}
echo json_encode(['ok' => !in_array(false, $results, true), 'files' => $results]);
PHP;
$exitCode = 1;
try {
    if (file_put_contents($endpoint, $source) !== strlen($source)) {
        throw new RuntimeException('Cannot write web endpoint.');
    }
    $context = stream_context_create(['http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\nUser-Agent: OpcacheInvalidator/1.0\r\n",
        'content' => http_build_query(['token' => $token]),
        'timeout' => 30, 'follow_location' => 0, 'ignore_errors' => true,
    ]]);
    $url = rtrim(BASE_URL, '/').'/'.$name;
    $response = file_get_contents($url, false, $context);
    if ($response === false) {
        throw new RuntimeException('HTTP request failed.');
    }
    $status = 'unknown HTTP status';
    $contentType = 'unknown';
    foreach ($http_response_header ?? [] as $header) {
        if (str_starts_with($header, 'HTTP/')) {
            $status = $header;
        } elseif (stripos($header, 'Content-Type:') === 0) {
            $contentType = trim(substr($header, strlen('Content-Type:')));
        }
    }
    try {
        $result = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $error) {
        throw new RuntimeException(
            "Invalid JSON response from {$url}: {$status}; Content-Type: {$contentType}; "
            .strlen($response)." bytes; JSON error: {$error->getMessage()}",
            0,
            $error,
        );
    }
    if (!is_array($result) || !is_bool($result['ok'] ?? null)) {
        throw new RuntimeException("Unexpected response from {$url}: {$status}; missing boolean 'ok'.");
    }
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
    $exitCode = ($result['ok'] ?? false) === true ? 0 : 1;
} catch (Throwable $error) {
    fwrite(STDERR, $error->getMessage().PHP_EOL);
} finally {
    if (is_file($endpoint) && !unlink($endpoint)) {
        fwrite(STDERR, "Cannot delete endpoint: {$endpoint}\n");
        $exitCode = 1;
    }
}
exit($exitCode);
