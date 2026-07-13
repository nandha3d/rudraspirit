<?php

namespace App\Console\Commands;

use App\Models\BusinessSetting;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * White-label logo helper. Registers a logo image as an Upload and points the
 * brand logo settings at it, so it shows in admin + storefront + footer:
 *
 *   php artisan brand:logo "C:/path/to/logo.png"
 *   php artisan brand:logo logo.png --settings=header_logo,footer_logo
 *
 * Default targets header_logo (storefront), footer_logo, and the admin
 * system_logo_black / system_logo_white.
 */
class SetBrandLogo extends Command
{
    protected $signature = 'brand:logo {path : Path to the logo image file}
                            {--settings=header_logo,footer_logo,system_logo_black,system_logo_white : Comma-separated business settings to point at the logo}';

    protected $description = 'Register a logo image and set the brand logo settings (white-label)';

    public function handle(): int
    {
        $path = $this->argument('path');
        if (! is_file($path)) {
            $this->error("File not found: $path");
            return self::FAILURE;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (! in_array($ext, ['png', 'jpg', 'jpeg', 'svg', 'webp', 'gif'], true)) {
            $this->error("Not a supported image type: .$ext");
            return self::FAILURE;
        }

        $destDir = public_path('uploads/all');
        if (! is_dir($destDir) && ! mkdir($destDir, 0775, true) && ! is_dir($destDir)) {
            $this->error("Cannot create upload dir: $destDir");
            return self::FAILURE;
        }

        $fname = 'brand-logo-' . date('YmdHis') . '-' . Str::random(6) . '.' . $ext;
        $dest = $destDir . DIRECTORY_SEPARATOR . $fname;
        if (! copy($path, $dest)) {
            $this->error('Failed to copy the file into public/uploads/all.');
            return self::FAILURE;
        }

        $adminId = User::where('user_type', 'admin')->orderBy('id')->value('id');

        $upload = Upload::create([
            'file_original_name' => pathinfo($path, PATHINFO_FILENAME),
            'file_name'          => 'uploads/all/' . $fname,
            'user_id'            => $adminId,
            'extension'          => $ext,
            'type'               => 'image',
            'file_size'          => filesize($dest),
        ]);

        $settings = array_values(array_filter(array_map('trim', explode(',', (string) $this->option('settings')))));
        foreach ($settings as $key) {
            $s = BusinessSetting::firstOrNew(['type' => $key]);
            $s->value = $upload->id;
            $s->save();
        }

        Cache::forget('business_settings');

        $this->info("Logo registered as upload #{$upload->id} ({$upload->file_name}).");
        $this->info('Set for: ' . implode(', ', $settings));
        return self::SUCCESS;
    }
}
