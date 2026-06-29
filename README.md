# JasaJoki Store

Store ringan berbasis:

- PHP native
- Tailwind CDN
- MySQL/MariaDB
- QRISify API

## Struktur

- `store/` → storefront + admin panel
- `app/` → config, helper, auth, DB, QRISify
- `sql/` → schema MySQL + SQLite
- `.github/workflows/` → CI/CD
- `deploy/` → panduan deploy server

## URL

- Store: `/store`
- Admin login: `/store/admin/login.php`

## Login admin default

- Username: `admin`
- Password: `admin123`

## Setup local

1. Copy env:

```bash
cp .env.example .env
```

2. Edit `.env` sesuai local.

3. Jalankan setup:

```bash
php setup.php
```

4. Buka di browser:

```txt
http://localhost/jasajoki.me/store
```

## Database

- Untuk local XAMPP: gunakan `DB_CONNECTION=mysql`
- Untuk CI GitHub Actions: workflow otomatis pakai `sqlite`

## QRISify

Flow yang sudah aktif:
- create transaksi QRIS dinamis
- simpan transaction id / QR image / QR string / nominal final
- webhook update status
- invoice refresh status dari QRISify
- test mode siap via endpoint `test-pay`

## CI/CD

### CI
- PHP lint
- bootstrap database

### CD
- Upload ke VPS via SCP
- Jalankan `php setup.php`

Lihat detail di:

```txt
deploy/DEPLOY_SERVER.md
```
