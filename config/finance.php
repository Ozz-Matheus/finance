<?php

return [
    // Configure a randomly generated, 32+ character secret in the server .env file.
    // Do not commit the token or share it in chat.
    'import_token' => env('FINANCE_IMPORT_API_TOKEN'),
];
