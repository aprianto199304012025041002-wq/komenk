# 🗼 KOMENK — Koordinat Microwave Link

**KOMENK** adalah aplikasi web peta interaktif untuk visualisasi data ISR Fixed Service PP (Point-to-Point Microwave Link) dari Balai Monitor Spektrum Frekuensi Radio Kelas I Yogyakarta.

- **URL Produksi:** `https://komenk.balmonjogja.info`
- **Hosting:** Hostinger
- **Stack:** PHP 8+, PDO MySQL, Vanilla JS, Leaflet.js
- **Auth:** SSO GUDEG (`gudeg.xo.je`)
- **Deploy:** GitHub Actions → FTP → Hostinger (push ke `main` = auto deploy)
- **Repo:** `aprianto199304012025041002-wq/komenk`

---

## ✨ Fitur

- **Peta interaktif** berbasis Leaflet dengan rotasi bebas (compass, pinch, shift+scroll)
- **Multi-klien** — pilih satu atau beberapa klien sekaligus, ikon hexagon berwarna berbeda per klien
- **Garis MW Link** — jalur microwave antar stasiun divisualisasikan di canvas overlay (performa tinggi, tidak memperlambat peta meski ribuan link)
- **Ghost site** — stasiun lawan yang tidak terdaftar di database ditampilkan dengan ikon berbeda
- **Detail panel** — per site: daftar stasiun lawan, No. SIMF, frekuensi TX/RX, azimuth, jarak, status ISR, spesifikasi antena & perangkat
- **Profil terrain (Topo)** — grafik elevasi sepanjang jalur link via Open-Elevation API
- **Filter wilayah** — filter site berdasarkan kota/kabupaten
- **Pencarian** — cari nama site atau frekuensi TX
- **Basemap** — pilihan Dark, Bright, Satelit
- **Responsif** — mode mobile dengan drawer, bottom sheet, dan FAB controls
- **Persist state** — pilihan klien, basemap, dan tema disimpan di `localStorage`

---

## 🏗️ Struktur File

```
komenk/
├── .github/workflows/
│   └── deploy.yml          # Push ke main → generate komenk_config.php → FTP ke Hostinger
├── .htaccess               # Blokir akses ke komenk_config.php, Options -Indexes, security headers
├── .gitignore              # Exclude: komenk_config.php, .ftp-deploy-sync-state.json, .vs/
├── api/
│   ├── _security.php       # Helper keamanan API: cek sesi SSO, rate limit, CORS
│   ├── clients.php         # Daftar klien + statistik (total ISR, total link count)
│   └── isr.php             # Semua baris ISR mentah satu klien (filter opsional per link_id)
├── assets/
│   └── css/komenk.css      # Stylesheet utama
├── includes/
│   └── auth.php            # Helper sesi SSO: _session_start(), require_login(), verify_token(), is_logged_in()
├── callback.php            # SSO OAuth callback — verifikasi token + state, isi session
├── config.php              # Loader konfigurasi → require komenk_config.php (tidak di-commit)
├── db.php                  # Koneksi PDO MySQL
├── default.php             # Landing page / halaman default
├── favicon.svg             # Favicon aplikasi
├── index.php               # Halaman peta utama (butuh login)
├── login.php               # Redirect ke SSO GUDEG dengan state CSRF
└── logout.php              # Hapus session + redirect
```

---

## ⚙️ Setup

### 1. Clone

```bash
git clone https://github.com/aprianto199304012025041002-wq/komenk.git
cd komenk
```

### 2. Siapkan Database MySQL

Database harus berisi tabel `isr` dengan kolom Fixed Service PP dari SIMS. Kolom yang dipakai aplikasi ini antara lain:

`clnt_id`, `clnt_name`, `service`, `subservice`, `link_id`, `freq`, `freq_pair`, `no_simf`, `stn_name`, `stn_addr`, `sid_lat`, `sid_long`, `to_lat_deg/min/sec/dir_ind`, `to_long_deg/min/sec/dir_ind`, `stasiun_lawan`, `validity_date`, `azim`, `circuit_len`, `h_asl`, `hgt_ant`, `erp_pwr_dbm`, `eq_mfr`, `eq_mdl`, `trans_type`, `city`, `province`

### 3. Buat `komenk_config.php`

File ini **tidak ada di repo** dan dibuat otomatis oleh GitHub Actions saat deploy. Untuk lokal:

