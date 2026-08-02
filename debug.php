<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo '<h3>Test index.php</h3>';

// Simulasi apa yang dilakukan index.php
echo '<p>Step 1: require config...</p>';
require_once __DIR__ . '/config.php';
echo '<p>✅ config OK</p>';

echo '<p>Step 2: require auth...</p>';
require_once __DIR__ . '/includes/auth.php';
echo '<p>✅ auth OK</p>';

echo '<p>Step 3: require_login()...</p>';
// Jangan panggil require_login() karena akan redirect
// Cek saja apakah fungsi ada
echo '<p>' . (function_exists('require_login') ? '✅ require_login exists' : '❌ require_login NOT FOUND') . '</p>';
echo '<p>' . (function_exists('get_user') ? '✅ get_user exists' : '❌ get_user NOT FOUND') . '</p>';
echo '<p>' . (defined('SSO_URL') ? '✅ SSO_URL: ' . SSO_URL : '❌ SSO_URL not defined') . '</p>';
echo '<p>' . (defined('SSO_CLIENT_ID') ? '✅ SSO_CLIENT_ID: ' . SSO_CLIENT_ID : '❌ SSO_CLIENT_ID not defined') . '</p>';
echo '<p>' . (defined('SSO_PUBLIC_KEY') ? '✅ SSO_PUBLIC_KEY: ' . strlen(SSO_PUBLIC_KEY) . ' chars' : '❌ SSO_PUBLIC_KEY not defined') . '</p>';

// Cek includes/auth.php
$auth_content = file_get_contents(__DIR__ . '/includes/auth.php');
echo '<p>includes/auth.php size: ' . strlen($auth_content) . ' bytes</p>';
