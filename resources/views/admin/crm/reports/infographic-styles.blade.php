@push('styles')
<style>
.crm-workspace:has(.crm-report-bento){--report-blue:#5d87ff;--report-cyan:#36bffa;gap:22px}
.crm-workspace [data-chart-download-status]:empty{display:none}
.crm-report-insight{display:flex;flex-direction:column;padding:26px!important;overflow:hidden}
.crm-report-insight__header{align-items:flex-start!important;gap:12px!important}
.crm-report-insight__identity{display:flex;align-items:flex-start;gap:12px;flex:1 1 200px;min-width:0}
.crm-report-insight__identity>div{min-width:0}
.crm-report-insight__identity h2{font-size:16px!important;line-height:1.4;letter-spacing:-.025em}
.crm-report-insight__identity .crm-report-chart-subtitle{font-size:11px;max-width:34ch}
.crm-report-insight__icon{display:grid;place-items:center;width:38px;height:38px;flex:0 0 38px;border-radius:11px;background:#eef3ff;color:#5978c9}
.crm-report-insight__icon>svg{width:21px!important;height:21px!important;fill:none;stroke:currentColor;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round}
.crm-report-insight__period{display:inline-flex;padding:5px 8px;border:1px solid var(--admin-line);border-radius:7px;color:var(--admin-muted);background:var(--admin-canvas);font-size:10px;white-space:nowrap}
.crm-report-insight__summary{display:flex;align-items:center;justify-content:space-between;gap:16px;margin:25px 0 12px}
.crm-report-insight__summary>div:first-child{display:grid;gap:4px}
.crm-report-insight__summary strong{font-size:32px;line-height:1.2;letter-spacing:-.05em;font-weight:700;color:var(--admin-ink);font-variant-numeric:tabular-nums}
.crm-report-insight__summary span{color:var(--admin-muted);font-size:11px;line-height:1.6}
.crm-report-insight__context{display:grid;gap:4px;text-align:right;max-width:45%;padding-left:15px;border-left:1px solid var(--admin-line)}
.crm-report-insight__context .crm-report-insight__context-value{font-size:14px;font-weight:700;color:#486ac1;font-variant-numeric:tabular-nums}
.crm-report-insight__plot{display:flex;align-items:center;justify-content:center;min-height:220px;margin:0 -7px;flex:1}
.crm-report-insight__plot>svg{max-height:320px}
.crm-report-insight__plot--empty{margin:12px 0 16px;border:1px dashed #dfe7f4;border-radius:13px;background:linear-gradient(180deg,#fafcff,#fff);min-height:230px}
.crm-report-country-empty{text-align:center;padding:24px 20px;max-width:34ch}
.crm-report-country-empty>svg{width:86px!important;height:69px!important;margin:0 auto 10px}
.crm-report-country-empty h3{font-size:14px;margin:0 0 7px;color:var(--admin-ink);line-height:1.5}
.crm-report-country-empty p{margin:0;color:var(--admin-muted);font-size:12px;line-height:1.7}
.crm-report-insight__footer{display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:12px;padding-top:14px;margin-top:12px;border-top:1px solid var(--admin-line)}
.crm-report-insight__data{display:contents}
.crm-report-insight__data summary{display:flex;align-items:center;gap:7px;min-height:44px;padding:0!important;font-size:11px!important;color:var(--admin-muted);font-weight:650}
.crm-report-insight__data summary::-webkit-details-marker{display:none}
.crm-report-insight__data summary>svg{width:16px!important;height:16px!important;fill:none;stroke:currentColor;stroke-width:1.7}
.crm-report-insight__data[open]>.crm-report-table{order:3;flex:1 1 100%;width:100%}
.crm-report-insight__data[open] summary{color:#486ac1}
.crm-report-downloads--compact{gap:0!important;border:1px solid var(--admin-line);border-radius:8px;overflow:hidden;margin-left:auto}
.crm-report-downloads--compact .button{min-height:44px;padding:0 10px!important;font-size:10px!important;border:0;border-radius:0;background:#fff;color:var(--admin-muted);box-shadow:none}
.crm-report-downloads--compact .button+.button{border-left:1px solid var(--admin-line)}
.crm-report-downloads--compact .button:hover{background:#f0f4ff;color:#4267bf}
.crm-report-insight__legend{display:flex;gap:16px;flex-wrap:wrap;padding-bottom:8px}
.crm-report-insight__legend span{display:flex;align-items:center;gap:6px;font-size:11px;color:var(--admin-muted)}
.crm-report-insight__legend i{width:7px;height:7px;background:#5d87ff;border-radius:50%}
.crm-report-insight__legend span+span i{background:#36bffa}
@media(max-width:600px){.crm-report-insight{padding:20px 16px!important}.crm-report-insight__summary{margin-top:22px}.crm-report-insight__plot{min-height:190px}.crm-report-insight__plot--empty{min-height:220px}.crm-report-insight__identity{flex-basis:190px}.crm-report-insight__period{font-size:9px}.crm-report-insight__identity h2{font-size:15px!important}.crm-report-downloads--compact .button{min-width:44px}.crm-report-insight__footer{gap:8px}}
.crm-workspace:has(.crm-report-bento)>.page-head p{text-align:left;margin-top:8px;font-size:13px;max-width:65ch;line-height:1.6}
.crm-workspace:has(.crm-report-bento) .panel{border-radius:14px;box-shadow:0 3px 18px rgba(15,35,75,.025)}
.crm-report-bento{display:grid;grid-template-columns:minmax(0,2.1fr) minmax(270px,1fr);gap:22px;align-items:stretch}
.crm-report-trend{grid-row:span 2;display:flex;flex-direction:column}
.crm-report-trend__body{display:grid;grid-template-columns:minmax(0,1fr) 150px;align-items:center;gap:20px;flex:1}
.crm-report-trend__body>div:first-child{min-width:0}
.crm-report-trend__aside{padding:20px 0}
.crm-report-trend__aside>strong{display:block;font-size:28px;color:var(--admin-ink);line-height:1.3;margin-top:8px;letter-spacing:-.04em}
.crm-report-trend__aside>p{margin:3px 0;color:var(--admin-muted);font-size:12px}
.crm-report-trend__aside>.button{margin-top:20px;background:var(--report-blue);color:#fff;border-color:transparent;width:100%}
.crm-report-trend__divider{height:1px;background:var(--admin-line);margin:20px 0}
.crm-report-dot-label{display:flex;align-items:center;gap:7px;font-size:11px;line-height:1.6;color:var(--admin-muted)}
.crm-report-dot-label i{display:block;width:7px;height:7px;border-radius:50%;flex:0 0 7px;background:var(--report-blue)}
.crm-report-dot-label--cyan i{background:var(--report-cyan)}
.crm-report-breakdown h2,.crm-report-monthly h2{font-size:17px;margin:0;color:var(--admin-ink)}
.crm-report-breakdown__body{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-top:12px}
.crm-report-breakdown__body>div{min-width:0}
.crm-report-breakdown__body>svg{width:104px;height:104px;flex:0 0 104px}
.crm-report-big-number{display:block;font-size:30px;line-height:1.3;font-weight:650;letter-spacing:-.04em;color:var(--admin-ink);font-variant-numeric:tabular-nums}
.crm-report-breakdown__body p{font-size:12px;color:var(--admin-muted);margin:6px 0 10px}
.crm-report-small-tag{display:inline-flex;padding:4px 8px;border-radius:6px;background:#edf9f5;font-size:10px;color:#267567}
.crm-report-breakdown__legend{display:flex;flex-wrap:wrap;gap:12px;margin-top:14px}
.crm-report-breakdown__legend span{display:flex;align-items:center;gap:5px;color:var(--admin-muted);font-size:10px}
.crm-report-breakdown__legend i{display:block;width:7px;height:7px;border-radius:50%;background:var(--report-blue)}
.crm-report-breakdown__legend span+span i{background:#d9e2fc}
.crm-report-monthly{position:relative;overflow:hidden;padding-bottom:0!important}
.crm-report-monthly__head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:12px}
.crm-report-mini-icon{display:grid;place-items:center;width:38px;height:38px;flex:0 0 38px;border-radius:10px;background:#eaf5ff;color:#2586ba}
.crm-report-mini-icon svg{width:21px;height:21px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round}
.crm-report-monthly__date{font-size:12px;margin:6px 0 10px;color:var(--admin-muted)}
.crm-report-monthly .crm-report-spark{display:block;width:calc(100% + 48px);max-width:none;margin:0 -24px;height:76px;object-fit:cover}
.crm-conversion-panel .crm-conversion-gauge__value{stroke:var(--report-blue)}
.crm-conversion-panel .crm-conversion-metric--total{background:linear-gradient(160deg,#edf3ff,var(--admin-card) 80%);border-color:#e4ecff}
.crm-conversion-panel .crm-conversion-period-icon{background:#edf3ff;color:#476ac4}
.crm-report-bento .crm-report-downloads{margin:12px 0}
.crm-report-bento summary{font-size:12px}
.crm-report-grid .crm-report-panel--counsellors{grid-column:1/-1}
@media(max-width:1500px){.crm-report-trend__body{grid-template-columns:minmax(0,1fr)}.crm-report-trend__aside{display:grid;grid-template-columns:1fr 1fr;gap:4px 18px;padding:0}.crm-report-trend__aside>.crm-report-dot-label{grid-row:1}.crm-report-trend__aside>strong{grid-row:2;margin:0;font-size:25px}.crm-report-trend__aside>p{grid-row:3}.crm-report-trend__aside>.button{grid-column:1/-1;margin-top:14px}.crm-report-trend__divider{display:none}}
@media(max-width:1150px){.crm-report-bento{grid-template-columns:repeat(2,minmax(0,1fr))}.crm-report-trend{grid-column:1/-1;grid-row:auto}.crm-report-trend__body{grid-template-columns:minmax(0,1fr) 160px}.crm-report-trend__aside{display:block}.crm-report-trend__aside>strong{margin-top:8px}.crm-report-trend__divider{display:block;margin:16px 0}.crm-report-breakdown__body>svg{width:88px;height:88px;flex-basis:88px}.crm-report-big-number{font-size:27px}}
@media(max-width:650px){.crm-report-bento{grid-template-columns:minmax(0,1fr);gap:16px}.crm-report-trend{grid-column:auto}.crm-report-trend__body{grid-template-columns:minmax(0,1fr);gap:0}.crm-report-trend__aside{display:grid}.crm-report-trend__divider{display:none}.crm-report-breakdown__body>svg{width:110px;height:110px;flex-basis:110px}.crm-report-monthly .crm-report-spark{width:calc(100% + 32px);margin:0 -16px;height:90px}}
.crm-report-welcome{display:flex;align-items:center;justify-content:space-between;gap:24px;padding:28px 32px;border:1px solid var(--admin-line);border-radius:18px;background:linear-gradient(115deg,#f0f4ff 0%,#f7f9ff 57%,#fff1f2 100%);overflow:hidden}
.crm-report-welcome>div{max-width:56ch;min-width:0}
.crm-report-welcome h2{margin:8px 0 10px;font-size:26px;line-height:1.3;letter-spacing:-.045em;color:var(--admin-ink)}
.crm-report-welcome p{color:var(--admin-muted);font-size:13px;margin:0;line-height:1.7}
.crm-report-welcome__art{width:230px;max-width:32%;height:auto;flex:0 0 auto}
.crm-report-welcome__stats{display:flex;gap:28px;margin-top:22px}
.crm-report-welcome__stats>span{display:grid;gap:4px;color:var(--admin-muted);font-size:12px}
.crm-report-welcome__stats>span+span{border-left:1px solid #dbe3f2;padding-left:28px}
.crm-report-welcome__stats strong{font-size:28px;font-weight:700;color:var(--admin-ink);line-height:1.2;letter-spacing:-.04em}
.crm-report-overview{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:16px}
.crm-report-tile{--tile-tint:#edf3ff;--tile-accent:#365da9;--tile-border:#dfe9fa;position:relative;display:flex;flex-direction:column;gap:0;padding:22px 22px 0;border-radius:18px;text-align:left;min-width:0;overflow:hidden;border:1px solid var(--tile-border);background:linear-gradient(135deg,var(--tile-tint),var(--admin-card) 160%);color:var(--tile-accent);box-shadow:0 3px 12px rgba(18,37,72,.02)}
.crm-report-tile:before{content:"";position:absolute;width:100px;height:100px;border:1px solid var(--tile-accent);border-radius:50%;opacity:.06;right:-38px;top:-45px;pointer-events:none}
.crm-report-tile__top{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:18px}
.crm-report-tile__icon{display:grid;place-items:center;width:44px;height:44px;flex:0 0 44px;border-radius:14px;background:rgba(255,255,255,.88);border:1px solid rgba(255,255,255,.9);box-shadow:0 4px 10px rgba(26,52,92,.035)}
.crm-report-tile__icon svg{width:25px;height:25px;fill:none;stroke:currentColor;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round}
.crm-report-tile__scope{display:inline-flex;padding:4px 8px;border:1px solid var(--tile-border);border-radius:6px;background:rgba(255,255,255,.42);font-size:10px;font-weight:650;white-space:nowrap;line-height:1.5}
.crm-report-tile h2{margin:0;font-size:14px;line-height:1.4;font-weight:650;letter-spacing:-.015em}
.crm-report-tile__value{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:8px;min-width:0}
.crm-report-tile__value>strong{font-size:40px;font-weight:700;line-height:1.2;font-variant-numeric:tabular-nums;letter-spacing:-.055em;overflow-wrap:anywhere}
.crm-report-tile__watermark{display:block;width:66px;height:42px;flex:0 0 66px}
.crm-report-tile__description{margin:5px 0 20px;font-size:11px;line-height:1.6;opacity:.9}
.crm-report-tile__footer{display:flex;align-items:center;gap:7px;margin:auto -22px 0;padding:12px 22px;border-top:1px solid var(--tile-border);background:rgba(255,255,255,.32);font-size:10px;line-height:1.5;min-height:41px}
.crm-report-tile__footer>span{display:block;width:5px;height:5px;flex:0 0 5px;border-radius:50%;background:currentColor;opacity:.65}
.crm-report-tile--mint{--tile-tint:#e9faf5;--tile-accent:#176b5c;--tile-border:#d6efe6}.crm-report-tile--violet{--tile-tint:#f0edff;--tile-accent:#6550ad;--tile-border:#e4def8}.crm-report-tile--cyan{--tile-tint:#e8f7fc;--tile-accent:#25637c;--tile-border:#d4eaf3}.crm-report-tile--amber{--tile-tint:#fff6e7;--tile-accent:#88601f;--tile-border:#f1e6cf}.crm-report-tile--rose{--tile-tint:var(--admin-primary-soft);--tile-accent:var(--admin-primary-dark);--tile-border:#f6dce0}
.crm-report-data-note{display:flex;gap:10px;align-items:flex-start;color:var(--admin-muted);padding:0 5px}
.crm-report-data-note svg{width:17px;height:17px;flex:0 0 17px;fill:none;stroke:currentColor;stroke-width:1.7;margin-top:2px}
.crm-report-data-note p{margin:0;font-size:12px;line-height:1.6}
.crm-report-chart-subtitle{font-size:12px;color:var(--admin-muted);margin:5px 0 0;line-height:1.6}
.crm-report-methodology summary{cursor:pointer;font-weight:700}
.crm-report-panel--months{grid-column:1/-1}
.crm-report-panel--months svg{max-height:320px}
@media(max-width:1400px){.crm-report-overview{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(min-width:1401px) and (max-width:1750px){.crm-report-tile{padding:18px 14px 0}.crm-report-tile__footer{margin-left:-14px;margin-right:-14px;padding:10px 14px}.crm-report-tile__scope{font-size:9px}.crm-report-tile__icon{width:36px;height:36px;flex-basis:36px;border-radius:11px}.crm-report-tile__watermark{width:42px;flex-basis:42px}.crm-report-tile h2{font-size:13px}}
@media(max-width:600px){.crm-report-welcome{padding:22px 18px;gap:0}.crm-report-welcome h2{font-size:23px}.crm-report-welcome__art{display:none}.crm-report-welcome__stats{gap:18px}.crm-report-welcome__stats>span+span{padding-left:18px}.crm-report-welcome__stats strong{font-size:25px}.crm-report-overview{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.crm-report-tile{padding:16px 8px;gap:6px}.crm-report-tile__icon{width:34px;height:34px}.crm-report-tile__icon svg{width:26px;height:26px}}
.crm-conversion-panel{container-type:inline-size;padding:28px;overflow:hidden}
.crm-conversion-heading{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:26px}
.crm-conversion-heading__title{display:flex;align-items:center;gap:13px;min-width:0}
.crm-conversion-period-icon{display:grid;place-items:center;flex:0 0 44px;width:44px;height:44px;border-radius:13px;background:var(--admin-primary-soft);color:var(--admin-primary-dark)}
.crm-conversion-panel svg{display:block;fill:none;stroke:currentColor;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round}
.crm-conversion-period-icon svg{width:23px;height:23px}
.crm-conversion-eyebrow{display:block;color:var(--admin-muted);font-size:11px;letter-spacing:.09em;text-transform:uppercase;font-weight:700}
.crm-conversion-heading h2{margin:3px 0 0;color:var(--admin-ink);font-size:21px;line-height:1.25;letter-spacing:-.03em}
.crm-conversion-period{border:1px solid var(--admin-line);border-radius:99px;padding:7px 12px;color:var(--admin-muted);font-size:12px;white-space:nowrap;background:var(--admin-canvas)}
.crm-stage-flow{list-style:none;margin:0 0 24px;padding:20px 24px;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));border:1px solid var(--admin-line);border-radius:16px;background:var(--admin-canvas)}
.crm-stage-flow li{position:relative;display:flex;align-items:center;gap:12px;min-width:0}
.crm-stage-flow__icon{display:grid;place-items:center;width:38px;height:38px;flex:0 0 38px;border:1px solid var(--admin-line);border-radius:12px;background:var(--admin-card);color:var(--admin-ink)}
.crm-stage-flow__icon svg{width:21px;height:21px}
.crm-stage-flow__text{display:flex;flex-direction:column;gap:2px;min-width:0}
.crm-stage-flow__text>span{font-size:12px;color:var(--admin-muted)}
.crm-stage-flow__text strong{font-size:24px;line-height:1.2;letter-spacing:-.04em;font-variant-numeric:tabular-nums;color:var(--admin-ink);overflow-wrap:anywhere}
.crm-stage-flow__connector{position:absolute;right:12px;width:36px;height:18px;color:var(--admin-muted);opacity:.65}
.crm-conversion-metrics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
.crm-conversion-metric{padding:20px 16px;border:1px solid var(--admin-line);border-radius:16px;min-width:0;text-align:center;background:var(--admin-card)}
.crm-conversion-metric--total{border-color:var(--admin-primary-soft);background:linear-gradient(160deg,var(--admin-primary-soft),var(--admin-card) 75%)}
.crm-conversion-metric__top{display:flex;justify-content:space-between;align-items:center;gap:8px;text-align:left}
.crm-conversion-metric__label{font-size:12px;font-weight:700;color:var(--admin-muted);line-height:1.5}
.crm-conversion-metric__top>svg{width:19px;height:19px;flex:0 0 19px;color:var(--admin-muted)}
.crm-conversion-metric--total .crm-conversion-metric__top>svg{color:var(--admin-primary-dark)}
.crm-conversion-gauge{position:relative;width:116px;height:116px;margin:18px auto 14px}
.crm-conversion-gauge>svg{width:100%;height:100%;stroke-width:8}
.crm-conversion-gauge__track{stroke:var(--admin-line)}
.crm-conversion-gauge__value{stroke:var(--admin-primary)}
.crm-conversion-gauge__empty{stroke:var(--admin-muted)}
.crm-conversion-gauge__number{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;gap:2px;color:var(--admin-ink);font-variant-numeric:tabular-nums}
.crm-conversion-gauge__number strong{font-size:30px;font-weight:750;letter-spacing:-.065em;line-height:1}
.crm-conversion-gauge__number>span{font-size:14px;margin-top:10px}
.crm-conversion-metric--unavailable .crm-conversion-gauge__track{stroke-dasharray:3 5}
.crm-conversion-metric h3{margin:0;color:var(--admin-ink);font-size:14px;line-height:1.5;font-weight:700;letter-spacing:-.01em}
.crm-conversion-count{margin:10px 0 0;display:flex;align-items:baseline;justify-content:center;gap:5px;flex-wrap:wrap;font-size:12px;color:var(--admin-muted)}
.crm-conversion-count>strong{font-size:21px;color:var(--admin-ink);line-height:1.3;font-variant-numeric:tabular-nums;letter-spacing:-.03em}
.crm-conversion-note{margin:7px auto 0!important;font-size:11px!important;line-height:1.6;color:var(--admin-muted);max-width:25ch}
.crm-conversion-exact{font-weight:700;color:var(--admin-ink);white-space:nowrap}
.crm-conversion-footnote{display:flex;align-items:flex-start;gap:8px;margin-top:18px;color:var(--admin-muted)}
.crm-conversion-footnote svg{flex:0 0 15px;width:15px;height:15px;margin-top:2px}
.crm-conversion-footnote p{margin:0;font-size:11px;line-height:1.6}
@container(max-width:850px){.crm-stage-flow{padding:18px;gap:12px}.crm-stage-flow li{gap:8px}.crm-stage-flow__connector{display:none}.crm-stage-flow__icon{width:32px;height:32px;flex-basis:32px}.crm-stage-flow__text>span{font-size:11px}.crm-stage-flow__text strong{font-size:22px}.crm-conversion-metric{padding:18px 10px}.crm-conversion-gauge{width:100px;height:100px}.crm-conversion-metric__top>svg{display:none}}
@container(max-width:650px){.crm-conversion-metrics{grid-template-columns:repeat(2,minmax(0,1fr))}}
@container(max-width:490px){.crm-conversion-heading{align-items:flex-start;flex-direction:column;gap:12px}.crm-conversion-heading h2{font-size:20px}.crm-conversion-period{font-size:11px}.crm-stage-flow{grid-template-columns:repeat(2,minmax(0,1fr));gap:20px 12px}.crm-stage-flow__text>span{font-size:12px}.crm-conversion-metrics{gap:10px}.crm-conversion-metric{padding:14px 10px}.crm-conversion-metric__top{min-height:36px}.crm-conversion-metric__top>svg{display:none}.crm-conversion-metric__label{font-size:11px}.crm-conversion-gauge{width:94px;height:94px;margin:12px auto}.crm-conversion-gauge__number strong{font-size:26px}.crm-conversion-metric h3{font-size:12px}.crm-conversion-count{font-size:11px;gap:3px}.crm-conversion-count>strong{font-size:19px}.crm-conversion-note{font-size:11px!important}}
@media(max-width:600px){.crm-conversion-panel{padding:18px 14px}.crm-conversion-footnote p{font-size:11px}}
@media(max-width:600px){.crm-report-tile{padding:16px 14px 0}.crm-report-tile__top{margin-bottom:14px;gap:5px}.crm-report-tile__icon{width:35px;height:35px;flex-basis:35px;border-radius:11px}.crm-report-tile__icon svg{width:21px;height:21px}.crm-report-tile__scope{padding:3px 5px;font-size:9px}.crm-report-tile h2{font-size:12px;min-height:34px}.crm-report-tile__value>strong{font-size:34px}.crm-report-tile__watermark{width:42px;height:32px;flex-basis:42px}.crm-report-tile__description{font-size:10px;min-height:32px;margin-bottom:12px}.crm-report-tile__footer{margin-left:-14px;margin-right:-14px;padding:10px 14px;font-size:9px;min-height:47px}}
@media(max-width:360px){.crm-report-tile__scope{display:none}.crm-report-tile__watermark{display:none}.crm-report-tile__footer{font-size:10px}}
@media(forced-colors:active){.crm-conversion-gauge__track{stroke:GrayText}.crm-conversion-gauge__value{stroke:Highlight}.crm-conversion-gauge__empty{stroke:CanvasText}.crm-conversion-metric{border-color:CanvasText}}
</style>
@endpush
