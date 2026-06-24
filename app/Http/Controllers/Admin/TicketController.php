<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProcessScanRequest;
use App\Models\Bootcamp;
use App\Models\BootcampRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketController extends Controller
{
    /**
     * Halaman scan QR code + absensi per bootcamp
     * GET /admin/tickets/scan?bootcamp_id=
     */
    public function scan(Request $request)
    {
        // Ambil semua bootcamp offline yang masih aktif (upcoming / ongoing)
        $bootcamps = Bootcamp::where('type', 'offline')
            ->where(function ($q) {
                $q->where('status', 'upcoming')
                  ->orWhere('status', 'ongoing');
            })
            ->orderBy('start_date', 'desc')
            ->get();

        // Jika ada bootcamp_id dipilih, load data absensi
        $selectedBootcamp = null;
        $registrations    = collect();
        $checkedInCount   = 0;
        $totalCount       = 0;

        if ($request->filled('bootcamp_id')) {
            $selectedBootcamp = Bootcamp::find($request->bootcamp_id);

            if ($selectedBootcamp) {
                $registrations = $selectedBootcamp->registrations()
                    ->where('payment_status', 'paid')
                    ->with('user')
                    ->orderByDesc('checked_in')        // yang sudah hadir di atas
                    ->orderByDesc('checked_in_at')     // yang terakhir check-in paling atas
                    ->get();

                $checkedInCount = $registrations->where('checked_in', true)->count();
                $totalCount     = $registrations->count();
            }
        }

        return view('admin.tickets.scan', compact(
            'bootcamps',
            'selectedBootcamp',
            'registrations',
            'checkedInCount',
            'totalCount',
        ));
    }

    /**
     * Process scan result - verifikasi dan check-in tiket (absensi)
     * POST /admin/tickets/process-scan
     */
    public function processScan(ProcessScanRequest $request)
    {
        $validated = $request->validated();

        $ticket = BootcampRegistration::with(['bootcamp', 'user'])
            ->where('ticket_code', $validated['ticket_code'])
            ->first();

        // Tiket tidak ditemukan
        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan dalam sistem.',
            ], 404);
        }

        // Bootcamp bukan offline
        if ($ticket->bootcamp->type !== 'offline') {
            return response()->json([
                'success' => false,
                'message' => 'Tiket ini bukan untuk event offline.',
            ], 400);
        }

        // Tiket belum dibayar
        if ($ticket->payment_status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Tiket belum lunas, tidak bisa absensi.',
            ], 400);
        }

        // Bootcamp sudah selesai atau dibatalkan
        if ($ticket->bootcamp->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Event telah dibatalkan.',
            ], 400);
        }

        if ($ticket->bootcamp->status === 'completed' || $ticket->bootcamp->end_date < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Event sudah selesai.',
            ], 400);
        }

        // Sudah check-in / sudah absen
        if ($ticket->checked_in) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta sudah absen pada ' . $ticket->checked_in_at->format('d M Y H:i'),
                'status'  => 'already_checked_in',
                'ticket'  => [
                    'user_name'     => $ticket->user->name,
                    'user_email'    => $ticket->user->email,
                    'bootcamp_name' => $ticket->bootcamp->title,
                    'ticket_code'   => $ticket->ticket_code,
                    'checked_in_at' => $ticket->checked_in_at->format('d M Y H:i'),
                ],
            ], 409);
        }

        // ── VALID → Absensi berhasil ─────────────────────────────────────────
        $ticket->update([
            'checked_in'    => true,
            'checked_in_at' => now(),
        ]);

        // Hitung ulang statistik bootcamp ini
        $bootcamp       = $ticket->bootcamp;
        $totalCount     = $bootcamp->registrations()->where('payment_status', 'paid')->count();
        $checkedInCount = $bootcamp->registrations()->where('payment_status', 'paid')->where('checked_in', true)->count();

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat!',
            'status'  => 'checked_in',
            'ticket'  => [
                'user_name'     => $ticket->user->name,
                'user_email'    => $ticket->user->email,
                'user_avatar'   => avatarUrl($ticket->user),
                'bootcamp_name' => $bootcamp->title,
                'bootcamp_id'   => $bootcamp->id,
                'bootcamp_date' => $bootcamp->start_date->format('d M Y'),
                'ticket_code'   => $ticket->ticket_code,
                'checked_in_at' => $ticket->checked_in_at->format('d M Y H:i'),
            ],
            'stats' => [
                'checked_in' => $checkedInCount,
                'total'      => $totalCount,
                'percentage' => $totalCount > 0 ? round(($checkedInCount / $totalCount) * 100) : 0,
            ],
        ], 200);
    }

    /**
     * Daftar bootcamp offline untuk dipilih absensi
     * GET /admin/tickets
     */
    public function index()
    {
        $bootcamps = Bootcamp::where('type', 'offline')
            ->where(function ($query) {
                $query->where('status', 'upcoming')
                    ->orWhere('status', 'ongoing');
            })
            ->with(['registrations' => function ($query) {
                $query->where('payment_status', 'paid');
            }])
            ->latest('start_date')
            ->get();

        return view('admin.tickets.index', compact('bootcamps'));
    }

    /**
     * Detail absensi peserta per bootcamp
     * GET /admin/tickets/{bootcamp}
     */
    public function showBootcamp($bootcampId)
    {
        $bootcamp = Bootcamp::findOrFail($bootcampId);

        $registrations = $bootcamp->registrations()
            ->where('payment_status', 'paid')
            ->with('user')
            ->orderByDesc('checked_in')
            ->orderByDesc('checked_in_at')
            ->get();

        $checkedInCount = $registrations->where('checked_in', true)->count();
        $totalCount     = $registrations->count();

        return view('admin.tickets.show-bootcamp', compact(
            'bootcamp',
            'registrations',
            'checkedInCount',
            'totalCount',
        ));
    }

    /**
     * API: Ambil data absensi real-time untuk satu bootcamp (JSON)
     * GET /admin/tickets/{bootcamp}/attendance-data
     */
    public function attendanceData($bootcampId)
    {
        $bootcamp = Bootcamp::findOrFail($bootcampId);

        $registrations = $bootcamp->registrations()
            ->where('payment_status', 'paid')
            ->with('user')
            ->orderByDesc('checked_in')
            ->orderByDesc('checked_in_at')
            ->get();

        $checkedInCount = $registrations->where('checked_in', true)->count();
        $totalCount     = $registrations->count();

        return response()->json([
            'stats' => [
                'checked_in' => $checkedInCount,
                'total'      => $totalCount,
                'percentage' => $totalCount > 0 ? round(($checkedInCount / $totalCount) * 100) : 0,
            ],
            'registrations' => $registrations->map(fn ($r) => [
                'id'             => $r->id,
                'user_name'      => $r->user->name,
                'user_email'     => $r->user->email,
                'user_avatar'    => avatarUrl($r->user),
                'ticket_code'    => $r->ticket_code,
                'checked_in'     => $r->checked_in,
                'checked_in_at'  => $r->checked_in ? $r->checked_in_at->format('H:i') : null,
                'checked_in_full' => $r->checked_in ? $r->checked_in_at->format('d M Y H:i') : null,
            ])->values(),
        ]);
    }

    /**
     * Export daftar hadir ke PDF
     * GET /admin/tickets/{bootcamp}/export-pdf
     */
    public function exportPdf($bootcampId)
    {
        $bootcamp = Bootcamp::findOrFail($bootcampId);

        $registrations = $bootcamp->registrations()
            ->where('payment_status', 'paid')
            ->with('user')
            ->orderBy('checked_in', 'desc')
            ->orderBy('checked_in_at', 'asc')
            ->get();

        $checkedInCount = $registrations->where('checked_in', true)->count();
        $totalCount     = $registrations->count();

        $pdf = Pdf::loadView('pdf.attendance', compact(
            'bootcamp',
            'registrations',
            'checkedInCount',
            'totalCount',
        ))->setPaper('a4', 'portrait');

        $filename = 'Absensi-' . \Str::slug($bootcamp->title) . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export daftar hadir ke Excel (CSV)
     * GET /admin/tickets/{bootcamp}/export-excel
     */
    public function exportExcel($bootcampId): StreamedResponse
    {
        $bootcamp = Bootcamp::findOrFail($bootcampId);

        $registrations = $bootcamp->registrations()
            ->where('payment_status', 'paid')
            ->with('user')
            ->orderBy('checked_in', 'desc')
            ->orderBy('checked_in_at', 'asc')
            ->get();

        $filename = 'Absensi-' . \Str::slug($bootcamp->title) . '-' . now()->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($bootcamp, $registrations) {
            $handle = fopen('php://output', 'w');

            // BOM for UTF-8 agar Excel bisa baca karakter Indonesia
            fwrite($handle, "\xEF\xBB\xBF");

            // Header info
            fputcsv($handle, ['Daftar Hadir Peserta']);
            fputcsv($handle, ['Program', $bootcamp->title]);
            fputcsv($handle, ['Tanggal', $bootcamp->start_date->translatedFormat('d F Y')]);
            fputcsv($handle, ['Lokasi', $bootcamp->location ?? '-']);
            fputcsv($handle, ['Diekspor', now()->translatedFormat('d F Y H:i')]);
            fputcsv($handle, []); // blank row

            // Column headers
            fputcsv($handle, [
                'No',
                'Nama Peserta',
                'Email',
                'Kode Tiket',
                'Status Kehadiran',
                'Jam Hadir',
            ]);

            // Data rows
            foreach ($registrations as $idx => $reg) {
                fputcsv($handle, [
                    $idx + 1,
                    $reg->user->name,
                    $reg->user->email,
                    $reg->ticket_code,
                    $reg->checked_in ? 'Hadir' : 'Belum Hadir',
                    $reg->checked_in ? $reg->checked_in_at->format('H:i') : '-',
                ]);
            }

            // Summary
            $checkedIn = $registrations->where('checked_in', true)->count();
            $total     = $registrations->count();
            fputcsv($handle, []);
            fputcsv($handle, ['', '', '', 'Total Peserta', $total]);
            fputcsv($handle, ['', '', '', 'Hadir', $checkedIn]);
            fputcsv($handle, ['', '', '', 'Belum Hadir', $total - $checkedIn]);
            fputcsv($handle, ['', '', '', 'Persentase', ($total > 0 ? round(($checkedIn / $total) * 100) : 0) . '%']);

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
