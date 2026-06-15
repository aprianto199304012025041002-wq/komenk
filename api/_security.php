<?php
/**
 * _security.php — helper keamanan
 */

// ── CORS: izinkan domain sendiri + akses langsung ───────────────
$allowed_origins = [
    'https://komenk.balmonjogja.info',
    'http://komenk.balmonjogja.info',
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins, true)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    // Akses langsung (tanpa Origin) = dari domain sendiri, izinkan
    header('Access-Control-Allow-Origin: https://komenk.balmonjogja.info');
}
header('Vary: Origin');
header_remove('X-Powered-By');

// ── Rate limiting — hanya jika temp dir writable ─────────────────
function check_rate_limit(string $endpoint, int $max = 30, int $window = 60): void {
    $tmp = sys_get_temp_dir();
    if (!is_writable($tmp)) return; // skip kalau tidak bisa tulis, jangan crash

    $ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = $tmp . '/komenk_rl_' . md5($endpoint . $ip);
    $now = time();

    $data = ['count' => 0, 'start' => $now];
    if (file_exists($key)) {
        $raw = @json_decode(@file_get_contents($key), true);
        if (is_array($raw) && ($now - $raw['start']) < $window) {
            $data = $raw;
        }
    }

    $data['count']++;
    @file_put_contents($key, json_encode($data), LOCK_EX);

    if ($data['count'] > $max) {
        http_response_code(429);
        header('Retry-After: ' . ($window - ($now - $data['start'])));
        echo json_encode(['success' => false, 'error' => 'Terlalu banyak permintaan. Coba lagi sebentar.']);
        exit;
    }
}

// ── Helper: safe error response ──────────────────────────────────
function api_error(int $code, string $msg, ?Throwable $e = null): never {
    if ($e) {
        error_log('[KOMENK] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    }
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}
