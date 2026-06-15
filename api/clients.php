<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=30');

require_once __DIR__ . '/../db.php';

try {
    $sql = "
        SELECT
            clnt_id, MIN(clnt_name) AS clnt_name,
            COUNT(*) AS total_isr,
            COUNT(DISTINCT link_id) AS total_link,
            SUM(CASE WHEN trans_type = 'Transmitter' THEN 1 ELSE 0 END) AS total_tx,
            SUM(CASE WHEN trans_type = 'Receiver'    THEN 1 ELSE 0 END) AS total_rx,
            SUM(CASE WHEN sid_lat IS NOT NULL AND sid_long IS NOT NULL THEN 1 ELSE 0 END) AS total_plotted,
            SUM(CASE WHEN to_lat_deg IS NOT NULL AND stasiun_lawan IS NOT NULL THEN 1 ELSE 0 END) AS total_ghost_candidate,
            MIN(validity_date) AS earliest_exp,
            MAX(validity_date) AS latest_exp
        FROM isr
        WHERE service = 'Fixed Service' AND subservice IN ('PP', 'PP Private')
        GROUP BY clnt_id
        ORDER BY total_isr DESC
    ";
    $rows = $pdo->query($sql)->fetchAll();
    echo json_encode(['success' => true, 'data' => $rows]);
} catch (PDOException $e) {
    error_log('[KOMENK] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Terjadi kesalahan server.']);
}