<?php

namespace Database\Seeders;

use App\Models\License;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account. Override via ADMIN_EMAIL / ADMIN_PASSWORD before seeding.
        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $password = env('ADMIN_PASSWORD', 'change-me-now');

        User::updateOrCreate(
            ['email' => $email],
            ['name' => 'Administrator', 'password' => Hash::make($password)],
        );

        $this->command?->warn("Admin login: {$email} / (password from ADMIN_PASSWORD, default 'change-me-now')");

        // A ready-to-use sample license for smoke-testing the verify endpoint.
        if (! License::where('customer_email', 'sample@example.com')->exists()) {
            $license = License::create([
                'license_key'      => License::generateKey(),
                'product'          => config('license.default_product'),
                'customer_name'    => 'Sample Customer',
                'customer_email'   => 'sample@example.com',
                'status'           => 'active',
                'activation_limit' => 1,
                'notes'            => 'Auto-created sample license for testing.',
            ]);
            $license->addons()->create([
                'addon_identifier' => 'affiliate_system',
                'label'            => 'Affiliate System',
            ]);

            $this->command?->info("Sample license key: {$license->license_key}");
        }
    }
}
