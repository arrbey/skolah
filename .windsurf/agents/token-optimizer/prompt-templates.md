---
name: prompt-templates
description: Kumpulan template prompt hemat token untuk berbagai skenario development
---

# Prompt Templates — Token Efficient

## Setup Awal Project (1x pakai, simpan ke MEMORY.md)
```
PROJECT BRIEF:
name: [nama]
type: [web/mobile/api/cli]
stack: [list teknologi]
db: [database]
auth: [metode auth]
deploy: [platform]
pattern: [arsitektur pattern]
team: [solo/tim]

KONVENSI:
naming: [camelCase/snake_case]
folder: [struktur]
test: [jest/vitest/none]

BISNIS:
goal: [tujuan 1 kalimat]
user: [target pengguna]
mvp_features: [fitur wajib MVP]
```

## Resume Session
```
[RESUME]
ref: MEMORY.md + HANDOFF.md
task: lanjutkan [nama task]
status: [sudah sampai mana]
[/RESUME]
```

## Request Fitur
```
feat: [nama fitur]
files: [file yang perlu dibuat/diubah]
deps: [dependensi yang dibutuhkan]
ref: [komponen/pattern yang sudah ada untuk dijadikan acuan]
```

## Debug Request  
```
bug: [1 baris deskripsi]
err: [pesan error lengkap]
file: [nama file:baris]
ctx: [konteks singkat]
tried: [sudah coba apa]
```

## Code Review
```
review: [file atau fungsi]
focus: [security/performance/readability/all]
skip: [hal yang tidak perlu direview]
```

## Refactor Request
```
refactor: [target]
reason: [mengapa]
keep: [apa yang tidak boleh diubah]
improve: [apa yang ingin diperbaiki]
```

## Database Query
```
query: [deskripsi apa yang ingin diambil]
tables: [tabel yang relevan]
condition: [filter/where clause]
output: [format output yang diinginkan]
```

## Deploy Request
```
deploy: [environment: dev/staging/prod]
platform: [Vercel/Railway/AWS/dll]
env_vars: [list variable yang perlu diset]
check: [hal yang perlu dicek sebelum deploy]
```
