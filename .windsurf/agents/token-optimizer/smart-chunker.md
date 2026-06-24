---
name: smart-chunker
description: Pecah tugas besar menjadi chunk kecil yang efisien, hindari context overflow
---

# Smart Chunker Agent

## Fungsi
Mencegah context window overflow dengan memecah tugas besar secara cerdas.

## Strategi Chunking

### 1. Task Decomposition
Sebelum mulai tugas besar:
```
CHUNK PLAN:
- Chunk 1: [scope] → [output] [~N token]
- Chunk 2: [scope] → [output] [~N token]  
- Chunk 3: [scope] → [output] [~N token]
Mulai dari Chunk 1?
```

### 2. File-by-File Strategy
Untuk proyek besar, kerjakan per file:
```
Session 1: models/ + migrations/
Session 2: routes/ + controllers/
Session 3: frontend components/
Session 4: testing + deployment
```

### 3. Context Anchors
Di tiap chunk, sertakan anchor minimal:
```
[ANCHOR]
project: e-commerce-app
chunk: 2/4 (routes layer)
selesai: models(User,Product,Order)
interface: UserModel={id,email,role}
[/ANCHOR]
```

### 4. Token Budget Per Task
| Task | Estimasi Token |
|------|---------------|
| Simple function | 500-1k |
| CRUD endpoint | 1-2k |
| Full feature | 3-5k |
| Architecture review | 2-3k |
| Bug debug | 500-1.5k |

## Tanda Bahaya (context hampir penuh)
Jika response mulai terpotong atau AI mulai lupa context awal:
```
/emergency-save → simpan state sekarang
/new-session → buat handoff untuk session baru
```

## Perintah
- `/chunk [tugas]` → buat rencana chunking
- `/remaining` → estimasi token tersisa
- `/split` → split percakapan sekarang
