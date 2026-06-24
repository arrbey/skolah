# SKOLAH.COM — SECURITY IMPLEMENTATION GUIDE
# Copy prompt ini ke Copilot/Claude satu fase per satu

# ============================================================
# FASE SECURITY 1 — MIDDLEWARE & HEADERS
# ============================================================

@workspace Buatkan file app/Http/Middleware/SecurityHeaders.php yang
menghalau semua serangan umum web. Isi lengkap:

```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Cegah clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Cegah MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // XSS Protection browser lama
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Batasi fitur browser
        $response->headers->set('Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(self)');

        // HSTS — paksa HTTPS 1 tahun (aktifkan setelah SSL terpasang)
        $response->headers->set('Strict-Transport-Security',
            'max-age=31536000; includeSubDomains; preload');

        // Content Security Policy — sesuaikan dengan kebutuhan
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://app.midtrans.com https://www.youtube.com https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: https: blob:",
            "frame-src https://www.youtube.com https://app.midtrans.com",
            "connect-src 'self' https://api.midtrans.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "upgrade-insecure-requests",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // Hapus header yang membocorkan info server
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
```

Daftarkan di bootstrap/app.php:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
})
```

# ============================================================
# FASE SECURITY 2 — RATE LIMITING & BRUTE FORCE PROTECTION
# ============================================================

@workspace Buatkan proteksi rate limiting lengkap di
app/Providers/AppServiceProvider.php — method boot():

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

// Login: 5x per menit per IP, lockout 15 menit
RateLimiter::for('login', function (Request $request) {
    return [
        Limit::perMinute(5)->by($request->ip())->response(function () {
            return response()->json([
                'message' => 'Terlalu banyak percobaan login. Coba lagi dalam 15 menit.'
            ], 429);
        }),
        Limit::perMinutes(15, 20)->by($request->ip()),
    ];
});

// Register: 3x per menit per IP
RateLimiter::for('register', function (Request $request) {
    return Limit::perMinute(3)->by($request->ip());
});

// Forgot password: 5x per jam per IP
RateLimiter::for('forgot-password', function (Request $request) {
    return Limit::perHour(5)->by($request->ip());
});

// Checkout: 10x per menit per user
RateLimiter::for('checkout', function (Request $request) {
    return Limit::perMinute(10)->by($request->user()?->id ?? $request->ip());
});

// API umum: 60x per menit per user/IP
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?? $request->ip());
});

// Upload: 20x per jam per user
RateLimiter::for('upload', function (Request $request) {
    return Limit::perHour(20)->by($request->user()?->id ?? $request->ip());
});

// Webhook Midtrans: 100x per menit per IP Midtrans
RateLimiter::for('webhook', function (Request $request) {
    return Limit::perMinute(100)->by($request->ip());
});
```

Tambahkan di routes/web.php:
```php
Route::middleware('throttle:login')->post('/login', [AuthController::class, 'login']);
Route::middleware('throttle:register')->post('/register', [AuthController::class, 'register']);
Route::middleware('throttle:forgot-password')->post('/forgot-password', [...]);
Route::middleware('throttle:checkout')->post('/checkout/process', [...]);
```

# ============================================================
# FASE SECURITY 3 — SQL INJECTION & XSS PREVENTION
# ============================================================

@workspace Buatkan app/Http/Middleware/SanitizeInput.php yang
membersihkan semua input sebelum masuk ke controller:

```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    // Field yang BOLEH mengandung HTML (konten editor admin/instructor)
    protected array $allowHtml = [
        'description', 'content', 'body', 'about'
    ];

    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        array_walk_recursive($input, function (&$value, $key) {
            if (is_string($value)) {
                if (!in_array($key, $this->allowHtml)) {
                    // Strip semua HTML tag dari input biasa
                    $value = strip_tags($value);
                    // Encode karakter berbahaya
                    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
                }
                // Trim whitespace
                $value = trim($value);
                // Hapus null bytes (cegah null byte injection)
                $value = str_replace(chr(0), '', $value);
            }
        });
        $request->merge($input);
        return $next($request);
    }
}
```

