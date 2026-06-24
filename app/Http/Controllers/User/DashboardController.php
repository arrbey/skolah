<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookOrder;
use App\Models\BootcampRegistration;
use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\LessonProgress;
use App\Models\Order;
use App\Services\MembershipService;
use App\Services\MidtransService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected MembershipService $membershipService,
        protected MidtransService   $midtransService,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — /dashboard  (overview)
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = $request->user();

        // ── Stats ringkas ────────────────────────────────────────────────
        $totalCourses     = CourseEnrollment::forUser($user->id)->count();
        $completedCourses = CourseEnrollment::forUser($user->id)->completed()->count();
        $totalBootcamps   = BootcampRegistration::forUser($user->id)->paid()->count();
        $totalCertificates = Certificate::forUser($user->id)->count();

        // ── Course aktif (belum selesai, 5 terakhir) ─────────────────────
        $activeCourses = CourseEnrollment::forUser($user->id)
            ->whereNull('completed_at')
            ->with('course:id,title,slug,thumbnail,instructor_id', 'course.instructor:id,name')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        // ── Bootcamp upcoming ────────────────────────────────────────────
        $upcomingBootcamps = BootcampRegistration::forUser($user->id)
            ->paid()
            ->whereHas('bootcamp', fn ($q) => $q->where('start_date', '>', now()))
            ->with('bootcamp:id,title,slug,thumbnail,start_date,type,platform')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // ── Membership status ────────────────────────────────────────────
        $activeMembership = $this->membershipService->getActiveMembership($user->id);

        // ── Order terbaru ────────────────────────────────────────────────
        $recentOrders = Order::forUser($user->id)
            ->with('items')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // ── Data Grafik Mingguan (7 Hari Terakhir) ───────────────────────
        $chartData = $this->getWeeklyChartData($user->id);

        // ── Logika Lencana (Badges) ──────────────────────────────────────
        $badges = $this->getUserBadges($user, $totalCourses, $completedCourses, $totalBootcamps);

        return view('dashboard.index', compact(
            'user',
            'totalCourses',
            'completedCourses',
            'totalBootcamps',
            'totalCertificates',
            'activeCourses',
            'upcomingBootcamps',
            'activeMembership',
            'recentOrders',
            'chartData',
            'badges'
        ));
    }

    /**
     * Ambil data jumlah pelajaran yang selesai 7 hari terakhir.
     */
    protected function getWeeklyChartData(int $userId): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->translatedFormat('D'); // Nama hari singkat (Sen, Sel, dst)
            
            $count = LessonProgress::where('user_id', $userId)
                ->where('is_completed', true)
                ->whereDate('watched_at', $date->toDateString())
                ->count();
            
            $data[] = $count;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Tentukan lencana yang didapat user secara dinamis.
     */
    protected function getUserBadges($user, $totalCourses, $completedCourses, $totalBootcamps): array
    {
        $allBadges = [
            ['id' => 'newbie',    'name' => 'Langkah Pertama', 'desc' => 'Mulai belajar 1 kursus', 'icon' => '🌱', 'unlocked' => $totalCourses >= 1],
            ['id' => 'scholar',   'name' => 'Si Kutu Buku',    'desc' => 'Selesaikan 1 kursus',   'icon' => '📚', 'unlocked' => $completedCourses >= 1],
            ['id' => 'warrior',   'name' => 'Pejuang Bootcamp','desc' => 'Daftar 1 bootcamp',    'icon' => '🔥', 'unlocked' => $totalBootcamps >= 1],
            ['id' => 'persistent','name' => 'Pantang Menyerah','desc' => 'Belajar 3 hari berturut-turut', 'icon' => '⚡', 'unlocked' => $this->calculateStreak($user->id) >= 3],
        ];

        return $allBadges;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MY COURSES — /dashboard/my-courses
    // ─────────────────────────────────────────────────────────────────────────

    public function myCourses(Request $request)
    {
        $user   = $request->user();
        $filter = $request->get('filter', 'all'); // all | in-progress | completed

        $query = CourseEnrollment::forUser($user->id)
            ->with('course:id,title,slug,thumbnail,instructor_id,level,rating,rating_count', 'course.instructor:id,name');

        $query = match ($filter) {
            'in-progress' => $query->inProgress(),
            'completed'   => $query->completed(),
            default       => $query,
        };

        $enrollments = $query->orderByDesc('enrolled_at')->paginate(12);

        $stats = [
            'all'         => CourseEnrollment::forUser($user->id)->count(),
            'in-progress' => CourseEnrollment::forUser($user->id)->inProgress()->count(),
            'completed'   => CourseEnrollment::forUser($user->id)->completed()->count(),
            'not-started' => CourseEnrollment::forUser($user->id)->notStarted()->count(),
        ];

        // ── Learning Streak (consecutive days with lessons completed) ─────────
        $streakDays = $this->calculateStreak($user->id);

        // ── Total pelajaran selesai bulan ini ─────────────────────────────────
        $lessonsThisMonth = LessonProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->where('watched_at', '>=', now()->startOfMonth())
            ->count();

        // ── Rata-rata progress semua kursus aktif ─────────────────────────────
        $avgProgress = CourseEnrollment::forUser($user->id)->inProgress()->avg('progress_percentage') ?? 0;

        return view('dashboard.my-courses', compact(
            'enrollments', 'filter', 'stats',
            'streakDays', 'lessonsThisMonth', 'avgProgress'
        ));
    }

    /**
     * Hitung berapa hari berturut-turut user belajar (streak).
     */
    protected function calculateStreak(int $userId): int
    {
        $dates = LessonProgress::where('user_id', $userId)
            ->where('is_completed', true)
            ->whereNotNull('watched_at')
            ->selectRaw('DATE(watched_at) as learn_date')
            ->distinct()
            ->orderByDesc('learn_date')
            ->pluck('learn_date');

        if ($dates->isEmpty()) {
            return 0;
        }

        $streak  = 0;
        $checkDay = now()->toDateString();

        foreach ($dates as $date) {
            if ($date === $checkDay) {
                $streak++;
                $checkDay = now()->subDays($streak)->toDateString();
            } else {
                break;
            }
        }

        // Jika hari ini belum belajar, cek mulai dari kemarin
        if ($streak === 0 && $dates->first() === now()->subDay()->toDateString()) {
            $checkDay = now()->subDay()->toDateString();
            foreach ($dates as $date) {
                if ($date === $checkDay) {
                    $streak++;
                    $checkDay = now()->subDays($streak + 1)->toDateString();
                } else {
                    break;
                }
            }
        }

        return $streak;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MY BOOTCAMPS — /dashboard/my-bootcamps
    // ─────────────────────────────────────────────────────────────────────────

    public function myBootcamps(Request $request)
    {
        $user   = $request->user();
        $filter = $request->get('filter', 'all'); // all | upcoming | completed

        $query = BootcampRegistration::forUser($user->id)
            ->paid()
            ->with('bootcamp:id,title,slug,thumbnail,start_date,end_date,type,platform,location,status,instructor_id', 'bootcamp.instructor:id,name');

        $query = match ($filter) {
            'upcoming'  => $query->whereHas('bootcamp', fn ($q) => $q->whereIn('status', ['upcoming', 'ongoing'])),
            'completed' => $query->whereHas('bootcamp', fn ($q) => $q->where('status', 'completed')),
            default     => $query,
        };

        $registrations = $query->orderByDesc('registered_at')->paginate(12);

        $stats = [
            'all'       => BootcampRegistration::forUser($user->id)->paid()->count(),
            'upcoming'  => BootcampRegistration::forUser($user->id)->paid()
                ->whereHas('bootcamp', fn ($q) => $q->whereIn('status', ['upcoming', 'ongoing']))->count(),
            'completed' => BootcampRegistration::forUser($user->id)->paid()
                ->whereHas('bootcamp', fn ($q) => $q->where('status', 'completed'))->count(),
        ];

        return view('dashboard.my-bootcamps', compact('registrations', 'filter', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MY BOOKS — /dashboard/my-books
    // ─────────────────────────────────────────────────────────────────────────

    public function myBooks(Request $request)
    {
        $user   = $request->user();
        $filter = $request->get('filter', 'all'); // all | digital | physical

        $query = BookOrder::forUser($user->id)
            ->whereHas('order', fn ($q) => $q->where('status', 'paid'))
            ->with('book:id,title,slug,cover_image,author,type,file_path,pages', 'book.instructor:id,name');

        $query = match ($filter) {
            'digital'  => $query->where('purchase_type', 'digital'),
            'physical' => $query->where('purchase_type', 'physical'),
            default    => $query,
        };

        $bookOrders = $query->orderByDesc('created_at')->paginate(12);

        $stats = [
            'all'      => BookOrder::forUser($user->id)->whereHas('order', fn ($q) => $q->where('status', 'paid'))->count(),
            'digital'  => BookOrder::forUser($user->id)->whereHas('order', fn ($q) => $q->where('status', 'paid'))->where('purchase_type', 'digital')->count(),
            'physical' => BookOrder::forUser($user->id)->whereHas('order', fn ($q) => $q->where('status', 'paid'))->where('purchase_type', 'physical')->count(),
        ];

        return view('dashboard.my-books', compact('bookOrders', 'filter', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MY BOOK DETAIL — /dashboard/my-books/{bookOrderId}
    // ─────────────────────────────────────────────────────────────────────────

    public function myBookDetail(Request $request, int $bookOrderId)
    {
        $user = $request->user();

        $bookOrder = BookOrder::forUser($user->id)
            ->whereHas('order', fn ($q) => $q->where('status', 'paid'))
            ->with([
                'book:id,title,slug,cover_image,author,type,file_path,pages,description,isbn,publisher',
                'book.instructor:id,name,avatar',
                'order:id,order_number,paid_at,total',
                'histories.actor:id,name',
            ])
            ->findOrFail($bookOrderId);

        return view('dashboard.my-book-detail', compact('bookOrder'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MY BOOTCAMP DETAIL — /dashboard/my-bootcamps/{ticketCode}
    // ─────────────────────────────────────────────────────────────────────────

    public function myBootcampDetail(Request $request, string $ticketCode)
    {
        $user = $request->user();

        $registration = BootcampRegistration::forUser($user->id)
            ->where('ticket_code', $ticketCode)
            ->with([
                'bootcamp:id,title,slug,description,thumbnail,start_date,end_date,type,platform,meeting_link,location,status,price,discount_price,max_participants,total_registered,instructor_id',
                'bootcamp.instructor:id,name,avatar',
            ])
            ->firstOrFail();

        abort_if($registration->payment_status !== 'paid', 403, 'Tiket belum lunas.');

        $bootcamp = $registration->bootcamp;

        // QR Code content — berisi ticket_code + nama user + bootcamp slug
        $qrContent = implode('|', [
            $registration->ticket_code,
            $bootcamp->slug,
            $user->id,
        ]);
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($qrContent);

        return view('dashboard.my-bootcamp-detail', compact(
            'registration',
            'bootcamp',
            'qrUrl',
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CERTIFICATES — /dashboard/certificates
    // ─────────────────────────────────────────────────────────────────────────

    public function certificates(Request $request)
    {
        $certificates = Certificate::forUser($request->user()->id)
            ->with('course:id,title,slug,thumbnail,instructor_id', 'course.instructor:id,name')
            ->orderByDesc('issued_at')
            ->paginate(12);

        return view('dashboard.certificates', compact('certificates'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ORDERS — /dashboard/orders
    // ─────────────────────────────────────────────────────────────────────────

    public function orders(Request $request)
    {
        $user   = $request->user();
        $filter = $request->get('filter', 'all'); // all | paid | pending | failed

        $query = Order::forUser($user->id)->with('items');

        $query = match ($filter) {
            'paid'    => $query->paid(),
            'pending' => $query->pending(),
            'failed'  => $query->failed(),
            default   => $query,
        };

        $orders = $query->orderByDesc('created_at')->paginate(15);

        $stats = [
            'all'     => Order::forUser($user->id)->count(),
            'paid'    => Order::forUser($user->id)->paid()->count(),
            'pending' => Order::forUser($user->id)->pending()->count(),
            'failed'  => Order::forUser($user->id)->failed()->count(),
        ];

        return view('dashboard.orders', compact('orders', 'filter', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PAY ORDER — /dashboard/orders/{order}/pay  (lanjutkan pembayaran)
    // ─────────────────────────────────────────────────────────────────────────

    public function payOrder(Request $request, Order $order)
    {
        // Policy: hanya pemilik order atau admin
        $this->authorize('view', $order);

        // Guard: hanya order pending yang bisa dibayar
        if ($order->status !== 'pending') {
            return redirect()->route('dashboard.orders')
                ->with('info', 'Order ini sudah tidak bisa dibayar.');
        }

        // Guard: cek apakah order sudah kedaluwarsa
        if ($order->is_expired) {
            $order->update(['status' => 'failed']);
            return redirect()->route('dashboard.orders')
                ->with('error', 'Batas waktu pembayaran telah habis. Order dibatalkan.');
        }

        // Selalu generate snap redirect URL baru untuk memastikan valid
        try {
            $redirectUrl = $this->midtransService->createSnapToken($order);
            
            // REDIRECT MODE: Langsung arahkan ke halaman pembayaran aman Midtrans
            return redirect()->away($redirectUrl);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Pay order: Redirect URL generation failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);

            return redirect()->route('dashboard.orders')
                ->with('error', 'Gagal memproses pembayaran. Silakan coba lagi.');
        }
    }

    public function finishOnboarding(Request $request)
    {
        $user = $request->user();
        $page = $request->input('page');

        if ($page) {
            $seen = $user->seen_onboarding_pages ?? [];
            if (!in_array($page, $seen)) {
                $seen[] = $page;
                $user->update(['seen_onboarding_pages' => $seen]);
            }
        } else {
            // Fallback: tandai semua selesai jika tidak ada page spesifik
            $user->update(['has_seen_onboarding' => true]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Download invoice PDF.
     */
    public function downloadInvoice(Request $request, Order $order)
    {
        // Pastikan hanya pemilik yang bisa download
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        // Hanya order yang sudah lunas yang bisa download invoice resmi
        if ($order->status !== 'paid') {
            return back()->with('error', 'Invoice hanya tersedia untuk pesanan yang sudah lunas.');
        }

        $order->load(['user', 'items']);

        $pdf = Pdf::loadView('invoices.order-invoice', compact('order'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice-' . $order->reference . '.pdf');
    }
}
