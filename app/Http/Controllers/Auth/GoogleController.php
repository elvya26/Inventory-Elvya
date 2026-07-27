<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GoogleController extends Controller
{
    private function googleClientId(): ?string
    {
        return env('GOOGLE_CLIENT_ID');
    }

    private function googleClientSecret(): ?string
    {
        return env('GOOGLE_CLIENT_SECRET');
    }

    private function googleRedirectUri(): ?string
    {
        if ($envUri = env('GOOGLE_REDIRECT_URI')) {
            return $envUri;
        }

        return route('login.google.callback');
    }

    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('user_id')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $clientId = $this->googleClientId();
        $redirectUri = $this->googleRedirectUri();
        if (! $clientId || ! $redirectUri) {
            abort(500, 'Google OAuth config is missing (GOOGLE_CLIENT_ID / GOOGLE_REDIRECT_URI).');
        }

        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);
        $request->session()->put('login_origin_path', $request->is('admin') || $request->is('admin/*') ? 'admin' : 'store');
        $request->session()->save();

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'prompt' => 'select_account',
            'state' => $state,
        ]);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    public function handleCallback(Request $request): RedirectResponse
    {
        $clientSecret = $this->googleClientSecret();
        $redirectUri = $this->googleRedirectUri();
        $clientId = $this->googleClientId();

        if (! $clientId || ! $clientSecret || ! $redirectUri) {
            abort(500, 'Google OAuth config is missing (GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET / GOOGLE_REDIRECT_URI).');
        }

        $code = $request->query('code');
        $state = $request->query('state');
        $expectedState = $request->session()->pull('google_oauth_state');

        $isValidState = $state && $expectedState && hash_equals($expectedState, $state);

        if (!$code) {
            abort(401, 'Missing OAuth code.');
        }

        if (!$isValidState && !app()->environment('local')) {
            abort(401, 'Invalid OAuth state. Silakan coba login ulang.');
        }

        $tokenResponse = $this->requestToken($clientId, $clientSecret, $redirectUri, $code);
        $accessToken = $tokenResponse['access_token'] ?? null;
        if (! $accessToken) {
            $detail = $tokenResponse['error_description'] ?? $tokenResponse['error'] ?? 'unknown error';
            abort(401, 'Google token exchange failed: '.$detail);
        }

        $profile = $this->fetchGoogleProfile($accessToken);
        $googleId = $profile['sub'] ?? $profile['id'] ?? null;
        $email = $profile['email'] ?? null;
        $name = $profile['name'] ?? null;
        $avatar = $profile['picture'] ?? null;

        if (! $googleId) {
            abort(401, 'Google profile is missing id.');
        }

        $userQuery = User::query();

        // Prioritas lookup: google_id kalau kolom ada, fallback email.
        if ($this->columnExists('google_id')) {
            $userQuery->where('google_id', $googleId);
        } elseif (! empty($email)) {
            $userQuery->where('email', $email);
        }

        $user = $userQuery->first();

        $payload = $this->buildUserPayload($googleId, $name, $email, $avatar);

        if (! $user) {
            $user = User::query()->create($payload);
        } else {
            $user->fill($payload);
            $user->save();
        }

        $request->session()->put('user_id', $user->id);
        $request->session()->put('user_name', $user->name ?? $user->email ?? 'User');

        $originPath = $request->session()->pull('login_origin_path', 'store');

        if ($originPath === 'admin') {
            return redirect()->route('dashboard');
        }

        return redirect()->route('ecommerce.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        $isStorefront = $request->is('licitastore/*') || $request->routeIs('ecommerce.logout');

        $request->session()->forget(['user_id', 'user_name']);

        if ($isStorefront) {
            return redirect()->route('ecommerce.login')->with('status', 'Anda berhasil keluar.');
        }

        return redirect()->route('login')->with('status', 'Anda berhasil keluar.');
    }

    private function requestToken(string $clientId, string $clientSecret, string $redirectUri, string $code): array
    {
        // prefer curl when available
        if (function_exists('curl_version')) {
            return $this->requestTokenCurl($clientId, $clientSecret, $redirectUri, $code);
        }

        return $this->requestTokenHttp($clientId, $clientSecret, $redirectUri, $code);
    }

    private function requestTokenHttp(string $clientId, string $clientSecret, string $redirectUri, string $code): array
    {
        $body = http_build_query([
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/x-www-form-urlencoded',
                ],
                'content' => $body,
                'timeout' => 10,
            ],
        ]);

        $raw = file_get_contents('https://oauth2.googleapis.com/token', false, $context);
        if (! $raw) {
            return [];
        }

        $json = json_decode($raw, true);
        return is_array($json) ? $json : [];
    }

    private function requestTokenCurl(string $clientId, string $clientSecret, string $redirectUri, string $code): array
    {
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $raw = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($raw ?: '', true);
        return is_array($json) ? $json : [];
    }

    private function fetchGoogleProfile(string $accessToken): array
    {
        if (function_exists('curl_version')) {
            return $this->fetchGoogleProfileCurl($accessToken);
        }

        return $this->fetchGoogleProfileHttp($accessToken);
    }

    private function fetchGoogleProfileCurl(string $accessToken): array
    {
        $ch = curl_init('https://www.googleapis.com/oauth2/v3/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$accessToken]);

        $raw = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($raw ?: '', true);

        return is_array($json) ? $json : [];
    }

    private function fetchGoogleProfileHttp(string $accessToken): array
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'Authorization: Bearer '.$accessToken,
                'timeout' => 10,
            ],
        ]);

        $raw = file_get_contents('https://www.googleapis.com/oauth2/v3/userinfo', false, $context);
        if (! $raw) {
            return [];
        }

        $json = json_decode($raw, true);

        return is_array($json) ? $json : [];
    }

    private function buildUserPayload(string $googleId, ?string $name, ?string $email, ?string $avatar): array
    {
        $payload = [];

        if ($this->columnExists('google_id') && ! empty($googleId)) {
            $payload['google_id'] = $googleId;
        }

        if ($this->columnExists('name') && ! empty($name)) {
            $payload['name'] = $name;
        }

        if ($this->columnExists('email') && ! empty($email)) {
            $payload['email'] = $email;
        }

        if ($this->columnExists('avatar') && ! empty($avatar)) {
            $payload['avatar'] = $avatar;
        }

        return $payload;
    }

    private function columnExists(string $column): bool
    {
        try {
            return Schema::hasColumn('users', $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}

