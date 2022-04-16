<?php

namespace App\Services;

use App\Contracts\TokenCache;
use App\Contracts\TokenGenerator;
use App\Http\Requests\SignInFormRequest;
use App\Http\Requests\SignUpFormRequest;
use App\Models\Client as ModelsClient;
use App\Models\User;
use DateInterval;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;

class AuthService implements TokenGenerator, TokenCache
{
    const STATUS_OK = 200;
    const TYPE_PASSWORD = 'password';
    const TYPE_REFRESH_TOKEN = 'refresh_token';

    protected array $clientCredentials = [];

    public function __construct()
    {
        $this->clientCredentials = [
            'client_id' => config('oauth2.client_id'),
            'client_secret' => config('oauth2.client_secret'),
            'scope' => '*'
        ];
    }

    public function generateTokens(array $data, string $grantType = self::TYPE_PASSWORD)
    {
        $data['grant_type'] = $grantType;
        if ($grantType === self::TYPE_PASSWORD) {
            $data = array_merge($this->clientCredentials, $data);
        } else {
            $data = array_merge($this->clientCredentials, $data);
        }

        $request = Request::create('oauth/token', 'POST', $data);

        $response = app()->handle($request);

        if ($response->status() !== self::STATUS_OK) {
            return false;
        }

        return (object)json_decode($response->content());
    }

    public function storeTokens(object $tokens)
    {
        setcookie('access_token', $tokens->access_token, addSeconds(Passport::$tokensExpireIn->s), '/api', '', false, true);
        setcookie('refresh_token', $tokens->refresh_token, time() + 7776000, '/api', '', false, true);
    }

    protected function removeTokens()
    {
        setcookie('refresh_token', null, time(), '/api');
        setcookie('access_token', null, time(), '/api');
    }

    public function signUp(SignUpFormRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);

        User::query()
            ->create($data);

        if (is_bool($oauthTokens = $this->generateTokens([
            'email' => $data['email'],
            'password' => $request->password
        ]))) {
            return response()->json([
                'message' => 'Invalid credentials',
                'success' => 0
            ], 401);
        }
        $this->storeTokens($oauthTokens);

        return response()->json([
            'message' => 'Signed Up',
            'success' => 1
        ], 201);
    }

    public function signIn(SignInFormRequest $request)
    {
        $data = $request->validated();
        $user = User::query()
            ->where('email', '=', $data['email'])
            ->first();

        if (!password_verify($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
                'success' => 0
            ], 401);
        }

        if (is_bool($oauthTokens = $this->generateTokens([
            'username' => $data['email'],
            'password' => $data['password']
        ]))) {
            return response()->json([
                'message' => 'Invalid credentials',
                'success' => 0
            ], 401);
        }
        $this->storeTokens($oauthTokens);

        return response()->json([
            'message' => 'Signed In',
            'success' => 1
        ]);
    }

    public function logout()
    {
        /** @var \App\Models\User $user */
        $user = auth('api')->user();
        $accessTokenId = $user->tokens()->first()->id;

        app(TokenRepository::class)->revokeAccessToken($accessTokenId);
        app(RefreshTokenRepository::class)->revokeRefreshTokensByAccessTokenId($accessTokenId);
        $this->removeTokens();

        return response()->json([
            'message' => 'Logged Out',
            'success' => 1
        ]);
    }

    public function refreshTokens(Request $request)
    {
        $refreshToken = $request->header('refresh_token') ?? null;
        $oauthTokens = $this->generateTokens([
            'refresh_token' => $refreshToken
        ], self::TYPE_REFRESH_TOKEN);

        if (is_bool($oauthTokens)) {
            setcookie('refresh_token', null, time(), '/api');
            throw new AuthorizationException('Unauthenticated', 401);
        }

        $this->storeTokens($oauthTokens);
        app(ModelsClient::class)->setHeaders($request, $oauthTokens);
        return true;
    }
}
