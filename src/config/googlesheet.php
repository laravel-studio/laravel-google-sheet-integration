<?php
return [
    'config' => [        
        'client_id'=>'<Google Client ID>',
        'project_id'=>'<Google Project ID>',
        'auth_uri'=>'https://accounts.google.com/o/oauth2/auth',
        'token_uri'=>'https://oauth2.googleapis.com/token',
        'auth_provider_x509_cert_url'=>'https://www.googleapis.com/oauth2/v1/certs',
        'client_secret'=>'<Google Client secret>',
        'redirect_uris'=>[env('APP_URL')."/userAuthenticated"],
        'javascript_origins'=>[env('APP_URL')]
    ],

    'project_number'=>'<Google Project number>',

    'return_url' => env('GOOGLE_SHEET_RETURN_URL', "#"),
    'auto_auth' => false
];