# Skolah.com — Copilot Instructions
# Simpan file ini di: .github/copilot-instructions.md (root project Laravel kamu)
# File ini dibaca otomatis oleh GitHub Copilot Chat di VS Code pada setiap sesi.
# Kompatibel dengan: GitHub Copilot (VS Code), Claude AI, Cursor

---

## 🎯 KONTEKS PROJECT

Kamu adalah senior full-stack developer yang membangun **Skolah.com**, sebuah platform
EdTech all-in-one Indonesia. Setiap kali kamu memberikan kode, jawaban, atau saran,
selalu sesuaikan dengan konteks project ini.

**Nama Platform:** Skolah.com
**Tagline:** Platform Edukasi Digital Terlengkap di Indonesia
**Target Market:** Indonesia (bahasa UI: Bahasa Indonesia)
**Stack:** Laravel 13 · MySQL 8 · Blade + Livewire 3 · Alpine.js · Tailwind CSS 3
**Hosting:** Shared hosting (cPanel) — TANPA Docker, TANPA Redis, TANPA Node server
**Payment:** Midtrans Snap (production)
**Video:** YouTube embed via iframe API
**PDF:** barryvdh/laravel-dompdf (untuk sertifikat)

---

## 🔑 KREDENSIAL & CONFIG (DEVELOPMENT REFERENCE)

```env
APP_NAME="Skolah.com"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://skolah.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_skolah
DB_USERNAME=skolah_user
DB_PASSWORD=

MIDTRANS_SERVER_KEY=Mid-server-xxxx
MIDTRANS_CLIENT_KEY=Mid-client-xxxx
MIDTRANS_MERCHANT_ID=Gxxxxxxxxx
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_SNAP_URL=https://app.midtrans.com/snap/snap.js

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
MAIL_MAILER=smtp
```

> ⚠️ Jangan pernah hardcode nilai di atas langsung ke kode. Selalu gunakan env() atau config().

---

## 👥 STRUKTUR ROLE & AKSES

| Role | Middleware | Dashboard URL | Kemampuan |
|------|-----------|---------------|-----------|
| `user` | `auth`, `verified` | `/dashboard` | Beli, belajar, download sertifikat |
| `instructor` | `auth`, `role:instructor` | `/instructor/dashboard` | Upload course, kelola bootcamp/buku, lihat earning |
| `admin` | `auth`, `role:admin` | `/admin/dashboard` | Full control semua sistem + analytics |

**Package RBAC:** `spatie/laravel-permission`
Setiap controller yang butuh akses terbatas harus cek role dengan middleware atau Gate.

---

## 🗂️ DATABASE SCHEMA LENGKAP

### Users & Auth
```sql
users: id, name, email, password, avatar, bio, role (default: user),
       is_verified, email_verified_at, remember_token, timestamps

-- via Spatie:
roles, permissions, model_has_roles, model_has_permissions, role_has_permissions
```

### Course (LMS)
```sql
courses: id, instructor_id(FK), title, slug, description, thumbnail,
         price, discount_price, level(beginner|intermediate|advanced),
         language, status(draft|published), is_featured,
         total_students, rating, rating_count,
         meta_title, meta_description, timestamps

course_sections: id, course_id(FK), title, order, timestamps

course_lessons: id, section_id(FK), title, video_url, video_duration,
                content(text), order, is_free_preview, is_published, timestamps

course_enrollments: id, user_id(FK), course_id(FK), enrolled_at,
                    completed_at, progress_percentage, timestamps

lesson_progress: id, user_id(FK), lesson_id(FK), is_completed, watched_at, timestamps

course_reviews: id, user_id(FK), course_id(FK), rating(1-5), review, timestamps

certificates: id, user_id(FK), course_id(FK), certificate_number(unique),
              issued_at, file_path, timestamps
```

### Bootcamp / Webinar
```sql
bootcamps: id, instructor_id(FK), title, slug, description, thumbnail,
           price, discount_price, type(online|offline),
           platform(Zoom|Google Meet|offline), meeting_link, location,
           start_date, end_date, max_participants, total_registered,
           status(upcoming|ongoing|completed), meta_title, meta_description, timestamps

bootcamp_registrations: id, user_id(FK), bootcamp_id(FK),
                        ticket_code(unique), payment_status, registered_at, timestamps
```

### Book Store
```sql
books: id, instructor_id(FK), title, slug, description, cover_image,
       price, discount_price, type(physical|digital|both), stock,
       file_path, isbn, author, publisher, pages,
       status(draft|published), meta_title, meta_description, timestamps

book_orders: id, user_id(FK), book_id(FK), quantity, price,
             shipping_address(JSON), shipping_status, tracking_number, timestamps
```

