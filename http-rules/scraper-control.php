<?php

// Match botnet traffic: HTTP/1.1, Windows UA and YITH product filter.
if (
    $protocol === 'HTTP/1.1'
    && strpos($ua, 'Windows') !== false
    && strpos($query, 'yith_wcan=1') !== false
) {
    return 429;
}

// Reject conflicting OS claims: Linux client hint with a macOS UA.
if (
    strpos($_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? '', 'Linux') !== false
    && strpos($ua, 'Macintosh') !== false
) {
    return 403;
}

// Block REST pagination scraping from IPXO proxy ranges.
// Ranges: 151.245.204.0/22 151.245.208.0/22
if (
    in_cidr($ip, ['151.245.204.0/22', '151.245.208.0/22'])
    && $is_rest
    && isset($_GET['per_page'])
) {
    return 403;
}
