# Security Audit Report — Skolah.com

Tanggal: 2026-06-24
Scope: Laravel app config, routes, auth/RBAC, payment webhook, upload, backup, headers, dependency advisories.

## Critical

1. Secrets exposed in local `.env`
   - Active keys found: `APP_KEY`, Resend API key, AWS secret, Midtrans keys, Pusher secret, TinyMCE key.
   - Risk: leaked repo/workstation backup/log/share → account takeover, payment/API abuse.
   - Fix: rotate all exposed secrets before production; ensure `.env` never committed/uploaded outside server.

2. Production-unsafe environment values in `.env`
   - `APP_ENV=local`, `APP_DEBUG=true`.
   - Risk: stack trace/env disclosure if deployed as-is.
   - Fix prod: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://skolah.com`.

3. Midtrans webhook lacks route-level hardening
   - Route: `POST api/midtrans/webhook` middleware only `api`.
   - Missing: `midtrans_ip`, `throttle`, `audit`.
   - Controller verifies signature + amount (good), but endpoint remains brute-force/log-noise target.
   - Fix: `Route::post('/midtrans/webhook', MidtransWebhookController::class)->middleware(['throttle:webhook','midtrans_ip'])->name(...)`.

4. Trusted proxy fallback accepts all proxies
   - `bootstrap/app.php` fallback uses `*` when `TRUSTED_PROXIES` empty.
   - Risk: spoofed `X-Forwarded-For` affects rate limits/audit/IP allowlists.
   - Fix: in production require explicit proxy IPs; otherwise trust none.

## High

5. Dependency vulnerabilities
   - `composer audit`: 21 advisories / 13 packages.
   - High: `laravel/framework`, `phpseclib/phpseclib`, `symfony/mime`; multiple medium Symfony/Guzzle advisories.
   - `npm audit --omit=dev`: 0 vulnerabilities.
   - Fix: run `composer update laravel/framework symfony/* guzzlehttp/* phpseclib/phpseclib mtdowling/jmespath.php --with-all-dependencies`, retest.

6. Backup ZIP encryption unset
   - `.env`: `BACKUP_ARCHIVE_PASSWORD=` blank.
   - `config/backup.php` includes `.env` in backups.
   - Risk: backup compromise exposes all secrets + DB.
   - Fix: set strong `BACKUP_ARCHIVE_PASSWORD`; restrict admin download; rotate old unencrypted backups.

7. Payment service returns redirect URL, dead token return
   - `MidtransService::createSnapToken()` saves token but returns `$redirectUrl`; `return $token` unreachable.
   - Risk: callers expecting Snap token may mis-handle payment flow; security confusion around client token exposure.
   - Fix: split methods or rename return contract (`createSnapRedirectUrl`) consistently.

8. Webhook logs raw request body
   - `MidtransWebhookController` logs `$request->all()` + raw body.
   - Risk: transaction IDs/signatures/customer data retained in logs.
   - Fix: log minimal fields; redact `signature_key`, tokens, raw body.

## Medium

9. Email verification route auto-logins by signed link
   - `/email/verify/{id}/{hash}` logs user in if signature valid.
   - Risk: forwarded/leaked email verification link becomes login link until expiry.
   - Fix: verify email only; require login after verification, or very short expiry + one-time token.

10. CSP weakened by inline handlers + `unsafe-eval`
   - Views use inline `onclick`; CSP script-src allows `unsafe-eval`.
   - Risk: XSS blast radius higher.
   - Fix: remove inline handlers; use nonce scripts/listeners; remove `unsafe-eval` if Livewire/Alpine compatibility allows.

11. Raw Blade output in CMS/home/blog/bundle views
   - Examples: home settings titles/descriptions, gallery title, bootcamp/program content, blog post, bundle description.
   - Some input sanitized, but risk depends on all admin/instructor content paths.
   - Fix: enforce HTML sanitizer on save for every rich-text field; keep `{!! !!}` only for trusted sanitized HTML.

12. Upload temp filename includes original name
   - `Instructor\LessonController` stores temp video as random prefix + original filename.
   - Risk: weird Unicode/control names, PII leakage in temp paths/logs, path edge cases.
   - Fix: use hash-only filename + extension from server MIME validation.

13. API webhook GET health endpoint discloses env/IP
   - `GET api/midtrans/webhook` returns `env` in controller health path too if GET hits controller; route closure returns status.
   - Risk: unnecessary service fingerprinting.
   - Fix: remove public GET webhook route or return only `ok`.

14. `.htaccess` production HTTPS/session flags disabled
   - HTTPS redirect and HSTS commented; `session.cookie_secure Off` in Apache block.
   - Laravel config sets secure cookies true, but Apache PHP setting conflicts in some environments.
   - Fix prod: enable HTTPS redirect/HSTS; set cookie secure On.

## Positive findings

- Auth login regenerates session; failed login generic message.
- Forgot password avoids email enumeration.
- RBAC present on user/instructor/admin groups.
- Many owner checks via policy/scoped queries.
- Webhook validates signature and amount before success processing.
- Session config uses encryption, httpOnly, secure, SameSite lax, JSON serialization.
- Security headers middleware present with CSP, frame, nosniff, referrer, permissions, HSTS non-local.
- Backup admin routes protected by auth/verified/admin/audit/admin_idle.
- Book/certificate downloads use policy/signed object URLs/controlled streaming.

## Priority fix order

1. Rotate secrets; set production `.env`; encrypt backups.
2. Patch Composer dependencies.
3. Harden webhook middleware + trusted proxy config.
4. Redact webhook/payment logs.
5. Remove email verification auto-login.
6. Clean CSP inline handlers/raw HTML surfaces.
7. Hash upload temp filenames.
8. Enable HTTPS/HSTS/cookie secure in production server config.
