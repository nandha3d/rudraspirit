<?php

namespace Database\Seeders;

use App\Models\License;
use App\Models\Plan;
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

        // ------------------------------------------------------------------
        // Plans. Module identifiers match the engine's addon unique_identifier
        // values (addons table on client deployments).
        // ------------------------------------------------------------------
        $plans = [
            [
                'name'             => 'Starter',
                'description'      => 'Everything you need to open a single online store.',
                'price'            => 14999,
                'currency'         => 'INR',
                'billing_period'   => 'yearly',
                'duration_days'    => 365,
                'activation_limit' => 1,
                'modules'          => ['otp_system', 'offline_payment', 'indian_pincode'],
                'features'         => [
                    'Core e-commerce engine',
                    'Web storefront + admin panel',
                    'Mobile app APIs (V2 + V3)',
                    '1 domain',
                    'Email support',
                ],
                'is_featured'      => false,
                'sort_order'       => 1,
            ],
            [
                'name'             => 'Business',
                'description'      => 'Grow with marketing modules and customer loyalty.',
                'price'            => 29999,
                'currency'         => 'INR',
                'billing_period'   => 'yearly',
                'duration_days'    => 365,
                'activation_limit' => 2,
                'modules'          => [
                    'otp_system', 'offline_payment', 'affiliate_system',
                    'club_point', 'refund_request',
                    // RudraSpirit finance/utility add-ons
                    'indian_pincode', 'live_currency_rates', 'profit_reports', 'accounting',
                ],
                'features'         => [
                    'Everything in Starter',
                    'Staging + production domains',
                    'Priority support',
                ],
                'is_featured'      => true,
                'sort_order'       => 2,
            ],
            [
                'name'             => 'Enterprise',
                'description'      => 'The full platform — every module, multi-domain.',
                'price'            => 59999,
                'currency'         => 'INR',
                'billing_period'   => 'yearly',
                'duration_days'    => 365,
                'activation_limit' => 5,
                'modules'          => [
                    'otp_system', 'offline_payment', 'affiliate_system',
                    'club_point', 'refund_request', 'auction',
                    'seller_subscription', 'wholesale', 'pos_system',
                    // Every RudraSpirit finance/utility add-on (full platform)
                    'indian_pincode', 'live_currency_rates', 'profit_reports',
                    'accounting', 'gst_reports', 'partner_share', 'purchase_inventory',
                ],
                'features'         => [
                    'Everything in Business',
                    'Up to 5 domains',
                    'Dedicated support',
                ],
                'is_featured'      => false,
                'sort_order'       => 3,
            ],
        ];

        foreach ($plans as $data) {
            Plan::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($data['name'])],
                $data + ['slug' => \Illuminate\Support\Str::slug($data['name']), 'is_active' => true],
            );
        }

        $this->command?->info('Plans seeded: Starter, Business, Enterprise.');

        // A ready-to-use sample license on the Business plan for smoke-testing.
        if (! License::where('customer_email', 'sample@example.com')->exists()) {
            $license = License::create([
                'license_key'      => License::generateKey(),
                'product'          => config('license.default_product'),
                'plan_id'          => Plan::where('slug', 'business')->value('id'),
                'customer_name'    => 'Sample Customer',
                'customer_email'   => 'sample@example.com',
                'status'           => 'active',
                'activation_limit' => 1,
                'notes'            => 'Auto-created sample license for testing.',
            ]);

            $this->command?->info("Sample license key: {$license->license_key}");
        }

        // RudraSpirit's own production license — Enterprise plan (entitles every
        // module). Keyed on email so re-seeding keeps the SAME key (so the shop's
        // LICENSE_KEY never breaks). Provide RUDRASPIRIT_LICENSE_KEY to pin one.
        $existingRudraKey = License::where('customer_email', 'admin@rudraspirit.com')->value('license_key');
        $rudra = License::updateOrCreate(
            ['customer_email' => 'admin@rudraspirit.com'],
            [
                'license_key'      => env('RUDRASPIRIT_LICENSE_KEY') ?: ($existingRudraKey ?: License::generateKey()),
                'product'          => config('license.default_product'),
                'plan_id'          => Plan::where('slug', 'enterprise')->value('id'),
                'customer_name'    => 'RudraSpirit',
                'status'           => 'active',
                'activation_limit' => 3,
                'notes'            => 'Primary production license for rudraspirit.com (all modules).',
            ],
        );

        $this->command?->warn("RudraSpirit (Enterprise) license key: {$rudra->license_key}");
    }
}