Tambahkan di bootstrap/app.php sebelum SecurityHeaders:
```php
$middleware->append(\App\Http\Middleware\SanitizeInput::class);
```

Aturan wajib untuk SEMUA controller:
- SELALU gunakan Eloquent ORM, JANGAN raw query dengan input user
- Jika raw query terpaksa, WAJIB gunakan parameterized binding:
  DB::select('SELECT * FROM courses WHERE id = ?', [$id]);
- Output ke Blade: SELALU {{ $var }} bukan {!! $var !!}
- Pengecualian {!! !!} HANYA untuk konten yang sudah divalidasi admin

# ============================================================
# FASE SECURITY 4 — FILE UPLOAD SECURITY
# ============================================================

@workspace Buatkan app/Services/SecureFileUpload.php untuk semua
upload di platform — images, PDF buku, avatar:

```php
<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class SecureFileUpload
{
    // MIME type yang diizinkan per kategori
    protected array $allowedMimes = [
        'image'    => ['image/jpeg', 'image/png', 'image/webp'],
        'document' => ['application/pdf'],
        'video'    => [], // video tidak di-upload, pakai YouTube
    ];

    protected array $maxSizes = [
        'image'    => 5120,   // 5 MB dalam KB
        'document' => 51200,  // 50 MB dalam KB
    ];

    public function uploadImage(UploadedFile $file, string $folder): string
    {
        $this->validate($file, 'image');

        // Buat nama file random — JANGAN gunakan nama asli user
        $filename = Str::random(40) . '.webp';
        $path = storage_path("app/public/{$folder}");

        if (!file_exists($path)) mkdir($path, 0755, true);

        // Proses via Intervention/Image:
        // - Resize maksimal 1200px width
        // - Strip EXIF metadata (hapus GPS, info kamera)
        // - Konversi ke WebP untuk performa
        // - Kualitas 85%
        Image::make($file)
            ->resize(1200, null, fn($c) => $c->aspectRatio()->upsize())
            ->stripExif()
            ->encode('webp', 85)
            ->save("{$path}/{$filename}");

        return "storage/{$folder}/{$filename}";
    }

    public function uploadDocument(UploadedFile $file, string $folder): string
    {
        $this->validate($file, 'document');

        $filename = Str::random(40) . '.pdf';

        // Simpan di LUAR public/ — hanya bisa diakses via controller
        $path = storage_path("app/private/{$folder}");
        if (!file_exists($path)) mkdir($path, 0755, true);

        $file->move($path, $filename);

        // Return path relatif dari storage/app/private/
        return "private/{$folder}/{$filename}";
    }

    protected function validate(UploadedFile $file, string $type): void
    {
        // 1. Cek MIME type via konten file (bukan ekstensi)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file->getRealPath());

        if (!in_array($realMime, $this->allowedMimes[$type])) {
            throw new \Exception("Tipe file tidak diizinkan: {$realMime}");
        }

        // 2. Cek ukuran file
        if ($file->getSize() / 1024 > $this->maxSizes[$type]) {
            $maxMB = $this->maxSizes[$type] / 1024;
            throw new \Exception("Ukuran file melebihi batas {$maxMB}MB");
        }

        // 3. Cek nama file dari karakter berbahaya (path traversal)
        $originalName = $file->getClientOriginalName();
        if (preg_match('/[\/\\\\:*?"<>|]/', $originalName)) {
            throw new \Exception("Nama file mengandung karakter tidak valid");
        }

        // 4. Untuk image: verifikasi benar-benar image via getimagesize()
        if ($type === 'image') {
            $imageInfo = @getimagesize($file->getRealPath());
            if ($imageInfo === false) {
                throw new \Exception("File bukan gambar yang valid");
            }
        }
    }
}
```

Penggunaan di controller:
```php
$uploader = new SecureFileUpload();
$thumbnailPath = $uploader->uploadImage($request->file('thumbnail'), 'courses');
$course->thumbnail = $thumbnailPath;
```

