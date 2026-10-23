<?php

// Reject POST requests from Baiduspider's renderer.
if (
    $method === 'POST'
    && strpos($ua, 'Baiduspider-render/') !== false
) {
    return 429;
}

// Block the Alibaba bot IP range: 47.79.0.0/16
if (
    in_cidr($ip, ['47.79.0.0/16'])
) {
    return 429;
}

// Block the YouBot IP range: 68.67.112.0/24
if (
    in_cidr($ip, ['68.67.112.0/24'])
) {
    return 429;
}
