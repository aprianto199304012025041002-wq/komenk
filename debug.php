<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
echo '<h3>PHP OK - version: ' . phpversion() . '</h3>';

// Cek config
$config = '/home/u781960094/domains/komenk.balmonjogja.info/komenk_config.php';
if (file_exists($config)) {
    echo '<p>✅ komenk_config.php ada</p>';
    require_once $config;
    echo '<p>✅ config loaded</p>';
    echo '<p>SSO_URL: ' . (defined('SSO_URL') ? SSO_URL : 'TIDAK TERDEFINISI') . '</p>';
    echo '<p>SSO_CLIENT_ID: ' . (defined('SSO_CLIENT_ID') ? SSO_CLIENT_ID : 'TIDAK TERDEFINISI') . '</p>';
    echo '<p>SSO_PUBLIC_KEY: ' . (defined('SSO_PUBLIC_KEY') ? 'ADA (' . strlen(SSO_PUBLIC_KEY) . ' chars)' : 'TIDAK TERDEFINISI') . '</p>';
    echo '<p>DB_HOST: ' . (defined('DB_HOST') ? DB_HOST : 'TIDAK TERDEFINISI') . '</p>';
} else {
    echo '<p>❌ komenk_config.php TIDAK ADA di: ' . $config . '</p>';
}

// Cek auth.php
$auth = __DIR__ . '/includes/auth.php';
echo '<p>' . (file_exists($auth) ? '✅' : '❌') . ' includes/auth.php ' . (file_exists($auth) ? 'ada' : 'TIDAK ADA') . '</p>';
