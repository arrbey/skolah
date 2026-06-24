<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class RunBackupJob implements ShouldQueue
{
    use Queueable;

    /**
     * Timeout: max 30 menit untuk proses backup.
     */
    public $timeout = 1800;

    /**
     * Jangan retry otomatis — backup gagal → admin trigger manual lagi.
     */
    public $tries = 1;

    protected ?bool $onlyDb;
    protected ?int $triggeredBy;

    public function __construct(bool $onlyDb = false, ?int $triggeredBy = null)
    {
        $this->onlyDb = $onlyDb;
        $this->triggeredBy = $triggeredBy;
    }

    public function handle(): void
    {
        Log::channel('daily')->info('Manual backup triggered', [
            'only_db'        => $this->onlyDb,
            'triggered_by'   => $this->triggeredBy,
        ]);

        $options = $this->onlyDb ? ['--only-db' => true] : [];
        $exitCode = Artisan::call('backup:run', $options);

        Log::channel('daily')->info('Manual backup finished', [
            'exit_code' => $exitCode,
            'output'    => Artisan::output(),
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::channel('daily')->error('Manual backup job failed', [
            'triggered_by' => $this->triggeredBy,
            'error'        => $e->getMessage(),
        ]);
    }
}
