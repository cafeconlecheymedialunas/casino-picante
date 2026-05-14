@push('styles')
<style>
    .account-page { padding:42px 0 0; }
    .account-head { display:flex; align-items:end; justify-content:space-between; gap:18px; flex-wrap:wrap; margin-bottom:22px; }
    .account-kicker { color:var(--orange); font-size:11px; font-weight:900; letter-spacing:.16em; text-transform:uppercase; }
    .account-title { font-family:var(--font-display); font-size:48px; line-height:.9; margin:7px 0 0; letter-spacing:.02em; }
    .account-title span { color:var(--orange); }
    .account-grid { display:grid; grid-template-columns:1fr; gap:18px; }
    .account-card { border:1px solid rgba(255,255,255,.09); border-radius:12px; background:linear-gradient(180deg,#150807,#080302); padding:24px; box-shadow:0 18px 50px rgba(0,0,0,.28); }
    .account-card h2 { font-family:var(--font-display); font-size:28px; line-height:1; margin:0 0 16px; letter-spacing:.02em; }
    .account-section-title { font-family:var(--font-display); font-size:22px; line-height:1; margin:0 0 14px; letter-spacing:.02em; color:var(--orange); }
    .account-form-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .account-field.full { grid-column:1 / -1; }
    .account-label { display:block; color:var(--muted); font-size:10px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; margin-bottom:6px; }
    .account-input { width:100%; border:1px solid rgba(255,120,50,.22); border-radius:8px; background:#100706; color:#fff; padding:12px 13px; font:700 13px var(--font-body); outline:none; }
    .account-input:focus { border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }
    .account-error { color:#ff8a8a; font-size:11px; font-weight:800; margin-top:5px; }
    .account-flash { margin-bottom:16px; border:1px solid rgba(37,196,107,.38); border-radius:10px; background:rgba(37,196,107,.1); color:#adffd0; padding:12px 14px; font-size:13px; font-weight:800; }
    .account-flash-ticket { margin-bottom:16px; border:1px solid rgba(255,170,80,.38); border-radius:10px; background:rgba(255,170,80,.1); color:#ffd0a0; padding:12px 14px; font-size:13px; font-weight:800; }

    /* Tabs */
    .account-tabs { display:flex; gap:4px; margin-bottom:20px; flex-wrap:wrap; }
    .account-tab { padding:10px 20px; border-radius:999px; font-size:13px; font-weight:800; cursor:pointer; background:rgba(255,255,255,.04); color:var(--muted); border:1px solid transparent; transition:all .2s; }
    .account-tab:hover { color:var(--orange); background:rgba(255,106,26,.08); }
    .account-tab.active { color:#fff; background:rgba(255,106,26,.18); border-color:rgba(255,106,26,.3); }

    /* Numbers table */
    .numbers-table { width:100%; border-collapse:collapse; }
    .numbers-table th { text-align:left; color:var(--muted); font-size:10px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; padding:8px 12px; border-bottom:1px solid rgba(255,255,255,.08); }
    .numbers-table td { padding:10px 12px; border-bottom:1px solid rgba(255,255,255,.04); font-size:13px; }
    .numbers-table strong { color:var(--orange); font-family:var(--font-mono); font-size:16px; }
    .numbers-table .line-name { color:var(--muted); font-weight:700; }

    /* Bonuses */
    .bonus-item { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 14px; border:1px solid rgba(255,255,255,.06); border-radius:8px; margin-bottom:8px; }
    .bonus-item-info { flex:1; }
    .bonus-item-title { font-weight:800; font-size:14px; margin-bottom:2px; }
    .badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:10px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
    .badge-available { background:rgba(37,196,107,.15); color:#25c46b; }
    .badge-used { background:rgba(255,255,255,.08); color:var(--muted); }
    .badge-expired { background:rgba(255,71,87,.12); color:#ff8a8a; }

    /* Ticket form */
    .ticket-select { width:100%; border:1px solid rgba(255,120,50,.22); border-radius:8px; background:#100706; color:#fff; padding:12px 13px; font:700 13px var(--font-body); outline:none; }
    .ticket-select:focus { border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }
    .ticket-textarea { width:100%; border:1px solid rgba(255,120,50,.22); border-radius:8px; background:#100706; color:#fff; padding:12px 13px; font:700 13px var(--font-body); outline:none; resize:vertical; min-height:100px; }
    .ticket-textarea:focus { border-color:var(--orange); box-shadow:0 0 0 3px rgba(255,106,26,.12); }

    /* Tickets list */
    .ticket-item { border:1px solid rgba(255,255,255,.06); border-radius:8px; padding:14px; margin-bottom:8px; }
    .ticket-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; }
    .ticket-code { font-family:var(--font-mono); font-weight:900; font-size:13px; color:var(--orange); }
    .ticket-status { font-size:11px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; padding:3px 10px; border-radius:999px; }
    .ticket-status-open { background:rgba(255,106,26,.15); color:var(--orange); }
    .ticket-status-progress { background:rgba(255,170,80,.15); color:#ffaa50; }
    .ticket-status-closed { background:rgba(37,196,107,.15); color:#25c46b; }

    @media (max-width: 640px) {
        .account-form-grid { grid-template-columns:1fr; }
        .numbers-table th, .numbers-table td { padding:6px 8px; font-size:12px; }
    }
</style>
@endpush