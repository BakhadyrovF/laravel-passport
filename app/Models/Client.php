<?php

namespace App\Models;

use DateInterval;
use Illuminate\Http\Request;
use Laravel\Passport\Client as PassportClient;

class Client extends PassportClient
{
    public function setHeaders(Request $request, object $tokens = null): void
    {
        if (is_null($tokens)) {
            $request->headers->set('Authorization', 'Bearer ' . $request->cookie('access_token'));
            $request->headers->set('refresh_token', $request->cookie('refresh_token') ?? '');
            return;
        }

        $request->headers->set('Authorization', 'Bearer ' . $tokens->access_token);
        $request->headers->set('refresh_token', $tokens->refresh_token);
        return;
    }
}
