<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookCheckoutController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BootcampCheckoutController;
use App\Http\Controllers\BootcampController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\LearnController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\InstructorApplicationController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\SettingsController;
use App\Http\Controllers\User\QuizController as UserQuizController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Instructor\LessonController as InstructorLessonController;
use App\Http\Controllers\Instructor\BootcampController as InstructorBootcampController;
use App\Http\Controllers\Instructor\BookController as InstructorBookController;
use App\Http\Controllers\Instructor\BookOrderController as InstructorBookOrderController;
use App\Http\Controllers\Instructor\EarningController as InstructorEarningController;
use App\Http\Controllers\Instructor\QuizController as InstructorQuizController;
use App\Http\Controllers\Instructor\CourseVariantController as InstructorCourseVariantController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\CourseEnrollmentController as AdminCourseEnrollmentController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\BookOrderController as AdminBookOrderController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Admin\CertificateTemplateController as AdminCertificateTemplateController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\PromoCodeController as AdminPromoCodeController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\MembershipController as AdminMembershipController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\InstructorApplicationController as AdminInstructorApplicationController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\BootcampController as AdminBootcampController;
use App\Http\Controllers\Admin\BenefitController as AdminBenefitController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\LandingProgramController as AdminLandingProgramController;
use App\Http\Controllers\Admin\CampusController as AdminCampusController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\BackupController as AdminBackupController;
use App\Http\Controllers\Admin\InstitutionController as AdminInstitutionController;
use App\Http\Controllers\Admin\InstructorController as AdminInstructorController;
use App\Http\Controllers\Admin\BundleController as AdminBundleController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\BundleController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Midtrans Webhook moved to api.php

// ── Homepage ───────────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ── Sitemap ────────────────────────────────────────────────────────────────────
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// ── Auth Routes (Guest only) ───────────────────────────────────────────────────
Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.post');

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware(['throttle:register', \Spatie\Honeypot\ProtectAgainstSpam::class])
        ->name('register.post');

    // Forgot Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
        ->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
        ->middleware('throttle:forgot-password')
        ->name('password.email');

    // Reset Password
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
        ->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:forgot-password')
        ->name('password.update');

    // Social Login (Google)
    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])
        ->where('provider', 'google')
        ->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->where('provider', 'google')
        ->name('social.callback');

});

// Forced Password Change (must be authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/auth/force-password-change', [AuthController::class, 'showForcePasswordChange'])
        ->name('auth.force-password-change')->withoutMiddleware([\App\Http\Middleware\ForcePasswordChange::class]);
    Route::post('/auth/force-password-change', [AuthController::class, 'updateForcePasswordChange'])
        ->middleware('throttle:forgot-password')
        ->name('auth.force-password-change.post')->withoutMiddleware([\App\Http\Middleware\ForcePasswordChange::class]);
});

// ── Logout (Auth only) ─────────────────────────────────────────────────────────
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Email Verification ─────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    // Halaman pemberitahuan "cek email Anda"
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // Kirim ulang email verifikasi
    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Link verifikasi baru telah dikirim ke email Anda.');
    })->middleware('throttle:6,1')->name('verification.send');
});

// Link verifikasi dari email — bisa diakses tanpa login (auto-login setelah verify)
Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Http\Request $request, $id, $hash) {
    // Validasi signature URL
    if (! \Illuminate\Support\Facades\URL::hasValidSignature($request)) {
        return redirect()->route('login')
            ->with('error', 'Link verifikasi tidak valid atau sudah kadaluarsa. Silakan login dan kirim ulang.');
    }

    $user = \App\Models\User::findOrFail($id);

    // Validasi hash email
    if (! hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
        return redirect()->route('login')
            ->with('error', 'Link verifikasi tidak valid.');
    }

    // Jika sudah terverifikasi
    if ($user->hasVerifiedEmail()) {
        // Auto-login jika belum login
        if (! \Illuminate\Support\Facades\Auth::check()) {
            \Illuminate\Support\Facades\Auth::login($user);
        }
        return redirect()->route('dashboard')
            ->with('success', 'Email Anda sudah terverifikasi sebelumnya. Selamat datang!');
    }

    // Verifikasi email
    $user->markEmailAsVerified();
    event(new \Illuminate\Auth\Events\Verified($user));

    // Auto-login user
    \Illuminate\Support\Facades\Auth::login($user);

    return redirect()->route('dashboard')
        ->with('success', '🎉 Email berhasil diverifikasi! Selamat datang di Skolah.com, ' . $user->name . '!');
})->middleware('signed')->name('verification.verify');

