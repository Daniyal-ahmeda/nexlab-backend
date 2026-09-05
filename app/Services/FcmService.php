<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    /**
     * Send a push notification to a user via Firebase FCM HTTP v1.
     *
     * @param  array<string, string>  $data
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        if (! $user->fcm_token) {
            Log::warning('FCM: user has no device token', ['user_id' => $user->id]);

            return;
        }

        $projectId = config('services.firebase.project_id');

        if (! $projectId) {
            Log::warning('FCM: FIREBASE_PROJECT_ID not configured');

            return;
        }

        try {
            $accessToken = $this->getAccessToken();

            Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                    'message' => [
                        'token' => $user->fcm_token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => array_map('strval', $data),
                        'android' => [
                            'priority' => 'high',
                            'notification' => [
                                'channel_id' => 'nexlab_results',
                                'sound' => 'default',
                            ],
                        ],
                        'apns' => [
                            'payload' => [
                                'aps' => [
                                    'sound' => 'default',
                                    'badge' => 1,
                                ],
                            ],
                        ],
                    ],
                ])
                ->throw();
        } catch (\Throwable $e) {
            Log::error('FCM: failed to send notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Exchange the service account JSON for a short-lived OAuth2 bearer token.
     */
    private function getAccessToken(): string
    {
        $credentialsPath = config('services.firebase.credentials');

        if (! $credentialsPath || ! file_exists(base_path($credentialsPath))) {
            throw new \RuntimeException('Firebase service account credentials file not found at: '.$credentialsPath);
        }

        /** @var array{client_email: string, private_key: string, token_uri: string} $credentials */
        $credentials = json_decode(file_get_contents(base_path($credentialsPath)), true);

        $now = time();
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claim = base64_encode(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => $credentials['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $signingInput = "{$header}.{$claim}";
        openssl_sign($signingInput, $signature, $credentials['private_key'], 'SHA256');
        $jwt = $signingInput.'.'.base64_encode($signature);

        $response = Http::asForm()->post($credentials['token_uri'], [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ])->throw()->json();

        return $response['access_token'];
    }
}
