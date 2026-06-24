<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlastEmailRequest;
use App\Http\Requests\Admin\StorePromoCodeRequest;
use App\Http\Requests\Admin\UpdatePromoCodeRequest;
use App\Mail\PromoNotificationMail;
use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PromoCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = PromoCode::query();

        if ($search = $request->input('search')) {
            $query->where('code', 'like', "%{$search}%");
        }

        if ($request->input('status') === 'active') {
            $query->valid();
        } elseif ($request->input('status') === 'inactive') {
            $query->where('is_active', false);
        } elseif ($request->input('status') === 'expired') {
            $query->whereNotNull('expires_at')->where('expires_at', '<=', now());
        }

        $promoCodes = $query->latest()->paginate(20);

        return view('admin.promo-codes.index', compact('promoCodes'));
    }

    public function create()
    {
        return view('admin.promo-codes.create');
    }

    public function store(StorePromoCodeRequest $request)
    {
        $data = $request->validated();
        $data['min_purchase'] = $data['min_purchase'] ?? 0;
        $data['is_active']    = $data['is_active'] ?? true;

        PromoCode::create($data);

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Kode promo berhasil dibuat.');
    }

    public function edit(PromoCode $promoCode)
    {
        return view('admin.promo-codes.edit', compact('promoCode'));
    }

    public function update(UpdatePromoCodeRequest $request, PromoCode $promoCode)
    {
        $data = $request->validated();
        $data['min_purchase'] = $data['min_purchase'] ?? 0;

        $promoCode->update($data);

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Kode promo berhasil diperbarui.');
    }

    public function destroy(PromoCode $promoCode)
    {
        $promoCode->delete();

        return back()->with('success', "Kode promo \"{$promoCode->code}\" berhasil dihapus.");
    }

    public function toggleActive(PromoCode $promoCode)
    {
        $promoCode->update(['is_active' => ! $promoCode->is_active]);

        $label = $promoCode->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Kode promo \"{$promoCode->code}\" berhasil {$label}.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /admin/promo-codes/{promoCode}/blast — Form kirim email promo
    // ─────────────────────────────────────────────────────────────────────────

    public function showBlast(PromoCode $promoCode)
    {
        $totalUsers = User::where('role', 'user')
            ->count();

        return view('admin.promo-codes.blast', compact('promoCode', 'totalUsers'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /admin/promo-codes/{promoCode}/blast — Kirim email promo ke semua user
    // ─────────────────────────────────────────────────────────────────────────

    public function blast(BlastEmailRequest $request, PromoCode $promoCode)
    {
        if (! $promoCode->is_valid) {
            return back()->with('error', 'Kode promo ini sudah tidak valid.');
        }

        $customMessage = $request->input('custom_message') ?? '';

        $users = User::where('role', 'user')
            ->select('id', 'name', 'email')
            ->get();

        $count = 0;
        $failed = 0;
        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(
                    new PromoNotificationMail($user, $promoCode, $customMessage)
                );
                $count++;
            } catch (\Exception $e) {
                $failed++;
                Log::warning('Promo blast email failed', [
                    'user_email' => $user->email,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        Log::info('Promo blast sent', [
            'promo_code'     => $promoCode->code,
            'total_sent'     => $count,
            'total_failed'   => $failed,
            'custom_message' => $customMessage,
            'sent_by'        => $request->user()->id,
        ]);

        $message = "Email promo \"{$promoCode->code}\" berhasil dikirim ke {$count} user.";
        if ($failed > 0) {
            $message .= " ({$failed} gagal kirim)";
        }

        return redirect()->route('admin.promo-codes.index')
            ->with('success', $message);
    }
}
