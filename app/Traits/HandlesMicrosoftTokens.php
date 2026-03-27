<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

/**
 * Trait for handling Microsoft token refresh logic
 * Can be used in any class that needs to work with Microsoft Graph API
 */
trait HandlesMicrosoftTokens
{
    /**
     * Get the user instance for token operations
     * Can be overridden in classes using this trait
     */
    protected function getMicrosoftTokenUser($user = null)
    {
        if ($user) {
            return $user;
        }

        // Try to get authenticated user
        if (auth()->check()) {
            return auth()->user();
        }

        return null;
    }

    /**
     * Check if user's Microsoft token needs refresh and refresh if needed
     */
    protected function ensureMicrosoftTokenIsValid($user = null)
    {
        $user = $this->getMicrosoftTokenUser($user);

        if (!$user || !$user->microsoft_token) {
            return false;
        }

        // Check if token is expired or about to expire (within 5 minutes)
        if ($user->microsoft_token_expires_at && $user->microsoft_token_expires_at->subMinutes(5)->isPast()) {
            try {
                return $this->refreshMicrosoftToken($user);
            } catch (\Exception $e) {
                Log::error('Failed to refresh Microsoft token: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                ]);
                return false;
            }
        }

        return true;
    }

    /**
     * Refresh the Microsoft access token using the refresh token
     */
    protected function refreshMicrosoftToken($user): bool
    {
        if (!$user->microsoft_refresh_token) {
            Log::warning('No refresh token available for user: ' . $user->id);
            $user->microsoft_token = null;
            $user->save();
            return false;
        }

        try {
            $client = new Client();
            $response = $client->post(
                'https://login.microsoftonline.com/' . config('microsoft.microsoft.tenant_id') . '/oauth2/v2.0/token',
                [
                    'form_params' => [
                        'client_id' => config('microsoft.microsoft.client_id'),
                        'client_secret' => config('microsoft.microsoft.client_secret'),
                        'refresh_token' => $user->microsoft_refresh_token,
                        'grant_type' => 'refresh_token',
                        'scope' => 'Calendars.ReadWrite User.Read offline_access',
                    ],
                    'timeout' => 10,
                ]
            );

            $tokenData = json_decode($response->getBody(), true);

            if (!isset($tokenData['access_token'])) {
                throw new \Exception('No access token in response');
            }

            $user->microsoft_token = $tokenData['access_token'];
            if (isset($tokenData['refresh_token'])) {
                $user->microsoft_refresh_token = $tokenData['refresh_token'];
            }
            $user->microsoft_token_expires_at = now()->addSeconds($tokenData['expires_in'] ?? 3600);
            $user->save();

            Log::info('Microsoft token refreshed successfully', [
                'user_id' => $user->id,
                'expires_in' => $tokenData['expires_in'] ?? 'unknown',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Microsoft token refresh failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString(),
            ]);

            // Clear invalid tokens
            $user->microsoft_token = null;
            $user->microsoft_refresh_token = null;
            $user->microsoft_token_expires_at = null;
            $user->save();

            return false;
        }
    }
}