// ── User Dashboard ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:user|instructor|admin'])
    ->prefix('dashboard')
    ->name('dashboard')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->name('');  // → route('dashboard')

        Route::post('/onboarding/finish', [DashboardController::class, 'finishOnboarding'])
            ->name('.onboarding.finish');

        Route::get('/my-courses', [DashboardController::class, 'myCourses'])
            ->name('.my-courses');

        Route::get('/my-bootcamps', [DashboardController::class, 'myBootcamps'])
            ->name('.my-bootcamps');

        Route::get('/my-bootcamps/{ticketCode}', [DashboardController::class, 'myBootcampDetail'])
            ->name('.my-bootcamp-detail');

        Route::get('/my-books', [DashboardController::class, 'myBooks'])
            ->name('.my-books');

        Route::get('/my-books/{bookOrderId}', [DashboardController::class, 'myBookDetail'])
            ->name('.my-book-detail');

        Route::get('/certificates', [DashboardController::class, 'certificates'])
            ->name('.certificates');

        Route::get('/membership', [MembershipController::class, 'dashboard'])
            ->name('.membership');

        Route::post('/membership/cancel', [MembershipController::class, 'cancel'])
            ->name('.membership.cancel');

        Route::get('/orders', [DashboardController::class, 'orders'])
            ->name('.orders');

        Route::get('/orders/{order}/pay', [DashboardController::class, 'payOrder'])
            ->name('.orders.pay');
        
        Route::get('/orders/{order}/invoice', [DashboardController::class, 'downloadInvoice'])
            ->name('.orders.invoice');

        // Become Instructor
        Route::get('/become-instructor', [InstructorApplicationController::class, 'index'])
            ->name('.become-instructor');
        Route::post('/become-instructor', [InstructorApplicationController::class, 'store'])
            ->name('.become-instructor.store');

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])
            ->name('.settings');
        Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])
            ->name('.settings.profile');
        Route::post('/settings/password', [SettingsController::class, 'updatePassword'])
            ->middleware('throttle:forgot-password')
            ->name('.settings.password');
        Route::post('/settings/avatar', [SettingsController::class, 'updateAvatar'])
            ->middleware('throttle:upload')
            ->name('.settings.avatar');
        Route::delete('/settings/avatar', [SettingsController::class, 'deleteAvatar'])
            ->name('.settings.avatar.delete');

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])
            ->name('.notifications');
        Route::get('/notifications/{id}/read', [NotificationController::class, 'markRead'])
            ->name('.notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])
            ->name('.notifications.mark-all-read');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])
            ->name('.notifications.destroy');

        // Chat System inside Dashboard
        Route::get('/chat/{receiverId?}', [ChatController::class, 'index'])->name('.chat');
        Route::post('/chat/send', [ChatController::class, 'sendMessage'])
            ->middleware('throttle:chat')
            ->name('.chat.send');

        // Chat API for Floating Chat
        Route::get('/api/chat/users', [ChatController::class, 'getApiUsers'])->name('.api.chat.users');
        Route::get('/api/chat/messages/{receiverId}', [ChatController::class, 'getApiMessages'])->name('.api.chat.messages');
    });



