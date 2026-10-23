<?php

namespace Waf\Http_rules;

/** @param list<string> $ranges */
function in_cidr(string $ip, array $ranges): bool
{
    $address = inet_pton($ip);
    if ($address === false) {
        return false;
    }

    foreach ($ranges as $range) {
        $parts = explode('/', $range, 2);
        $network = inet_pton($parts[0]);
        if ($network === false || strlen($network) !== strlen($address)) {
            continue;
        }

        $max_bits = strlen($address) * 8;
        $prefix = $parts[1] ?? (string) $max_bits;
        if (!preg_match('/\A[0-9]{1,3}\z/', $prefix) || (int) $prefix > $max_bits) {
            continue;
        }

        $bits = (int) $prefix;
        $bytes = intdiv($bits, 8);
        if (substr($address, 0, $bytes) !== substr($network, 0, $bytes)) {
            continue;
        }

        $remaining = $bits % 8;
        if ($remaining === 0) {
            return true;
        }

        $mask = (0xff << (8 - $remaining)) & 0xff;
        if ((ord($address[$bytes]) & $mask) === (ord($network[$bytes]) & $mask)) {
            return true;
        }
    }

    return false;
}

function check(): int
{
    $ip       = $_SERVER['REMOTE_ADDR'] ?? '';
    $protocol = $_SERVER['SERVER_PROTOCOL'] ?? '';
    $method   = $_SERVER['REQUEST_METHOD'] ?? '';
    $uri      = $_SERVER['REQUEST_URI'] ?? '';
    $path     = explode('?', $uri, 2)[0];
    $query    = $_SERVER['QUERY_STRING'] ?? '';
    $ua       = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $referer  = $_SERVER['HTTP_REFERER'] ?? '';
    $accept   = $_SERVER['HTTP_ACCEPT'] ?? '';
    $header_list = array_map('strtolower', array_keys(getallheaders()));
    $is_rest  = preg_match('~^/wp-json(?:/|$)~', $path) === 1 || isset($_GET['rest_route']);

    // Add rule snippets here.

    return 200;
}

function run(): void
{
    if (PHP_SAPI === 'cli') {
        return;
    }

    $status = check();

    if ($status !== 200) {
        http_response_code($status);
        exit;
    }
}

run();