# ============================================================
# FASE SECURITY 5 — CSRF + SESSION HARDENING
# ============================================================

@workspace Update config/session.php untuk session yang aman:

```php
return [
    'driver'          => env('SESSION_DRIVER', 'file'),
    'lifetime'        => 120,           // 2 jam idle logout
    'expire_on_close' => false,
    'encrypt'         => true,          // Enkripsi session data
    'files'           => storage_path('framework/sessions'),
    'cookie'          => env('SESSION_COOKIE', 'skolah_session'),
    'path'            => '/',
    'domain'          => env('SESSION_DOMAIN', null),
    'secure'          => env('SESSION_SECURE_COOKIE', true),  // HTTPS only
    'http_only'       => true,          // Tidak bisa diakses JS
    'same_site'       => 'lax',         // Cegah CSRF lintas situs
];
```

Tambahkan di VerifyCsrfToken.php — HANYA webhook yang dikecualikan:
```php
protected $except = [
    'midtrans/webhook',     // Midtrans tidak kirim CSRF token
];
```

Semua form Blade WAJIB ada:
```blade
<form method="POST" action="...">
    @csrf
    {{-- isi form --}}
</form>
```

# ============================================================
# FASE SECURITY 6 — AUTHENTICATION HARDENING
# ============================================================

@workspace Buatkan system autentikasi yang aman di
app/Http/Controllers/Auth/AuthController.php:

```php
<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\LoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        // Cek apakah IP ini sudah terlalu banyak gagal
        $key = 'login.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik."
            ]);
        }

        // Cek kredensial
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($key, 900); // lockout 15 menit

            // Log percobaan gagal
            $this->logFailedAttempt($request);

            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai.'
                // JANGAN beritahu field mana yang salah secara spesifik
            ]);
        }

        // Login berhasil — reset counter, regenerate session
        RateLimiter::clear($key);
        $request->session()->regenerate(); // Cegah session fixation

        $user = Auth::user();

        // Redirect berdasarkan role
        return match(true) {
            $user->hasRole('admin')      => redirect()->intended('/admin/dashboard'),
            $user->hasRole('instructor') => redirect()->intended('/instructor/dashboard'),
            default                      => redirect()->intended('/dashboard'),
        };
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            // JANGAN simpan password plain text apapun alasannya
        ]);

        $user->assignRole('user');
        $user->sendEmailVerificationNotification();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard')
            ->with('info', 'Akun berhasil dibuat. Cek email untuk verifikasi.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();    // Hapus semua data session
        $request->session()->regenerateToken(); // Buat CSRF token baru
        return redirect('/login');
    }

    protected function logFailedAttempt(Request $request): void
    {
        // Simpan ke DB untuk monitoring (tabel: login_attempts)
        \DB::table('login_attempts')->insert([
            'ip_address' => $request->ip(),
            'email'      => $request->email,
            'user_agent' => $request->userAgent(),
            'attempted_at' => now(),
        ]);
    }
}
```

Buat juga tabel login_attempts via migration:
```php
Schema::create('login_attempts', function (Blueprint $table) {
    $table->id();
    $table->string('ip_address', 45);
    $table->string('email');
    $table->string('user_agent')->nullable();
    $table->timestamp('attempted_at');
});
```

# ============================================================
# FASE SECURITY 7 — AUTHORIZATION & OWNERSHIP CHECK
# ============================================================

@workspace Buatkan Policy untuk setiap model utama di
app/Policies/ — pastikan user HANYA bisa akses data miliknya:

