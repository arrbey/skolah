<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================================
// Scheduled Commands — Skolah.com
// ============================================================

// Otomatis gagalkan order yang melewati batas waktu 24 jam
Schedule::command('orders:expire-unpaid')->everyFiveMinutes();

// Kirim email pengingat pembayaran (3 jam sebelum kedaluwarsa)
Schedule::command('orders:send-payment-reminders')->hourly();

// Kirim email pengingat membership yang akan berakhir (H-3 dan H-1)
Schedule::command('memberships:send-expiry-reminders')->dailyAt('09:00');

// Kirim email pengingat bootcamp (H-1 dan 1 jam sebelum mulai)
Schedule::command('bootcamps:send-reminders')->everyFifteenMinutes();

// Bersihkan audit logs > 90 hari (Fase 9 — Security)
Schedule::command('model:prune', ['--model' => [\App\Models\AuditLog::class]])->daily();

// ─── Backup Otomatis (spatie/laravel-backup) ─────────────────────────────
// Jalankan cleanup dulu (hapus backup lama sesuai retention policy),
// lalu backup baru, lalu monitor (kirim alert jika >24 jam tidak ada backup sehat)
Schedule::command('backup:clean')->daily()->at('01:30')
    ->onOneServer()
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Scheduled backup:clean failed');
    });

Schedule::command('backup:run')->daily()->at('02:00')
    ->onOneServer()
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Scheduled backup:run failed');
    });

Schedule::command('backup:monitor')->daily()->at('09:00')
    ->onOneServer();
