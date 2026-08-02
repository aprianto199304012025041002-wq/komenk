<?php
/**
 * login.php — Halaman login KOMENK via SSO GUDEG
 */
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: /index.php');
    exit;
}

_session_start();
$state = bin2hex(random_bytes(16));
$_SESSION['sso_state']    = $state;
$_SESSION['sso_state_ts'] = time();

$login_url = SSO_URL . '?client_id=' . urlencode(SSO_CLIENT_ID) . '&state=' . urlencode($state);

$err_msg = '';
$err_code = $_GET['err'] ?? '';

// Kalau tidak ada error, langsung redirect ke GUDEG SSO
// User yang sudah login di GUDEG akan langsung balik tanpa perlu input password
if (!$err_code) {
    header('Location: ' . $login_url);
    exit;
}

// err=3 (state CSRF) — reset dan redirect ulang
if ($err_code === '3') {
    header('Location: ' . $login_url);
    exit;
}

switch ($err_code) {
    case '1': $err_msg = 'Token tidak ditemukan. Coba login kembali.'; break;
    case '2': $err_msg = 'Token tidak valid atau sudah kedaluwarsa.'; break;
    case '403': $err_msg = 'Akun Anda tidak memiliki akses ke aplikasi ini.'; break;
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="theme-color" content="#080b10">
<title>Login — KOMENK</title>
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=DM+Sans:opsz,wght@9..40,300;400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

:root,[data-theme="dark"]{
  --bg:#080b10;--surface:#0f1520;--surface2:#161e2c;
  --border:#1e2a3a;--border-hi:#2a3a50;
  --accent:#00e5ff;--accent-glow:rgba(0,229,255,.18);--accent-bg:rgba(0,229,255,.06);--accent-bdr:rgba(0,229,255,.2);
  --text:#f1f5f9;--muted:#4a6080;--muted-hi:#6b8099;
  --red:#ef4444;--red-glow:rgba(239,68,68,.14);
  --glass:rgba(8,11,16,.88);--glass-hi:rgba(15,21,32,.95);
  --shadow-float:0 8px 32px rgba(0,0,0,.7),0 2px 8px rgba(0,0,0,.5),0 0 0 1px rgba(255,255,255,.04);
  --radius:8px;--radius-sm:5px;--radius-lg:12px;
  --mono:'Space Mono',monospace;--sans:'DM Sans',sans-serif;
  --t-fast:.12s cubic-bezier(.4,0,.2,1);--t-med:.22s cubic-bezier(.4,0,.2,1);
}
[data-theme="bright"]{
  --bg:#f0f4f8;--surface:#ffffff;--surface2:#edf2f7;
  --border:#d1dbe8;--border-hi:#b8c8da;
  --accent:#0284c7;--accent-glow:rgba(2,132,199,.12);--accent-bg:rgba(2,132,199,.07);--accent-bdr:rgba(2,132,199,.25);
  --text:#0f172a;--muted:#64748b;--muted-hi:#475569;
  --red:#dc2626;--red-glow:rgba(220,38,38,.1);
  --glass:rgba(240,244,248,.92);--glass-hi:rgba(255,255,255,.97);
  --shadow-float:0 4px 20px rgba(0,0,0,.1),0 1px 4px rgba(0,0,0,.07),0 0 0 1px rgba(0,0,0,.05);
}

html,body{height:100%;background:var(--bg);color:var(--text);font-family:var(--sans);transition:background var(--t-med),color var(--t-med);}
body{display:flex;align-items:center;justify-content:center;min-height:100dvh;padding:20px 16px;}

/* Theme FAB */
.theme-fab{position:fixed;top:calc(14px + env(safe-area-inset-top));right:14px;z-index:999;width:36px;height:36px;background:var(--glass-hi);backdrop-filter:blur(16px) saturate(160%);border:1px solid var(--border-hi);border-radius:var(--radius);cursor:pointer;color:var(--muted-hi);box-shadow:var(--shadow-float);display:flex;align-items:center;justify-content:center;font-size:18px;transition:border-color var(--t-fast),color var(--t-fast),box-shadow var(--t-fast);-webkit-tap-highlight-color:transparent;}
.theme-fab:hover{border-color:var(--accent);color:var(--accent);box-shadow:var(--shadow-float),0 0 12px var(--accent-glow);}

/* Container */
.wrap{width:100%;max-width:380px;}

/* Logo */
.logo-area{text-align:center;margin-bottom:28px;}
.logo-icon{width:56px;height:56px;background:var(--accent-bg);border:1px solid var(--accent-bdr);border-radius:var(--radius-lg);display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;box-shadow:0 0 28px var(--accent-glow);font-size:28px;}
.logo-title{font-family:var(--mono);font-size:20px;font-weight:700;letter-spacing:4px;background:linear-gradient(135deg,var(--accent),#7dd3fc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
.logo-sub{font-size:10px;color:var(--muted-hi);letter-spacing:2px;text-transform:uppercase;margin-top:6px;font-family:var(--mono);line-height:1.7;}
.logo-sub strong{display:block;color:var(--muted-hi);font-size:10px;margin-top:2px;}

/* Card */
.card{background:var(--glass-hi);backdrop-filter:blur(20px) saturate(160%);border:1px solid var(--border-hi);border-radius:var(--radius-lg);padding:28px 24px;box-shadow:var(--shadow-float);}

/* Error */
.err-box{background:var(--red-glow);border:1px solid rgba(239,68,68,.25);border-radius:var(--radius-sm);padding:10px 14px;font-size:12px;color:var(--red);margin-bottom:18px;display:flex;align-items:center;gap:8px;}

/* SSO Button */
.btn-sso{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:12px 16px;background:var(--accent-bg);border:1px solid var(--accent-bdr);border-radius:var(--radius);color:var(--accent);font-family:var(--mono);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;text-decoration:none;transition:background var(--t-fast),border-color var(--t-fast),box-shadow var(--t-fast),transform var(--t-fast);-webkit-tap-highlight-color:transparent;}
.btn-sso:hover{background:rgba(0,229,255,.12);border-color:var(--accent);box-shadow:0 0 20px var(--accent-glow);transform:translateY(-1px);}
.btn-sso:active{transform:scale(.98);}
[data-theme="bright"] .btn-sso:hover{background:rgba(2,132,199,.1);}
.btn-sso-icon{font-size:18px;flex-shrink:0;}

/* Note */
.note{text-align:center;font-size:11px;color:var(--muted);margin-top:16px;font-family:var(--mono);letter-spacing:.5px;}
.footer{text-align:center;margin-top:24px;font-size:10px;color:var(--muted);font-family:var(--mono);letter-spacing:1px;text-transform:uppercase;}

@media(max-width:420px){
  body{align-items:flex-start;padding-top:calc(70px + env(safe-area-inset-top));}
  .card{padding:20px 16px;}
}
</style>
</head>
<body>

<button class="theme-fab" onclick="toggleTheme()" title="Ganti tema">&#9728;</button>

<div class="wrap">
  <div class="logo-area">
    <div class="logo-icon">&#128225;</div>
    <div class="logo-title">KOMENK</div>
    <div class="logo-sub">
      Koordinat Microwave Link
      <strong>Balai Monitor Spektrum Frekuensi Radio Kelas I Yogyakarta</strong>
    </div>
  </div>

  <div class="card">
    <?php if ($err_msg): ?>
    <div class="err-box">&#9888; <?= htmlspecialchars($err_msg) ?></div>
    <?php endif; ?>

    <a href="<?= htmlspecialchars($login_url) ?>" class="btn-sso">
      <span class="btn-sso-icon">&#128737;</span>
      Masuk dengan GUDEG JOGJA
    </a>

    <p class="note">Akses hanya untuk petugas yang terdaftar</p>
  </div>

  <p class="footer">© <?= date('Y') ?> QOS Balmon Jogja Team</p>
</div>

<script>
(function(){
  var K='komenk_theme';
  function apply(t){
    document.documentElement.setAttribute('data-theme',t);
    try{localStorage.setItem(K,t);}catch(e){}
    document.querySelector('.theme-fab').textContent=t==='bright'?'\u263D':'\u2600';
  }
  function get(){try{return localStorage.getItem(K)||'dark';}catch(e){return 'dark';}}
  window.toggleTheme=function(){apply(get()==='dark'?'bright':'dark');};
  apply(get());
})();
</script>
</body>
</html>
