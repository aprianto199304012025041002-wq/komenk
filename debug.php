<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo '<h3>1. Config constants</h3>';
require_once __DIR__ . '/config.php';
echo 'SSO_URL: ' . (defined('SSO_URL') ? SSO_URL : '❌ NOT DEFINED') . '<br>';
echo 'SSO_CLIENT_ID: ' . (defined('SSO_CLIENT_ID') ? SSO_CLIENT_ID : '❌ NOT DEFINED') . '<br>';
echo 'SSO_PUBLIC_KEY: ' . (defined('SSO_PUBLIC_KEY') ? '✅ ' . strlen(SSO_PUBLIC_KEY) . ' chars' : '❌ NOT DEFINED') . '<br>';

echo '<h3>2. Session test</h3>';
require_once __DIR__ . '/includes/auth.php';
_session_start();
echo 'Session started OK<br>';
echo 'user_id: ' . ($_SESSION["user_id"] ?? 'kosong') . '<br>';

echo '<h3>3. Redirect test</h3>';
echo 'Kalau klik ini, harusnya redirect ke GUDEG: ';
$state = bin2hex(random_bytes(16));
$url = SSO_URL . '?client_id=' . urlencode(SSO_CLIENT_ID) . '&state=' . urlencode($state);
echo '<a href="' . htmlspecialchars($url) . '">Test redirect ke GUDEG</a><br>';

echo '<h3>4. index.php ada?</h3>';
echo file_exists(__DIR__ . '/index.php') ? '✅ index.php ada, size: ' . filesize(__DIR__ . '/index.php') . ' bytes' : '❌ index.php TIDAK ADA';
