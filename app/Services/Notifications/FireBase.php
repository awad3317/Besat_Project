<?php

namespace App\Services\Notifications;

use Exception;
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

class FireBase
{
    public static function send($heading, $message, $deviceIds, $data = [])
    {
        $deviceIds = array_values(array_filter($deviceIds));
        if (empty($deviceIds)) {
            throw new Exception('No device IDs provided');
        }

        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new ServiceAccountCredentials($scopes, config('services.firebase.credentials'));
        $accessToken = $credentials->fetchAuthToken()['access_token'];
        $projectId = config('services.firebase.project_id');

        $messagePayload = [
            'notification' => [
                'title' => $heading,
                'body' => $message,
            ],
            // أضف webpush لتعمل مع متصفحات الويب
            'webpush' => [
                'notification' => [
                    'title' => $heading,
                    'body' => $message,
                    'icon' => url('/logo.png'),
                    'badge' => url('/logo.png'),
                    'actions' => [
                        [
                            'action' => 'open',
                            'title' => 'فتح'
                        ]
                    ]
                ],
                'fcm_options' => [
                    'link' => url('/dashboard')
                ]
            ],
            'android' => [
                'priority' => 'high',
                'notification' => [
                    'sound' => 'default',
                ],
            ],
            'apns' => [
                'payload' => [
                    'aps' => [
                        'sound' => 'default',
                    ],
                ],
            ],
        ];

        if (!empty($data)) {
            $messagePayload['data'] = $data;
        }

        // إرسال لكل device
        $responses = [];
        foreach ($deviceIds as $deviceToken) {
            $messagePayload['token'] = $deviceToken;
            $payload = ['message' => $messagePayload];
            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $client = new Client();
            
            try {
                $response = $client->request('POST', $url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $payload,
                    'timeout' => 10
                ]);
                
                $responses[$deviceToken] = json_decode($response->getBody(), true);
                
            } catch (\Exception $e) {
                \Log::error('Firebase sending error for token: ' . $deviceToken, [
                    'error' => $e->getMessage()
                ]);
                $responses[$deviceToken] = ['error' => $e->getMessage()];
            }
        }

        return $responses;
    }
}