// ── Instructor Panel ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:instructor|admin', 'audit'])
    ->prefix('instructor')
    ->name('instructor.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [InstructorDashboardController::class, 'index'])
            ->name('dashboard');

        // Courses CRUD
        Route::get('/courses', [InstructorCourseController::class, 'index'])
            ->name('courses.index');
        Route::get('/courses/create', [InstructorCourseController::class, 'create'])
            ->name('courses.create');
        Route::post('/courses', [InstructorCourseController::class, 'store'])
            ->middleware('throttle:upload')
            ->name('courses.store');
        Route::get('/courses/{course}/edit', [InstructorCourseController::class, 'edit'])
            ->name('courses.edit');
        Route::put('/courses/{course}', [InstructorCourseController::class, 'update'])
            ->middleware('throttle:upload')
            ->name('courses.update');
        Route::delete('/courses/{course}', [InstructorCourseController::class, 'destroy'])
            ->name('courses.destroy');
        Route::get('/courses/{course}/students', [InstructorCourseController::class, 'students'])
            ->name('courses.students');

        // Course Variants
        Route::get('/courses/{course}/variants', [InstructorCourseVariantController::class, 'index'])
            ->name('courses.variants.index');
        Route::post('/courses/{course}/variants', [InstructorCourseVariantController::class, 'store'])
            ->name('courses.variants.store');
        Route::put('/courses/{course}/variants/{variant}', [InstructorCourseVariantController::class, 'update'])
            ->name('courses.variants.update');
        Route::delete('/courses/{course}/variants/{variant}', [InstructorCourseVariantController::class, 'destroy'])
            ->name('courses.variants.destroy');

        // Lessons Management
        Route::get('/courses/{course}/lessons', [InstructorLessonController::class, 'index'])
            ->name('courses.lessons');

        // Sections
        Route::post('/courses/{course}/sections', [InstructorLessonController::class, 'storeSection'])
            ->name('courses.sections.store');
        Route::put('/courses/{course}/sections/{section}', [InstructorLessonController::class, 'updateSection'])
            ->name('courses.sections.update');
        Route::delete('/courses/{course}/sections/{section}', [InstructorLessonController::class, 'destroySection'])
            ->name('courses.sections.destroy');
        Route::post('/courses/{course}/sections/reorder', [InstructorLessonController::class, 'reorderSections'])
            ->name('courses.sections.reorder');

        // Lessons
        Route::post('/courses/{course}/sections/{section}/lessons', [InstructorLessonController::class, 'storeLesson'])
            ->middleware('throttle:upload')
            ->name('courses.lessons.store');
        Route::put('/courses/{course}/lessons/{lesson}', [InstructorLessonController::class, 'updateLesson'])
            ->middleware('throttle:upload')
            ->name('courses.lessons.update');
        Route::delete('/courses/{course}/lessons/{lesson}', [InstructorLessonController::class, 'destroyLesson'])
            ->name('courses.lessons.destroy');
        Route::post('/courses/{course}/sections/{section}/lessons/reorder', [InstructorLessonController::class, 'reorderLessons'])
            ->name('courses.lessons.reorder');

        // Bootcamps CRUD
        Route::get('/bootcamps', [InstructorBootcampController::class, 'index'])
            ->name('bootcamps.index');
        Route::get('/bootcamps/create', [InstructorBootcampController::class, 'create'])
            ->name('bootcamps.create');
        Route::post('/bootcamps', [InstructorBootcampController::class, 'store'])
            ->middleware('throttle:upload')
            ->name('bootcamps.store');
        Route::get('/bootcamps/{bootcamp}/edit', [InstructorBootcampController::class, 'edit'])
            ->name('bootcamps.edit');
        Route::put('/bootcamps/{bootcamp}', [InstructorBootcampController::class, 'update'])
            ->middleware('throttle:upload')
            ->name('bootcamps.update');
        Route::delete('/bootcamps/{bootcamp}', [InstructorBootcampController::class, 'destroy'])
            ->name('bootcamps.destroy');
        Route::get('/bootcamps/{bootcamp}/registrations', [InstructorBootcampController::class, 'registrations'])
            ->name('bootcamps.registrations');

        // Books CRUD
        Route::get('/books', [InstructorBookController::class, 'index'])
            ->name('books.index');
        Route::get('/books/create', [InstructorBookController::class, 'create'])
            ->name('books.create');
        Route::post('/books', [InstructorBookController::class, 'store'])
            ->middleware('throttle:upload')
            ->name('books.store');
        Route::get('/books/{book}/edit', [InstructorBookController::class, 'edit'])
            ->name('books.edit');
        Route::put('/books/{book}', [InstructorBookController::class, 'update'])
            ->middleware('throttle:upload')
            ->name('books.update');
        Route::delete('/books/{book}', [InstructorBookController::class, 'destroy'])
            ->name('books.destroy');
        Route::get('/books/{book}/orders', [InstructorBookController::class, 'orders'])
            ->name('books.orders');

        // Book Orders — monitoring pengiriman buku fisik
        Route::get('/book-orders', [InstructorBookOrderController::class, 'index'])
            ->name('book-orders.index');
        Route::get('/book-orders/{bookOrder}', [InstructorBookOrderController::class, 'show'])
            ->name('book-orders.show');
        Route::patch('/book-orders/{bookOrder}/update-status', [InstructorBookOrderController::class, 'updateStatus'])
            ->name('book-orders.update-status');
        Route::post('/book-orders/{bookOrder}/confirm-delivery', [InstructorBookOrderController::class, 'confirmDelivery'])
            ->name('book-orders.confirm-delivery');

        // Earnings
        Route::get('/earnings', [InstructorEarningController::class, 'index'])
            ->name('earnings');

        // ── Quiz (Pretest & Posttest) ──────────────────────────────────────
        Route::prefix('courses/{course}/quizzes')->name('courses.quizzes.')->group(function () {
            Route::get('/', [InstructorQuizController::class, 'index'])
                ->name('index');
            Route::get('/create', [InstructorQuizController::class, 'create'])
                ->name('create');
            Route::post('/', [InstructorQuizController::class, 'store'])
                ->name('store');
            Route::get('/{quiz}/edit', [InstructorQuizController::class, 'edit'])
                ->name('edit');
            Route::put('/{quiz}', [InstructorQuizController::class, 'update'])
                ->name('update');
            Route::delete('/{quiz}', [InstructorQuizController::class, 'destroy'])
                ->name('destroy');
            Route::get('/{quiz}/questions', [InstructorQuizController::class, 'questions'])
                ->name('questions');
            Route::post('/{quiz}/questions', [InstructorQuizController::class, 'storeQuestion'])
                ->name('questions.store');
            Route::put('/{quiz}/questions/{question}', [InstructorQuizController::class, 'updateQuestion'])
                ->name('questions.update');
            Route::delete('/{quiz}/questions/{question}', [InstructorQuizController::class, 'destroyQuestion'])
                ->name('questions.destroy');
            Route::get('/{quiz}/results', [InstructorQuizController::class, 'results'])
                ->name('results');

            // Import Aiken Format (bulk import soal pilihan ganda)
            Route::get('/{quiz}/import', [InstructorQuizController::class, 'showImport'])
                ->name('import');
            Route::post('/{quiz}/import', [InstructorQuizController::class, 'import'])
                ->name('import.store');
        });
    });

