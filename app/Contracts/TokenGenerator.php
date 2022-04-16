<?php

namespace App\Contracts;

interface TokenGenerator
{
    /**
     * Sends request to ouath2 server and returns access-refresh-tokens
     * @param array $data
     * @return array|object|bool
     */
    public function generateTokens(array $data, string $grantType = 'password');
}
