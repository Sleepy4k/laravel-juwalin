# Panduan Instalasi — Laravel Sewa Hosting

Panduan lengkap untuk menginstal dan mengkonfigurasi aplikasi di lingkungan lokal maupun production.

---

## Persyaratan Sistem

| Kebutuhan         | Versi Minimum |
|-------------------|---------------|
| PHP               | 8.4           |
| Composer          | 2.x           |
| Node.js + Bun     | Node 20+      |
| MySQL / MariaDB   | 8.0+          |
| Laravel Herd      | *(opsional, untuk lokal)* |
| Proxmox VE        | 7.x / 8.x     |

---

## 1. Clone & Install Dependensi

```bash
git clone <repo-url> laravel-sewa-hosting
cd laravel-sewa-hosting

composer install
bun install        # atau: npm install
```

---

## 2. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Buka `.env` dan sesuaikan:

```dotenv
APP_NAME="Sewa Hosting"
APP_URL=https://laravel-sewa-hosting.test

DB_HOST=127.0.0.1
DB_DATABASE=sewa_hosting
DB_USERNAME=root
DB_PASSWORD=

# Proxmox VE
PROXMOX_HOST=https://your-proxmox-ip:8006
PROXMOX_USER=root@pam
PROXMOX_PASSWORD=your-password
PROXMOX_NODE=pve
PROXMOX_TEMPLATE_VMID=115

# Pakasir Payment Gateway
PAKASIR_PROJECT=your-project-slug
PAKASIR_API_KEY=your-api-key
PAKASIR_SANDBOX=true

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@sewaho.test
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 3. Database & Seeder

```bash
php artisan migrate --seed
```

Seeder yang dijalankan:
- `RolePermissionSeeder` — membuat role `admin` dan `user`
- `SiteSettingsSeeder` — mengisi nilai default pengaturan situs
- `DatabaseSeeder` — menjalankan semua seeder di atas

---

## 4. Build Frontend

```bash
# Development (hot-reload)
bun run dev        # atau: npm run dev

# Production build
bun run build      # atau: npm run build
```

---

## 5. Storage Link

```bash
php artisan storage:link
```

Diperlukan agar logo dan favicon yang di-upload dapat diakses secara publik.

---

## 6. Akun Admin Pertama

Setelah migrate & seed, buat akun admin via Tinker:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::create([
    'name'     => 'Admin',
    'email'    => 'admin@example.com',
    'password' => bcrypt('password'),
]);
$user->assignRole('admin');
```

Atau gunakan fitur **Tambah Admin** di panel admin setelah login pertama.

---

## 7. Konfigurasi Pakasir (Payment Gateway)

1. Daftar di [https://app.pakasir.com](https://app.pakasir.com)
2. Buat project baru dan catat **Project Slug** dan **API Key**
3. Isi `PAKASIR_PROJECT` dan `PAKASIR_API_KEY` di `.env`
4. Set URL webhook di dashboard Pakasir ke: `https://yourdomain.com/webhooks/pakasir`
5. Untuk testing, aktifkan `PAKASIR_SANDBOX=true`

---

## 8. Konfigurasi Proxmox VE

Aplikasi menggunakan Proxmox API untuk provisioning LXC Container secara otomatis.

- Buat user API di Proxmox dengan permission `VM.Clone`, `VM.Config.*`, `VM.PowerMgmt`
- Siapkan template LXC (catat VMID-nya untuk `PROXMOX_TEMPLATE_VMID`)
- Pastikan node Proxmox dapat diakses dari server Laravel

---

## 9. Queue Worker

Untuk email dan provisioning container berjalan di background:

```bash
php artisan queue:work --sleep=3 --tries=3
```

Atau dengan Supervisor di production:

```ini
[program:laravel-worker]
command=php /path/to/app/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
```

---

## 10. Menjalankan Tests

```bash
php artisan test --compact
```

---

## Struktur Direktori Penting

```
app/
  Enums/          — Status enums (OrderStatus, PaymentStatus, dll.)
  Http/
    Controllers/
      Admin/      — Controller panel admin
      Auth/       — Autentikasi
      Payment/    — Webhook Pakasir
      Portal/     — Portal pelanggan
  Jobs/           — ProvisionContainerJob
  Mail/           — Email notifikasi
  Models/         — Eloquent models
  Services/       — ProxmoxApiService, PakasirService
  Settings/       — SiteSettings (spatie/laravel-settings)
database/
  migrations/     — Skema database
  seeders/        — Data awal
resources/views/
  admin/          — Views panel admin
  auth/           — Views login/register
  components/     — Layouts & UI components
  emails/         — Template email
  portal/         — Views portal pelanggan
  public/         — Views halaman publik
```