// ── Admin Panel ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:admin', 'audit', 'admin_idle'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::post('/users/import', [AdminUserController::class, 'import'])->name('users.import');
        Route::get('/users/template', [AdminUserController::class, 'downloadTemplate'])->name('users.template');
        Route::patch('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
        Route::patch('/users/{user}/activate', [AdminUserController::class, 'activate'])->name('users.activate');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::get('/users/export', [AdminUserController::class, 'export'])->name('users.export');
        Route::patch('/users/{user}/verify', [AdminUserController::class, 'verifyManual'])->name('users.verify');

        // Courses
        Route::resource('/courses', AdminCourseController::class);
        Route::resource('/bundles', AdminBundleController::class);
        Route::patch('/courses/{course}/approve', [AdminCourseController::class, 'approve'])->name('courses.approve');
        Route::patch('/courses/{course}/reject', [AdminCourseController::class, 'reject'])->name('courses.reject');
        Route::patch('/courses/{course}/toggle-featured', [AdminCourseController::class, 'toggleFeatured'])->name('courses.toggle-featured');
        Route::get('/courses/{course}/blast', [AdminCourseController::class, 'showBlast'])->name('courses.blast');
        Route::post('/courses/{course}/blast', [AdminCourseController::class, 'blast'])->name('courses.blast.send');

        // Course Enrollments — manual enroll/unenroll untuk user yang bayar offline
        Route::get('/courses/{course}/enrollments',                       [AdminCourseEnrollmentController::class, 'index'])->name('courses.enrollments.index');
        Route::post('/courses/{course}/enrollments',                      [AdminCourseEnrollmentController::class, 'store'])->name('courses.enrollments.store');
        Route::delete('/courses/{course}/enrollments/{enrollment}',       [AdminCourseEnrollmentController::class, 'destroy'])->name('courses.enrollments.destroy');
        Route::get('/courses/{course}/enrollments/search-users',          [AdminCourseEnrollmentController::class, 'searchUsers'])->name('courses.enrollments.search-users');

        // Orders
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');

        // Categories
        Route::resource('/categories', AdminCategoryController::class)->except(['show']);

        // Banners
        Route::resource('/banners', AdminBannerController::class)->except(['show']);
        Route::patch('/banners/{banner}/toggle-active', [AdminBannerController::class, 'toggleActive'])->name('banners.toggle-active');
        Route::post('/banners/reorder', [AdminBannerController::class, 'reorder'])->name('banners.reorder');

        // Benefits (Layanan/Fitur Landing Page)
        Route::resource('/benefits', AdminBenefitController::class)->except(['show']);
        Route::patch('/benefits/{benefit}/toggle-active', [AdminBenefitController::class, 'toggleActive'])->name('benefits.toggle-active');
        Route::post('/benefits/reorder', [AdminBenefitController::class, 'reorder'])->name('benefits.reorder');

        // Landing Programs (Selection style Image 2)
        Route::resource('/landing-programs', AdminLandingProgramController::class)->except(['show']);

        // Campuses (Offline Locations)
        Route::resource('/campuses', AdminCampusController::class)->except(['show']);

        // Galleries (Activity Gallery)
        Route::resource('/galleries', AdminGalleryController::class)->except(['show']);

        // Institutions (Lembaga Management)
        Route::resource('/institutions', AdminInstitutionController::class)->except(['show']);

        // Promo Codes
        Route::resource('/promo-codes', AdminPromoCodeController::class)->except(['show']);
        Route::patch('/promo-codes/{promo_code}/toggle-active', [AdminPromoCodeController::class, 'toggleActive'])->name('promo-codes.toggle-active');
        Route::get('/promo-codes/{promo_code}/blast', [AdminPromoCodeController::class, 'showBlast'])->name('promo-codes.blast');
        Route::post('/promo-codes/{promo_code}/blast', [AdminPromoCodeController::class, 'blast'])->name('promo-codes.blast.send');

        // Settings
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/upload-logo', [AdminSettingController::class, 'uploadLogo'])
            ->middleware('throttle:upload')
            ->name('settings.upload-logo');
        Route::post('/settings/upload-favicon', [AdminSettingController::class, 'uploadFavicon'])
            ->middleware('throttle:upload')
            ->name('settings.upload-favicon');
        Route::post('/settings/clear-cache', [AdminSettingController::class, 'clearCache'])->name('settings.clear-cache');

        // Memberships
        Route::resource('/memberships', AdminMembershipController::class)->except(['show']);
        Route::patch('/memberships/{membership}/toggle-active', [AdminMembershipController::class, 'toggleActive'])->name('memberships.toggle-active');
        Route::patch('/memberships/{membership}/toggle-popular', [AdminMembershipController::class, 'togglePopular'])->name('memberships.toggle-popular');

        // Analytics
        Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');
        Route::get('/analytics/export', [AdminAnalyticsController::class, 'export'])->name('analytics.export');

        // Event Management - Ticket Scanner & Absensi
        Route::get('/tickets/scan', [AdminTicketController::class, 'scan'])->name('tickets.scan');
        Route::post('/tickets/process-scan', [AdminTicketController::class, 'processScan'])->name('tickets.process-scan');
        Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/{bootcamp}', [AdminTicketController::class, 'showBootcamp'])->name('tickets.show-bootcamp');
        Route::get('/tickets/{bootcamp}/attendance-data', [AdminTicketController::class, 'attendanceData'])->name('tickets.attendance-data');
        Route::get('/tickets/{bootcamp}/export-pdf', [AdminTicketController::class, 'exportPdf'])->name('tickets.export-pdf');
        Route::get('/tickets/{bootcamp}/export-excel', [AdminTicketController::class, 'exportExcel'])->name('tickets.export-excel');

        // Instructor Applications
        Route::get('/instructor-applications', [AdminInstructorApplicationController::class, 'index'])->name('instructor-applications.index');
        Route::get('/instructor-applications/{application}', [AdminInstructorApplicationController::class, 'show'])->name('instructor-applications.show');
        Route::post('/instructor-applications/{application}/approve', [AdminInstructorApplicationController::class, 'approve'])->name('instructor-applications.approve');
        Route::post('/instructor-applications/{application}/reject', [AdminInstructorApplicationController::class, 'reject'])->name('instructor-applications.reject');

        // Bootcamps
        Route::resource('/bootcamps', AdminBootcampController::class)->except(['show']);
        Route::get('/bootcamps/{bootcamp}/blast', [AdminBootcampController::class, 'showBlast'])->name('bootcamps.blast');
        Route::post('/bootcamps/{bootcamp}/blast', [AdminBootcampController::class, 'blast'])->name('bootcamps.blast.send');

        // Book Orders — monitoring pengiriman buku fisik
        Route::get('/book-orders', [AdminBookOrderController::class, 'index'])
            ->name('book-orders.index');
        Route::get('/book-orders/{bookOrder}', [AdminBookOrderController::class, 'show'])
            ->name('book-orders.show');
        Route::patch('/book-orders/{bookOrder}/update-status', [AdminBookOrderController::class, 'updateStatus'])
            ->name('book-orders.update-status');
        Route::post('/book-orders/{bookOrder}/confirm-delivery', [AdminBookOrderController::class, 'confirmDelivery'])
            ->name('book-orders.confirm-delivery');

        // Books — CRUD lengkap
        Route::resource('/books', AdminBookController::class)->except(['show']);
        Route::patch('/books/{book}/toggle-status', [AdminBookController::class, 'toggleStatus'])->name('books.toggle-status');

        // Blog — CRUD lengkap
        Route::resource('/posts', AdminPostController::class)->except(['show']);
        Route::patch('/posts/{post}/toggle-status', [AdminPostController::class, 'toggleStatus'])->name('posts.toggle');

        // Testimonials
        Route::get('/testimonials', [AdminTestimonialController::class, 'index'])->name('testimonials.index');
        Route::patch('/testimonials/{testimonial}/toggle-featured', [AdminTestimonialController::class, 'toggleFeatured'])->name('testimonials.toggle-featured');
        Route::delete('/testimonials/{testimonial}', [AdminTestimonialController::class, 'destroy'])->name('testimonials.destroy');

        // Tags
        Route::get('/tags', [AdminTagController::class, 'index'])->name('tags.index');
        Route::post('/tags', [AdminTagController::class, 'store'])->name('tags.store');
        Route::put('/tags/{tag}', [AdminTagController::class, 'update'])->name('tags.update');
        Route::delete('/tags/{tag}', [AdminTagController::class, 'destroy'])->name('tags.destroy');

        // Desain Sertifikat
        Route::get('/certificate-templates/preview', [AdminCertificateTemplateController::class, 'preview'])->name('certificate-templates.preview');
        Route::resource('/certificate-templates', AdminCertificateTemplateController::class)->except(['show']);
        Route::patch('/certificate-templates/{certificateTemplate}/set-active', [AdminCertificateTemplateController::class, 'setActive'])->name('certificate-templates.set-active');

        // Instructor Management
        Route::get('/instructors', [AdminInstructorController::class, 'index'])->name('instructors.index');
        Route::post('/instructors/{user}/toggle-active', [AdminInstructorController::class, 'toggleActive'])->name('instructors.toggle-active');
        Route::post('/instructors/{user}/toggle-public', [AdminInstructorController::class, 'togglePublic'])->name('instructors.toggle-public');
        Route::get('/instructors/activities', [AdminInstructorController::class, 'activities'])->name('instructors.activities');

        // Audit Logs — Monitoring aksi admin/instructor
        Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/{auditLog}', [AdminAuditLogController::class, 'show'])->name('audit-logs.show');

        // Backup Management — Trigger manual + download + delete
        Route::get('/backups', [AdminBackupController::class, 'index'])->name('backups.index');
        Route::post('/backups', [AdminBackupController::class, 'store'])->name('backups.store');
        Route::get('/backups/download', [AdminBackupController::class, 'download'])->name('backups.download');
        Route::delete('/backups', [AdminBackupController::class, 'destroy'])->name('backups.destroy');

        // Flash Sales
        Route::resource('flash-sales', FlashSaleController::class);
        Route::post('flash-sales/{flash_sale}/items', [FlashSaleController::class, 'addItem'])->name('flash-sales.items.add');
        Route::delete('flash-sales/items/{item}', [FlashSaleController::class, 'removeItem'])->name('flash-sales.items.remove');
    });

