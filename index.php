<?php
/**
 * index.php — KOMENK · Koordinat Microwave Link
 */
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="theme-color" content="#0c0f14">
<title>KOMENK · Koordinat Microwave Link</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css">
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=DM+Sans:opsz,wght@9..40,300;400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

/* ── DARK THEME (default) ── */
:root,[data-theme="dark"]{
  --bg:#0c0f14;--surface:#141920;--surface2:#1c2330;--border:#252d3a;
  --accent:#00e5ff;--accent2:#ff6b35;--accent3:#a78bfa;
  --text:#e2e8f0;--muted:#64748b;
  --green:#22c55e;--yellow:#eab308;--red:#ef4444;
  --shadow-txt:0 1px 4px rgba(0,0,0,.9),0 0 8px rgba(0,0,0,.7);
  --shadow-float:0 4px 24px rgba(0,0,0,.55),0 1px 6px rgba(0,0,0,.4);
  --shadow-hex:drop-shadow(0 1px 3px rgba(0,0,0,.8)) drop-shadow(0 0 6px rgba(0,0,0,.6));
  --glass:rgba(12,15,20,.82);
  --radius:6px;--mono:'Space Mono',monospace;--sans:'DM Sans',sans-serif;
  --leaflet-bg:#0c0f14;
}

/* ── BRIGHT THEME ── */
[data-theme="bright"]{
  --bg:#f0f4f8;--surface:#ffffff;--surface2:#e8edf3;--border:#cbd5e1;
  --accent:#0284c7;--accent2:#ea580c;--accent3:#7c3aed;
  --text:#1e293b;--muted:#64748b;
  --green:#16a34a;--yellow:#ca8a04;--red:#dc2626;
  --shadow-txt:0 1px 3px rgba(255,255,255,.9),0 0 6px rgba(255,255,255,.7);
  --shadow-float:0 4px 24px rgba(0,0,0,.15),0 1px 6px rgba(0,0,0,.1);
  --shadow-hex:drop-shadow(0 1px 4px rgba(0,0,0,.45)) drop-shadow(0 0 8px rgba(0,0,0,.3));
  --glass:rgba(240,244,248,.88);
  --leaflet-bg:#e8e8e8;
}

html,body{
  height:100%;background:var(--bg);color:var(--text);
  font-family:var(--sans);overflow:hidden;
  -webkit-tap-highlight-color:transparent;
  transition:background .25s,color .25s;
}

/* ══════════════════════════════════════════════
   LAYOUT — FULL MAP, semua floating
   ══════════════════════════════════════════════ */
.app{position:relative;width:100%;height:100vh;height:100dvh;overflow:hidden}
#map{position:absolute;inset:0;width:100%;height:100%;z-index:1}

/* ══════════════════════════════════════════════
   FLOATING TOP-LEFT — Logo + Theme toggle
   ══════════════════════════════════════════════ */
.float-logo{
  position:absolute;top:12px;left:12px;z-index:600;
  display:flex;align-items:center;gap:8px;
  background:var(--glass);backdrop-filter:blur(10px);
  border:1px solid var(--border);border-radius:var(--radius);
  padding:7px 12px;box-shadow:var(--shadow-float);
}
.logo-mark{font-family:var(--mono);font-size:13px;color:var(--accent);letter-spacing:3px;white-space:nowrap}
.logo-sep{width:1px;height:18px;background:var(--border);flex-shrink:0}
.logo-sub{font-size:9px;color:var(--muted);letter-spacing:1px;white-space:nowrap}

/* ══════════════════════════════════════════════
   FLOATING TOP-RIGHT (mobile) / kiri di bawah logo (desktop):
   Theme toggle + basemap + link toggle
   ══════════════════════════════════════════════ */
.float-controls{
  position:absolute;top:12px;left:12px;margin-top:50px;
  z-index:600;display:flex;flex-direction:column;gap:6px;
  /* akan di-override di desktop */
}

/* Control pill — container per baris */
.ctrl-pill{
  display:flex;align-items:center;gap:6px;
  background:var(--glass);backdrop-filter:blur(10px);
  border:1px solid var(--border);border-radius:var(--radius);
  padding:6px 10px;box-shadow:var(--shadow-float);
  font-size:11px;color:var(--muted);white-space:nowrap;
}
.ctrl-pill select{
  background:transparent;border:none;color:var(--text);
  font-size:11px;cursor:pointer;outline:none;
  font-family:var(--sans);
}
.ctrl-pill select option{background:var(--surface);color:var(--text)}

/* Toggle switch */
.tog{position:relative;width:34px;height:20px;flex-shrink:0}
.tog input{opacity:0;width:0;height:0}
.tog-s{position:absolute;inset:0;background:var(--border);border-radius:10px;transition:.2s;cursor:pointer}
.tog-s:before{content:'';position:absolute;width:14px;height:14px;left:3px;top:3px;background:var(--muted);border-radius:50%;transition:.2s}
input:checked+.tog-s{background:rgba(0,229,255,.25);border:1px solid var(--accent)}
input:checked+.tog-s:before{transform:translateX(14px);background:var(--accent)}
[data-theme="bright"] input:checked+.tog-s{background:rgba(2,132,199,.2);border-color:var(--accent)}
[data-theme="bright"] input:checked+.tog-s:before{background:var(--accent)}

/* ══════════════════════════════════════════════
   FLOATING CLIENT SELECTOR — bawah logo (mobile), atau panel kiri (desktop)
   ══════════════════════════════════════════════ */

/* mobile: dropdown di bawah logo group */
.float-selector{
  position:absolute;z-index:600;
  top:12px;left:12px;
  /* stack ke bawah dengan margin-top dinamis via JS --sel-top */
  margin-top:var(--sel-top, 110px);
  width:calc(100vw - 24px);max-width:320px;
}

.ms-trigger{
  width:100%;background:var(--glass);backdrop-filter:blur(10px);
  border:1px solid var(--border);border-radius:var(--radius);
  padding:8px 10px;display:flex;align-items:center;gap:6px;
  cursor:pointer;font-size:12px;color:var(--text);transition:border .15s;
  box-shadow:var(--shadow-float);
}
.ms-trigger:hover,.ms-trigger.open{border-color:var(--accent)}
.chips-area{flex:1;display:flex;gap:4px;flex-wrap:nowrap;overflow:hidden;min-width:0}
.chip{
  font-size:10px;font-family:var(--mono);padding:2px 7px;border-radius:2px;
  border:1px solid;white-space:nowrap;flex-shrink:0;
}
.chip-caret{color:var(--muted);font-size:12px;transition:transform .2s;flex-shrink:0;margin-left:auto}
.chip-caret.open{transform:rotate(180deg)}

