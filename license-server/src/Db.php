<?php
/**
 * Tiny PDO wrapper + auto-migration. Works with SQLite (default) or MySQL.
 */
class Db
{
    private static ?PDO $pdo = null;

    public static function conn(array $config): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        if (($config['db_driver'] ?? 'sqlite') === 'mysql') {
            $m = $config['mysql'];
            $dsn = "mysql:host={$m['host']};port={$m['port']};dbname={$m['database']};charset=utf8mb4";
            $pdo = new PDO($dsn, $m['username'], $m['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $autoInc = 'INT AUTO_INCREMENT PRIMARY KEY';
        } else {
            $path = $config['sqlite_path'];
            $dir = dirname($path);
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            $pdo = new PDO('sqlite:' . $path, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $pdo->exec('PRAGMA journal_mode = WAL');
            $pdo->exec('PRAGMA foreign_keys = ON');
            $autoInc = 'INTEGER PRIMARY KEY AUTOINCREMENT';
        }

        self::migrate($pdo, $autoInc);
        self::$pdo = $pdo;
        return $pdo;
    }

    private static function migrate(PDO $pdo, string $autoInc): void
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS licenses (
            id $autoInc,
            code VARCHAR(64) NOT NULL UNIQUE,
            type VARCHAR(16) NOT NULL DEFAULT 'item',
            addon_identifier VARCHAR(120) NULL,
            buyer_name VARCHAR(191) NULL,
            buyer_email VARCHAR(191) NULL,
            status VARCHAR(16) NOT NULL DEFAULT 'active',
            max_domains INT NOT NULL DEFAULT 1,
            note TEXT NULL,
            created_at VARCHAR(32) NOT NULL,
            updated_at VARCHAR(32) NULL
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS activations (
            id $autoInc,
            license_id INT NOT NULL,
            domain VARCHAR(191) NOT NULL,
            ip VARCHAR(64) NULL,
            created_at VARCHAR(32) NOT NULL
        )");

        // per-license module/feature entitlements (per-license overrides)
        $pdo->exec("CREATE TABLE IF NOT EXISTS entitlements (
            id $autoInc,
            license_id INT NOT NULL,
            module VARCHAR(120) NOT NULL,
            enabled INT NOT NULL DEFAULT 1,
            updated_at VARCHAR(32) NULL
        )");

        // plan -> feature matrix overrides (defaults come from the catalog min_tier)
        $pdo->exec("CREATE TABLE IF NOT EXISTS plan_features (
            id $autoInc,
            plan VARCHAR(32) NOT NULL,
            feature VARCHAR(120) NOT NULL,
            enabled INT NOT NULL DEFAULT 1,
            updated_at VARCHAR(32) NULL
        )");

        // add plan column to licenses (existing rows default to enterprise = all on)
        try { $pdo->exec("ALTER TABLE licenses ADD COLUMN plan VARCHAR(32) DEFAULT 'enterprise'"); } catch (Throwable $e) {}

        // helpful indexes (ignore failures on re-run)
        try { $pdo->exec("CREATE INDEX idx_lic_code ON licenses(code)"); } catch (Throwable $e) {}
        try { $pdo->exec("CREATE INDEX idx_act_lic ON activations(license_id)"); } catch (Throwable $e) {}
        try { $pdo->exec("CREATE UNIQUE INDEX idx_act_unique ON activations(license_id, domain)"); } catch (Throwable $e) {}
        try { $pdo->exec("CREATE UNIQUE INDEX idx_ent_unique ON entitlements(license_id, module)"); } catch (Throwable $e) {}
    }
}