// ── Public Pages ───────────────────────────────────────────────────────────────
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');
Route::post('/courses/{course}/reviews', [CourseController::class, 'storeReview'])
    ->middleware(['auth', 'verified'])
    ->name('courses.reviews.store');

// Bundles
Route::get('/bundles', [BundleController::class, 'index'])->name('bundles.index');
Route::get('/bundles/{slug}', [BundleController::class, 'show'])->name('bundles.show');

// ── Learning Room ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'enrolled'])
    ->group(function () {
        // Redirect ke lesson pertama
        Route::get('/learn/{slug}', [LearnController::class, 'show'])
            ->name('learn');

        // Lesson spesifik
        Route::get('/learn/{slug}/lessons/{lessonId}', [LearnController::class, 'show'])
            ->name('learn.lesson');
    });

// ── Quiz (Pretest & Posttest) untuk User ────────────────────────────────────────
Route::middleware(['auth', 'verified'])
    ->prefix('courses/{course}/quiz')
    ->name('quiz.')
    ->group(function () {
        Route::get('/{quiz}', [UserQuizController::class, 'show'])
            ->name('show');
        Route::post('/{quiz}/start', [UserQuizController::class, 'start'])
            ->name('start');
        Route::get('/{quiz}/attempt/{attempt}', [UserQuizController::class, 'attempt'])
            ->name('attempt');
        Route::post('/{quiz}/attempt/{attempt}/submit', [UserQuizController::class, 'submit'])
            ->name('submit');
        Route::get('/{quiz}/attempt/{attempt}/result', [UserQuizController::class, 'result'])
            ->name('result');
    });

