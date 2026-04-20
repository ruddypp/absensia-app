# Deploy Guide

Panduan singkat deploy `absensi-app` untuk server publik atau Dokploy.

## Environment Production

Gunakan env production seperti ini:

```env
APP_NAME="Absensi App"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://domainkamu.com
ASSET_URL=https://domainkamu.com

DB_CONNECTION=mysql
DB_HOST=mysql-service-name
DB_PORT=3306
DB_DATABASE=absensi_app
DB_USERNAME=absensi_user
DB_PASSWORD=password_db

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
```

Catatan:

- `APP_URL` dan `ASSET_URL` wajib `https`.
- App ini sudah diatur untuk percaya reverse proxy dan force `https` saat production.

## Perintah Setelah Deploy

Jalankan ini di container app:

```bash
cd /app
php artisan optimize:clear
php artisan storage:link
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Akun Demo Seeder

Semua password default:

```text
ganteng123
```

Akun:

- `reza@absensi.local` → role `super_admin`
- `nabila@absensi.local` → role `hrd`
- `dimas@absensi.local` → role `kepala_departemen`
- `sinta@absensi.local` → role `karyawan`

## Verifikasi Setelah Live

Pastikan:

1. Login page tampil normal.
2. CSS dan JS termuat lewat `https`.
3. Login dengan akun demo berhasil.
4. Dashboard terbuka.
5. Master data departemen, jabatan, lokasi kerja, jadwal, dan komponen gaji sudah terisi.
