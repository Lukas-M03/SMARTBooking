<?php
//copilot generated configuration file for Microsoft Graph API integration, storing client ID, secret, and tenant ID from environment variables
return [
    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'tenant_id' => env('MICROSOFT_TENANT_ID'),
    ],
];
