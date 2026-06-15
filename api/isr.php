<?php
/**
 * api/isr.php
 * Mengembalikan semua ISR Fixed Service / PP untuk satu klien.
 * Pairing dilakukan di sisi frontend (JS) — server hanya kirim raw rows.
 *
 * Query params:
 *   clnt_id  (required)
 *   link_id  (optional) — filter satu link
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: private, max-age=300');

require_once __DIR__ . '/../api/_security.php';
check_rate_limit('isr');
require_once __DIR__ . '/../db.php';

$clntId = isset($_GET['clnt_id']) ? (int)$_GET['clnt_id'] : 0;
$linkId = isset($_GET['link_id']) ? (float)$_GET['link_id'] : null;

if (!$clntId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'clnt_id diperlukan.']);
    exit;
}

try {
    $params = [$clntId];
    $linkFilter = '';
    if ($linkId !== null) {
        $linkFilter = ' AND a.link_id = ?';
        $params[]   = $linkId;
    }

    $sql = "
        SELECT
            a.id, a.clnt_id, a.clnt_name, a.no_simf, a.appl_id, a.site_id,
            a.status_simf, a.service, a.subservice, a.trans_type,
            a.freq, a.freq_pair, a.erp_pwr_dbm, a.eq_pwr,
            a.eq_mfr, a.eq_mdl, a.emis_class_1,
            a.stn_name, a.stn_addr, a.sid_long, a.sid_lat,
            a.area_of_service, a.city, a.district, a.province,
            a.link_id, a.circuit_len, a.azim, a.h_asl, a.hgt_ant,
            a.appl_date, a.licence_date, a.validity_date, a.umur_isr,
            a.upt,
            a.to_clnt_id, a.to_site_id, a.stasiun_lawan,
            a.to_lat_deg, a.to_lat_min, a.to_lat_sec, a.to_lat_dir_ind,
            a.to_long_deg, a.to_long_min, a.to_long_sec, a.to_long_dir_ind
        FROM isr a
        WHERE a.service    = 'Fixed Service'
          AND a.subservice IN ('PP', 'PP Private')
          AND a.clnt_id    = ?
          $linkFilter
        ORDER BY a.link_id, a.site_id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'clnt_id' => $clntId,
        'total'   => count($rows),
        'links'   => [],   // pairing dilakukan di frontend; field dipertahankan agar tidak breaking
        'raw'     => $rows,
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    api_error(500, 'Terjadi kesalahan server.', $e);
}