```php
// app/Policies/CoursePolicy.php
class CoursePolicy
{
    // Hanya instructor pemilik course yang bisa edit
    public function update(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id
            || $user->hasRole('admin');
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id
            || $user->hasRole('admin');
    }

    // Hanya user yang sudah enroll yang bisa akses LMS
    public function viewLearningRoom(User $user, Course $course): bool
    {
        return $course->enrollments()
            ->where('user_id', $user->id)
            ->exists();
    }
}

// app/Policies/CertificatePolicy.php
class CertificatePolicy
{
    // Hanya pemilik sertifikat yang bisa download
    public function download(User $user, Certificate $certificate): bool
    {
        return $user->id === $certificate->user_id;
    }
}

// app/Policies/BookPolicy.php
class BookPolicy
{
    // Download buku digital hanya jika sudah dibeli
    public function download(User $user, Book $book): bool
    {
        return $user->orders()
            ->whereHas('items', fn($q) => $q
                ->where('itemable_type', Book::class)
                ->where('itemable_id', $book->id))
            ->where('status', 'paid')
            ->exists();
    }
}
```

Daftarkan di app/Providers/AuthServiceProvider.php:
```php
protected $policies = [
    Course::class      => CoursePolicy::class,
    Certificate::class => CertificatePolicy::class,
    Book::class        => BookPolicy::class,
];
```

Gunakan di controller:
```php
// Di CourseController
public function edit(Course $course) {
    $this->authorize('update', $course);
    // ...
}

// Di CertificateController
public function download(Certificate $certificate) {
    $this->authorize('download', $certificate);
    // ...
}
```

# ============================================================
# FASE SECURITY 8 — MIDTRANS WEBHOOK SECURITY
# ============================================================

@workspace Buatkan CheckoutController@webhook yang benar-benar aman:

```php
public function webhook(Request $request)
{
    // 1. Verifikasi signature Midtrans SEBELUM apapun
    $payload      = $request->all();
    $orderId      = $payload['order_id']      ?? '';
    $statusCode   = $payload['status_code']   ?? '';
    $grossAmount  = $payload['gross_amount']  ?? '';
    $signatureKey = $payload['signature_key'] ?? '';

    // Hitung expected signature
    $expected = hash('sha512',
        $orderId . $statusCode . $grossAmount . config('midtrans.server_key')
    );

    // Gunakan hash_equals() — cegah timing attack
    if (!hash_equals($expected, $signatureKey)) {
        \Log::warning('Midtrans webhook: signature tidak valid', [
            'ip'       => $request->ip(),
            'order_id' => $orderId,
        ]);
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // 2. Verifikasi order ada di database kita
    $order = Order::where('order_number', $orderId)->first();
    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    // 3. Cegah double processing — cek status saat ini
    if ($order->status === 'paid') {
        return response()->json(['message' => 'Already processed'], 200);
    }

    // 4. Verifikasi jumlah tidak dimanipulasi
    if ((int) $grossAmount !== (int) $order->total) {
        \Log::error('Midtrans webhook: jumlah tidak cocok', [
            'order_id'     => $orderId,
            'expected'     => $order->total,
            'received'     => $grossAmount,
        ]);
        return response()->json(['message' => 'Amount mismatch'], 400);
    }

    // 5. Update status berdasarkan transaction_status Midtrans
    $transactionStatus = $payload['transaction_status'] ?? '';
    $fraudStatus       = $payload['fraud_status'] ?? 'accept';

    \DB::transaction(function () use ($order, $payload, $transactionStatus, $fraudStatus) {
        if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
            $order->update(['status' => 'paid', 'paid_at' => now(),
                'midtrans_transaction_id' => $payload['transaction_id']]);
            $this->processOrderItems($order);

        } elseif ($transactionStatus === 'settlement') {
            $order->update(['status' => 'paid', 'paid_at' => now()]);
            $this->processOrderItems($order);

        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $order->update(['status' => 'failed']);

        } elseif ($transactionStatus === 'refund') {
            $order->update(['status' => 'refunded']);
        }
    });

    return response()->json(['message' => 'OK'], 200);
}

protected function processOrderItems(Order $order): void
{
    foreach ($order->items as $item) {
        match($item->itemable_type) {
            Course::class       => $this->enrollCourse($order->user_id, $item->itemable_id),
            Bootcamp::class     => $this->registerBootcamp($order->user_id, $item->itemable_id),
            Book::class         => $this->grantBookAccess($order->user_id, $item->itemable_id),
            MembershipPlan::class => $this->activateMembership($order->user_id, $item->itemable_id),
        };
    }
    // Kirim email konfirmasi (via queue)
    dispatch(new \App\Jobs\SendOrderConfirmation($order));
}
```

