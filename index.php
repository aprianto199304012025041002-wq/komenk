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

html,body{height:100%;background:var(--bg);color:var(--text);font-family:var(--sans);overflow:hidden;-webkit-tap-highlight-color:transparent;transition:background .25s,color .25s;}
.app{position:relative;width:100%;height:100vh;height:100dvh;overflow:hidden}
#map{position:absolute;inset:0;width:100%;height:100%;z-index:1}

.float-logo{position:absolute;top:12px;left:12px;z-index:600;display:flex;align-items:center;gap:6px;background:var(--glass);backdrop-filter:blur(10px);border:1px solid var(--border);border-radius:var(--radius);padding:6px 10px;box-shadow:var(--shadow-float);}
.logo-mark{font-family:var(--mono);font-size:13px;color:var(--accent);letter-spacing:3px;white-space:nowrap}
.logo-sep{width:1px;height:18px;background:var(--border);flex-shrink:0}
.logo-sub{font-size:9px;color:var(--muted);letter-spacing:1px;white-space:nowrap}

.float-controls{position:absolute;top:12px;z-index:600;display:flex;flex-direction:row;align-items:center;gap:6px;left:12px;flex-wrap:nowrap;}
.ctrl-pill{display:flex;align-items:center;gap:5px;background:var(--glass);backdrop-filter:blur(10px);border:1px solid var(--border);border-radius:var(--radius);padding:5px 9px;box-shadow:var(--shadow-float);font-size:11px;color:var(--muted);white-space:nowrap;}
.ctrl-pill select{background:transparent;border:none;color:var(--text);font-size:11px;cursor:pointer;outline:none;font-family:var(--sans);}
.ctrl-pill select option{background:var(--surface);color:var(--text)}

.tog{position:relative;width:34px;height:20px;flex-shrink:0}
.tog input{opacity:0;width:0;height:0}
.tog-s{position:absolute;inset:0;background:var(--border);border-radius:10px;transition:.2s;cursor:pointer}
.tog-s:before{content:'';position:absolute;width:14px;height:14px;left:3px;top:3px;background:var(--muted);border-radius:50%;transition:.2s}
input:checked+.tog-s{background:rgba(0,229,255,.25);border:1px solid var(--accent)}
input:checked+.tog-s:before{transform:translateX(14px);background:var(--accent)}
[data-theme="bright"] input:checked+.tog-s{background:rgba(2,132,199,.2);border-color:var(--accent)}
[data-theme="bright"] input:checked+.tog-s:before{background:var(--accent)}

.float-selector{display:none}