// ── Certificates ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])
    ->group(function () {
        // Download via course slug (utama)
        Route::get('/certificates/{courseSlug}/download', [CertificateController::class, 'download'])
            ->name('certificates.download');

        // Download via certificate number (backward compatible: SKOL-2026-000001)
        Route::get('/certificates/{certNumber}/download', [CertificateController::class, 'downloadByCertNumber'])
            ->name('certificates.download.by-number')
            ->where('certNumber', 'SKOL-\d{4}-\d+');
    });

// ── Cart ────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/add-bundle/{bundle}', [CartController::class, 'addBundle'])->name('cart.add-bundle');
    Route::delete('/cart/{cart}', [CartController::class, 'remove'])->name('cart.remove');
    Route::patch('/cart/{cart}', [CartController::class, 'updateQuantity'])->name('cart.update');
    Route::post('/cart/apply-promo', [CartController::class, 'applyPromo'])->name('cart.apply-promo');
    Route::delete('/cart/promo/remove', [CartController::class, 'removePromo'])->name('cart.remove-promo');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/process', [CheckoutController::class, 'store'])
        ->middleware(['throttle:checkout', 'audit'])
        ->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');
});

// ── Bootcamps (Public) ──────────────────────────────────────────────────────
Route::get('/bootcamps', [BootcampController::class, 'index'])->name('bootcamps.index');
Route::get('/bootcamps/{slug}', [BootcampController::class, 'show'])->name('bootcamps.show');

