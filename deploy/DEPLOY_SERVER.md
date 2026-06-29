# Deploy ke server

## Struktur target

- Root domain: `jasajoki.me`
- Store app: `/var/www/jasajoki.me/store`

## Secrets GitHub Actions yang harus diisi

- `VPS_HOST`
- `VPS_USER`
- `VPS_PORT`
- `VPS_SSH_KEY`
- `VPS_TARGET_DIR`

Contoh `VPS_TARGET_DIR`:

```txt
/var/www/jasajoki.me
```

## Langkah server

1. Buat folder:
   - `/var/www/jasajoki.me`
2. Copy file config Apache `deploy/apache-store-subdir.conf`
3. Pastikan `jasajoki.me` sudah mengarah ke server
4. Isi `.env` produksi dengan koneksi MariaDB dan kredensial Tripay
5. Jalankan workflow `Deploy to VPS`
