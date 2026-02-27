<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use App\Traits\HandlesMicrosoftTokens;

// Copilot generated controller to handle Microsoft OAuth authentication and token management for calendar integration
class MicrosoftAuthController extends Controller
{
    use HandlesMicrosoftTokens;

    private Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    /**
     * Redirect user to Microsoft login
     */
    public function redirectToMicrosoft()
    {
        $clientId = config('microsoft.microsoft.client_id');
        $tenantId = config('microsoft.microsoft.tenant_id');
        $redirectUri = route('microsoft.callback');
        $scopes = ['Calendars.ReadWrite', 'User.Read', 'offline_access'];
        
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', $scopes),
            'response_mode' => 'query',
            'state' => Str::random(40),
        ];

        session(['microsoft_oauth_state' => $params['state']]);

        $url = 'https://login.microsoftonline.com/' . $tenantId 
            . '/oauth2/v2.0/authorize?' . http_build_query($params);

        return redirect($url);
    }

    /**
     * Handle Microsoft callback
     */
    public function handleCallback(Request $request)
    {
        // Verify state
        if ($request->state !== session('microsoft_oauth_state')) {
            return redirect('/login')->with('error', 'Invalid state parameter.');
        }

        if ($request->has('error')) {
            return redirect('/login')->with('error', 'Authorization failed: ' . $request->error);
        }

        if (!$request->has('code')) {
            return redirect('/login')->with('error', 'No authorization code received.');
        }

        try {
            // Exchange code for token
            $token = $this->exchangeCodeForToken($request->code);

            // Get user info
            $userInfo = $this->getUserInfo($token['access_token']);

            // Update or create user
            $user = Auth::user();
            if ($user) {
                $user->microsoft_token = $token['access_token'];
                $user->microsoft_refresh_token = $token['refresh_token'] ?? null;
                $user->microsoft_token_expires_at = now()->addSeconds($token['expires_in']);
                $user->save();

                return redirect('/dashboard')->with('success', 'Microsoft calendar connected successfully!');
            }

            return redirect('/login')->with('error', 'User not found.');
        } catch (\Exception $e) {
            Log::error('Microsoft OAuth error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }

    /**
     * Exchange authorization code for access token
     */
    private function exchangeCodeForToken(string $code): array
    {
        try {
            $response = $this->httpClient->post(
                'https://login.microsoftonline.com/' . config('microsoft.microsoft.tenant_id') . '/oauth2/v2.0/token',
                [
                    'form_params' => [
                        'client_id' => config('microsoft.microsoft.client_id'),
                        'client_secret' => config('microsoft.microsoft.client_secret'),
                        'code' => $code,
                        'redirect_uri' => route('microsoft.callback'),
                        'grant_type' => 'authorization_code',
                        'scope' => 'Calendars.ReadWrite User.Read offline_access',
                    ],
                ]
            );

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Failed to exchange code for token: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Refresh an expired access token using refresh token
     */
    public function refreshToken(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        if (!$user->microsoft_token) {
            return response()->json(['error' => 'Not connected to Outlook'], 400);
        }

        try {
            $refreshed = $this->refreshMicrosoftToken($user);

            if ($refreshed) {
                return response()->json([
                    'success' => true,
                    'message' => 'Token refreshed successfully',
                    'expires_at' => $user->microsoft_token_expires_at,
                ]);
            }

            return response()->json(['error' => 'Failed to refresh token'], 500);
        } catch (\Exception $e) {
            Log::error('Token refresh request failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to refresh token'], 500);
        }
    }

    /**
     * Get user info from Microsoft Graph
     */
    private function getUserInfo(string $accessToken): array
    {
        $response = $this->httpClient->get(
            'https://graph.microsoft.com/v1.0/me',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * Disconnect Microsoft calendar
     */
    public function disconnect(Request $request)
    {
        $user = Auth::user();
        $user->microsoft_token = null;
        $user->microsoft_refresh_token = null;
        $user->microsoft_token_expires_at = null;
        $user->save();

        return redirect()->back()->with('success', 'Microsoft calendar disconnected.');
    }
}
