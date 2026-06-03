@php
    $dashboardTheme = \App\Models\Setting::query()->where('key', 'dashboard_theme')->value('value');
    $dashboardTheme = in_array($dashboardTheme, ['dark', 'light'], true) ? $dashboardTheme : 'dark';
    $siteTitle = \App\Models\Setting::siteTitle();
    $siteLogoUrl = \App\Models\Setting::siteLogoUrl();
    $siteFaviconUrl = \App\Models\Setting::siteFaviconUrl();
@endphp
<!DOCTYPE html>
<html lang="es" data-dashboard-theme="{{ $dashboardTheme }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? $siteTitle.' Dashboard' }}</title>
    <link rel="icon" href="{{ $siteFaviconUrl }}">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --black: #0a0606;
            --black-2: #120909;
            --black-3: #1a0d0d;
            --ink: #2a1414;
            --line: rgba(255,255,255,0.08);
            --line-2: rgba(255,255,255,0.14);
            --line-warm: rgba(255,120,50,0.22);
            --orange: #ff6a1a;
            --orange-2: #ff8a3d;
            --orange-deep: #e6580f;
            --amber: #ffb347;
            --white: #ffffff;
            --muted: rgba(255,255,255,0.62);
            --muted-2: rgba(255,255,255,0.42);
            --good: #25c46b;
            --warn: #ffb347;
            --font-display: 'Bebas Neue', sans-serif;
            --font-body: 'Manrope', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
            --r-xs: 6px;
            --r-sm: 10px;
            --r-md: 14px;
            --r-lg: 20px;
            --r-xl: 28px;
        }
        :root[data-dashboard-theme="light"] {
            --black: #f7f1e8;
            --black-2: #fffaf3;
            --black-3: #ffffff;
            --ink: #fff5e8;
            --line: rgba(42,20,20,0.11);
            --line-2: rgba(42,20,20,0.18);
            --line-warm: rgba(230,88,15,0.22);
            --white: #130908;
            --muted: rgba(19,9,8,0.82);
            --muted-2: rgba(19,9,8,0.68);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        [x-cloak] { display: none !important; }
        html, body { background: var(--black); color: var(--white); font-family: var(--font-body); height: 100%; overflow-x: hidden; }
        
        .dash-shell { display: flex; height: 100vh; width: 100%; max-width: 100vw; overflow-x: hidden; }
        .sidebar { 
            width: 220px; padding: 18px 14px; border-right: 1px solid var(--line); 
            background: linear-gradient(180deg, #0d0707 0%, #0a0606 100%); 
            display: flex; flex-direction: column; gap: 4; flex-shrink: 0; 
            overflow-y: auto;
        }
        .sidebar-logo { padding: 4px 8px 14px; border-bottom: 1px solid var(--line); margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
        .sidebar-logo-img { width: 118px; max-height: 36px; object-fit: contain; object-position: left center; }
        .sidebar-logo-text { font-family: var(--font-display); font-size: 22px; color: var(--orange); }
        .sidebar-section { font-size: 10px; font-weight: 800; letter-spacing: 0.18em; color: var(--orange); margin: 16px 0 8px; padding-left: 8px; }
        .sidebar-item {
            display: flex; align-items: center; gap: 10;
            padding: 8px 10px; border-radius: 8px;
            font-size: 13px; cursor: pointer; transition: all 0.2s; color: var(--muted);
            text-decoration: none;
            margin-bottom: 5px;
        }
        .sidebar-item:hover { background: rgba(255,255,255,0.04); color: #fff; }
        .sidebar-item.active { 
            background: rgba(255,106,26,0.12); color: var(--orange); font-weight: 700; 
            border: 1px solid rgba(255,106,26,0.3); 
        }
        .sidebar-item-icon { font-size: 14px; width: 18px; text-align: center; flex-shrink: 0; margin-right: 4px; }
        .sidebar-spacer { flex: 1; }
        .sidebar-user { 
            padding: 10px; border-radius: 10px; 
            background: rgba(255,255,255,0.03); border: 1px solid var(--line); 
            display: flex; align-items: center; gap: 10; 
        }
        .sidebar-avatar { 
            width: 28px; height: 28px; border-radius: 50%; 
            background: linear-gradient(135deg, var(--orange), var(--amber)); 
            display: flex; align-items: center; justify-content: center; 
            color: #190702; font-weight: 800; font-size: 11px; 
        }
        .sidebar-user-name { font-size: 12px; font-weight: 700; }
        .sidebar-user-role { font-size: 10px; color: var(--muted); }
        
        .main { flex: 1; min-width: 0; display: flex; flex-direction: column; overflow: hidden; }
        
        .btn-primary, .new-client-btn, .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 38px;
            padding: 0 18px;
            border-radius: 9px;
            border: 1px solid rgba(255,106,26,0.55);
            background: linear-gradient(135deg, rgba(255,106,26,0.18), rgba(255,106,26,0.06));
            color: var(--orange);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            font-family: var(--font-body);
            white-space: nowrap;
            flex-shrink: 0;
            text-decoration: none;
        }
        .btn-primary:hover, .new-client-btn:hover, .btn-add:hover {
            background: linear-gradient(135deg, rgba(255,106,26,0.28), rgba(255,106,26,0.14));
            border-color: var(--orange);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(255,106,26,0.22);
        }
        .btn-primary svg, .new-client-btn svg, .btn-add svg { stroke: var(--orange); flex-shrink: 0; }
        
        .page-action-strip { display: flex; justify-content: flex-end; gap: 10px; padding: 12px 28px 0; }
        .btn-ghost {
            background: rgba(255,255,255,0.04); color: #fff;
            border: 1px solid var(--line-2); border-radius: 999px;
            padding: 10px 20px; cursor: pointer; font-size: 12px; transition: all 0.2s;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.08); border-color: var(--orange); }
        
        .hamburger-btn {
            display: none; align-items:center; justify-content:center;
            width:36px; height:36px; border:none; background:rgba(255,255,255,.04);
            border-radius:8px; color:var(--white); font-size:20px; cursor:pointer;
            transition:background .15s; flex-shrink:0;
        }
        .hamburger-btn:hover { background:rgba(255,255,255,.08); }
        .sidebar-overlay {
            display:none; position:fixed; inset:0; background:rgba(0,0,0,.55);
            z-index:98; backdrop-filter:blur(2px);
        }
        .sidebar-close {
            display:none; width:28px; height:28px; align-items:center; justify-content:center;
            background:rgba(255,255,255,.05); border:1px solid var(--line); border-radius:6px;
            color:var(--muted-2); cursor:pointer; margin-left:auto; font-size:13px; flex-shrink:0;
        }
        .sidebar-close:hover { color:#fff; border-color:var(--line-2); }

        @media (max-width: 1024px) {
            .hamburger-btn { display:flex; }
            .sidebar-close { display:flex; }
            .sidebar-overlay.open { display:block; }
            .sidebar {
                position:fixed; top:0; left:0; bottom:0; z-index:99;
                transform:translateX(-100%); transition:transform .25s cubic-bezier(.4,0,.2,1);
                border-right:1px solid var(--line);
                box-shadow:4px 0 24px rgba(0,0,0,.4);
            }
            .sidebar.open { transform:translateX(0); }
            .main-content { padding-top:8px; }
        }
        @media (max-width: 768px) {
            .wrap-content { padding:0 5%; }
        }
        @media (max-width: 480px) {
            .wrap-content { padding:0 6%; }
        }

        .sidebar-line-selector {
            padding: 10px; margin: 4px 0 12px;
            background: rgba(255,106,26,0.06);
            border: 1px solid rgba(255,106,26,0.15);
            border-radius: 12px;
        }
        .sidebar-line-label {
            font-size: 9px; font-weight: 800; letter-spacing: 0.16em;
            color: var(--orange); opacity: 0.8; margin-bottom: 6px; padding-left: 2px;
        }
        .sidebar-line-select {
            width: 100%; background: rgba(255,255,255,0.05);
            border: 1px solid var(--line-2); border-radius: 8px;
            padding: 8px 30px 8px 10px; font-size: 13px; font-weight: 700;
            color: var(--white); cursor: pointer;
            background-position: right 8px center;
        }
        .sidebar-line-select:hover {
            border-color: var(--orange);
            background-color: rgba(255,255,255,0.08);
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--black-2); border-radius: 4px; }
        ::-webkit-scrollbar-thumb { background: var(--orange); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--orange-2); }
        ::-webkit-scrollbar-corner { background: var(--black-2); }
        
        /* Firefox */
        * { scrollbar-width: thin; scrollbar-color: var(--orange) var(--black-2); }
        
        /* Main content padding */
        .main { flex: 1; min-width: 0; display: flex; flex-direction: column; overflow: hidden; }
        .main-content {
            flex: 1;
            min-width: 0;
            max-width: 100%;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0;
        }
        .page-container {
            padding:0 0 20px 0;
            min-height: 100%;
            min-width: 0;
            max-width: 100%;
        }
        .wrap-content { min-width: 0; max-width: 100%; overflow-x: clip; }
        
        /* Ensure all scrollable areas have proper styling */
        .scrollable {
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Global select styles */
        select,
        select.select,
        select.filter-select,
        select.form-input,
        select.form-select,
        select.lns-select,
        select.sidebar-line-select,
        select.contact-type,
        select.repeater-type {
            background-color: var(--black-3);
            color: var(--white);
            border: 1px solid var(--line-2);
            border-radius: var(--r-sm);
            padding: 8px 32px 8px 12px;
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23ff6a1a' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 12px 12px;
            background-origin: content-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        select:hover,
        select.select:hover,
        select.filter-select:hover,
        select.form-input:hover,
        select.form-select:hover,
        select.lns-select:hover,
        select.sidebar-line-select:hover,
        select.contact-type:hover,
        select.repeater-type:hover { border-color: var(--orange); }
        select:focus,
        select.select:focus,
        select.filter-select:focus,
        select.form-input:focus,
        select.form-select:focus,
        select.lns-select:focus,
        select.sidebar-line-select:focus,
        select.contact-type:focus,
        select.repeater-type:focus { outline: none; border-color: var(--orange); box-shadow: 0 0 0 3px rgba(255,106,26,0.15); }

        select option {
            background-color: #120909;
            color: var(--white);
            padding: 8px 12px;
        }
        select option:hover,
        select option:focus {
            background-color: rgba(255,106,26,0.12);
            color: var(--orange-2);
        }
        select option:checked,
        select option[selected] {
            background-color: rgba(255,106,26,0.25);
            color: var(--orange);
        }
        select option:disabled {
            color: var(--muted-2);
        }
        select option[value=""] {
            color: var(--muted-2);
        }

        .page-header { display:flex; flex-direction:column; gap:8px; padding:14px 28px 10px; border-bottom:1px solid var(--line); background:var(--black); position:sticky; top:0; z-index:80; margin-bottom:30px; }
        .page-header-top { display:flex; align-items:center; justify-content:space-between; gap:12px; min-height:38px; }
        .page-title { margin:0; font-family:var(--font-display); font-size:34px; letter-spacing:.03em; line-height:.95; }
        .page-subtitle { margin:3px 0 0; color:var(--muted-2); font-size:12px; }
        .ph-title-block { padding:0 28px 14px; }
        .ph-title { margin:0; font-family:var(--font-display); font-size:28px; letter-spacing:.03em; line-height:.95; }
        .ph-subtitle { margin:3px 0 0; color:var(--muted-2); font-size:12px; }
        @media (max-width: 768px) {
            .ph-title-block { padding:0 12px 10px; }
            .ph-title { font-size:22px; }
            .ph-subtitle { font-size:10px; }
        }
        .page-header-btn { height:38px; padding:0 16px; border-radius:8px; border:1px solid var(--orange); background:var(--orange); color:#190702; font-weight:600; font-size:13px; cursor:pointer; transition:all .15s; }
        .page-header-btn:hover { background:#ff8a4a; border-color:#ff8a4a; }
        .page-header-right { display:flex; align-items:center; gap:10px; flex-shrink:0; margin-left:auto; }
        .header-icon-btn { position:relative; width:38px; height:38px; border-radius:8px; border:1px solid var(--line); background:rgba(255,255,255,.035); color:var(--white); cursor:pointer; font-weight:900; display:flex; align-items:center; justify-content:center; }
        .header-icon-btn b { position:absolute; top:-6px; right:-5px; min-width:18px; height:18px; padding:0 5px; border-radius:999px; background:var(--orange); color:#190702; font-size:10px; display:flex; align-items:center; justify-content:center; }
        .profile-menu, .notification-menu { position:relative; }
        .profile-trigger { height:38px; display:flex; align-items:center; gap:9px; border:1px solid var(--line); border-radius:8px; background:rgba(255,255,255,.035); color:var(--white); padding:4px 9px 4px 4px; cursor:pointer; }
        .header-icon-btn:hover, .profile-trigger:hover { border-color:var(--orange); background:rgba(255,106,26,.1); }
        .profile-trigger img { width:30px; height:30px; border-radius:7px; background:rgba(255,255,255,.08); }
        .profile-trigger strong { display:block; max-width:130px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:12px; }
        .profile-trigger small { display:block; color:var(--muted-2); font-size:10px; margin-top:1px; text-align:left; }

        @media (max-width: 768px) {
            .page-header { padding:10px 12px 8px; gap:6px; }
            .page-title { font-size:22px; }
            .page-subtitle { font-size:10px; }
            .profile-trigger span { display:none; }
            .profile-trigger svg { display:none; }
            .notifications-dropdown { right:-80px; width:300px; }
            .profile-dropdown { right:0; left:auto; }
            .page-header-top { min-height:34px; }
        }
        @media (max-width: 480px) {
            .page-title { font-size:18px; }
            .page-header-right { gap:4px; }
            .header-icon-btn { width:32px; height:32px; font-size:15px; }
            .profile-trigger { padding:2px; }
            .profile-trigger img { width:26px; height:26px; }
            .notifications-dropdown { right:-100px; width:280px; }
        }
        .header-dropdown { position:absolute; top:46px; right:0; min-width:230px; border:1px solid var(--line-2); border-radius:8px; background:#140909; box-shadow:0 18px 45px rgba(0,0,0,.45); z-index:120; overflow:hidden; }
        .profile-dropdown { min-width:200px; }
        .profile-dropdown a, .profile-dropdown button { display:block; width:100%; padding:12px 14px; color:var(--white); background:transparent; border:0; text-align:left; text-decoration:none; font:inherit; font-size:13px; cursor:pointer; }
        .profile-dropdown a:hover, .profile-dropdown button:hover { background:rgba(255,106,26,.12); }
        .notifications-dropdown { width:340px; max-width:calc(100vw - 32px); }
        .dropdown-head { display:flex; justify-content:space-between; align-items:center; padding:12px 14px; border-bottom:1px solid var(--line); gap:8px; }
        .dropdown-head strong { font-size:12px; letter-spacing:.08em; text-transform:uppercase; }
        .settings-link { font-size:11px; color:var(--orange); text-decoration:none; font-weight:700; }
        .settings-link:hover { opacity:0.8; }
        .dropdown-head button { border:0; background:transparent; color:var(--orange); font-size:11px; cursor:pointer; font-weight:800; }
        .dropdown-head button:hover { opacity:0.8; }
        .dropdown-body { max-height:320px; overflow-y:auto; }
        .notification-item { display:grid; grid-template-columns:9px 1fr; gap:10px; padding:11px 14px; color:var(--white); border-bottom:1px solid var(--line); cursor:pointer; transition:background .12s; }
        .notification-item:hover { background:rgba(255,255,255,.03); }
        .notification-item.unread { background:rgba(255,106,26,.08); }
        .notification-item.unread:hover { background:rgba(255,106,26,.14); }
        .notification-dot { width:8px; height:8px; border-radius:999px; margin-top:5px; background:var(--orange); flex-shrink:0; }
        .notification-dot.type-danger { background:#ff4757; }
        .notification-dot.type-success { background:var(--good); }
        .notification-dot.type-warning { background:var(--warn); }
        .notification-content strong, .notification-content a, .notification-content small, .notification-content em { display:block; }
        .notification-content strong, .notification-content a { font-size:12px; color:var(--white); text-decoration:none; font-weight:800; }
        .notification-content a:hover { color:var(--orange); }
        .notification-content small { color:var(--muted); font-size:11px; line-height:1.35; margin-top:2px; }
        .notification-content em { color:var(--muted-2); font-size:10px; margin-top:5px; font-style:normal; }
        .dropdown-empty { padding:22px 14px; color:var(--muted-2); text-align:center; font-size:12px; }
        .image-uploader { display:flex;flex-direction:column;gap:6px; }
        .image-uploader-head { display:flex;align-items:center;gap:8px; }
        .image-uploader-label { font-size:11px;font-weight:800;letter-spacing:.06em;color:var(--muted-2);text-transform:uppercase; }
        .image-uploader-hint { font-size:10px;color:var(--muted);background:rgba(255,255,255,.06);padding:2px 6px;border-radius:4px; }
        .image-uploader-drop { display:flex;align-items:center;justify-content:center;border:1px dashed var(--line-2);border-radius:7px;min-height:80px;cursor:pointer;overflow:hidden;transition:border-color .2s;position:relative; }
        .image-uploader-drop:hover { border-color:var(--orange); }
        .image-uploader-drop img { width:100%;height:80px;object-fit:cover;display:block; }
        .image-uploader-empty { font-size:11px;color:var(--muted);pointer-events:none; }
        .image-uploader-drop input[type=file] { position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%; }
        .image-uploader-actions { display:flex;gap:6px;flex-wrap:wrap; }
        .image-uploader-button { display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;border:1px solid var(--line-2);background:rgba(255,255,255,.05);color:var(--white);transition:background .15s;position:relative;overflow:hidden; }
        .image-uploader-button:hover { background:rgba(255,255,255,.1); }
        .image-uploader-button.danger { border-color:rgba(255,80,80,.3);color:#ff5050; }
        .image-uploader-button.danger:hover { background:rgba(255,80,80,.1); }
        .image-uploader-button input[type=file] { position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%; }
        .image-uploader-loading { color:var(--orange); font-size:11px; font-weight:800; }

        .module-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }
        .module-header-left { display: flex; align-items: baseline; gap: 12px; }
        .module-header-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .module-title { font-family: var(--font-display); font-size: 22px; letter-spacing: .03em; }
        .module-count { font-size: 11px; color: var(--muted-2); white-space: nowrap; }
        .search-input { min-width: 200px; }

        .module-top-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 12px 28px 8px;
            margin-bottom: 4px;
        }

        [data-dashboard-theme="light"] body {
            background:
                radial-gradient(circle at top right, rgba(255, 106, 26, .10), transparent 26rem),
                linear-gradient(180deg, #fffaf3 0%, #f7f1e8 100%);
        }
        [data-dashboard-theme="light"] .sidebar {
            background: linear-gradient(180deg, #fffdf8 0%, #f4eadc 100%);
            box-shadow: 1px 0 0 rgba(42,20,20,.04);
        }
        [data-dashboard-theme="light"] .sidebar-logo-text,
        [data-dashboard-theme="light"] .sidebar-section,
        [data-dashboard-theme="light"] .sidebar-line-label {
            color: var(--orange-deep);
        }
        [data-dashboard-theme="light"] .sidebar-item:hover,
        [data-dashboard-theme="light"] .sidebar-user,
        [data-dashboard-theme="light"] .header-icon-btn,
        [data-dashboard-theme="light"] .profile-trigger,
        [data-dashboard-theme="light"] .settings-sidebar,
        [data-dashboard-theme="light"] .settings-panel {
            background: rgba(255,255,255,.68);
        }
        [data-dashboard-theme="light"] .sidebar-item.active,
        [data-dashboard-theme="light"] .sidebar-line-selector {
            background: rgba(255,106,26,.11);
        }
        [data-dashboard-theme="light"] .page-header {
            background: rgba(255,250,243,.92);
            backdrop-filter: blur(12px);
        }
        [data-dashboard-theme="light"] .header-dropdown {
            background: #fffdf8;
            box-shadow: 0 18px 45px rgba(42,20,20,.12);
        }
        [data-dashboard-theme="light"] .btn-ghost,
        [data-dashboard-theme="light"] .hamburger-btn,
        [data-dashboard-theme="light"] .sidebar-close,
        [data-dashboard-theme="light"] .image-uploader-button {
            background: rgba(255,255,255,.72);
            color: var(--white);
        }
        [data-dashboard-theme="light"] select,
        [data-dashboard-theme="light"] select.select,
        [data-dashboard-theme="light"] select.filter-select,
        [data-dashboard-theme="light"] select.form-input,
        [data-dashboard-theme="light"] select.form-select,
        [data-dashboard-theme="light"] select.lns-select,
        [data-dashboard-theme="light"] select.sidebar-line-select,
        [data-dashboard-theme="light"] select.contact-type,
        [data-dashboard-theme="light"] select.repeater-type,
        [data-dashboard-theme="light"] input,
        [data-dashboard-theme="light"] textarea,
        [data-dashboard-theme="light"] .settings-input {
            background-color: rgba(255,255,255,.78);
            color: var(--white);
        }
        [data-dashboard-theme="light"] select option {
            background-color: #fffaf3;
            color: var(--white);
        }
        [data-dashboard-theme="light"] .notification-item:hover,
        [data-dashboard-theme="light"] .profile-dropdown a:hover,
        [data-dashboard-theme="light"] .profile-dropdown button:hover {
            background: rgba(255,106,26,.10);
        }
        [data-dashboard-theme="light"] .wrap-content {
            color: var(--white);
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            .stat-card,
            .kpi-card,
            .info-box,
            .desc-box,
            .month-card,
            .channel-card,
            .plat-card,
            .agent-row,
            .settings-panel,
            .settings-sidebar,
            .setting-card,
            .notification-settings-content,
            .admin-table,
            .table-card,
            .detail-card,
            .line-card,
            .client-card,
            .bonus-card,
            .ticket-card,
            .raffle-card,
            .post-card,
            .metric-card,
            .chart-card,
            .activity-card,
            .modal-panel,
            .ld-hero-meta,
            .ld-tabs,
            .ld-tab-body,
            .detail-tabs,
            .tab-content,
            .edit-layout,
            .edit-content,
            .avatar-library-current,
            .avatar-library-option,
            .contact-repeater-row,
            .bubble
        ) {
            background: rgba(255,255,255,.82) !important;
            background-image: none !important;
            color: var(--white) !important;
            border-color: var(--line) !important;
            box-shadow: 0 14px 36px rgba(42,20,20,.06);
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            .admin-table,
            table,
            thead,
            tbody,
            tr,
            .t-row,
            .sales-row,
            .client-row,
            .ticket-row,
            .bonus-row,
            .raffle-row,
            .post-row,
            .agent-row,
            .search-result-row
        ) {
            color: var(--white) !important;
            border-color: var(--line) !important;
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            thead,
            .t-row.head,
            .sales-row.head,
            .table-head,
            .admin-table th
        ) {
            background: rgba(244,234,220,.86) !important;
            color: var(--muted) !important;
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            tbody tr,
            .t-row,
            .sales-row,
            .client-row,
            .ticket-row,
            .bonus-row,
            .raffle-row,
            .post-row
        ):hover {
            background: rgba(255,106,26,.08) !important;
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            .form-input,
            .input,
            .search-input,
            .filter-input,
            .settings-input,
            .chat-input,
            .new-chat input,
            .new-chat textarea,
            input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
            textarea,
            select
        ) {
            background: rgba(255,255,255,.9) !important;
            background-image: none !important;
            color: var(--white) !important;
            border-color: var(--line-2) !important;
            box-shadow: none !important;
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            .form-input,
            .input,
            .search-input,
            .filter-input,
            .settings-input,
            .chat-input,
            input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
            textarea,
            select
        ):focus {
            border-color: var(--orange) !important;
            box-shadow: 0 0 0 3px rgba(255,106,26,.14) !important;
            outline: none !important;
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            .modal-panel,
            .header-dropdown,
            .profile-dropdown,
            .notification-menu,
            .notifications-dropdown
        ) {
            background: #fffdf8 !important;
            background-image: none !important;
            color: var(--white) !important;
            border-color: var(--line-2) !important;
            box-shadow: 0 22px 54px rgba(42,20,20,.16) !important;
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            .btn-icon,
            .btn-icon-sm,
            .btn-soft,
            .btn-ghost,
            .modal-close,
            .avatar-library-toggle,
            .image-uploader-button,
            .pg-btn
        ) {
            background: rgba(255,255,255,.78) !important;
            color: var(--white) !important;
            border-color: var(--line) !important;
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            .btn-icon:hover,
            .btn-icon-sm:hover,
            .btn-soft:hover,
            .btn-ghost:hover,
            .modal-close:hover,
            .avatar-library-toggle:hover,
            .image-uploader-button:hover,
            .pg-btn:hover
        ) {
            background: rgba(255,106,26,.12) !important;
            border-color: var(--orange) !important;
            color: var(--orange-deep) !important;
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            .ld-cover,
            .detail-cover,
            .line-cover
        ) {
            background: linear-gradient(135deg, rgba(255,106,26,.18), rgba(255,250,243,.86)) !important;
            border-color: var(--line) !important;
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            .ld-avatar,
            .detail-avatar,
            .table-avatar,
            .agent-row-avatar,
            .result-avatar,
            .avatar-library-preview img,
            .avatar-library-preview .avatar-library-fallback
        ) {
            background: #fffaf3 !important;
            border-color: rgba(230,88,15,.28) !important;
            box-shadow: 0 10px 24px rgba(42,20,20,.12);
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            .role-agent,
            .line-badge,
            .perm-chip-off,
            .ag-perm-off,
            .pg-btn,
            .theme-option
        ) {
            background: rgba(244,234,220,.78) !important;
            color: var(--muted) !important;
            border-color: var(--line) !important;
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            .status-inactive,
            .b-inactive
        ) {
            background: rgba(255,71,87,.10) !important;
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            .status-active,
            .b-active
        ) {
            background: rgba(37,196,107,.10) !important;
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            [style*="#120909"],
            [style*="#140909"],
            [style*="#1c0e0e"],
            [style*="#1c0d0a"],
            [style*="#0a0606"],
            [style*="rgba(255,255,255,.02)"],
            [style*="rgba(255,255,255,.03)"],
            [style*="rgba(255,255,255,.035)"],
            [style*="rgba(255,255,255,.04)"],
            [style*="rgba(255,255,255,.05)"],
            [style*="rgba(255,255,255,.06)"]
        ) {
            background: rgba(255,255,255,.86) !important;
            background-image: none !important;
            color: var(--white) !important;
            border-color: var(--line) !important;
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            [style*="color:#fff"],
            [style*="color: #fff"],
            [style*="color:white"],
            [style*="color: white"],
            [style*="rgba(255,255,255,0.62)"],
            [style*="rgba(255,255,255,.62)"],
            [style*="rgba(255,255,255,0.42)"],
            [style*="rgba(255,255,255,.42)"]
        ) {
            color: var(--white) !important;
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            p,
            small,
            span,
            td,
            th,
            label,
            .module-count,
            .settings-label,
            .settings-panel-subtitle,
            .kpi-label,
            .info-box-label,
            .month-label,
            .plat-meta,
            .channel-val
        ) {
            color: var(--muted) !important;
            border-color: var(--line);
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            .page-subtitle,
            .ph-subtitle,
            .sidebar-user-role,
            .notification-content small,
            .notification-content em,
            .dropdown-empty,
            .module-count,
            .settings-panel-subtitle,
            .theme-copy small,
            .kpi-label,
            .info-box-label,
            .month-label,
            .plat-meta,
            .channel-val,
            .table-sub,
            .t-muted,
            .empty-state,
            .chat-sub,
            .ticket-meta
        ) {
            color: var(--muted-2) !important;
        }
        [data-dashboard-theme="light"] .wrap-content :is(
            h1,
            h2,
            h3,
            h4,
            strong,
            .module-title,
            .settings-panel-title,
            .kpi-val,
            .month-total,
            .channel-name,
            .plat-name,
            .agent-row-name,
            .detail-title,
            .ld-hero-name
        ) {
            color: var(--white) !important;
        }
        [data-dashboard-theme="light"] input::placeholder,
        [data-dashboard-theme="light"] textarea::placeholder {
            color: rgba(19,9,8,.58) !important;
        }
        [data-dashboard-theme="light"] ::-webkit-scrollbar-track { background: #efe5d7; }
        [data-dashboard-theme="light"] * { scrollbar-color: var(--orange) #efe5d7; }
    </style>
</head>
<body data-dashboard-theme="{{ $dashboardTheme }}">
    <div class="dash-shell" x-data="{ sidebarOpen: false }" @toggle-sidebar.window="sidebarOpen = !sidebarOpen">
        <div class="sidebar-overlay" :class="sidebarOpen && 'open'" x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak></div>
        <aside class="sidebar" :class="sidebarOpen && 'open'">
            <div class="sidebar-logo">
                @if($siteLogoUrl)
                <img src="{{ $siteLogoUrl }}" alt="{{ $siteTitle }}" class="sidebar-logo-img">
                @else
                <svg width="20" height="20" viewBox="0 0 40 40">
                    <circle cx="20" cy="22" r="16" fill="rgba(255,106,26,0.35)" opacity="0.6" />
                    <path d="M14 12 C 12 18, 12 26, 18 32 C 24 36, 32 32, 33 24 C 34 18, 28 14, 22 14 C 18 14, 16 13, 14 12 Z" fill="#ff6a1a" />
                </svg>
                <span class="sidebar-logo-text">{{ $siteTitle }}</span>
                @endif
                <button class="sidebar-close" @click="sidebarOpen = false" title="Cerrar menú"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            {{-- Active line selector (only shown when an agent is in session) --}}
            @php
                $sidebarUser = auth()->user();
                $sidebarIsAdmin = $sidebarUser?->hasRole(\App\Support\Roles::ADMIN) ?? false;
                $sidebarIsPanelOwner = $sidebarIsAdmin || ($sidebarUser?->hasRole(\App\Support\Roles::CAJERO) ?? false);
                $activeVendorId = session('active_vendor_id');
                $sidebarVendors = $sidebarIsAdmin
                    ? \App\Models\Vendor::query()->where('is_active', true)->orderByDesc('is_direct')->orderBy('name')->get()
                    : collect();
                $sessionAgentId = session('active_agent_id');
                $sessionLineId  = session('active_line_id');
                $sidebarLineAgent = $currentLineAgent ?? null;
                $sidebarCan = function (array $permissions) use ($sessionAgentId, $sidebarLineAgent): bool {
                    if (! $sessionAgentId) {
                        return true;
                    }

                    if (! $sidebarLineAgent) {
                        return false;
                    }

                    foreach ($permissions as $permission) {
                        if ($sidebarLineAgent->hasPermission($permission)) {
                            return true;
                        }
                    }

                    return false;
                };
                $sidebarCanEditHome = $sidebarCan([\App\Support\Permissions::HOME_EDIT]);
                $allLines = $sessionAgentId
                    ? \App\Models\LineAgent::with('line')
                        ->where('agent_id', $sessionAgentId)
                        ->where('is_active', true)
                        ->get()
                        ->pluck('line')
                    : \App\Models\Line::where('status', 'active')
                        ->when($activeVendorId, fn ($query) => $query->where('vendor_id', (int) $activeVendorId))
                        ->orderBy('name')
                        ->get();
            @endphp
            @if($sidebarIsAdmin && $sidebarVendors->count() > 0)
            <div class="sidebar-line-selector">
                <div class="sidebar-line-label">VENDOR ACTIVO</div>
                <form method="POST" id="vendor-selector-form" action="{{ route('admin.session.vendor', $activeVendorId ?: 0) }}">
                    @csrf
                    <select
                        class="sidebar-line-select"
                        data-route-template="{{ route('admin.session.vendor', ['id' => '__ID__']) }}"
                        onchange="switchContext(this)"
                    >
                        <option value="0" {{ !$activeVendorId ? 'selected' : '' }}>Global - solo lectura</option>
                        @foreach($sidebarVendors as $vendorOption)
                        <option value="{{ $vendorOption->id }}" {{ (int) $activeVendorId === (int) $vendorOption->id ? 'selected' : '' }}>
                            {{ $vendorOption->is_direct ? '★ ' : '' }}{{ $vendorOption->name }}
                        </option>
                        @endforeach
                    </select>
                </form>
            </div>
            @endif

            @if($allLines->count() > 0 && (! $sidebarIsAdmin || $activeVendorId))
            <div class="sidebar-line-selector">
                <div class="sidebar-line-label">LÍNEA ACTIVA</div>
                <form method="POST" id="line-selector-form" action="{{ route('admin.session.line', $sessionLineId ?: 0) }}">
                    @csrf
                    <select
                        class="sidebar-line-select"
                        data-route-template="{{ route('admin.session.line', ['id' => '__ID__']) }}"
                        onchange="switchContext(this)"
                    >
                        @foreach($allLines as $sl)
                        <option value="{{ $sl->id }}" {{ $sessionLineId == $sl->id ? 'selected' : '' }}>
                            {{ $sl->name }}
                        </option>
                        @endforeach
                        @if($sidebarIsPanelOwner)
                        <option value="0" {{ !$sessionLineId ? 'selected' : '' }}>Todas las líneas</option>
                        @endif
                    </select>
                </form>
            </div>
            @endif

            @if($sidebarCan([\App\Support\Permissions::DASHBOARD_READ]))
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="sidebar-item-icon"><i class="fa-solid fa-chart-line"></i></span> Overview
            </a>
            @endif

            @if($sidebarCan([\App\Support\Permissions::USER_READ, \App\Support\Permissions::USER_UPDATE, \App\Support\Permissions::USER_BLOCK]))
            <a href="{{ route('admin.clientes') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.clientes*') ? 'active' : '' }}">
                <span class="sidebar-item-icon"><i class="fa-solid fa-users"></i></span> Clientes
            </a>
            @endif

            @if($sidebarCan([\App\Support\Permissions::AGENT_CREATE, \App\Support\Permissions::AGENT_ASSIGN, \App\Support\Permissions::AGENT_UPDATE, \App\Support\Permissions::AGENT_PERMISSIONS]))
            <a href="{{ route('admin.agentes') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.agentes') ? 'active' : '' }}">
                <span class="sidebar-item-icon"><i class="fa-solid fa-user-tie"></i></span> Agentes
            </a>
            @endif

            @if($sidebarCan([\App\Support\Permissions::LINE_READ, \App\Support\Permissions::LINE_EDIT]))
            <a href="{{ route('admin.lineas') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.lineas*') ? 'active' : '' }}">
                <span class="sidebar-item-icon"><i class="fa-solid fa-layer-group"></i></span> Lineas
            </a>
            @endif


            @if($sidebarIsAdmin && $sidebarCanEditHome)
            @php
                $paginasActive = request()->routeIs('admin.editor.inicio*') || request()->routeIs('admin.editor.paginas*');
            @endphp
            <div x-data="{ open: {{ $paginasActive ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sidebar-item" style="width:100%;border:none;background:none;cursor:pointer;text-align:left;" :class="open ? 'active' : ''">
                    <span class="sidebar-item-icon"><i class="fa-solid fa-file-lines"></i></span>
                    Páginas
                    <i class="fa-solid" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'" style="margin-left:auto;font-size:10px;opacity:0.6;"></i>
                </button>
                <div x-show="open" x-collapse style="padding-left:10px;">
                    <a href="{{ route('admin.editor.inicio') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.editor.inicio*') ? 'active' : '' }}" style="font-size:13px;">
                        <span class="sidebar-item-icon"><i class="fa-solid fa-house-chimney"></i></span> Home
                    </a>
                    <a href="{{ route('admin.editor.paginas', 'sorteos') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.editor.paginas') && request()->route('page') === 'sorteos' ? 'active' : '' }}" style="font-size:13px;">
                        <span class="sidebar-item-icon"><i class="fa-solid fa-dice"></i></span> Sorteos
                    </a>
                    <a href="{{ route('admin.editor.paginas', 'lineas') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.editor.paginas') && request()->route('page') === 'lineas' ? 'active' : '' }}" style="font-size:13px;">
                        <span class="sidebar-item-icon"><i class="fa-solid fa-layer-group"></i></span> Lineas
                    </a>
                    <a href="{{ route('admin.editor.paginas', 'bonos') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.editor.paginas') && request()->route('page') === 'bonos' ? 'active' : '' }}" style="font-size:13px;">
                        <span class="sidebar-item-icon"><i class="fa-solid fa-gift"></i></span> Bonos
                    </a>
                    <a href="{{ route('admin.editor.paginas', 'cajeros') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.editor.paginas') && request()->route('page') === 'cajeros' ? 'active' : '' }}" style="font-size:13px;">
                        <span class="sidebar-item-icon"><i class="fa-solid fa-cash-register"></i></span> Cajeros
                    </a>
                    <a href="{{ route('admin.editor.paginas', 'novedades') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.editor.paginas') && request()->route('page') === 'novedades' ? 'active' : '' }}" style="font-size:13px;">
                        <span class="sidebar-item-icon"><i class="fa-solid fa-newspaper"></i></span> Novedades
                    </a>
                </div>
            </div>
            @endif

            @if($sidebarCan([\App\Support\Permissions::NEWS_READ, \App\Support\Permissions::NEWS_CREATE, \App\Support\Permissions::NEWS_UPDATE, \App\Support\Permissions::NEWS_DELETE]))
            <a href="{{ route('admin.novedades') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.novedades') ? 'active' : '' }}">
                <span class="sidebar-item-icon"><i class="fa-solid fa-newspaper"></i></span> Blog
            </a>
            @endif

            @if($sidebarCan([\App\Support\Permissions::BONO_READ, \App\Support\Permissions::BONO_CREATE, \App\Support\Permissions::BONO_UPDATE, \App\Support\Permissions::BONO_DELETE]))
            <a href="{{ route('admin.bonos') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.bonos*') ? 'active' : '' }}">
                <span class="sidebar-item-icon"><i class="fa-solid fa-gift"></i></span> Bonos
            </a>
            @endif

            @if($sidebarCan([\App\Support\Permissions::LINE_EDIT]))
            <a href="{{ route('admin.ventas') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.ventas*') ? 'active' : '' }}">
                <span class="sidebar-item-icon"><i class="fa-solid fa-dollar-sign"></i></span> Ventas
            </a>
            @endif

            @if($sidebarCan([\App\Support\Permissions::SORTEO_READ, \App\Support\Permissions::SORTEO_CREATE, \App\Support\Permissions::SORTEO_UPDATE, \App\Support\Permissions::SORTEO_DELETE]))
            <a href="{{ route('admin.sorteos') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.sorteos*') ? 'active' : '' }}">
                <span class="sidebar-item-icon"><i class="fa-solid fa-dice"></i></span> Sorteos
            </a>
            @endif

            @if($sidebarCan([\App\Support\Permissions::TICKET_READ, \App\Support\Permissions::TICKET_UPDATE, \App\Support\Permissions::TICKET_CLOSE]))
            <a href="{{ route('admin.tickets') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.tickets*') ? 'active' : '' }}">
                <span class="sidebar-item-icon"><i class="fa-solid fa-ticket"></i></span> Tickets
            </a>
            @endif

            @if($sidebarIsAdmin)
            <a href="{{ route('admin.cajeros') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.cajeros') ? 'active' : '' }}">
                <span class="sidebar-item-icon"><i class="fa-solid fa-cash-register"></i></span> Cajeros
            </a>
            @endif

            @if($sidebarIsPanelOwner)
            <a href="{{ route('admin.configuracion') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.configuracion*') ? 'active' : '' }}">
                <span class="sidebar-item-icon"><i class="fa-solid fa-gear"></i></span> Configuracion
            </a>
            @endif

            <a href="{{ route('admin.perfil') }}" wire:navigate class="sidebar-item {{ request()->routeIs('admin.perfil') ? 'active' : '' }}">
                <span class="sidebar-item-icon"><i class="fa-solid fa-user"></i></span> Mi Perfil
            </a>

            <div class="sidebar-spacer"></div>
        </aside>
        
        <main class="main">
            <div class="main-content">
                @hasSection('header')
                    @yield('header')
                @endif
                @hasSection('page-title-block')
                    @yield('page-title-block')
                @endif
                <div class="wrap-content" style="padding:0 2%;">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
                </div>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    @livewireScripts
    <script>
        function switchContext(select) {
            const form = select?.form;
            const routeTemplate = select?.dataset?.routeTemplate;

            if (! form || ! routeTemplate) {
                return;
            }

            submitContextForm(form, routeTemplate.replace('__ID__', select.value));
        }

        function submitContextForm(form, action) {
            if (! form) {
                return;
            }

            form.action = action;
            form.submit();
        }

        window.addEventListener('dashboard-theme-updated', (event) => {
            const theme = event.detail?.theme === 'light' ? 'light' : 'dark';
            document.documentElement.dataset.dashboardTheme = theme;
            document.body.dataset.dashboardTheme = theme;
        });
    </script>
</body>
</html>
