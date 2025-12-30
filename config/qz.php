<?php

return [
    'certificate_base64' => env('QZ_CERT_BASE64'),
    'certificate_raw' => env('QZ_CERT_RAW'),
    'certificate_path' => env('QZ_CERT_PATH', storage_path('app/qz/qz-tray.pem')),
    'private_key_base64' => env('QZ_PRIVATE_KEY_BASE64'),
    'private_key_raw' => env('QZ_PRIVATE_KEY_RAW'),
    'private_key_path' => env('QZ_PRIVATE_KEY_PATH', storage_path('app/qz/qz-tray.key')),
    'private_key_passphrase' => env('QZ_PRIVATE_KEY_PASSPHRASE'),
];


