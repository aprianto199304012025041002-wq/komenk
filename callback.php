<?php
/**
 * callback.php — SSO callback KOMENK
 * Menerima token dari GUDEG SSO, verifikasi, lalu set session.
 */
require_once __DIR__ . '/includes/auth.php';

_session_start();

// Anti-replay: kalau sudah login, langsung ke app
if (!empty($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}

$token = $_GET['token'] ?? '';
if (!$token) {
    error_log('[KOMENK] Callback: token tidak ada dari IP ' . ($_SERVER['REMOTE_ADDR'] ?? '-'));
    header('Location: /login.php?err=1');
    exit;
}

// Validasi state — cegah login CSRF
$state            = $_GET['state'] ?? '';
$session_state    = $_SESSION['sso_state'] ?? '';
$session_state_ts = $_SESSION['sso_state_ts'] ?? 0;

// Hapus state (one-time use)
unset($_SESSION['sso_state'], $_SESSION['sso_state_ts']);

if (!$state || !$session_state || !hash_equals($session_state, $state)) {
    error_log('[KOMENK] Callback: state tidak valid dari IP ' . ($_SERVER['REMOTE_ADDR'] ?? '-'));
    header('Location: /login.php?err=3');
    exit;
}

// State expired setelah 10 menit
if (time() - $session_state_ts > 600) {
    error_log('[KOMENK] Callback: state expired dari IP ' . ($_SERVER['REMOTE_ADDR'] ?? '-'));
    header('Location: /login.php?err=3');
    exit;
}

$payload = verify_sso_token($token);
if (!$payload) {
    error_log('[KOMENK] Callback: token tidak valid dari IP ' . ($_SERVER['REMOTE_ADDR'] ?? '-'));
    header('Location: /login.php?err=2');
    exit;
}

// Session fixation prevention
session_regenerate_id(true);

$_SESSION['user_id']    = $payload['user_id'];
$_SESSION['name']       = $payload['name'];
$_SESSION['email']      = $payload['email'];
$_SESSION['role']       = $payload['role'];
$_SESSION['login_time'] = time();
$_SESSION['ip']         = $_SERVER['REMOTE_ADDR'] ?? '';

header('Location: /index.php');
exit;
