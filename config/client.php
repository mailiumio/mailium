<?php

return [

    'url' => env('CLIENT_URL'),

    'auth' => [
        'redirect_url' => env('CLIENT_URL') . 'auth/callback',
    ],

];