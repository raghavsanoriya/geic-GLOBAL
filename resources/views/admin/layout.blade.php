<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'Trans Globe Indore LMS')</title>
    <style>
        @font-face{font-family:Gilroy;src:url("{{ asset('store/1/fonts/Gilroy-Regular.woff2') }}") format('woff2');font-style:normal;font-weight:400;font-display:swap}
        @font-face{font-family:Gilroy;src:url("{{ asset('store/1/fonts/Gilroy-Medium.woff2') }}") format('woff2');font-style:normal;font-weight:500;font-display:swap}
        @font-face{font-family:Gilroy;src:url("{{ asset('store/1/fonts/Gilroy-Bold.woff2') }}") format('woff2');font-style:normal;font-weight:700 900;font-display:swap}
        :root{--admin-primary:#e5242e;--admin-primary-dark:#bc1625;--admin-primary-soft:#fff0f1;--admin-ink:#2a3547;--admin-muted:#6c7894;--admin-line:#e8edf5;--admin-canvas:#f6f8fc;--admin-card:#fff;--admin-sidebar:264px}*{box-sizing:border-box}html{background:var(--admin-canvas)}body{min-width:320px;margin:0;background:var(--admin-canvas);color:var(--admin-ink);font:14px/1.5 'Plus Jakarta Sans',system-ui,sans-serif}a{color:inherit;text-decoration:none}button,input,select,textarea{font:inherit}button{cursor:pointer}.admin-shell{display:grid;grid-template-columns:var(--admin-sidebar) minmax(0,1fr);min-height:100dvh}.admin-sidebar{position:sticky;z-index:20;top:0;display:flex;min-height:100dvh;flex-direction:column;padding:24px 16px 18px;border-right:1px solid var(--admin-line);background:#fff}.admin-brand{display:flex;align-items:center;gap:10px;padding:0 10px;color:#1c2940;font-size:15px;font-weight:800;letter-spacing:-.04em}.admin-brand__mark{display:grid;width:34px;height:34px;place-items:center;border-radius:11px;background:linear-gradient(145deg,#e5242e,#c91928);color:#fff;box-shadow:0 9px 18px rgba(229,36,46,.26)}.admin-brand__mark svg{width:20px;height:20px}.admin-brand__copy{display:grid;line-height:1.05}.admin-brand__copy small{margin-top:3px;color:#7b8aa7;font-size:8px;font-weight:800;letter-spacing:.11em;text-transform:uppercase}.admin-sidebar__section{margin:38px 10px 8px;color:#a7b1c4;font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.admin-nav{display:grid;gap:4px}.admin-nav a{display:flex;min-height:46px;align-items:center;gap:12px;padding:0 12px;border-radius:10px;color:#53627c;font-size:13px;font-weight:700;transition:.2s ease}.admin-nav a svg{width:19px;height:19px;flex:0 0 19px;fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.8}.admin-nav a:hover,.admin-nav a[aria-current=page]{background:var(--admin-primary);color:#fff;box-shadow:0 8px 16px rgba(229,36,46,.22)}.admin-nav a:hover:not([aria-current=page]){background:var(--admin-primary-soft);color:var(--admin-primary-dark);box-shadow:none}.admin-sidebar__footer{margin:20px 4px 0;padding:16px;border-radius:14px;background:#fff0f1}.admin-sidebar__footer strong{display:block;color:#34496d;font-size:12px}.admin-sidebar__footer p{margin:5px 0 12px;color:#7583a0;font-size:10px;line-height:1.55}.admin-sidebar__footer a{display:inline-flex;align-items:center;gap:5px;color:var(--admin-primary-dark);font-size:11px;font-weight:800}.admin-sidebar__account{display:flex;align-items:center;gap:10px;margin-top:auto;padding:16px 9px 3px;border-top:1px solid var(--admin-line)}.avatar{display:grid;width:34px;height:34px;place-items:center;flex:0 0 34px;border-radius:50%;background:#fff1ea;color:#f06a4a;font-size:12px;font-weight:800}.admin-sidebar__account strong{display:block;overflow:hidden;max-width:145px;text-overflow:ellipsis;white-space:nowrap;font-size:11px}.admin-sidebar__account span{display:block;margin-top:1px;color:#93a0b8;font-size:10px}.admin-main{min-width:0}.admin-topbar{position:sticky;z-index:15;top:0;display:flex;min-height:74px;align-items:center;justify-content:space-between;padding:0 34px;border-bottom:1px solid var(--admin-line);background:rgba(255,255,255,.92);backdrop-filter:blur(12px)}.topbar-left,.topbar-actions{display:flex;align-items:center;gap:12px}.menu-button,.topbar-icon{display:inline-grid;width:38px;height:38px;place-items:center;border:0;border-radius:10px;background:transparent;color:#52627e}.menu-button{display:none}.menu-button:hover,.topbar-icon:hover{background:var(--admin-primary-soft);color:var(--admin-primary-dark)}.menu-button svg,.topbar-icon svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.8}.admin-crumb{font-size:15px;font-weight:800;letter-spacing:-.025em}.admin-crumb small{margin-left:7px;color:#a2aec0;font-size:11px;font-weight:600}.topbar-site{display:inline-flex;min-height:38px;align-items:center;gap:7px;padding:0 12px;border:1px solid var(--admin-line);border-radius:10px;background:#fff;color:#55637e;font-size:11px;font-weight:800}.topbar-site:hover{border-color:#facbd0;color:var(--admin-primary-dark)}.topbar-site svg{width:15px;height:15px;fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-width:2}.sign-out{border:0;background:transparent;color:#76849e;font-size:11px;font-weight:800}.sign-out:hover{color:#ee5c5c}.admin-content{padding:29px 34px 42px}.page-head{display:flex;align-items:end;justify-content:space-between;margin-bottom:23px;gap:24px}.eyebrow{display:block;color:var(--admin-primary);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}.page-head h1{margin:4px 0 0;color:var(--admin-ink);font-size:26px;letter-spacing:-.055em;line-height:1.15}.page-head p{max-width:480px;margin:0;color:#8794ad;font-size:12px;text-align:right}.grid{display:grid;gap:18px}.panel,.content-card,.media-card{border:1px solid var(--admin-line);border-radius:16px;background:var(--admin-card);box-shadow:0 4px 14px rgba(42,53,71,.025)}.panel__head{display:flex;align-items:center;justify-content:space-between;padding:19px 20px;border-bottom:1px solid var(--admin-line);gap:12px}.panel__head h2,.side-panel h2{margin:0;font-size:14px;letter-spacing:-.035em}.panel__head span,.side-panel>p{color:#929eb4;font-size:10px}.button{display:inline-flex;min-height:39px;align-items:center;justify-content:center;gap:7px;padding:0 13px;border:1px solid transparent;border-radius:9px;background:var(--admin-primary);color:#fff;font-size:11px;font-weight:800;transition:.2s ease}.button:hover{background:var(--admin-primary-dark);box-shadow:0 7px 14px rgba(229,36,46,.2)}.button--quiet{border-color:#dbe3ef;background:#fff;color:#4d5e7d}.button--quiet:hover{background:var(--admin-primary-soft);color:var(--admin-primary-dark);box-shadow:none}.filters{display:grid;grid-template-columns:minmax(180px,1fr) 150px auto auto;padding:15px 20px;border-bottom:1px solid var(--admin-line);gap:9px}.input,.select{width:100%;min-height:40px;padding:0 12px;border:1px solid #dce3ee;border-radius:9px;outline:none;background:#fff;color:var(--admin-ink);font-size:12px}.input:focus,.select:focus{border-color:var(--admin-primary);box-shadow:0 0 0 3px rgba(229,36,46,.13)}textarea.input{min-height:130px;padding:11px;resize:vertical}.table-wrap{overflow-x:auto}table{width:100%;min-width:730px;border-collapse:collapse}th{padding:12px 20px;background:#fbfcff;color:#8d9bb2;font-size:9px;font-weight:800;letter-spacing:.08em;text-align:left;text-transform:uppercase}td{padding:15px 20px;border-top:1px solid var(--admin-line);color:#65738e;font-size:11px}.student{display:block;color:#2a3547;font-size:12px;font-weight:800}.sub{display:block;margin-top:2px;color:#99a5b9;font-size:10px}.pill{display:inline-flex;min-height:23px;align-items:center;padding:3px 8px;border-radius:99px;background:var(--admin-primary-soft);color:var(--admin-primary-dark);font-size:10px;font-weight:800}.empty{padding:55px 22px;color:#95a7c1;text-align:center}.empty h3{margin:0;color:var(--admin-ink);font-size:14px}.empty p{margin:6px auto 0;font-size:11px}.pagination{display:flex;justify-content:flex-end;padding:14px 20px}.content-pages{grid-template-columns:repeat(3,minmax(0,1fr))}.content-card{display:flex;min-height:180px;flex-direction:column;padding:20px}.content-card__type{color:var(--admin-primary);font-size:9px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.content-card h2{margin:7px 0 0;font-size:16px;letter-spacing:-.035em}.content-card p{margin:7px 0 17px;color:#8a98b0;font-size:11px}.content-card__foot{display:flex;align-items:center;justify-content:space-between;margin-top:auto;gap:10px}.content-card__foot span{color:#9dadc3;font-size:10px;font-weight:700}.editor{display:grid;grid-template-columns:minmax(0,1fr) 270px;align-items:start;gap:18px}.editor__fields{display:grid;gap:15px;padding:22px}.field{display:grid;gap:6px}.field label{font-size:11px;font-weight:800}.field small{color:#95a6bf;font-size:10px}.editor__hint{padding:20px}.editor__hint h2{margin:0;font-size:14px}.editor__hint p{color:#91a1bb;font-size:11px}.media-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:15px}.media-card{overflow:hidden}.media-card img{display:block;width:100%;aspect-ratio:1.4;object-fit:cover;background:#edf2f8}.media-card__body{padding:11px}.media-card code{display:block;overflow-wrap:anywhere;color:#587094;font-size:9px}.notice{margin-bottom:17px;padding:12px 14px;border:1px solid #bfead8;border-radius:11px;background:#f0fdf7;color:#187654;font-size:11px;font-weight:700}@media(max-width:1050px){:root{--admin-sidebar:226px}.content-pages,.media-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.editor{grid-template-columns:1fr}}@media(max-width:760px){.admin-shell{display:block}.admin-sidebar{position:fixed;z-index:50;top:0;bottom:0;left:0;width:min(278px,86vw);min-height:100dvh;transform:translateX(-105%);box-shadow:18px 0 45px rgba(27,42,75,.16);transition:transform .25s ease}.admin-shell.nav-open .admin-sidebar{transform:translateX(0)}.admin-shell.nav-open:after{position:fixed;z-index:40;inset:0;background:rgba(22,35,59,.32);content:''}.menu-button{display:inline-grid}.admin-topbar{min-height:62px;padding:0 14px}.admin-crumb small,.topbar-site span,.topbar-actions .topbar-icon{display:none}.topbar-site{width:38px;padding:0;justify-content:center}.admin-content{padding:21px 14px 34px}.page-head{display:block}.page-head p{margin-top:8px;text-align:left}.content-pages,.media-grid{grid-template-columns:1fr}.filters{grid-template-columns:1fr;padding:14px}.panel__head{padding:16px 14px}.admin-sidebar__footer{margin-top:24px}}
        .admin-sidebar{height:100dvh;min-height:0;align-self:start;overflow-y:auto;overscroll-behavior:contain;scrollbar-width:thin;scrollbar-color:#dbe3ef transparent}
        .admin-sidebar__section{margin-top:24px}
        .admin-sidebar__account{margin-top:12px}
        .admin-sidebar__account .avatar{display:grid;margin-top:0;color:#f06a4a}
        .admin-sidebar__collapse{position:fixed;z-index:30;top:50%;left:calc(var(--admin-sidebar) - 26px);display:grid;width:26px;height:42px;min-height:42px;place-items:center;margin:0;padding:0;border:0;border-radius:10px 0 0 10px;background:var(--admin-primary);color:#fff;box-shadow:0 6px 15px rgba(229,36,46,.24);transform:translateY(-50%);touch-action:none;cursor:ew-resize;transition:left .22s ease,background-color .2s ease,box-shadow .2s ease}
        .admin-sidebar__collapse:hover,.admin-sidebar__collapse.is-dragging{background:var(--admin-primary-dark);box-shadow:0 11px 25px rgba(188,22,37,.38)}
        .admin-sidebar__collapse:focus-visible{outline:3px solid rgba(229,36,46,.24);outline-offset:3px}
        .admin-sidebar__collapse svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-width:2;transition:transform .2s ease}
        .admin-sidebar__collapse span{position:absolute;width:1px;height:1px;overflow:hidden;margin:-1px;padding:0;border:0;clip:rect(0 0 0 0);white-space:nowrap}
        .admin-nav__label{white-space:nowrap}
        @media(min-width:761px){.admin-shell{transition:grid-template-columns .22s ease}.admin-sidebar{position:fixed;top:0;bottom:0;left:0;width:var(--admin-sidebar)}.admin-main{grid-column:2}.admin-shell.sidebar-collapsed{grid-template-columns:82px minmax(0,1fr)}.admin-shell.sidebar-collapsed .admin-sidebar{width:82px;padding-right:12px;padding-left:12px}.admin-shell.sidebar-collapsed .admin-brand{justify-content:center;padding:0}.admin-shell.sidebar-collapsed .admin-brand__copy,.admin-shell.sidebar-collapsed .admin-sidebar__section,.admin-shell.sidebar-collapsed .admin-sidebar__account>span:last-child{position:absolute;width:1px;height:1px;overflow:hidden;margin:-1px;padding:0;border:0;clip:rect(0 0 0 0);white-space:nowrap}.admin-shell.sidebar-collapsed .admin-nav a{justify-content:center;padding:0}.admin-shell.sidebar-collapsed .admin-nav__label{position:absolute;width:1px;height:1px;overflow:hidden;margin:-1px;padding:0;border:0;clip:rect(0 0 0 0);white-space:nowrap}.admin-shell.sidebar-collapsed .admin-sidebar__section+.admin-nav{margin-top:16px}.admin-shell.sidebar-collapsed .admin-sidebar__footer{display:none}.admin-shell.sidebar-collapsed .admin-sidebar__account{justify-content:center;padding-right:0;padding-left:0}.admin-shell.sidebar-collapsed .admin-sidebar__collapse{left:56px}.admin-shell.sidebar-collapsed .admin-sidebar__collapse svg{transform:rotate(180deg)}}
        @media(max-width:760px){.admin-sidebar__collapse{display:none}.admin-sidebar{height:100dvh}.admin-sidebar__account{margin-top:12px}}
    </style>
    @stack('styles')
    <style>
        /* Rocket LMS dashboard architecture adapted to the GEIC brand. Scoped
           to this authenticated admin layout; public and landing views do not
           load these rules. */
        :root{
            --admin-primary:#e31e24;
            --admin-primary-dark:#c6161c;
            --admin-primary-soft:#fff0f1;
            --admin-hover:#f3951e;
            --admin-ink:#0e2145;
            --admin-muted:#62718a;
            --admin-subtle:#cdd5e2;
            --admin-line:#e9edf3;
            --admin-canvas:#f5f8f9;
            --admin-card:#fff;
            --admin-sidebar:258px;
            --admin-header:70px;
            --admin-control:48px;
            --admin-radius-button:8px;
            --admin-radius-control:12px;
            --admin-radius-card:24px;
            --field-label-bg:#fff;
        }
        html{background:var(--admin-canvas)}
        body{background:var(--admin-canvas);color:var(--admin-ink);font:14px/1.5 Gilroy,Inter,system-ui,sans-serif}
        button,input,select,textarea{font:inherit}
        :where(a,button,input,select,textarea,[tabindex]):focus-visible{outline:3px solid rgba(227,30,36,.24);outline-offset:3px}
        .admin-shell{grid-template-columns:var(--admin-sidebar) minmax(0,1fr)}
        .admin-sidebar{width:var(--admin-sidebar);padding:0 0 18px;border-right:0;background:#fff;scrollbar-color:var(--admin-subtle) transparent}
        .admin-brand{position:sticky;z-index:2;top:0;min-height:92px;justify-content:center;padding:12px 20px;border-bottom:1px solid var(--admin-line);background:#fff;color:var(--admin-ink)}
        .admin-brand__full{display:block;width:min(210px,100%);height:auto;max-height:64px;object-fit:contain}
        .admin-brand__mark{display:none;width:44px;height:44px;flex:0 0 44px;place-items:center;border-radius:12px;background:#fff;box-shadow:0 7px 18px rgba(21,81,157,.12)}
        .admin-brand__mark img{display:block;width:36px;height:36px;object-fit:contain}
        .admin-sidebar__section{margin:24px 0 8px;padding:0 20px 0 32px;color:var(--admin-muted);font-size:12px;letter-spacing:.08em}
        .admin-nav{gap:0}
        .admin-nav a{position:relative;min-height:40px;padding:0 20px 0 32px;border-radius:0;color:var(--admin-muted);font-size:14px;font-weight:400;transition:background-color .18s ease,color .18s ease}
        .admin-nav a svg{width:20px;height:20px;flex-basis:20px;stroke-width:1.8}
        .admin-nav a:hover:not([aria-current=page]){background:var(--admin-hover);color:var(--admin-ink);box-shadow:none}
        .admin-nav a[aria-current=page]{background:var(--admin-primary-soft);color:var(--admin-primary);box-shadow:none;font-weight:500}
        .admin-nav a[aria-current=page]:before{position:absolute;inset:7px auto 7px 0;width:3px;border-radius:0 4px 4px 0;background:currentColor;content:''}
        .admin-sidebar__footer{margin:24px 20px 0;padding:16px;border:1px solid #ffe0e2;border-radius:16px;background:#fff8f8}
        .admin-sidebar__footer strong{color:var(--admin-ink);font-size:13px}
        .admin-sidebar__footer p{color:var(--admin-muted);font-size:11px}
        .admin-sidebar__footer a{color:var(--admin-primary);font-size:12px}
        .admin-sidebar__footer a:hover{color:#985600}
        .admin-sidebar__account{min-height:66px;margin:16px 20px 0;padding:14px 0 0;border-color:var(--admin-line)}
        .admin-sidebar__account .avatar{width:44px;height:44px;flex-basis:44px;border:4px solid var(--admin-canvas);background:var(--admin-primary-soft);color:var(--admin-primary);font-size:14px}
        .admin-sidebar__account strong{color:var(--admin-ink);font-size:13px}
        .admin-sidebar__account span{color:var(--admin-muted);font-size:11px}
        .admin-sidebar__collapse{left:calc(var(--admin-sidebar) - 26px);background:var(--admin-primary);box-shadow:0 6px 15px rgba(227,30,36,.22)}
        .admin-sidebar__collapse:hover,.admin-sidebar__collapse.is-dragging{background:var(--admin-hover);color:var(--admin-ink);box-shadow:0 11px 25px rgba(243,149,30,.26)}
        .admin-topbar{min-height:var(--admin-header);padding:0 30px;border-color:var(--admin-line);background:#fff;backdrop-filter:none}
        .admin-crumb{color:var(--admin-ink);font-size:16px;font-weight:700}
        .admin-crumb small{color:var(--admin-muted);font-size:12px;font-weight:400}
        .menu-button,.topbar-icon{width:44px;height:44px;border-radius:10px;color:var(--admin-muted)}
        .topbar-back{display:inline-grid;width:44px;height:44px;place-items:center;border:1px solid var(--admin-line);border-radius:10px;background:#fff;color:var(--admin-muted)}
        .topbar-back svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.8}
        .topbar-back:hover{border-color:var(--admin-hover);background:var(--admin-hover);color:var(--admin-ink)}
        .menu-button:hover,.topbar-icon:hover{background:var(--admin-hover);color:var(--admin-ink)}
        .topbar-site{min-height:44px;padding:0 14px;border-color:var(--admin-line);border-radius:10px;color:var(--admin-muted);font-size:13px;font-weight:500}
        .topbar-site:hover{border-color:var(--admin-hover);background:var(--admin-hover);color:var(--admin-ink)}
        .sign-out{min-height:44px;padding:0 8px;color:var(--admin-muted);font-size:13px;font-weight:500}
        .sign-out:hover{color:#985600}
        .admin-content{padding:20px 32px 40px}
        .page-head{align-items:center;margin-bottom:20px}
        .eyebrow{color:var(--admin-primary);font-size:10px}
        .page-head h1{margin-top:4px;color:var(--admin-ink);font-size:22px;letter-spacing:-.035em}
        .page-head p{color:var(--admin-muted);font-size:13px}
        .panel,.content-card,.media-card,.dash-panel,.dash-metric{border:0;border-radius:var(--admin-radius-card);background:var(--admin-card);box-shadow:none}
        .panel__head{padding:20px 22px;border-color:var(--admin-line)}
        .panel__head h2,.side-panel h2{color:var(--admin-ink);font-size:14px}
        .panel__head span,.side-panel>p{color:var(--admin-muted);font-size:12px}
        .button,.cms-button{min-height:var(--admin-control);padding:0 20px;border-radius:var(--admin-radius-button);font-size:14px;font-weight:500;white-space:nowrap;flex-shrink:0;transition:background-color .18s ease,border-color .18s ease,color .18s ease,box-shadow .18s ease}
        .button:hover,.cms-button--publish:hover{border-color:var(--admin-hover);background:var(--admin-hover);color:var(--admin-ink);box-shadow:none}
        .button--quiet,.cms-button{border-color:var(--admin-line);background:#fff;color:#52627e}
        .button--quiet:hover,.cms-button:hover:not(:disabled):not(.cms-button--publish){border-color:var(--admin-hover);background:var(--admin-hover);color:var(--admin-ink);box-shadow:none}
        .input,.select{min-height:var(--admin-control);padding:0 16px;border-color:var(--admin-line);border-radius:var(--admin-radius-control);color:var(--admin-ink);font-size:14px}
        .input::placeholder{color:var(--admin-muted)}
        .input:focus,.select:focus{border-color:var(--admin-primary);box-shadow:0 0 0 3px rgba(227,30,36,.11)}
        textarea.input{min-height:150px;padding:14px 16px}
        .field{gap:8px}
        .field label{color:var(--admin-ink);font-size:12px}
        .field:has(> .input,> .select){position:relative;padding-top:7px}
        .field:has(> .input,> .select)>label{position:absolute;z-index:1;top:0;left:12px;padding:0 4px;background:var(--field-label-bg);color:var(--admin-muted);font-weight:400;line-height:14px}
        .field small{color:var(--admin-muted);font-size:11px}
        .notice{padding:14px 16px;border-radius:12px;font-size:13px}
        .filters{grid-template-columns:minmax(220px,1fr) 180px auto auto;padding:16px 22px;border-color:var(--admin-line)}
        th{padding:13px 22px;background:#fafcff;color:var(--admin-muted);font-size:10px}
        td{padding:16px 22px;border-color:var(--admin-line);color:#65738e;font-size:13px}
        .student{color:var(--admin-ink);font-size:13px}
        .sub{color:var(--admin-muted);font-size:11px}
        .pill{min-height:26px;background:var(--admin-primary-soft);color:var(--admin-primary);font-size:11px}
        .content-pages{gap:16px}
        .content-card{min-height:200px;padding:22px}
        .content-card__type{color:var(--admin-primary);font-size:10px}
        .content-card h2{color:var(--admin-ink);font-size:17px}
        .content-card p{color:var(--admin-muted);font-size:12px}
        .content-card__foot span{color:var(--admin-muted);font-size:11px}
        .media-grid{gap:16px}
        .media-card{overflow:hidden}
        .media-card__body{padding:14px}
        .cms-workflow{gap:20px}
        .cms-editor__top{padding:20px 22px}
        .cms-editor__top h2{color:var(--admin-ink);font-size:14px}
        .cms-editor__top p{color:var(--admin-muted);font-size:12px}
        .cms-stepper{padding:18px 22px;background:#fafcff}
        .cms-step-panel{padding:22px}
        .cms-section{padding:20px;border-color:var(--admin-line);border-radius:16px;background:#fafcff;--field-label-bg:#fafcff}
        .cms-section legend{color:var(--admin-primary);font-size:10px}
        .cms-actions{padding:18px 22px;background:#fafcff}
        .cms-actions{align-items:stretch;flex-direction:column}
        .cms-actions__left,.cms-actions__right{width:100%}
        .cms-actions__right{justify-content:flex-end}
        .cms-inspector{padding:22px}
        .cms-media-field{border-color:var(--admin-line);border-radius:16px}
        .cms-dropzone,.media-dropzone{border-color:var(--admin-subtle);background:#fafcff}
        .cms-dropzone:hover,.cms-dropzone.is-dragging,.media-dropzone:hover,.media-dropzone.is-dragging{border-color:var(--admin-primary);background:var(--admin-primary-soft);box-shadow:0 0 0 3px rgba(227,30,36,.06)}
        .cms-library-button{min-height:40px;border-color:var(--admin-line);border-radius:8px;color:#52627e;font-size:11px}
        .cms-library-button:hover{border-color:var(--admin-hover);background:var(--admin-hover);color:var(--admin-ink)}
        .cms-media-dialog{border-radius:var(--admin-radius-card)}
        .cms-media-dialog__close{width:44px;height:44px}
        .media-upload,.media-library-help{border-radius:var(--admin-radius-card)}
        .media-library-help__icon{width:48px;height:48px;border-radius:12px;background:var(--admin-primary-soft);color:var(--admin-primary)}
        .dash-metric{min-height:112px;padding:20px 22px}
        .dash-metric__icon{width:48px;height:48px;flex-basis:48px;border-radius:12px;background:var(--admin-primary-soft);color:var(--admin-primary)}
        .dash-panel{overflow:hidden}
        .quick-list a{min-height:48px;border:1px solid transparent;border-radius:12px;background:#fafcff;color:#52627e;font-size:13px;font-weight:500}
        .quick-list a:hover{border-color:var(--admin-hover);background:var(--admin-hover);color:var(--admin-ink)}
        .quick-list span{width:36px;height:36px;border-radius:10px;color:var(--admin-primary);box-shadow:none}
        .quick-list span svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.8}
        .dash-quick h2,.dash-interest h2,.dash-panel__head h2{color:var(--admin-ink);font-size:14px}
        .dash-quick p,.dash-interest>p,.dash-panel__head p{color:var(--admin-muted);font-size:12px}
        .content-groups{padding:8px;border:0;border-radius:var(--admin-radius-card);box-shadow:none}
        .content-group{min-height:48px;border-radius:12px;color:#697791;font-size:13px;font-weight:500}
        .content-group:hover{background:var(--admin-hover);color:var(--admin-ink)}
        .content-group[aria-current=page]{background:var(--admin-primary);color:#fff;box-shadow:none}
        .create-page__form,.create-page__aside{padding:22px}
        .create-page__aside h2{color:var(--admin-ink);font-size:14px}
        .create-page__aside p{color:var(--admin-muted);font-size:12px}
        .create-page__note{border-radius:12px;background:var(--admin-primary-soft);font-size:11px}
        .media-dropzone{min-height:230px;border-radius:16px}
        .media-dropzone strong{color:var(--admin-ink);font-size:14px}
        .media-dropzone p,.media-upload__meta{color:var(--admin-muted);font-size:11px}
        .account-layout{display:grid;grid-template-columns:minmax(0,1.45fr) minmax(260px,.55fr);align-items:start;gap:20px}
        .account-stack{display:grid;gap:20px}
        .account-card{padding:22px}
        .account-card__head{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;gap:16px}
        .account-card__head h2{margin:0;color:var(--admin-ink);font-size:16px}
        .account-card__head p{margin:4px 0 0;color:var(--admin-muted);font-size:12px}
        .account-form{display:grid;gap:18px}
        .account-form__grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
        .account-form__full{grid-column:1/-1}
        .account-actions{display:flex;justify-content:flex-end;gap:10px;padding-top:4px}
        .account-summary{display:grid;gap:16px;padding:22px}
        .account-summary__avatar{display:grid;width:64px;height:64px;place-items:center;border:4px solid var(--admin-canvas);border-radius:50%;background:var(--admin-primary-soft);color:var(--admin-primary);font-size:21px;font-weight:700}
        .account-summary h2{margin:0;color:var(--admin-ink);font-size:16px}
        .account-summary p{margin:4px 0 0;color:var(--admin-muted);font-size:12px}
        .account-facts{display:grid;gap:0;border:1px solid var(--admin-line);border-radius:16px;background:#fafcff}
        .account-fact{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid var(--admin-line);gap:14px;font-size:12px}
        .account-fact:last-child{border-bottom:0}.account-fact span{color:var(--admin-muted)}.account-fact strong{color:var(--admin-ink);text-align:right}
        .access-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;gap:14px}
        .access-toolbar form{display:grid;grid-template-columns:minmax(190px,1fr) 170px auto;flex:1;gap:10px}
        .access-table .avatar{width:40px;height:40px;flex-basis:40px;border:4px solid var(--admin-canvas)}
        .access-user{display:flex;align-items:center;gap:11px}.access-user>div{min-width:0}
        .status-dot{display:inline-flex;align-items:center;gap:7px;color:#16734d;font-size:11px;font-weight:500}.status-dot:before{width:7px;height:7px;border-radius:50%;background:#3fcd82;content:''}.status-dot--inactive{color:#7b879b}.status-dot--inactive:before{background:var(--admin-subtle)}
        .permission-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
        .permission-option{position:relative;display:flex;min-height:94px;align-items:flex-start;padding:16px;border:1px solid var(--admin-line);border-radius:16px;background:#fff;cursor:pointer;gap:12px;transition:border-color .18s ease,background-color .18s ease}
        .permission-option:hover{border-color:var(--admin-hover);background:#fffaf4}
        .permission-option:has(input:checked){border-color:#f4a9ad;background:var(--admin-primary-soft)}
        .permission-option input{width:18px;height:18px;flex:0 0 18px;margin:2px 0 0;accent-color:var(--admin-primary)}
        .permission-option strong,.permission-option span{display:block}.permission-option strong{color:var(--admin-ink);font-size:13px}.permission-option span{margin-top:4px;color:var(--admin-muted);font-size:11px;line-height:1.45}
        .access-switch{display:flex;align-items:center;justify-content:space-between;padding:15px 16px;border:1px solid var(--admin-line);border-radius:16px;background:#fafcff;gap:16px}.access-switch strong,.access-switch span{display:block}.access-switch strong{color:var(--admin-ink);font-size:13px}.access-switch span{margin-top:3px;color:var(--admin-muted);font-size:11px}.access-switch input{width:20px;height:20px;accent-color:var(--admin-primary)}
        .role-preview{padding:15px 16px;border-radius:16px;background:var(--admin-primary-soft);color:#8b3942;font-size:12px}.role-preview strong{display:block;margin-bottom:3px;color:var(--admin-primary)}
        .error-summary{margin-bottom:18px;padding:14px 16px;border:1px solid #f4b5ba;border-radius:12px;background:#fff3f4;color:#9f2029;font-size:12px}.error-summary strong{display:block;margin-bottom:5px;color:#851923}.error-summary ul{margin:0;padding-left:18px}
        @media(max-width:960px){.account-layout{grid-template-columns:1fr}.access-toolbar{align-items:stretch;flex-direction:column}.access-toolbar form{width:100%}}
        @media(max-width:680px){.account-form__grid,.permission-grid{grid-template-columns:1fr}.account-form__full{grid-column:auto}.access-toolbar form{grid-template-columns:1fr}.account-actions{align-items:stretch;flex-direction:column-reverse}.account-actions .button{width:100%}.account-card,.account-summary{padding:18px}}
        @media(min-width:761px){
            .admin-shell.sidebar-collapsed{grid-template-columns:82px minmax(0,1fr)}
            .admin-shell.sidebar-collapsed .admin-sidebar{width:82px;padding-right:0;padding-left:0}
            .admin-shell.sidebar-collapsed .admin-brand{min-height:var(--admin-header);padding:0;justify-content:center}
            .admin-shell.sidebar-collapsed .admin-brand__full{display:none}
            .admin-shell.sidebar-collapsed .admin-brand__mark{display:grid}
            .admin-shell.sidebar-collapsed .admin-nav a{padding:0;justify-content:center}
            .admin-shell.sidebar-collapsed .admin-nav a[aria-current=page]:before{display:none}
            .admin-shell.sidebar-collapsed .admin-sidebar__account{margin-right:14px;margin-left:14px;padding-top:14px}
        }
        @media(max-width:1050px){
            :root{--admin-sidebar:226px}
            .admin-sidebar__section,.admin-nav a{padding-right:18px;padding-left:26px}
        }
        @media(max-width:760px){
            :root{--admin-sidebar:258px}
            .admin-sidebar{width:min(278px,86vw)}
            .admin-topbar{min-height:62px;padding:0 14px}
            .admin-content{padding:20px 14px 34px}
            .page-head h1{font-size:20px}
            .page-head p{font-size:12px}
            .filters{grid-template-columns:1fr;padding:14px}
            .button,.cms-button{min-height:46px}
        }
        @media(prefers-reduced-motion:reduce){*,*:before,*:after{scroll-behavior:auto!important;transition-duration:.01ms!important;animation-duration:.01ms!important;animation-iteration-count:1!important}}
    </style>
</head>
<body>
    <div class="admin-shell" data-admin-shell>
        <aside class="admin-sidebar" aria-label="Admin navigation">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}" aria-label="Trans Globe Indore dashboard">
                <img class="admin-brand__full" src="{{ asset('assets/admin/trans-globe-indore-logo-horizontal.svg') }}" alt="" width="210" height="41">
                <span class="admin-brand__mark" aria-hidden="true">
                    <img src="{{ asset('assets/admin/trans-globe-indore-icon.svg') }}" alt="" width="36" height="36">
                </span>
            </a>
            <span class="admin-sidebar__section">Workspace</span>
            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" title="Dashboard" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg><span class="admin-nav__label">Dashboard</span></a>
                @can('content.manage')<a href="{{ route('admin.pages.index') }}" title="Website content" @if(request()->routeIs('admin.pages.*')) aria-current="page" @endif><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 3h10l4 4v14H5z"/><path d="M14 3v5h5M8 13h8M8 17h6"/></svg><span class="admin-nav__label">Website content</span></a>@endcan
                @can('media.manage')<a href="{{ route('admin.media.index') }}" title="Media library" @if(request()->routeIs('admin.media.*')) aria-current="page" @endif><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="8.5" cy="9" r="1.5"/><path d="m4.5 17 5-5 3 3 2-2 5 4"/></svg><span class="admin-nav__label">Media library</span></a>@endcan
            </nav>
            @canany(['enquiries.view', 'enquiries.export'])<span class="admin-sidebar__section">Lead management</span><nav class="admin-nav">@can('enquiries.view')<a href="{{ route('admin.enquiries.index') }}" title="Student enquiries" @if(request()->routeIs('admin.enquiries.index')) aria-current="page" @endif><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v12H8l-4 4V5Z"/><path d="M8 10h8M8 14h5"/></svg><span class="admin-nav__label">Student enquiries</span></a>@endcan @can('enquiries.export')<a href="{{ route('admin.enquiries.export') }}" title="Export leads" @if(request()->routeIs('admin.enquiries.export')) aria-current="page" @endif><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12M8 11l4 4 4-4M5 20h14"/></svg><span class="admin-nav__label">Export leads</span></a>@endcan</nav>@endcanany
            <span class="admin-sidebar__section">Account</span>
            <nav class="admin-nav">
                @can('users.manage')<a href="{{ route('admin.users.index') }}" title="Team access" @if(request()->routeIs('admin.users.*')) aria-current="page" @endif><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="7" r="4"/><path d="M17 8v6M14 11h6"/></svg><span class="admin-nav__label">Team access</span></a>@endcan
                <a href="{{ route('admin.profile.edit') }}" title="My profile" @if(request()->routeIs('admin.profile.*')) aria-current="page" @endif><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg><span class="admin-nav__label">My profile</span></a>
                <a href="{{ route('admin.settings.edit') }}" title="Settings" @if(request()->routeIs('admin.settings.*')) aria-current="page" @endif><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.83 2.83-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21H9.6v-.09A1.7 1.7 0 0 0 8.6 19.4a1.7 1.7 0 0 0-1.88.34l-.06.06-2.83-2.83.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.1-.4H3V9.6h.09A1.7 1.7 0 0 0 4.6 8.6a1.7 1.7 0 0 0-.34-1.88l-.06-.06 2.83-2.83.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1.1V3h4v.09A1.7 1.7 0 0 0 15.4 4.6a1.7 1.7 0 0 0 1.88-.34l.06-.06 2.83 2.83-.06.06A1.7 1.7 0 0 0 19.4 9c.12.37.33.7.6 1 .3.27.68.4 1.1.4H21v4h-.09a1.7 1.7 0 0 0-1.51.6Z"/></svg><span class="admin-nav__label">Settings</span></a>
            </nav>
            <div class="admin-sidebar__footer"><strong>Keep content current</strong><p>Update pages and images from one protected, easy-to-use workspace.</p><a href="{{ route('admin.pages.index') }}">Manage website <span aria-hidden="true">→</span></a></div>
            <a class="admin-sidebar__account" href="{{ route('admin.profile.edit') }}"><span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->adminRoleLabel() }}</span></span></a>
        </aside>
        <button class="admin-sidebar__collapse" type="button" data-sidebar-collapse aria-label="Collapse sidebar" aria-expanded="true"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m13 7-5 5 5 5"/><path d="m18 7-5 5 5 5"/></svg><span>Collapse sidebar</span></button>
        <div class="admin-main"><header class="admin-topbar"><div class="topbar-left"><button class="menu-button" type="button" data-side-toggle aria-label="Open navigation" aria-expanded="false"><svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>@hasSection('backUrl')<a class="topbar-back" href="@yield('backUrl')" aria-label="@yield('backLabel', 'Go back')" title="@yield('backLabel', 'Go back')"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg></a>@endif<span class="admin-crumb">@yield('crumb', 'Dashboard') <small>Trans Globe Indore LMS</small></span></div><div class="topbar-actions"><a class="topbar-site" href="{{ url('/') }}" target="_blank" rel="noopener"><svg viewBox="0 0 24 24"><path d="M14 4h6v6M20 4l-9 9"/><path d="M19 14v5H5V5h5"/></svg><span>View website</span></a><button class="topbar-icon" type="button" aria-label="Notifications" title="Notifications"><svg viewBox="0 0 24 24"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg></button><form method="post" action="{{ route('admin.logout') }}">@csrf<button class="sign-out" type="submit">Sign out</button></form></div></header><main class="admin-content" id="admin-main-content" tabindex="-1">@yield('content')</main></div>
    </div>
    <script>(() => { const shell = document.querySelector('[data-admin-shell]'); const toggle = document.querySelector('[data-side-toggle]'); const collapse = document.querySelector('[data-sidebar-collapse]'); if (!shell) return; const storageKey = 'geic-admin-sidebar-collapsed'; const serverDefault = @json((bool) (auth()->user()->admin_preferences['sidebar_collapsed'] ?? false)); const setCollapsed = collapsed => { shell.classList.toggle('sidebar-collapsed', collapsed); if (collapse) { collapse.setAttribute('aria-expanded', String(!collapsed)); collapse.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar'); collapse.querySelector('span').textContent = collapsed ? 'Expand sidebar' : 'Collapse sidebar'; } }; const persistCollapsed = collapsed => { setCollapsed(collapsed); try { localStorage.setItem(storageKey, String(collapsed)); } catch (error) {} }; try { const saved = localStorage.getItem(storageKey); setCollapsed(saved === null ? serverDefault : saved === 'true'); } catch (error) { setCollapsed(serverDefault); } if (collapse) { let dragStartX = null; let dragged = false; collapse.addEventListener('pointerdown', event => { dragStartX = event.clientX; dragged = false; collapse.classList.add('is-dragging'); collapse.setPointerCapture?.(event.pointerId); }); collapse.addEventListener('pointermove', event => { if (dragStartX !== null && Math.abs(event.clientX - dragStartX) > 6) dragged = true; }); collapse.addEventListener('pointerup', event => { if (dragStartX === null) return; const distance = event.clientX - dragStartX; dragStartX = null; collapse.classList.remove('is-dragging'); if (Math.abs(distance) > 24) persistCollapsed(distance < 0); }); collapse.addEventListener('pointercancel', () => { dragStartX = null; dragged = false; collapse.classList.remove('is-dragging'); }); collapse.addEventListener('click', () => { if (dragged) { dragged = false; return; } persistCollapsed(!shell.classList.contains('sidebar-collapsed')); }); } if (toggle) toggle.addEventListener('click', () => { const open = shell.classList.toggle('nav-open'); toggle.setAttribute('aria-expanded', String(open)); toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation'); }); })();</script>
    @stack('scripts')
</body>
</html>
