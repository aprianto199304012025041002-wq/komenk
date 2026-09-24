<?php
/**
 * api/health.php — health check endpoint KOMENK
 * Tidak butuh auth — untuk monitoring otomatis (UptimeRobot dll)
 */
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
    $cnt = $pdo->query("SELECT COUNT(*) FROM isr")->fetchColumn();
    echo json_encode([
        'status' => 'ok',
        'app'    => 'komenk',
        'db'     => 'connected',
        'rows'   => (int)$cnt,
        'time'   => date('c'),
    ]);
} catch (Throwable $e) {
    error_log('[KOMENK] Health check failed: ' . $e->getMessage());
    http_response_code(503);
    echo json_encode(['status' => 'error', 'app' => 'komenk', 'db' => 'unreachable', 'time' => date('c')]);
}
