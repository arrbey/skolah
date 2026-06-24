---
name: session-bridge
description: Jembatan antar session — pastikan AI baru langsung paham tanpa perlu onboarding ulang
---

# Session Bridge Agent

## Masalah yang Diselesaikan
Setiap session baru = AI "lupa" segalanya = buang banyak token untuk re-explain = cepat limit.

## Solusi: Session Handoff Protocol

### Template HANDOFF.md
Buat file ini di root project dan update setelah tiap session:

```markdown
# SESSION HANDOFF
generated: [timestamp]
tokens_saved_estimate: [N]

## TL;DR (50 kata max)
[Ringkasan proyek dalam 1-2 kalimat]

## SEDANG DIKERJAKAN
Task: [nama task]
File: [file yang relevan]
Baris: [nomor baris jika ada]
Problem: [masalah jika ada]

## SUDAH SELESAI (ringkas)
- auth system (JWT, bcrypt)
- user CRUD API
- dashboard UI

## BELUM SELESAI
- [ ] payment integration (stripe, 60% done)
- [ ] email notifications
- [ ] deploy to production

## KEPUTUSAN TEKNIS PENTING
- Pakai PostgreSQL bukan MongoDB karena: relasi kompleks
- State management: Zustand bukan Redux karena: lebih simple
- Auth: JWT refresh token dengan 7 hari expiry

## FILE PENTING
- /src/config/db.ts — konfigurasi database
- /src/middleware/auth.ts — middleware autentikasi  
- /src/types/index.ts — semua TypeScript types

## JANGAN TANYA INI LAGI
- Tech stack sudah fix, tidak perlu saran ganti
- Tidak pakai TypeScript strict mode (sudah keputusan)
- Deploy ke Railway bukan Vercel (sudah keputusan)

## MULAI DARI SINI
[Instruksi spesifik untuk melanjutkan]
```

## Cara Pakai Session Baru
```
Baca HANDOFF.md dulu. Setelah paham, jawab: "Ready. Melanjutkan [task]."
Jangan tanya hal yang sudah ada di HANDOFF.md.
```

## Auto-Update
Selalu tambahkan ke akhir setiap response:
```
[SESSION UPDATE: task_selesai=X, next=Y, file_diubah=Z]
```
