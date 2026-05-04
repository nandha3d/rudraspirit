<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Resources\V3\AddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $addresses = Address::where('user_id', $request->user()->id)->latest()->get();

        return $this->collectionResponse($addresses, AddressResource::class);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'address'     => 'required|string',
            'country_id'  => 'required|integer',
            'state_id'    => 'nullable|integer',
            'city_id'     => 'nullable|integer',
            'postal_code' => 'nullable|string',
            'phone'       => 'nullable|string',
        ]);

        $address = new Address();
        $address->user_id     = $request->user()->id;
        $address->address     = $request->address;
        $address->country_id  = $request->country_id;
        $address->state_id    = $request->state_id;
        $address->city_id     = $request->city_id;
        $address->postal_code = $request->postal_code;
        $address->phone       = $request->phone;
        $address->latitude    = $request->latitude;
        $address->longitude   = $request->longitude;
        $address->save();

        return $this->createdResponse(new AddressResource($address), 'Address created.');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $address = Address::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();
        $address->update($request->only([
            'address', 'country_id', 'state_id', 'city_id',
            'postal_code', 'phone', 'latitude', 'longitude'
        ]));

        return $this->resourceResponse($address->fresh(), AddressResource::class);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $address = Address::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();
        $address->delete();

        return $this->successResponse(null, ['message' => 'Address deleted.']);
    }
}
