<?php

namespace App\Console\Commands;

use App\Models\Pincode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportPincodes extends Command
{
    /**
     * Import the All-India Pincode directory (India Post) into the pincodes table.
     *
     * Get the CSV free from data.gov.in ("All India Pincode Directory"), then:
     *   php artisan pincode:import storage/app/pincodes.csv
     * or download straight onto the server:
     *   php artisan pincode:import --url="https://.../pincodes.csv"
     *
     * @var string
     */
    protected $signature = 'pincode:import
                            {file? : Path to the CSV file (absolute, or relative to project root)}
                            {--url= : Download the CSV from this URL first}
                            {--no-truncate : Append instead of replacing the table}';

    protected $description = 'Import the All-India Pincode directory (India Post) into the pincodes table';

    /**
     * Map of normalised CSV headers -> table column. First match wins.
     */
    private array $headerMap = [
        'pincode'      => 'pincode',
        'pin'          => 'pincode',
        'officename'   => 'office_name',
        'officetype'   => 'office_type',
        'district'     => 'district',
        'districtname' => 'district',
        'statename'    => 'state',
        'state'        => 'state',
        'circlename'   => 'circle',
        'circle'       => 'circle',
        'regionname'   => 'region',
        'region'       => 'region',
        'divisionname' => 'division',
        'division'     => 'division',
        'delivery'     => 'delivery',
        'latitude'     => 'latitude',
        'lat'          => 'latitude',
        'longitude'    => 'longitude',
        'long'         => 'longitude',
        'lng'          => 'longitude',
    ];

    public function handle(): int
    {
        $path = $this->argument('file');

        if ($url = $this->option('url')) {
            $path = storage_path('app/pincodes_download_' . date('YmdHis') . '.csv');
            $this->info("Downloading $url ...");
            $data = @file_get_contents($url);
            if ($data === false) {
                $this->error('Download failed.');
                return self::FAILURE;
            }
            file_put_contents($path, $data);
        }

        if (! $path) {
            $this->error('Provide a CSV file path or --url. See: php artisan help pincode:import');
            return self::FAILURE;
        }

        if (! file_exists($path) && file_exists(base_path($path))) {
            $path = base_path($path);
        }
        if (! file_exists($path)) {
            $this->error("File not found: $path");
            return self::FAILURE;
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            $this->error("Unable to open: $path");
            return self::FAILURE;
        }

        // Header row -> column index map.
        $header = fgetcsv($handle);
        if (! $header) {
            $this->error('Empty CSV.');
            fclose($handle);
            return self::FAILURE;
        }
        // Strip UTF-8 BOM from first header cell.
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

        $colIndex = [];
        foreach ($header as $i => $name) {
            $norm = preg_replace('/[^a-z0-9]/', '', strtolower((string) $name));
            if (isset($this->headerMap[$norm]) && ! isset($colIndex[$this->headerMap[$norm]])) {
                $colIndex[$this->headerMap[$norm]] = $i;
            }
        }

        if (! isset($colIndex['pincode'])) {
            $this->error('No "pincode" column found. Headers seen: ' . implode(', ', $header));
            fclose($handle);
            return self::FAILURE;
        }

        if (! $this->option('no-truncate')) {
            $this->info('Clearing existing pincodes ...');
            DB::table('pincodes')->truncate();
        }

        $columns = ['pincode', 'office_name', 'office_type', 'district', 'state', 'circle', 'region', 'division', 'delivery', 'latitude', 'longitude'];
        $batch = [];
        $count = 0;
        $chunk = 2000;

        while (($row = fgetcsv($handle)) !== false) {
            $rec = [];
            foreach ($columns as $col) {
                $val = isset($colIndex[$col]) ? trim((string) ($row[$colIndex[$col]] ?? '')) : null;
                if ($col === 'latitude' || $col === 'longitude') {
                    $val = is_numeric($val) ? (float) $val : null;
                } elseif ($val === '' || strtoupper((string) $val) === 'NA') {
                    $val = null;
                }
                $rec[$col] = $val;
            }

            if (empty($rec['pincode'])) {
                continue;
            }

            $batch[] = $rec;
            $count++;

            if (count($batch) >= $chunk) {
                Pincode::insert($batch);
                $batch = [];
                if ($count % 20000 === 0) {
                    $this->line("  imported $count rows ...");
                }
            }
        }
        if ($batch) {
            Pincode::insert($batch);
        }
        fclose($handle);

        if ($url ?? false) {
            @unlink($path);
        }

        $this->info("Done. Imported $count pincode rows.");
        return self::SUCCESS;
    }
}