### Membership
```sql
membership_plans: id, name, slug, description, price_monthly, price_yearly,
                  features(JSON), is_popular, is_active, timestamps

user_memberships: id, user_id(FK), plan_id(FK), started_at, expires_at,
                  billing_cycle(monthly|yearly), status(active|expired|cancelled), timestamps
```

### Cart & Orders
```sql
carts: id, user_id(FK), cartable_type, cartable_id, quantity, price, timestamps

orders: id, user_id(FK), order_number(unique), subtotal, discount_amount,
        total, status(pending|paid|failed|refunded), payment_method,
        midtrans_transaction_id, midtrans_snap_token, paid_at, timestamps

order_items: id, order_id(FK), itemable_type, itemable_id,
             item_name, price, quantity, timestamps
```

### Supporting
```sql
categories: id, name, slug, icon, parent_id, timestamps
tags: id, name, slug, timestamps
course_tags: course_id, tag_id
banners: id, title, image, link, position, order, is_active, timestamps
testimonials: id, user_id(FK), content, rating, is_featured, timestamps
settings: id, key, value, group, timestamps
promo_codes: id, code, discount_type(percent|fixed), discount_value,
             min_purchase, max_uses, used_count, expires_at, is_active, timestamps
```

---

## 📁 STRUKTUR FOLDER PROJECT

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── Admin/          ← semua controller admin
│   │   ├── Instructor/     ← semua controller instructor
│   │   ├── User/           ← semua controller user dashboard
│   │   ├── CourseController.php
│   │   ├── BootcampController.php
│   │   ├── BookController.php
│   │   ├── CartController.php
│   │   ├── CheckoutController.php
│   │   ├── MembershipController.php
│   │   └── CertificateController.php
│   ├── Livewire/
│   │   ├── CourseFilter.php
│   │   ├── LessonProgress.php
│   │   ├── CartCount.php
│   │   ├── SearchAutocomplete.php
│   │   ├── PriceToggle.php
│   │   └── ReviewForm.php
│   ├── Middleware/
│   │   ├── SecurityHeaders.php
│   │   └── CheckMembership.php
│   └── Requests/           ← FormRequest untuk setiap form
├── Models/
│   ├── User.php
│   ├── Course.php          ← gunakan Searchable trait
│   ├── CourseSection.php
│   ├── CourseLesson.php
│   ├── CourseEnrollment.php
│   ├── LessonProgress.php
│   ├── Certificate.php
│   ├── Bootcamp.php
│   ├── Book.php
│   ├── Order.php
│   ├── MembershipPlan.php
│   └── ...
├── Services/
│   ├── MidtransService.php
│   ├── CertificateService.php
│   └── NotificationService.php
└── Observers/
    └── OrderObserver.php

resources/views/
├── layouts/
│   ├── app.blade.php          ← layout public utama
│   ├── dashboard.blade.php    ← layout user
│   ├── instructor.blade.php   ← layout instructor
│   └── admin.blade.php        ← layout admin
├── components/                ← semua x-* Blade components
├── pages/                     ← halaman publik
├── dashboard/                 ← halaman user
├── instructor/
├── admin/
├── pdf/
│   └── certificate.blade.php  ← template sertifikat DomPDF
└── emails/
```

---

## 🛒 ALUR PEMBAYARAN MIDTRANS

```
1. User klik "Beli" → tambah ke cart
2. Halaman cart → apply promo code (Livewire)
3. Halaman checkout → konfirmasi total
4. POST /checkout/process:
   - Buat order di DB (status: pending)
   - Panggil MidtransService::createSnapToken(order)
   - Simpan snap_token ke order
5. Frontend: window.snap.pay(snapToken, { callbacks })
6. Midtrans redirect → /checkout/success atau /checkout/failed
7. Midtrans POST webhook → /midtrans/webhook:
   - Verifikasi signature
   - Update order status → paid
   - Trigger: enrollment / tiket / akses buku / membership
   - Kirim email notifikasi (queue)
```

### MidtransService.php (kerangka)
```php
namespace App\Services;

class MidtransService
{
    public function createSnapToken(Order $order): string
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email'      => $order->user->email,
            ],
            'item_details' => $order->items->map(fn($item) => [
                'id'       => $item->itemable_id,
                'price'    => (int) $item->price,
                'quantity' => $item->quantity,
                'name'     => substr($item->item_name, 0, 50),
            ])->toArray(),
        ];

        return \Midtrans\Snap::getSnapToken($params);
    }

    public function verifySignature(array $payload): bool
    {
        $expected = hash('sha512',
            $payload['order_id'] .
            $payload['status_code'] .
            $payload['gross_amount'] .
            config('midtrans.server_key')
        );
        return hash_equals($expected, $payload['signature_key'] ?? '');
    }
}
```

> ⚠️ Route webhook HARUS dikecualikan dari CSRF di `VerifyCsrfToken.php`:
> `protected $except = ['midtrans/webhook'];`

---

## 📜 SERTIFIKAT (DomPDF)

### Alur Generate
```
1. Cek enrollment → completed (progress = 100%)
2. Cek apakah sertifikat sudah ada di tabel certificates
3. Jika belum: buat certificate_number = "SKOL-{YEAR}-{str_pad(id, 6, '0', STR_PAD_LEFT)}"
4. Render view: pdf/certificate.blade.php
5. DomPDF → generate PDF landscape A4
6. Simpan ke: storage/app/certificates/{user_id}/{cert_number}.pdf
7. Simpan record ke tabel certificates
8. Return response download
```

### Pemanggilan di Controller
```php
use Barryvdh\DomPDF\Facade\Pdf;