/* ── Filter wilayah ── */
.filter-wrap{position:relative}
.filter-trigger{
  background:var(--glass);backdrop-filter:blur(10px);
  border:1px solid var(--border);border-radius:var(--radius);
  padding:4px 8px;display:flex;align-items:center;gap:5px;
  cursor:pointer;font-size:11px;color:var(--text);transition:border .15s;
  box-shadow:var(--shadow-float);min-width:110px;max-width:160px;height:32px;
}
.filter-trigger:hover,.filter-trigger.open{border-color:var(--accent)}
.filter-trigger.active{border-color:var(--accent2);color:var(--accent2)}
.filter-label{flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:120px}
.filter-caret{font-size:11px;color:var(--muted);transition:transform .2s;flex-shrink:0}
.filter-caret.open{transform:rotate(180deg)}
.filter-clear{font-size:12px;color:var(--muted);cursor:pointer;transition:color .12s;flex-shrink:0;display:none;line-height:1;padding:0 1px}
.filter-trigger.active .filter-clear{display:block}
.filter-clear:hover{color:var(--red)}
.filter-dropdown{
  position:fixed;min-width:250px;
  background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);
  z-index:1200;display:none;flex-direction:column;
  box-shadow:var(--shadow-float);max-height:min(320px, 60vh);overflow:hidden;
}
.filter-dropdown.open{display:flex}
.filter-search-wrap{padding:8px;border-bottom:1px solid var(--border);flex-shrink:0}
.filter-search-input{
  width:100%;background:var(--bg);border:1px solid var(--border);border-radius:4px;
  color:var(--text);font-size:12px;padding:6px 10px;outline:none;
  font-family:var(--sans);transition:border-color .15s;
}
.filter-search-input:focus{border-color:var(--accent)}
.filter-search-input::placeholder{color:var(--muted)}
.filter-list{overflow-y:auto;overscroll-behavior:contain;flex:1}
.filter-item{
  display:flex;align-items:center;gap:8px;padding:8px 12px;
  cursor:pointer;font-size:12px;border-bottom:1px solid var(--border);transition:background .12s;
}
.filter-item:last-child{border-bottom:none}
.filter-item:hover{background:var(--surface2)}
.filter-item.selected{background:rgba(0,229,255,.06)}
[data-theme="bright"] .filter-item.selected{background:rgba(2,132,199,.06)}
.filter-check{
  width:14px;height:14px;border:1px solid var(--border);border-radius:2px;
  flex-shrink:0;display:flex;align-items:center;justify-content:center;
}
.filter-item.selected .filter-check{background:var(--accent);border-color:var(--accent)}
.filter-check-mark{color:#000;font-size:10px;font-weight:700;display:none}
[data-theme="dark"] .filter-check-mark{color:#000}
.filter-item.selected .filter-check-mark{display:block}
.filter-item-name{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.filter-item-count{font-family:var(--mono);font-size:10px;color:var(--muted);flex-shrink:0}
.filter-no-result{padding:16px 12px;text-align:center;color:var(--muted);font-size:12px}

.ms-trigger{background:var(--glass);backdrop-filter:blur(10px);border:1px solid var(--border);border-radius:var(--radius);padding:5px 9px;display:flex;align-items:center;gap:5px;cursor:pointer;font-size:11px;color:var(--text);transition:border .15s;box-shadow:var(--shadow-float);min-width:120px;max-width:220px;}
.ms-trigger:hover,.ms-trigger.open{border-color:var(--accent)}
.chips-area{flex:1;display:flex;gap:4px;flex-wrap:nowrap;overflow:hidden;min-width:0}
.chip{font-size:10px;font-family:var(--mono);padding:2px 7px;border-radius:2px;border:1px solid;white-space:nowrap;flex-shrink:0;}
.chip-caret{color:var(--muted);font-size:12px;transition:transform .2s;flex-shrink:0;margin-left:auto}
.chip-caret.open{transform:rotate(180deg)}
.ms-dropdown{margin-top:4px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);z-index:700;max-height:300px;display:none;box-shadow:var(--shadow-float);overflow:hidden;display:none;flex-direction:column;}
.ms-dropdown.open{display:flex}
.ms-search-wrap{padding:8px;border-bottom:1px solid var(--border);flex-shrink:0}
.ms-search-input{width:100%;background:var(--bg);border:1px solid var(--border);border-radius:4px;color:var(--text);font-size:12px;padding:6px 10px;outline:none;font-family:var(--sans);transition:border-color .15s}
.ms-search-input:focus{border-color:var(--accent)}
.ms-search-input::placeholder{color:var(--muted)}
.ms-list-scroll{overflow-y:auto;overscroll-behavior:contain;max-height:250px}
.ms-no-result{padding:16px 12px;text-align:center;color:var(--muted);font-size:12px}
.ms-item{display:flex;align-items:center;gap:8px;padding:9px 12px;cursor:pointer;font-size:12px;border-bottom:1px solid var(--border);transition:background .12s;}
.ms-item:last-child{border-bottom:none}
.ms-item:hover{background:var(--surface2)}
.ms-item.selected{background:rgba(0,229,255,.06)}
[data-theme="bright"] .ms-item.selected{background:rgba(2,132,199,.06)}
.ms-check{width:14px;height:14px;border:1px solid var(--border);border-radius:2px;flex-shrink:0;display:flex;align-items:center;justify-content:center;}
.ms-item.selected .ms-check{background:var(--accent);border-color:var(--accent)}
.ms-check-mark{color:#fff;font-size:10px;font-weight:700;display:none}
[data-theme="dark"] .ms-check-mark{color:#000}
.ms-item.selected .ms-check-mark{display:block}
.ms-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.ms-name{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ms-count{font-family:var(--mono);font-size:10px;color:var(--muted);flex-shrink:0}
.ms-stats{display:flex;gap:4px;flex-shrink:0}
.ms-stat-chip{font-family:var(--mono);font-size:9px;color:var(--muted);background:var(--surface2);border:1px solid var(--border);border-radius:2px;padding:1px 5px;white-space:nowrap;}

.leaflet-container{background:var(--leaflet-bg)!important;transition:background .25s}
.leaflet-control-zoom a{background:var(--surface)!important;color:var(--text)!important;border-color:var(--border)!important}
.leaflet-control-attribution{background:var(--glass)!important;color:var(--muted)!important;font-size:10px!important;backdrop-filter:blur(6px)!important}
.lf-tooltip{background:var(--surface)!important;border:1px solid var(--border)!important;border-radius:var(--radius)!important;color:var(--text)!important;font-family:var(--sans)!important;font-size:12px!important;padding:6px 10px!important;box-shadow:0 4px 12px rgba(0,0,0,.4)!important}
.lf-tooltip b{color:var(--accent)}
.leaflet-marker-icon svg{filter:var(--shadow-hex)}
.link-count-label{background:rgba(12,15,20,.82);border:1px solid rgba(255,255,255,.3);border-radius:3px;font-family:var(--mono);font-size:10px;font-weight:700;color:#fff;padding:1px 5px;white-space:nowrap;pointer-events:none;transition:opacity .15s;line-height:1.4;text-shadow:0 1px 3px rgba(0,0,0,.8);box-shadow:0 1px 4px rgba(0,0,0,.6);}
[data-theme="bright"] .link-count-label{background:rgba(255,255,255,.88);border-color:rgba(0,0,0,.2);color:#1e293b;text-shadow:0 1px 2px rgba(255,255,255,.8);box-shadow:0 1px 4px rgba(0,0,0,.2);}
.hex-skeleton{animation:hex-pulse 1.4s ease-in-out infinite}
@keyframes hex-pulse{0%,100%{opacity:.18}50%{opacity:.38}}
@keyframes spin{to{transform:rotate(360deg)}}
.spinner{width:24px;height:24px;border:2px solid var(--border);border-top-color:var(--accent);border-radius:50%;animation:spin .6s linear infinite}
.state-msg{display:flex;align-items:center;justify-content:center;height:100%;color:var(--muted);font-size:13px;flex-direction:column;gap:8px}

.leaflet-top.leaflet-right{top:0;right:0}
.leaflet-top.leaflet-right .leaflet-control-zoom{margin-top:calc(var(--topbar-h,12px) + 8px)!important;margin-right:10px!important;}
.leaflet-control-zoom{border:none!important;box-shadow:var(--shadow-float)!important}
.leaflet-control-zoom a{width:36px!important;height:36px!important;line-height:36px!important;font-size:18px!important;border-radius:4px!important;background:var(--glass)!important;backdrop-filter:blur(10px)!important;color:var(--text)!important;border:1px solid var(--border)!important;cursor:pointer;transition:background .12s,border-color .15s;}
.leaflet-control-zoom a:hover{background:var(--surface2)!important;border-color:var(--accent)!important}
.leaflet-control-zoom-in{border-radius:4px 4px 0 0!important;border-bottom:none!important}
.leaflet-control-zoom-out{border-radius:0 0 4px 4px!important}
.leaflet-bottom.leaflet-left{bottom:0;left:0}
.leaflet-bottom{padding-bottom:env(safe-area-inset-bottom,0)}

.map-controls{position:absolute;right:10px;z-index:599;display:flex;flex-direction:column;gap:8px;top:calc(var(--topbar-h,12px) + 94px);}
.map-ctrl-btn{width:36px;height:36px;border-radius:4px;background:var(--glass);backdrop-filter:blur(10px);border:1px solid var(--border);color:var(--accent);font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s,border-color .15s;box-shadow:var(--shadow-float);}
.map-ctrl-btn:hover{background:var(--surface2);border-color:var(--accent)}
.compass-btn{color:var(--text)}
.compass-btn:hover{border-color:var(--accent);color:var(--accent)}
.compass-btn.rotated{border-color:var(--accent);color:var(--accent)}
#compassNeedle{display:block;transition:transform .3s ease}

.loc-popup{position:absolute;right:56px;z-index:600;background:var(--surface);backdrop-filter:blur(10px);border:1px solid var(--border);border-radius:var(--radius);padding:10px 12px;display:none;flex-direction:column;gap:8px;min-width:220px;top:calc(var(--topbar-h,12px) + 94px);max-height:50vh;overflow-y:auto;box-shadow:var(--shadow-float);overscroll-behavior:contain}
.loc-popup.open{display:flex}
.loc-popup label{font-size:11px;color:var(--muted);display:flex;flex-direction:column;gap:3px}
.loc-popup input{background:var(--bg);border:1px solid var(--border);border-radius:4px;color:var(--text);font-family:var(--mono);font-size:12px;padding:5px 8px;outline:none;transition:border .15s;}
.loc-popup input:focus{border-color:var(--accent)}
.loc-popup-btns{display:flex;gap:6px}
.loc-popup-btns button{flex:1;padding:6px;border-radius:4px;font-size:11px;cursor:pointer;border:1px solid var(--border);background:var(--surface);color:var(--text);transition:background .12s,border-color .15s;}
.loc-popup-btns button:hover{background:var(--surface2);border-color:var(--accent)}
.loc-popup-btns button.primary{background:var(--accent);color:#000;border-color:var(--accent);font-weight:600}
.loc-popup-btns button.primary:hover{background:#00c4dd}
[data-theme="bright"] .loc-popup-btns button.primary:hover{background:#0369a1}

.float-stats{position:absolute;bottom:calc(12px + env(safe-area-inset-bottom,0));left:50%;transform:translateX(-50%);z-index:600;display:flex;align-items:stretch;gap:0;background:var(--glass);backdrop-filter:blur(10px);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-float);min-width:220px;}
.stat-pill{display:flex;flex-direction:column;align-items:center;flex:1;padding:6px 12px;}
.stat-pill+.stat-pill{border-left:1px solid var(--border)}
.sp-val{font-family:var(--mono);font-size:15px;color:var(--accent);font-weight:700}
.sp-lbl{font-size:9px;color:var(--muted);letter-spacing:.5px;text-transform:uppercase;margin-top:2px}

.float-legend-btn{position:absolute;z-index:600;bottom:calc(60px + env(safe-area-inset-bottom,0));left:50%;transform:translateX(-50%);background:var(--glass);backdrop-filter:blur(10px);border:1px solid var(--border);border-radius:var(--radius);padding:5px 12px;font-size:11px;color:var(--muted);cursor:pointer;display:flex;align-items:center;gap:5px;box-shadow:var(--shadow-float);white-space:nowrap;transition:color .15s,border-color .15s;}
.float-legend-btn:hover{color:var(--text);border-color:var(--accent)}
.float-legend-btn.active{color:var(--accent)}
.legend-caret{font-size:10px;transition:transform .2s}
.float-legend-btn.active .legend-caret{transform:rotate(180deg)}
.legend-panel{position:absolute;z-index:605;bottom:calc(90px + env(safe-area-inset-bottom,0));left:50%;transform:translateX(-50%);background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);max-height:0;overflow:hidden;transition:max-height .28s ease;box-shadow:var(--shadow-float);min-width:140px;}
.legend-panel.open{max-height:200px;overflow-y:auto;overscroll-behavior:contain}
.legend-inner{padding:8px 12px;display:flex;flex-wrap:wrap;gap:6px 14px}
.leg-row{
  display:grid;
  grid-template-columns:8px 1fr 60px 36px;
  align-items:center;
  gap:8px;
  font-size:11px;
  color:var(--text);
  padding:5px 0;
}
.leg-row+.leg-row{border-top:1px solid rgba(255,255,255,.03)}
.leg-row .leg-name{
  overflow:hidden;
  text-overflow:ellipsis;
  white-space:nowrap;
  font-size:11px;
  letter-spacing:.1px;
}
.leg-statbar{
  display:flex;height:5px;width:100%;border-radius:3px;
  overflow:hidden;background:var(--border);flex-shrink:0;
}
.leg-statbar-seg{height:100%}
.leg-statbar-seg.active{background:var(--green)}
.leg-statbar-seg.soon{background:var(--yellow)}
.leg-statbar-seg.expired{background:var(--red)}
.leg-stat-count{
  font-family:var(--mono);font-size:9.5px;color:var(--muted);
  white-space:nowrap;text-align:right;
}
.leg-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}

.detail-panel{position:absolute;bottom:0;left:0;right:0;background:var(--surface);border-top:2px solid var(--border);box-shadow:0 -4px 24px rgba(0,0,0,.35);max-height:60vh;height:auto;transform:translateY(100%);transition:transform .28s cubic-bezier(.4,0,.2,1);z-index:1000;display:flex;flex-direction:column}
.detail-panel.open{transform:translateY(0)}
.panel-handle{padding:10px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;flex-shrink:0;background:var(--surface2)}
.panel-handle h3{font-family:var(--mono);font-size:13px;color:var(--accent);letter-spacing:1.5px;font-weight:700}
.panel-sub{font-size:11px;color:var(--muted);flex:1;margin-left:4px}
.panel-breadcrumb{font-size:10px;color:var(--muted);font-family:var(--mono);display:flex;align-items:center;gap:6px;padding:6px 16px;border-bottom:1px solid var(--border);background:var(--bg);flex-shrink:0}
.panel-breadcrumb span.active{color:var(--accent)}
.panel-close{background:none;border:none;color:var(--muted);cursor:pointer;font-size:16px;line-height:1;padding:2px 6px;margin-left:auto;transition:color .15s,background .12s;border-radius:4px;}
.panel-close:hover{color:var(--text);background:var(--surface2)}
.panel-body{flex:0 1 auto;overflow-y:auto;padding:14px 18px;max-height:calc(60vh - 80px);overscroll-behavior:contain}
@keyframes fadeInUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
.panel-body.anim{animation:fadeInUp .2s ease forwards}
.panel-body::-webkit-scrollbar{width:4px}
.panel-body::-webkit-scrollbar-thumb{background:var(--border)}

.panel-tabs{display:flex;gap:0;border-bottom:1px solid var(--border);overflow-x:auto;flex-shrink:0;scrollbar-width:none}
.panel-tabs::-webkit-scrollbar{display:none}
.panel-tab{padding:8px 14px;font-size:11px;font-family:var(--mono);letter-spacing:.5px;cursor:pointer;white-space:nowrap;border-bottom:2px solid transparent;color:var(--muted);transition:color .15s,border-color .15s,background .12s;flex-shrink:0;display:flex;align-items:center;gap:6px;}
.panel-tab:hover{color:var(--text);background:var(--surface2)}
.panel-tab.active{color:var(--text);border-bottom-color:var(--tab-color,var(--accent))}
.panel-tab-dot{width:7px;height:7px;border-radius:50%;background:var(--tab-color,var(--accent));flex-shrink:0}
.panel-tab-pane{display:none}
.panel-tab-pane.active{display:block}

.lawan-group{background:var(--bg);border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:8px;box-shadow:0 1px 2px rgba(0,0,0,.18)}
.lawan-header{display:flex;align-items:center;gap:8px;padding:12px 14px;cursor:pointer;transition:background .12s;user-select:none}
.lawan-header:hover{background:var(--surface2)}
.lawan-header-static{display:flex;align-items:center;gap:8px;padding:10px 12px}
.lawan-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;box-shadow:0 0 0 3px rgba(255,255,255,.04)}
.lawan-dot.ghost{opacity:.55;outline:1px dashed currentColor;background:transparent!important;box-shadow:none}
.lawan-name{font-size:13px;font-weight:600;flex:1;min-width:0;color:var(--text);letter-spacing:.2px;word-break:break-word}
.lawan-count{font-size:10px;color:var(--muted);background:var(--surface2);padding:2px 8px;border-radius:20px;flex-shrink:0;font-family:var(--mono)}
.lawan-chevron{color:var(--muted);font-size:13px;transition:transform .2s;flex-shrink:0}
.lawan-chevron.open{transform:rotate(180deg)}
.lawan-links{display:flex;flex-direction:column;gap:0;border-top:1px solid var(--border)}
.link-row{padding:12px 14px;border-bottom:1px solid var(--border);background:var(--surface)}
.link-row:last-child{border-bottom:none}
.link-id-row{display:flex;align-items:center;gap:6px;margin-bottom:8px;cursor:pointer;transition:opacity .12s;border-radius:4px;padding:2px 0}
.link-id-row:hover{opacity:.8}
.link-badge{font-family:var(--mono);font-size:11px;font-weight:700;padding:4px 11px;border-radius:4px;border:1px solid;letter-spacing:.5px;white-space:nowrap}
.isr-pair-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px}
.isr-half{background:var(--surface2);border-radius:6px;padding:10px 12px;border:1px solid var(--border);border-top:2px solid var(--accent3)}
.isr-half:last-child{border-top-color:var(--accent2)}
.isr-half-ghost{opacity:.5}
.isr-half-label{font-size:9px;color:var(--accent3);text-transform:uppercase;letter-spacing:.8px;margin-bottom:5px;font-weight:700}
.isr-half:last-child .isr-half-label{color:var(--accent2)}
.isr-half-stn{font-size:13px;font-weight:600;color:var(--text);margin-bottom:5px;line-height:1.4;word-break:break-word}
.isr-half-simf{font-size:11px;font-family:var(--mono);color:var(--muted);margin-bottom:3px;letter-spacing:.3px;word-break:break-all}
.isr-half-freq{font-size:11px;color:var(--muted);margin-top:3px}
.pill-tx{color:var(--accent);font-size:9px;font-family:var(--mono);font-weight:700}
.pill-rx{color:var(--accent2);font-size:9px;font-family:var(--mono);font-weight:700}
.link-info-bar{display:grid;grid-template-columns:repeat(4,1fr);gap:6px;margin:8px 0}
.info-chip{background:var(--surface2);border:1px solid var(--border);border-radius:6px;padding:7px 10px;display:flex;flex-direction:column;gap:3px;min-width:0}
.info-chip-label{font-size:9px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px}
.info-chip-val{font-size:12px;color:var(--text);font-family:var(--mono);font-weight:600;word-break:break-word}
.info-chip-val.good{color:#fff;background:var(--green);padding:2px 6px;border-radius:4px;display:inline-block}.info-chip-val.warn{color:#000;background:var(--yellow);padding:2px 6px;border-radius:4px;display:inline-block}.info-chip-val.danger{color:#fff;background:var(--red);padding:2px 6px;border-radius:4px;display:inline-block}
.ant-specs{display:grid;grid-template-columns:1fr 1fr;gap:5px;margin-bottom:8px}
.ant-half{background:var(--surface2);border:1px solid var(--border);border-radius:6px;padding:9px 12px}
.ant-half-label{font-size:9px;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px;font-weight:700}
.ant-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:5px}
.ant-row:last-child{margin-bottom:0}
.ant-key{font-size:11px;color:var(--muted)}.ant-val{font-size:12px;color:var(--text);font-family:var(--mono);font-weight:500;word-break:break-word}
.mw-extra-grid{display:grid;grid-template-columns:auto 1fr;gap:5px 14px;font-size:12px;margin-bottom:8px;padding:9px 12px;background:var(--surface2);border:1px solid var(--border);border-radius:6px}
.mw-extra-grid .k{color:var(--muted)}.mw-extra-grid .v{color:var(--text);font-family:var(--mono);font-weight:500}
.mwlink-detail{display:none;padding:8px 0 2px}
.mwlink-detail.open{display:block}
.mw-chevron{color:var(--muted);font-size:13px;transition:transform .2s;flex-shrink:0}
.copy-btn{background:none;border:none;color:var(--muted);cursor:pointer;padding:1px 4px;border-radius:3px;font-size:10px;transition:color .12s,background .12s;vertical-align:middle;margin-left:3px}
.copy-btn:hover{color:var(--accent);background:var(--surface2)}
.copy-btn.copied{color:var(--green)}
.mw-chevron.open{transform:rotate(180deg)}
.goto-btn{display:flex;align-items:center;justify-content:center;gap:6px;margin-top:10px;width:100%;padding:8px 12px;border:1px solid var(--accent);border-radius:6px;background:rgba(0,229,255,.05);color:var(--accent);font-size:12px;font-weight:600;cursor:pointer;transition:background .12s,border-color .15s;}
.goto-btn:hover{background:rgba(0,229,255,.12)}
.chip-ok{color:#fff;border-color:var(--green);background:var(--green)}
.chip-exp{color:#fff;border-color:var(--red);background:var(--red)}
.chip-soon{color:#000;border-color:var(--yellow);background:var(--yellow)}

@media(min-width:769px){
  .float-logo{top:12px;left:12px}
  .float-stats{display:none!important}
  .float-legend-btn{display:none!important}
  .legend-panel{display:none!important}

  /* Legend desktop: panel kecil kiri bawah, selalu terlihat kalau ada klien aktif */
  .desktop-legend{
    display:none;position:absolute;left:12px;
    bottom:calc(12px + env(safe-area-inset-bottom,0));
    z-index:600;background:var(--glass);backdrop-filter:blur(10px);
    border:1px solid var(--border);border-radius:var(--radius);
    box-shadow:var(--shadow-float);width:clamp(220px,22vw,300px);
    max-height:min(380px, calc(100dvh - 140px));
    display:none;flex-direction:column;
    overflow:hidden;
  }
  .desktop-legend.show{display:flex}
  .desktop-legend-title{
    font-family:var(--mono);font-size:10px;color:var(--accent);
    letter-spacing:1.2px;text-transform:uppercase;
    padding:10px 14px;border-bottom:1px solid var(--border);
    flex-shrink:0;display:flex;align-items:center;gap:6px;
    cursor:pointer;user-select:none;transition:background .12s;
  }
  .desktop-legend-title:hover{background:rgba(255,255,255,.04)}
  [data-theme="bright"] .desktop-legend-title:hover{background:rgba(0,0,0,.04)}
  .desktop-legend-title-text{flex:1}
  .desktop-legend-count{
    font-family:var(--mono);font-size:9px;color:var(--muted);
    background:var(--surface2);padding:1px 6px;border-radius:10px;flex-shrink:0;
  }
  .desktop-legend-caret{
    font-size:9px;color:var(--muted);transition:transform .22s ease;flex-shrink:0;
  }
  .desktop-legend.collapsed .desktop-legend-caret{transform:rotate(-90deg)}
  .desktop-legend.collapsed .desktop-legend-title{border-bottom-color:transparent}
  .desktop-legend-body{
    overflow-y:auto;overscroll-behavior:contain;
    max-height:400px;
    transition:max-height .25s cubic-bezier(.4,0,.2,1),padding .25s;
    padding:6px 14px;
  }
  .desktop-legend.collapsed .desktop-legend-body{
    max-height:0;padding-top:0;padding-bottom:0;overflow:hidden;
  }
  .desktop-legend-body::-webkit-scrollbar{width:4px}
  .desktop-legend-body::-webkit-scrollbar-thumb{background:var(--border);border-radius:2px}
  .desktop-legend .leg-row{grid-template-columns:8px 1fr 64px 38px}
  .detail-panel{position:absolute;top:0;right:0;bottom:0;left:auto;width:clamp(340px,35vw,510px);max-height:100dvh;transform:translateX(100%);transition:transform .28s cubic-bezier(.4,0,.2,1);border-top:none;border-left:2px solid var(--border);border-radius:0;z-index:1000;}
  .detail-panel.open{transform:translateX(0)}
  .panel-body{max-height:calc(100dvh - 140px);padding:12px 14px}
  .leaflet-top.leaflet-right .leaflet-control-zoom{margin-top:12px!important;margin-right:10px!important;}
  .map-controls{top:calc(12px + 74px + 12px);right:10px;}
  .loc-popup{right:56px;top:calc(12px + 74px + 12px);}
}

@media(max-width:768px){
  .float-logo{display:none}
  .float-controls{display:none}
  .float-selector{display:none}
  .float-stats{display:none}
  .float-legend-btn{display:none}
  .legend-panel{display:none}
  .map-controls{display:none}
  .loc-popup{display:none!important}
  .leaflet-top.leaflet-right .leaflet-control-zoom{display:none!important}

  /* FAB kiri atas: hamburger */
  .mob-fab-logo{position:absolute;top:12px;left:12px;z-index:600;width:40px;height:40px;border-radius:10px;background:var(--glass);backdrop-filter:blur(10px);border:1px solid var(--border);box-shadow:var(--shadow-float);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--accent);transition:background .12s,border-color .15s;-webkit-tap-highlight-color:transparent;touch-action:manipulation;}
  .mob-fab-logo:hover,.mob-fab-logo:active{background:var(--surface2);border-color:var(--accent)}

  /* FAB kanan atas */
  .mob-fab-zoom{position:absolute;top:12px;right:12px;z-index:600;display:flex;flex-direction:column;gap:4px;}
  .mob-fab-btn{width:40px;height:40px;border-radius:10px;background:var(--glass);backdrop-filter:blur(10px);border:1px solid var(--border);box-shadow:var(--shadow-float);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;color:var(--text);transition:background .12s,border-color .15s;-webkit-tap-highlight-color:transparent;touch-action:manipulation;}
  .mob-fab-btn:hover,.mob-fab-btn:active{background:var(--surface2);border-color:var(--accent)}
  .mob-fab-btn.accent{color:var(--accent);font-size:16px}

  /* Kompas mobile */
  .mob-fab-compass{width:40px;height:40px;border-radius:10px;background:var(--glass);backdrop-filter:blur(10px);border:1px solid var(--border);box-shadow:var(--shadow-float);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .12s,border-color .15s;-webkit-tap-highlight-color:transparent;touch-action:manipulation;}
  .mob-fab-compass:hover,.mob-fab-compass:active{background:var(--surface2);border-color:var(--accent)}
  .mob-fab-compass.rotated{border-color:var(--accent)}
  #mobCompassNeedle{display:block;transition:transform .3s ease}

  /* Bottom bar */
  .mob-bar{position:absolute;bottom:0;left:0;right:0;z-index:600;background:var(--glass);backdrop-filter:blur(14px);border-top:1px solid var(--border);padding:8px 12px calc(8px + env(safe-area-inset-bottom));display:flex;align-items:center;gap:8px;box-shadow:0 -2px 20px rgba(0,0,0,.35);}
  .mob-sel-trigger{flex:1;min-width:0;display:flex;align-items:center;gap:6px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:8px 10px;cursor:pointer;transition:border-color .15s,background .12s;touch-action:manipulation;-webkit-tap-highlight-color:transparent;}
  .mob-sel-trigger.open,.mob-sel-trigger:hover,.mob-sel-trigger:active{border-color:var(--accent)}
  .mob-sel-chips{flex:1;min-width:0;display:flex;gap:4px;overflow:hidden}
  .mob-sel-caret{color:var(--muted);font-size:12px;flex-shrink:0;transition:transform .2s}
  .mob-sel-caret.open{transform:rotate(180deg)}
  .mob-stats{display:flex;gap:0;flex-shrink:0}
  .mob-stat{display:flex;flex-direction:column;align-items:center;padding:0 8px;border-left:1px solid var(--border);}
  .mob-stat:first-child{border-left:none}
  .mob-stat-val{font-family:var(--mono);font-size:11px;font-weight:700;color:var(--accent);line-height:1.2}
  .mob-stat-lbl{font-size:8px;color:var(--muted);letter-spacing:.4px;text-transform:uppercase}

  /* Sheet */
  .mob-sheet{position:absolute;left:0;right:0;bottom:0;z-index:700;background:var(--surface);border-radius:16px 16px 0 0;border-top:1px solid var(--border);transform:translateY(100%);transition:transform .28s cubic-bezier(.4,0,.2,1);max-height:70dvh;display:flex;flex-direction:column;box-shadow:0 -4px 32px rgba(0,0,0,.45);}
  .mob-sheet.open{transform:translateY(0)}
  .mob-sheet-handle{padding:10px 16px 6px;flex-shrink:0;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);}
  .mob-sheet-handle::before{content:'';display:block;width:36px;height:4px;border-radius:2px;background:var(--border);position:absolute;top:6px;left:50%;transform:translateX(-50%);}
  .mob-sheet-title{font-family:var(--mono);font-size:11px;color:var(--muted);letter-spacing:1px}
  .mob-sheet-close{background:none;border:none;color:var(--muted);cursor:pointer;font-size:16px;padding:4px 6px;transition:color .15s;border-radius:4px;}
  .mob-sheet-close:hover{color:var(--text)}
  .mob-sheet-list{flex:1;overflow-y:auto;overscroll-behavior:contain}
  .mob-sheet-list::-webkit-scrollbar{display:none}
  .mob-sheet-list .ms-item{padding:14px 16px;min-height:56px;font-size:14px;border-bottom:1px solid var(--border)}
  .mob-sheet-list .ms-item:last-child{border-bottom:none}
  .mob-sheet-list .ms-check{width:18px;height:18px}
  .mob-sheet-list .ms-dot{width:10px;height:10px}
  .mob-sheet-list .ms-stat-chip{font-size:10px;padding:2px 6px}
  .mob-sheet-list .ms-name{font-size:14px}

  /* Drawer */
  .mob-drawer{position:absolute;top:0;left:0;bottom:0;z-index:800;width:260px;background:var(--surface);border-right:1px solid var(--border);transform:translateX(-100%);transition:transform .25s cubic-bezier(.4,0,.2,1);display:flex;flex-direction:column;box-shadow:4px 0 24px rgba(0,0,0,.4);padding-top:env(safe-area-inset-top,0);}
  .mob-drawer.open{transform:translateX(0)}
  .mob-drawer-scrim{position:absolute;inset:0;z-index:790;background:rgba(0,0,0,.45);opacity:0;pointer-events:none;transition:opacity .25s;}
  .mob-drawer-scrim.open{opacity:1;pointer-events:auto}
  .mob-drawer-head{padding:16px;border-bottom:1px solid var(--border);flex-shrink:0;display:flex;align-items:center;gap:8px;}
  .mob-drawer-logo{font-family:var(--mono);font-size:14px;color:var(--accent);letter-spacing:3px}
  .mob-drawer-sub{font-size:9px;color:var(--muted);letter-spacing:1px;line-height:1.4}
  .mob-drawer-body{flex:1;overflow-y:auto;padding:12px 0;overscroll-behavior:contain}
  .mob-drawer-section{padding:0 16px 12px}
  .mob-drawer-label{font-size:9px;color:var(--muted);letter-spacing:1.5px;text-transform:uppercase;margin-bottom:8px;padding:0 0 4px;border-bottom:1px solid var(--border)}
  .mob-drawer-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.04);font-size:13px;color:var(--text);}
  .mob-drawer-row:last-child{border-bottom:none}
  .mob-drawer-select{background:var(--surface2);border:1px solid var(--border);border-radius:6px;color:var(--text);font-size:12px;padding:5px 8px;outline:none;cursor:pointer;font-family:var(--sans);transition:border-color .15s;}
  .mob-drawer-select:hover{border-color:var(--accent)}
  .mob-drawer-select option{background:var(--surface)}
  .mob-drawer-legend{padding:0 16px 12px}
  .mob-drawer-legend-header{
    display:flex;align-items:center;justify-content:space-between;
    cursor:pointer;user-select:none;
    font-size:9px;color:var(--muted);letter-spacing:1.5px;text-transform:uppercase;
    padding:0 0 4px;border-bottom:1px solid var(--border);margin-bottom:0;
    transition:color .15s;
  }
  .mob-drawer-legend-header:hover{color:var(--text)}
  .mob-drawer-legend-caret{font-size:9px;transition:transform .22s ease}
  .mob-drawer-legend.collapsed .mob-drawer-legend-caret{transform:rotate(-90deg)}
  .mob-legend-items{
    max-height:400px;overflow:hidden;
    transition:max-height .25s cubic-bezier(.4,0,.2,1),margin .25s;
    margin-top:6px;
  }
  .mob-drawer-legend.collapsed .mob-legend-items{max-height:0;margin-top:0}

  /* Filter wilayah di drawer mobile */
  .mob-drawer-filter{padding:0 16px 12px}
  .mob-filter-search{
    width:100%;background:var(--bg);border:1px solid var(--border);
    border-radius:6px;color:var(--text);font-size:13px;padding:8px 10px;
    outline:none;font-family:var(--sans);transition:border-color .15s;margin-top:6px;
  }
  .mob-filter-search:focus{border-color:var(--accent)}
  .mob-filter-search::placeholder{color:var(--muted)}
  .mob-filter-list{margin-top:4px;max-height:200px;overflow-y:auto;overscroll-behavior:contain}
  .mob-filter-list::-webkit-scrollbar{display:none}
  .mob-filter-item{
    display:flex;align-items:center;gap:10px;
    padding:12px 0;border-bottom:1px solid var(--border);
    cursor:pointer;font-size:13px;color:var(--text);transition:background .12s;
    -webkit-tap-highlight-color:transparent;
  }
  .mob-filter-item:last-child{border-bottom:none}
  .mob-filter-item.selected .filter-check{background:var(--accent);border-color:var(--accent)}
  .mob-filter-item.selected .filter-check-mark{display:block}
  .mob-filter-count{font-family:var(--mono);font-size:11px;color:var(--muted);margin-left:auto;flex-shrink:0}
  .mob-filter-active-label{
    font-size:11px;color:var(--accent2);margin-top:6px;display:none;
  }
  .mob-filter-active-label.show{display:flex;align-items:center;gap:6px}
  .mob-filter-clear-btn{
    font-size:11px;color:var(--muted);cursor:pointer;margin-left:auto;
    padding:2px 6px;border-radius:4px;border:1px solid var(--border);
    background:var(--surface);transition:color .12s,border-color .12s;
  }
  .mob-filter-clear-btn:hover{color:var(--red);border-color:var(--red)}
  .mob-legend-items{display:flex;flex-direction:column;gap:6px;margin-top:6px}
  .mob-legend-row{
    display:grid;grid-template-columns:9px 1fr 60px 38px;
    align-items:center;gap:8px;font-size:12px;color:var(--text);padding:6px 0;
  }
  .mob-legend-row+.mob-legend-row{border-top:1px solid rgba(255,255,255,.04)}
  .mob-legend-row .leg-name{font-size:12px}
  .mob-legend-row .leg-statbar{height:6px}
  .mob-legend-row .leg-stat-count{font-size:10px}
  .mob-legend-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0}
  .mob-loc-section{padding:0 16px 12px}
  .mob-loc-input{display:flex;flex-direction:column;gap:6px;margin-top:6px}
  .mob-loc-input label{font-size:11px;color:var(--muted);display:flex;flex-direction:column;gap:3px}
  .mob-loc-input input{background:var(--bg);border:1px solid var(--border);border-radius:6px;color:var(--text);font-family:var(--mono);font-size:13px;padding:8px 10px;outline:none;transition:border .15s;}
  .mob-loc-input input:focus{border-color:var(--accent)}
  .mob-loc-btns{display:flex;gap:6px;margin-top:6px}
  .mob-loc-btns button{flex:1;padding:9px 6px;border-radius:6px;font-size:12px;cursor:pointer;border:1px solid var(--border);background:var(--surface2);color:var(--text);touch-action:manipulation;transition:background .12s,border-color .15s;}
  .mob-loc-btns button:hover{background:var(--surface);border-color:var(--accent)}
  .mob-loc-btns button.primary{background:var(--accent);color:#000;border-color:var(--accent);font-weight:700}
  .mob-loc-btns button.primary:hover{background:#00c4dd}

  /* Detail panel mobile */
  .detail-panel{position:absolute;bottom:0;left:0;right:0;top:auto;width:auto;max-height:82dvh;height:auto;transform:translateY(100%);border-radius:16px 16px 0 0;border-top:1px solid var(--border);border-left:none;z-index:900;padding-bottom:env(safe-area-inset-bottom);}
  .detail-panel.open{transform:translateY(0)}
  .panel-body{max-height:calc(82dvh - 110px - env(safe-area-inset-bottom));padding:10px 12px;overflow-y:auto;overscroll-behavior:contain;}
  .panel-body::-webkit-scrollbar{display:none}
  .panel-handle{padding:16px 12px 8px;position:relative}
  .panel-handle::before{content:'';display:block;width:36px;height:4px;border-radius:2px;background:var(--border);position:absolute;top:6px;left:50%;transform:translateX(-50%);}
  .panel-handle h3{font-size:12px}
  .panel-sub{font-size:11px}
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
  <div id="map"></div>
  <div id="stateOverlay" class="state-msg" style="position:absolute;inset:0;background:transparent;z-index:500;pointer-events:none"></div>

  <div class="float-logo" id="floatLogo">
    <span class="logo-mark">KOMENK</span>
    <div class="logo-sep"></div>
    <span class="logo-sub">KOORDINAT MICROWAVE LINK</span>
  </div>

  <div class="float-controls" id="floatControls">
    <div class="ctrl-pill">
      <span>Peta</span>
      <select id="basemapSel">
        <option value="dark">&#127761; Dark</option>
        <option value="bright">&#9728;&#65039; Bright</option>
        <option value="satellite">&#128752;&#65039; Satelit</option>
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
    <div class="ctrl-pill ctrl-pill-sel" style="padding:0;overflow:visible;position:relative;background:none;border:none;box-shadow:none">
      <div class="ms-trigger" id="msTrigger" onclick="toggleDropdown()" style="height:32px;padding:4px 10px">
        <div class="chips-area" id="chipsArea">
          <span class="chip" style="border-color:var(--accent);color:var(--accent)">Memuat&#8230;</span>
        </div>
        <span class="chip-caret" id="chipCaret">&#9660;</span>
      </div>
      <div class="ms-dropdown" id="msDropdown" style="position:absolute;top:calc(100% + 4px);left:0;min-width:260px"></div>
    </div>

    <!-- Filter wilayah kab/kota -->
    <div class="filter-wrap" id="filterWrap">
      <div class="filter-trigger" id="filterTrigger" onclick="toggleFilterDropdown()">
        <span class="filter-label" id="filterLabel">Semua wilayah</span>
        <span class="filter-clear" id="filterClear" title="Reset filter" onclick="clearAreaFilter(event)">&#10005;</span>
        <span class="filter-caret" id="filterCaret">&#9660;</span>
      </div>
      <div class="filter-dropdown" id="filterDropdown">
        <div class="filter-search-wrap">
          <input type="text" class="filter-search-input" id="filterSearchInput"
            placeholder="Cari kota/kabupaten&#8230;"
            oninput="filterAreaList(this.value)">
        </div>
        <div class="filter-list" id="filterList"></div>
      </div>
    </div>
  </div>

  <div class="map-controls" id="mapControls">
    <button class="map-ctrl-btn" id="locBtn" title="Lokasi saya">&#9678;</button>
    <button class="map-ctrl-btn compass-btn" id="compassBtn" title="Reset rotasi peta">
      <svg id="compassNeedle" width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
        <polygon points="9,1 11,9 9,17 7,9" fill="currentColor" opacity=".4"/>
        <polygon points="9,1 11,9 9,8 7,9" fill="#ef4444"/>
      </svg>
    </button>
  </div>

  <div class="loc-popup" id="locPopup">
    <label>Latitude<input type="number" id="manualLat" placeholder="-7.8013" step="any"></label>
    <label>Longitude<input type="number" id="manualLng" placeholder="110.3649" step="any"></label>
    <div class="loc-popup-btns">
      <button onclick="useManualLoc()">Pakai</button>
      <button onclick="useGPSLoc()" title="Deteksi otomatis">&#9678; GPS</button>
      <button class="primary" onclick="goToLoc()">Go</button>
    </div>
  </div>

  <div class="detail-panel" id="detailPanel">
    <div class="panel-handle">
      <h3 id="panelTitle">DETAIL</h3>
      <span class="panel-sub" id="panelSub"></span>
      <button class="panel-close" id="panelClose" title="Tutup">&#10005;</button>
    </div>
    <div class="panel-tabs" id="panelTabs" style="display:none"></div>
    <div class="panel-breadcrumb" id="panelBreadcrumb" style="display:none"></div>
    <div class="panel-body" id="panelBody"></div>
  </div>

  <div class="float-stats" id="floatStats">
    <div class="stat-pill"><span class="sp-val" id="footIsr">&#8212;</span><span class="sp-lbl">ISR</span></div>
    <div class="stat-pill"><span class="sp-val" id="footLink">&#8212;</span><span class="sp-lbl">MW Link</span></div>
    <div class="stat-pill"><span class="sp-val" id="footKlien">&#8212;</span><span class="sp-lbl">Klien</span></div>
  </div>

  <button class="float-legend-btn" id="legendBtn" onclick="toggleLegend()">
    Legenda <span class="legend-caret">&#9650;</span>
  </button>
  <div class="legend-panel" id="legendPanel">
    <div class="legend-inner" id="legendInner"></div>
  </div>

  <!-- Legend desktop: selalu tampil di kiri bawah saat ada klien aktif -->
  <div class="desktop-legend" id="desktopLegend">
    <div class="desktop-legend-title" onclick="toggleDesktopLegend()">
      <span class="desktop-legend-title-text">Status ISR per klien</span>
      <span class="desktop-legend-count" id="desktopLegendCount">0</span>
      <span class="desktop-legend-caret">&#9660;</span>
    </div>
    <div class="desktop-legend-body" id="desktopLegendInner"></div>
  </div>

  <!-- ══ MOBILE ══ -->
  <!-- FAB kiri atas: hamburger (bukan huruf K) -->
  <div class="mob-fab-logo" id="mobFabLogo" onclick="mobOpenDrawer()">
    <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect width="18" height="2.5" rx="1.25" fill="currentColor"/>
      <rect y="5.75" width="18" height="2.5" rx="1.25" fill="currentColor"/>
      <rect y="11.5" width="18" height="2.5" rx="1.25" fill="currentColor"/>
    </svg>
  </div>

  <!-- FAB kanan atas: zoom + kompas + GPS -->
  <div class="mob-fab-zoom">
    <div class="mob-fab-btn" onclick="map.zoomIn()">+</div>
    <div class="mob-fab-btn" onclick="map.zoomOut()">&#8722;</div>
    <div class="mob-fab-compass" id="mobCompassBtn" onclick="mobResetNorth()" title="Reset ke utara">
      <svg id="mobCompassNeedle" width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
        <polygon points="9,1 11,9 9,17 7,9" fill="currentColor" opacity=".4"/>
        <polygon points="9,1 11,9 9,8 7,9" fill="#ef4444"/>
      </svg>
    </div>
    <div class="mob-fab-btn accent" id="mobLocBtn" onclick="mobToggleGPS()">&#9678;</div>
  </div>

  <div class="mob-bar" id="mobBar">
    <div class="mob-sel-trigger" id="mobSelTrigger" onclick="mobToggleSheet()">
      <div class="mob-sel-chips" id="mobSelChips">
        <span style="color:var(--muted);font-size:11px">Pilih klien&#8230;</span>
      </div>
      <span class="mob-sel-caret" id="mobSelCaret">&#9650;</span>
    </div>
    <div class="mob-stats">
      <div class="mob-stat"><span class="mob-stat-val" id="mobIsr">&#8212;</span><span class="mob-stat-lbl">ISR</span></div>
      <div class="mob-stat"><span class="mob-stat-val" id="mobMw">&#8212;</span><span class="mob-stat-lbl">MW</span></div>
    </div>
  </div>

  <div class="mob-sheet" id="mobSheet">
    <div class="mob-sheet-handle">
      <span class="mob-sheet-title">PILIH KLIEN</span>
      <button class="mob-sheet-close" onclick="mobCloseSheet()">&#10005;</button>
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
            <option value="dark">&#127761; Dark</option>
            <option value="bright">&#9728;&#65039; Bright</option>
            <option value="satellite">&#128752;&#65039; Satelit</option>
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
          <button onclick="mobGPS()">&#9678; GPS</button>
          <button class="primary" onclick="mobGoLoc()">Go</button>
        </div>
      </div>
      <!-- Filter wilayah di drawer -->
      <div class="mob-drawer-filter" id="mobDrawerFilter" style="display:none">
        <div class="mob-drawer-label">Filter Wilayah</div>
        <div class="mob-filter-active-label" id="mobFilterActiveLabel">
          <span id="mobFilterActiveText">Semua wilayah</span>
          <span class="mob-filter-clear-btn" onclick="clearAreaFilter()">Reset</span>
        </div>
        <input type="text" class="mob-filter-search" id="mobFilterSearch"
          placeholder="Cari kota/kabupaten&#8230;"
          oninput="mobFilterAreaList(this.value)">
        <div class="mob-filter-list" id="mobFilterList"></div>
      </div>

      <!-- Legenda collapsible -->
      <div class="mob-drawer-legend" id="mobDrawerLegend" style="display:none">
        <div class="mob-drawer-legend-header" onclick="toggleMobLegend()">
          <span>Legenda</span>
          <span class="mob-drawer-legend-caret">&#9660;</span>
        </div>
        <div class="mob-legend-items" id="mobLegendItems"></div>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js"></script>
<script>
!function(t){"function"==typeof define&&define.amd?define(t):t()}((function(){"use strict";const t=L.extend({},L.DomUtil);L.extend(L.DomUtil,{setTransform:function(o,e,i,n,a){var s=e||new L.Point(0,0);if(!n)return e=s._round(),t.setTransform.apply(this,arguments);s=s.rotateFrom(n,a),o.style[L.DomUtil.TRANSFORM]="translate3d("+s.x+"px,"+s.y+"px,0)"+(i?" scale("+i+")":"")+" rotate("+n+"rad)"},setPosition:function(o,e,i,n,a){if(!i)return t.setPosition.apply(this,arguments);o._leaflet_pos=e,L.Browser.any3d?L.DomUtil.setTransform(o,e,a,i,n):(o.style.left=e.x+"px",o.style.top=e.y+"px")},DEG_TO_RAD:Math.PI/180,RAD_TO_DEG:180/Math.PI}),L.Draggable.include({}),L.extend(L.Point.prototype,{rotate:function(t){return this.rotateFrom(t,new L.Point(0,0))},rotateFrom:function(t,o){if(!t)return this;var e=Math.sin(t),i=Math.cos(t),n=o.x,a=o.y,s=this.x-n,r=this.y-a;return new L.Point(s*i-r*e+n,s*e+r*i+a)}});const o=L.extend({},L.DivOverlay.prototype);L.DivOverlay.include({getEvents:function(){return L.extend(o.getEvents.apply(this,arguments),{rotate:this._updatePosition})},_updatePosition:function(){if(this._map&&(o._updatePosition.apply(this,arguments),this._map&&this._map._rotate&&this._zoomAnimated)){var t=this._getAnchor(),e=L.DomUtil.getPosition(this._container).subtract(t);L.DomUtil.setPosition(this._container,this._map.rotatedPointToMapPanePoint(e).add(t))}}});const e=L.extend({},L.Popup.prototype);L.Popup.include({_animateZoom:function(t){if(e._animateZoom.apply(this,arguments),this._map&&this._map._rotate){var o=this._getAnchor(),i=L.DomUtil.getPosition(this._container).subtract(o);L.DomUtil.setPosition(this._container,this._map.rotatedPointToMapPanePoint(i).add(o))}},_adjustPan:function(){if(!(!this.options.autoPan||this._map._panAnim&&this._map._panAnim._inProgress))if(this._autopanning)this._autopanning=!1;else{var t=this._map,o=parseInt(L.DomUtil.getStyle(this._container,"marginBottom"),10)||0,e=this._container.offsetHeight+o,i=this._containerWidth,n=new L.Point(this._containerLeft,-e-this._containerBottom);n._add(L.DomUtil.getPosition(this._container));var a=n._add(this._map._getMapPanePos()),s=L.point(this.options.autoPanPadding),r=L.point(this.options.autoPanPaddingTopLeft||s),h=L.point(this.options.autoPanPaddingBottomRight||s),_=t.getSize(),d=0,p=0;a.x+i+h.x>_.x&&(d=a.x+i-_.x+h.x),a.x-d-r.x<0&&(d=a.x-r.x),a.y+e+h.y>_.y&&(p=a.y+e-_.y+h.y),a.y-p-r.y<0&&(p=a.y-r.y),(d||p)&&(this.options.keepInView&&(this._autopanning=!0),t.fire("autopanstart").panBy([d,p]))}}});const i=L.extend({},L.Tooltip.prototype);L.Tooltip.include({_animateZoom:function(t){if(!this._map._rotate)return i._animateZoom.apply(this,arguments);var o=this._map._latLngToNewLayerPoint(this._latlng,t.zoom,t.center);o=this._map.rotatedPointToMapPanePoint(o),this._setPosition(o)},_updatePosition:function(){if(!this._map._rotate)return i._updatePosition.apply(this,arguments);var t=this._map.latLngToLayerPoint(this._latlng);t=this._map.rotatedPointToMapPanePoint(t),this._setPosition(t)}}),L.extend({},L.Icon.prototype),L.Icon.include({_setIconStyles:function(t,o){var e=this.options,i=e[o+"Size"];"number"==typeof i&&(i=[i,i]);var n=L.point(i),a=L.point("shadow"===o&&e.shadowAnchor||e.iconAnchor||n&&n.divideBy(2,!0));t.className="leaflet-marker-"+o+" "+(e.className||""),a&&(t.style.marginLeft=-a.x+"px",t.style.marginTop=-a.y+"px",t.style[L.DomUtil.TRANSFORM+"Origin"]=a.x+"px "+a.y+"px 0px"),n&&(t.style.width=n.x+"px",t.style.height=n.y+"px")}});const n=L.extend({},L.Marker.prototype);var a;L.Marker.mergeOptions({rotation:0,rotateWithView:!1,scale:void 0});var s={_onDrag:function(t){var o=this._marker,e=o.options.rotation||o.options.rotateWithView,i=o._shadow,n=L.DomUtil.getPosition(o._icon);!e&&i&&L.DomUtil.setPosition(i,n),o._map._rotate&&(n=o._map.mapPanePointToRotatedPoint(n));var a=o._map.layerPointToLatLng(n);o._latlng=a,t.latlng=a,t.oldLatLng=this._oldLatLng,e?o.setLatLng(a):o.fire("move",t),o.fire("drag",t)},_onDragEnd:function(t){this._marker._map._rotate&&this._marker.update(),a._onDragEnd.apply(this,arguments)}};L.Marker.include({getEvents:function(){return L.extend(n.getEvents.apply(this,arguments),{rotate:this.update})},_initInteraction:function(){var t=n._initInteraction.apply(this,arguments);return this.dragging&&this.dragging.enabled()&&this._map&&this._map._rotate&&(a=a||Object.getPrototypeOf(this.dragging),this.dragging.disable(),Object.assign(this.dragging,{_onDrag:s._onDrag.bind(this.dragging),_onDragEnd:s._onDragEnd.bind(this.dragging)}),this.dragging.enable()),t},_setPos:function(t){this._map._rotate&&(t=this._map.rotatedPointToMapPanePoint(t));var o=this.options.rotation||0;this.options.rotateWithView&&(o+=this._map._bearing),this._icon&&L.DomUtil.setPosition(this._icon,t,o,t,this.options.scale),this._shadow&&L.DomUtil.setPosition(this._shadow,t,o,t,this.options.scale),this._zIndex=t.y+this.options.zIndexOffset,this._resetZIndex()},setRotation:function(t){this.options.rotation=t,this.update()}});const r=L.extend({},L.GridLayer.prototype);L.GridLayer.include({getEvents:function(){var t=r.getEvents.apply(this,arguments);return this._map._rotate&&!this.options.updateWhenIdle&&(this._onRotate||(this._onRotate=L.Util.throttle(this._onMoveEnd,this.options.updateInterval,this)),t.rotate=this._onRotate),t},_getTiledPixelBounds:function(t){return this._map._rotate?this._map._getNewPixelBounds(t,this._tileZoom):r._getTiledPixelBounds.apply(this,arguments)}});const h=L.extend({},L.Renderer.prototype);L.Renderer.include({getEvents:function(){return L.extend(h.getEvents.apply(this,arguments),{rotate:this._update})},onAdd:function(){h.onAdd.apply(this,arguments),L.version<="1.9.3"&&this._container.classList.add("leaflet-zoom-animated")},_updateTransform:function(t,o){if(!this._map._rotate)return h._updateTransform.apply(this,arguments);var e=this._map.getZoomScale(o,this._zoom),i=this._map._latLngToNewLayerPoint(this._topLeft,o,t);L.DomUtil.setTransform(this._container,i,e)},_update:function(){if(!this._map._rotate)return h._update.apply(this,arguments);this._bounds=this._map._getPaddedPixelBounds(this.options.padding),this._topLeft=this._map.layerPointToLatLng(this._bounds.min),this._center=this._map.getCenter(),this._zoom=this._map.getZoom()}});const _=L.extend({},L.Map.prototype);L.Map.mergeOptions({rotate:!1,bearing:0}),L.Map.include({initialize:function(t,o){o.rotate&&(this._rotate=!0,this._bearing=0),_.initialize.apply(this,arguments),this.options.rotate&&this.setBearing(this.options.bearing)},containerPointToLayerPoint:function(t){return this._rotate?L.point(t).subtract(this._getMapPanePos()).rotateFrom(-this._bearing,this._getRotatePanePos()).subtract(this._getRotatePanePos()):_.containerPointToLayerPoint.apply(this,arguments)},layerPointToContainerPoint:function(t){return this._rotate?L.point(t).add(this._getRotatePanePos()).rotateFrom(this._bearing,this._getRotatePanePos()).add(this._getMapPanePos()):_.layerPointToContainerPoint.apply(this,arguments)},rotatedPointToMapPanePoint:function(t){return L.point(t).rotate(this._bearing)._add(this._getRotatePanePos())},mapPanePointToRotatedPoint:function(t){return L.point(t)._subtract(this._getRotatePanePos()).rotate(-this._bearing)},mapBoundsToContainerBounds:function(t){if(!this._rotate&&_.mapBoundsToContainerBounds)return _.mapBoundsToContainerBounds.apply(this,arguments);const o=this.getPixelOrigin(),e=this.layerPointToContainerPoint(this.project(t.getNorthWest())._subtract(o)),i=this.layerPointToContainerPoint(this.project(t.getNorthEast())._subtract(o)),n=this.layerPointToContainerPoint(this.project(t.getSouthWest())._subtract(o)),a=this.layerPointToContainerPoint(this.project(t.getSouthEast())._subtract(o));return L.bounds([L.point(Math.min(e.x,i.x,a.x,n.x),Math.min(e.y,i.y,a.y,n.y)),L.point(Math.max(e.x,i.x,a.x,n.x),Math.max(e.y,i.y,a.y,n.y))])},getBounds:function(){if(!this._rotate)return _.getBounds.apply(this,arguments);var t=this.getSize();return new L.LatLngBounds([this.containerPointToLatLng([0,0]),this.containerPointToLatLng([t.x,0]),this.containerPointToLatLng([t.x,t.y]),this.containerPointToLatLng([0,t.y])])},setBearing:function(t){if(L.Browser.any3d&&this._rotate){var o=L.Util.wrapNum(t,[0,360])*L.DomUtil.DEG_TO_RAD,e=this._getPixelCenter(),i=this._getRotatePanePos().rotateFrom(-this._bearing,e),n=i.rotateFrom(o,e);L.DomUtil.setPosition(this._rotatePane,i,o,e),this._pivot=e,this._bearing=o,this._rotatePanePos=n,this.fire("rotate")}},getBearing:function(){return this._bearing*L.DomUtil.RAD_TO_DEG},_initPanes:function(){var t=this._panes={};this._paneRenderers={},this._mapPane=this.createPane("mapPane",this._container),L.DomUtil.setPosition(this._mapPane,new L.Point(0,0)),this._rotate?(this._rotatePane=this.createPane("rotatePane",this._mapPane),this._norotatePane=this.createPane("norotatePane",this._mapPane),this.createPane("tilePane",this._rotatePane),this.createPane("overlayPane",this._rotatePane),this.createPane("shadowPane",this._norotatePane),this.createPane("markerPane",this._norotatePane),this.createPane("tooltipPane",this._norotatePane),this.createPane("popupPane",this._norotatePane)):(this.createPane("tilePane"),this.createPane("overlayPane"),this.createPane("shadowPane"),this.createPane("markerPane"),this.createPane("tooltipPane"),this.createPane("popupPane")),this.options.markerZoomAnimation||(L.DomUtil.addClass(t.markerPane,"leaflet-zoom-hide"),L.DomUtil.addClass(t.shadowPane,"leaflet-zoom-hide"))},panInside(t,o){if(!this._rotate||Math.abs(this._bearing).toFixed(1)<.1)return _.panInside.apply(this,arguments);o=o||{};const e=L.point(o.paddingTopLeft||o.padding||[0,0]),i=L.point(o.paddingBottomRight||o.padding||[0,0]),n=this._container.getBoundingClientRect(),a=this.latLngToContainerPoint(t),s=L.bounds([L.point(n),L.point(n).add(this.getSize())]),r=s.getCenter(),h=L.bounds([s.min.add(e),s.max.subtract(i)]),d=h.getSize();if(!h.contains(a)){this._enforcingBounds=!0;const t=a.subtract(h.getCenter()),e=h.extend(a).getSize().subtract(d);r.x+=t.x<0?-e.x:e.x,r.y+=t.y<0?-e.y:e.y,this.panTo(this.containerPointToLatLng(r),o),this._enforcingBounds=!1}return this},getBoundsZoom(t,o,e){if(!this._rotate||Math.abs(this._bearing).toFixed(1)<.1)return _.getBoundsZoom.apply(this,arguments);t=L.latLngBounds(t),e=L.point(e||[0,0]);let i=this.getZoom()||0;const n=this.getMinZoom(),a=this.getMaxZoom(),s=this.getSize().subtract(e),r=this.mapBoundsToContainerBounds(t).getSize(),h=this.options.zoomSnap,d=s.x/r.x,p=s.y/r.y,l=o?Math.max(d,p):Math.min(d,p);return i=this.getScaleZoom(l,i),h&&(i=Math.round(i/(h/100))*(h/100),i=o?Math.ceil(i/h)*h:Math.floor(i/h)*h),Math.max(n,Math.min(a,i))},_getCenterOffset:function(t){var o=_._getCenterOffset.apply(this,arguments);return this._rotate&&(o=o.rotate(this._bearing)),o},_getRotatePanePos:function(){return this._rotatePanePos||new L.Point(0,0)},_getNewPixelOrigin:function(t,o){if(!this._rotate)return _._getNewPixelOrigin.apply(this,arguments);var e=this.getSize()._divideBy(2);return this.project(t,o).rotate(this._bearing)._subtract(e)._add(this._getMapPanePos())._add(this._getRotatePanePos()).rotate(-this._bearing)._round()},_getNewPixelBounds:function(t,o){if(t=t||this.getCenter(),o=o||this.getZoom(),!this._rotate&&_._getNewPixelBounds)return _._getNewPixelBounds.apply(this,arguments);var e=this._animatingZoom?Math.max(this._animateToZoom,this.getZoom()):this.getZoom(),i=this.getZoomScale(e,o),n=this.project(t,o).floor(),a=this.getSize(),s=new L.Bounds([this.containerPointToLayerPoint([0,0]).floor(),this.containerPointToLayerPoint([a.x,0]).floor(),this.containerPointToLayerPoint([0,a.y]).floor(),this.containerPointToLayerPoint([a.x,a.y]).floor()]).getSize().divideBy(2*i);return new L.Bounds(n.subtract(s),n.add(s))},_getPixelCenter:function(){return!this._rotate&&_._getPixelCenter?_._getPixelCenter.apply(this,arguments):this.getSize()._divideBy(2)._subtract(this._getMapPanePos())},_getPaddedPixelBounds:function(t){if(!this._rotate&&_._getPaddedPixelBounds)return _._getPaddedPixelBounds.apply(this,arguments);var o=t,e=this.getSize(),i=e.multiplyBy(-o),n=e.multiplyBy(1+o);return new L.Bounds([this.containerPointToLayerPoint([i.x,i.y]).floor(),this.containerPointToLayerPoint([i.x,n.y]).floor(),this.containerPointToLayerPoint([n.x,i.y]).floor(),this.containerPointToLayerPoint([n.x,n.y]).floor()])},_handleGeolocationResponse:function(t){if(this._container._leaflet_id){var o=t.coords.latitude,e=t.coords.longitude,i=t.coords.heading,n=new L.LatLng(o,e),a=n.toBounds(t.coords.accuracy),s=this._locateOptions;if(s.setView){var r=this.getBoundsZoom(a);this.setView(n,s.maxZoom?Math.min(r,s.maxZoom):r)}var h={latlng:n,bounds:a,timestamp:t.timestamp,heading:i};for(var _ in t.coords)"number"==typeof t.coords[_]&&(h[_]=t.coords[_]);this.fire("locationfound",h)}}}),L.Map.CompassBearing=L.Handler.extend({initialize:function(t){this._map=t,"ondeviceorientationabsolute"in window?this.__deviceOrientationEvent="deviceorientationabsolute":"ondeviceorientation"in window&&(this.__deviceOrientationEvent="deviceorientation"),this._throttled=L.Util.throttle(this._onDeviceOrientation,100,this)},addHooks:function(){this._map._rotate&&this.__deviceOrientationEvent?L.DomEvent.on(window,this.__deviceOrientationEvent,this._throttled,this):this.disable()},removeHooks:function(){this._map._rotate&&this.__deviceOrientationEvent&&L.DomEvent.off(window,this.__deviceOrientationEvent,this._throttled,this)},_onDeviceOrientation:function(t){var o=t.webkitCompassHeading||t.alpha,e=0;!t.absolute&&t.webkitCompassHeading&&(o=360-o),t.absolute||void 0===window.orientation||(e=window.orientation),this._map.setBearing(o-e)}}),L.Map.addInitHook("addHandler","compassBearing",L.Map.CompassBearing),L.Map.mergeOptions({trackContainerMutation:!1}),L.Map.ContainerMutation=L.Handler.extend({addHooks:function(){this._observer||(this._observer=new MutationObserver(L.Util.bind(this._map.invalidateSize,this._map))),this._observer.observe(this._map.getContainer(),{childList:!1,attributes:!0,characterData:!1,subtree:!1,attributeFilter:["style"]})},removeHooks:function(){this._observer.disconnect()}}),L.Map.addInitHook("addHandler","trackContainerMutation",L.Map.ContainerMutation),L.Map.mergeOptions({bounceAtZoomLimits:!0}),L.Map.TouchGestures=L.Handler.extend({initialize:function(t){this._map=t,this.rotate=!!this._map.options.touchRotate,this.zoom=!!this._map.options.touchZoom},addHooks:function(){L.DomEvent.on(this._map._container,"touchstart",this._onTouchStart,this)},removeHooks:function(){L.DomEvent.off(this._map._container,"touchstart",this._onTouchStart,this)},_onTouchStart:function(t){var o=this._map;if(t.touches&&2===t.touches.length&&!o._animatingZoom&&!this._zooming&&!this._rotating){var e=o.mouseEventToContainerPoint(t.touches[0]),i=o.mouseEventToContainerPoint(t.touches[1]),n=e.subtract(i);this._centerPoint=o.getSize()._divideBy(2),this._startLatLng=o.containerPointToLatLng(this._centerPoint),this.zoom?("center"!==o.options.touchZoom&&(this._pinchStartLatLng=o.containerPointToLatLng(e.add(i)._divideBy(2))),this._startDist=e.distanceTo(i),this._startZoom=o.getZoom(),this._zooming=!0):this._zooming=!1,this.rotate?(this._startTheta=Math.atan(n.x/n.y),this._startBearing=o.getBearing(),n.y<0&&(this._startBearing+=180),this._rotating=!0):this._rotating=!1,this._moved=!1,o._stop(),L.DomEvent.on(document,"touchmove",this._onTouchMove,this).on(document,"touchend touchcancel",this._onTouchEnd,this),L.DomEvent.preventDefault(t)}},_onTouchMove:function(t){if(t.touches&&2===t.touches.length&&(this._zooming||this._rotating)){var o,e=this._map,i=e.mouseEventToContainerPoint(t.touches[0]),n=e.mouseEventToContainerPoint(t.touches[1]),a=i.subtract(n),s=i.distanceTo(n)/this._startDist;if(this._rotating){var r=(Math.atan(a.x/a.y)-this._startTheta)*L.DomUtil.RAD_TO_DEG;a.y<0&&(r+=180),r&&e.setBearing(this._startBearing-r)}if(this._zooming)if(this._zoom=e.getScaleZoom(s,this._startZoom),!e.options.bounceAtZoomLimits&&(this._zoom<e.getMinZoom()&&s<1||this._zoom>e.getMaxZoom()&&s>1)&&(this._zoom=e._limitZoom(this._zoom)),"center"===e.options.touchZoom){if(this._center=this._startLatLng,1===s)return}else{if(o=i._add(n)._divideBy(2)._subtract(this._centerPoint),1===s&&0===o.x&&0===o.y)return;var h=-e.getBearing()*L.DomUtil.DEG_TO_RAD;this._center=e.unproject(e.project(this._pinchStartLatLng).subtract(o.rotate(h)))}this._moved||(e._moveStart(!0,!1),this._moved=!0),L.Util.cancelAnimFrame(this._animRequest);var _=e._move.bind(e,this._center,this._zoom,{pinch:!0,round:!1},void 0);this._animRequest=L.Util.requestAnimFrame(_,this,!0),L.DomEvent.preventDefault(t)}},_onTouchEnd:function(){this._moved&&(this._zooming||this._rotating)?(this._zooming=!1,this._rotating=!1,L.Util.cancelAnimFrame(this._animRequest),L.DomEvent.off(document,"touchmove",this._onTouchMove,this).off(document,"touchend touchcancel",this._onTouchEnd,this),this.zoom&&(this._map.options.zoomAnimation?this._map._animateZoom(this._center,this._map._limitZoom(this._zoom),!0,this._map.options.zoomSnap):this._map._resetView(this._center,this._map._limitZoom(this._zoom)))):this._zooming=!1}}),L.Map.addInitHook("addHandler","touchGestures",L.Map.TouchGestures),L.Map.mergeOptions({touchRotate:!1}),L.Map.TouchRotate=L.Handler.extend({addHooks:function(){this._map.touchGestures.enable(),this._map.touchGestures.rotate=!0},removeHooks:function(){this._map.touchGestures.rotate=!1}}),L.Map.addInitHook("addHandler","touchRotate",L.Map.TouchRotate),L.Map.mergeOptions({shiftKeyRotate:!0}),L.Map.ShiftKeyRotate=L.Handler.extend({addHooks:function(){L.DomEvent.on(this._map._container,"wheel",this._handleShiftScroll,this),this._map.shiftKeyRotate.rotate=!0},removeHooks:function(){L.DomEvent.off(this._map._container,"wheel",this._handleShiftScroll,this),this._map.shiftKeyRotate.rotate=!1},_handleShiftScroll:function(t){t.shiftKey?(t.preventDefault(),this._map.scrollWheelZoom.disable(),this._map.setBearing(this._map._bearing*L.DomUtil.RAD_TO_DEG+5*Math.sign(t.deltaY))):this._map.scrollWheelZoom.enable()}}),L.Map.addInitHook("addHandler","shiftKeyRotate",L.Map.ShiftKeyRotate),L.Map.addInitHook((function(){this.scrollWheelZoom.enabled()&&this.shiftKeyRotate.enabled()&&(this.scrollWheelZoom.disable(),this.scrollWheelZoom.enable())})),L.Map.mergeOptions({touchZoom:L.Browser.touch,bounceAtZoomLimits:!1}),L.Map.TouchZoom=L.Handler.extend({addHooks:function(){L.DomUtil.addClass(this._map._container,"leaflet-touch-zoom"),this._map.touchGestures.enable(),this._map.touchGestures.zoom=!0},removeHooks:function(){L.DomUtil.removeClass(this._map._container,"leaflet-touch-zoom"),this._map.touchGestures.zoom=!1}}),L.Map.addInitHook("addHandler","touchZoom",L.Map.TouchZoom),L.Control.Rotate=L.Control.extend({options:{position:"topleft",closeOnZeroBearing:!0},onAdd:function(t){var o=this._container=L.DomUtil.create("div","leaflet-control-rotate leaflet-bar"),e=this._arrow=L.DomUtil.create("span","leaflet-control-rotate-arrow");e.style.backgroundImage="url(\"data:image/svg+xml;charset=utf-8,%3Csvg width='29' height='29' viewBox='0 0 29 29' xmlns='http://www.w3.org/2000/svg' fill='%23333'%3E%3Cpath d='M10.5 14l4-8 4 8h-8z'/%3E%3Cpath d='M10.5 16l4 8 4-8h-8z' fill='%23ccc'/%3E%3C/svg%3E\")",e.style.cursor="grab",e.style.display="block",e.style.width="100%",e.style.height="100%",e.style.backgroundRepeat="no-repeat",e.style.backgroundPosition="50%";var i=this._link=L.DomUtil.create("a","leaflet-control-rotate-toggle",o);return i.appendChild(e),i.href="#",i.title="Rotate map",L.DomEvent.on(i,"dblclick",L.DomEvent.stopPropagation).on(i,"mousedown",this._handleMouseDown,this).on(i,"click",L.DomEvent.stop).on(i,"click",this._cycleState,this).on(i,"click",this._refocusOnMap,this),L.Browser.any3d||L.DomUtil.addClass(i,"leaflet-disabled"),this._restyle(),t.on("rotate",this._restyle,this),this._follow=!1,this._canFollow=!1,this.options.closeOnZeroBearing&&0===t.getBearing()&&(o.style.display="none"),o},onRemove:function(t){t.off("rotate",this._restyle,this)},_handleMouseDown:function(t){L.DomEvent.stop(t),this.dragging=!0,this.dragstartX=t.pageX,this.dragstartY=t.pageY,L.DomEvent.on(document,"mousemove",this._handleMouseDrag,this).on(document,"mouseup",this._handleMouseUp,this)},_handleMouseUp:function(t){L.DomEvent.stop(t),this.dragging=!1,L.DomEvent.off(document,"mousemove",this._handleMouseDrag,this).off(document,"mouseup",this._handleMouseUp,this)},_handleMouseDrag:function(t){if(this.dragging){var o=t.clientX-this.dragstartX;this._map.setBearing(o)}},_cycleState:function(t){if(this._map){var o=this._map;o.touchRotate.enabled()||o.compassBearing.enabled()?o.compassBearing.enabled()?(o.compassBearing.disable(),o.setBearing(0),this.options.closeOnZeroBearing&&o.touchRotate.enable()):(o.touchRotate.disable(),o.compassBearing.enable()):o.touchRotate.enable(),this._restyle()}},_restyle:function(){if(this._map.options.rotate){var t=this._map,o=t.getBearing();this._arrow.style.transform="rotate("+o+"deg)",o&&this.options.closeOnZeroBearing&&(this._container.style.display="block"),t.compassBearing.enabled()?this._link.style.backgroundColor="orange":t.touchRotate.enabled()?this._link.style.backgroundColor=null:(this._link.style.backgroundColor="grey",0===o&&this.options.closeOnZeroBearing&&(this._container.style.display="none"))}else L.DomUtil.addClass(this._link,"leaflet-disabled")}}),L.control.rotate=function(t){return new L.Control.Rotate(t)},L.Map.mergeOptions({rotateControl:!0}),L.Map.addInitHook((function(){if(this.options.rotateControl){var t="object"==typeof this.options.rotateControl?this.options.rotateControl:{};this.rotateControl=L.control.rotate(t),this.addControl(this.rotateControl)}}))}));

const API = { clients:'api/clients.php', isr:'api/isr.php' };
const map = L.map('map',{zoomControl:false,rotate:true,touchRotate:true,bearing:0,rotateControl:false,shiftKeyRotate:false});
L.control.zoom({position:'topright'}).addTo(map);
map.setView([-7.80,110.37],11);

const BASEMAPS={
  dark:{url:'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',attr:'\u00A9 OpenStreetMap \u00A9 CARTO',sub:'abcd'},
  bright:{url:'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',attr:'\u00A9 OpenStreetMap \u00A9 CARTO',sub:'abcd'},
  satellite:{url:'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',attr:'\u00A9 Esri',sub:'',maxNativeZoom:19}
};
let currentBaseTile=null;
/* ── Persist state ke localStorage ── */
const LS = {
  KEY: 'komenk_state',
  _skip: false,
  save() {
    if (this._skip) return;
    try {
      // selectedIds dan elements mungkin belum ada saat dipanggil awal
      const ids = typeof selectedIds !== 'undefined' ? [...selectedIds] : [];
      const bm = document.getElementById('basemapSel')?.value || 'dark';
      const th = document.documentElement.getAttribute('data-theme') || 'dark';
      localStorage.setItem(this.KEY, JSON.stringify({clients:ids,basemap:bm,theme:th}));
    } catch(e) {}
  },
  load() {
    try { return JSON.parse(localStorage.getItem(this.KEY)) || {}; }
    catch(e) { return {}; }
  }
};

function setBasemap(key){
  if(currentBaseTile) map.removeLayer(currentBaseTile);
  const b=BASEMAPS[key]||BASEMAPS.dark;
  currentBaseTile=L.tileLayer(b.url,{attribution:b.attr,subdomains:b.sub||'',maxNativeZoom:b.maxNativeZoom||20,maxZoom:22});
  if(key==='satellite'){currentBaseTile.on('tileerror',()=>{const nz=b.maxNativeZoom||19;if(map.getZoom()>nz)map.setZoom(nz,{animate:true});});}
  currentBaseTile.addTo(map);currentBaseTile.bringToBack();
  document.querySelector('.leaflet-container').style.background=key==='dark'?'#0c0f14':'#e8e8e8';
  const sel=document.getElementById('basemapSel');if(sel)sel.value=key;
  const mobSel=document.getElementById('mobBasemap');if(mobSel)mobSel.value=key;
  if(key==='dark'||key==='bright')applyUiTheme(key==='bright');
  LS.save();
}
setBasemap('dark');

function applyUiTheme(isBright){
  document.documentElement.setAttribute('data-theme',isBright?'bright':'dark');
  const t1=document.getElementById('themeToggle');if(t1)t1.checked=isBright;
  const t2=document.getElementById('mobThemeToggle');if(t2)t2.checked=isBright;
  LS.save();
}
function mobSwitchTheme(isBright){
  applyUiTheme(isBright);
  const cur=document.getElementById('basemapSel')?.value;
  if(cur==='dark'||cur==='bright')setBasemap(isBright?'bright':'dark');
}



function recalcLayout(){
  const logo=document.getElementById('floatLogo');
  const ctrl=document.getElementById('floatControls');
  if(!logo||!ctrl)return;
  if(window.innerWidth>=769){
    // Reset dulu agar measurement akurat
    ctrl.style.left='';
    const r=logo.getBoundingClientRect();
    if(r.width>0){
      ctrl.style.top='12px';
      ctrl.style.left=(r.right+8)+'px';
    }
  } else {
    ctrl.style.left='';
    ctrl.style.top='';
  }
}

let allClients=[],selectedIds=new Set(),mapLayers=L.layerGroup().addTo(map);
const edgeCanvas=document.createElement('canvas');
edgeCanvas.style.cssText='position:absolute;top:0;left:0;pointer-events:none;';
let edgeCanvasVisible=false,_edgeRAF=null;

function _mountEdgeCanvas(){
  const container=map.getContainer();edgeCanvas.style.position='absolute';edgeCanvas.style.zIndex='400';edgeCanvas.style.pointerEvents='none';
  if(!container.contains(edgeCanvas))container.appendChild(edgeCanvas);
}
map.whenReady(_mountEdgeCanvas);

let _edgeDrawData=[],_activeDrawSiteIds=null;

function _resizeEdgeCanvas(){
  const sz=map.getSize();const pad=Math.ceil(Math.sqrt(sz.x*sz.x+sz.y*sz.y));
  const W=sz.x+pad*2,H=sz.y+pad*2;
  edgeCanvas.width=W;edgeCanvas.height=H;edgeCanvas.style.width=W+'px';edgeCanvas.style.height=H+'px';
  edgeCanvas.style.left=(-pad)+'px';edgeCanvas.style.top=(-pad)+'px';edgeCanvas._pad=pad;
}

function _drawEdges(){
  _edgeRAF=null;const ctx=edgeCanvas.getContext('2d');
  if(!edgeCanvasVisible||!_edgeDrawData.length){if(edgeCanvas.width)ctx.clearRect(0,0,edgeCanvas.width,edgeCanvas.height);return;}
  _resizeEdgeCanvas();const pad=edgeCanvas._pad||0;ctx.clearRect(0,0,edgeCanvas.width,edgeCanvas.height);
  _edgeDrawData.forEach(e=>{
    if(_activeDrawSiteIds&&!_activeDrawSiteIds.has(e.siteAId)&&!_activeDrawSiteIds.has(e.siteBId))return;
    // Respek filter wilayah: sembunyikan garis jika kedua site tidak lolos filter
    if(selectedAreas.size>0){
      const sA=sitesIndex[e.siteAId],sB=sitesIndex[e.siteBId];
      const aOk=sA&&sitePassesFilter(sA);const bOk=sB&&sitePassesFilter(sB);
      if(!aOk&&!bOk)return;
    }
    const pa=map.latLngToContainerPoint([e.lat1,e.lng1]);const pb=map.latLngToContainerPoint([e.lat2,e.lng2]);
    ctx.beginPath();ctx.moveTo(pa.x+pad,pa.y+pad);ctx.lineTo(pb.x+pad,pb.y+pad);
    ctx.strokeStyle=e.color;ctx.lineWidth=e.weight;ctx.globalAlpha=e.ghost?0.55:0.85;
    ctx.setLineDash(e.ghost?[8,5]:[8,4,2,4]);ctx.stroke();
  });
  ctx.globalAlpha=1;ctx.setLineDash([]);
}

function scheduleEdgeDraw(){if(_edgeRAF)return;_edgeRAF=requestAnimationFrame(_drawEdges);}
map.on('move zoom rotate zoomend moveend viewreset',scheduleEdgeDraw);
map.on('zoomanim',()=>{scheduleEdgeDraw();});

let edgeLayer=L.layerGroup();
let markerLayer=L.layerGroup().addTo(map);
let linkLabelsLayer=L.layerGroup();
let skeletonLayer=L.layerGroup().addTo(map);
let activeSiteId=null,openLinkId=null;
let sitesIndex={},edgesIndex={};
let allLayersData={};
map.on('click',()=>{closeDropdownAll();});
let showLinks=false;

/* ── Kompas desktop + mobile dalam 1 listener ── */
document.getElementById('compassBtn').addEventListener('click',()=>{map.setBearing(0);});
map.on('rotate',()=>{
  const bearing=map.getBearing();
  const needle=document.getElementById('compassNeedle');if(needle)needle.style.transform=`rotate(${bearing}deg)`;
  const btn=document.getElementById('compassBtn');if(btn)btn.classList.toggle('rotated',Math.abs(bearing)>0.5);
  const mobNeedle=document.getElementById('mobCompassNeedle');if(mobNeedle)mobNeedle.style.transform=`rotate(${bearing}deg)`;
  const mobBtn=document.getElementById('mobCompassBtn');if(mobBtn)mobBtn.classList.toggle('rotated',Math.abs(bearing)>0.5);
});

const CLIENT_COLORS=['#ffffff','#a78bfa','#22c55e','#f472b6','#38bdf8','#fb923c','#4ade80','#e879f9','#facc15','#94a3b8'];
let clientColorMap={};

function assignClientColor(name){
  const n=(name||'').toUpperCase();
  if(n.includes('TELEKOMUNIKASI SELULAR')||n.includes('TELKOMSEL'))return '#ff1a1a';
  if(n.includes('XLSMART'))return '#1a8cff';
  if(n.includes('INDOSAT'))return '#ffe600';
  return null;
}

const M={JAN:0,FEB:1,MAR:2,APR:3,MAY:4,JUN:5,JUL:6,AUG:7,SEP:8,OCT:9,NOV:10,DEC:11};
function expStatus(d){if(!d)return 'unknown';const p=d.split('-');if(p.length<3)return 'unknown';let yr=+p[2];if(yr<100)yr+=yr<70?2000:1900;const dt=new Date(yr,M[p[1]?.toUpperCase()]??0,+p[0]);const diff=(dt-new Date())/86400000;return diff<0?'expired':diff<180?'soon':'active';}
function fmtFreq(v){return v!=null?v+' MHz':'\u2014';}
function esc(s){if(s==null)return '';return String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
function dmsToDecimal(deg,min,sec,dir){if(deg==null||deg===''||deg===undefined)return null;const d=parseFloat(deg);if(isNaN(d))return null;const m=parseFloat(min)||0;const s=parseFloat(sec)||0;if(d===0&&m===0&&s===0)return null;let dd=d+m/60+s/3600;if(dir==='S'||dir==='W')dd=-dd;return isNaN(dd)?null:dd;}
function haversineM(lat1,lon1,lat2,lon2){const R=6371000,dl=(lat2-lat1)*Math.PI/180,dg=(lon2-lon1)*Math.PI/180;const a=Math.sin(dl/2)**2+Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dg/2)**2;return R*2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));}
function nameSimilarity(a,b){if(!a||!b)return 0;a=a.toLowerCase().replace(/[^a-z0-9]/g,'');b=b.toLowerCase().replace(/[^a-z0-9]/g,'');if(a===b)return 1;let common=0;for(let i=0;i<Math.min(a.length,b.length);i++){if(a[i]===b[i])common++;else break;}return common/Math.max(a.length,b.length);}
function addrNorm(s){return(s||'').toLowerCase().replace(/[^a-z0-9]/g,' ').replace(/\s+/g,' ').trim();}
function stnNorm(s){if(!s)return '';const parts=s.split('_');const seen=new Set();const deduped=parts.filter(p=>{const k=p.toLowerCase().replace(/\s+/g,'');if(seen.has(k))return false;seen.add(k);return true;});return addrNorm(deduped.join(' '));}
function addrWordOverlap(a,b){if(!a||!b)return 0;const wa=new Set(addrNorm(a).split(' ').filter(w=>w.length>3));const wb=new Set(addrNorm(b).split(' ').filter(w=>w.length>3));if(!wa.size||!wb.size)return 0;let overlap=0;wa.forEach(w=>{if(wb.has(w))overlap++;});return overlap/Math.min(wa.size,wb.size);}
function stnWordOverlap(a,b){if(!a||!b)return 0;const wa=new Set(stnNorm(a).split(' ').filter(w=>w.length>2));const wb=new Set(stnNorm(b).split(' ').filter(w=>w.length>2));if(!wa.size||!wb.size)return 0;let overlap=0;wa.forEach(w=>{if(wb.has(w)){overlap++;}else{for(const wb2 of wb){if(wb2.startsWith(w)||w.startsWith(wb2)){overlap+=0.7;break;}}}});return overlap/Math.min(wa.size,wb.size);}
function siteHasSimilarLawan(site,lawan){if(!lawan)return false;const la=stnNorm(lawan);return site.isr.some(r=>{if(!r.stasiun_lawan)return false;const lb=stnNorm(r.stasiun_lawan);if(la===lb)return true;return stnWordOverlap(lawan,r.stasiun_lawan)>=0.6;});}

const MAX_DIST=100;const CROSS_CLUSTER_DIST=20;

function clusterISRtoSites(rows){
  const sites=[];
  rows.forEach(isr=>{
    const lat=isr.sid_lat?parseFloat(isr.sid_lat):null;const lng=isr.sid_long?parseFloat(isr.sid_long):null;
    if(lat===null||lng===null||isNaN(lat)||isNaN(lng))return;
    let best=null,bestScore=-1;
    for(const site of sites){const dist=haversineM(lat,lng,site.lat,site.lng);if(dist>MAX_DIST)continue;let score=0;if(dist<20)score=100;else{const nsBest=site.isr.length?Math.max(...site.isr.map(r=>nameSimilarity(isr.stn_name,r.stn_name))):0;if(nsBest>=0.7)score=Math.max(score,60+Math.round(nsBest*20));const addrOverlapBest=site.isr.length?Math.max(...site.isr.map(r=>addrWordOverlap(isr.stn_addr,r.stn_addr))):0;if(addrOverlapBest>=0.5)score=Math.max(score,60+Math.round(addrOverlapBest*30));if(siteHasSimilarLawan(site,isr.stasiun_lawan))score=Math.max(score,75);if(isr.stasiun_lawan&&site.isr.some(r=>r.stasiun_lawan===isr.stasiun_lawan))score=Math.max(score,80);}if(score>bestScore){bestScore=score;best=site;}}
    if(best&&bestScore>0){best.isr.push(isr);const n=best.isr.length;best.lat=(best.lat*(n-1)+lat)/n;best.lng=(best.lng*(n-1)+lng)/n;}
    else{sites.push({id:'S'+sites.length,lat,lng,label:isr.stn_addr?isr.stn_addr.replace(/,.*$/,'').trim().substring(0,30):(isr.stn_name||'Site'),isr:[isr],ghost:false,marker:null});}
  });
  const ghostSites=[];
  rows.forEach(isr=>{
    const toLat=dmsToDecimal(isr.to_lat_deg,isr.to_lat_min,isr.to_lat_sec,isr.to_lat_dir_ind);const toLng=dmsToDecimal(isr.to_long_deg,isr.to_long_min,isr.to_long_sec,isr.to_long_dir_ind);
    if(toLat===null||toLng===null)return;
    const nearSite=sites.find(t=>haversineM(toLat,toLng,t.lat,t.lng)<MAX_DIST);if(nearSite)return;
    if(isr.stasiun_lawan){const lawanNorm=stnNorm(isr.stasiun_lawan);const nameMatch=sites.find(t=>{if(t.isr.some(r=>r.id===isr.id))return false;return t.isr.some(r=>{if(!r.stn_name)return false;if(stnNorm(r.stn_name)===lawanNorm)return true;return stnWordOverlap(r.stn_name,isr.stasiun_lawan)>=0.6;});});if(nameMatch)return;}
    const existGhost=ghostSites.find(t=>haversineM(toLat,toLng,t.lat,t.lng)<MAX_DIST);
    if(existGhost){if(!existGhost.isr.some(r=>r._ghost_ref_simf===isr.no_simf&&r._ghost_ref_lawan===isr.stasiun_lawan))existGhost.isr.push({stn_name:isr.stasiun_lawan,_is_ghost:true,_ghost_ref_simf:isr.no_simf,_ghost_ref_lawan:isr.stasiun_lawan,parent_isr:isr});return;}
    ghostSites.push({id:'G'+ghostSites.length,lat:toLat,lng:toLng,label:isr.stasiun_lawan||'Ghost Site',isr:[{stn_name:isr.stasiun_lawan,_is_ghost:true,_ghost_ref_simf:isr.no_simf,_ghost_ref_lawan:isr.stasiun_lawan,parent_isr:isr}],ghost:true,marker:null});
  });
  return [...sites,...ghostSites];
}

function buildMWLinks(sites,rows){
  const edges=new Map();const isrToSite=new Map();
  sites.filter(s=>!s.ghost).forEach(s=>s.isr.forEach(r=>isrToSite.set(r.id,s)));
  rows.forEach(isr=>{
    const sA=isrToSite.get(isr.id);if(!sA)return;let sB=null,method='solo';
    if(isr.link_id!==null){
      for(const s of sites){if(s.id===sA.id||s.ghost)continue;if(s.isr.some(r=>r.link_id==isr.link_id)){sB=s;method='link_id';break;}}
      if(!sB){for(const s of sites){if(s.id===sA.id||!s.ghost)continue;if(s.isr.some(r=>r.parent_isr&&r.parent_isr.link_id==isr.link_id)){sB=s;method='link_id';break;}}}
      if(!sB){const toLat=dmsToDecimal(isr.to_lat_deg,isr.to_lat_min,isr.to_lat_sec,isr.to_lat_dir_ind);const toLng=dmsToDecimal(isr.to_long_deg,isr.to_long_min,isr.to_long_sec,isr.to_long_dir_ind);
        if(toLat!==null&&toLng!==null){
          for(const s of sites){if(s.id===sA.id||!s.ghost)continue;if(haversineM(toLat,toLng,s.lat,s.lng)<MAX_DIST){sB=s;method='ghost_coord';break;}}
          if(!sB){for(const s of sites){if(s.id===sA.id||s.ghost)continue;if(haversineM(toLat,toLng,s.lat,s.lng)<MAX_DIST){sB=s;method='coord_real';break;}}}
          if(!sB&&isr.stasiun_lawan){const lawanNorm=stnNorm(isr.stasiun_lawan);for(const s of sites){if(s.id===sA.id)continue;if(s.isr.some(r=>r.stn_name&&(stnNorm(r.stn_name)===lawanNorm||stnWordOverlap(r.stn_name,isr.stasiun_lawan)>=0.6))){sB=s;method='name_fallback';break;}}}
        }
      }
    }
    if(!sB)return;
    const edgeKey=[sA.id,sB.id].sort().join('|');const isInferred=method==='ghost_coord'||method==='coord_real'||method==='name_fallback';
    if(!edges.has(edgeKey))edges.set(edgeKey,{id:edgeKey,siteA:sA.id<sB.id?sA:sB,siteB:sA.id<sB.id?sB:sA,mwlinks:[],ghost:sB.ghost||isInferred,line:null});
    const e=edges.get(edgeKey);
    if(!e.mwlinks.find(m=>m.link_id==isr.link_id&&m.link_id!==null)){e.mwlinks.push({link_id:isr.link_id,freq_tx:isr.freq,freq_rx:isr.freq_pair,no_simf:isr.no_simf,appl_id:isr.appl_id,validity:isr.validity_date,circuit_len:isr.circuit_len,azim:isr.azim,eq_mfr:isr.eq_mfr,eq_mdl:isr.eq_mdl,stn_name:isr.stn_name,stasiun_lawan:isr.stasiun_lawan,trans_type:isr.trans_type,h_asl:isr.h_asl,hgt_ant:isr.hgt_ant,erp:isr.erp_pwr_dbm,status_simf:isr.status_simf,licence_date:isr.licence_date,appl_date:isr.appl_date,umur_isr:isr.umur_isr,city:isr.city,province:isr.province,method,_isr_id:isr.id,_src_site_id:sA.id});}
  });
  const edgeArr=[...edges.values()];
  edgeArr.forEach(e=>{const cnt=e.mwlinks.length;e.siteA.linkCount=(e.siteA.linkCount||0)+cnt;e.siteB.linkCount=(e.siteB.linkCount||0)+cnt;});
  return edgeArr;
}

function mkSiteIcon(count,ghost,active,color){
  color=color||'#00e5ff';
  if(ghost){const op=active?0.25:0.12;const sw=active?2:1.5;return L.divIcon({html:`<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><polygon points="12,2 21,7 21,17 12,22 3,17 3,7" fill="${color}" fill-opacity="${op}" stroke="${color}" stroke-width="${sw}" stroke-dasharray="4 2.5" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.5" fill="${color}" fill-opacity=".6"/></svg>`,className:'',iconSize:[24,24],iconAnchor:[12,12],tooltipAnchor:[13,0]});}
  const label=count>99?'99+':String(count);const sz=count>9?36:count>3?30:26;const fs=count>99?7:count>9?9:11;const r=sz/2;const ir=r-5;
  const pts6=(R)=>{let pts='';for(let i=0;i<6;i++){const a=(i*60-90)*Math.PI/180;pts+=(r+R*Math.cos(a)).toFixed(2)+','+(r+R*Math.sin(a)).toFixed(2)+' ';}return pts.trim();};
  const glowOp=active?0.4:0.15;const fillOp=active?0.95:0.88;const shadow=active?`<polygon points="${pts6(r-0.5)}" fill="${color}" fill-opacity="0.18" stroke="none"/>`:'' ;
  return L.divIcon({html:`<svg xmlns="http://www.w3.org/2000/svg" width="${sz}" height="${sz}" viewBox="0 0 ${sz} ${sz}">${shadow}<polygon points="${pts6(r-1.5)}" fill="${color}" fill-opacity="${glowOp}" stroke="${color}" stroke-width="${active?2:1.2}" stroke-linejoin="round"/><polygon points="${pts6(ir)}" fill="${color}" fill-opacity="${fillOp}" stroke="none"/><text x="${r}" y="${r+fs*0.35}" text-anchor="middle" font-size="${fs}" fill="#000" font-weight="800" font-family="monospace" letter-spacing="-0.5">${label}</text></svg>`,className:'',iconSize:[sz,sz],iconAnchor:[r,r],tooltipAnchor:[r+2,0]});
}

function mkPieIcon(count,colors,active){
  const label=count>99?'99+':String(count);const sz=count>9?36:count>3?30:26;const R=sz/2,fs=count>99?7:count>9?9:11;const n=colors.length;
  const hex=(r)=>{let p='';for(let i=0;i<6;i++){const a=(i*60-90)*Math.PI/180;p+=(R+r*Math.cos(a)).toFixed(2)+','+(R+r*Math.sin(a)).toFixed(2)+' ';}return p.trim();};
  const outerR=R-1.5,innerR=Math.max(R-6,5);let paths='';
  if(n===1){paths=`<polygon points="${hex(outerR)}" fill="${colors[0]}" fill-opacity="${active?.4:.18}" stroke="${colors[0]}" stroke-width="${active?2:1.2}" stroke-linejoin="round"/>`;paths+=`<polygon points="${hex(innerR)}" fill="${colors[0]}" fill-opacity="${active?.92:.85}" stroke="none"/>`;}
  else{const cid='cp'+Math.random().toString(36).slice(2,7);paths+=`<defs><clipPath id="${cid}"><polygon points="${hex(outerR)}"/></clipPath></defs>`;const slice=2*Math.PI/n;for(let i=0;i<n;i++){const a1=i*slice-Math.PI/2,a2=(i+1)*slice-Math.PI/2;const x1=R+outerR*Math.cos(a1),y1=R+outerR*Math.sin(a1);const x2=R+outerR*Math.cos(a2),y2=R+outerR*Math.sin(a2);const large=slice>Math.PI?1:0;paths+=`<path d="M${R},${R} L${x1.toFixed(2)},${y1.toFixed(2)} A${outerR},${outerR} 0 ${large},1 ${x2.toFixed(2)},${y2.toFixed(2)} Z" fill="${colors[i]}" clip-path="url(#${cid})"/>`;}paths+=`<polygon points="${hex(outerR)}" fill="none" stroke="#00000033" stroke-width="0.5" stroke-linejoin="round"/>`;paths+=`<polygon points="${hex(innerR)}" fill="#141920" stroke="none"/>`;}
  const textFill=n===1?'#000':'#fff';
  return L.divIcon({html:`<svg xmlns="http://www.w3.org/2000/svg" width="${sz}" height="${sz}" viewBox="0 0 ${sz} ${sz}">${paths}<text x="${R}" y="${R+fs*0.35}" text-anchor="middle" font-size="${fs}" fill="${textFill}" font-weight="800" font-family="monospace" letter-spacing="-0.5">${label}</text></svg>`,className:'',iconSize:[sz,sz],iconAnchor:[R,R],tooltipAnchor:[R+2,0]});
}

function applySharedSiteIcons(activeIds){
  const allSites=[];
  activeIds.forEach(clntId=>{const data=allLayersData[clntId];if(!data)return;data.sites.forEach(site=>{if(!site.marker||site.ghost)return;allSites.push({clntId,site});});});
  const clusters=[];
  allSites.forEach(entry=>{let target=null;for(const c of clusters){if(haversineM(entry.site.lat,entry.site.lng,c.lat,c.lng)<CROSS_CLUSTER_DIST){target=c;break;}}if(target){target.entries.push(entry);const n=target.entries.length;target.lat=(target.lat*(n-1)+entry.site.lat)/n;target.lng=(target.lng*(n-1)+entry.site.lng)/n;}else{clusters.push({lat:entry.site.lat,lng:entry.site.lng,entries:[entry]});}});
  clusters.forEach(c=>{const entries=c.entries;if(entries.length<2)return;const colors=[...new Set(entries.map(e=>clientColorMap[e.clntId]||'#ffffff'))];const totalLinks=entries.reduce((s,e)=>s+(e.site.linkCount||e.site.isr.length),0);const isActive=entries.some(e=>e.site.id===activeSiteId);const icon=mkPieIcon(totalLinks,colors,isActive);entries[0].site.marker.setIcon(icon);entries[0].site._sharedWith=entries.slice(1).map(e=>e.site.id);for(let i=1;i<entries.length;i++){if(markerLayer.hasLayer(entries[i].site.marker))markerLayer.removeLayer(entries[i].site.marker);entries[i].site._hiddenByShared=entries[0].site.id;}});
}

let currentSites=[],currentEdges=[];

function renderAllVisible(skipFit=false){
  Object.values(allLayersData).forEach(data=>{data.sites.forEach(s=>{s.marker=null;});data.edges.forEach(e=>{e.line=null;e.hitbox=null;e.labelMarker=null;});});
  mapLayers.clearLayers();edgeLayer.clearLayers();_edgeDrawData=[];markerLayer.clearLayers();linkLabelsLayer.clearLayers();skeletonLayer.clearLayers();sitesIndex={};edgesIndex={};currentSites=[];currentEdges=[];
  const overlay=document.getElementById('stateOverlay');const activeIds=[...selectedIds];
  if(!activeIds.length){overlay.style.display='flex';overlay.innerHTML='<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg><span>Pilih klien untuk melihat peta</span>';updateFooter();return;}
  overlay.style.display='none';const bounds=[];
  activeIds.forEach(clntId=>{
    const data=allLayersData[clntId];if(!data)return;const color=clientColorMap[clntId]||'#00e5ff';
    data.sites.forEach(site=>{const m=L.marker([site.lat,site.lng],{icon:mkSiteIcon(site.linkCount||site.isr.length,site.ghost,false,color)});const clntName=(()=>{const c=allClients.find(x=>x.clnt_id==clntId);return c?c.clnt_name.split(',')[0].split(' ').slice(0,3).join(' '):''})();
      const tipText=site.ghost?`<b>${esc(site.label)}</b><br><span style="color:${color}">Ghost site</span>`:`<b>${esc(site.label)}</b><br><span style="color:${color};font-size:10px">${esc(clntName)}</span><br>${site.isr.length} ISR &middot; ${site.linkCount||0} MW Link`;m.bindTooltip(tipText,{className:'lf-tooltip',direction:'right',offset:[12,0]});m.on('click',(ev)=>{L.DomEvent.stopPropagation(ev);selectSite(site.id);});site.marker=m;sitesIndex[site.id]=site;bounds.push([site.lat,site.lng]);});
    data.edges.forEach(e=>{const a=e.siteA,b=e.siteB;const lineColor=e.ghost?color+'88':color+'cc';_edgeDrawData.push({color:lineColor,weight:e.ghost?2:3,ghost:e.ghost,lat1:a.lat,lng1:a.lng,lat2:b.lat,lng2:b.lng,siteAId:a.id,siteBId:b.id});const hitbox=L.polyline([[a.lat,a.lng],[b.lat,b.lng]],{color:'transparent',weight:20,opacity:0.001,interactive:true,bubblingMouseEvents:false}).addTo(edgeLayer);hitbox.bindTooltip(`<b>${e.mwlinks.length} MWLink</b><br>${esc(a.label)} \u2194 ${esc(b.label)}`,{className:'lf-tooltip',direction:'top',sticky:true});hitbox.on('click',(ev)=>{L.DomEvent.stopPropagation(ev);selectSite(a.id);});e.line=null;e.hitbox=hitbox;edgesIndex[e.id]=e;const midLat=(a.lat+b.lat)/2,midLng=(a.lng+b.lng)/2;const lbl=L.marker([midLat,midLng],{icon:L.divIcon({html:`<div class="link-count-label">${e.mwlinks.length}</div>`,className:'',iconSize:[20,16],iconAnchor:[10,8]}),interactive:false,zIndexOffset:-100}).addTo(linkLabelsLayer);e.labelMarker=lbl;});
    currentSites.push(...data.sites);currentEdges.push(...data.edges);
  });
  if(bounds.length&&!skipFit)map.fitBounds(bounds,{padding:[50,50],maxZoom:14});
  refreshMarkers();applySharedSiteIcons([...selectedIds]);requestAnimationFrame(()=>updateLinkLabels(null));buildAreaFilterList();
  edgeCanvasVisible=showLinks;scheduleEdgeDraw();updateFooter();
}

function refreshMarkers(){
  if(!currentSites.length)return;const bounds=map.getBounds().pad(0.15);markerLayer.clearLayers();
  currentSites.forEach(site=>{if(!site.marker)return;if(!sitePassesFilter(site))return;if(bounds.contains([site.lat,site.lng])){const clntId=findClientOfSite(site.id);const color=clientColorMap[clntId]||'#00e5ff';site.marker.setIcon(mkSiteIcon(site.linkCount||site.isr.length,site.ghost,site.id===activeSiteId,color));markerLayer.addLayer(site.marker);}});
  applySharedSiteIcons([...selectedIds]);
}
map.on('moveend zoomend',refreshMarkers);

function showSkeletonForClient(clntId){skeletonLayer.clearLayers();const color=clientColorMap[clntId]||'#00e5ff';const bounds=map.getBounds();const sw=bounds.getSouthWest(),ne=bounds.getNorthEast();const latSpan=ne.lat-sw.lat,lngSpan=ne.lng-sw.lng;const positions=[[sw.lat+latSpan*.25,sw.lng+lngSpan*.25],[sw.lat+latSpan*.25,sw.lng+lngSpan*.65],[sw.lat+latSpan*.5,sw.lng+lngSpan*.45],[sw.lat+latSpan*.65,sw.lng+lngSpan*.2],[sw.lat+latSpan*.7,sw.lng+lngSpan*.7],[sw.lat+latSpan*.4,sw.lng+lngSpan*.8]];positions.forEach(([lat,lng])=>{const icon=L.divIcon({html:`<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" class="hex-skeleton"><polygon points="13,2 22,7.5 22,18.5 13,24 4,18.5 4,7.5" fill="${color}" fill-opacity=".15" stroke="${color}" stroke-width="1.5" stroke-dasharray="4 2.5" stroke-linejoin="round"/></svg>`,className:'',iconSize:[26,26],iconAnchor:[13,13]});L.marker([lat,lng],{icon,interactive:false,zIndexOffset:-500}).addTo(skeletonLayer);});}
function clearSkeleton(){skeletonLayer.clearLayers();}

/* Hitung statistik aktif/segera-habis/expired dari semua MW link milik 1 klien */
function getClientExpiryStats(clntId){
  const data = allLayersData[clntId];
  if(!data || !data.edges) return null;
  let active=0, soon=0, expired=0, unknown=0;
  data.edges.forEach(e=>{
    e.mwlinks.forEach(mw=>{
      const s = expStatus(mw.validity);
      if(s==='active') active++;
      else if(s==='soon') soon++;
      else if(s==='expired') expired++;
      else unknown++;
    });
  });
  const total = active+soon+expired+unknown;
  if(total===0) return null;
  return {active,soon,expired,unknown,total};
}

function renderStatBar(stats){
  if(!stats || stats.total===0) return '';
  const pct = (n)=> (n/stats.total*100).toFixed(1);
  const segs = [
    stats.active  ? `<div class="leg-statbar-seg active"  style="width:${pct(stats.active)}%"  title="${stats.active} Aktif"></div>`  : '',
    stats.soon    ? `<div class="leg-statbar-seg soon"    style="width:${pct(stats.soon)}%"    title="${stats.soon} Segera Habis"></div>` : '',
    stats.expired ? `<div class="leg-statbar-seg expired" style="width:${pct(stats.expired)}%" title="${stats.expired} Kadaluarsa"></div>` : '',
  ].join('');
  return `<div class="leg-statbar">${segs}</div><span class="leg-stat-count">${stats.active}/${stats.total}</span>`;
}

function updateLegend(){
  const activeIds=[...selectedIds];

  // Renderer bersama untuk semua tempat legend muncul.
  // Nama lengkap disimpan di title="" untuk tooltip saat nama dipotong ellipsis.
  const renderRows = (rowClass, dotClass) => activeIds.map(id=>{
    const c=allClients.find(x=>x.clnt_id==id);
    const col=clientColorMap[id]||'#00e5ff';
    const fullName=c?c.clnt_name.split(',')[0]:String(id);
    const shortName=c?c.clnt_name.split(',')[0].split(' ').slice(0,2).join(' '):id;
    const stats=getClientExpiryStats(id);
    return `<div class="${rowClass}"><div class="${dotClass}" style="background:${col}"></div><span class="leg-name" title="${esc(fullName)}">${esc(shortName)}</span>${renderStatBar(stats)}</div>`;
  }).join('');

  // 1) Legend mobile (bottom-sheet, toggle via tombol Legenda)
  const legendInner=document.getElementById('legendInner');
  if(legendInner) legendInner.innerHTML = renderRows('leg-row','leg-dot');

  // 2) Legend drawer mobile (di dalam menu hamburger)
  const mobWrap=document.getElementById('mobDrawerLegend');
  const mobInner=document.getElementById('mobLegendItems');
  if(mobWrap&&mobInner){
    mobWrap.style.display=activeIds.length?'':'none';
    mobInner.innerHTML = renderRows('mob-legend-row','mob-legend-dot');
  }

  // 3) Legend desktop: panel selalu tampil kiri bawah saat ada klien aktif
  const dskWrap=document.getElementById('desktopLegend');
  const dskInner=document.getElementById('desktopLegendInner');
  const dskCount=document.getElementById('desktopLegendCount');
  if(dskWrap&&dskInner){
    dskWrap.classList.toggle('show', activeIds.length>0);
    dskInner.innerHTML = renderRows('leg-row','leg-dot');
    if(dskCount) dskCount.textContent = activeIds.length + ' klien';
  }

  // 4) Filter wilayah drawer mobile: tampil kalau ada klien aktif
  const mobFilterWrap = document.getElementById('mobDrawerFilter');
  if(mobFilterWrap) mobFilterWrap.style.display = activeIds.length ? '' : 'none';
  if(activeIds.length) buildMobFilterList();
}

function updateLinkLabels(activeSiteId){
  currentEdges.forEach(e=>{if(!e.labelMarker)return;const isConn=activeSiteId&&(e.siteA.id===activeSiteId||e.siteB.id===activeSiteId);const noSelection=!activeSiteId;const el=e.labelMarker.getElement()?.querySelector('.link-count-label');if(el){let op=0;if(showLinks)op=noSelection||isConn?1:.25;else if(isConn)op=1;el.style.opacity=op;el.style.fontWeight=isConn?'700':'400';el.style.borderColor=isConn?'currentColor':'rgba(255,255,255,.2)';}});
}

function selectSite(siteId,fromGoto=false){
  if(typeof mobCloseSheet==='function')mobCloseSheet();
  currentSites.forEach(s=>{if(s.marker){const clntId=findClientOfSite(s.id);const color=clientColorMap[clntId]||'#00e5ff';s.marker.setIcon(mkSiteIcon(s.linkCount||s.isr.length,s.ghost,s.id===siteId,color));}});
  activeSiteId=siteId;openLinkId=null;const site=sitesIndex[siteId];if(!site){closePanel();return;}
  const sharedSiteIds=new Set([siteId]);if(!site.ghost){findAllClientsOfCoord(site).forEach(({site:cs})=>sharedSiteIds.add(cs.id));}
  const allEdgesHere=[];for(const [clntId,data] of Object.entries(allLayersData)){if(!selectedIds.has(+clntId))continue;data.edges.forEach(e=>{if(sharedSiteIds.has(e.siteA.id)||sharedSiteIds.has(e.siteB.id))allEdgesHere.push({e,clntId:+clntId});});}
  const connectedEdgeIds=new Set(allEdgesHere.map(({e})=>e.id));
  currentEdges.forEach(e=>{const isConnected=connectedEdgeIds.has(e.id);if(e.line){const clntId=findClientOfSite(e.siteA.id)||findClientOfSite(e.siteB.id);const color=clientColorMap[clntId]||'#00e5ff';e.line.setStyle({color:e.ghost?color+'88':color+'cc',opacity:isConnected?1:showLinks?0.15:0,weight:isConnected?4:e.ghost?2:3,dashArray:e.ghost?'8 5':'8 4 2 4'});}});
  if(!map.hasLayer(edgeLayer))edgeLayer.addTo(map);
  _activeDrawSiteIds=sharedSiteIds;edgeCanvasVisible=true;scheduleEdgeDraw();
  if(!map.hasLayer(linkLabelsLayer))linkLabelsLayer.addTo(map);
  updateLinkLabels(siteId);if(fromGoto)map.panTo([site.lat,site.lng],{animate:true});
  const pb=document.getElementById('panelBody');pb.innerHTML='';pb.classList.remove('anim');requestAnimationFrame(()=>pb.classList.add('anim'));showSitePanel(site);
}

function findClientOfSite(siteId){for(const [clntId,data] of Object.entries(allLayersData)){if(!selectedIds.has(+clntId))continue;if(data.sites.find(s=>s.id===siteId))return +clntId;}for(const [clntId,data] of Object.entries(allLayersData)){if(data.sites.find(s=>s.id===siteId))return +clntId;}return null;}
function findAllClientsOfCoord(site){const result=[];for(const [clntId,data] of Object.entries(allLayersData)){if(!selectedIds.has(+clntId))continue;const match=data.sites.find(s=>!s.ghost&&haversineM(s.lat,s.lng,site.lat,site.lng)<CROSS_CLUSTER_DIST);if(match)result.push({clntId:+clntId,site:match});}return result;}

function showSitePanel(site){
  const clntId=findClientOfSite(site.id);const siteColor=clientColorMap[clntId]||'#00e5ff';
  document.getElementById('panelTitle').textContent=site.label;document.getElementById('panelBreadcrumb').style.display='none';
  document.getElementById('detailPanel').classList.add('open');document.querySelector('.app').classList.add('panel-open');document.getElementById('panelBody').scrollTop=0;
  if(site.ghost){
    document.getElementById('panelTabs').style.display='none';const refs=site.isr.filter(r=>r._is_ghost);
    document.getElementById('panelSub').textContent=`Ghost site \u00B7 ${refs.length} link masuk`;
    document.getElementById('panelBody').innerHTML=`<p style="color:var(--muted);font-size:12px;margin-bottom:12px">Koordinat dari kolom <code style="color:var(--accent3)">to_lat/to_long</code> \u2014 site ini tidak ada di database klien ini.</p>${refs.map(r=>{const p=r.parent_isr;return `<div class="lawan-group"><div class="lawan-header-static"><div class="lawan-dot" style="background:${siteColor}"></div><div class="lawan-name">${esc(p?.stn_name)||'\u2014'}</div><span class="lawan-count">Link ID: ${esc(p?.link_id)||'\u2014'}</span></div><div class="lawan-links"><div class="link-row"><div class="isr-pair-grid"><div class="isr-half"><div class="isr-half-label">ISR lawan (sumber)</div><div class="isr-half-stn">${esc(p?.stn_name)||'\u2014'}</div><div class="isr-half-simf">${esc(p?.no_simf)||'\u2014'}</div><div class="isr-half-freq"><span class="pill-tx">TX</span> ${fmtFreq(p?.freq)}</div></div><div class="isr-half isr-half-ghost"><div class="isr-half-label">ISR ini (ghost)</div><div class="isr-half-stn" style="color:var(--muted)">\u2014</div><div class="isr-half-freq" style="color:var(--muted)">Tidak ada di DB</div></div></div></div></div></div>`;}).join('')}`;
    requestAnimationFrame(()=>{updatePanelHeight();setTimeout(updatePanelHeight,300);});return;
  }
  const sharedClients=findAllClientsOfCoord(site);const showTabs=sharedClients.length>1;const tabsEl=document.getElementById('panelTabs');tabsEl.style.display=showTabs?'flex':'none';
  let totalEdges=0,totalLinks=0;sharedClients.forEach(({clntId:cid,site:cs})=>{const edges=currentEdges.filter(e=>(e.siteA.id===cs.id||e.siteB.id===cs.id));totalEdges+=edges.length;totalLinks+=edges.reduce((s,e)=>s+e.mwlinks.length,0);});
  document.getElementById('panelSub').textContent=`${totalEdges} node lawan \u00B7 ${totalLinks} MW link`;
  if(showTabs){tabsEl.innerHTML=sharedClients.map(({clntId:cid},ti)=>{const c=allClients.find(x=>x.clnt_id==cid);const col=clientColorMap[cid]||'#00e5ff';const name=c?c.clnt_name.split(',')[0].split(' ').slice(0,3).join(' '):cid;return `<div class="panel-tab${ti===0?' active':''}" id="ptab-${cid}" style="--tab-color:${col}" onclick="switchPanelTab(${cid})"><span class="panel-tab-dot"></span>${esc(name)}</div>`;}).join('');}
  const bodyEl=document.getElementById('panelBody');
  bodyEl.innerHTML=sharedClients.map(({clntId:cid,site:cs},ti)=>{
    const col=clientColorMap[cid]||'#00e5ff';
    const myEdges=allLayersData[cid]?allLayersData[cid].edges.filter(e=>e.siteA.id===cs.id||e.siteB.id===cs.id):currentEdges.filter(e=>e.siteA.id===cs.id||e.siteB.id===cs.id);
    const groupsHtml=myEdges.map((e,ei)=>{
      const partner=e.siteA.id===cs.id?e.siteB:e.siteA;const partnerClntId=findClientOfSite(partner.id);const partnerColor=clientColorMap[partnerClntId]||col;const gid=`pg-${cid}-${ei}`;
      const linksHtml=e.mwlinks.map((mw,idx)=>{
        const mid=`${e.id}_${idx}_${cid}`.replace(/\|/g,'__');const safeEid=e.id.replace(/\|/g,'__');
        const srcSite=mw._src_site_id===cs.id?cs:partner;const otherSite=srcSite===cs?partner:cs;
        const otherIsr=otherSite.isr.find(r=>r.link_id==mw.link_id)||otherSite.isr.find(r=>r._is_ghost&&r.parent_isr?.link_id==mw.link_id);
        const srcData={simf:mw.no_simf||'\u2014',freqTx:fmtFreq(mw.freq_tx),freqRx:fmtFreq(mw.freq_rx),stn:mw.stn_name||srcSite.label};
        const otherData=(otherIsr&&!otherIsr._is_ghost)?{simf:otherIsr.no_simf||'\u2014',freqTx:fmtFreq(otherIsr.freq),freqRx:fmtFreq(otherIsr.freq_pair),stn:otherIsr.stn_name||otherSite.label}:{simf:mw.stasiun_lawan||'\u2014',freqTx:fmtFreq(mw.freq_rx),freqRx:fmtFreq(mw.freq_tx),stn:otherSite.label};
        const asalData=srcSite===cs?srcData:otherData;const lawanData=srcSite===cs?otherData:srcData;
        const statusSimf=mw.status_simf||(mw.validity?({active:'AKTIF',expired:'KADALUARSA',soon:'SEGERA HABIS',unknown:'\u2014'}[expStatus(mw.validity)]):'\u2014');
        const statusCls={AKTIF:'good','KADALUARSA':'danger','SEGERA HABIS':'warn'}[statusSimf]||'';
        const infoBar=`<div class="link-info-bar"><div class="info-chip"><span class="info-chip-label">Azimuth</span><span class="info-chip-val">${mw.azim!=null?mw.azim+'\u00B0':'\u2014'}</span></div><div class="info-chip"><span class="info-chip-label">Jarak</span><span class="info-chip-val">${(cs.lat&&cs.lng&&partner.lat&&partner.lng)?((haversineM(cs.lat,cs.lng,partner.lat,partner.lng)/1000).toFixed(2)+' km'):'\u2014'}</span></div><div class="info-chip"><span class="info-chip-label">Status ISR</span><span class="info-chip-val ${statusCls}">${esc(statusSimf)}</span></div><div class="info-chip"><span class="info-chip-label">Berlaku s.d.</span><span class="info-chip-val ${expStatus(mw.validity)==='expired'?'danger':expStatus(mw.validity)==='soon'?'warn':''}">${esc(mw.validity)||'\u2014'}</span></div></div>`;
        const antSpecs=`<div class="ant-specs"><div class="ant-half"><div class="ant-half-label">Antena</div><div class="ant-row"><span class="ant-key">H-ASL</span><span class="ant-val">${mw.h_asl!=null?mw.h_asl+' m':'\u2014'}</span></div><div class="ant-row"><span class="ant-key">Tinggi ant.</span><span class="ant-val">${mw.hgt_ant!=null?mw.hgt_ant+' m':'\u2014'}</span></div><div class="ant-row"><span class="ant-key">ERP</span><span class="ant-val">${mw.erp!=null?mw.erp+' dBm':'\u2014'}</span></div></div><div class="ant-half"><div class="ant-half-label">Perangkat</div><div class="ant-row"><span class="ant-key">Mfr</span><span class="ant-val">${esc(mw.eq_mfr)||'\u2014'}</span></div><div class="ant-row"><span class="ant-key">Model</span><span class="ant-val">${esc(mw.eq_mdl)||'\u2014'}</span></div><div class="ant-row"><span class="ant-key">Trans</span><span class="ant-val">${esc(mw.trans_type)||'\u2014'}</span></div></div></div>`;
        const extraGrid=[mw.licence_date?`<span class="k">Tgl. Lisensi</span><span class="v">${esc(mw.licence_date)}</span>`:'',mw.appl_date?`<span class="k">Tgl. Daftar</span><span class="v">${esc(mw.appl_date)}</span>`:'',mw.umur_isr?`<span class="k">Umur ISR</span><span class="v">${esc(mw.umur_isr)}</span>`:'',(mw.city||mw.province)?`<span class="k">Lokasi</span><span class="v">${esc([mw.city,mw.province].filter(Boolean).join(', '))}</span>`:''].filter(Boolean).join('');
        return `<div class="link-row" id="mwi-${mid}"><div class="link-id-row" onclick="toggleMWLink('${mid}','${safeEid}','${partner.id}')"><span class="link-badge" style="color:${col};background:${col}18;border-color:${col}40">LINK ID \u00B7 ${esc(mw.link_id)||'\u2014'}</span><i class="mw-chevron" id="mwc-${mid}" style="margin-left:auto;color:var(--muted);font-size:13px">&#9660;</i></div><div class="isr-pair-grid"><div class="isr-half"><div class="isr-half-label">Stasiun asal</div><div class="isr-half-stn">${esc(asalData.stn)}</div><div class="isr-half-simf">${esc(asalData.simf)}<button class="copy-btn" data-copy="${asalData.simf}" title="Salin SIMF">⎘</button></div><div class="isr-half-freq"><span class="pill-tx">TX</span> ${asalData.freqTx}</div><div class="isr-half-freq"><span class="pill-rx">RX</span> ${asalData.freqRx}</div></div><div class="isr-half"><div class="isr-half-label">Stasiun lawan</div><div class="isr-half-stn">${esc(lawanData.stn)}</div><div class="isr-half-simf">${esc(lawanData.simf)}<button class="copy-btn" data-copy="${lawanData.simf}" title="Salin SIMF">⎘</button></div><div class="isr-half-freq"><span class="pill-tx">TX</span> ${lawanData.freqTx}</div><div class="isr-half-freq"><span class="pill-rx">RX</span> ${lawanData.freqRx}</div></div></div>${infoBar}${antSpecs}${extraGrid?`<div class="mw-extra-grid">${extraGrid}</div>`:''}<div class="mwlink-detail" id="mwd-${mid}">${!e.ghost?`<button class="goto-btn" onclick="gotoSite('${partner.id}')">\u2192 Lihat site lawan: ${esc(partner.label)}</button>`:''}</div></div>`;
      }).join('');
      const isFirst=ei===0;return `<div class="lawan-group" id="${gid}"><div class="lawan-header" onclick="toggleLawanGroup('${gid}')"><div class="lawan-dot ${e.ghost?'ghost':''}" style="background:${partnerColor}"></div><div class="lawan-name">${esc(partner.label)}</div><span class="lawan-count">${e.mwlinks.length} link</span><span class="lawan-chevron${isFirst?' open':''}" >&#9660;</span></div><div class="lawan-links" id="${gid}-links" style="display:${isFirst?'flex':'none'}">${linksHtml}</div></div>`;
    }).join('');
    return `<div class="panel-tab-pane${ti===0?' active':''}" id="ppane-${cid}" data-clnt="${cid}">${groupsHtml||`<p style="color:var(--muted);font-size:12px;padding:8px 0">Tidak ada MW link.</p>`}</div>`;
  }).join('');
  requestAnimationFrame(()=>{updatePanelHeight();setTimeout(updatePanelHeight,300);});
}

function switchPanelTab(cid){document.querySelectorAll('.panel-tab').forEach(t=>t.classList.remove('active'));document.querySelectorAll('.panel-tab-pane').forEach(p=>p.classList.remove('active'));const tab=document.getElementById('ptab-'+cid);const pane=document.getElementById('ppane-'+cid);if(tab)tab.classList.add('active');if(pane)pane.classList.add('active');document.getElementById('panelBody').scrollTop=0;}
function toggleLawanGroup(gid){const links=document.getElementById(gid+'-links');const chv=document.querySelector(`#${gid} .lawan-chevron`);if(!links)return;const open=links.style.display!=='none'&&links.style.display!=='';links.style.display=open?'none':'flex';if(chv)chv.classList.toggle('open',!open);}
function toggleMWLink(mid,safeEid,partnerId){const realEid=safeEid.replace(/__/g,'|');const det=document.getElementById('mwd-'+mid);const chv=document.getElementById('mwc-'+mid);if(!det)return;const wasOpen=det.classList.contains('open');document.querySelectorAll('.mwlink-detail.open').forEach(d=>d.classList.remove('open'));document.querySelectorAll('.mw-chevron.open').forEach(c=>c.classList.remove('open'));currentEdges.forEach(e=>{if(e.line){const clntId=findClientOfSite(e.siteA.id)||findClientOfSite(e.siteB.id);const color=clientColorMap[clntId]||'#00e5ff';e.line.setStyle({color:e.id===realEid?(e.ghost?color+'ee':color):(e.ghost?color+'44':color+'44'),weight:e.id===realEid?5:e.ghost?2:3,dashArray:e.ghost?'8 5':'8 4 2 4'});}});if(!wasOpen){det.classList.add('open');if(chv)chv.classList.add('open');openLinkId=mid;currentSites.forEach(s=>{if(s.marker){const clntId=findClientOfSite(s.id);const color=clientColorMap[clntId]||'#00e5ff';s.marker.setIcon(mkSiteIcon(s.linkCount||s.isr.length,s.ghost,s.id===activeSiteId||s.id===partnerId,color));}});}else{openLinkId=null;currentSites.forEach(s=>{if(s.marker){const clntId=findClientOfSite(s.id);const color=clientColorMap[clntId]||'#00e5ff';s.marker.setIcon(mkSiteIcon(s.linkCount||s.isr.length,s.ghost,s.id===activeSiteId,color));}});}}
function gotoSite(siteId){const from=sitesIndex[activeSiteId];const to=sitesIndex[siteId];const bc=document.getElementById('panelBreadcrumb');bc.style.display='flex';bc.innerHTML=`<span style="cursor:pointer;color:var(--accent)" onclick="selectSite('${from?.id}',true)">\u2190 ${esc(from?.label)||'Site Asal'}</span><span style="color:var(--muted)">\u203A</span><span class="active">${esc(to?.label)||esc(siteId)}</span>`;selectSite(siteId,true);}
function repositionZoom(){const topBar=document.querySelector('.top-bar');if(!topBar)return;const h=topBar.getBoundingClientRect().height;document.documentElement.style.setProperty('--topbar-h',h+'px');}
function updatePanelHeight(){const p=document.getElementById('detailPanel');if(!p)return;const h=p.getBoundingClientRect().height;document.querySelector('.app').style.setProperty('--panel-h',h+'px');repositionZoom();}
function closePanel(){
  document.getElementById('detailPanel').classList.remove('open');document.querySelector('.app').classList.remove('panel-open');document.getElementById('panelTabs').style.display='none';document.getElementById('panelBreadcrumb').style.display='none';
  activeSiteId=null;openLinkId=null;
  currentSites.forEach(s=>{if(s.marker){const clntId=findClientOfSite(s.id);const color=clientColorMap[clntId]||'#00e5ff';s.marker.setIcon(mkSiteIcon(s.linkCount||s.isr.length,s.ghost,false,color));}});
  currentEdges.forEach(e=>{if(e.line){const clntId=findClientOfSite(e.siteA.id)||findClientOfSite(e.siteB.id);const color=clientColorMap[clntId]||'#00e5ff';e.line.setStyle({color:e.ghost?color+'88':color+'cc',weight:e.ghost?2:3,dashArray:e.ghost?'8 5':'8 4 2 4',opacity:1});}});
  _activeDrawSiteIds=null;
  if(!showLinks){if(map.hasLayer(edgeLayer))map.removeLayer(edgeLayer);if(map.hasLayer(linkLabelsLayer))map.removeLayer(linkLabelsLayer);edgeCanvasVisible=false;scheduleEdgeDraw();}else{scheduleEdgeDraw();}
  updateLinkLabels(null);requestAnimationFrame(repositionZoom);
}

/* copy via event delegation di panelBody */
document.getElementById('panelBody').addEventListener('click', function(ev){
  const btn = ev.target.closest('.copy-btn');
  if (!btn || !btn.dataset.copy) return;
  const text = btn.dataset.copy;
  navigator.clipboard.writeText(text).catch(()=>{
    const ta=document.createElement('textarea');ta.value=text;document.body.appendChild(ta);ta.select();document.execCommand('copy');document.body.removeChild(ta);
  });
  btn.textContent='✓';btn.classList.add('copied');
  setTimeout(()=>{btn.textContent='⎘';btn.classList.remove('copied');},1500);
});

function copyText(text, btn) {
  navigator.clipboard.writeText(text).then(()=>{
    btn.textContent='✓';btn.classList.add('copied');
    setTimeout(()=>{btn.textContent='⎘';btn.classList.remove('copied');},1500);
  }).catch(()=>{
    const ta=document.createElement('textarea');ta.value=text;document.body.appendChild(ta);ta.select();document.execCommand('copy');document.body.removeChild(ta);
    btn.textContent='✓';btn.classList.add('copied');
    setTimeout(()=>{btn.textContent='⎘';btn.classList.remove('copied');},1500);
  });
}

function toggleDropdown(){const dd=document.getElementById('msDropdown');const caret=document.getElementById('chipCaret');const trigger=document.getElementById('msTrigger');const isOpen=dd.classList.contains('open');closeFilterDropdown();dd.classList.toggle('open');caret.classList.toggle('open',!isOpen);trigger.classList.toggle('open',!isOpen);}
/* ══════════════════════════════════════════════
   FILTER WILAYAH (kabupaten/kota)
   ══════════════════════════════════════════════ */
let selectedAreas = new Set(); // Set city string yang dipilih, kosong = semua

/* Kumpulkan semua city unik dari klien yang sudah di-load */
function getAvailableCities(){
  const cityCount = {};
  [...selectedIds].forEach(clntId=>{
    const data = allLayersData[clntId];
    if(!data) return;
    data.sites.forEach(site=>{
      site.isr.forEach(isr=>{
        if(isr.city){
          const c = isr.city.trim().toUpperCase();
          cityCount[c] = (cityCount[c]||0) + 1;
        }
      });
    });
  });
  return Object.entries(cityCount)
    .sort((a,b)=> b[1]-a[1] || a[0].localeCompare(b[0]));
}

/* Cek apakah site lolos filter wilayah */
function sitePassesFilter(site){
  if(selectedAreas.size === 0) return true;
  return site.isr.some(isr => isr.city && selectedAreas.has(isr.city.trim().toUpperCase()));
}

/* Rebuild dropdown list */
function buildAreaFilterList(){
  const cities = getAvailableCities();
  const list = document.getElementById('filterList');
  if(!list) return;

  const prevQ = document.getElementById('filterSearchInput')?.value || '';

  if(!cities.length){
    list.innerHTML = '<div class="filter-no-result">Muat klien terlebih dahulu</div>';
    return;
  }

  list.innerHTML = cities.map(([city, count])=>{
    const sel = selectedAreas.has(city);
    return `<div class="filter-item${sel?' selected':''}" data-city="${esc(city)}">`
      +`<div class="filter-check"><span class="filter-check-mark">&#10003;</span></div>`
      +`<span class="filter-item-name">${esc(city)}</span>`
      +`<span class="filter-item-count">${count}</span>`
      +`</div>`;
  }).join('');
  // Event delegation — aman untuk semua nama kota, tanpa quote escape issues
  list.onclick = ev => {
    ev.stopPropagation(); // cegah bubble ke filterTrigger onclick
    const item = ev.target.closest('.filter-item');
    if(item && item.dataset.city) toggleAreaFilter(item.dataset.city);
  };
  if(prevQ) filterAreaList(prevQ);
}

/* Filter list saat user ketik */
function filterAreaList(q){
  const items = document.querySelectorAll('#filterList .filter-item');
  const lq = q.trim().toLowerCase();
  let visible = 0;
  items.forEach(el=>{
    const name = el.querySelector('.filter-item-name')?.textContent.toLowerCase()||'';
    const match = !lq || name.includes(lq);
    el.style.display = match ? '' : 'none';
    if(match) visible++;
  });
  let noResult = document.querySelector('#filterList .filter-no-result');
  if(visible===0){
    if(!noResult){
      noResult = document.createElement('div');
      noResult.className = 'filter-no-result';
      noResult.textContent = 'Wilayah tidak ditemukan';
      document.getElementById('filterList').appendChild(noResult);
    }
  } else if(noResult){ noResult.remove(); }
}

/* Refresh interaktivitas hitbox edge sesuai filter wilayah aktif */
function refreshEdgeHitboxes(){
  currentEdges.forEach(e=>{
    if(!e.hitbox)return;
    const sA=sitesIndex[e.siteA?.id],sB=sitesIndex[e.siteB?.id];
    const passes=selectedAreas.size===0||(sA&&sitePassesFilter(sA))||(sB&&sitePassesFilter(sB));
    e.hitbox.setStyle({interactive:passes,opacity:passes?0.001:0});
    if(passes)e.hitbox.on('click',e.hitbox._events?.click?.[0]||function(){});
  });
}

/* Toggle satu kota */
function toggleAreaFilter(city){
  if(selectedAreas.has(city)) selectedAreas.delete(city);
  else selectedAreas.add(city);
  updateFilterTriggerLabel();
  buildAreaFilterList();
  updateMobFilterLabel();
  refreshMarkers();
  refreshEdgeHitboxes();
  scheduleEdgeDraw();
}

/* Clear semua filter */
function clearAreaFilter(ev){
  if(ev && ev.stopPropagation) ev.stopPropagation();
  selectedAreas.clear();
  updateFilterTriggerLabel();
  buildAreaFilterList();
  buildMobFilterList();
  updateMobFilterLabel();
  refreshMarkers();
  refreshEdgeHitboxes();
  scheduleEdgeDraw();
}

/* Update label di trigger button */
function updateFilterTriggerLabel(){
  const trigger = document.getElementById('filterTrigger');
  const label = document.getElementById('filterLabel');
  const caret = document.getElementById('filterCaret');
  if(!trigger||!label) return;
  if(selectedAreas.size===0){
    label.textContent='Semua wilayah';
    trigger.classList.remove('active');
  } else if(selectedAreas.size===1){
    label.textContent=[...selectedAreas][0];
    trigger.classList.add('active');
  } else {
    label.textContent=selectedAreas.size+' wilayah';
    trigger.classList.add('active');
  }
}

/* Toggle dropdown filter */
function toggleFilterDropdown(){
  const dd = document.getElementById('filterDropdown');
  const caret = document.getElementById('filterCaret');
  const trigger = document.getElementById('filterTrigger');
  const isOpen = dd.classList.contains('open');
  // Tutup dropdown lain dulu
  closeDropdownAll();
  if(!isOpen){
    buildAreaFilterList();
    // Posisikan dropdown via JS (position:fixed, perlu koordinat layar)
    const rect = trigger.getBoundingClientRect();
    const ddMaxH = Math.min(320, window.innerHeight * 0.6);
    const spaceBelow = window.innerHeight - rect.bottom - 8;
    const spaceAbove = rect.top - 8;
    if(spaceBelow >= 120 || spaceBelow >= spaceAbove){
      // Buka ke bawah
      dd.style.top = (rect.bottom + 4) + 'px';
      dd.style.bottom = 'auto';
      dd.style.maxHeight = Math.min(ddMaxH, spaceBelow) + 'px';
    } else {
      // Buka ke atas
      dd.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
      dd.style.top = 'auto';
      dd.style.maxHeight = Math.min(ddMaxH, spaceAbove) + 'px';
    }
    dd.style.left = rect.left + 'px';
    // Pastikan tidak keluar kanan layar
    const ddW = Math.max(250, rect.width);
    if(rect.left + ddW > window.innerWidth - 8){
      dd.style.left = (window.innerWidth - ddW - 8) + 'px';
    }
    dd.classList.add('open');
    caret.classList.add('open');
    trigger.classList.add('open');
    setTimeout(()=>{ document.getElementById('filterSearchInput')?.focus(); }, 50);
  }
}

/* Close filter dropdown */
function closeFilterDropdown(){
  document.getElementById('filterDropdown')?.classList.remove('open');
  document.getElementById('filterCaret')?.classList.remove('open');
  document.getElementById('filterTrigger')?.classList.remove('open');
}

function closeDropdownAll(){document.getElementById('msDropdown').classList.remove('open');document.getElementById('chipCaret').classList.remove('open');document.getElementById('msTrigger').classList.remove('open');document.getElementById('legendPanel').classList.remove('open');document.getElementById('legendBtn').classList.remove('active');document.getElementById('locPopup').classList.remove('open');closeFilterDropdown();}
document.addEventListener('click',e=>{if(!e.target.closest('#msTrigger')&&!e.target.closest('#msDropdown')&&!e.target.closest('#legendBtn')&&!e.target.closest('#legendPanel')&&!e.target.closest('#locBtn')&&!e.target.closest('#locPopup')&&!e.target.closest('#filterWrap')){closeDropdownAll();}});

function renderClientItem(c){
  const color=clientColorMap[c.clnt_id]||'#00e5ff';const sel=selectedIds.has(+c.clnt_id);
  return `<div class="ms-item${sel?' selected':''}" onclick="toggleClient(${c.clnt_id})" data-name="${esc(c.clnt_name.toLowerCase())}"><div class="ms-check"><span class="ms-check-mark">\u2713</span></div><div class="ms-dot" style="background:${color}"></div><div class="ms-name">${esc(c.clnt_name.split(',')[0].split(' ').slice(0,2).join(' '))}</div><div class="ms-stats"><span class="ms-stat-chip">${c.total_isr} ISR</span><span class="ms-stat-chip">${c.total_link} MW</span></div></div>`;
}

function renderAllItem(){
  const isAllSelected=selectedIds.size===allClients.length;
  return `<div class="ms-item${isAllSelected?' selected':''}" onclick="toggleClient('__all__')" data-name="semua klien"><div class="ms-check"><span class="ms-check-mark">\u2713</span></div><div class="ms-name" style="color:var(--accent);font-weight:500">Semua klien</div><span class="ms-count">${allClients.reduce((s,c)=>s+(+c.total_isr),0)} ISR</span></div>`;
}

function buildDropdown(){
  const dd=document.getElementById('msDropdown');
  const mobList=document.getElementById('mobSheetList');
  const wasOpen=dd.classList.contains('open');
  const allItem=renderAllItem();
  const items=allClients.map(renderClientItem).join('');

  // Simpan query search yang sedang aktif, supaya tidak reset saat re-render
  const prevDesktopQuery = document.getElementById('msSearchInput')?.value || '';
  const prevMobQuery = document.getElementById('mobSearchInput')?.value || '';

  // Desktop dropdown: search input + scrollable list
  dd.innerHTML=`<div class="ms-search-wrap"><input type="text" class="ms-search-input" id="msSearchInput" placeholder="Cari klien\u2026" oninput="filterClientList(this.value,'msList')" value="${esc(prevDesktopQuery)}"></div><div class="ms-list-scroll" id="msList">${allItem}${items}</div>`;
  if(wasOpen)dd.classList.add('open');
  if(prevDesktopQuery) filterClientList(prevDesktopQuery, 'msList');

  // Mobile sheet: search input + list
  if(mobList){
    mobList.innerHTML=`<div class="ms-search-wrap"><input type="text" class="ms-search-input" id="mobSearchInput" placeholder="Cari klien\u2026" oninput="filterClientList(this.value,'mobList')" value="${esc(prevMobQuery)}"></div><div id="mobListItems">${allItem}${items}</div>`;
    if(prevMobQuery) filterClientList(prevMobQuery, 'mobList');
  }
}

function filterClientList(query, containerId){
  const container = containerId==='msList' ? document.getElementById('msList') : document.getElementById('mobListItems');
  if(!container) return;
  const q = query.trim().toLowerCase();
  const items = container.querySelectorAll('.ms-item');
  let visibleCount = 0;
  items.forEach(el=>{
    const name = el.dataset.name || '';
    const match = !q || name.includes(q);
    el.style.display = match ? '' : 'none';
    if(match) visibleCount++;
  });
  // Tampilkan pesan kalau tidak ada hasil
  let noResult = container.querySelector('.ms-no-result');
  if(visibleCount===0){
    if(!noResult){
      noResult=document.createElement('div');
      noResult.className='ms-no-result';
      noResult.textContent='Klien tidak ditemukan';
      container.appendChild(noResult);
    }
  } else if(noResult){
    noResult.remove();
  }
}

/* ── updateChips: 0=placeholder, 1=nama klien, >1=jumlah, semua=Semua klien ── */
function updateChips(){
  const area=document.getElementById('chipsArea');
  if(selectedIds.size===0){area.innerHTML=`<span style="font-size:12px;color:var(--muted)">Pilih klien\u2026</span>`;}
  else if(selectedIds.size===allClients.length){area.innerHTML=`<span class="chip" style="border-color:var(--accent);color:var(--accent)">Semua klien (${allClients.length})</span>`;}
  else if(selectedIds.size===1){const id=[...selectedIds][0];const c=allClients.find(x=>x.clnt_id==id);const col=clientColorMap[id]||'#00e5ff';const name=c?c.clnt_name.split(',')[0].split(' ').slice(0,3).join(' '):id;area.innerHTML=`<span class="chip" style="border-color:${col};color:${col}">${esc(name)}</span>`;}
  else{area.innerHTML=`<span class="chip" style="border-color:var(--accent);color:var(--accent)">${selectedIds.size} klien dipilih</span>`;}
  mobRenderChips();
}

/* ── mobRenderChips: sama, versi mobile ── */
function mobRenderChips(){
  const area=document.getElementById('mobSelChips');if(!area)return;
  if(selectedIds.size===0){area.innerHTML=`<span style="color:var(--muted);font-size:11px">Pilih klien\u2026</span>`;}
  else if(selectedIds.size===allClients.length){area.innerHTML=`<span class="chip" style="border-color:var(--accent);color:var(--accent)">Semua klien (${allClients.length})</span>`;}
  else if(selectedIds.size===1){const id=[...selectedIds][0];const c=allClients.find(x=>x.clnt_id==id);const col=clientColorMap[id]||'#00e5ff';const name=c?c.clnt_name.split(',')[0].split(' ').slice(0,3).join(' '):id;area.innerHTML=`<span class="chip" style="border-color:${col};color:${col}">${esc(name)}</span>`;}
  else{area.innerHTML=`<span class="chip" style="border-color:var(--accent);color:var(--accent)">${selectedIds.size} klien dipilih</span>`;}
}

async function toggleClient(clntId){
  if(clntId==='__all__'){const isAllSelected=selectedIds.size===allClients.length;if(isAllSelected){selectedIds.clear();clearAreaFilter();}else{await ensureAllLoaded();allClients.forEach(c=>selectedIds.add(+c.clnt_id));if(navigator.geolocation){navigator.geolocation.getCurrentPosition(pos=>{setUserLocation(pos.coords.latitude,pos.coords.longitude);map.setView([pos.coords.latitude,pos.coords.longitude],15,{animate:true});},()=>{map.setView([-7.80,110.37],15,{animate:true});});}else{map.setView([-7.80,110.37],15,{animate:true});}if(showLinks)setLinkVisibility(false);}}
  else{const id=+clntId;if(selectedIds.has(id)){selectedIds.delete(id);}else{selectedIds.add(id);showSkeletonForClient(id);await ensureLoaded(id);clearSkeleton();}}
  LS.save();
  const skipFitNow=(clntId==='__all__'&&selectedIds.size===allClients.length);
  if(skipFitNow){buildDropdown();updateChips();closePanel();map.once('moveend',()=>{renderAllVisible(true);});}else{renderAllVisible(false);buildDropdown();updateChips();closePanel();}
}

async function ensureLoaded(clntId){
  clntId=+clntId;if(allLayersData[clntId])return;let j;
  for(let attempt=0;attempt<2;attempt++){try{const r=await fetch(`${API.isr}?clnt_id=${clntId}`,{signal:AbortSignal.timeout(20000)});j=await r.json();break;}catch(e){if(attempt===0){await new Promise(res=>setTimeout(res,1500));continue;}const overlay=document.getElementById('stateOverlay');overlay.style.display='flex';overlay.innerHTML='<span>&#9888; Gagal memuat data. Coba lagi atau refresh halaman.</span>';console.error('fetch isr error',e);return;}}
  if(!j||!j.success){const overlay=document.getElementById('stateOverlay');overlay.style.display='flex';overlay.innerHTML=`<span>&#9888; ${j?.error||'Gagal memuat data ISR'}</span>`;return;}
  const rows=j.raw||[];let sites=clusterISRtoSites(rows);sites.forEach(s=>{s.id=`c${clntId}_${s.id}`;});
  const edges=buildMWLinks(sites,rows);const connectedIds=new Set();edges.forEach(e=>{connectedIds.add(e.siteA.id);connectedIds.add(e.siteB.id);});
  sites=sites.filter(s=>!s.ghost||connectedIds.has(s.id));allLayersData[clntId]={sites,edges,rows};
}

async function ensureAllLoaded(){const overlay=document.getElementById('stateOverlay');overlay.style.display='flex';overlay.innerHTML='<div class="spinner"></div><span>Memuat semua klien\u2026</span>';await Promise.all(allClients.map(c=>ensureLoaded(c.clnt_id)));overlay.style.display='none';}

/* ── updateFooter: tampilkan angka + spasi + satuan ── */
function updateFooter(){
  const ids=[...selectedIds];let totalIsr=0,totalLink=0;
  ids.forEach(id=>{const c=allClients.find(x=>x.clnt_id==id);if(c){totalIsr+=(+c.total_isr);totalLink+=(+c.total_link);}});
  document.getElementById('footIsr').textContent=totalIsr.toLocaleString('id')+' ISR';
  document.getElementById('footLink').textContent=totalLink.toLocaleString('id')+' MW';
  document.getElementById('footKlien').textContent=ids.length+' klien';
  const mi=document.getElementById('mobIsr');if(mi)mi.textContent=totalIsr.toLocaleString('id')+' ISR';
  const mm=document.getElementById('mobMw');if(mm)mm.textContent=totalLink.toLocaleString('id')+' MW';
  updateLegend();
}

function toggleLegend(){const panel=document.getElementById('legendPanel');const btn=document.getElementById('legendBtn');const isOpen=panel.classList.contains('open');panel.classList.toggle('open',!isOpen);btn.classList.toggle('active',!isOpen);requestAnimationFrame(repositionZoom);}

async function loadClients(){
  let j;
  for(let attempt=0;attempt<2;attempt++){try{const r=await fetch(API.clients,{signal:AbortSignal.timeout(10000)});j=await r.json();break;}catch(e){if(attempt===0){await new Promise(res=>setTimeout(res,1500));continue;}document.getElementById('stateOverlay').innerHTML='<span>&#9888; Koneksi ke server gagal. Refresh untuk coba lagi.</span>';return;}}
  if(!j.success){document.getElementById('stateOverlay').innerHTML=`<span>\u26A0 ${j.error||'Gagal memuat klien'}</span>`;return;}
  allClients=j.data;let paletteIdx=0;
  allClients.forEach((c)=>{const named=assignClientColor(c.clnt_name);if(named){clientColorMap[+c.clnt_id]=named;}else{clientColorMap[+c.clnt_id]=CLIENT_COLORS[paletteIdx%CLIENT_COLORS.length];paletteIdx++;}});
  buildDropdown();updateChips();
  /* Restore state dari localStorage */
  const saved = LS.load();
  const _saving = LS._skip = true; // flag: jangan save saat restore
  if (saved.theme) applyUiTheme(saved.theme === 'bright');
  if (saved.basemap) setBasemap(saved.basemap);
  LS._skip = false;

  const overlay=document.getElementById('stateOverlay');

  if (saved.clients && saved.clients.length) {
    const validIds = saved.clients.filter(id => allClients.find(c=>+c.clnt_id===+id));
    if (validIds.length) {
      overlay.style.display='flex';
      overlay.innerHTML='<div class="spinner"></div><span>Memuat sesi sebelumnya…</span>';
      try {
        await Promise.all(validIds.map(id => ensureLoaded(+id)));
        validIds.forEach(id => selectedIds.add(+id));
        buildDropdown(); updateChips();
        renderAllVisible(false);
        requestAnimationFrame(()=>{ recalcLayout(); });
        return;
      } catch(e) {
        console.warn('Restore gagal, reset state', e);
        selectedIds.clear();
        try { localStorage.removeItem(LS.KEY); } catch(_) {}
      }
    }
  }

  overlay.style.display='flex';
  overlay.innerHTML='<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg><span>Pilih klien untuk melihat peta</span>';
  requestAnimationFrame(()=>{ recalcLayout(); });
}

function setLinkVisibility(checked){
  showLinks=checked;const lt=document.getElementById('linkToggle');if(lt)lt.checked=checked;const mlt=document.getElementById('mobLinkToggle');if(mlt)mlt.checked=checked;
  if(showLinks){_activeDrawSiteIds=null;if(!map.hasLayer(edgeLayer))edgeLayer.addTo(map);if(!map.hasLayer(linkLabelsLayer))linkLabelsLayer.addTo(map);edgeCanvasVisible=true;scheduleEdgeDraw();}
  else{if(map.hasLayer(edgeLayer))map.removeLayer(edgeLayer);if(map.hasLayer(linkLabelsLayer))map.removeLayer(linkLabelsLayer);edgeCanvasVisible=false;scheduleEdgeDraw();}
}
document.getElementById('linkToggle').addEventListener('change',function(){setLinkVisibility(this.checked);});
function mobSwitchLinks(checked){setLinkVisibility(checked);}
document.getElementById('basemapSel').addEventListener('change',function(){setBasemap(this.value);});
function mobSwitchBasemap(v){setBasemap(v);}
document.getElementById('themeToggle').addEventListener('change',function(){applyUiTheme(this.checked);});
document.getElementById('panelClose').addEventListener('click',closePanel);



const _panel=document.getElementById('detailPanel');
_panel.addEventListener('touchstart',(ev)=>{ev.stopPropagation();},{passive:true});
_panel.addEventListener('touchmove',(ev)=>{ev.stopPropagation();},{passive:true});

let userLocMarker=null;
function setUserLocation(lat,lng){
  if(userLocMarker)map.removeLayer(userLocMarker);
  const icon=L.divIcon({html:`<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22"><circle cx="11" cy="11" r="10" fill="#00e5ff" fill-opacity=".2" stroke="#00e5ff" stroke-width="2"/><circle cx="11" cy="11" r="4" fill="#00e5ff"/><circle cx="11" cy="11" r="4" fill="#00e5ff" opacity=".4"><animate attributeName="r" from="4" to="10" dur="1.5s" repeatCount="indefinite"/><animate attributeName="opacity" from=".4" to="0" dur="1.5s" repeatCount="indefinite"/></circle></svg>`,className:'',iconSize:[22,22],iconAnchor:[11,11]});
  userLocMarker=L.marker([lat,lng],{icon,zIndexOffset:2000,interactive:false}).addTo(map);
  userLocMarker.bindTooltip('Lokasi Anda',{className:'lf-tooltip',direction:'right',offset:[12,0]});
}
function useGPSLoc(){if(!navigator.geolocation){alert('Geolocation tidak didukung browser ini.');return;}navigator.geolocation.getCurrentPosition(pos=>{document.getElementById('manualLat').value=pos.coords.latitude.toFixed(6);document.getElementById('manualLng').value=pos.coords.longitude.toFixed(6);},()=>alert('Gagal mendapatkan lokasi GPS.'));}
function useManualLoc(){const lat=parseFloat(document.getElementById('manualLat').value);const lng=parseFloat(document.getElementById('manualLng').value);if(isNaN(lat)||isNaN(lng)){alert('Masukkan koordinat yang valid.');return;}document.getElementById('locPopup').classList.remove('open');setUserLocation(lat,lng);map.setView([lat,lng],15,{animate:true});}
function goToLoc(){const lat=parseFloat(document.getElementById('manualLat').value);const lng=parseFloat(document.getElementById('manualLng').value);if(!isNaN(lat)&&!isNaN(lng)){useManualLoc();}else{useGPSLoc();setTimeout(()=>{const la=parseFloat(document.getElementById('manualLat').value);const lo=parseFloat(document.getElementById('manualLng').value);if(!isNaN(la)&&!isNaN(lo))useManualLoc();},1500);}}
document.getElementById('locBtn').addEventListener('click',(e)=>{e.stopPropagation();document.getElementById('locPopup').classList.toggle('open');});
document.addEventListener('click',(e)=>{if(!e.target.closest('#locBtn')&&!e.target.closest('#locPopup'))document.getElementById('locPopup').classList.remove('open');});



/* ── Scroll zoom guard: disable saat kursor di panel ── */
(function(){
  const PANEL_SEL = '#detailPanel,#msDropdown,#locPopup,#legendPanel,.mob-sheet,.mob-drawer';
  let leaveTimer = null;

  function disableZoom(){
    clearTimeout(leaveTimer);
    map.scrollWheelZoom.disable();
  }
  function enableZoom(){
    clearTimeout(leaveTimer);
    leaveTimer = setTimeout(()=> map.scrollWheelZoom.enable(), 350);
  }

  // Track via mousemove di document — paling reliable
  document.addEventListener('mousemove', function(ev){
    if(ev.target.closest(PANEL_SEL)) disableZoom();
    else enableZoom();
  }, { passive: true });
})();

/* ── Mobile: legend collapsible toggle ── */
function toggleMobLegend(){
  const wrap = document.getElementById('mobDrawerLegend');
  if(!wrap) return;
  wrap.classList.toggle('collapsed');
  try { localStorage.setItem('komenk_mob_legend_collapsed', wrap.classList.contains('collapsed')?'1':'0'); } catch(e){}
}
(function(){
  try {
    if(localStorage.getItem('komenk_mob_legend_collapsed')==='1'){
      const el=document.getElementById('mobDrawerLegend');
      if(el) el.classList.add('collapsed');
    }
  } catch(e){}
})();

/* ── Mobile: filter wilayah di drawer ── */
function buildMobFilterList(){
  const list = document.getElementById('mobFilterList');
  if(!list) return;
  const cities = getAvailableCities();
  if(!cities.length){
    list.innerHTML = '<div style="padding:12px 0;color:var(--muted);font-size:12px">Belum ada data wilayah</div>';
    return;
  }
  list.innerHTML = cities.map(([city, count])=>{
    const sel = selectedAreas.has(city);
    return `<div class="mob-filter-item${sel?' selected':''}" data-city="${esc(city)}">
      <div class="filter-check"><span class="filter-check-mark">&#10003;</span></div>
      <span>${esc(city)}</span>
      <span class="mob-filter-count">${count}</span>
    </div>`;
  }).join('');

  list.onclick = ev => {
    ev.stopPropagation();
    const item = ev.target.closest('.mob-filter-item');
    if(item && item.dataset.city){
      toggleAreaFilter(item.dataset.city);
      buildMobFilterList(); // rebuild list mobile
      updateMobFilterLabel();
    }
  };
}

function mobFilterAreaList(q){
  const items = document.querySelectorAll('#mobFilterList .mob-filter-item');
  const lq = q.trim().toLowerCase();
  items.forEach(el=>{
    const name = el.querySelector('span')?.textContent.toLowerCase()||'';
    el.style.display = !lq || name.includes(lq) ? '' : 'none';
  });
}

function updateMobFilterLabel(){
  const label = document.getElementById('mobFilterActiveLabel');
  const text = document.getElementById('mobFilterActiveText');
  if(!label||!text) return;
  if(selectedAreas.size===0){
    label.classList.remove('show');
    text.textContent='Semua wilayah';
  } else {
    label.classList.add('show');
    text.textContent = selectedAreas.size===1
      ? [...selectedAreas][0]
      : selectedAreas.size+' wilayah dipilih';
  }
}

/* ── Desktop legend collapse toggle ── */
function toggleDesktopLegend(){
  const el = document.getElementById('desktopLegend');
  el.classList.toggle('collapsed');
  try { localStorage.setItem('komenk_legend_collapsed', el.classList.contains('collapsed')?'1':'0'); } catch(e){}
}
// Restore collapsed state on load
(function(){
  try {
    if(localStorage.getItem('komenk_legend_collapsed')==='1'){
      const el=document.getElementById('desktopLegend');
      if(el) el.classList.add('collapsed');
    }
  } catch(e){}
})();

window.addEventListener('beforeunload',()=>{ LS.save(); });



/* ── Keyboard shortcuts ── */
document.addEventListener('keydown', function(ev){
  // Jangan intercept kalau user sedang di input/textarea
  if(ev.target.matches('input,textarea,select')) return;
  switch(ev.key){
    case 'Escape':
      if(document.getElementById('detailPanel').classList.contains('open')){
        closePanel(); ev.preventDefault();
      } else if(document.getElementById('msDropdown').classList.contains('open')){
        closeDropdownAll(); ev.preventDefault();
      }
      break;
    case 'l': case 'L':
      setLinkVisibility(!showLinks); ev.preventDefault();
      break;
    case 't': case 'T':
      const isBright = document.documentElement.getAttribute('data-theme')==='bright';
      applyUiTheme(!isBright);
      const cur=document.getElementById('basemapSel')?.value;
      if(cur==='dark'||cur==='bright') setBasemap(!isBright?'bright':'dark');
      ev.preventDefault();
      break;
  }
});
window.addEventListener("resize",()=>{repositionZoom();updateChips();recalcLayout();});
window.addEventListener("orientationchange",()=>{setTimeout(()=>{repositionZoom();updateChips();recalcLayout();},150);});
requestAnimationFrame(()=>{
  repositionZoom();recalcLayout();
  // Ulangi setelah font Google Fonts selesai load
  setTimeout(()=>{repositionZoom();recalcLayout();},300);
  setTimeout(()=>{repositionZoom();recalcLayout();},800);
});
// Fallback: font load event
if(document.fonts){document.fonts.ready.then(()=>recalcLayout());}
loadClients();

/* ══ MOBILE JS ══ */
const isMobileView=()=>window.innerWidth<=768;
function mobOpenDrawer(){document.getElementById('mobDrawer').classList.add('open');document.getElementById('mobScrim').classList.add('open');mobCloseSheet();const bm=document.getElementById('basemapSel').value;document.getElementById('mobBasemap').value=bm;document.getElementById('mobThemeToggle').checked=document.getElementById('themeToggle').checked;document.getElementById('mobLinkToggle').checked=showLinks;}
function mobCloseDrawer(){document.getElementById('mobDrawer').classList.remove('open');document.getElementById('mobScrim').classList.remove('open');}
let mobSheetOpen=false;
function mobToggleSheet(){mobSheetOpen?mobCloseSheet():mobOpenSheet();}
function mobOpenSheet(){mobSheetOpen=true;document.getElementById('mobSheet').classList.add('open');document.getElementById('mobSelCaret').classList.add('open');document.getElementById('mobSelTrigger').classList.add('open');}
function mobCloseSheet(){mobSheetOpen=false;document.getElementById('mobSheet').classList.remove('open');document.getElementById('mobSelCaret').classList.remove('open');document.getElementById('mobSelTrigger').classList.remove('open');}
function mobToggleGPS(){if(!navigator.geolocation){alert('Geolocation tidak didukung browser ini.');return;}navigator.geolocation.getCurrentPosition(pos=>{const lat=pos.coords.latitude,lng=pos.coords.longitude;setUserLocation(lat,lng);map.setView([lat,lng],15,{animate:true});document.getElementById('manualLat').value=lat.toFixed(6);document.getElementById('manualLng').value=lng.toFixed(6);const ml=document.getElementById('mobLat');if(ml)ml.value=lat.toFixed(6);const mln=document.getElementById('mobLng');if(mln)mln.value=lng.toFixed(6);},()=>alert('Gagal mendapatkan lokasi GPS.'));}
function mobGPS(){if(!navigator.geolocation){alert('Geolocation tidak didukung browser ini.');return;}navigator.geolocation.getCurrentPosition(pos=>{document.getElementById('mobLat').value=pos.coords.latitude.toFixed(6);document.getElementById('mobLng').value=pos.coords.longitude.toFixed(6);},()=>alert('Gagal mendapatkan lokasi GPS.'));}
function mobGoLoc(){const lat=parseFloat(document.getElementById('mobLat').value);const lng=parseFloat(document.getElementById('mobLng').value);if(isNaN(lat)||isNaN(lng)){alert('Masukkan koordinat yang valid.');return;}setUserLocation(lat,lng);map.setView([lat,lng],15,{animate:true});mobCloseDrawer();}

/* ── Kompas mobile: reset ke utara ── */
function mobResetNorth(){map.setBearing(0);}

map.on('click',()=>{mobCloseSheet();});

(function(){
  const panel=document.getElementById('detailPanel');let startY=0,dragging=false;
  panel.addEventListener('touchstart',(ev)=>{if(!isMobileView())return;if(ev.target.closest('.panel-body'))return;startY=ev.touches[0].clientY;dragging=true;},{passive:true});
  panel.addEventListener('touchmove',(ev)=>{if(!dragging)return;const dy=ev.touches[0].clientY-startY;if(dy>0)panel.style.transform=`translateY(${dy}px)`;},{passive:true});
  panel.addEventListener('touchend',(ev)=>{if(!dragging)return;dragging=false;const dy=(ev.changedTouches[0]?.clientY||0)-startY;panel.style.transform='';if(dy>80)closePanel();});
})();

</script>
</body>
</html>
