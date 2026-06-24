<?php

namespace App\Console\Commands;

use App\Mail\MembershipExpiryMail;
use App\Models\UserMembership;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMembershipExpiryReminders extends Command
{
    protected $signature = 'memberships:send-expiry-reminders';

    protected $description = 'Kirim email pengingat membership yang akan berakhir dalam 3 hari dan 1 hari';

    public function handle(): int
    {
        // Kirim reminder untuk membership yang berakhir dalam 3 hari (±1 jam window)
        $memberships = UserMembership::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->where(function ($q) {
                // 3 hari sebelum (window: 2.5 - 3.5 hari)
                $q->whereBetween('expires_at', [
                    now()->addDays(3)->subMinutes(30),
                    now()->addDays(3)->addMinutes(30),
                ])
                // 1 hari sebelum (window: 0.5 - 1.5 hari)
                ->orWhereBetween('expires_at', [
                    now()->addDay()->subMinutes(30),
                    now()->addDay()->addMinutes(30),
                ]);
            })
            ->with(['user', 'plan'])
            ->get();

        if ($memberships->isEmpty()) {
            $this->info('Tidak ada membership yang perlu diingatkan.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($memberships as $membership) {
            Mail::to($membership->user->email)->send(new MembershipExpiryMail($membership));
            $count++;

            Log::info('Membership expiry reminder sent', [
                'user_id'     => $membership->user_id,
                'plan'        => $membership->plan->name,
                'expires_at'  => $membership->expires_at,
                'days_left'   => $membership->days_remaining,
            ]);
        }

        $this->info("Berhasil mengirim {$count} email pengingat membership.");

        return self::SUCCESS;
    }
}
