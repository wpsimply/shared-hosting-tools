<?php

// Scrapers: product filter and a Macintosh Chrome UA
if (
    str_contains($query, 'filter_')
    && preg_match('~^Mozilla/.*Macintosh.*Chrome~', $ua) === 1
    && in_array($header_list, [
        'host,sec-ch-ua,sec-ch-ua-mobile,sec-ch-ua-platform,upgrade-insecure-requests,user-agent,accept,sec-fetch-site,sec-fetch-mode,sec-fetch-user,sec-fetch-dest,accept-encoding,accept-language,x-https',
        'host,connection,pragma,cache-control,accept,upgrade-insecure-requests,user-agent,sec-ch-ua,sec-ch-ua-mobile,sec-ch-ua-platform,sec-fetch-site,sec-fetch-mode,sec-fetch-user,sec-fetch-dest,accept-encoding,x-https',
    ], true)
) {
    return 429;
}

// Scrapers: Macintosh UA and MacOSX client hint
if (
    preg_match('~^Mozilla/.*Macintosh.*Intel Mac OS X 10_15_7~', $ua) === 1
    && ($_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? '') === 'Mac OS X'
    && in_array($header_list, [
        'host,pragma,cache-control,accept,upgrade-insecure-requests,user-agent,sec-ch-ua,sec-ch-ua-mobile,sec-ch-ua-platform,sec-fetch-site,sec-fetch-mode,sec-fetch-user,sec-fetch-dest,accept-encoding,priority,x-https',
        'host,content-length,pragma,cache-control,accept,content-type,origin,user-agent,x-requested-with,sec-ch-ua,sec-ch-ua-mobile,sec-ch-ua-platform,sec-fetch-site,sec-fetch-mode,sec-fetch-dest,referer,accept-encoding,priority,cookie,x-https',
    ], true)
) {
    return 429;
}

// Scrapers: product filter and HTTP/1.1 requests with a Windows UA
if (
    $protocol === 'HTTP/1.1'
    && strpos($ua, 'Windows') !== false
    && strpos($query, 'yith_wcan=1') !== false
) {
    return 429;
}

// Linux in the platform client hint and Macintosh in the UA
if (
    strpos($_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? '', 'Linux') !== false
    && strpos($ua, 'Macintosh') !== false
) {
    return 403;
}

// IPXO requests to REST routes with per_page, or POST requests with wc-ajax
// Ranges: 151.245.204.0/22 151.245.208.0/22
if (
    in_cidr($ip, ['151.245.204.0/22', '151.245.208.0/22'])
    && (
        ($is_rest && isset($_GET['per_page']))
        || ($method === 'POST' && isset($_GET['wc-ajax']))
    )
) {
    return 403;
}