.ms-dropdown{
  margin-top:4px;
  background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);
  z-index:700;max-height:260px;overflow-y:auto;display:none;
  box-shadow:var(--shadow-float);
}
.ms-dropdown.open{display:block}
.ms-item{
  display:flex;align-items:center;gap:8px;padding:9px 12px;
  cursor:pointer;font-size:12px;border-bottom:1px solid var(--border);
  transition:background .12s;
}
.ms-item:last-child{border-bottom:none}
.ms-item:hover{background:var(--surface2)}
.ms-item.selected{background:rgba(0,229,255,.06)}
[data-theme="bright"] .ms-item.selected{background:rgba(2,132,199,.06)}
.ms-check{
  width:14px;height:14px;border:1px solid var(--border);border-radius:2px;
  flex-shrink:0;display:flex;align-items:center;justify-content:center;
}
.ms-item.selected .ms-check{background:var(--accent);border-color:var(--accent)}
.ms-check-mark{color:#fff;font-size:10px;font-weight:700;display:none}
[data-theme="dark"] .ms-check-mark{color:#000}
.ms-item.selected .ms-check-mark{display:block}
.ms-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.ms-name{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ms-count{font-family:var(--mono);font-size:10px;color:var(--muted);flex-shrink:0}

/* Stats dalam selector — ISR + MW count per klien */
.ms-stats{display:flex;gap:4px;flex-shrink:0}
.ms-stat-chip{
  font-family:var(--mono);font-size:9px;color:var(--muted);
  background:var(--surface2);border:1px solid var(--border);
  border-radius:2px;padding:1px 5px;white-space:nowrap;
}

/* ══════════════════════════════════════════════
   MAP
   ══════════════════════════════════════════════ */
.leaflet-container{background:var(--leaflet-bg)!important;transition:background .25s}
.leaflet-control-zoom a{background:var(--surface)!important;color:var(--text)!important;border-color:var(--border)!important}
.leaflet-control-attribution{background:var(--glass)!important;color:var(--muted)!important;font-size:10px!important;backdrop-filter:blur(6px)!important}
.lf-tooltip{
  background:var(--surface)!important;border:1px solid var(--border)!important;
  border-radius:var(--radius)!important;color:var(--text)!important;
  font-family:var(--sans)!important;font-size:12px!important;
  padding:6px 10px!important;box-shadow:0 4px 12px rgba(0,0,0,.4)!important
}
.lf-tooltip b{color:var(--accent)}

/* Hexagon + label shadow */
.leaflet-marker-icon svg{filter:var(--shadow-hex)}
.link-count-label{
  background:rgba(12,15,20,.82);border:1px solid rgba(255,255,255,.3);
  border-radius:3px;font-family:var(--mono);font-size:10px;font-weight:700;
  color:#fff;padding:1px 5px;white-space:nowrap;pointer-events:none;
  transition:opacity .15s;line-height:1.4;
  text-shadow:0 1px 3px rgba(0,0,0,.8);
  box-shadow:0 1px 4px rgba(0,0,0,.6);
}
[data-theme="bright"] .link-count-label{
  background:rgba(255,255,255,.88);border-color:rgba(0,0,0,.2);color:#1e293b;
  text-shadow:0 1px 2px rgba(255,255,255,.8);box-shadow:0 1px 4px rgba(0,0,0,.2);
}

/* Skeleton hex */
.hex-skeleton{animation:hex-pulse 1.4s ease-in-out infinite}
@keyframes hex-pulse{0%,100%{opacity:.18}50%{opacity:.38}}
@keyframes spin{to{transform:rotate(360deg)}}
.spinner{width:24px;height:24px;border:2px solid var(--border);border-top-color:var(--accent);border-radius:50%;animation:spin .6s linear infinite}
.state-msg{display:flex;align-items:center;justify-content:center;height:100%;color:var(--muted);font-size:13px;flex-direction:column;gap:8px}

/* ══════════════════════════════════════════════
   LEAFLET CONTROLS POSITION
   ══════════════════════════════════════════════ */
.leaflet-top.leaflet-right{top:0;right:0}
.leaflet-top.leaflet-right .leaflet-control-zoom{
  margin-top:calc(var(--topbar-h,12px) + 8px)!important;
  margin-right:10px!important;
}
.leaflet-control-zoom{border:none!important;box-shadow:var(--shadow-float)!important}
.leaflet-control-zoom a{
  width:36px!important;height:36px!important;line-height:36px!important;
  font-size:18px!important;border-radius:4px!important;
  background:var(--glass)!important;backdrop-filter:blur(10px)!important;
  color:var(--text)!important;border:1px solid var(--border)!important;
}
.leaflet-control-zoom-in{border-radius:4px 4px 0 0!important;border-bottom:none!important}
.leaflet-control-zoom-out{border-radius:0 0 4px 4px!important}
/* Attribution kiri bawah */
.leaflet-bottom.leaflet-left{bottom:0;left:0}
/* desktop: safe area bawah */
.leaflet-bottom{padding-bottom:env(safe-area-inset-bottom,0)}

/* ══════════════════════════════════════════════
   MAP CONTROLS — GPS + Compass, kanan bawah zoom
   ══════════════════════════════════════════════ */
.map-controls{
  position:absolute;right:10px;z-index:599;
  display:flex;flex-direction:column;gap:8px;
  top:calc(var(--topbar-h,12px) + 94px);
}
.map-ctrl-btn{
  width:36px;height:36px;border-radius:4px;
  background:var(--glass);backdrop-filter:blur(10px);
  border:1px solid var(--border);color:var(--accent);
  font-size:16px;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  transition:background .15s,border-color .15s;
  box-shadow:var(--shadow-float);
}
.map-ctrl-btn:hover{background:var(--surface2);border-color:var(--accent)}
.compass-btn{color:var(--text)}
.compass-btn.rotated{border-color:var(--accent);color:var(--accent)}
#compassNeedle{display:block;transition:transform .3s ease}

/* Loc popup */
.loc-popup{
  position:absolute;right:56px;z-index:600;
  background:var(--surface);backdrop-filter:blur(10px);
  border:1px solid var(--border);border-radius:var(--radius);
  padding:10px 12px;display:none;flex-direction:column;gap:8px;
  min-width:220px;top:calc(var(--topbar-h,12px) + 94px);
  max-height:50vh;overflow-y:auto;
  box-shadow:var(--shadow-float);
}
.loc-popup.open{display:flex}
.loc-popup label{font-size:11px;color:var(--muted);display:flex;flex-direction:column;gap:3px}
.loc-popup input{
  background:var(--bg);border:1px solid var(--border);border-radius:4px;
  color:var(--text);font-family:var(--mono);font-size:12px;padding:5px 8px;
  outline:none;transition:border .15s;
}
.loc-popup input:focus{border-color:var(--accent)}
.loc-popup-btns{display:flex;gap:6px}
.loc-popup-btns button{
  flex:1;padding:6px;border-radius:4px;font-size:11px;cursor:pointer;
  border:1px solid var(--border);background:var(--surface);color:var(--text);
  transition:background .12s;
}
.loc-popup-btns button.primary{background:var(--accent);color:#000;border-color:var(--accent);font-weight:600}
.loc-popup-btns button:hover{background:var(--surface2)}
.loc-popup-btns button.primary:hover{background:#00c4dd}
[data-theme="bright"] .loc-popup-btns button.primary:hover{background:#0369a1}

/* ══════════════════════════════════════════════
   FOOTER STATS FLOATING — bottom center (mobile) / bawah di sidebar (desktop)
   ══════════════════════════════════════════════ */
.float-stats{
  position:absolute;bottom:calc(12px + env(safe-area-inset-bottom,0));
  left:50%;transform:translateX(-50%);
  z-index:600;
  display:flex;align-items:stretch;gap:0;
  background:var(--glass);backdrop-filter:blur(10px);
  border:1px solid var(--border);border-radius:var(--radius);
  box-shadow:var(--shadow-float);
  min-width:220px;
}
.stat-pill{
  display:flex;flex-direction:column;align-items:center;
  flex:1;padding:6px 12px;
}
.stat-pill+.stat-pill{border-left:1px solid var(--border)}
.sp-val{font-family:var(--mono);font-size:15px;color:var(--accent);font-weight:700}
.sp-lbl{font-size:9px;color:var(--muted);letter-spacing:.5px;text-transform:uppercase;margin-top:2px}

/* ══════════════════════════════════════════════
   LEGEND FLOATING — bawah stats
   ══════════════════════════════════════════════ */
.float-legend-btn{
  position:absolute;z-index:600;
  bottom:calc(60px + env(safe-area-inset-bottom,0));
  left:50%;transform:translateX(-50%);
  background:var(--glass);backdrop-filter:blur(10px);
  border:1px solid var(--border);border-radius:var(--radius);
  padding:5px 12px;font-size:11px;color:var(--muted);cursor:pointer;
  display:flex;align-items:center;gap:5px;
  box-shadow:var(--shadow-float);white-space:nowrap;
  transition:color .15s;
}
.float-legend-btn:hover{color:var(--text)}
.float-legend-btn.active{color:var(--accent)}
.legend-caret{font-size:10px;transition:transform .2s}
.float-legend-btn.active .legend-caret{transform:rotate(180deg)}

.legend-panel{
  position:absolute;z-index:605;
  bottom:calc(90px + env(safe-area-inset-bottom,0));
  left:50%;transform:translateX(-50%);
  background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);
  max-height:0;overflow:hidden;transition:max-height .28s ease;
  box-shadow:var(--shadow-float);min-width:140px;
}
.legend-panel.open{max-height:200px;overflow-y:auto}
.legend-inner{padding:8px 12px;display:flex;flex-wrap:wrap;gap:6px 14px}
.leg-row{display:flex;align-items:center;gap:5px;font-size:11px;color:var(--muted)}
.leg-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}

/* ══════════════════════════════════════════════
   DETAIL PANEL — bottom sheet (mobile)
   ══════════════════════════════════════════════ */
.detail-panel{
  position:absolute;bottom:0;left:0;right:0;
  background:var(--surface);border-top:2px solid var(--border);
  box-shadow:0 -4px 24px rgba(0,0,0,.35);
  max-height:60vh;height:auto;
  transform:translateY(100%);transition:transform .28s cubic-bezier(.4,0,.2,1);
  z-index:1000;display:flex;flex-direction:column
}
.detail-panel.open{transform:translateY(0)}
.panel-handle{
  padding:10px 16px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;gap:10px;flex-shrink:0;background:var(--surface2)
}
.panel-handle h3{font-family:var(--mono);font-size:12px;color:var(--accent);letter-spacing:1.5px;font-weight:700}
.panel-sub{font-size:11px;color:var(--muted);flex:1;margin-left:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.panel-breadcrumb{
  font-size:10px;color:var(--muted);font-family:var(--mono);
  display:flex;align-items:center;gap:6px;padding:6px 16px;
  border-bottom:1px solid var(--border);background:var(--bg);flex-shrink:0
}
.panel-breadcrumb span.active{color:var(--accent)}
.panel-close{
  background:none;border:none;color:var(--muted);cursor:pointer;
  font-size:16px;line-height:1;padding:2px 6px;margin-left:auto;transition:color .15s
}
.panel-close:hover{color:var(--text)}
.panel-body{flex:0 1 auto;overflow-y:auto;padding:12px 16px;max-height:calc(60vh - 80px)}
.panel-body::-webkit-scrollbar{width:4px}
.panel-body::-webkit-scrollbar-thumb{background:var(--border)}

/* ── PANEL TABS ── */
.panel-tabs{display:flex;gap:0;border-bottom:1px solid var(--border);overflow-x:auto;flex-shrink:0;scrollbar-width:none}
.panel-tabs::-webkit-scrollbar{display:none}
.panel-tab{
  padding:8px 14px;font-size:11px;font-family:var(--mono);letter-spacing:.5px;
  cursor:pointer;white-space:nowrap;border-bottom:2px solid transparent;
  color:var(--muted);transition:color .15s,border-color .15s;flex-shrink:0;
  display:flex;align-items:center;gap:6px;
}
.panel-tab:hover{color:var(--text)}
.panel-tab.active{color:var(--text);border-bottom-color:var(--tab-color,var(--accent))}
.panel-tab-dot{width:7px;height:7px;border-radius:50%;background:var(--tab-color,var(--accent));flex-shrink:0}
.panel-tab-pane{display:none}
.panel-tab-pane.active{display:block}

/* ── PANEL CONTENT ── */
.lawan-group{background:var(--bg);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:8px;box-shadow:0 1px 2px rgba(0,0,0,.18)}
.lawan-header{display:flex;align-items:center;gap:8px;padding:10px 12px;cursor:pointer;transition:background .12s;user-select:none}
.lawan-header:hover{background:var(--surface2)}
.lawan-header-static{display:flex;align-items:center;gap:8px;padding:10px 12px}
.lawan-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;box-shadow:0 0 0 3px rgba(255,255,255,.04)}
.lawan-dot.ghost{opacity:.55;outline:1px dashed currentColor;background:transparent!important;box-shadow:none}
.lawan-name{font-size:12.5px;font-weight:600;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text);letter-spacing:.2px}
.lawan-count{font-size:10px;color:var(--muted);background:var(--surface2);padding:2px 8px;border-radius:20px;flex-shrink:0;font-family:var(--mono)}
.lawan-chevron{color:var(--muted);font-size:13px;transition:transform .2s;flex-shrink:0}
.lawan-chevron.open{transform:rotate(180deg)}
.lawan-links{display:flex;flex-direction:column;gap:0;border-top:1px solid var(--border)}
.link-row{padding:10px 12px;border-bottom:1px solid var(--border);background:var(--surface)}
.link-row:last-child{border-bottom:none}
.link-id-row{display:flex;align-items:center;gap:6px;margin-bottom:8px;cursor:pointer}
.link-badge{font-family:var(--mono);font-size:10px;font-weight:700;padding:3px 9px;border-radius:4px;border:1px solid;letter-spacing:.5px;white-space:nowrap}
.isr-pair-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.isr-half{background:var(--surface2);border-radius:6px;padding:8px 10px;border:1px solid var(--border);border-top:2px solid var(--accent3)}
.isr-half:last-child{border-top-color:var(--accent2)}
.isr-half-ghost{opacity:.5}
.isr-half-label{font-size:8.5px;color:var(--accent3);text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;font-weight:700}
.isr-half:last-child .isr-half-label{color:var(--accent2)}
.isr-half-stn{font-size:12px;font-weight:600;color:var(--text);margin-bottom:4px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.isr-half-simf{font-size:10px;font-family:var(--mono);color:var(--muted);margin-bottom:3px;letter-spacing:.3px}
.isr-half-freq{font-size:10px;color:var(--muted);margin-top:2px}
.pill-tx{color:var(--accent);font-size:9px;font-family:var(--mono);font-weight:700}
.pill-rx{color:var(--accent2);font-size:9px;font-family:var(--mono);font-weight:700}
.isr-pair-grid{margin-bottom:8px}
.link-info-bar{display:grid;grid-template-columns:repeat(4,1fr);gap:5px;margin:8px 0}
.info-chip{background:var(--surface2);border:1px solid var(--border);border-radius:6px;padding:6px 8px;display:flex;flex-direction:column;gap:2px;min-width:0}
.info-chip-label{font-size:9px;color:var(--muted);text-transform:uppercase;letter-spacing:.4px}
.info-chip-val{font-size:11px;color:var(--text);font-family:var(--mono);font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.info-chip-val.good{color:var(--green)}.info-chip-val.warn{color:var(--yellow)}.info-chip-val.danger{color:var(--red)}
.ant-specs{display:grid;grid-template-columns:1fr 1fr;gap:5px;margin-bottom:8px}
.ant-half{background:var(--surface2);border:1px solid var(--border);border-radius:6px;padding:7px 9px}
.ant-half-label{font-size:9px;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:5px;font-weight:700}
.ant-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:3px}
.ant-row:last-child{margin-bottom:0}
.ant-key{font-size:10px;color:var(--muted)}.ant-val{font-size:11px;color:var(--text);font-family:var(--mono);font-weight:500}
.mw-extra-grid{display:grid;grid-template-columns:auto 1fr;gap:4px 10px;font-size:11px;margin-bottom:8px;padding:8px 9px;background:var(--surface2);border:1px solid var(--border);border-radius:6px}
.mw-extra-grid .k{color:var(--muted)}.mw-extra-grid .v{color:var(--text);font-family:var(--mono);font-weight:500}
.mwlink-detail{display:none;padding:8px 0 2px}
.mwlink-detail.open{display:block}
.mw-meta-grid{display:grid;grid-template-columns:1fr 1fr;gap:4px 12px;font-size:11px}
.mw-meta-grid .k{color:var(--muted)}.mw-meta-grid .v{color:var(--text);font-weight:500}
.mw-chevron{color:var(--muted);font-size:13px;transition:transform .2s;flex-shrink:0}
.mw-chevron.open{transform:rotate(180deg)}
.goto-btn{
  display:flex;align-items:center;justify-content:center;gap:6px;
  margin-top:10px;width:100%;padding:8px 12px;border:1px solid var(--accent);
  border-radius:6px;background:rgba(0,229,255,.05);color:var(--accent);
  font-size:12px;font-weight:600;cursor:pointer;transition:background .12s
}
.goto-btn:hover{background:rgba(0,229,255,.12)}
.chip-ok{color:var(--green);border-color:var(--green);background:rgba(34,197,94,.08)}
.chip-exp{color:var(--red);border-color:var(--red);background:rgba(239,68,68,.08)}
.chip-soon{color:var(--yellow);border-color:var(--yellow);background:rgba(234,179,8,.08)}

/* ══════════════════════════════════════════════
   DESKTOP — sidebar kanan
   ══════════════════════════════════════════════ */
@media(min-width:769px){
  /* Logo tetap di kiri atas */
  .float-logo{top:12px;left:12px}

  /* Controls di kiri bawah logo */
  .float-controls{
    top:60px;left:12px;margin-top:0;
  }

  /* Selector — floating di kiri, di bawah controls */
  .float-selector{
    top:auto;left:12px;margin-top:0;
    width:260px;max-width:260px;
    /* posisi otomatis di bawah float-controls via JS */
    position:absolute;
  }

  /* Detail panel — SIDEBAR kanan, bukan bottom sheet */
  .detail-panel{
    position:absolute;
    top:0;right:0;bottom:0;left:auto;
    width:340px;
    max-height:100dvh;
    transform:translateX(100%);
    transition:transform .28s cubic-bezier(.4,0,.2,1);
    border-top:none;border-left:2px solid var(--border);
    border-radius:0;
    z-index:1000;
  }
  .detail-panel.open{transform:translateX(0)}
  .panel-body{max-height:calc(100dvh - 120px)}

  /* Stats — bottom left, di bawah selector */
  .float-stats{
    left:12px;transform:none;
    bottom:calc(12px + env(safe-area-inset-bottom,0));
    min-width:200px;
  }

  /* Legend — di atas stats, kiri */
  .float-legend-btn{
    left:12px;transform:none;
    bottom:calc(60px + env(safe-area-inset-bottom,0));
  }
  .legend-panel{
    left:12px;transform:none;
    bottom:calc(90px + env(safe-area-inset-bottom,0));
  }

  /* Zoom kanan atas — tetap */
  .leaflet-top.leaflet-right .leaflet-control-zoom{
    margin-top:12px!important;
    margin-right:10px!important;
  }
  .map-controls{
    top:calc(12px + 74px + 12px); /* 12px top + 74px zoom + 12px gap */
    right:10px;
  }
  .loc-popup{
    right:56px;
    top:calc(12px + 74px + 12px);
  }
}

/* ══════════════════════════════════════════════
   MOBILE — layout ulang total
   ══════════════════════════════════════════════ */
@media(max-width:768px){

  /* Sembunyikan elemen desktop yang tidak dipakai di mobile */
  .float-logo{display:none}
  .float-controls{display:none}
  .float-selector{display:none}
  .float-stats{display:none}
  .float-legend-btn{display:none}
  .legend-panel{display:none}
  .map-controls{display:none}
  .loc-popup{display:none!important}
  .leaflet-top.leaflet-right .leaflet-control-zoom{display:none!important}

  /* ── FAB kiri atas: tombol KOMENK / settings ── */
  .mob-fab-logo{
    position:absolute;top:12px;left:12px;z-index:600;
    width:40px;height:40px;border-radius:10px;
    background:var(--glass);backdrop-filter:blur(10px);
    border:1px solid var(--border);box-shadow:var(--shadow-float);
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;
    font-family:var(--mono);font-size:13px;font-weight:700;color:var(--accent);
    letter-spacing:1px;
  }

  /* FAB kanan atas: zoom +/- */
  .mob-fab-zoom{
    position:absolute;top:12px;right:12px;z-index:600;
    display:flex;flex-direction:column;gap:4px;
  }
  .mob-fab-btn{
    width:40px;height:40px;border-radius:10px;
    background:var(--glass);backdrop-filter:blur(10px);
    border:1px solid var(--border);box-shadow:var(--shadow-float);
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;font-size:20px;color:var(--text);
    transition:background .12s;-webkit-tap-highlight-color:transparent;
    touch-action:manipulation;
  }
  .mob-fab-btn:active{background:var(--surface2)}
  .mob-fab-btn.accent{color:var(--accent);font-size:16px}

  /* ── BOTTOM BAR — client selector + stats ── */
  .mob-bar{
    position:absolute;bottom:0;left:0;right:0;z-index:600;
    background:var(--glass);backdrop-filter:blur(14px);
    border-top:1px solid var(--border);
    padding:8px 12px calc(8px + env(safe-area-inset-bottom));
    display:flex;align-items:center;gap:8px;
    box-shadow:0 -2px 20px rgba(0,0,0,.35);
  }
  /* Trigger klien — kiri, flex-grow */
  .mob-sel-trigger{
    flex:1;min-width:0;
    display:flex;align-items:center;gap:6px;
    background:var(--surface2);border:1px solid var(--border);
    border-radius:8px;padding:8px 10px;cursor:pointer;
    transition:border-color .15s;
    touch-action:manipulation;-webkit-tap-highlight-color:transparent;
  }
  .mob-sel-trigger.open,.mob-sel-trigger:active{border-color:var(--accent)}
  .mob-sel-chips{flex:1;min-width:0;display:flex;gap:4px;overflow:hidden}
  .mob-sel-caret{color:var(--muted);font-size:12px;flex-shrink:0;transition:transform .2s}
  .mob-sel-caret.open{transform:rotate(180deg)}

  /* Stats kanan — compact */
  .mob-stats{display:flex;gap:0;flex-shrink:0}
  .mob-stat{
    display:flex;flex-direction:column;align-items:center;
    padding:0 8px;border-left:1px solid var(--border);
  }
  .mob-stat:first-child{border-left:none}
  .mob-stat-val{font-family:var(--mono);font-size:13px;font-weight:700;color:var(--accent);line-height:1.2}
  .mob-stat-lbl{font-size:8px;color:var(--muted);letter-spacing:.4px;text-transform:uppercase}

  /* ── SHEET: client list (slide up dari bottom bar) ── */
  .mob-sheet{
    position:absolute;left:0;right:0;bottom:0;z-index:700;
    background:var(--surface);
    border-radius:16px 16px 0 0;border-top:1px solid var(--border);
    transform:translateY(100%);transition:transform .28s cubic-bezier(.4,0,.2,1);
    max-height:70dvh;display:flex;flex-direction:column;
    box-shadow:0 -4px 32px rgba(0,0,0,.45);
  }
  .mob-sheet.open{transform:translateY(0)}
  .mob-sheet-handle{
    padding:10px 16px 6px;flex-shrink:0;
    display:flex;align-items:center;justify-content:space-between;
    border-bottom:1px solid var(--border);
  }
  .mob-sheet-handle::before{
    content:'';display:block;width:36px;height:4px;border-radius:2px;
    background:var(--border);position:absolute;top:6px;left:50%;transform:translateX(-50%);
  }
  .mob-sheet-title{font-family:var(--mono);font-size:11px;color:var(--muted);letter-spacing:1px}
  .mob-sheet-close{
    background:none;border:none;color:var(--muted);cursor:pointer;
    font-size:16px;padding:4px 6px;
  }
  .mob-sheet-list{flex:1;overflow-y:auto;overscroll-behavior:contain}
  .mob-sheet-list::-webkit-scrollbar{display:none}

  /* item dalam sheet list — lebih besar untuk touch */
  .mob-sheet-list .ms-item{padding:14px 16px;min-height:56px;font-size:14px;border-bottom:1px solid var(--border)}
  .mob-sheet-list .ms-item:last-child{border-bottom:none}
  .mob-sheet-list .ms-check{width:18px;height:18px}
  .mob-sheet-list .ms-dot{width:10px;height:10px}
  .mob-sheet-list .ms-stat-chip{font-size:10px;padding:2px 6px}
  .mob-sheet-list .ms-name{font-size:14px}

  /* ── DRAWER: settings (slide dari kiri) ── */
  .mob-drawer{
    position:absolute;top:0;left:0;bottom:0;z-index:800;
    width:260px;background:var(--surface);
    border-right:1px solid var(--border);
    transform:translateX(-100%);transition:transform .25s cubic-bezier(.4,0,.2,1);
    display:flex;flex-direction:column;
    box-shadow:4px 0 24px rgba(0,0,0,.4);
    padding-top:env(safe-area-inset-top,0);
  }
  .mob-drawer.open{transform:translateX(0)}
  .mob-drawer-scrim{
    position:absolute;inset:0;z-index:790;
    background:rgba(0,0,0,.45);
    opacity:0;pointer-events:none;transition:opacity .25s;
  }
  .mob-drawer-scrim.open{opacity:1;pointer-events:auto}
  .mob-drawer-head{
    padding:16px;border-bottom:1px solid var(--border);flex-shrink:0;
    display:flex;align-items:center;gap:8px;
  }
  .mob-drawer-logo{font-family:var(--mono);font-size:14px;color:var(--accent);letter-spacing:3px}
  .mob-drawer-sub{font-size:9px;color:var(--muted);letter-spacing:1px;line-height:1.4}
  .mob-drawer-body{flex:1;overflow-y:auto;padding:12px 0}
  .mob-drawer-section{padding:0 16px 12px}
  .mob-drawer-label{font-size:9px;color:var(--muted);letter-spacing:1.5px;text-transform:uppercase;margin-bottom:8px;padding:0 0 4px;border-bottom:1px solid var(--border)}
  .mob-drawer-row{
    display:flex;align-items:center;justify-content:space-between;
    padding:10px 0;border-bottom:1px solid rgba(255,255,255,.04);
    font-size:13px;color:var(--text);
  }
  .mob-drawer-row:last-child{border-bottom:none}
  .mob-drawer-select{
    background:var(--surface2);border:1px solid var(--border);
    border-radius:6px;color:var(--text);font-size:12px;
    padding:5px 8px;outline:none;cursor:pointer;
    font-family:var(--sans);
  }
  .mob-drawer-select option{background:var(--surface)}

  /* Legend inside drawer */
  .mob-drawer-legend{padding:0 16px 12px}
  .mob-legend-items{display:flex;flex-direction:column;gap:6px;margin-top:6px}
  .mob-legend-row{display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted)}
  .mob-legend-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0}

  /* GPS / Loc drawer section */
  .mob-loc-section{padding:0 16px 12px}
  .mob-loc-input{
    display:flex;flex-direction:column;gap:6px;margin-top:6px;
  }
  .mob-loc-input label{font-size:11px;color:var(--muted);display:flex;flex-direction:column;gap:3px}
  .mob-loc-input input{
    background:var(--bg);border:1px solid var(--border);border-radius:6px;
    color:var(--text);font-family:var(--mono);font-size:13px;padding:8px 10px;
    outline:none;transition:border .15s;
  }
  .mob-loc-input input:focus{border-color:var(--accent)}
  .mob-loc-btns{display:flex;gap:6px;margin-top:6px}
  .mob-loc-btns button{
    flex:1;padding:9px 6px;border-radius:6px;font-size:12px;cursor:pointer;
    border:1px solid var(--border);background:var(--surface2);color:var(--text);
    touch-action:manipulation;
  }
  .mob-loc-btns button.primary{background:var(--accent);color:#000;border-color:var(--accent);font-weight:700}

  /* ── Detail panel mobile (bottom sheet) ── */
  .detail-panel{
    position:absolute;bottom:0;left:0;right:0;top:auto;width:auto;
    max-height:82dvh;height:auto;
    transform:translateY(100%);
    border-radius:16px 16px 0 0;
    border-top:1px solid var(--border);border-left:none;
    z-index:900;
    padding-bottom:env(safe-area-inset-bottom);
  }
  .detail-panel.open{transform:translateY(0)}
  .panel-body{
    max-height:calc(82dvh - 110px - env(safe-area-inset-bottom));
    padding:10px 12px;overflow-y:auto;overscroll-behavior:contain;
  }
  .panel-body::-webkit-scrollbar{display:none}
  .panel-handle{padding:16px 12px 8px;position:relative}
  .panel-handle::before{
    content:'';display:block;width:36px;height:4px;border-radius:2px;
    background:var(--border);position:absolute;top:6px;left:50%;transform:translateX(-50%);
  }
  .panel-handle h3{font-size:12px}
  .panel-sub{font-size:11px}

  /* Panel content touch-friendly */
  .lawan-header{padding:14px 12px;min-height:52px}
  .lawan-name{font-size:14px}
  .link-row{padding:12px 10px}
  .link-id-row{min-height:48px;align-items:center}
  .link-badge{font-size:11px;padding:5px 10px}
  .link-info-bar{grid-template-columns:repeat(2,1fr);gap:6px}
  .info-chip{padding:8px 9px}
  .info-chip-val{font-size:13px}
  .info-chip-label{font-size:10px}
  .isr-pair-grid{grid-template-columns:1fr;gap:6px}
  .isr-half{padding:10px 11px}
  .isr-half-stn{font-size:13px}
  .goto-btn{padding:14px;font-size:13px;min-height:48px}
}

/* Landscape mobile */
@media(max-width:768px) and (orientation:landscape){
  .mob-bar{padding:5px 12px calc(5px + env(safe-area-inset-bottom))}
  .mob-sheet{max-height:80dvh}
  .detail-panel{max-height:88dvh;border-radius:0}
  .panel-body{max-height:calc(88dvh - 100px - env(safe-area-inset-bottom))}
  .panel-handle::before{display:none}
  .panel-handle{padding:8px 12px}
  .isr-pair-grid{grid-template-columns:1fr 1fr}
}

@media(max-width:360px){
  .mob-drawer{width:230px}
  .link-info-bar{grid-template-columns:repeat(2,1fr)}
  .panel-body{padding:8px 10px}
}
</style>
</head>
<body>
<div class="app">
  <!-- PETA FULL SCREEN -->
  <div id="map"></div>

  <!-- State overlay (loading / pilih klien) -->
  <div id="stateOverlay" class="state-msg" style="position:absolute;inset:0;background:transparent;z-index:500;pointer-events:none"></div>

  <!-- FLOATING: Logo + tema -->
  <div class="float-logo" id="floatLogo">
    <span class="logo-mark">KOMENK</span>
    <div class="logo-sep"></div>
    <span class="logo-sub">KOORDINAT MICROWAVE LINK</span>
  </div>

  <!-- FLOATING: Controls (basemap, link toggle, theme) -->
  <div class="float-controls" id="floatControls">
    <div class="ctrl-pill">
      <span>Peta</span>
      <select id="basemapSel">
        <option value="dark">🌑 Dark</option>
        <option value="bright">☀️ Bright</option>
        <option value="satellite">🛰️ Satelit</option>
      </select>
    </div>
    <div class="ctrl-pill">
      <span>Tema UI</span>
      <label class="tog" title="Ganti tema UI">
        <input type="checkbox" id="themeToggle">
        <span class="tog-s"></span>
      </label>
    </div>
    <div class="ctrl-pill">
      <span>Garis link</span>
      <label class="tog">
        <input type="checkbox" id="linkToggle">
        <span class="tog-s"></span>
      </label>
    </div>
  </div>

  <!-- FLOATING: Client selector -->
  <div class="float-selector" id="floatSelector">
    <div class="ms-trigger" id="msTrigger" onclick="toggleDropdown()">
      <div class="chips-area" id="chipsArea">
        <span class="chip" style="border-color:var(--accent);color:var(--accent)">Memuat…</span>
      </div>
      <span class="chip-caret" id="chipCaret">▾</span>
    </div>
    <div class="ms-dropdown" id="msDropdown"></div>
  </div>

  <!-- MAP CONTROLS: GPS + Compass -->
  <div class="map-controls" id="mapControls">
    <button class="map-ctrl-btn" id="locBtn" title="Lokasi saya">&#9678;</button>
    <button class="map-ctrl-btn compass-btn" id="compassBtn" title="Reset rotasi peta">
      <svg id="compassNeedle" width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
        <polygon points="9,1 11,9 9,17 7,9" fill="currentColor" opacity=".4"/>
        <polygon points="9,1 11,9 9,8 7,9" fill="#ef4444"/>
      </svg>
    </button>
  </div>

  <!-- Loc popup -->
  <div class="loc-popup" id="locPopup">
    <label>Latitude<input type="number" id="manualLat" placeholder="-7.8013" step="any"></label>
    <label>Longitude<input type="number" id="manualLng" placeholder="110.3649" step="any"></label>
    <div class="loc-popup-btns">
      <button onclick="useManualLoc()">Pakai</button>
      <button onclick="useGPSLoc()" title="Deteksi otomatis">&#9678; GPS</button>
      <button class="primary" onclick="goToLoc()">Go</button>
    </div>
  </div>

  <!-- DETAIL PANEL (bottom sheet mobile / sidebar desktop) -->
  <div class="detail-panel" id="detailPanel">
    <div class="panel-handle">
      <h3 id="panelTitle">DETAIL</h3>
      <span class="panel-sub" id="panelSub"></span>
      <button class="panel-close" id="panelClose" title="Tutup">✕</button>
    </div>
    <div class="panel-tabs" id="panelTabs" style="display:none"></div>
    <div class="panel-breadcrumb" id="panelBreadcrumb" style="display:none"></div>
    <div class="panel-body" id="panelBody"></div>
  </div>

  <!-- FLOATING: Stats bawah -->
  <div class="float-stats" id="floatStats">
    <div class="stat-pill">
      <span class="sp-val" id="footIsr">—</span>
      <span class="sp-lbl">ISR</span>
    </div>
    <div class="stat-pill">
      <span class="sp-val" id="footLink">—</span>
      <span class="sp-lbl">MW Link</span>
    </div>
    <div class="stat-pill">
      <span class="sp-val" id="footKlien">—</span>
      <span class="sp-lbl">Klien</span>
    </div>
  </div>

  <!-- FLOATING: Legend -->
  <button class="float-legend-btn" id="legendBtn" onclick="toggleLegend()">
    Legenda <span class="legend-caret">▲</span>
  </button>
  <div class="legend-panel" id="legendPanel">
    <div class="legend-inner" id="legendInner"></div>
  </div>

  <!-- ══ MOBILE ELEMENTS ══ -->
  <div class="mob-fab-logo" id="mobFabLogo" onclick="mobOpenDrawer()">K</div>

  <div class="mob-fab-zoom">
    <div class="mob-fab-btn" onclick="map.zoomIn()">+</div>
    <div class="mob-fab-btn" onclick="map.zoomOut()">−</div>
    <div class="mob-fab-btn accent" id="mobLocBtn" onclick="mobToggleGPS()">◎</div>
  </div>

  <div class="mob-bar" id="mobBar">
    <div class="mob-sel-trigger" id="mobSelTrigger" onclick="mobToggleSheet()">
      <div class="mob-sel-chips" id="mobSelChips">
        <span style="color:var(--muted);font-size:11px">Pilih klien…</span>
      </div>
      <span class="mob-sel-caret" id="mobSelCaret">▲</span>
    </div>
    <div class="mob-stats">
      <div class="mob-stat">
        <span class="mob-stat-val" id="mobIsr">—</span>
        <span class="mob-stat-lbl">ISR</span>
      </div>
      <div class="mob-stat">
        <span class="mob-stat-val" id="mobMw">—</span>
        <span class="mob-stat-lbl">MW</span>
      </div>
    </div>
  </div>

  <div class="mob-sheet" id="mobSheet">
    <div class="mob-sheet-handle">
      <span class="mob-sheet-title">PILIH KLIEN</span>
      <button class="mob-sheet-close" onclick="mobCloseSheet()">✕</button>
    </div>
    <div class="mob-sheet-list" id="mobSheetList"></div>
  </div>

  <div class="mob-drawer-scrim" id="mobScrim" onclick="mobCloseDrawer()"></div>

  <div class="mob-drawer" id="mobDrawer">
    <div class="mob-drawer-head">
      <div>
        <div class="mob-drawer-logo">KOMENK</div>
        <div class="mob-drawer-sub">KOORDINAT MICROWAVE LINK</div>
      </div>
    </div>
    <div class="mob-drawer-body">
      <div class="mob-drawer-section">
        <div class="mob-drawer-label">Tampilan Peta</div>
        <div class="mob-drawer-row">
          <span>Basemap</span>
          <select class="mob-drawer-select" id="mobBasemap" onchange="mobSwitchBasemap(this.value)">
            <option value="dark">🌑 Dark</option>
            <option value="bright">☀️ Bright</option>
            <option value="satellite">🛰️ Satelit</option>
          </select>
        </div>
        <div class="mob-drawer-row">
          <span>Tema UI</span>
          <label class="tog"><input type="checkbox" id="mobThemeToggle" onchange="mobSwitchTheme(this.checked)"><span class="tog-s"></span></label>
        </div>
        <div class="mob-drawer-row">
          <span>Garis Link</span>
          <label class="tog"><input type="checkbox" id="mobLinkToggle" onchange="mobSwitchLinks(this.checked)"><span class="tog-s"></span></label>
        </div>
      </div>
      <div class="mob-loc-section">
        <div class="mob-drawer-label">Koordinat / GPS</div>
        <div class="mob-loc-input">
          <label>Latitude<input type="number" id="mobLat" placeholder="-7.8013" step="any"></label>
          <label>Longitude<input type="number" id="mobLng" placeholder="110.3649" step="any"></label>
        </div>
        <div class="mob-loc-btns">
          <button onclick="mobGPS()">◎ GPS</button>
          <button class="primary" onclick="mobGoLoc()">Go</button>
        </div>
      </div>
      <div class="mob-drawer-legend" id="mobDrawerLegend" style="display:none">
        <div class="mob-drawer-label">Legenda</div>
        <div class="mob-legend-items" id="mobLegendItems"></div>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js"></script>
<script>
/* ════════════════════════════════════════════════════
   KOMENK — JS Core
   ════════════════════════════════════════════════════ */

/* ── State ── */
const S = {
  clients:[],selectedIds:new Set(),
  allLayersData:{},
  hexLayers:{},edgeLayers:{},
  currentHexLayer:null,
  showLinks:false,
  userMarker:null,userLat:null,userLng:null,
  locWatchId:null,
  mapBearing:0,
  currentSiteData:null,
  currentTabId:null,
  legendOpen:false,
  dropdownOpen:false,
};

/* ── Client Colors ── */
const CLIENT_COLORS = [
  '#00e5ff','#ff6b35','#a78bfa','#22c55e','#eab308',
  '#f472b6','#38bdf8','#fb923c','#4ade80','#c084fc'
];
function clientColor(i){ return CLIENT_COLORS[i % CLIENT_COLORS.length] }

/* ── Basemap tiles ── */
const TILES = {
  dark:{
    url:'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
    attr:'&copy; <a href="https://carto.com/">CARTO</a>'
  },
  bright:{
    url:'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
    attr:'&copy; <a href="https://carto.com/">CARTO</a>'
  },
  satellite:{
    url:'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
    attr:'Tiles &copy; Esri'
  }
};

/* ══════════════════════════════════════════════
   MAP INIT
   ══════════════════════════════════════════════ */
const map = L.map('map',{
  center:[-2.5,118],zoom:5,
  zoomControl:false,
  attributionControl:true,
});
L.control.zoom({position:'topright'}).addTo(map);

let currentTileLayer = L.tileLayer(TILES.dark.url,{
  maxZoom:19,subdomains:'abcd',attribution:TILES.dark.attr
}).addTo(map);

/* ── Remove Leaflet default logo from controls (hilangkan logo leaflet) ── */
map.attributionControl.setPrefix('');

/* ── Show welcome msg tapi peta tetap ada ── */
function setOverlay(html){
  const el = document.getElementById('stateOverlay');
  el.innerHTML = html;
  el.style.pointerEvents = html ? 'none' : 'none';
}

/* ══════════════════════════════════════════════
   THEME
   ══════════════════════════════════════════════ */
const themeToggle = document.getElementById('themeToggle');
themeToggle.addEventListener('change', () => {
  const isDark = !themeToggle.checked;
  document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'bright');
  // Jika basemap sinkron dengan tema, update tiles juga
  const bsel = document.getElementById('basemapSel').value;
  if(bsel === 'dark' || bsel === 'bright'){
    if(isDark && bsel !== 'dark'){
      document.getElementById('basemapSel').value = 'dark';
      switchBasemap('dark');
    } else if(!isDark && bsel !== 'bright'){
      document.getElementById('basemapSel').value = 'bright';
      switchBasemap('bright');
    }
  }
});

function switchBasemap(key){
  const t = TILES[key] || TILES.dark;
  map.removeLayer(currentTileLayer);
  currentTileLayer = L.tileLayer(t.url,{maxZoom:19,subdomains:'abcd',attribution:t.attr}).addTo(map);
  currentTileLayer.bringToBack();
}

document.getElementById('basemapSel').addEventListener('change', e => {
  const v = e.target.value;
  switchBasemap(v);
  if(v === 'dark'){
    document.documentElement.setAttribute('data-theme','dark');
    themeToggle.checked = false;
  } else if(v === 'bright'){
    document.documentElement.setAttribute('data-theme','bright');
    themeToggle.checked = true;
  }
});

/* ══════════════════════════════════════════════
   LAYOUT: posisi floating elements
   ══════════════════════════════════════════════ */
function recalcLayout(){
  const logo = document.getElementById('floatLogo');
  const ctrl = document.getElementById('floatControls');
  const sel  = document.getElementById('floatSelector');
  const isDesktop = window.innerWidth >= 769;

  if(isDesktop){
    const logoH = logo.getBoundingClientRect().height;
    ctrl.style.top = (12 + logoH + 8) + 'px';
    ctrl.style.left = '12px';
    ctrl.style.marginTop = '0';
    const ctrlH = ctrl.getBoundingClientRect().height;
    const ctrlTop = parseFloat(ctrl.style.top)||60;
    sel.style.top = (ctrlTop + ctrlH + 8) + 'px';
    sel.style.left = '12px';
    sel.style.marginTop = '0';
  } else {
    ctrl.style.top = '';
    ctrl.style.left = '';
    ctrl.style.marginTop = '';
    sel.style.top = '';
    sel.style.left = '';
    sel.style.marginTop = '';
    const logoH = logo.getBoundingClientRect().height;
    const ctrlH = ctrl.getBoundingClientRect().height;
    ctrl.style.position = 'absolute';
    ctrl.style.top = (12 + logoH + 8) + 'px';
    const ctrlTop2 = 12 + logoH + 8 + ctrlH + 8;
    sel.style.position = 'absolute';
    sel.style.top = ctrlTop2 + 'px';
    sel.style.width = 'calc(100vw - 24px)';
    sel.style.maxWidth = '320px';
  }
}
window.addEventListener('resize', recalcLayout);

/* ══════════════════════════════════════════════
   CLIENT API
   ══════════════════════════════════════════════ */
async function fetchClients(){
  const r = await fetch('api/clients.php');
  const d = await r.json();
  if(!d.success) throw new Error(d.error||'Gagal load klien');
  return d.data;
}

async function fetchSiteData(clientId){
  const r = await fetch(`api/isr.php?client_id=${clientId}`);
  const d = await r.json();
  if(!d.success) throw new Error(d.error||'Gagal load ISR');
  return d;
}

async function fetchLinkDetail(isrId){
  const r = await fetch(`api/link_detail.php?isr_id=${isrId}`);
  const d = await r.json();
  if(!d.success) throw new Error(d.error||'Gagal load detail');
  return d.data;
}

/* ══════════════════════════════════════════════
   DROPDOWN
   ══════════════════════════════════════════════ */
function toggleDropdown(){
  S.dropdownOpen = !S.dropdownOpen;
  document.getElementById('msDropdown').classList.toggle('open', S.dropdownOpen);
  document.getElementById('chipCaret').classList.toggle('open', S.dropdownOpen);
  document.getElementById('msTrigger').classList.toggle('open', S.dropdownOpen);
}

document.addEventListener('click', e => {
  if(!document.getElementById('floatSelector').contains(e.target)){
    S.dropdownOpen = false;
    document.getElementById('msDropdown').classList.remove('open');
    document.getElementById('chipCaret').classList.remove('open');
    document.getElementById('msTrigger').classList.remove('open');
  }
});

function renderDropdown(){
  const dd = document.getElementById('msDropdown');
  dd.innerHTML = S.clients.map((c,i) => {
    const col = clientColor(i);
    const sel = S.selectedIds.has(c.id);
    const ld = S.allLayersData[c.id];
    const isrN = ld ? (ld.sites||[]).length : (c.isr_count||'—');
    const mwN  = ld ? (ld.edges||[]).length : (c.mw_count||'—');
    return `<div class="ms-item${sel?' selected':''}" onclick="toggleClient(${c.id})" style="--c:${col}">
      <div class="ms-check"><span class="ms-check-mark">✓</span></div>
      <div class="ms-dot" style="background:${col}"></div>
      <span class="ms-name">${c.name}</span>
      <div class="ms-stats">
        <span class="ms-stat-chip">${isrN}ISR</span>
        <span class="ms-stat-chip">${mwN}MW</span>
      </div>
    </div>`;
  }).join('');
}

function renderChips(){
  const area = document.getElementById('chipsArea');
  const sel = S.clients.filter(c => S.selectedIds.has(c.id));
  if(sel.length === 0){
    area.innerHTML = `<span style="color:var(--muted);font-size:11px">Pilih klien untuk melihat…</span>`;
    return;
  }
  area.innerHTML = sel.map((c,i) => {
    const col = clientColor(S.clients.indexOf(c));
    return `<span class="chip" style="color:${col};border-color:${col}">${c.name}</span>`;
  }).join('');
}

function updateFooterStats(){
  let totalIsr=0, totalMw=0, totalCl=0;
  S.clients.forEach((c,i) => {
    if(S.selectedIds.has(c.id)){
      totalCl++;
      const ld = S.allLayersData[c.id];
      if(ld){
        totalIsr += (ld.sites||[]).length;
        totalMw  += (ld.edges||[]).length;
      }
    }
  });
  document.getElementById('footIsr').textContent  = totalIsr||'—';
  document.getElementById('footLink').textContent = totalMw||'—';
  document.getElementById('footKlien').textContent= totalCl||'—';
}

/* ══════════════════════════════════════════════
   HEXAGON MARKER
   ══════════════════════════════════════════════ */
function hexPath(r){
  const pts = [];
  for(let i=0;i<6;i++){
    const a = Math.PI/180*(60*i-30);
    pts.push([r+r*Math.cos(a), r+r*Math.sin(a)]);
  }
  return 'M'+pts.map(p=>p.join(',')).join('L')+'Z';
}

function makeHexIcon(color, count, size=26){
  const r = size/2;
  const hex = hexPath(r);
  const svg = `<svg width="${size}" height="${size}" viewBox="0 0 ${size} ${size}" xmlns="http://www.w3.org/2000/svg">
    <path d="${hex}" fill="${color}" fill-opacity=".18" stroke="${color}" stroke-width="1.5"/>
    <path d="${hex}" fill="none" stroke="${color}" stroke-width=".5" opacity=".35"/>
    ${count>1?`<text x="${r}" y="${r+3.5}" text-anchor="middle" font-size="8.5" font-family="Space Mono,monospace" fill="${color}" font-weight="700">${count>99?'99+':count}</text>`:''}
  </svg>`;
  return L.divIcon({
    html:svg,
    className:'',
    iconSize:[size,size],
    iconAnchor:[size/2,size/2],
    popupAnchor:[0,-size/2]
  });
}

/* ══════════════════════════════════════════════
   RENDER MAP LAYERS
   ══════════════════════════════════════════════ */
function clearClientLayers(cid){
  if(S.hexLayers[cid]){map.removeLayer(S.hexLayers[cid]);delete S.hexLayers[cid]}
  if(S.edgeLayers[cid]){map.removeLayer(S.edgeLayers[cid]);delete S.edgeLayers[cid]}
}

function renderClientLayer(cid){
  clearClientLayers(cid);
  const ld = S.allLayersData[cid];
  if(!ld) return;
  const ci = S.clients.findIndex(c=>c.id===cid);
  const color = clientColor(ci<0?0:ci);

  // Edge layer
  const edgeGroup = L.layerGroup();
  if(S.showLinks && ld.edges){
    ld.edges.forEach(edge => {
      if(edge.from_lat && edge.from_lng && edge.to_lat && edge.to_lng){
        L.polyline([
          [parseFloat(edge.from_lat), parseFloat(edge.from_lng)],
          [parseFloat(edge.to_lat),   parseFloat(edge.to_lng)]
        ],{color, weight:1.5, opacity:.7, dashArray:'4,3'}).addTo(edgeGroup);
      }
    });
  }
  edgeGroup.addTo(map);
  S.edgeLayers[cid] = edgeGroup;

  // Hex markers
  const hexGroup = L.layerGroup();
  (ld.sites||[]).forEach(site => {
    if(!site.lat || !site.lng) return;
    const lat = parseFloat(site.lat), lng = parseFloat(site.lng);
    if(isNaN(lat)||isNaN(lng)) return;
    const cnt = site.link_count||1;
    const icon = makeHexIcon(color, cnt);
    const m = L.marker([lat,lng],{icon}).addTo(hexGroup);
    m.bindTooltip(
      `<b>${site.site_name||site.isr_id}</b><br>${cnt} link`,
      {className:'lf-tooltip',direction:'top',offset:[0,-8]}
    );
    m.on('click', () => openSitePanel(site, cid));
  });
  hexGroup.addTo(map);
  S.hexLayers[cid] = hexGroup;
}

/* ══════════════════════════════════════════════
   TOGGLE CLIENT
   ══════════════════════════════════════════════ */
async function toggleClient(cid){
  if(S.selectedIds.has(cid)){
    S.selectedIds.delete(cid);
    clearClientLayers(cid);
    renderChips();
    renderDropdown();
    updateFooterStats();
    updateLegend();
    setOverlay(S.selectedIds.size===0?welcomeHTML():'');
    return;
  }
  S.selectedIds.add(cid);
  renderChips();
  renderDropdown();
  setOverlay('');

  if(!S.allLayersData[cid]){
    // spinner ringan — tidak hide peta
    const loadMsg = document.createElement('div');
    loadMsg.id = 'loadMsg_'+cid;
    loadMsg.className = 'state-msg';
    loadMsg.style.cssText = 'position:absolute;bottom:120px;left:50%;transform:translateX(-50%);z-index:800;background:var(--glass);border:1px solid var(--border);border-radius:6px;padding:8px 18px;backdrop-filter:blur(8px);font-size:12px;pointer-events:none;gap:8px;display:flex';
    loadMsg.innerHTML = `<div class="spinner"></div><span>Memuat ${S.clients.find(c=>c.id===cid)?.name||''}…</span>`;
    document.querySelector('.app').appendChild(loadMsg);

    try{
      const d = await fetchSiteData(cid);
      S.allLayersData[cid] = d;
    } catch(err){
      S.selectedIds.delete(cid);
      renderChips();
      renderDropdown();
      document.getElementById('loadMsg_'+cid)?.remove();
      alert('Gagal memuat data: '+err.message);
      return;
    }
    document.getElementById('loadMsg_'+cid)?.remove();
    renderDropdown(); // update counts di dropdown
  }

  renderClientLayer(cid);
  updateFooterStats();
  updateLegend();

  // Zoom ke bounds klien
  const pts = (S.allLayersData[cid].sites||[])
    .filter(s=>s.lat&&s.lng)
    .map(s=>[parseFloat(s.lat),parseFloat(s.lng)]);
  if(pts.length>0 && S.selectedIds.size===1){
    const b = L.latLngBounds(pts);
    map.fitBounds(b,{padding:[60,60],maxZoom:12});
  }
}

/* ══════════════════════════════════════════════
   LINK TOGGLE
   ══════════════════════════════════════════════ */
document.getElementById('linkToggle').addEventListener('change', e => {
  S.showLinks = e.target.checked;
  S.selectedIds.forEach(cid => {
    if(S.edgeLayers[cid]) map.removeLayer(S.edgeLayers[cid]);
    delete S.edgeLayers[cid];
    renderClientLayer(cid);
  });
});

/* ══════════════════════════════════════════════
   LEGEND
   ══════════════════════════════════════════════ */
function updateLegend(){
  const inner = document.getElementById('legendInner');
  const rows = S.clients.filter(c=>S.selectedIds.has(c.id)).map((c,i)=>{
    const col = clientColor(S.clients.indexOf(c));
    return `<div class="leg-row"><div class="leg-dot" style="background:${col}"></div>${c.name}</div>`;
  });
  inner.innerHTML = rows.join('');
}

function toggleLegend(){
  S.legendOpen = !S.legendOpen;
  const btn = document.getElementById('legendBtn');
  const panel = document.getElementById('legendPanel');
  btn.classList.toggle('active', S.legendOpen);
  btn.querySelector('.legend-caret').textContent = S.legendOpen ? '▼' : '▲';
  panel.classList.toggle('open', S.legendOpen);
}

/* ══════════════════════════════════════════════
   WELCOME STATE (peta tetap tampil)
   ══════════════════════════════════════════════ */
function welcomeHTML(){
  return `<div style="background:var(--glass);border:1px solid var(--border);border-radius:8px;padding:14px 18px;backdrop-filter:blur(12px);font-size:12px;color:var(--muted);text-align:center;max-width:260px;line-height:1.7;box-shadow:var(--shadow-float)">
    Pilih klien melalui selector<br>untuk menampilkan data
  </div>`;
}

/* ══════════════════════════════════════════════
   DETAIL PANEL
   ══════════════════════════════════════════════ */
function openSitePanel(site, primaryCid){
  S.currentSiteData = site;
  document.getElementById('panelTitle').textContent = site.site_name || site.isr_id || 'SITE';
  document.getElementById('panelSub').textContent = site.alamat || site.address || '';

  const panelTabs  = document.getElementById('panelTabs');
  const panelBody  = document.getElementById('panelBody');
  const breadcrumb = document.getElementById('panelBreadcrumb');

  // Kumpulkan semua klien yang punya data di site ini (berdasar isr_id)
  const siteClients = [];
  S.clients.forEach((c,ci) => {
    if(!S.selectedIds.has(c.id)) return;
    const ld = S.allLayersData[c.id];
    if(!ld) return;
    const edges = (ld.edges||[]).filter(e =>
      e.from_isr_id == site.isr_id || e.to_isr_id == site.isr_id
    );
    if(edges.length > 0 || c.id == primaryCid){
      siteClients.push({client:c, idx:ci, edges});
    }
  });

  if(siteClients.length <= 1){
    // Single client — no tabs
    panelTabs.style.display = 'none';
    breadcrumb.style.display = 'none';
    S.currentTabId = siteClients[0]?.client?.id || primaryCid;
    renderPanelContent(site, primaryCid, panelBody);
  } else {
    // Multi client tabs
    panelTabs.style.display = 'flex';
    breadcrumb.style.display = 'none';
    S.currentTabId = primaryCid;
    renderTabs(siteClients, site, panelTabs, panelBody);
  }

  document.getElementById('detailPanel').classList.add('open');
  closeLegend();
}

function renderTabs(siteClients, site, tabsEl, bodyEl){
  tabsEl.innerHTML = siteClients.map(sc => {
    const col = clientColor(sc.idx);
    const active = sc.client.id === S.currentTabId ? ' active' : '';
    return `<div class="panel-tab${active}" style="--tab-color:${col}" onclick="switchTab(${sc.client.id}, this)">
      <div class="panel-tab-dot"></div>
      ${sc.client.name}
    </div>`;
  }).join('');
  renderPanelContent(site, S.currentTabId, bodyEl);
}

function switchTab(cid, tabEl){
  S.currentTabId = cid;
  document.querySelectorAll('.panel-tab').forEach(t=>t.classList.remove('active'));
  tabEl.classList.add('active');
  renderPanelContent(S.currentSiteData, cid, document.getElementById('panelBody'));
}

function renderPanelContent(site, cid, container){
  container.innerHTML = `<div class="state-msg" style="height:80px"><div class="spinner"></div><span>Memuat…</span></div>`;
  fetchLinkDetail(site.isr_id).then(data => {
    container.innerHTML = buildPanelHTML(data, site, cid);
    attachPanelEvents(container);
  }).catch(err => {
    container.innerHTML = `<div class="state-msg" style="height:80px;color:var(--red)">Gagal: ${err.message}</div>`;
  });
}

function closeSitePanel(){
  document.getElementById('detailPanel').classList.remove('open');
}

document.getElementById('panelClose').onclick = closeSitePanel;

/* ══════════════════════════════════════════════
   PANEL HTML BUILDER
   ══════════════════════════════════════════════ */
function licenseStatus(exp){
  if(!exp) return {cls:'',txt:'—'};
  const d = new Date(exp), now = new Date();
  const diff = (d-now)/(1000*86400);
  if(diff<0) return {cls:'chip-exp',txt:'Exp'};
  if(diff<90) return {cls:'chip-soon',txt:'Soon'};
  return {cls:'chip-ok',txt:'OK'};
}

function buildPanelHTML(data, site, cid){
  const ci = S.clients.findIndex(c=>c.id===cid);
  const color = clientColor(ci<0?0:ci);

  // Filter edges untuk klien ini
  const ld = S.allLayersData[cid];
  const clientEdges = ld ? (ld.edges||[]).filter(e =>
    e.from_isr_id == site.isr_id || e.to_isr_id == site.isr_id
  ) : [];

  // Group links by lawan site
  const lawanMap = {};
  (data.links||[]).forEach(link => {
    const lawan = link.remote_site_name || link.remote_isr_id || 'Unknown';
    if(!lawanMap[lawan]) lawanMap[lawan] = [];
    lawanMap[lawan].push(link);
  });

  let html = '';

  // Goto koordinat
  html += `<button class="goto-btn" onclick="map.setView([${site.lat||0},${site.lng||0}],14);closeSitePanel()">
    &#9655; Zoom ke Site
  </button>`;

  if(Object.keys(lawanMap).length === 0){
    html += `<div style="padding:20px 0;text-align:center;color:var(--muted);font-size:12px">Tidak ada data link</div>`;
    return html;
  }

  Object.keys(lawanMap).forEach(lawan => {
    const links = lawanMap[lawan];
    const uid = 'lw_'+btoa(encodeURIComponent(lawan)).replace(/[^a-z0-9]/gi,'').substr(0,8);

    html += `<div class="lawan-group">
      <div class="lawan-header" onclick="toggleLawan('${uid}')">
        <div class="lawan-dot" style="background:${color}"></div>
        <span class="lawan-name">${lawan}</span>
        <span class="lawan-count">${links.length} link</span>
        <span class="lawan-chevron" id="chev_${uid}">▾</span>
      </div>
      <div class="lawan-links" id="lawan_${uid}" style="display:none">`;

    links.forEach(link => {
      const lic = licenseStatus(link.license_expiry);
      const linkEdge = clientEdges.find(e =>
        (e.from_isr_id==site.isr_id && e.to_isr_id==link.remote_isr_id) ||
        (e.to_isr_id==site.isr_id && e.from_isr_id==link.remote_isr_id)
      );
      const mwId = 'mw_'+Math.random().toString(36).substr(2,7);
      html += `<div class="link-row">
        <div class="link-id-row" onclick="toggleMwDetail('${mwId}')">
          <span class="link-badge ${lic.cls}" style="${!lic.cls?'border-color:var(--border);color:var(--muted)':''}">${link.link_id||link.id||'LINK'}</span>
          <span style="font-size:10px;color:var(--muted);flex:1">${link.band||''} ${link.capacity||''}</span>
          ${lic.cls?`<span class="link-badge ${lic.cls}" style="font-size:9px">${lic.txt}</span>`:''}
          <span class="mw-chevron" id="${mwId}_chev">▾</span>
        </div>

        <!-- ISR pair -->
        <div class="isr-pair-grid">
          <div class="isr-half">
            <div class="isr-half-label">Local</div>
            <div class="isr-half-stn">${link.local_station_name||site.site_name||site.isr_id||'—'}</div>
            <div class="isr-half-simf">${link.local_simf||link.local_isr||'—'}</div>
            <div class="isr-half-freq">
              <span class="pill-tx">TX</span> ${link.local_tx_freq||'—'}
              &nbsp;<span class="pill-rx">RX</span> ${link.local_rx_freq||'—'}
            </div>
          </div>
          <div class="isr-half">
            <div class="isr-half-label">Remote</div>
            <div class="isr-half-stn">${lawan}</div>
            <div class="isr-half-simf">${link.remote_simf||link.remote_isr||'—'}</div>
            <div class="isr-half-freq">
              <span class="pill-tx">TX</span> ${link.remote_tx_freq||'—'}
              &nbsp;<span class="pill-rx">RX</span> ${link.remote_rx_freq||'—'}
            </div>
          </div>
        </div>

        <!-- Info chips -->
        <div class="link-info-bar">
          <div class="info-chip">
            <span class="info-chip-label">Jarak</span>
            <span class="info-chip-val">${link.distance_km ? link.distance_km+'km' : '—'}</span>
          </div>
          <div class="info-chip">
            <span class="info-chip-label">Kapasitas</span>
            <span class="info-chip-val">${link.capacity||'—'}</span>
          </div>
          <div class="info-chip">
            <span class="info-chip-label">Band</span>
            <span class="info-chip-val">${link.band||'—'}</span>
          </div>
          <div class="info-chip">
            <span class="info-chip-label">Lisensi</span>
            <span class="info-chip-val${lic.cls?' '+lic.cls:''}">${link.license_expiry ? new Date(link.license_expiry).getFullYear() : '—'}</span>
          </div>
        </div>

        <!-- MW detail collapsible -->
        <div class="mwlink-detail" id="${mwId}">
          ${buildMwDetail(link)}
        </div>
      </div>`;
    });

    html += `</div></div>`;
  });

  return html;
}

function buildMwDetail(link){
  const fields = [
    ['Polarisasi', link.polarization],
    ['Modulasi', link.modulation],
    ['Output Power', link.output_power],
    ['Threshold BER', link.ber_threshold],
    ['Konfigurasi', link.configuration],
    ['Vendor', link.vendor],
    ['Model', link.model],
    ['No. NIE', link.nie_number],
    ['S/N Local', link.serial_local],
    ['S/N Remote', link.serial_remote],
  ].filter(([k,v]) => v);

  if(!fields.length) return '<div style="font-size:11px;color:var(--muted);padding:4px 0">Tidak ada detail tambahan</div>';

  let g = '<div class="mw-extra-grid">';
  fields.forEach(([k,v]) => {
    g += `<span class="k">${k}</span><span class="v">${v}</span>`;
  });
  g += '</div>';

  // Antena
  const hasAnt = link.ant_local_size || link.ant_remote_size;
  if(hasAnt){
    g += `<div class="ant-specs">
      <div class="ant-half">
        <div class="ant-half-label">Antena Local</div>
        ${link.ant_local_size?`<div class="ant-row"><span class="ant-key">Ukuran</span><span class="ant-val">${link.ant_local_size}</span></div>`:''}
        ${link.ant_local_gain?`<div class="ant-row"><span class="ant-key">Gain</span><span class="ant-val">${link.ant_local_gain} dBi</span></div>`:''}
        ${link.ant_local_type?`<div class="ant-row"><span class="ant-key">Tipe</span><span class="ant-val">${link.ant_local_type}</span></div>`:''}
      </div>
      <div class="ant-half">
        <div class="ant-half-label">Antena Remote</div>
        ${link.ant_remote_size?`<div class="ant-row"><span class="ant-key">Ukuran</span><span class="ant-val">${link.ant_remote_size}</span></div>`:''}
        ${link.ant_remote_gain?`<div class="ant-row"><span class="ant-key">Gain</span><span class="ant-val">${link.ant_remote_gain} dBi</span></div>`:''}
        ${link.ant_remote_type?`<div class="ant-row"><span class="ant-key">Tipe</span><span class="ant-val">${link.ant_remote_type}</span></div>`:''}
      </div>
    </div>`;
  }
  return g;
}

function attachPanelEvents(container){
  // touch swipe to close on mobile
}

/* ══════════════════════════════════════════════
   PANEL TOGGLE HELPERS (global scope)
   ══════════════════════════════════════════════ */
function toggleLawan(uid){
  const el = document.getElementById('lawan_'+uid);
  const chev = document.getElementById('chev_'+uid);
  if(!el) return;
  const open = el.style.display !== 'none';
  el.style.display = open ? 'none' : 'flex';
  el.style.flexDirection = 'column';
  if(chev) chev.classList.toggle('open', !open);
}

function toggleMwDetail(id){
  const el = document.getElementById(id);
  const chev = document.getElementById(id+'_chev');
  if(!el) return;
  el.classList.toggle('open');
  if(chev) chev.classList.toggle('open', el.classList.contains('open'));
}

/* ══════════════════════════════════════════════
   GPS / LOCATION
   ══════════════════════════════════════════════ */
document.getElementById('locBtn').addEventListener('click', () => {
  const popup = document.getElementById('locPopup');
  popup.classList.toggle('open');
});

function useGPSLoc(){
  if(!navigator.geolocation){alert('Browser tidak support GPS');return}
  navigator.geolocation.getCurrentPosition(pos => {
    document.getElementById('manualLat').value = pos.coords.latitude.toFixed(6);
    document.getElementById('manualLng').value = pos.coords.longitude.toFixed(6);
  }, err => alert('GPS error: '+err.message));
}

function useManualLoc(){
  const lat = parseFloat(document.getElementById('manualLat').value);
  const lng = parseFloat(document.getElementById('manualLng').value);
  if(isNaN(lat)||isNaN(lng)){alert('Koordinat tidak valid');return}
  S.userLat = lat; S.userLng = lng;
  placeUserMarker(lat,lng);
}

function goToLoc(){
  const lat = parseFloat(document.getElementById('manualLat').value);
  const lng = parseFloat(document.getElementById('manualLng').value);
  if(isNaN(lat)||isNaN(lng)){alert('Koordinat tidak valid');return}
  S.userLat = lat; S.userLng = lng;
  placeUserMarker(lat,lng);
  map.setView([lat,lng],14);
  document.getElementById('locPopup').classList.remove('open');
}

function placeUserMarker(lat,lng){
  if(S.userMarker) map.removeLayer(S.userMarker);
  const pulseIcon = L.divIcon({
    html:`<div style="width:14px;height:14px;border-radius:50%;background:var(--accent);
      box-shadow:0 0 0 4px rgba(0,229,255,.25),0 0 12px rgba(0,229,255,.5);
      animation:none;border:2px solid #fff"></div>`,
    className:'',iconSize:[14,14],iconAnchor:[7,7]
  });
  S.userMarker = L.marker([lat,lng],{icon:pulseIcon,zIndexOffset:1000}).addTo(map);
  S.userMarker.bindTooltip(`${lat.toFixed(5)}, ${lng.toFixed(5)}`,{permanent:false,className:'lf-tooltip'});
}

/* ── Compass ── */
document.getElementById('compassBtn').addEventListener('click', () => {
  map.setBearing ? map.setBearing(0) : null;
  document.getElementById('compassNeedle').style.transform = 'rotate(0deg)';
  document.getElementById('compassBtn').classList.remove('rotated');
});

/* ══════════════════════════════════════════════
   INIT
   ══════════════════════════════════════════════ */
function closeLegend(){
  if(S.legendOpen) toggleLegend();
}

// Close panel on map click
map.on('click', () => {
  document.getElementById('locPopup').classList.remove('open');
});

(async function init(){
  setOverlay(welcomeHTML());
  try {
    const clients = await fetchClients();
    S.clients = clients;
    renderDropdown();
    renderChips();
    mobRenderSheet();
    updateFooterStats();
    updateLegend();
    setTimeout(recalcLayout, 100);
  } catch(err){
    setOverlay(`<div style="background:var(--glass);border:1px solid var(--red);border-radius:8px;padding:14px 18px;backdrop-filter:blur(12px);color:var(--red);font-size:12px">Gagal memuat klien: ${err.message}</div>`);
  }
})();

/* ══════════════════════════════════════════════
   MOBILE JS
   ══════════════════════════════════════════════ */
const isMobile = () => window.innerWidth <= 768;

/* ── Drawer (settings) ── */
function mobOpenDrawer(){
  if(!isMobile()) return;
  document.getElementById('mobDrawer').classList.add('open');
  document.getElementById('mobScrim').classList.add('open');
  mobCloseSheet();
}
function mobCloseDrawer(){
  document.getElementById('mobDrawer').classList.remove('open');
  document.getElementById('mobScrim').classList.remove('open');
}

/* ── Sheet (client list) ── */
let mobSheetOpen = false;
function mobToggleSheet(){
  mobSheetOpen ? mobCloseSheet() : mobOpenSheet();
}
function mobOpenSheet(){
  mobSheetOpen = true;
  document.getElementById('mobSheet').classList.add('open');
  document.getElementById('mobSelCaret').classList.add('open');
  document.getElementById('mobSelTrigger').classList.add('open');
}
function mobCloseSheet(){
  mobSheetOpen = false;
  document.getElementById('mobSheet').classList.remove('open');
  document.getElementById('mobSelCaret').classList.remove('open');
  document.getElementById('mobSelTrigger').classList.remove('open');
}

/* ── Render sheet list ── */
function mobRenderSheet(){
  const list = document.getElementById('mobSheetList');
  if(!list) return;
  list.innerHTML = S.clients.map((c,i) => {
    const col = clientColor(i);
    const sel = S.selectedIds.has(c.id);
    const ld = S.allLayersData[c.id];
    const isrN = ld ? (ld.sites||[]).length : (c.isr_count||'—');
    const mwN  = ld ? (ld.edges||[]).length : (c.mw_count||'—');
    return `<div class="ms-item${sel?' selected':''}" onclick="mobToggleClient(${c.id})">
      <div class="ms-check"><span class="ms-check-mark">✓</span></div>
      <div class="ms-dot" style="background:${col}"></div>
      <span class="ms-name">${c.name}</span>
      <div class="ms-stats">
        <span class="ms-stat-chip">${isrN}ISR</span>
        <span class="ms-stat-chip">${mwN}MW</span>
      </div>
    </div>`;
  }).join('');
}

/* ── Toggle client (mobile wrapper) ── */
async function mobToggleClient(cid){
  await toggleClient(cid);   // pakai fungsi utama yang sudah ada
  mobRenderSheet();
  mobRenderChips();
  mobUpdateStats();
  mobUpdateLegend();
}

/* ── Chips di bottom bar ── */
function mobRenderChips(){
  const area = document.getElementById('mobSelChips');
  if(!area) return;
  const sel = S.clients.filter(c => S.selectedIds.has(c.id));
  if(sel.length === 0){
    area.innerHTML = `<span style="color:var(--muted);font-size:11px">Pilih klien…</span>`;
    return;
  }
  area.innerHTML = sel.map(c => {
    const col = clientColor(S.clients.indexOf(c));
    return `<span class="chip" style="color:${col};border-color:${col};font-size:10px;padding:2px 6px">${c.name}</span>`;
  }).join('');
}

/* ── Stats di bottom bar ── */
function mobUpdateStats(){
  let isr=0, mw=0;
  S.clients.forEach(c => {
    if(!S.selectedIds.has(c.id)) return;
    const ld = S.allLayersData[c.id];
    if(ld){ isr += (ld.sites||[]).length; mw += (ld.edges||[]).length; }
  });
  const mi = document.getElementById('mobIsr');
  const mm = document.getElementById('mobMw');
  if(mi) mi.textContent = isr||'—';
  if(mm) mm.textContent = mw||'—';
}

/* ── Legend di drawer ── */
function mobUpdateLegend(){
  updateLegend(); // update desktop legend juga
  const wrap = document.getElementById('mobDrawerLegend');
  const inner = document.getElementById('mobLegendItems');
  if(!wrap || !inner) return;
  const sel = S.clients.filter(c => S.selectedIds.has(c.id));
  wrap.style.display = sel.length ? '' : 'none';
  inner.innerHTML = sel.map(c => {
    const col = clientColor(S.clients.indexOf(c));
    return `<div class="mob-legend-row"><div class="mob-legend-dot" style="background:${col}"></div>${c.name}</div>`;
  }).join('');
}

/* ── Basemap (mobile) ── */
function mobSwitchBasemap(v){
  switchBasemap(v);
  document.getElementById('basemapSel').value = v;
  if(v==='dark'){ document.documentElement.setAttribute('data-theme','dark'); document.getElementById('mobThemeToggle').checked=false; document.getElementById('themeToggle').checked=false; }
  else if(v==='bright'){ document.documentElement.setAttribute('data-theme','bright'); document.getElementById('mobThemeToggle').checked=true; document.getElementById('themeToggle').checked=true; }
}

/* ── Tema UI (mobile) ── */
function mobSwitchTheme(bright){
  document.documentElement.setAttribute('data-theme', bright ? 'bright' : 'dark');
  document.getElementById('themeToggle').checked = bright;
  const bv = bright ? 'bright' : 'dark';
  if(document.getElementById('basemapSel').value !== 'satellite'){
    document.getElementById('basemapSel').value = bv;
    document.getElementById('mobBasemap').value = bv;
    switchBasemap(bv);
  }
}

/* ── Garis link (mobile) ── */
function mobSwitchLinks(v){
  document.getElementById('linkToggle').checked = v;
  S.showLinks = v;
  S.selectedIds.forEach(cid => {
    if(S.edgeLayers[cid]) map.removeLayer(S.edgeLayers[cid]);
    delete S.edgeLayers[cid];
    renderClientLayer(cid);
  });
}

/* ── GPS (mobile) ── */
let mobGpsActive = false;
function mobToggleGPS(){
  if(mobGpsActive){
    mobGpsActive = false;
    document.getElementById('mobLocBtn').style.color = '';
    if(S.locWatchId){ navigator.geolocation.clearWatch(S.locWatchId); S.locWatchId = null; }
    return;
  }
  if(!navigator.geolocation){ alert('Browser tidak support GPS'); return; }
  mobGpsActive = true;
  document.getElementById('mobLocBtn').style.color = 'var(--green)';
  navigator.geolocation.getCurrentPosition(pos => {
    const lat = pos.coords.latitude, lng = pos.coords.longitude;
    placeUserMarker(lat, lng);
    map.setView([lat, lng], 14);
    document.getElementById('mobLat').value = lat.toFixed(6);
    document.getElementById('mobLng').value = lng.toFixed(6);
  }, err => {
    mobGpsActive = false;
    document.getElementById('mobLocBtn').style.color = '';
    alert('GPS error: ' + err.message);
  });
}

function mobGPS(){
  if(!navigator.geolocation){ alert('Browser tidak support GPS'); return; }
  navigator.geolocation.getCurrentPosition(pos => {
    document.getElementById('mobLat').value = pos.coords.latitude.toFixed(6);
    document.getElementById('mobLng').value = pos.coords.longitude.toFixed(6);
  }, err => alert('GPS error: ' + err.message));
}

function mobGoLoc(){
  const lat = parseFloat(document.getElementById('mobLat').value);
  const lng = parseFloat(document.getElementById('mobLng').value);
  if(isNaN(lat)||isNaN(lng)){ alert('Koordinat tidak valid'); return; }
  placeUserMarker(lat, lng);
  map.setView([lat, lng], 14);
  mobCloseDrawer();
}

/* ── Close sheet on map tap ── */
map.on('click', () => {
  mobCloseSheet();
  if(isMobile()) document.getElementById('locPopup').classList.remove('open');
});

/* ── Sync: openSitePanel mobile tutup sheet ── */
const _origOpenSitePanel = openSitePanel;
window.openSitePanel = function(site, cid){
  if(isMobile()) mobCloseSheet();
  _origOpenSitePanel(site, cid);
};

/* ── Swipe down to close detail panel (mobile) ── */
(function(){
  const panel = document.getElementById('detailPanel');
  let startY=0, isDragging=false;
  panel.addEventListener('touchstart', e => {
    if(e.target.closest('.panel-body')) return; // allow scroll inside body
    startY = e.touches[0].clientY;
    isDragging = true;
  }, {passive:true});
  panel.addEventListener('touchmove', e => {
    if(!isDragging) return;
    const dy = e.touches[0].clientY - startY;
    if(dy > 0) panel.style.transform = `translateY(${dy}px)`;
  }, {passive:true});
  panel.addEventListener('touchend', e => {
    if(!isDragging) return;
    isDragging = false;
    const dy = e.changedTouches[0].clientY - startY;
    panel.style.transform = '';
    if(dy > 80) closeSitePanel();
  });
})();
</script>
</body>
</html>