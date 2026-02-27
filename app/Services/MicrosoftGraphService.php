<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Carbon\Carbon;

// Service class to interact with Microsoft Graph API for calendar events related to bookings

class MicrosoftGraphService
{
    private Client $httpClient;
    private string $token;
    private User $user;
    private const GRAPH_API_URL = 'https://graph.microsoft.com/v1.0';

    public function __construct(User $user)
    {
        if (!$user->microsoft_token) {
            throw new \Exception('User has no Microsoft Graph token. Please authenticate first.');
        }

        $this->user = $user;
        $this->token = $user->microsoft_token;
        $this->httpClient = new Client();
        
        // Refresh token if it's about to expire
        $this->ensureTokenIsValid();
    }

    /**
     * Ensure the token is valid and refresh if needed
     */
    private function ensureTokenIsValid(): void
    {
        if ($this->user->microsoft_token_expires_at && $this->user->microsoft_token_expires_at->isPast()) {
            try {
                $this->refreshToken();
            } catch (\Exception $e) {
                Log::error('Failed to refresh Microsoft token: ' . $e->getMessage());
                throw $e;
            }
        }
    }

    /**
     * Refresh the access token
     */
    private function refreshToken(): void
    {
        if (!$this->user->microsoft_refresh_token) {
            throw new \Exception('No refresh token available.');
        }

        $client = new Client();
        try {
            $response = $client->post(
                'https://login.microsoftonline.com/' . config('microsoft.microsoft.tenant_id') . '/oauth2/v2.0/token',
                [
                    'form_params' => [
                        'client_id' => config('microsoft.microsoft.client_id'),
                        'client_secret' => config('microsoft.microsoft.client_secret'),
                        'refresh_token' => $this->user->microsoft_refresh_token,
                        'grant_type' => 'refresh_token',
                        'scope' => 'Calendars.ReadWrite User.Read offline_access',
                    ],
                ]
            );

            $tokenData = json_decode($response->getBody(), true);

            if (isset($tokenData['access_token'])) {
                $this->user->microsoft_token = $tokenData['access_token'];
                $this->token = $tokenData['access_token'];
                if (isset($tokenData['refresh_token'])) {
                    $this->user->microsoft_refresh_token = $tokenData['refresh_token'];
                }
                $this->user->microsoft_token_expires_at = now()->addSeconds($tokenData['expires_in']);
                $this->user->save();

                Log::info('Microsoft token refreshed successfully for user: ' . $this->user->id);
            } else {
                throw new \Exception('Invalid token response from Microsoft.');
            }
        } catch (\Exception $e) {
            Log::error('Token refresh failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Create a calendar event for a booking
     */
    public function createBookingEvent(array $eventData): ?string
    {
        try {
            $response = $this->httpClient->post(
                self::GRAPH_API_URL . '/me/calendar/events',
                [
                    'headers' => $this->getHeaders(),
                    'json' => $eventData,
                ]
            );

            $responseData = json_decode($response->getBody(), true);
            return $responseData['id'] ?? null;
        } catch (\Exception $e) {
            Log::error('Failed to create calendar event: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update a calendar event
     */
    public function updateBookingEvent(string $eventId, array $eventData): bool
    {
        try {
            $this->httpClient->patch(
                self::GRAPH_API_URL . '/me/calendar/events/' . $eventId,
                [
                    'headers' => $this->getHeaders(),
                    'json' => $eventData,
                ]
            );
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update calendar event: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a calendar event
     */
    public function deleteBookingEvent(string $eventId): bool
    {
        try {
            $this->httpClient->delete(
                self::GRAPH_API_URL . '/me/calendar/events/' . $eventId,
                ['headers' => $this->getHeaders()]
            );
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete calendar event: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user's available time slots
     */
    public function getAvailability(\DateTime $start, \DateTime $end): array
    {
        try {
            $response = $this->httpClient->get(
                self::GRAPH_API_URL . '/me/calendarview',
                [
                    'headers' => $this->getHeaders(),
                    'query' => [
                        'startDateTime' => $start->format('c'),
                        'endDateTime' => $end->format('c'),
                    ],
                ]
            );

            $responseData = json_decode($response->getBody(), true);
            return $responseData['value'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch calendar availability: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Format booking data for calendar event
     */
    public static function formatBookingAsEvent($booking, $adviser): array
    {
        return [
            'subject' => "Booking: {$booking->student->name} with {$adviser->name}",
            'start' => [
                'dateTime' => $booking->start_time->format('c'),
                'timeZone' => 'Europe/London',
            ],
            'end' => [
                'dateTime' => $booking->end_time->format('c'),
                'timeZone' => 'Europe/London',
            ],
            'bodyPreview' => "Booking for {$booking->student->name}",
            'body' => [
                'contentType' => 'HTML',
                'content' => "<p>Student: {$booking->student->name}</p><p>Email: {$booking->student->email}</p>",
            ],
            'attendees' => [
                [
                    'emailAddress' => [
                        'address' => $booking->student->email,
                        'name' => $booking->student->name,
                    ],
                    'type' => 'required',
                ],
            ],
            'isReminderOn' => true,
            'reminderMinutesBeforeStart' => 15,
        ];
    }
}
