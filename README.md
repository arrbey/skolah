# Skolah.com — Platform Edukasi Digital Terlengkap di Indonesia

Skolah.com adalah platform EdTech all-in-one untuk kursus online, bootcamp/webinar, toko buku, membership, sertifikat, checkout, dan pembayaran Midtrans.

> Stack: Laravel 12 · MySQL 8 · Blade · Livewire 4 · Alpine.js · Tailwind CSS 4 · Vite
---

## Requirements

| Komponen | Versi minimum |
| --- | --- |
| PHP | 8.2+ |
| MySQL | 8.0+ |
| Composer | 2.x |
| Node.js | 20+ direkomendasikan |
| NPM | 10+ direkomendasikan |
| Apache | `mod_rewrite` aktif |

---

## Instalasi Development

```bash
git clone https://github.com/yourorg/skolah.git
cd skolah
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
```

Jalankan server lokal:

```bash
php artisan serve
```

Atau gunakan script development:

```bash
composer run dev
```

---

## Contoh `.env` Development

Jangan commit file `.env` ke GitHub. Gunakan `.env.example` untuk template publik.

```env
APP_NAME="Skolah.com"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_skolah
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
QUEUE_CONNECTION=database
CACHE_STORE=file

MIDTRANS_SERVER_KEY=Mid-server-xxxx
MIDTRANS_CLIENT_KEY=Mid-client-xxxx
MIDTRANS_MERCHANT_ID=Gxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IP_WHITELIST=false
```

---

## Deployment Shared Hosting / cPanel

### 1. Upload project

Upload project ke:

```text
/home/username/public_html
```

Jangan upload:

```text
.env
node_modules
vendor
storage/logs/*.log
```

Catatan: `public/build` boleh ikut GitHub/deploy agar shared hosting tidak perlu menjalankan `npm install` dan `npm run build`.

### 2. Document root

Set document root domain ke:

```text
/home/username/public_html/public
```

Jangan arahkan domain ke root project.

### 3. Install dependency production

```bash
cd ~/public_html
composer install --no-dev --optimize-autoloader
```

Jika asset belum tersedia:

```bash
npm ci
npm run build
```

### 4. `.env` production

Buat `.env` langsung di server. Jangan commit ke GitHub.

```env
APP_NAME="Skolah.com"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://skolah.com
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_db
DB_USERNAME=nama_user
DB_PASSWORD=password_db

SESSION_DRIVER=file
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
QUEUE_CONNECTION=database
CACHE_STORE=file

MIDTRANS_SERVER_KEY=Mid-server-xxxx
MIDTRANS_CLIENT_KEY=Mid-client-xxxx
MIDTRANS_MERCHANT_ID=Gxxxxxxxxx
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_IP_WHITELIST=true
```

### 5. Migrasi, link storage, cache

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 6. Cron job cPanel

```cron
* * * * * /usr/local/bin/php /home/username/public_html/artisan schedule:run >> /dev/null 2>&1
```

### 7. Queue worker

Project memakai database queue. Jalankan queue worker via Supervisor jika tersedia, atau cron cPanel jika shared hosting terbatas.

```bash
php artisan queue:work --tries=3 --timeout=120
```

---

## Midtrans

Webhook production yang harus didaftarkan di dashboard Midtrans:

```text
https://skolah.com/api/midtrans/webhook
```

Webhook development/ngrok contoh:

```text
https://your-ngrok-domain.ngrok-free.dev/api/midtrans/webhook
```

Catatan keamanan:

- Production: `MIDTRANS_IP_WHITELIST=true`
- Ngrok/sandbox: `MIDTRANS_IP_WHITELIST=false`
- Webhook diverifikasi dengan signature SHA-512
- Amount order diverifikasi sebelum fulfillment


## Fitur Utama

- LMS kursus video, progress belajar, review, sertifikat PDF
- Bootcamp/webinar online-offline dengan tiket digital
- Book store untuk buku digital/fisik
- Membership bulanan/tahunan
- Cart, promo code, checkout
- Midtrans Snap payment
- Multi-role admin, instructor, user via Spatie Permission
- SEO meta, OpenGraph, sitemap, robots, JSON-LD
- Backup database/file ke storage remote

---

## Package Utama

| Package | Kegunaan |
| --- | --- |
| `laravel/framework` | Core framework |
| `livewire/livewire` | Reactive UI |
| `spatie/laravel-permission` | Role & permission |
| `spatie/laravel-honeypot` | Anti-spam form |
| `spatie/laravel-backup` | Backup |
| `artesaos/seotools` | SEO meta |
| `barryvdh/laravel-dompdf` | PDF certificate |
| `midtrans/midtrans-php` | Payment gateway |
| `league/flysystem-aws-s3-v3` | S3/MinIO storage |
| `resend/resend-php` | Email delivery |

---

## Security Checklist Production

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://skolah.com`
- `.env` tidak masuk GitHub
- Document root mengarah ke folder `public`
- HTTPS aktif
- `SESSION_SECURE_COOKIE=true`
- `MIDTRANS_IP_WHITELIST=true`
- Backup archive password diisi
- Secret/API key tidak ditulis di README, issue, commit, atau screenshot publik
- Jalankan audit dependency berkala:

```bash
composer audit
npm audit --audit-level=moderate
```

---

## Artisan Commands

```bash
# Clear cache
php artisan optimize:clear

# Build cache production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Queue
php artisan queue:work --tries=3 --timeout=120
php artisan queue:failed
php artisan queue:restart

# Test
php artisan test
```

---

## Troubleshooting

| Masalah | Solusi |
| --- | --- |
| 500 error | Cek `storage/logs/laravel.log`, lalu `php artisan optimize:clear` |
| Asset/CSS tidak muncul | Pastikan `public/build` ada atau jalankan `npm run build` |
| Storage image tidak muncul | Jalankan `php artisan storage:link` |
| Webhook Midtrans gagal | Pastikan URL `/api/midtrans/webhook`, method POST, domain HTTPS, config cache clear |
| Config `.env` tidak terbaca | Jalankan `php artisan config:clear` |
| Queue tidak jalan | Jalankan `php artisan queue:work` atau setup cron/Supervisor |

---

## Lisensi

MIT

---

Skolah.com — Platform Edukasi Digital Terlengkap di Indonesia
