<?php

namespace App\Http\Controllers;

use App\Models\BootcampRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // VERIFY (PUBLIC) — dipanggil saat QR code discan
    // GET /tickets/verify/{ticketCode}
    // ─────────────────────────────────────────────────────────────────────────

    public function verify(string $ticketCode)
    {
        $registration = BootcampRegistration::where('ticket_code', $ticketCode)
            ->with([
                'bootcamp:id,title,slug,thumbnail,type,platform,location,start_date,end_date,status',
                'user:id,name,email,avatar',
            ])
            ->first();

        // Tiket tidak ditemukan
        if (! $registration) {
            return view('tickets.verify', [
                'status'       => 'invalid',
                'message'      => 'Tiket tidak ditemukan. Kode tiket tidak valid.',
                'registration' => null,
            ]);
        }

        // Hanya untuk bootcamp offline
        if ($registration->bootcamp?->type !== 'offline') {
            return view('tickets.verify', [
                'status'       => 'not_applicable',
                'message'      => 'Tiket ini adalah event online. QR scan tidak diperlukan.',
                'registration' => $registration,
            ]);
        }

        // Belum bayar
        if ($registration->payment_status !== 'paid') {
            return view('tickets.verify', [
                'status'       => 'unpaid',
                'message'      => 'Tiket ini belum lunas.',
                'registration' => $registration,
            ]);
        }

        // Sudah check-in sebelumnya
        if ($registration->checked_in) {
            return view('tickets.verify', [
                'status'       => 'already_checked_in',
                'message'      => 'Peserta ini sudah melakukan check-in pada '
                    . $registration->checked_in_at?->translatedFormat('d F Y, H:i') . ' WIB.',
                'registration' => $registration,
            ]);
        }

        // Valid — siap check-in
        return view('tickets.verify', [
            'status'       => 'valid',
            'message'      => 'Tiket valid. Peserta belum melakukan check-in.',
            'registration' => $registration,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CHECK-IN (ADMIN/PANITIA) — POST dari halaman verifikasi
    // POST /tickets/{ticketCode}/checkin
    // ─────────────────────────────────────────────────────────────────────────

    public function checkin(Request $request, string $ticketCode)
    {
        $registration = BootcampRegistration::where('ticket_code', $ticketCode)
            ->with('bootcamp:id,title,type,status', 'user:id,name,email')
            ->firstOrFail();

        // Guard: hanya offline
        abort_if($registration->bootcamp?->type !== 'offline', 400, 'Hanya untuk event offline.');

        // Guard: harus paid
        abort_if($registration->payment_status !== 'paid', 400, 'Tiket belum lunas.');

        // Guard: sudah check-in
        if ($registration->checked_in) {
            return redirect()
                ->route('tickets.verify', $ticketCode)
                ->with('warning', 'Peserta ini sudah check-in sebelumnya.');
        }

        $registration->update([
            'checked_in'    => true,
            'checked_in_at' => now(),
        ]);

        Log::info('Ticket checked in', [
            'ticket_code' => $ticketCode,
            'user_id'     => $registration->user_id,
            'bootcamp_id' => $registration->bootcamp_id,
            'checked_in_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('tickets.verify', $ticketCode)
            ->with('success', 'Check-in berhasil! Peserta ' . $registration->user->name . ' telah tercatat hadir.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DOWNLOAD PDF — Auth user (hanya pemilik tiket)
    // GET /dashboard/tickets/{ticketCode}/download-pdf
    // ─────────────────────────────────────────────────────────────────────────

    public function downloadPdf(Request $request, string $ticketCode)
    {
        $registration = BootcampRegistration::where('ticket_code', $ticketCode)
            ->where('payment_status', 'paid')
            ->with([
                'bootcamp:id,title,slug,thumbnail,type,platform,location,start_date,end_date,status,instructor_id',
                'bootcamp.instructor:id,name',
                'user:id,name,email,avatar',
            ])
            ->firstOrFail();

        // Policy: hanya pemilik tiket atau admin
        $this->authorize('download', $registration);

        // Hanya untuk bootcamp offline
        abort_if($registration->bootcamp?->type !== 'offline', 403, 'Tiket PDF hanya tersedia untuk event offline.');

        $qrImageUrl = $registration->qr_image_url;

        $pdf = Pdf::loadView('pdf.ticket', compact('registration', 'qrImageUrl'))
            ->setPaper('a5', 'portrait');

        $filename = 'Tiket-' . $registration->ticket_code . '.pdf';

        return $pdf->download($filename);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DOWNLOAD QR PNG — Auth user (hanya pemilik tiket)
    // GET /dashboard/tickets/{ticketCode}/download-qr
    // ─────────────────────────────────────────────────────────────────────────

    public function downloadQr(Request $request, string $ticketCode)
    {
        $registration = BootcampRegistration::where('ticket_code', $ticketCode)
            ->where('payment_status', 'paid')
            ->with('bootcamp:id,type')
            ->firstOrFail();

        // Policy: hanya pemilik tiket atau admin
        $this->authorize('download', $registration);

        abort_if($registration->bootcamp?->type !== 'offline', 403, 'QR code hanya untuk event offline.');

        $svg = $registration->generateQrSvg(500);

        return response($svg, 200, [
            'Content-Type'        => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="QR-' . $registration->ticket_code . '.svg"',
        ]);
    }
}
