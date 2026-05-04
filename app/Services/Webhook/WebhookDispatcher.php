<?php

namespace App\Services\Webhook;

use App\Models\WebhookEndpoint;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookDispatcher
{
    public function dispatch(string $eventName, array $payload): void
    {
        if (!config('headless.webhooks.enabled', false)) {
            return;
        }

        $endpoints = WebhookEndpoint::where('is_active', true)
            ->whereJsonContains('events', $eventName)
            ->get();

        foreach ($endpoints as $endpoint) {
            $this->sendPayload($endpoint, $eventName, $payload);
        }
    }

    private function sendPayload(WebhookEndpoint $endpoint, string $eventName, array $payload): void
    {
        $timestamp = time();
        $signature = $this->generateSignature($endpoint->secret, $payload, $timestamp);

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'X-Commerce-Event'     => $eventName,
                    'X-Commerce-Signature' => $signature,
                    'X-Commerce-Timestamp' => $timestamp,
                ])
                ->post($endpoint->url, $payload);

            $this->logDelivery($endpoint->id, $eventName, $payload, $response->status(), $response->body(), $response->successful());
        } catch (\Exception $e) {
            Log::error("Webhook delivery failed: {$e->getMessage()}");
            $this->logDelivery($endpoint->id, $eventName, $payload, null, $e->getMessage(), false);
        }
    }

    private function generateSignature(?string $secret, array $payload, int $timestamp): string
    {
        if (!$secret) {
            return '';
        }
        $data = $timestamp . '.' . json_encode($payload);
        return hash_hmac('sha256', $data, $secret);
    }

    private function logDelivery(int $endpointId, string $event, array $payload, ?int $status, ?string $response, bool $isSuccessful): void
    {
        WebhookLog::create([
            'webhook_endpoint_id' => $endpointId,
            'event'               => $event,
            'payload'             => $payload,
            'status_code'         => $status,
            'response'            => $response,
            'is_successful'       => $isSuccessful,
        ]);
    }
}
