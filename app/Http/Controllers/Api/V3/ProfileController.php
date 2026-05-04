<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Resources\V3\UserResource;
use App\Http\Resources\V3\AddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * GET /api/v3/user/profile
     */
    public function show(Request $request): JsonResponse
    {
        return $this->resourceResponse($request->user(), UserResource::class);
    }

    /**
     * PATCH /api/v3/user/profile
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'name'  => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
        ]);

        $user = $request->user();
        $user->update($request->only(['name', 'phone']));

        return $this->resourceResponse($user->fresh(), UserResource::class);
    }
}
