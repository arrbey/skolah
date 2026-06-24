# Dokumentasi Teknis — Skolah.com

**Versi:** 1.0  
**Tanggal:** Maret 2026  
**Stack:** Laravel 13 · MySQL 8 · Livewire 3 · Alpine.js · Tailwind CSS 3

---

## 1. Gambaran Umum

Skolah.com adalah platform EdTech (Education Technology) all-in-one Indonesia yang menyediakan:

| Modul | Deskripsi |
|-------|-----------|
| **LMS (Kursus)** | Video course berjenjang dengan tracking progress dan sertifikat |
| **Bootcamp/Webinar** | Event pembelajaran online/offline dengan tiket digital |
| **Book Store** | Toko buku digital (PDF) dan fisik |
| **Membership** | Langganan premium (bulanan/tahunan) untuk akses unlimited |
| **Payment** | Integrasi Midtrans Snap untuk semua transaksi |

---

## 2. Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────┐
│                     Browser / Client                    │
│         (Tailwind CSS · Alpine.js · Livewire 3)         │
└──────────────────────┬──────────────────────────────────┘
                       │ HTTP/HTTPS
┌──────────────────────▼──────────────────────────────────┐
│               Apache (Shared Hosting)                   │
│                  public/.htaccess                       │
│              Document Root: /public/                    │
└──────────────────────┬──────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────┐
│                  Laravel 13 Application                 │
│                                                         │
│  Middleware Stack:                                      │
│  SecurityHeaders → CSRF → Auth → Role → Controller     │
│                                                         │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐             │
│  │Controllers│  │ Livewire │  │ Services │             │
│  └──────────┘  └──────────┘  └──────────┘             │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐             │
│  │  Models  │  │FormReq.  │  │ Observers│             │
│  └──────────┘  └──────────┘  └──────────┘             │
└──────────────────────┬──────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────┐
│              MySQL 8 Database (db_skolah)                │
└─────────────────────────────────────────────────────────┘
                       │
         ┌─────────────┼─────────────┐
┌────────▼────┐  ┌─────▼─────┐  ┌───▼────────┐
│  Midtrans   │  │  Storage  │  │  Mail/SMTP │
│  (Payment)  │  │ (Files)   │  │  (Queue)   │
└─────────────┘  └───────────┘  └────────────┘
```

---

## 3. Struktur Role & Akses

### Role Hierarchy

```
admin
  └── Full access: semua fitur + konfigurasi sistem

instructor
  └── Kelola konten: kursus, bootcamp, buku milik sendiri
  └── Lihat earnings & statistik

user (default)
  └── Beli, belajar, download sertifikat
  └── Kelola profil & order
```

### Middleware yang Digunakan

| Middleware | Kelas | Fungsi |
|-----------|-------|--------|
| `auth` | Laravel built-in | Wajib login |
| `verified` | Laravel built-in | Email sudah terverifikasi |
| `role:admin` | Spatie Permission | Hanya admin |
| `role:instructor` | Spatie Permission | Hanya instruktur |
| `membership` | `CheckMembership` | Cek membership aktif |
| `enrolled` | `CheckEnrollment` | Cek sudah enroll kursus |
| `throttle:login` | Laravel built-in | Rate limit 5/mnt |
| `throttle:checkout` | Laravel built-in | Rate limit 10/mnt |

---

## 4. Database Schema

### Tabel Utama

#### `users`
```
id, name, email, password, avatar, bio, role,
is_verified, email_verified_at, remember_token, timestamps
```

#### `courses`
```
id, instructor_id(FK), title, slug, description, thumbnail,
price, discount_price, level(beginner|intermediate|advanced),
language, status(draft|published), is_featured,
total_students, rating, rating_count,
meta_title, meta_description, timestamps
```

#### `course_sections`
```
id, course_id(FK), title, order, timestamps
```

#### `course_lessons`
```
id, section_id(FK), title, video_url, video_duration,
content, order, is_free_preview, is_published, timestamps
```

#### `course_enrollments`
```
id, user_id(FK), course_id(FK), enrolled_at,
completed_at, progress_percentage, timestamps
```

#### `lesson_progress`
```
id, user_id(FK), lesson_id(FK), is_completed, watched_at, timestamps
```

#### `certificates`
```
id, user_id(FK), course_id(FK), certificate_number(unique),
issued_at, file_path, timestamps
```

#### `bootcamps`
```
id, instructor_id(FK), title, slug, description, thumbnail,
price, discount_price, type(online|offline),
platform, meeting_link, location,
start_date, end_date, max_participants, total_registered,
status(upcoming|ongoing|completed), timestamps
```

#### `books`
```
id, instructor_id(FK), title, slug, description, cover_image,
price, discount_price, type(physical|digital|both), stock,
file_path, isbn, author, publisher, pages,
status(draft|published), timestamps
```

#### `orders`
```
id, user_id(FK), order_number(unique), subtotal, discount_amount,
total, status(pending|paid|failed|refunded), payment_method,
midtrans_transaction_id, midtrans_snap_token, paid_at, timestamps
```

#### `order_items`
```
id, order_id(FK), itemable_type, itemable_id,
item_name, price, quantity, timestamps
```

#### `membership_plans`
```
id, name, slug, description, price_monthly, price_yearly,
features(JSON), is_popular, is_active, timestamps
```

#### `user_memberships`
```
id, user_id(FK), plan_id(FK), started_at, expires_at,
billing_cycle(monthly|yearly), status(active|expired|cancelled), timestamps
```

---

## 5. Alur Bisnis

### 5.1 Pembelian Kursus

```
1. User browse /courses → klik kursus
2. Halaman /courses/{slug} → klik "Beli Sekarang"
3. POST /cart/add → tambah ke cart
4. GET /cart → review cart, apply promo code
5. GET /checkout → konfirmasi total
6. POST /checkout/process:
   a. Buat Order (status: pending)
   b. Buat OrderItem (itemable: Course)
   c. MidtransService::createSnapToken()
   d. Simpan snap_token ke order
