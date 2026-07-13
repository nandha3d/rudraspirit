<?php

namespace App\Console\Commands;

use App\Models\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateCurrencyRates extends Command
{
    /**
     * @var string
     */
    protected $signature = 'currency:update-rates {--dry-run : Show fetched rates without saving}';

    /**
     * @var string
     */
    protected $description = 'Fetch live foreign-exchange rates and update the exchange_rate of active currencies';

    public function handle(): int
    {
        if (! feature_allowed('live_currency_rates')) {
            $this->warn("Module 'live_currency_rates' is not entitled by the license plan. Skipping.");
            return self::SUCCESS;
        }

        if (! (bool) get_setting('currency_auto_update', config('currency_rates.enabled', true))) {
            $this->info('Currency auto-update is disabled. Skipping.');
            return self::SUCCESS;
        }

        // Anchor: the system default currency. All rates are stored relative to it
        // (convert_price() divides by the default rate and multiplies by the target).
        $defaultId = get_setting('system_default_currency');
        $default   = Currency::find($defaultId);

        if (! $default || empty($default->code)) {
            $this->error('No valid system default currency set. Aborting.');
            return self::FAILURE;
        }

        $base = strtoupper(trim($default->code));

        $endpoint = rtrim(config('currency_rates.provider_url', 'https://open.er-api.com/v6/latest'), '/') . '/' . $base;

        try {
            $response = Http::timeout(config('currency_rates.timeout', 15))->retry(2, 500)->get($endpoint);
        } catch (\Throwable $e) {
            Log::warning('[currency:update-rates] request failed: ' . $e->getMessage());
            $this->error('Rate provider request failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        if (! $response->ok()) {
            $this->error('Rate provider returned HTTP ' . $response->status() . '. Rates unchanged.');
            return self::FAILURE;
        }

        $data  = $response->json();
        $rates = $data['rates'] ?? null;

        // open.er-api.com uses result=success; other providers may omit it.
        if ((isset($data['result']) && $data['result'] !== 'success') || ! is_array($rates) || empty($rates)) {
            $this->error('Rate provider returned no usable rates. Rates unchanged.');
            return self::FAILURE;
        }

        // Normalise keys to uppercase for safe matching.
        $rates = array_change_key_case($rates, CASE_UPPER);

        $currencies = Currency::where('status', 1)->get();
        $updated = 0;
        $skipped = [];

        foreach ($currencies as $currency) {
            $code = strtoupper(trim($currency->code));

            if ($code === $base) {
                // Anchor is always 1 relative to itself.
                if ((float) $currency->exchange_rate !== 1.0 && ! $this->option('dry-run')) {
                    $currency->exchange_rate = 1;
                    $currency->save();
                }
                continue;
            }

            if (! isset($rates[$code])) {
                $skipped[] = $code;
                continue;
            }

            $rate = (float) $rates[$code];
            if ($rate <= 0) {
                $skipped[] = $code;
                continue;
            }

            $this->line(sprintf('  %s: %s -> %s', $code, $currency->exchange_rate, $rate));

            if (! $this->option('dry-run')) {
                $currency->exchange_rate = $rate;
                $currency->save();
            }
            $updated++;
        }

        // Bust the cached default currency so new rates take effect immediately.
        Cache::forget('system_default_currency');

        $stamp = $data['time_last_update_utc'] ?? now()->toIso8601String();
        $msg = sprintf(
            '[currency:update-rates] base=%s updated=%d skipped=%s source_time=%s%s',
            $base,
            $updated,
            $skipped ? implode(',', $skipped) : 'none',
            $stamp,
            $this->option('dry-run') ? ' (DRY RUN, nothing saved)' : ''
        );

        Log::info($msg);
        $this->info($msg);

        return self::SUCCESS;
    }
}
