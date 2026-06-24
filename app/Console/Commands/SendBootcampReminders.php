<?php

namespace App\Console\Commands;

use App\Mail\BootcampReminderMail;
use App\Models\Bootcamp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBootcampReminders extends Command
{
    protected $signature = 'bootcamps:send-reminders';

    protected $description = 'Kirim email pengingat bootcamp H-1 dan 1 jam sebelum mulai (termasuk link Zoom/Meet)';

    public function handle(): int
    {
        $count = 0;

        // ── Reminder H-1 (bootcamp yang mulai besok, window ±30 menit) ────────
        $tomorrow = Bootcamp::where('status', 'upcoming')
            ->whereBetween('start_date', [
                now()->addDay()->subMinutes(30),
                now()->addDay()->addMinutes(30),
            ])
            ->with([
                'paidRegistrations' => fn($q) => $q->where('reminder_sent_1day', false),
                'paidRegistrations.user',
                'instructor'
            ])
            ->get();

        foreach ($tomorrow as $bootcamp) {
            $sentCount = 0;
            foreach ($bootcamp->paidRegistrations as $registration) {
                Mail::to($registration->user->email)->send(
                    new BootcampReminderMail($registration->user, $bootcamp, '1day')
                );
                $registration->update(['reminder_sent_1day' => true]);
                $count++;
                $sentCount++;
            }

            if ($sentCount > 0) {
                Log::info('Bootcamp H-1 reminders sent', [
                    'bootcamp_id' => $bootcamp->id,
                    'title'       => $bootcamp->title,
                    'start_date'  => $bootcamp->start_date,
                    'recipients'  => $sentCount,
                ]);
            }
        }

        // ── Reminder 1 jam sebelum (window ±15 menit) ────────────────────────
        $soon = Bootcamp::where('status', 'upcoming')
            ->whereBetween('start_date', [
                now()->addHour()->subMinutes(15),
                now()->addHour()->addMinutes(15),
            ])
            ->with([
                'paidRegistrations' => fn($q) => $q->where('reminder_sent_1hour', false),
                'paidRegistrations.user',
                'instructor'
            ])
            ->get();

        foreach ($soon as $bootcamp) {
            $sentCount = 0;
            foreach ($bootcamp->paidRegistrations as $registration) {
                Mail::to($registration->user->email)->send(
                    new BootcampReminderMail($registration->user, $bootcamp, '1hour')
                );
                $registration->update(['reminder_sent_1hour' => true]);
                $count++;
                $sentCount++;
            }

            if ($sentCount > 0) {
                Log::info('Bootcamp 1-hour reminders sent', [
                    'bootcamp_id' => $bootcamp->id,
                    'title'       => $bootcamp->title,
                    'start_date'  => $bootcamp->start_date,
                    'recipients'  => $sentCount,
                ]);
            }
        }

        $this->info("Berhasil mengirim {$count} email pengingat bootcamp.");

        return self::SUCCESS;
    }
}