# ============================================================
# FASE SECURITY 9 — AUDIT LOG & MONITORING
# ============================================================

@workspace Buatkan sistem audit log di
app/Http/Middleware/AuditLog.php:

```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuditLog
{
    // Route yang WAJIB dicatat
    protected array $watch = [
        'admin/*', 'instructor/*',
        'checkout/*', 'midtrans/*',
        'dashboard/certificates/*',
    ];

    // Method yang dicatat
    protected array $methods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($this->shouldLog($request)) {
            \DB::table('audit_logs')->insert([
                'user_id'    => auth()->id(),
                'ip_address' => $request->ip(),
                'method'     => $request->method(),
                'url'        => $request->fullUrl(),
                'status'     => $response->getStatusCode(),
                'user_agent' => substr($request->userAgent(), 0, 500),
                'created_at' => now(),
            ]);
        }

        return $response;
    }

    protected function shouldLog(Request $request): bool
    {
        if (!in_array($request->method(), $this->methods)) return false;

        foreach ($this->watch as $pattern) {
            if ($request->is($pattern)) return true;
        }

        return false;
    }
}
```

Buat migration tabel audit_logs:
```php
Schema::create('audit_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    $table->string('ip_address', 45);
    $table->string('method', 10);
    $table->string('url', 1000);
    $table->smallInteger('status');
    $table->string('user_agent', 500)->nullable();
    $table->timestamp('created_at');

    $table->index(['user_id', 'created_at']);
    $table->index(['ip_address', 'created_at']);
});
```

Buat juga tabel login_attempts (untuk monitoring brute force):
```php
Schema::create('login_attempts', function (Blueprint $table) {
    $table->id();
    $table->string('ip_address', 45);
    $table->string('email');
    $table->string('user_agent', 500)->nullable();
    $table->timestamp('attempted_at');
    $table->index(['ip_address', 'attempted_at']);
});
```

# ============================================================
# FASE SECURITY 10 — .htaccess LENGKAP (Shared Hosting)
# ============================================================

@workspace Buatkan file public/.htaccess yang komprehensif untuk
keamanan di shared hosting cPanel:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Redirect HTTP ke HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Blokir akses langsung ke file sensitif
    RewriteRule ^(\.env|\.git|composer\.(json|lock)|package\.json|artisan) - [F,L]

    # Laravel routing
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    Header always set Permissions-Policy "camera=(), microphone=(), geolocation=()"

    # Hapus header yang bocorkan info server
    Header always unset X-Powered-By
    Header always unset Server
</IfModule>

# Blokir akses ke file berbahaya
<FilesMatch "\.(env|git|sql|log|bak|swp|tmp|htpasswd)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Blokir akses ke folder tersembunyi
<IfModule mod_rewrite.c>
    RewriteRule "(^|/)\." - [F]
</IfModule>

# Matikan directory listing
Options -Indexes

# Batasi ukuran upload (10MB)
<IfModule mod_php.c>
    php_value upload_max_filesize 10M
    php_value post_max_size 12M
    php_value max_execution_time 60
    php_value memory_limit 256M
</IfModule>

# Cegah eksekusi PHP di folder upload
<IfModule mod_rewrite.c>
    RewriteRule ^storage/.*\.(php|php5|phtml|pl|py|jsp|asp|sh|cgi)$ - [F,NC,L]
</IfModule>

# Proteksi session cookie
<IfModule mod_php.c>
    php_flag session.cookie_httponly on
    php_flag session.cookie_secure on
    php_value session.cookie_samesite Lax
    php_flag session.use_strict_mode on
</IfModule>

# Cache static assets (performa + keamanan)
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png  "access plus 1 year"
    ExpiresByType text/css   "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>