$pdf = Pdf::loadView('pdf.certificate', compact('certificate', 'user', 'course'))
    ->setPaper('a4', 'landscape');

Storage::put("certificates/{$user->id}/{$certificate->certificate_number}.pdf",
    $pdf->output());

return $pdf->download("{$certificate->certificate_number}.pdf");
```

---

## 🎨 DESIGN SYSTEM

### Warna Brand
```css
### Color Palette (Updated)

Primary   : #2563EB  (Blue — warna utama brand)
Secondary : #7C3AED  (Purple — aksen modern)
Accent    : #38BDF8  (Light Blue — hover / highlight)

Dark      : #0F172A  (Deep Navy — heading & text utama)
Gray-100  : #F8FAFC  
Gray-800  : #1E293B  

Success   : #10B981  
Warning   : #F59E0B  
Danger    : #EF4444  

White     : #FFFFFF
```

### Font
- **Inter** dari Google Fonts — import di layout utama
- H1: `text-4xl lg:text-5xl font-bold text-gray-900`
- Body: `text-base text-gray-600 leading-relaxed`

### Blade Components Wajib (x-*)
```
x-navbar              x-footer           x-course-card
x-bootcamp-card       x-book-card        x-membership-card
x-rating-stars        x-progress-bar     x-breadcrumb
x-section-header      x-alert            x-badge
x-avatar              x-price-display    x-skeleton-loader
```

### Format Harga (selalu gunakan helper ini)
```php
// AppServiceProvider atau helper:
function rupiah(int $amount): string {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}
// Output: Rp 299.000
```

### Format Tanggal (Indonesia)
```php
Carbon::setLocale('id');
$date->translatedFormat('d F Y'); // → "15 Januari 2025"
```

---

## 🔒 KEAMANAN (WAJIB DITERAPKAN)

### Middleware Security Headers
```php
// app/Http/Middleware/SecurityHeaders.php
$response->headers->set('X-Frame-Options', 'SAMEORIGIN');
$response->headers->set('X-Content-Type-Options', 'nosniff');
$response->headers->set('X-XSS-Protection', '1; mode=block');
$response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
// Daftarkan di bootstrap/app.php
```

### Rate Limiting (routes/web.php atau AppServiceProvider)
```php
RateLimiter::for('login',    fn() => Limit::perMinute(5)->by(request()->ip()));
RateLimiter::for('register', fn() => Limit::perMinute(3)->by(request()->ip()));
RateLimiter::for('checkout', fn() => Limit::perMinute(10)->by(auth()->id()));
```

### Aturan Keamanan
- SELALU gunakan `{{ }}` untuk output user, JANGAN `{!! !!}` kecuali konten admin
- SELALU gunakan FormRequest untuk validasi — jangan validate() langsung di controller
- JANGAN gunakan raw SQL dengan input user — gunakan Eloquent atau binding
- File upload: validasi MIME server-side, simpan di luar `public/`, hash nama file
- `APP_DEBUG=false` di production — TIDAK boleh true
- Semua secret di `.env` — TIDAK boleh hardcode

---

## 🔍 SEO

### Penggunaan SEOTools
```php
// Di setiap controller method:
SEOMeta::setTitle($course->meta_title ?? $course->title . ' | Skolah.com');
SEOMeta::setDescription($course->meta_description ?? substr(strip_tags($course->description), 0, 160));
OpenGraph::setImage($course->thumbnail_url);
OpenGraph::addProperty('type', 'website');
```

### JSON-LD untuk Course (di blade course/show.blade.php)
```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Course",
  "name": "{{ $course->title }}",
  "description": "{{ Str::limit(strip_tags($course->description), 200) }}",
  "provider": { "@type": "Organization", "name": "Skolah.com", "url": "{{ config('app.url') }}" },
  "offers": { "@type": "Offer", "price": "{{ $course->price }}", "priceCurrency": "IDR" },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "{{ $course->rating }}",
    "ratingCount": "{{ $course->rating_count }}"
  }
}
</script>
```

---

## 🌱 SEED DATA REFERENSI

### Akun Default (untuk development/testing)
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@skolah.com | Admin@123456 |
| Instructor 1 | budi@skolah.com | Instructor@123 |
| Instructor 2 | sari@skolah.com | Instructor@123 |
| Instructor 3 | ahmad@skolah.com | Instructor@123 |
| User biasa | user1@skolah.com | User@123456 |

### Urutan Seeder
```php
// database/seeders/DatabaseSeeder.php
$this->call([
    UserSeeder::class,
    CategorySeeder::class,
    CourseSeeder::class,      // include sections, lessons, enrollments
    BootcampSeeder::class,
    BookSeeder::class,
    MembershipSeeder::class,
    BannerSeeder::class,
    TestimonialSeeder::class,
    PromoCodeSeeder::class,
]);
```

### Promo Code Default
| Kode | Tipe | Nilai | Min. Beli |
|------|------|-------|-----------|
| SKOLAH20 | percent | 20% | Rp 100.000 |
| NEWMEMBER | fixed | Rp 50.000 | Rp 200.000 |
| BELAJAR10 | percent | 10% | - |

---

## ⚙️ SHARED HOSTING NOTES

```
Document Root HARUS diarahkan ke: /public_html/public/
Bukan: /public_html/

