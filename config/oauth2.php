<?php

return [
    'client_id' => env('CLIENT_ID'),
    'client_secret' => env('CLIENT_SECRET'),
    'token_url' => env('APP_URL') . '/' .  env('TOKEN_ENDPOINT')
];
