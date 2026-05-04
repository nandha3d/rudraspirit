<?php

namespace App\Http\Controllers\Api\V3\Admin;

use App\Http\Controllers\Api\V3\Controller;
use App\Http\Resources\V3\UserResource;
use App\Services\Admin\AdminCustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private AdminCustomerService $service;

    public function __construct(AdminCustomerService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['name', 'email']);
        $perPage = $this->getPerPage($request->input('per_page'));

        $customers = $this->service->listCustomers($filters, $perPage);

        return $this->paginatedResponse($customers, UserResource::class);
    }

    public function show(int $id): JsonResponse
    {
        $customer = $this->service->getCustomer($id);
        return $this->resourceResponse($customer, UserResource::class);
    }

    public function updateBan(Request $request, int $id): JsonResponse
    {
        $request->validate(['banned' => 'required|boolean']);
        
        $customer = $this->service->updateBanStatus($id, $request->banned);
        return $this->resourceResponse(new UserResource($customer));
    }
}
