<?php
/**
 * api/link_detail.php
 * Detail satu link, diidentifikasi dengan:
 *
 * CATATAN: File ini tidak dipanggil oleh frontend saat ini (dead code).
 * Dipertahankan sebagai cadangan untuk kebutuhan integrasi eksternal
 * atau debugging langsung via URL. Jangan hapus tanpa koordinasi.
 *
 *   link_id (primary), ATAU
 *   freq + stn_name (cross-reference fallback)
 *
 * Query params:
 *   link_id   (float, optional jika ada freq+stn)
 *   freq      (float, optional)
 *   stn_name  (string, optional)
 *   clnt_id   (int, optional — mempersempit scope)
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../api/_security.php';
check_rate_limit('link_detail');
require_once __DIR__ . '/../db.php';

$linkId  = isset($_GET['link_id'])  ? (float)$_GET['link_id']        : null;
$freq    = isset($_GET['freq'])     ? (float)$_GET['freq']            : null;
$stnName = isset($_GET['stn_name']) ? trim($_GET['stn_name'])         : null;
$clntId  = isset($_GET['clnt_id'])  ? (int)$_GET['clnt_id']          : null;

if ($linkId === null && $freq === null && $stnName === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Minimal satu parameter (link_id / freq / stn_name) diperlukan.']);
    exit;
}

try {
    /* ---- Cari semua ISR yang terkait dengan link ini ---- */
    $conditions = ["service = 'Fixed Service'", "subservice IN ('PP', 'PP Private')"];
    $params     = [];

    if ($clntId) {
        $conditions[] = "clnt_id = ?";
        $params[]     = $clntId;
    }

    if ($linkId !== null) {
        $conditions[] = "link_id = ?";
        $params[]     = $linkId;
    }

    $sql = "SELECT * FROM isr WHERE " . implode(' AND ', $conditions) . " ORDER BY trans_type, site_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    // Jika link_id tidak cukup (0 baris) atau belum ada, tambah lookup via freq / stn_name
    if (empty($rows) && ($freq !== null || $stnName !== null)) {
        $orConds = [];
        $orParams = ["Fixed Service"];

        if ($freq !== null) {
            // Cari stasiun yang freq-nya = freq atau freq_pair-nya = freq
            $orConds[] = "(ABS(freq - ?) < 0.001 OR ABS(freq_pair - ?) < 0.001)";
            $orParams[] = $freq;
            $orParams[] = $freq;
        }
        if ($stnName !== null) {
            $orConds[] = "(LOWER(TRIM(stn_name)) = LOWER(?) OR LOWER(TRIM(stasiun_lawan)) = LOWER(?))";
            $orParams[] = $stnName;
            $orParams[] = $stnName;
        }
        if ($clntId) {
            $orConds[] = "clnt_id = ?";
            $orParams[] = $clntId;
        }

        $sqlFb = "SELECT * FROM isr
                  WHERE service = ? AND subservice IN ('PP', 'PP Private')
                    AND (" . implode(' OR ', $orConds) . ")
                  ORDER BY trans_type, site_id";
        $stmtFb = $pdo->prepare($sqlFb);
        $stmtFb->execute($orParams);
        $seedRows = $stmtFb->fetchAll();

        // Kumpulkan semua link_id yang ditemukan, lalu ambil seluruh grup
        $foundLinkIds = array_unique(array_filter(array_column($seedRows, 'link_id')));

        if (!empty($foundLinkIds)) {
            $in = implode(',', array_fill(0, count($foundLinkIds), '?'));
            $stmtAll = $pdo->prepare("SELECT * FROM isr
                WHERE service = 'Fixed Service' AND subservice IN ('PP', 'PP Private')
                AND link_id IN ($in)
                ORDER BY trans_type, site_id");
            $stmtAll->execute($foundLinkIds);
            $rows = $stmtAll->fetchAll();
        } else {
            $rows = $seedRows; // tidak ada link_id, gunakan hasil langsung
        }
    }

    /* ---- Pasangkan TX ↔ RX dalam hasil ---- */
    $paired = [];
    $used   = [];
    for ($i = 0; $i < count($rows); $i++) {
        if (isset($used[$i])) continue;
        $a = $rows[$i];
        $partner = null;
        for ($j = $i + 1; $j < count($rows); $j++) {
            if (isset($used[$j])) continue;
            $b = $rows[$j];
            $freqOk = $a['freq'] !== null && $b['freq_pair'] !== null &&
                      abs($a['freq'] - $b['freq_pair']) < 0.001 &&
                      $a['freq_pair'] !== null && $b['freq'] !== null &&
                      abs($a['freq_pair'] - $b['freq']) < 0.001;
            $nameOk = (!empty($a['stn_name']) && !empty($b['stasiun_lawan']) &&
                       strtolower(trim($a['stn_name'])) === strtolower(trim($b['stasiun_lawan'])))
                   || (!empty($b['stn_name']) && !empty($a['stasiun_lawan']) &&
                       strtolower(trim($b['stn_name'])) === strtolower(trim($a['stasiun_lawan'])));
            if ($freqOk || $nameOk) {
                $partner = $b;
                $used[$j] = true;
                break;
            }
        }
        $used[$i] = true;
        $paired[] = [
            'station_a'   => $a,
            'station_b'   => $partner,
            'pair_method' => isset($freqOk) && $freqOk ? 'freq_pair' : ($partner ? 'stn_name' : 'solo'),
        ];
    }

    echo json_encode([
        'success' => true,
        'link_id' => $linkId,
        'total'   => count($rows),
        'pairs'   => $paired,
        'data'    => $rows,
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    api_error(500, 'Terjadi kesalahan server.', $e);
}