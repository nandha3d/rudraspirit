<?php

namespace App\Console\Commands;

use App\Models\BusinessSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * White-label deploy helper. The platform product is "Zolo Kart"; each client
 * deployment rebrands to its own store name with a single command:
 *
 *   php artisan brand:set "Client Store Name"
 *   php artisan brand:set "Client Store" --motto="Best deals online"
 *
 * Sets the visible brand (site_name / site_motto) used across the storefront,
 * admin, page titles and emails. Logo, domain (APP_URL) and LICENSE_KEY are set
 * separately — see WHITELABEL.md.
 */
class SetBrand extends Command
{
    protected $signature = 'brand:set {name : The store/brand name to display}
                                      {--motto= : Optional tagline/motto}';

    protected $description = 'Set the deployment brand name (white-label rebrand)';

    public function handle(): int
    {
        $name = trim((string) $this->argument('name'));
        if ($name === '') {
            $this->error('Brand name cannot be empty.');
            return self::FAILURE;
        }

        $this->putSetting('site_name', $name);

        if ($this->option('motto') !== null) {
            $this->putSetting('site_motto', trim((string) $this->option('motto')));
        }

        // get_setting() caches every business setting under this key.
        Cache::forget('business_settings');

        $this->info("Brand set to: {$name}");
        $this->line('Remaining per-deploy steps (see WHITELABEL.md):');
        $this->line('  - Logo/favicon: admin > Business Settings > Website Setup');
        $this->line('  - Domain:       APP_URL in .env + web server vhost');
        $this->line('  - License key:  LICENSE_KEY in .env (issued from the license server)');

        return self::SUCCESS;
    }

    private function putSetting(string $type, string $value): void
    {
        $setting = BusinessSetting::firstOrNew(['type' => $type]);
        $setting->value = $value;
        $setting->save();
    }
}
