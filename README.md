# PASAR — Visualisasi ISR Fixed Service PP
## Panduan Deploy ke Hostinger

### Struktur File
```
pasar_viz/
├── index.php           ← Halaman utama dashboard
├── config.php          ← Konfigurasi database (EDIT INI DULU)
├── db.php              ← Koneksi PDO (tidak perlu diubah)
├── .htaccess           ← Keamanan & performa Apache
└── api/
    ├── clients.php     ← Endpoint: daftar klien + statistik
    ├── isr.php         ← Endpoint: ISR per klien + grouping link
    └── link_detail.php ← Endpoint: detail satu link_id
```

---

### Langkah 1 — Siapkan Database di Hostinger
1. Login ke **hPanel Hostinger** → Databases → MySQL Databases
2. Buat database baru (catat nama, user, password)
3. Buka **phpMyAdmin** → import file `u781960094_pasar.sql`

### Langkah 2 — Edit `config.php`
```php
define('DB_HOST',    'localhost');          // Selalu localhost di Hostinger
define('DB_NAME',    'nama_database_anda');
define('DB_USER',    'username_database');
define('DB_PASS',    'password_database');
```

### Langkah 3 — Upload ke Hostinger
1. Buka **hPanel** → File Manager → masuk ke folder `public_html`
2. Upload seluruh isi folder `pasar_viz/` (bukan foldernya, isinya)
3. Pastikan struktur di `public_html/` seperti di atas

### Langkah 4 — Akses Aplikasi
Buka `https://domain-anda.com` — dashboard langsung tampil.

---

### Fitur Aplikasi
| Fitur | Keterangan |
|---|---|
| Filter otomatis | Hanya menampilkan data `service=Fixed Service` + `subservice=PP` |
| Grouping klien | Sidebar menampilkan semua klien beserta jumlah ISR & link |
| Peta interaktif | Titik TX (biru) & RX (oranye) per klien, garis link antar stasiun |
| Link ID | Klik titik/garis di peta → panel detail muncul per `link_id` |
| Detail ISR | Nomor SIMF, frekuensi, perangkat, masa berlaku, alamat, stasiun lawan |
| Status izin | Chip berwarna: Granted (hijau) / Segera Habis (kuning) / Expired (merah) |
| Pencarian | Search real-time nama klien di sidebar |

### Catatan Keamanan
- File `config.php` dan `db.php` dilindungi `.htaccess` (tidak bisa diakses via browser)
- Semua query menggunakan **PDO Prepared Statements** (aman dari SQL injection)
- `display_errors` dinonaktifkan di production
