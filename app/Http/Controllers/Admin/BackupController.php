<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\RunBackupJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Admin UI untuk manajemen backup.
 *
 * Backend: spatie/laravel-backup (lihat config/backup.php + routes/console.php).
 * Backup otomatis jalan daily 02:00 — halaman ini untuk trigger manual + download.
 */
class BackupController extends Controller
{
    /**
     * Disk sumber listing backup (local).
     * MinIO off-site tidak di-expose di UI (keamanan: admin tidak perlu download).
     */
    protected string $disk = 'backups';

    /**
     * Folder dalam disk (= APP_NAME dari spatie backup).
     */
    protected function folder(): string
    {
        return config('backup.backup.name', config('app.name', 'Skolah'));
    }

    /**
     * GET /admin/backups
     */
    public function index()
    {
        $storage = Storage::disk($this->disk);
        $folder  = $this->folder();

        $files = [];
        if ($storage->exists($folder)) {
            foreach ($storage->files($folder) as $path) {
                $files[] = [
                    'path'         => $path,
                    'name'         => basename($path),
                    'size'         => $storage->size($path),
                    'size_human'   => $this->humanSize($storage->size($path)),
                    'last_modified'=> $storage->lastModified($path),
                ];
            }
            // Terbaru dulu
            usort($files, fn ($a, $b) => $b['last_modified'] <=> $a['last_modified']);
        }

        // Statistik
        $totalSize = array_sum(array_column($files, 'size'));
        $stats = [
            'count'         => count($files),
            'total_size'    => $this->humanSize($totalSize),
            'newest'        => $files[0]['last_modified'] ?? null,
            'oldest'        => end($files)['last_modified'] ?? null,
            'remote_enabled'=> (bool) env('BACKUP_ENABLE_REMOTE', false),
        ];

        return view('admin.backups.index', compact('files', 'stats'));
    }

    /**
     * POST /admin/backups — trigger manual backup (dispatch ke queue).
     */
    public function store(Request $request)
    {
        $request->validate([
            'only_db' => 'nullable|boolean',
        ]);

        $onlyDb = $request->boolean('only_db');

        RunBackupJob::dispatch($onlyDb, auth()->id());

        Log::channel('daily')->info('Admin triggered manual backup', [
            'admin_id' => auth()->id(),
            'only_db'  => $onlyDb,
        ]);

        $type = $onlyDb ? 'database' : 'full';
        return back()->with('success', "Backup {$type} sudah di-antrikan. Cek halaman ini beberapa menit lagi untuk melihat hasilnya.");
    }

    /**
     * GET /admin/backups/download?file=... — download file backup.
     */
    public function download(Request $request)
    {
        $filename = $request->query('file');

        // Security: cegah path traversal — hanya terima nama file zip tanpa slash
        if (!$filename || !preg_match('/^[\w\-.]+\.zip$/', $filename)) {
            abort(400, 'Invalid filename.');
        }

        $path = $this->folder() . '/' . $filename;
        $storage = Storage::disk($this->disk);

        if (!$storage->exists($path)) {
            abort(404, 'Backup tidak ditemukan.');
        }

        Log::channel('daily')->warning('Admin downloaded backup file', [
            'admin_id' => auth()->id(),
            'file'     => $filename,
        ]);

        return $storage->download($path, $filename);
    }

    /**
     * DELETE /admin/backups — hapus file backup.
     */
    public function destroy(Request $request)
    {
        $filename = $request->input('file');

        if (!$filename || !preg_match('/^[\w\-.]+\.zip$/', $filename)) {
            return back()->with('error', 'Nama file tidak valid.');
        }

        $path = $this->folder() . '/' . $filename;
        $storage = Storage::disk($this->disk);

        if (!$storage->exists($path)) {
            return back()->with('error', 'Backup tidak ditemukan.');
        }

        $storage->delete($path);

        Log::channel('daily')->warning('Admin deleted backup file', [
            'admin_id' => auth()->id(),
            'file'     => $filename,
        ]);

        return back()->with('success', "Backup {$filename} berhasil dihapus.");
    }

    /**
     * Helper: bytes → human readable.
     */
    protected function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        $size = $bytes;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return number_format($size, $i > 0 ? 2 : 0, ',', '.') . ' ' . $units[$i];
    }
}