```php
<?php
define('DB_HOST',          'localhost');
define('DB_NAME',          'nama_database');
define('DB_USER',          'username_db');
define('DB_PASS',          'password_db');
define('DB_CHARSET',       'utf8mb4');
define('SSO_URL',          'https://gudeg.xo.je/sso.php');
define('SSO_CLIENT_ID',    'client_id_dari_gudeg');
define('SSO_CALLBACK_URL', 'https://komenk.balmonjogja.info/callback.php');
define('APP_NAME',         'komenk');
define('APP_BASE_URL',     'https://komenk.balmonjogja.info');
define('SSO_PUBLIC_KEY',   <<<'PEM'
-----BEGIN PUBLIC KEY-----
... (RSA public key dari GUDEG) ...
-----END PUBLIC KEY-----
PEM);
```

---

## 🔄 Cara Kerja — Render Peta

```
Browser
  │
  ├─ GET api/clients.php
  │    → daftar klien: clnt_id, nama, total ISR, total link count
  │
  └─ GET api/isr.php?clnt_id=X
       → semua baris ISR mentah klien X (opsional: &link_id=Y untuk satu link)
       │
       └─ JS: clusterISRtoSites()
              → kelompokkan baris ISR ke "site" berdasarkan radius 100m
              buildMWLinks()
              → hubungkan site berpasangan via link_id / koordinat stasiun lawan
              renderAllVisible()
              → gambar marker hexagon + garis link di Leaflet canvas overlay
```

Clustering dan linking dilakukan **di sisi klien (JavaScript)** — server hanya mengembalikan data mentah. Ini memungkinkan interaksi peta yang cepat tanpa round-trip ke server saat user klik atau toggle klien.

---

## 🔄 Alur Auth SSO

```
User → login.php (generate state CSRF → redirect ke gudeg.xo.je/sso.php)
     → GUDEG login → generate JWT RS256
     → Redirect ke callback.php?token=<jwt>&state=<state>
     → callback.php: verifikasi state (one-time, 10 menit), verify_token() (RS256)
     → Isi $_SESSION[user_id, name, email, role, ip]
     → Redirect ke index.php
```

KOMENK hanya memverifikasi token secara lokal (RS256 via `SSO_PUBLIC_KEY`) — tidak butuh `SSO_CLIENT_SECRET` karena tidak pernah memanggil `/verify.php` server-side.

---

## 🚀 Deploy

Push ke `main` → GitHub Actions:
1. Generate `komenk_config.php` dari GitHub Secrets
2. Deploy seluruh file via FTP ke Hostinger (`/public_html/`)
3. `.htaccess` di-exclude dari FTP — tidak di-overwrite saat deploy

### GitHub Secrets yang Dibutuhkan

| Secret | Keterangan |
|---|---|
| `DB_HOST` | Host database Hostinger |
| `DB_NAME` | Nama database |
| `DB_USER` | Username database |
| `DB_PASS` | Password database |
| `SSO_CLIENT_ID` | Client ID terdaftar di GUDEG (`fea056cf6cdea01d`) |
| `SSO_PUBLIC_KEY` | RSA public key GUDEG (base64) |
| `FTP_HOST` | Hostname FTP Hostinger |
| `FTP_USER` | Username FTP |
| `FTP_PASS` | Password FTP |

---

## 🛡️ Keamanan

- `komenk_config.php` tidak pernah masuk repo (`.gitignore`) dan diblokir akses HTTP via `.htaccess`
- `.htaccess` tidak di-overwrite saat deploy (di-exclude dari FTP workflow)
- `Options -Indexes` — directory listing dimatikan
- Semua endpoint API (`api/`) memverifikasi sesi SSO via `_security.php` sebelum mengembalikan data
- Rate limiting di `api/_security.php` untuk endpoint `isr` dan `clients`
- CORS dinamis di API — hanya `APP_BASE_URL` yang diizinkan
- Session: `httponly`, `secure`, `SameSite=None` (cross-origin SSO callback), regenerasi ID setelah login
- Validasi state CSRF di `callback.php` (one-time, expired setelah 10 menit)

---

## 📝 Catatan Teknis

- **Profil terrain** menggunakan Open-Elevation API publik (`api.open-elevation.com`) — bisa lambat atau tidak tersedia. Ini layanan eksternal, bukan bagian dari infrastruktur Balmon.
- **Ghost site** — kalau `link_id` ada tapi koordinat stasiun lawan tidak ada di database (ISR lawan mungkin di wilayah lain atau belum di-import), site lawan ditampilkan sebagai ghost dengan estimasi koordinat dari DMS (`to_lat_deg/min/sec` dll).
- **`api/isr.php`** mengembalikan semua ISR satu klien sekaligus. Parameter opsional `link_id` bisa dipakai untuk filter satu link. Perlu dimonitor kalau ada klien dengan ribuan ISR.
- **`api/clients.php`** mengembalikan daftar klien beserta total ISR dan total link count untuk keperluan UI pemilihan klien.

---

## 📄 Lisensi

Proyek privat — hak cipta milik Balai Monitor Spektrum Frekuensi Radio Kelas I Yogyakarta.
