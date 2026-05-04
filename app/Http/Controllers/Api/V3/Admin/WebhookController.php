<?php

namespace App\Http\Controllers\Api\V3\Admin;

use App\Http\Controllers\Api\V3\Controller;
use App\Models\WebhookEndpoint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function index(): JsonResponse
    {
        $endpoints = WebhookEndpoint::latest()->get();
        return $this->successResponse($endpoints);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'      => 'required|string',
            'url'       => 'required|url',
            'events'    => 'required|array',
            'is_active' => 'boolean'
        ]);

        $endpoint = WebhookEndpoint::create([
            'name'      => $request->name,
            'url'       => $request->url,
            'secret'    => bin2hex(random_bytes(16)),
            'events'    => $request->events,
            'is_active' => $request->is_active ?? true,
        ]);

        return $this->createdResponse($endpoint, 'Webhook created.');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $endpoint = WebhookEndpoint::findOrFail($id);
        $endpoint->update($request->only(['name', 'url', 'events', 'is_active']));

        return $this->successResponse($endpoint->fresh());
    }

    public function destroy(int $id): JsonResponse
    {
        $endpoint = WebhookEndpoint::findOrFail($id);
        $endpoint->delete();

        return $this->successResponse(null, ['message' => 'Webhook deleted.']);
    }

    public function logs(int $id): JsonResponse
    {
        $endpoint = WebhookEndpoint::findOrFail($id);
        $logs = $endpoint->logs()->latest()->take(50)->get();

        return $this->successResponse($logs);
    }
}