# Blokir User-Agent bot berbahaya umum
<IfModule mod_rewrite.c>
    RewriteCond %{HTTP_USER_AGENT} (havij|sqlmap|nikto|nmap|masscan|dirbuster) [NC]
    RewriteRule .* - [F,L]
</IfModule>
```

# ============================================================
# FASE SECURITY 11 — ENKRIPSI DATA SENSITIF ✅ DONE
# ============================================================
# Implementasi:
# 1. Migration: 2026_04_06_210000_widen_columns_for_encryption.php
#    - orders.midtrans_transaction_id, midtrans_snap_token, midtrans_order_id → TEXT
# 2. Artisan Command: app/Console/Commands/EncryptSensitiveData.php
#    - encrypt:sensitive-data --dry-run / --force (idempotent)
#    - 18 existing records encrypted (13 bio, 3 snap_token, 2 txn_id)
# 3. Model Casts (auto encrypt/decrypt via Laravel 'encrypted' cast):
#    - User.php: 'bio' => 'encrypted'
#    - Order.php: 'midtrans_snap_token' => 'encrypted',
#                 'midtrans_transaction_id' => 'encrypted',
#                 'midtrans_order_id' => 'encrypted'
#    - BookOrder.php: 'shipping_address' => 'encrypted:array'
# 4. Order.$hidden: midtrans_snap_token, midtrans_transaction_id
# 5. APP_KEY verified, NEVER change after production
# ============================================================

# ============================================================
# FASE SECURITY 12 — FORM REQUEST VALIDATION ✅ DONE
# ============================================================
#
# Tanggal implementasi: 2025
#
# RINGKASAN:
# Semua controller method yang menerima input user sekarang menggunakan
# dedicated FormRequest class — tidak ada lagi inline $request->validate().
#
# A. HARDENED 15 EXISTING FORM REQUESTS:
#    - Instructor\StoreCourseRequest    → min:5 title, min:20/max:50000 desc, max:99999999 price
#    - Instructor\UpdateCourseRequest   → (sama)
#    - Instructor\StoreBootcampRequest  → min:5 title, min:20 desc, max:10000 participants
#    - Instructor\UpdateBootcampRequest → (sama)
#    - Instructor\StoreBookRequest      → isbn regex, max:99999 stock/pages
#    - Instructor\UpdateBookRequest     → (sama)
#    - Instructor\StoreLessonRequest    → min:3 title, max:500 video_url, max:86400 duration
#    - Admin\StoreBannerRequest         → position in:home_hero,home_secondary,sidebar
#    - Admin\UpdateBannerRequest        → (sama) + messages()
#    - Admin\StoreCategoryRequest       → min:2, unique:categories,name
#    - Admin\UpdateCategoryRequest      → Rule::unique with ignore
#    - Admin\StorePromoCodeRequest      → alpha_dash, withValidator() percent<=100
#    - Admin\UpdatePromoCodeRequest     → (sama)
#    - Admin\StoreMembershipPlanRequest → min:3, max bounds, max:500 course_ids
#    - Admin\UpdateMembershipPlanRequest→ (sama) + messages()
#    - User\UpdateProfileRequest        → min:3, regex name, max:1000 bio
#    - User\UpdatePasswordRequest       → max:72 (bcrypt limit)
#    - BookShippingRequest              → regex phone & postal_code
#
# B. CREATED 16 NEW FORM REQUESTS:
#    - Admin\StoreTagRequest              → unique:tags,name
#    - Admin\UpdateTagRequest             → Rule::unique with ignore
#    - Admin\UpdateSettingRequest         → group in:general,seo,payment,email,social
#    - Admin\UploadLogoRequest            → image, max:2048
#    - Admin\UploadFaviconRequest         → image, max:512
#    - Admin\ReorderBannerRequest         → order array, exists:banners,id
#    - Admin\UpdateBookOrderStatusRequest → conditional shipped rules
#    - Admin\ConfirmDeliveryRequest       → delivery_photo image max:3072
#    - Admin\StoreCertificateTemplateRequest → 30+ template fields
#    - Admin\BlastEmailRequest            → custom_message nullable, max:1000
#    - Admin\ProcessScanRequest           → ticket_code required, max:100
#    - Admin\ApproveApplicationRequest    → admin_notes nullable, max:1000
#    - Admin\RejectApplicationRequest     → admin_notes required, min:10
#    - Admin\StoreBookRequest             → full admin book validation
#    - Admin\UpdateBookRequest            → (sama)
#    - User\InstructorApplicationRequest  → motivation min:50, phone regex
#
# C. WIRED 12 CONTROLLERS TO FORM REQUESTS:
#    - Admin\TagController::store/update
#    - Admin\SettingController::update/uploadLogo/uploadFavicon
#    - Admin\BookController::store/update
#    - Admin\BookOrderController::updateStatus/confirmDelivery
#    - Admin\CertificateTemplateController::store/update
#    - Admin\BannerController::reorder
#    - Admin\PromoCodeController::blast
#    - Admin\CourseController::blast
#    - Admin\BootcampController::blast
#    - Admin\TicketController::processScan
#    - Admin\InstructorApplicationController::approve/reject
#    - User\InstructorApplicationController::store
#
# TOTAL: 46 files modified/created, 0 inline validate() remaining
# All files syntax-checked (php -l), app boots clean (route:list OK)

# ============================================================
# CHECKLIST SECURITY FINAL — VERIFIKASI SEBELUM DEPLOY
# ============================================================

@workspace Sebelum deploy ke production, verifikasi semua poin ini:

KONFIGURASI:
□ APP_DEBUG=false di .env production
□ APP_ENV=production di .env production
□ APP_KEY sudah di-generate (php artisan key:generate)
□ Semua secret di .env, tidak ada yang hardcode di kode
□ .env tidak ter-commit ke Git (ada di .gitignore)

MIDDLEWARE:
□ SecurityHeaders middleware aktif dan terdaftar di bootstrap/app.php
□ SanitizeInput middleware aktif
□ AuditLog middleware aktif untuk route sensitif
□ Rate limiting aktif di semua route auth dan checkout

AUTENTIKASI:
□ Login throttle 5x per menit aktif
□ Session di-regenerate setelah login
□ Session di-invalidate setelah logout
□ Email verifikasi wajib sebelum akses dashboard
□ Password minimal 8 karakter dengan kombinasi

OTORISASI:
□ Semua Policy terdaftar di AuthServiceProvider
□ $this->authorize() dipanggil di setiap action sensitif
□ Instructor hanya bisa edit course miliknya sendiri
□ User hanya bisa download sertifikat miliknya

PAYMENT:
□ Webhook Midtrans verifikasi signature SEBELUM proses apapun
□ Webhook cek jumlah tidak dimanipulasi
□ Webhook cegah double processing (cek status 'paid')
□ Route webhook dikecualikan dari CSRF
□ Webhook menggunakan hash_equals() (bukan ==)

DATABASE:
□ Semua query pakai Eloquent atau parameterized binding
□ Tidak ada raw query dengan input user langsung
□ Data sensitif (shipping address, token) dienkripsi

FILE:
□ Upload divalidasi MIME type via konten file (bukan ekstensi)
□ File upload disimpan di luar public/
□ Nama file di-randomize (Str::random)
□ EXIF metadata di-strip dari gambar
□ PHP execution diblokir di folder storage via .htaccess

HEADERS & TRANSPORT:
□ HTTPS aktif dan .htaccess redirect HTTP → HTTPS
□ HSTS header aktif (setelah SSL terpasang)
□ CSP header dikonfigurasi
□ X-Frame-Options aktif
□ Directory listing dimatikan (-Indexes)

MONITORING:
□ Tabel audit_logs aktif merekam aksi admin/instructor
□ Tabel login_attempts merekam percobaan login gagal
□ File .env, .git, .sql diblokir di .htaccess
□ User-agent bot berbahaya diblokir di .htaccess
