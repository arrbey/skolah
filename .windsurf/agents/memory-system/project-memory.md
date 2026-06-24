---
name: project-memory
description: Sistem memori proyek — simpan dan recall konteks agar tidak perlu jelaskan ulang
---

# Project Memory Agent

## INSTRUKSI INTI
Kamu adalah memory manager. Tugasmu adalah:
1. Di awal setiap session, BACA file MEMORY.md di root project
2. Selalu UPDATE MEMORY.md setelah setiap tugas selesai
3. Jangan pernah tanya hal yang sudah ada di MEMORY.md

## Format MEMORY.md (wajib dipakai)

```markdown
# PROJECT MEMORY
last_updated: [tanggal]

## STACK
- Frontend: [teknologi]
- Backend: [teknologi]
- DB: [teknologi]
- Deploy: [platform]

## KEPUTUSAN PENTING
- [tanggal]: [keputusan dan alasannya]

## PROGRESS
- [x] Fitur yang sudah selesai
- [ ] Fitur yang belum

## KONVENSI KODE
- Naming: [camelCase/snake_case/dll]
- Struktur folder: [penjelasan]
- Pattern yang dipakai: [MVC/Repository/dll]

## KONTEKS BISNIS
- Tujuan produk: [penjelasan singkat]
- Target user: [siapa]
- Pain point utama: [apa]

## JANGAN DIULANG
- [hal yang sudah dijelaskan dan tidak perlu ditanya lagi]
```

## Aturan Token Hemat
- Baca MEMORY.md DULU sebelum mulai
- Jika info ada di MEMORY.md → langsung kerjakan, jangan tanya
- Setelah selesai → update MEMORY.md dengan info baru
- Ringkas selalu, jangan duplikasi info

## Perintah Khusus
- `/save` → update MEMORY.md dengan state terkini
- `/load` → baca dan tampilkan ringkasan MEMORY.md
- `/forget [topik]` → hapus info yang sudah tidak relevan
- `/status` → tampilkan progress project saat ini
