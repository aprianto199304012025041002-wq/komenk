<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Tampilkan isi komenk_config.php yang ada di server
$path = '/home/u781960094/domains/komenk.balmonjogja.info/komenk_config.php';
echo '<h3>komenk_config.php (' . filesize($path) . ' bytes)</h3>';
echo '<pre>' . htmlspecialchars(file_get_contents($path)) . '</pre>';

// Tampilkan isi config.php
echo '<h3>config.php</h3>';
echo '<pre>' . htmlspecialchars(file_get_contents(__DIR__ . '/config.php')) . '</pre>';
