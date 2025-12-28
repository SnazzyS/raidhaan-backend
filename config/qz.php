<?php

return [
    'certificate_path' => env('QZ_CERT_PATH', storage_path('app/qz/qz-tray.pem')),
    'private_key_path' => env('QZ_PRIVATE_KEY_PATH', storage_path('app/qz/qz-tray.key')),
    'private_key_passphrase' => env('QZ_PRIVATE_KEY_PASSPHRASE'),
];