// ── Bootcamp Checkout (Auth) ────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/bootcamp/checkout/process', [BootcampCheckoutController::class, 'process'])
        ->middleware(['throttle:checkout', 'audit'])
        ->name('bootcamp.checkout.process');
    Route::get('/bootcamp/checkout/success', [BootcampCheckoutController::class, 'success'])
        ->name('bootcamp.checkout.success');
    Route::get('/bootcamp/checkout/failed', [BootcampCheckoutController::class, 'failed'])
        ->name('bootcamp.checkout.failed');
});





// ── Help & FAQ (Public) ───────────────────────────────────────────────────
Route::get('/faq', [HelpController::class, 'faq'])->name('faq');

// ── Blog (Public) ──────────────────────────────────────────────────────────
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// ── Books (Public) ──────────────────────────────────────────────────────────
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{slug}', [BookController::class, 'show'])->name('books.show');

// ── Book Checkout (Auth) ────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/book/checkout/process', [BookCheckoutController::class, 'process'])
        ->middleware(['throttle:checkout', 'audit'])
        ->name('book.checkout.process');
    Route::get('/book/checkout/{slug}/shipping', [BookCheckoutController::class, 'shipping'])
        ->name('book.checkout.shipping');
    Route::post('/book/checkout/{slug}/shipping', [BookCheckoutController::class, 'shippingProcess'])
        ->name('book.checkout.shipping.process');
    Route::get('/book/checkout/success', [BookCheckoutController::class, 'success'])
        ->name('book.checkout.success');
    Route::get('/book/checkout/failed', [BookCheckoutController::class, 'failed'])
        ->name('book.checkout.failed');
    Route::get('/books/{slug}/download', [BookCheckoutController::class, 'download'])
        ->name('books.download');
});

