<?php

$configuredIceServers = env('WFH_MONITORING_ICE_SERVERS');
$configuredIceServers = $configuredIceServers ? json_decode($configuredIceServers, true) : null;

return [
    'api_key' => env('WFH_MONITORING_API_KEY'),
    'ice_servers' => is_array($configuredIceServers)
        ? $configuredIceServers
        : [
            ['urls' => 'stun:stun.l.google.com:19302'],
            [
                'urls' => [
                    'turn:openrelay.metered.ca:80',
                    'turn:openrelay.metered.ca:443',
                    'turn:openrelay.metered.ca:443?transport=tcp',
                ],
                'username' => 'openrelayproject',
                'credential' => 'openrelayproject',
            ],
        ],
];
