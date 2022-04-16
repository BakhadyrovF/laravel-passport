<?php

namespace App\Contracts;

interface TokenCache
{
    /**
     * Store tokens to http only cookie
     * @param object $tokens
     * @return bool
     */
    public function storeTokens(object $tokens);
}