Route::get('/membership', [MembershipController::class, 'index'])->name('membership');

// ── Membership Checkout (Auth) ──────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/membership/subscribe', [MembershipController::class, 'subscribe'])
        ->middleware('throttle:checkout')
        ->name('membership.subscribe');
    Route::get('/membership/checkout/success', [MembershipController::class, 'success'])
        ->name('membership.checkout.success');
    Route::get('/membership/checkout/failed', [MembershipController::class, 'failed'])
        ->name('membership.checkout.failed');
});

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::post('/contact', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'name'    => 'required|string|max:100',
        'email'   => 'required|email',
        'subject' => 'required|string',
        'message' => 'required|string|min:10',
    ]);

    // Log pesan (nanti bisa disambungkan ke email / queue)
    \Illuminate\Support\Facades\Log::info('Contact Form', $request->only('name','email','subject','message'));

    return back()->with('success', 'Pesan berhasil dikirim! Tim kami akan menghubungi kamu dalam 1-2 hari kerja.');
})->name('contact.send');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');

// ── Tickets ─────────────────────────────────────────────────────────────────────

// Verifikasi tiket publik (saat QR discan) — tidak perlu auth
Route::get('/tickets/verify/{ticketCode}', [TicketController::class, 'verify'])
    ->name('tickets.verify');

// Check-in peserta (admin/panitia) — perlu auth + role admin
Route::post('/tickets/{ticketCode}/checkin', [TicketController::class, 'checkin'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('tickets.checkin');

// Download tiket PDF (user pemilik tiket)
Route::get('/dashboard/tickets/{ticketCode}/download-pdf', [TicketController::class, 'downloadPdf'])
    ->middleware(['auth', 'verified'])
    ->name('tickets.download-pdf');

// Download QR code PNG (user pemilik tiket)
Route::get('/dashboard/tickets/{ticketCode}/download-qr', [TicketController::class, 'downloadQr'])
    ->middleware(['auth', 'verified'])
    ->name('tickets.download-qr');

// ── Certificates (Public verify) ───────────────────────────────────────────────
// (download route is already registered above via CertificateController)
Route::get('/verify/{certificateNumber}', [CertificateController::class, 'verify'])
    ->name('certificates.verify');
