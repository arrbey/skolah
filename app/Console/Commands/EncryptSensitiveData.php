<?php

namespace App\Console\Commands;

use App\Models\BookOrder;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * Fase 11 — Encrypt Existing Sensitive Data
 *
 * Mengenkripsi data plain text yang sudah ada di database.
 * Jalankan SEKALI setelah mengubah casts ke encrypted.
 *
 * Usage: php artisan encrypt:sensitive-data
 */
class EncryptSensitiveData extends Command
{
    protected $signature = 'encrypt:sensitive-data
                            {--dry-run : Tampilkan apa yang akan diencrypt tanpa mengubah data}
                            {--force : Skip confirmation prompt}';

    protected $description = 'Encrypt existing plain text sensitive data in database (one-time migration)';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN — Tidak ada data yang diubah.');
        }

        if (!$isDryRun && !$this->option('force')) {
            if (!$this->confirm('⚠️  Ini akan mengenkripsi data sensitif yang ada. Sudah backup database?')) {
                $this->info('Dibatalkan.');
                return 0;
            }
        }

        $totalEncrypted = 0;

        // ── 1. Users: bio ────────────────────────────────────────────────────
        $this->info('');
        $this->info('📝 Encrypting Users.bio...');
        $totalEncrypted += $this->encryptColumn(
            'users', 'bio', 'id',
            fn($value) => !$this->isAlreadyEncrypted($value),
            $isDryRun
        );

        // ── 2. Orders: midtrans_snap_token ──────────────────────────────────
        $this->info('');
        $this->info('💳 Encrypting Orders.midtrans_snap_token...');
        $totalEncrypted += $this->encryptColumn(
            'orders', 'midtrans_snap_token', 'id',
            fn($value) => !$this->isAlreadyEncrypted($value),
            $isDryRun
        );

        // ── 3. Orders: midtrans_transaction_id ──────────────────────────────
        $this->info('');
        $this->info('💳 Encrypting Orders.midtrans_transaction_id...');
        $totalEncrypted += $this->encryptColumn(
            'orders', 'midtrans_transaction_id', 'id',
            fn($value) => !$this->isAlreadyEncrypted($value),
            $isDryRun
        );

        // ── 4. Orders: midtrans_order_id ────────────────────────────────────
        $this->info('');
        $this->info('💳 Encrypting Orders.midtrans_order_id...');
        $totalEncrypted += $this->encryptColumn(
            'orders', 'midtrans_order_id', 'id',
            fn($value) => !$this->isAlreadyEncrypted($value),
            $isDryRun
        );

        // ── 5. BookOrders: shipping_address ─────────────────────────────────
        $this->info('');
        $this->info('📦 Encrypting BookOrders.shipping_address...');
        $totalEncrypted += $this->encryptJsonColumn(
            'book_orders', 'shipping_address', 'id',
            $isDryRun
        );

        $this->newLine();
        $action = $isDryRun ? 'Akan diencrypt' : 'Berhasil diencrypt';
        $this->info("✅ {$action}: {$totalEncrypted} records total.");

        return 0;
    }

    /**
     * Encrypt a single string column.
     */
    protected function encryptColumn(
        string $table,
        string $column,
        string $primaryKey,
        callable $shouldEncrypt,
        bool $isDryRun
    ): int {
        $count = 0;

        DB::table($table)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->orderBy($primaryKey)
            ->chunk(100, function ($rows) use ($table, $column, $primaryKey, $shouldEncrypt, $isDryRun, &$count) {
                foreach ($rows as $row) {
                    $value = $row->{$column};

                    if (!$shouldEncrypt($value)) {
                        $this->line("  ⏭ {$table}.{$primaryKey}={$row->{$primaryKey}} — already encrypted, skip.");
                        continue;
                    }

                    if ($isDryRun) {
                        $preview = substr($value, 0, 30) . (strlen($value) > 30 ? '...' : '');
                        $this->line("  📋 {$table}.{$primaryKey}={$row->{$primaryKey}} — would encrypt: \"{$preview}\"");
                    } else {
                        DB::table($table)
                            ->where($primaryKey, $row->{$primaryKey})
                            ->update([$column => Crypt::encryptString($value)]);
                        $this->line("  🔐 {$table}.{$primaryKey}={$row->{$primaryKey}} — encrypted.");
                    }

                    $count++;
                }
            });

        $this->info("  Total: {$count} records.");
        return $count;
    }

    /**
     * Encrypt a JSON column (array → encrypted:array).
     * encrypted:array = Crypt::encrypt(json_encode($array))
     */
    protected function encryptJsonColumn(
        string $table,
        string $column,
        string $primaryKey,
        bool $isDryRun
    ): int {
        $count = 0;

        DB::table($table)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->orderBy($primaryKey)
            ->chunk(100, function ($rows) use ($table, $column, $primaryKey, $isDryRun, &$count) {
                foreach ($rows as $row) {
                    $value = $row->{$column};

                    // Jika sudah encrypted (bukan JSON valid), skip
                    if (!$this->isValidJson($value)) {
                        $this->line("  ⏭ {$table}.{$primaryKey}={$row->{$primaryKey}} — not valid JSON, skip.");
                        continue;
                    }

                    if ($isDryRun) {
                        $preview = substr($value, 0, 40) . (strlen($value) > 40 ? '...' : '');
                        $this->line("  📋 {$table}.{$primaryKey}={$row->{$primaryKey}} — would encrypt JSON: \"{$preview}\"");
                    } else {
                        // encrypted:array cast expects Crypt::encrypt() of the raw JSON string
                        DB::table($table)
                            ->where($primaryKey, $row->{$primaryKey})
                            ->update([$column => Crypt::encryptString($value)]);
                        $this->line("  🔐 {$table}.{$primaryKey}={$row->{$primaryKey}} — encrypted JSON.");
                    }

                    $count++;
                }
            });

        $this->info("  Total: {$count} records.");
        return $count;
    }

    /**
     * Cek apakah value kemungkinan sudah diencrypt oleh Laravel.
     * Encrypted string dari Laravel selalu dimulai dengan "eyJ" (base64 dari JSON).
     */
    protected function isAlreadyEncrypted(string $value): bool
    {
        // Laravel encrypted strings are base64 JSON with iv, value, mac keys
        if (strlen($value) < 50) {
            return false;
        }

        // Try to decrypt — if successful, it's already encrypted
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Cek apakah string adalah valid JSON.
     */
    protected function isValidJson(string $value): bool
    {
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