7. Frontend: window.snap.pay(snapToken, callbacks)
8. Midtrans POST /midtrans/webhook:
   a. verifySignature() → 403 jika invalid
   b. Update order status → paid
   c. CourseEnrollmentService::enroll(user, course)
   d. Kirim email konfirmasi (queue)
9. User redirect ke /checkout/success
10. User bisa akses /learn/{course-slug}
```

### 5.2 Generate Sertifikat

```
1. User selesaikan semua lesson (progress = 100%)
2. GET /dashboard/certificates → klik "Download"
3. CertificateController::download():
   a. Cek enrollment completed
   b. Cek/buat record di tabel certificates
   c. Generate certificate_number = "SKOL-{YEAR}-{000001}"
   d. DomPDF render pdf/certificate.blade.php (A4 landscape)
   e. Simpan ke storage/app/certificates/{user_id}/
4. Return response()->download()
```

### 5.3 Bootcamp Registration

```
1. User buka /bootcamps/{slug}
2. Klik "Daftar Sekarang" → POST /bootcamp/checkout/process
3. Jika gratis → langsung BootcampRegistrationService::register()
4. Jika berbayar → buat Order + Midtrans Snap
5. Setelah webhook paid → buat BootcampRegistration
6. Kirim ticket_code via email
```

### 5.4 Membership

```
1. User buka /membership
2. Pilih plan + siklus (monthly/yearly)
3. POST /membership/subscribe → buat Order + Midtrans Snap
4. Setelah webhook paid → MembershipService::activate()
5. Buat UserMembership (started_at + expires_at)
6. Akses konten premium aktif
```

---

## 6. Struktur Controller

### Public Controllers

| Controller | Route | Fungsi |
|-----------|-------|--------|
| `HomeController` | `/` | Halaman beranda |
| `CourseController` | `/courses` | Listing & detail kursus |
| `BootcampController` | `/bootcamps` | Listing & detail bootcamp |
| `BookController` | `/books` | Listing & detail buku |
| `MembershipController` | `/membership` | Halaman & subscribe membership |
| `LearnController` | `/learn/{slug}` | Learning room (auth) |
| `CertificateController` | `/dashboard/certificates` | Download sertifikat |
| `CartController` | `/cart` | Keranjang belanja |
| `CheckoutController` | `/checkout` | Checkout umum |
| `BookCheckoutController` | `/book/checkout` | Checkout buku |
| `BootcampCheckoutController` | `/bootcamp/checkout` | Checkout bootcamp |
| `MidtransWebhookController` | `/midtrans/webhook` | Webhook Midtrans |
| `SitemapController` | `/sitemap.xml` | Generate sitemap |

### Instructor Controllers (`/instructor/*`)

| Controller | Fungsi |
|-----------|--------|
| `DashboardController` | Dashboard statistik |
| `CourseController` | CRUD kursus |
| `LessonController` | Kelola section & lesson |
| `BootcampController` | CRUD bootcamp |
| `BookController` | CRUD buku |
| `EarningController` | Laporan penghasilan |

### Admin Controllers (`/admin/*`)

| Controller | Fungsi |
|-----------|--------|
| `DashboardController` | Analytics & statistik |
| `UserController` | Kelola user |
| `CourseController` | Moderasi kursus |
| `OrderController` | Kelola order |
| `CategoryController` | Kelola kategori |
| `BannerController` | Kelola banner |
| `PromoCodeController` | Kelola kode promo |
| `MembershipController` | Kelola paket membership |
| `SettingController` | Pengaturan sistem |
| `AnalyticsController` | Laporan & analytics |

---

## 7. Livewire Components

| Komponen | File | Fungsi |
|---------|------|--------|
| `CourseFilter` | `CourseFilter.php` | Filter kursus real-time |
| `BootcampFilter` | `BootcampFilter.php` | Filter bootcamp real-time |
| `BookFilter` | `BookFilter.php` | Filter buku real-time |
| `CartCount` | `CartCount.php` | Badge jumlah item di cart |
| `LessonProgressComponent` | `LessonProgressComponent.php` | Toggle progress lesson |
| `PriceToggle` | `PriceToggle.php` | Toggle harga bulanan/tahunan |
| `ApplyPromoCode` | `ApplyPromoCode.php` | Validasi & apply promo code |

---

## 8. Services

### `MidtransService`

```php
createSnapToken(Order $order): string     // Buat Snap token
verifySignature(array $payload): bool     // Verifikasi webhook SHA-512
isPaymentSuccess(array $payload): bool    // Cek status sukses
isPaymentFailed(array $payload): bool     // Cek status gagal
```

### `CourseEnrollmentService`

```php
enroll(User $user, Course $course): CourseEnrollment
isEnrolled(User $user, Course $course): bool
updateProgress(User $user, Course $course): void
```

### `CertificateService`

```php
generate(User $user, Course $course): Certificate
// Render DomPDF, simpan ke storage, return Certificate model
```

### `BootcampRegistrationService`

```php
register(User $user, Bootcamp $bootcamp): BootcampRegistration
generateTicketCode(): string
```

### `MembershipService`

```php
activate(User $user, MembershipPlan $plan, string $cycle, Order $order): UserMembership
getActiveMembership(int $userId): ?UserMembership
isActive(int $userId): bool
```

---

## 9. SEO Implementation

### Controller-level SEO (semua public controller)

```php
SEOMeta::setTitle($title);
SEOMeta::setDescription($description);
SEOMeta::setKeywords([...]);
SEOMeta::setCanonical($url);
SEOMeta::addMeta('robots', 'index, follow');

OpenGraph::setTitle($title);
OpenGraph::setDescription($description);
OpenGraph::addImage($imageUrl, ['width' => 1200, 'height' => 630]);
OpenGraph::addProperty('type', 'website');
OpenGraph::setUrl($url);
OpenGraph::setSiteName('Skolah.com');

TwitterCard::setType('summary_large_image');
TwitterCard::setTitle($title);
TwitterCard::setDescription($description);
TwitterCard::setImage($imageUrl);
```

### JSON-LD (course/show.blade.php)

Schema.org `@type: Course` dengan:
- `educationalLevel`, `teaches`, `keywords`
- `hasCourseInstance` (mode online)
- `aggregateRating` (jika ada review)
- `offers` dengan harga IDR

### Sitemap (`/sitemap.xml`)

Auto-generated via `SitemapController`:
- Static pages (home, courses, bootcamps, books, membership)
- Semua published courses
- Semua upcoming/ongoing bootcamps
- Semua published books

### robots.txt

```
Disallow: /admin/, /instructor/, /dashboard/, /cart, /checkout, /learn/, /midtrans/
Sitemap: https://skolah.com/sitemap.xml
```

---

## 10. Security Checklist

| Aspek | Status | Detail |
|-------|--------|--------|
| Security Headers | ✅ | `SecurityHeaders` middleware global |
| CSRF Protection | ✅ | Semua form POST/PUT/DELETE |
| FormRequest Validation | ✅ | Semua input user |
| Rate Limiting | ✅ | Login, Register, Checkout |
| Webhook Signature | ✅ | SHA-512 verify sebelum proses |
| APP_DEBUG | ✅ | `false` di production |
| File Protection | ✅ | `.env`, `.log` diblokir via `.htaccess` |
| SQL Injection | ✅ | Eloquent ORM (no raw SQL) |
| XSS | ✅ | Blade `{{ }}` escape otomatis |
| Output Escaping | ✅ | `{!! !!}` hanya untuk konten admin |
| Password Hashing | ✅ | Bcrypt rounds: 12 |
| Session Security | ✅ | File driver, enkripsi payload |
| Directory Listing | ✅ | `Options -Indexes` di `.htaccess` |

---

## 11. Halaman yang Tersedia (52 Total)

### Public (18 halaman)
```
/                    Beranda
/courses             Daftar kursus (dengan filter)
/courses/{slug}      Detail kursus
/bootcamps           Daftar bootcamp
/bootcamps/{slug}    Detail bootcamp
/books               Toko buku
/books/{slug}        Detail buku
/membership          Paket membership
/search              Pencarian global
/login               Halaman login
/register            Halaman registrasi
/forgot-password     Lupa password
/about               Tentang kami
/contact             Kontak
/blog                Blog (placeholder)
/terms               Syarat & ketentuan
/privacy             Kebijakan privasi
/sitemap.xml         Sitemap (SEO)
```

### User Dashboard (9 halaman)
```
/dashboard                   Ringkasan
/dashboard/my-courses        Kursus saya
/learn/{slug}                Learning room
/dashboard/my-bootcamps      Bootcamp saya
/dashboard/my-books          Buku saya
/dashboard/certificates      Sertifikat
/dashboard/membership        Status membership
/dashboard/orders            Riwayat order
/dashboard/settings          Pengaturan akun
```

### Checkout (5 halaman)
```
/cart                   Keranjang belanja
/checkout               Konfirmasi checkout
/checkout/success       Pembayaran berhasil
/checkout/failed        Pembayaran gagal
/book/checkout/{slug}/shipping  Form alamat buku fisik
```

### Instructor Panel (8 halaman)
```
/instructor/dashboard
/instructor/courses
/instructor/courses/create
/instructor/courses/{id}/edit
/instructor/courses/{id}/lessons
/instructor/bootcamps
/instructor/books
/instructor/earnings
```

### Admin Panel (13 halaman)
```
/admin/dashboard
/admin/users
/admin/courses
/admin/bootcamps (via orders)
/admin/books (via orders)
/admin/orders
/admin/memberships
/admin/categories
/admin/banners
/admin/promo-codes
/admin/testimonials (via settings)
/admin/settings
/admin/analytics
```

---

## 12. Environment Variables Reference

```env
# Application
APP_NAME="Skolah.com"
APP_ENV=production           # local | production
APP_DEBUG=false              # WAJIB false di production
APP_URL=https://skolah.com
APP_TIMEZONE=Asia/Jakarta

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_skolah
DB_USERNAME=skolah_user
DB_PASSWORD=

# Midtrans
MIDTRANS_SERVER_KEY=Mid-server-xxx
MIDTRANS_CLIENT_KEY=Mid-client-xxx
MIDTRANS_MERCHANT_ID=Gxxxxxxx
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_SNAP_URL=https://app.midtrans.com/snap/snap.js

# Driver (shared hosting friendly)
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mail.skolah.com
MAIL_PORT=465
MAIL_USERNAME=noreply@skolah.com
MAIL_PASSWORD=
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@skolah.com
MAIL_FROM_NAME="Skolah.com"
```

---

## 13. Kode Promo Default

| Kode | Tipe | Nilai | Min. Beli |
|------|------|-------|-----------|
| `SKOLAH20` | Persentase | 20% | Rp 100.000 |
| `NEWMEMBER` | Fixed | Rp 50.000 | Rp 200.000 |
| `BELAJAR10` | Persentase | 10% | — |

---

## 14. Deployment Checklist

```
[ ] APP_DEBUG=false di .env
[ ] APP_ENV=production di .env
[ ] APP_URL=https://skolah.com
[ ] MIDTRANS_IS_PRODUCTION=true
[ ] composer install --no-dev --optimize-autoloader
[ ] php artisan migrate --force
[ ] php artisan storage:link
[ ] php artisan config:cache
[ ] php artisan route:cache
[ ] php artisan view:cache
[ ] php artisan event:cache
[ ] Cron job aktif di cPanel
[ ] Midtrans webhook URL terdaftar
[ ] Document Root → /public/
[ ] HTTPS aktif (aktifkan redirect di .htaccess)
[ ] Backup database terjadwal
```

---

*Skolah.com — Platform Edukasi Digital Terlengkap di Indonesia*  
*Dokumen ini terakhir diperbarui: Maret 2026*
