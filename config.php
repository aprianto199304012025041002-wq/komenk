<?php
/**
 * config.php — loader konfigurasi KOMENK.
 * komenk_config.php di-generate saat deploy via GitHub Actions
 * dan di-deploy ke direktori yang sama (/public_html/).
 * Diproteksi dari akses publik via .htaccess.
 */

$config_path = __DIR__ . '/komenk_config.php';

if (!file_exists($config_path)) {
    http_response_code(503);
    die('Server configuration missing. Please deploy komenk_config.php.');
}

require_once $config_path;