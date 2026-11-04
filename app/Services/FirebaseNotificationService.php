<?php

namespace App\Services;

use App\Models\FcmToken;
use App\User;
use Illuminate\Support\Facades\Log;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseNotificationService
{
    public function sendToUser(User $user, string $title, string $body, array $data = [])
    {
        $tokens = $user->activeFcmTokens()->pluck('fcm_token')->toArray();

        if (empty($tokens)) {
            Log::warning('No active FCM tokens found for user', ['user_id' => $user->id]);
            return false;
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    public function sendToAnonymousUsers(array $deviceIds, string $title, string $body, array $data = [])
    {
        $tokens = FcmToken::whereNull('user_id')
            ->whereIn('device_id', $deviceIds)
            ->where('is_active', true)
            ->pluck('fcm_token')
            ->toArray();

        if (empty($tokens)) {
            Log::warning('No active FCM tokens found for anonymous users', ['device_ids' => $deviceIds]);
            return false;
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    public function sendToAllActiveTokens(string $title, string $body, array $data = [])
    {
        $tokens = FcmToken::where('is_active', true)
            ->pluck('fcm_token')
            ->toArray();

        if (empty($tokens)) {
            Log::warning('No active FCM tokens found');
            return false;
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    public function sendToToken(string $token, string $title, string $body, array $data = [])
    {
        return $this->sendToTokens([$token], $title, $body, $data);
    }

    protected function sendToTokens(array $tokens, string $title, string $body, array $data = [])
    {
        if (empty($tokens)) {
            return false;
        }

        try {
            $messaging = Firebase::messaging();
            $notification = Notification::create($title, $body);

            $successCount = 0;
            $failedTokens = [];

            foreach ($tokens as $token) {
                try {
                    $message = CloudMessage::fromArray([
                        'notification' => $notification,
                        'data' => $data,
                        'android' => [
                            'priority' => 'high',
                            'notification' => [
                                'title' => $title,
                                'body' => $body,
                                'sound' => 'default',
                                'channel_id' => 'default'
                            ]
                        ],
                        'apns' => [
                            'headers' => [
                                'apns-priority' => '10'
                            ],
                            'payload' => [
                                'aps' => [
                                    'alert' => [
                                        'title' => $title,
                                        'body' => $body
                                    ],
                                    'sound' => 'default',
                                    'badge' => 1,
                                    'content-available' => 1
                                ]
                            ]
                        ],
                        'target' => 'token',
                        'token' => $token
                    ]);

                    $messaging->send($message);
                    $successCount++;
                } catch (\Exception $e) {
                    Log::warning('Failed to send to token', [
                        'token' => substr($token, 0, 20) . '...',
                        'error' => $e->getMessage()
                    ]);

                    if (
                        strpos($e->getMessage(), 'invalid-registration-token') !== false ||
                        strpos($e->getMessage(), 'registration-token-not-registered') !== false
                    ) {
                        $failedTokens[] = $token;
                    }
                }
            }

            if (!empty($failedTokens)) {
                $this->deactivateTokens($failedTokens);
            }

            Log::info('Notification sent', [
                'success_count' => $successCount,
                'failure_count' => count($failedTokens),
                'total' => count($tokens)
            ]);

            return $successCount > 0;
        } catch (\Exception $e) {
            Log::error('Failed to send notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    protected function deactivateTokens(array $tokens)
    {
        FcmToken::whereIn('fcm_token', $tokens)->update(['is_active' => false]);
    }

    protected function convertDataToStrings(array $data): array
    {
        $stringData = [];
        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $stringData[$key] = json_encode($value);
            } elseif (is_bool($value)) {
                $stringData[$key] = $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $stringData[$key] = '';
            } else {
                $stringData[$key] = (string) $value;
            }
        }
        return $stringData;
    }

    public function sendToTopic(string $topic, string $title, string $body, array $data = [])
    {
        try {
            $messaging = Firebase::messaging();

            $notification = \Kreait\Firebase\Messaging\Notification::create($title, $body);
            $stringData = $this->convertDataToStrings($data);

            $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('topic', $topic)
                ->withNotification($notification)
                ->withData($stringData)
                ->withAndroidConfig([
                    'priority' => 'high',
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'sound' => 'default',
                        'channel_id' => 'default'
                    ]
                ])
                ->withApnsConfig([
                    'headers' => [
                        'apns-priority' => '10'
                    ],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => $title,
                                'body' => $body
                            ],
                            'sound' => 'default',
                            'badge' => 1,
                            'content-available' => 1
                        ]
                    ]
                ]);

            $messaging->send($message);

            Log::info('Topic notification sent', ['topic' => $topic]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send topic notification', [
                'topic' => $topic,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
