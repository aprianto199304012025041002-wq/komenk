<?php
/**
 * config.php — loader konfigurasi KOMENK.
 * File konfigurasi sebenarnya (komenk_config.php) di-generate saat deploy
 * via GitHub Actions dan diletakkan satu level di atas webroot oleh Hostinger,
 * atau di direktori yang sama dengan file ini sebagai fallback.
 */

// Coba satu level di atas webroot (lokasi deploy Hostinger)
$config_above = dirname(__DIR__) . '/komenk_config.php';
// Fallback: direktori yang sama (local dev)
$config_local  = __DIR__ . '/komenk_config.php';

if (file_exists($config_above)) {
    require_once $config_above;
} elseif (file_exists($config_local)) {
    require_once $config_local;
} else {
    http_response_code(503);
    die('Server configuration missing. Please deploy komenk_config.php.');
}