Queue driver: database (BUKAN redis)
Cache driver: file (BUKAN redis/memcached)
Session driver: file

Cron job di cPanel (setiap menit):
/usr/local/bin/php /home/username/public_html/artisan schedule:run >> /dev/null 2>&1



## 🎬 YOUTUBE VIDEO HELPER

```php
// Ambil video ID dari berbagai format URL YouTube
function getYoutubeId(string $url): ?string {
    preg_match('/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/', $url, $m);
    return $m[1] ?? null;
}

// Embed URL
$embedUrl = 'https://www.youtube.com/embed/' . getYoutubeId($lesson->video_url) . '?rel=0&modestbranding=1';

// Di Blade:
// <iframe src="{{ $embedUrl }}" class="w-full aspect-video" allowfullscreen loading="lazy"></iframe>
```

---

## 📋 HALAMAN YANG HARUS ADA (52 Total)

### Public (17 halaman)
`/` · `/courses` · `/courses/{slug}` · `/bootcamps` · `/bootcamps/{slug}`
`/books` · `/books/{slug}` · `/membership` · `/instructors/{username}`
`/search` · `/login` · `/register` · `/forgot-password`
`/about` · `/contact` · `/blog` · `/terms` · `/privacy`

### User Dashboard (9 halaman)
`/dashboard` · `/dashboard/my-courses` · `/learn/{course-slug}`
`/dashboard/my-bootcamps` · `/dashboard/my-books` · `/dashboard/certificates`
`/dashboard/membership` · `/dashboard/orders` · `/dashboard/settings`

### Checkout (4 halaman)
`/cart` · `/checkout` · `/checkout/payment` · `/checkout/success` · `/checkout/failed`

### Instructor Panel (8 halaman)
`/instructor/dashboard` · `/instructor/courses` · `/instructor/courses/create`
`/instructor/courses/{id}/edit` · `/instructor/courses/{id}/lessons`
`/instructor/bootcamps` · `/instructor/books` · `/instructor/earnings`

### Admin Panel (13 halaman)
`/admin/dashboard` · `/admin/users` · `/admin/courses` · `/admin/bootcamps`
`/admin/books` · `/admin/orders` · `/admin/memberships` · `/admin/categories`
`/admin/banners` · `/admin/promo-codes` · `/admin/testimonials`
`/admin/settings` · `/admin/analytics`

---

## 💡 CARA GUNAKAN FILE INI DENGAN COPILOT / CLAUDE

File ini sudah otomatis dibaca oleh GitHub Copilot di VS Code.
Untuk Claude AI atau Cursor, paste konten file ini sebagai konteks awal.

### Contoh prompt yang bisa langsung digunakan:

```
@workspace Buatkan CourseController.php lengkap sesuai instruksi project ini,
termasuk index (dengan filter), show, dan halaman learning room.
Gunakan eager loading untuk menghindari N+1.
```

```
@workspace Buatkan MidtransService.php dengan method createSnapToken dan
verifySignature sesuai konfigurasi project ini.
```

```
@workspace Buatkan migration untuk semua tabel course (courses, course_sections,
course_lessons, course_enrollments, lesson_progress, certificates) sesuai schema di atas.
```

```
@workspace Buatkan Blade component x-course-card dengan desain Tailwind
sesuai color palette dan typography di design system project ini.
```

```
@workspace Buatkan DatabaseSeeder dengan semua seeder untuk Skolah.com
menggunakan data seed yang sudah didefinisikan di instruksi ini.
```

---

*Skolah.com — Platform Edukasi Digital Terlengkap di Indonesia*
*File ini di-maintain di: .github/copilot-instructions.md*
