<?php

namespace App\Services\Firebase;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Minimal Firebase Cloud Messaging HTTP v1 client.
 *
 * Mints an OAuth2 access token from a service-account key (RS256 JWT signed
 * with openssl — no external dependency) and sends a single-device message.
 * The token is cached until shortly before it expires.
 *
 * Fails safe: if Firebase is not configured, or anything goes wrong, send()
 * returns false and logs — it never throws into the calling (order) flow.
 */
class FcmV1Client
{
    private const TOKEN_URL   = 'https://oauth2.googleapis.com/token';
    private const SCOPE       = 'https://www.googleapis.com/auth/firebase.messaging';
    private const CACHE_KEY   = 'fcm.v1.access_token';

    public function isConfigured(): bool
    {
        $projectId   = (string) config('firebase.project_id');
        $credentials = (string) config('firebase.credentials');

        return $projectId !== '' && $credentials !== '' && is_file($credentials);
    }

    /**
     * Send a notification (title/body) + data payload to one device token.
     *
     * @param array<string,string> $data  Extra key/value data (values stringified).
     */
    public function send(string $deviceToken, string $title, string $body, array $data = []): bool
    {
        if (! $this->isConfigured()) {
            Log::info('FCM push skipped: Firebase not configured.');
            return false;
        }
        if ($deviceToken === '') {
            return false;
        }

        try {
            $sa = $this->serviceAccount();
            $token = $this->accessToken($sa);
            if (! $token) {
                return false;
            }

            $projectId = (string) config('firebase.project_id');
            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            // FCM v1 requires all data values to be strings.
            $stringData = [];
            foreach ($data as $k => $v) {
                $stringData[(string) $k] = (string) $v;
            }

            $message = [
                'message' => [
                    'token' => $deviceToken,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => $stringData,
                    'android' => [
                        'notification' => ['sound' => 'default', 'click_action' => 'FLUTTER_NOTIFICATION_CLICK'],
                    ],
                    'apns' => [
                        'payload' => ['aps' => ['sound' => 'default']],
                    ],
                ],
            ];

            [$status, $response] = $this->httpPost($url, json_encode($message), [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ]);

            if ($status >= 200 && $status < 300) {
                return true;
            }

            Log::warning("FCM v1 send failed (HTTP {$status}): {$response}");
            return false;
        } catch (\Throwable $e) {
            Log::warning('FCM v1 send error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * @return array{client_email:string,private_key:string,token_uri:string}
     */
    private function serviceAccount(): array
    {
        $json = json_decode((string) file_get_contents((string) config('firebase.credentials')), true);

        if (! is_array($json) || empty($json['client_email']) || empty($json['private_key'])) {
            throw new \RuntimeException('Invalid Firebase service-account JSON.');
        }

        return [
            'client_email' => $json['client_email'],
            'private_key'  => $json['private_key'],
            'token_uri'    => $json['token_uri'] ?? self::TOKEN_URL,
        ];
    }

    /**
     * Get (and cache) an OAuth2 access token via the JWT-bearer grant.
     */
    private function accessToken(array $sa): ?string
    {
        // Return a cached, still-valid token if we have one.
        $cached = Cache::get(self::CACHE_KEY);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $now = time();
        $claims = [
            'iss'   => $sa['client_email'],
            'scope' => self::SCOPE,
            'aud'   => $sa['token_uri'],
            'iat'   => $now,
            'exp'   => $now + 3600,
        ];

        $jwt = $this->signJwt($claims, $sa['private_key']);

        [$status, $response] = $this->httpPost(
            $sa['token_uri'],
            http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]),
            ['Content-Type: application/x-www-form-urlencoded'],
        );

        if ($status < 200 || $status >= 300) {
            Log::warning("FCM token request failed (HTTP {$status}): {$response}");
            return null;
        }

        $decoded   = json_decode($response, true);
        $token     = $decoded['access_token'] ?? null;
        $expiresIn = (int) ($decoded['expires_in'] ?? 3600);

        // Cache only a real token, and only until shortly before it expires.
        if ($token) {
            Cache::put(self::CACHE_KEY, $token, now()->addSeconds(max(60, $expiresIn - 300)));
        }

        return $token;
    }

    /**
     * Build a signed RS256 JWT from claims using the service-account private key.
     */
    private function signJwt(array $claims, string $privateKey): string
    {
        $b64 = fn ($data) => rtrim(strtr(base64_encode($data), '+/', '-_'), '=');

        $header  = $b64(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = $b64(json_encode($claims));
        $signingInput = "{$header}.{$payload}";

        $signature = '';
        if (! openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new \RuntimeException('Failed to sign FCM JWT — check the service-account private key.');
        }

        return "{$signingInput}." . $b64($signature);
    }

    /**
     * @return array{0:int,1:string} [httpStatus, responseBody]
     */
    private function httpPost(string $url, string $body, array $headers): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // SSL verification ON — these are Google endpoints with valid certs.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, (int) config('firebase.timeout', 10));

        $response = curl_exec($ch);
        if ($response === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException("cURL error contacting {$url}: {$err}");
        }
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [$status, (string) $response];
    }
}
