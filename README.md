# Laravel Sewa Hosting

Platform sewa hosting berbasis LXC Container dengan integrasi Proxmox VE dan payment gateway Pakasir.

## Fitur Utama

- **Panel Admin** - Kelola produk/paket, order, pembayaran, pengguna, dan pengaturan situs
- **Portal Pelanggan** - Order container, monitoring status, manajemen port forwarding, riwayat billing
- **Provisioning Otomatis** - LXC Container dibuat otomatis via Proxmox API saat pembayaran terkonfirmasi
- **Payment Gateway Pakasir** - QRIS, Virtual Account BNI/BRI/BCA/CIMB/Sampoerna
- **Email Notifikasi** - Konfirmasi order, pembayaran, dan selesai provisioning
- **Activity Log** - Semua aksi admin tercatat dengan kategori
- **Keamanan** - CSP headers, rate limiting, Argon2id password hashing
- **SEO** - Open Graph, Twitter Card, meta tags dinamis

## Tech Stack

- **Backend**: Laravel 12, PHP 8.4
- **Frontend**: Tailwind CSS v4, Vite
- **Database**: MySQL
- **Payment**: Pakasir (QRIS / Virtual Account)
- **Infrastructure**: Proxmox VE (LXC Container)
- **Auth & Roles**: Spatie Permission v7
- **Settings**: Spatie Laravel Settings v3

## Instalasi

Lihat [INSTALLATION.md](INSTALLATION.md) untuk panduan lengkap.

## Testing

```bash
php artisan test --compact
```

## Lisensi

MIT
