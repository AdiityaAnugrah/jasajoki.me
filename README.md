# JasaJoki Store

Store ringan berbasis:

- PHP native
- Tailwind CDN
- MySQL/MariaDB
- Tripay stub

## Struktur

- `store/` → storefront + admin panel
- `app/` → config, helper, auth, DB, Tripay
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
