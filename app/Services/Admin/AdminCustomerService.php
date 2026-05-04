<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminCustomerService
{
    public function listCustomers(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = User::where('user_type', 'customer')->latest();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }
        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        return $query->paginate($perPage);
    }

    public function getCustomer(int $id): User
    {
        return User::where('user_type', 'customer')->findOrFail($id);
    }

    public function updateBanStatus(int $id, bool $banned): User
    {
        $user = User::where('user_type', 'customer')->findOrFail($id);
        $user->banned = $banned ? 1 : 0;
        $user->save();

        return $user;
    }
}
