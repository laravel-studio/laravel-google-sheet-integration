<?php
return [
    'config' => [        
        'client_id'=>'526569989960-tqktuj966nt2eld1mubm0vqatji4ln3q.apps.googleusercontent.com',
        'project_id'=>'polished-core-259509',
        'auth_uri'=>'https://accounts.google.com/o/oauth2/auth',
        'token_uri'=>'https://oauth2.googleapis.com/token',
        'auth_provider_x509_cert_url'=>'https://www.googleapis.com/oauth2/v1/certs',
        'client_secret'=>'UegYPuKV9nkDfM4H-vpe-K35',
        'redirect_uris'=>["http://localhost:8000/userAuthenticated"],
        'javascript_origins'=>["http://localhost:8000"]
    ],

    'project_number'=>'526569989960',

    'return_url' => env('GOOGLE_SHEET_RETURN_URL', "#"),
    'auto_auth' => false
];