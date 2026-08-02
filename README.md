# KOMENK — Koordinat Microwave Link

Aplikasi web peta interaktif untuk visualisasi data ISR Fixed Service PP (Point-to-Point Microwave Link) dari Balai Monitor Spektrum Frekuensi Radio Kelas I Yogyakarta.

**URL Produksi:** `https://komenk.balmonjogja.info`

---

## Fitur

- **Peta interaktif** berbasis Leaflet dengan rotasi bebas (compass/pinch/shift+scroll)
- **Multi-klien** — pilih satu atau beberapa klien sekaligus, ikon hexagon berwarna per klien
- **Garis MW Link** — visualisasi jalur microwave antar stasiun dengan kanvas overlay (performa tinggi)
- **Ghost site** — site lawan yang tidak terdaftar di database ditampilkan dengan ikon berbeda
- **Detail panel** — per site: daftar stasiun lawan, SIMF, frekuensi TX/RX, azimuth, jarak, status ISR, spesifikasi antena
- **Profil terrain (Topo)** — grafik elevasi sepanjang jalur link menggunakan Open-Elevation API
- **Filter wilayah** — filter site berdasarkan kota/kabupaten
- **Pencarian** — cari nama site atau frekuensi TX
- **Basemap** — pilihan Dark / Bright / Satelit
- **Responsif** — mode mobile dengan drawer, bottom sheet, dan FAB controls
- **Persist state** — pilihan klien, basemap, dan tema disimpan di localStorage

---

## Stack Teknologi

- **Backend:** PHP 8+, PDO MySQL
- **Database:** MySQL (tabel `isr` Fixed Service PP)
- **Frontend:** Vanilla JS, Leaflet.js + plugin Leaflet.RotatedMarker
- **Auth:** SSO GUDEG (`gudeg.xo.je`)
- **Hosting:** Hostinger (FTP deploy)
- **CI/CD:** GitHub Actions

---

## Struktur File

```
komenk/
├── index.php           ← Halaman peta utama (butuh login)
├── login.php           ← Redirect ke SSO GUDEG
├── callback.php        ← SSO OAuth callback
├── logout.php          ← Hapus sesi
├── default.php         ← Halaman default / landing
├── config.php          ← Loader konfigurasi → komenk_config.php
├── db.php              ← Koneksi PDO MySQL
├── includes/
│   └── auth.php        ← Helper sesi & SSO
├── api/
│   ├── clients.php     ← Daftar klien + statistik ISR & link count
│   ├── isr.php         ← Data ISR mentah per klien (untuk render peta)
│   ├── link_detail.php ← Detail satu link_id
│   └── _security.php   ← Helper keamanan API
└── .github/workflows/
    └── deploy.yml      ← CI/CD GitHub Actions → FTP Hostinger
```

---

## Cara Kerja

```
Browser
  │
  ├─ GET api/clients.php  → daftar klien (clnt_id, nama, total ISR, total link)
  │
  └─ GET api/isr.php?clnt_id=X  → semua baris ISR mentah satu klien
       │
       └─ JS: clusterISRtoSites()  → kelompokkan ISR ke "site" (radius 100m)
              buildMWLinks()       → hubungkan site berpasangan via link_id / koordinat lawan
              renderAllVisible()   → gambar marker + garis di Leaflet
```

---

## Setup

### 1. Clone & siapkan database

```bash
git clone https://github.com/aprianto199304012025041002-wq/komenk.git
cd komenk
```

Database MySQL harus berisi tabel `isr` dengan kolom antara lain:
`clnt_id`, `clnt_name`, `service`, `subservice`, `link_id`, `freq`, `freq_pair`,
`no_simf`, `stn_name`, `stn_addr`, `sid_lat`, `sid_long`, `to_lat_deg/min/sec/dir_ind`,
`to_long_deg/min/sec/dir_ind`, `stasiun_lawan`, `validity_date`, `azim`, `circuit_len`,
`h_asl`, `hgt_ant`, `erp_pwr_dbm`, `eq_mfr`, `eq_mdl`, `trans_type`, `city`, `province`.

### 2. Buat `komenk_config.php`

File ini **tidak masuk repo** dan harus dibuat manual di server (atau otomatis via GitHub Actions):

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
define('SSO_PUBLIC_KEY',   '-----BEGIN PUBLIC KEY-----\n...\n-----END PUBLIC KEY-----');
```

---

## Deploy ke Hostinger

Push ke branch `main` → GitHub Actions otomatis deploy via FTP.

### Secrets yang dibutuhkan

| Secret | Keterangan |
|---|---|
| `DB_HOST` | Host database |
| `DB_NAME` | Nama database |
| `DB_USER` | Username database |
| `DB_PASS` | Password database |
| `SSO_CLIENT_ID` | Client ID terdaftar di GUDEG |
| `SSO_PUBLIC_KEY` | RSA public key GUDEG |
| `FTP_PASS` | Password FTP Hostinger |

---

## Catatan

- `komenk_config.php` tidak pernah masuk repo
- File `.vs/` (Visual Studio) sebaiknya dihapus dari repo (tidak diperlukan di server)
- API `isr.php` mengembalikan semua baris ISR mentah satu klien sekaligus — proses clustering dilakukan di sisi klien (JS)
- Profil terrain menggunakan Open-Elevation API publik (`api.open-elevation.com`) — bisa lambat atau tidak tersedia sewaktu-waktu
