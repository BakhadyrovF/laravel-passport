<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignInFormRequest;
use App\Http\Requests\SignUpFormRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $service;

    public function __construct()
    {
        $this->service = new AuthService();
    }

    public function signUp(SignUpFormRequest $request)
    {
        return $this->service->signUp($request);
    }

    public function signIn(SignInFormRequest $request)
    {
        // dd($request->headers);
        return $this->service->signIn($request);
    }

    public function logout()
    {
        return $this->service->logout();
    }

    public function userData(Request $request)
    {
        return auth('api')->user();
    }
}
