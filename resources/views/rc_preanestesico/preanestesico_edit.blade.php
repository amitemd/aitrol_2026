@extends('preanestesico.preanestesico')
@section('action-content')

<style type="text/css">
    /* V3 visual layer: the legacy form markup and its field contracts stay intact. */
    .content-wrapper { background: #eef6fa; }
    .preanestesico-v3 {
        --v3-navy: #0b4166;
        --v3-blue: #087fbd;
        --v3-cyan: #20b7c7;
        --v3-ink: #123b5d;
        --v3-muted: #67829a;
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0 0 24px;
        color: var(--v3-ink);
    }

    .preanestesico-v3 .v3-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 22px;
        padding: 24px 28px;
        border-radius: 14px;
        color: #fff;
        background: linear-gradient(110deg, var(--v3-navy), #087ea6 68%, #159f91);
        box-shadow: 0 10px 24px rgba(11, 65, 102, .16);
    }

    .preanestesico-v3 .v3-hero h1 { margin: 0 0 6px; font-size: 25px; font-weight: 700; }
    .preanestesico-v3 .v3-hero p { margin: 0; opacity: .9; font-size: 14px; }
    .preanestesico-v3 .v3-hero-icon {
        display: inline-flex; align-items: center; justify-content: center;
        width: 54px; height: 54px; margin-right: 14px; border: 1px solid rgba(255,255,255,.35);
        border-radius: 12px; background: rgba(255,255,255,.12); font-size: 25px;
    }
    .preanestesico-v3 .v3-hero-copy { display: flex; align-items: center; }
    .preanestesico-v3 .v3-status {
        min-width: 150px; padding: 10px 16px; border: 1px solid rgba(255,255,255,.35);
        border-radius: 10px; text-align: center; font-size: 11px; text-transform: uppercase;
        letter-spacing: .4px; background: rgba(255,255,255,.1);
    }
    .preanestesico-v3 .v3-status strong { display: block; margin-top: 4px; font-size: 14px; }
    .preanestesico-v3 .v3-jump-nav {
        position: sticky; top: 64px; z-index: 20; display: flex; align-items: center; gap: 8px;
        margin: 0 0 20px; padding: 9px; overflow-x: auto; border: 1px solid #d5e5ed;
        border-radius: 12px; background: rgba(255,255,255,.95); box-shadow: 0 6px 18px rgba(23,78,111,.07);
        backdrop-filter: blur(8px);
    }
    .preanestesico-v3 .v3-jump-nav button {
        display: inline-flex; align-items: center; gap: 7px; padding: 8px 12px; border-radius: 8px;
        border: 0; color: #315b71; background: transparent; font-family: inherit; font-size: 12px;
        font-weight: 700; text-decoration: none; white-space: nowrap; cursor: pointer;
    }
    .preanestesico-v3 .v3-jump-nav button:hover, .preanestesico-v3 .v3-jump-nav button:focus {
        color: #08799c; background: #eaf7f9; outline: 0;
    }
    .preanestesico-v3 .v3-section {
        margin: 18px 0; padding: 20px; border: 1px solid #cde1ed; border-radius: 12px;
        background: #fff; box-shadow: 0 5px 18px rgba(23, 78, 111, .07);
        scroll-margin-top: 128px;
    }
    .content-wrapper > .w3-bar.w3-blue-light {
        display: flex; align-items: center; gap: 8px; margin: 0; padding: 14px 28px;
        border-bottom: 1px solid #cde1ed; background: #fff !important;
    }
    .content-wrapper > .w3-bar.w3-blue-light .btn {
        padding: 10px 16px; border: 1px solid #b9d9e9; border-radius: 8px;
        color: #123b5d !important; background: #fff !important; font-weight: 600;
    }
    .content-wrapper > .w3-bar.w3-blue-light .btn:first-child,
    .content-wrapper > .w3-bar.w3-blue-light .btn:hover { color: #fff !important; border-color: #087fbd; background: #087fbd !important; }
    .content-wrapper > .text-right { margin: 16px 28px 0 !important; }
    .content-wrapper > .text-right .btn { padding: 9px 16px; border-radius: 8px; border: 0; background: #e85d67; }
    .preanestesico-v3 #frm { margin: 18px 0; padding: 20px; border: 1px solid #cde1ed;
        border-radius: 12px; background: #fff; box-shadow: 0 5px 18px rgba(23, 78, 111, .07);
        scroll-margin-top: 128px; }
    .preanestesico-v3 #frm { background: #f9fcfd; }
    .preanestesico-v3 #tabla_diagnostico > table:first-child,
    .preanestesico-v3 #tabla_indicacion > table, .preanestesico-v3 #tabla_examen > table { overflow: hidden; border-radius: 8px; }
    .preanestesico-v3 #tabla_diagnostico input:disabled,.preanestesico-v3 #tabla_indicacion input:disabled,.preanestesico-v3 #tabla_examen input:disabled { color:#315b71; background:#f6fafc; opacity:1; }
    .preanestesico-v3 .v3-form008-antecedent{padding:9px 11px;border:1px solid #bfe0ea;border-radius:8px;color:#214f67;background:#eef9fc;line-height:1.4}.preanestesico-v3 .v3-form008-antecedent__source{display:inline-block;margin-right:8px;padding:3px 7px;border-radius:12px;color:#08799d;background:#d9f3f8;font-size:10px;font-weight:800;text-transform:uppercase}.preanestesico-v3 .v3-form008-antecedent-lock{text-align:center;color:#6d8799}
    .preanestesico-v3 .v3-section-title {
        display: flex; align-items: center; gap: 9px; margin: -20px -20px 18px; padding: 14px 18px;
        border-radius: 12px 12px 0 0; color: #fff; background: linear-gradient(100deg, var(--v3-navy), var(--v3-cyan));
        font-size: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: .2px;
    }
    .preanestesico-v3 table { width: 100% !important; border: 0 !important; border-collapse: separate !important; border-spacing: 0 !important; }
    .preanestesico-v3 td { border-color: #d7e7ef !important; color: var(--v3-ink); }
    .preanestesico-v3 .bg-blue, .preanestesico-v3 .bg-purple, .preanestesico-v3 .bg-green {
        color: #fff !important; background: linear-gradient(100deg, var(--v3-navy), var(--v3-cyan)) !important;
    }
    .preanestesico-v3 .bg-blue b, .preanestesico-v3 .bg-purple b, .preanestesico-v3 .bg-green b { color: #fff !important; }
    .preanestesico-v3 input.form-control, .preanestesico-v3 select.form-control,
    .preanestesico-v3 textarea.form-control, .preanestesico-v3 input:not([type="hidden"]),
    .preanestesico-v3 select, .preanestesico-v3 textarea {
        max-width: 100%; min-height: 38px; border: 1px solid #c9dce9; border-radius: 8px;
        color: var(--v3-ink); background: #fff; box-shadow: none;
    }
    .preanestesico-v3 input:focus, .preanestesico-v3 select:focus, .preanestesico-v3 textarea:focus {
        border-color: var(--v3-cyan); box-shadow: 0 0 0 3px rgba(32,183,199,.13); outline: 0;
    }
    .preanestesico-v3 label, .preanestesico-v3 b { color: var(--v3-ink); }
    .preanestesico-v3 .btn { border-radius: 8px; font-weight: 600; }
    .preanestesico-v3 .btn-primary { border-color: var(--v3-blue); background: var(--v3-blue); }
    .preanestesico-v3 .btn-success, .preanestesico-v3 .bg-green { background: #1fc77a !important; border-color: #1fc77a !important; }
    .preanestesico-v3 .btn-danger { background: #e85d67; border-color: #e85d67; }
    .preanestesico-v3 .box-body { padding: 14px 0; }
    .preanestesico-v3 #frm > table, .preanestesico-v3 #frm > div, .preanestesico-v3 #frm > .box-body { margin-bottom: 14px; }
    .preanestesico-v3 > br, .preanestesico-v3 #frm > br { display: none; }
    .preanestesico-v3 #frm > div[style*="display: flex"] {
        align-items: center; flex-wrap: wrap; gap: 12px 16px !important; margin: 0 0 12px;
        padding: 14px; border: 1px solid #e0edf3; border-radius: 10px; background: #fff;
    }
    .preanestesico-v3 #frm > div[style*="display: flex"] > div[style*="margin-right"] { display: none; }
    .preanestesico-v3 #frm > div[style*="display: flex"] > label { flex: 0 1 190px; margin: 0; }
    .preanestesico-v3 #frm > div[style*="display: flex"] > input,
    .preanestesico-v3 #frm > div[style*="display: flex"] > select { flex: 1 1 190px; width: auto !important; }
    .preanestesico-v3 #frm > div[style*="display: flex"] > .select2-container {
        flex: 1 1 190px; width: auto !important; min-width: 190px;
    }
    .preanestesico-v3 #frm > div[style*="display: flex"] > .form-group {
        flex: 1 1 360px; width: auto !important; margin: 0 !important; padding: 0 !important;
    }
    .preanestesico-v3 .v3-entry-row { display: flex; align-items: flex-end; gap: 12px; }
    .preanestesico-v3 .v3-entry-row .v3-entry-control { flex: 1 1 auto; }
    .preanestesico-v3 .v3-entry-row .btn { flex: 0 0 42px; min-height: 40px; }
    .preanestesico-v3 .v3-helper { margin: -8px 0 16px; color: var(--v3-muted); font-size: 12px; }
    .preanestesico-v3 .v3-hospital-sync { display:flex; align-items:flex-start; gap:10px; margin:-5px 0 16px; padding:10px 12px; border:1px solid #b9e3d3; border-radius:9px; color:#145844; background:#effaf6; font-size:12px; line-height:1.4; }
    .preanestesico-v3 .v3-hospital-sync i { margin-top:2px; color:#15936b; }
    .preanestesico-v3 .v3-hospital-sync strong { display:block; color:#0e5941; }
    .preanestesico-v3 .v3-hero-actions { display:flex; align-items:center; gap:12px; }
    .preanestesico-v3 .v3-save-status {
        display:inline-flex; align-items:center; gap:7px; min-width:104px; padding:9px 12px;
        border:1px solid rgba(255,255,255,.3); border-radius:9px; color:#fff;
        background:rgba(255,255,255,.1); font-size:12px; font-weight:700;
    }
    .preanestesico-v3 .v3-save-status.is-saving i { animation:v3-spin .8s linear infinite; }
    .preanestesico-v3 .v3-save-status.is-error { border-color:#ffd5d8; color:#fff; background:#c94652; }
    @keyframes v3-spin { to { transform:rotate(360deg); } }
    .preanestesico-v3 .v3-jump-nav button.is-active { color:#08799c; background:#e4f5f8; }
    .preanestesico-v3 .v3-summary-section { padding-bottom:16px; }
    .preanestesico-v3 .v3-patient-grid {
        display:grid; grid-template-columns:minmax(230px,1.2fr) minmax(150px,.65fr) minmax(300px,1.7fr) minmax(210px,1fr);
        gap:10px;
    }
    .preanestesico-v3 .v3-summary-item {
        display:flex; flex-direction:column; justify-content:center; min-height:74px; padding:12px 14px;
        border:1px solid #d9e8ef; border-radius:10px; background:#f8fbfd;
    }
    .preanestesico-v3 .v3-summary-item span { margin-bottom:4px; color:var(--v3-muted); font-size:10px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; }
    .preanestesico-v3 .v3-summary-item strong { color:var(--v3-ink); font-size:13px; line-height:1.35; }
    .preanestesico-v3 .v3-summary-item small { margin-top:3px; color:var(--v3-muted); }
    .preanestesico-v3 .v3-summary-item--alert { border-color:#f2b7bc; background:#fff3f4; }
    .preanestesico-v3 .v3-summary-item--alert span,.preanestesico-v3 .v3-summary-item--alert strong { color:#a32e39; }
    .preanestesico-v3 .v3-diagnosis-list { display:flex; align-items:center; gap:8px; margin-top:10px; padding:10px 12px; overflow-x:auto; border-top:1px solid #e2edf3; }
    .preanestesico-v3 .v3-diagnosis-heading { flex:0 0 auto; color:var(--v3-muted); font-size:11px; font-weight:800; text-transform:uppercase; }
    .preanestesico-v3 .v3-diagnosis-chip { flex:0 0 auto; padding:6px 9px; border:1px solid #cfe3ec; border-radius:999px; color:#315b71; background:#f1f8fb; font-size:11px; }
    .preanestesico-v3 .v3-diagnosis-chip b { margin-right:5px; color:#08799c; }
    .preanestesico-v3 .v3-form-block {
        margin:12px 0; border:1px solid #d7e7ef; border-radius:11px; background:#fff; overflow:visible;
        scroll-margin-top:128px;
    }
    .preanestesico-v3 .v3-form-block > summary {
        display:flex; align-items:center; gap:11px; min-height:50px; padding:11px 15px; cursor:pointer;
        color:#174761; background:#f2f8fa; font-weight:800; list-style:none; user-select:none;
    }
    .preanestesico-v3 .v3-form-block > summary::-webkit-details-marker { display:none; }
    .preanestesico-v3 .v3-form-block > summary .v3-block-icon {
        display:inline-flex; align-items:center; justify-content:center; flex:0 0 32px; width:32px; height:32px;
        border-radius:8px; color:#087fbd; background:#dff2f6;
    }
    .preanestesico-v3 .v3-form-block > summary .v3-block-copy { display:flex; flex:1; flex-direction:column; min-width:0; }
    .preanestesico-v3 .v3-form-block > summary small { margin-top:2px; color:#71899a; font-size:11px; font-weight:500; }
    .preanestesico-v3 .v3-form-block > summary::after { content:none; }
    .preanestesico-v3 .v3-form-block > summary .v3-block-chevron { flex:0 0 auto; color:#628094; font-size:11px; transition:transform .18s ease; }
    .preanestesico-v3 .v3-form-block[open] > summary { border-bottom:1px solid #dceaf0; background:#eaf6f8; }
    .preanestesico-v3 .v3-form-block[open] > summary .v3-block-chevron { transform:rotate(180deg); }
    .preanestesico-v3 .v3-form-block-body { padding:16px; }
    .preanestesico-v3 .v3-form-block-body > br { display:none; }
    .preanestesico-v3 .v3-form-block-body > .v3-form-grid { margin-bottom:16px; }
    .preanestesico-v3 .v3-form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:13px 16px; }
    .preanestesico-v3 .v3-form-grid--3 { grid-template-columns:repeat(3,minmax(0,1fr)); }
    .preanestesico-v3 .v3-field { min-width:0; }
    .preanestesico-v3 .v3-field--wide { grid-column:1/-1; }
    .preanestesico-v3 .v3-field label { display:block; margin:0 0 6px; color:#315b71; font-size:11px; font-weight:800; text-transform:uppercase; }
    .preanestesico-v3 .v3-field input,.preanestesico-v3 .v3-field select,.preanestesico-v3 .v3-field textarea { width:100%!important; }
    .preanestesico-v3 .v3-field textarea { min-height:86px; padding:10px 12px; resize:vertical; }
    .preanestesico-v3 .v3-field--allergy { display:flex; flex-direction:column; }
    .preanestesico-v3 .v3-allergy-readonly-label {
        display:block; margin:0 0 6px; color:#315b71;
        font-size:11px; font-weight:800; text-transform:uppercase;
    }
    .preanestesico-v3 .v3-allergy-readonly-label i { display:none; }
    .preanestesico-v3 .v3-allergy-readonly-value {
        display:flex; align-items:center; min-height:38px; padding:8px 12px;
        border:1px solid #c9dce9; border-radius:8px; color:var(--v3-ink); background:#fff; font-weight:400;
    }
    .preanestesico-v3 .v3-field--allergy.has-allergy .v3-allergy-readonly-label {
        display:flex; align-items:center; gap:7px; min-height:32px; margin:0; padding:7px 10px;
        border-radius:8px 8px 0 0; color:#fff; background:#c62828;
        letter-spacing:.03em;
    }
    .preanestesico-v3 .v3-field--allergy.has-allergy .v3-allergy-readonly-label i { display:inline-block; }
    .preanestesico-v3 .v3-field--allergy.has-allergy .v3-allergy-readonly-value {
        min-height:42px; padding:10px 12px; border-color:#c62828; border-top:0;
        border-radius:0 0 8px 8px; color:#8c1d24; background:#fff7f7; font-weight:700;
    }
    .preanestesico-v3 #tabla_diagnostico td { padding:7px 5px; vertical-align:middle; }
    .preanestesico-v3 #tabla_diagnostico td:first-child { width:42px; text-align:center; }
    .preanestesico-v3 #tabla_diagnostico td:last-child { width:190px; }
    .preanestesico-v3 .v3-preloaded-background td { background:#f3f9fc; }
    .preanestesico-v3 .v3-source-badge {
        display:inline-flex; align-items:center; gap:5px; padding:6px 8px; border-radius:999px;
        color:#12627f; background:#dff2f7; font-size:10px; font-weight:800; white-space:nowrap;
    }
    .preanestesico-v3 .v3-source-badge--current { margin-right:6px; color:#356b43; background:#e6f7ea; }
    .preanestesico-v3 .v3-empty-background { padding:14px!important; color:var(--v3-muted); text-align:center; }
    .preanestesico-v3 .v3-pair-row {
        display:grid!important; grid-template-columns:minmax(130px,.55fr) minmax(180px,1fr) minmax(130px,.55fr) minmax(180px,1fr);
        align-items:center!important; gap:10px 14px!important; margin:0 0 12px!important; padding:0!important;
        border:0!important; border-radius:0!important; background:transparent!important;
    }
    .preanestesico-v3 .v3-pair-row > label { margin:0; color:#315b71; font-size:11px; font-weight:800; text-transform:uppercase; }
    .preanestesico-v3 .v3-pair-row > input,.preanestesico-v3 .v3-pair-row > select,.preanestesico-v3 .v3-pair-row > .select2-container { width:100%!important; min-width:0; }
    .preanestesico-v3 .v3-pair-row > div[style*="margin-right"] { display:none; }
    .preanestesico-v3 .v3-pair-row > .form-group { grid-column:span 2; width:auto!important; margin:0!important; padding:0!important; }
    .preanestesico-v3 .v3-form-block-body > .box-body { padding:0; }
    .preanestesico-v3 .v3-form-block-body > .box-body .form-group { width:100%; margin:0; padding:0; }
    .preanestesico-v3 .v3-form-block-body > .box-body input { width:100%!important; }
    .preanestesico-v3 .v3-form-block-body > table { margin:0 0 12px; }
    .preanestesico-v3 .v3-action-label { display:inline; margin-left:6px; }
    .preanestesico-v3 .v3-entry-row .btn { flex-basis:auto; padding:8px 14px; white-space:nowrap; }
    .preanestesico-v3 #examen .form-group { margin-bottom:0; }
    .preanestesico-v3 #examen .form-group:last-child { display:flex; align-items:flex-end; }
    .preanestesico-v3 #examen .form-group:last-child .btn { width:100%; }
    .preanestesico-v3 .v3-section-title,.preanestesico-v3 td.bg-blue,.preanestesico-v3 td.bg-purple,.preanestesico-v3 td.bg-green {
        background:#0e6485!important;
    }
    .preanestesico-v3 hr { border-color: #e2edf3; }
    .preanestesico-v3 .ui-autocomplete { z-index: 2000; }
    .dark-mode .preanestesico-v3 { --v3-ink:#dcebf1; --v3-muted:#9db4bf; }
    .dark-mode .preanestesico-v3 .v3-section, .dark-mode .preanestesico-v3 #frm,
    .dark-mode .preanestesico-v3 .v3-jump-nav { border-color:#34515f; background:#172d38; box-shadow:none; }
    .dark-mode .preanestesico-v3 #frm > div[style*="display: flex"] { border-color:#34515f; background:#203946; }
    .dark-mode .preanestesico-v3 input:not([type="hidden"]), .dark-mode .preanestesico-v3 select,
    .dark-mode .preanestesico-v3 textarea { border-color:#426171; color:#e4f0f4; background:#132934; }
    .dark-mode .preanestesico-v3 .v3-jump-nav button { color:#cce0e8; }
    .dark-mode .preanestesico-v3 .v3-summary-item,.dark-mode .preanestesico-v3 .v3-form-block { border-color:#34515f; background:#172d38; }
    .dark-mode .preanestesico-v3 .v3-form-block > summary { color:#dcebf1; background:#203946; }
    .dark-mode .preanestesico-v3 .v3-form-block[open] > summary { border-color:#34515f; background:#254653; }
    @media (max-width: 768px) {
        .content-wrapper > .w3-bar.w3-blue-light { flex-wrap: wrap; padding: 10px; }
        .content-wrapper > .text-right { margin: 12px 10px 0 !important; }
        .preanestesico-v3 { padding: 0 0 24px; }
        .preanestesico-v3 .v3-hero { align-items: flex-start; flex-direction: column; padding: 18px; }
        .preanestesico-v3 .v3-hero-actions { align-items:stretch; flex-direction:column; width:100%; }
        .preanestesico-v3 .v3-status { width: 100%; }
        .preanestesico-v3 .v3-jump-nav { position: static; }
        .preanestesico-v3 .v3-section { padding: 12px; }
        .preanestesico-v3 .v3-section-title { margin: -12px -12px 14px; }
        .preanestesico-v3 table { display: block; overflow-x: auto; }
        .preanestesico-v3 #frm > div[style*="display: flex"] { flex-wrap: wrap; gap: 8px !important; }
        .preanestesico-v3 #frm > div[style*="display: flex"] label { width: 100%; margin-top: 8px; }
        .preanestesico-v3 #frm > div[style*="display: flex"] select,
        .preanestesico-v3 #frm > div[style*="display: flex"] input,
        .preanestesico-v3 #frm > div[style*="display: flex"] > .select2-container { width: 100% !important; }
        .preanestesico-v3 .v3-entry-row { align-items: stretch; flex-direction: column; }
        .preanestesico-v3 .v3-entry-row .btn { width: 100%; }
        .preanestesico-v3 .v3-patient-grid,.preanestesico-v3 .v3-form-grid,.preanestesico-v3 .v3-form-grid--3 { grid-template-columns:1fr; }
        .preanestesico-v3 .v3-pair-row { grid-template-columns:1fr!important; }
        .preanestesico-v3 .v3-pair-row > .form-group { grid-column:auto; }
        .preanestesico-v3 .v3-field--wide { grid-column:auto; }
    }

    .icheckbox_flat-orange.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }

    td {
        padding: 3px !important;

    }

    div.formgroup.col-md-4 {
        margin-bottom: 0px !important;
    }

    .ui-corner-all {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget {
        font-family: Verdana, Arial, sans-serif;
        font-size: 12px;
    }

    .ui-menu {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
        opacity: 1;
    }

    .ui-autocomplete {
        opacity: 1;
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 470px !important;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }

    .ui-menu .ui-menu-item {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .ui-menu .ui-menu-item a {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }

    .ui-menu .ui-menu-item a:hover {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }

    .ui-widget-content a {
        color: #222222;
    }
</style>

<?php
//dd($pre_anestesico);

$fechaNacimientoValor = trim((string) optional($pre_anestesico->anestesiologia->historia->paciente)->fecha_nacimiento);
$fechaNacimiento = null;

if ($fechaNacimientoValor !== '' && $fechaNacimientoValor !== '0000-00-00') {
    try {
        $fechaNacimiento = \Carbon\Carbon::parse($fechaNacimientoValor);
        if ($fechaNacimiento->isFuture()) {
            $fechaNacimiento = null;
        }
    } catch (\Throwable $exception) {
        $fechaNacimiento = null;
    }
}

$edad = $fechaNacimiento ? $fechaNacimiento->age : '';
$diaNacimiento = $fechaNacimiento ? $fechaNacimiento->format('d') : '';
$mesNacimiento = $fechaNacimiento ? $fechaNacimiento->format('m') : '';

$procedimientosPropuestos = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $hc_procedimiento->id)
    ->with('procedimiento')
    ->get()
    ->map(function ($detalle) {
        return optional($detalle->procedimiento)->nombre;
    })
    ->filter()
    ->implode(' + ');

$paciente = $pre_anestesico->anestesiologia->historia->paciente;
$nombrePaciente = trim($paciente->apellido1 . ' ' . $paciente->apellido2 . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2);
$sexoPaciente = $paciente->sexo == 1 ? 'Masculino' : 'Femenino';
$alergiasPaciente = trim((string) ($alergias_paciente ?? ''));

?>


<section class="content preanestesico-v3">
<input type="hidden" value="{{$pre_anestesico->id}}" name="id_pre" id="id_pre" />

    <div class="v3-hero">
        <div class="v3-hero-copy">
            <span class="v3-hero-icon"><i class="fa fa-heartbeat" aria-hidden="true"></i></span>
            <div>
                <h1>Valoración preanestésica</h1>
                <p>Evaluación clínica y preparación segura para el procedimiento</p>
            </div>
        </div>
        <div class="v3-hero-actions">
            <div class="v3-save-status is-saved" id="preanestesico-save-status" role="status" aria-live="polite">
                <i class="fa fa-check-circle" aria-hidden="true"></i>
                <span>Guardado</span>
            </div>
            <div class="v3-status">Paciente<strong>{{ strtoupper(trim($paciente->apellido1 . ' ' . $paciente->nombre1)) }}</strong></div>
        </div>
    </div>

    <nav class="v3-jump-nav" aria-label="Secciones de la valoración preanestésica">
        <button type="button" data-scroll-target="resumen-clinico"><i class="fa fa-id-card-o" aria-hidden="true"></i> Resumen</button>
        <button type="button" data-scroll-target="antecedentes-personales"><i class="fa fa-history" aria-hidden="true"></i> Antecedentes</button>
        <button type="button" data-scroll-target="bloque-via-aerea"><i class="fa fa-stethoscope" aria-hidden="true"></i> Vía aérea</button>
        <button type="button" data-scroll-target="bloque-examen-fisico"><i class="fa fa-user-md" aria-hidden="true"></i> Examen físico</button>
        <button type="button" data-scroll-target="bloque-riesgos"><i class="fa fa-line-chart" aria-hidden="true"></i> Riesgos</button>
        <button type="button" data-scroll-target="bloque-ayuno"><i class="fa fa-clock-o" aria-hidden="true"></i> Ayuno</button>
        <button type="button" data-scroll-target="bloque-plan"><i class="fa fa-file-text-o" aria-hidden="true"></i> Plan</button>
        <button type="button" data-scroll-target="seccion-indicaciones"><i class="fa fa-list-alt" aria-hidden="true"></i> Indicaciones</button>
        <button type="button" data-scroll-target="seccion-examenes"><i class="fa fa-flask" aria-hidden="true"></i> Exámenes</button>
    </nav>

    <div class="v3-section v3-summary-section" id="resumen-clinico">
        <div class="v3-section-title"><i class="fa fa-id-card-o" aria-hidden="true"></i> Resumen clínico</div>
        <div class="v3-patient-grid">
            <div class="v3-summary-item v3-summary-item--patient">
                <span>Paciente</span>
                <strong>{{ strtoupper($nombrePaciente) }}</strong>
                <small>{{ $paciente->id }}</small>
            </div>
            <div class="v3-summary-item">
                <span>Edad y sexo</span>
                <strong>{{ $edad !== '' ? $edad . ' años' : 'Edad no registrada' }}</strong>
                <small>{{ $sexoPaciente }}</small>
            </div>
            <div class="v3-summary-item v3-summary-item--procedure">
                <span>Procedimiento propuesto</span>
                <strong>{{ $procedimientosPropuestos !== '' ? $procedimientosPropuestos : 'No registrado' }}</strong>
            </div>
            <div class="v3-summary-item {{ $alergiasPaciente !== '' ? 'v3-summary-item--alert' : '' }}" id="resumen-alergias">
                <span><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Alergias</span>
                <strong>{{ $alergiasPaciente !== '' ? $alergiasPaciente : 'No registra' }}</strong>
            </div>
        </div>
        <div class="v3-diagnosis-list" aria-label="Diagnósticos registrados">
            <span class="v3-diagnosis-heading">Diagnósticos</span>
            @forelse($pre_anestesico->anestesiologia->historia->diagnosticos as $diagnostico)
                @php
                    $c3 = Sis_medico\Cie_10_3::find($diagnostico->cie10);
                    $c4 = Sis_medico\Cie_10_4::find($diagnostico->cie10);
                    $descripcionDiagnostico = optional($c4)->descripcion ?: optional($c3)->descripcion ?: 'Diagnóstico sin descripción';
                @endphp
                <span class="v3-diagnosis-chip"><b>{{ $diagnostico->cie10 }}</b> {{ $descripcionDiagnostico }}</span>
            @empty
                <span class="v3-empty-state">No hay diagnósticos registrados.</span>
            @endforelse
        </div>
    </div>
    <div class="v3-section" id="antecedentes-personales">
    <div class="v3-section-title"><i class="fa fa-history" aria-hidden="true"></i> Antecedentes patológicos personales</div>
    <p class="v3-helper">Información precargada desde la historia médica. Esta sección es de solo lectura.</p>
    <div id="tabla_diagnostico">
        @include('rc_preanestesico.tabla_diagnostico', [
            'rc_antecedentes' => $rc_antecedentes,
            'antecedentes_paciente' => $antecedentes_paciente,
        ])
    </div>
    </div>

    <form id="frm" class="v3-clinical-form">
        <div class="v3-section-title"><i class="fa fa-stethoscope" aria-hidden="true"></i> Evaluación preanestésica</div>
        <p class="v3-helper">Los cambios realizados en los campos clínicos se guardan automáticamente.</p>
        <div class="v3-hospital-sync" role="status">
            <i class="fa fa-refresh" aria-hidden="true"></i>
            <div>
                <strong>Datos hospitalarios vinculados al mismo paciente y episodio</strong>
                @if(!empty($hospitalSync['updated_fields']))
                    Se completaron {{ count($hospitalSync['updated_fields']) }} campo(s) vacío(s)
                    @if(!empty($hospitalSync['sources']))
                        desde {{ implode(', ', $hospitalSync['sources']) }}.
                    @else
                        desde la historia clínica.
                    @endif
                @else
                    Los datos existentes del anestesiólogo se conservaron; solo se completan campos vacíos cuando hay información disponible.
                @endif
            </div>
        </div>

        <input type="hidden" value="{{$id}}" name="id_cab" id="id_cab" />

        <details class="v3-form-block" id="bloque-antecedentes-clinicos" open>
            <summary>
                <span class="v3-block-icon"><i class="fa fa-history" aria-hidden="true"></i></span>
                <span class="v3-block-copy">Antecedentes y contexto clínico<small>Antecedentes anestésicos, quirúrgicos, alergias y tipo de atención.</small></span>
                <i class="fa fa-chevron-down v3-block-chevron" aria-hidden="true"></i>
            </summary>
            <div class="v3-form-block-body">
                <div class="v3-form-grid">
                    <div class="v3-field">
                        <label for="anestesico">Antecedentes anestésicos</label>
                        <input type="text" name="anestesico" id="anestesico" value="{{$pre_anestesico->anestesico}}" onchange="actualizar();">
                    </div>
                    <div class="v3-field">
                        <label for="quirurgicos">Antecedentes quirúrgicos</label>
                        <input type="text" name="quirurgicos" id="quirurgicos" value="{{$pre_anestesico->quirurgicos}}" onchange="actualizar();">
                    </div>
                    <div class="v3-field v3-field--allergy {{ $alergiasPaciente !== '' ? 'has-allergy' : '' }}">
                        <span class="v3-allergy-readonly-label"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Alergias</span>
                        <div class="v3-allergy-readonly-value" role="status" aria-label="Alergias registradas del paciente">
                            {{ $alergiasPaciente !== '' ? $alergiasPaciente : 'No registra' }}
                        </div>
                    </div>
                    <div class="v3-field">
                        <label for="transfusiones">Transfusiones</label>
                        <input type="text" name="transfusiones" id="transfusiones" value="{{$pre_anestesico->transfuciones}}" onchange="actualizar();">
                    </div>
                    <div class="v3-field v3-field--wide">
                        <label for="habitos">Hábitos</label>
                        <input type="text" name="habitos" id="habitos" value="{{$pre_anestesico->habitos}}" onchange="actualizar();">
                    </div>
                </div>

        <div class="v3-pair-row" style="width: 100%; display: flex; gap: 10px">
            <label>Tipo</label>
            <select style="width: 25%;" class="form-control input-sm" name="tipo" id="tipo" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{$pre_anestesico->tipo == 'ELECTIVA' ? 'selected' : '' }} value="ELECTIVA">ELECTIVA</option>
                <option {{$pre_anestesico->tipo == 'EMERGENCIA' ? 'selected' : '' }} value="EMERGENCIA">EMERGENCIA</option>
                <option {{$pre_anestesico->tipo == 'URGENCIA' ? 'selected' : '' }} value="URGENCIA">URGENCIA</option>
            </select>

            <div style="margin-right: 50px;"></div>

            <label>RIESGO QUIRURGICO</label>
            <select style="width: 25%;" class="form-control input-sm" name="riesgo_quirurgico" id="riesgo_quirurgico" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{$pre_anestesico->riesgo_quirurgico == 'BAJO' ? 'selected' : '' }} value="BAJO">BAJO</option>
                <option {{$pre_anestesico->riesgo_quirurgico == 'MODERADO' ? 'selected' : '' }} value="MODERADO">MODERADO</option>
                <option {{$pre_anestesico->riesgo_quirurgico == 'ALTO' ? 'selected' : '' }} value="ALTO">ALTO</option>
            </select>
        </div>
            </div>
        </details>

        <details class="v3-form-block" id="bloque-via-aerea" open>
            <summary>
                <span class="v3-block-icon"><i class="fa fa-stethoscope" aria-hidden="true"></i></span>
                <span class="v3-block-copy">Evaluación de vía aérea<small>Mediciones y antecedentes relacionados con intubación difícil.</small></span>
                <i class="fa fa-chevron-down v3-block-chevron" aria-hidden="true"></i>
            </summary>
            <div class="v3-form-block-body">

        <div class="v3-pair-row" style="width: 100%; display: flex; gap: 10px">
            <label>APERTURA BUCAL(cm) </label>
            <select style="width: 20%;" class="form-control input-sm" name="apertura" id="apertura" onchange="actualizar();">
                <option value="">Seleccione</option>

                <option {{ $pre_anestesico->apertura_bucal == "<2" ? 'selected' : '' }} value="&lt;2">&lt;2</option>
                <option {{ $pre_anestesico->apertura_bucal == '2-2,5' ? 'selected' : '' }} value="2-2,5">2-2,5</option>
                <option {{ $pre_anestesico->apertura_bucal == '2.6-3' ? 'selected' : '' }} value="2.6-3">2.6-3 </option>
                <option {{ $pre_anestesico->apertura_bucal == '>3' ? 'selected' : '' }} value="&gt;3">&gt;3 </option>
            </select>

            <div style="margin-right: 50px;"></div>

            <label>DISTANCIA TIROMENTONEANA(cm) </label>
            <select style="width: 20%;" class="form-control input-sm" name="distancia" id="distancia" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{ $pre_anestesico->distancia_tiromentoneana == "<6" ? 'selected' : '' }} value="&lt;6"> &lt;6 </option>
                <option {{ $pre_anestesico->distancia_tiromentoneana == "6 - 6.5" ? 'selected' : '' }} value="6 - 6.5">6 - 6.5</option>
                <option {{ $pre_anestesico->distancia_tiromentoneana == ">6.5" ? 'selected' : '' }} value="&gt;6.5">&gt;6.5 </option>
            </select>
        </div>

        <br>


        <div class="v3-pair-row" style="width: 100%; display: flex; gap: 10px">
            <label> MALLAMPATI </label>
            <select style="width: 20%;" class="form-control input-sm" name="mallampati" id="mallampati" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{ $pre_anestesico->mallanpati == "I" ? 'selected' : '' }} value="I">I</option>
                <option {{ $pre_anestesico->mallanpati == "II" ? 'selected' : '' }} value="II">II</option>
                <option {{ $pre_anestesico->mallanpati === "III" ? 'selected' : '' }} value="III">III</option>
                <option {{ $pre_anestesico->mallanpati == "IV" ? 'selected' : '' }} value="IV">IV</option>
            </select>

            <div style="margin-right: 50px;"></div>


            <label>PROTRUSIÓN MANDIBULAR </label>
            <select style="width: 20%;" class="form-control input-sm" name="protrusion" id="protrusion" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{ $pre_anestesico->protusion_mandibular == "<0" ? 'selected' : '' }} value="&lt;0"> &lt;0 </option>
                <option {{ $pre_anestesico->protusion_mandibular == "0" ? 'selected' : '' }} value="0">0</option>
                <option {{ $pre_anestesico->protusion_mandibular == ">0" ? 'selected' : '' }} value="&gt;0">&gt;0</option>
            </select>
        </div>
        <br>

        <div class="v3-pair-row" style="width: 100%; display: flex; gap: 10px">
            <label>PERIMETRO CERVICAL(cm) </label>
            <select style="width: 20%;" class="form-control input-sm" name="perimetro" id="peimetro" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{ $pre_anestesico->perimetro_cervical == "<40" ? 'selected' : '' }} value="&lt;40"> &lt;40 </option>
                <option {{ $pre_anestesico->perimetro_cervical == ">40" ? 'selected' : '' }} value="&gt;40">&gt;40</option>
            </select>

            <div style="margin-right: 50px;"></div>

            <label>MOVILIDAD CERVICAL(°) </label>
            <select style="width: 20%;" class="form-control input-sm" name="movilidad" id="movilidad" onchange="actualizar();">
                <option value="">Seleccione </option>
                <option {{ $pre_anestesico->movilidad_cervical == "<35" ? 'selected' : '' }} value="&lt;35"> &lt;35 </option>
                <option {{ $pre_anestesico->movilidad_cervical == ">35" ? 'selected' : '' }} value="&gt;35">&gt;35</option>
            </select>
        </div>
        <br>


        <div class="v3-pair-row" style="width: 100%; display: flex; gap: 10px">
            <label>HISTORIA DEINTUBACIÓN DIFICIL </label>
            <select style="width: 20%;" class="form-control input-sm" name="historia" id="historia" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{ $pre_anestesico->intubacion_dificil == "SI" ? 'selected' : '' }} value="SI">SI</option>
                <option {{ $pre_anestesico->intubacion_dificil == "NO" ? 'selected' : '' }} value="NO">NO</option>
            </select>

            <div style="margin-right: 50px;"></div>


            <label>PATOLOGÍA ASOCIADA A INTUBACIÓN DIFICIL </label>
            <select style="width: 20%;" class="form-control input-sm" name="patologia" id="patologia" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{ $pre_anestesico->patologia == "SI" ? 'selected' : '' }} value="SI">SI</option>
                <option {{ $pre_anestesico->patologia == "NO" ? 'selected' : '' }} value="NO">NO</option>
            </select>
        </div>
            </div>
        </details>

        <details class="v3-form-block" id="bloque-examen-fisico" open>
            <summary>
                <span class="v3-block-icon"><i class="fa fa-user-md" aria-hidden="true"></i></span>
                <span class="v3-block-copy">Examen físico<small>Evaluación por sistemas y capacidad metabólica.</small></span>
                <i class="fa fa-chevron-down v3-block-chevron" aria-hidden="true"></i>
            </summary>
            <div class="v3-form-block-body">

        <div class="v3-form-grid">
            <div class="v3-field">
                <label for="presion">Presión arterial</label>
                <input type="text" name="presion" id="presion" value="{{$pre_anestesico->presion}}" placeholder="Ej. 120/80" onchange="actualizar();">
            </div>
            <div class="v3-field">
                <label for="pulso">Frecuencia cardíaca</label>
                <input type="number" min="0" step="1" name="pulso" id="pulso" value="{{$pre_anestesico->pulso}}" placeholder="lpm" onchange="actualizar();">
            </div>
            <div class="v3-field">
                <label for="frecuencia_respiratoria">Frecuencia respiratoria</label>
                <input type="number" min="0" step="1" name="frecuencia_respiratoria" id="frecuencia_respiratoria" value="{{$pre_anestesico->fecuencia_respiratoria}}" placeholder="rpm" onchange="actualizar();">
            </div>
            <div class="v3-field">
                <label for="temperatura">Temperatura</label>
                <input type="number" min="20" max="50" step="0.1" name="temperatura" id="temperatura" value="{{$pre_anestesico->temperatura}}" placeholder="°C" onchange="actualizar();">
            </div>
            <div class="v3-field">
                <label for="o2">Saturación de oxígeno</label>
                <input type="text" name="o2" id="o2" value="{{$pre_anestesico->o2}}" placeholder="%" onchange="actualizar();">
            </div>
        </div>

        <div class="v3-pair-row" style="width: 100%; display: flex; gap: 10px">
            <div class="form-group col-xs-6{{ $errors->has('torax') ? ' has-error' : '' }}">
                <label for="torax" class="col-md-20 control-label">TÓRAX</label>
                <input type="text" name="torax" id="torax" size="20" class="form-control" required value="{{$pre_anestesico->torax}}" onchange="actualizar();">
            </div>


            <div class="form-group col-xs-6{{ $errors->has('corazon') ? ' has-error' : '' }}">
                <label for="corazon" class="col-md-20 control-label">CORAZÓN </label>
                <input type="text" name="corazon" id="corazon" size="20" class="form-control" required value="{{$pre_anestesico->corazon}}" onchange="actualizar();">
            </div>
        </div>
        <br>
        <div class="v3-pair-row" style="width: 100%; display: flex; gap: 10px">
            <div class="form-group col-xs-6{{ $errors->has('pulmones') ? ' has-error' : '' }}">
                <label for="pulmones" class="col-md-20 control-label">PULMONES</label>
                <input type="text" name="pulmones" id="pulmones" size="20" class="form-control" required value="{{$pre_anestesico->pulmones}}" onchange="actualizar();">
            </div>

            <div class="form-group col-xs-6{{ $errors->has('abdomen') ? ' has-error' : '' }}">
                <label for="abdomen" class="col-md-20 control-label"> ABDÓMEN</label>
                <input type="text" name="abdomen" id="abdomen" size="20" class="form-control" required value="{{$pre_anestesico->abdomen}}" onchange="actualizar();">
            </div>
        </div>
        <br>
        <div class="v3-pair-row" style="width: 100%; display: flex; gap: 10px">
            <div class="form-group col-xs-6{{ $errors->has('extremidades') ? ' has-error' : '' }}">
                <label for="extremidades" class="col-md-20 control-label"> EXTREMIDADES</label>
                <input type="text" name="extremidades" id="extremidades" size="20" class="form-control" required value="{{$pre_anestesico->extremidades}}" onchange="actualizar();">
            </div>

            <div class="form-group col-xs-6{{ $errors->has('sistema') ? ' has-error' : '' }}">
                <label for="sistema" class="col-md-20 control-label"> SISTEMA NERVIOSO CENTRAL</label>
                <input type="text" name="sistema" id="sistema" size="20" class="form-control" required value="{{$pre_anestesico->sistema_nervisoso}}" onchange="actualizar();">
            </div>
        </div>
        <br>

        <div class="box-body">
            <div class="form-group col-xs-6{{ $errors->has('equivalente') ? ' has-error' : '' }}">
                <label for="equivalente" class="col-md-20 control-label">EQUIVALENTE METABÓLICO (METS)</label>
                <input type="text" name="equivalente" id="equivalente" size="20" class="form-control" value="{{$pre_anestesico->equivalente_metabolico}}" required onchange="actualizar();">
            </div>
        </div>
            </div>
        </details>

        <details class="v3-form-block" id="bloque-riesgos" open>
            <summary>
                <span class="v3-block-icon"><i class="fa fa-line-chart" aria-hidden="true"></i></span>
                <span class="v3-block-copy">Escalas y riesgos<small>Clasificación ASA y riesgos perioperatorios.</small></span>
                <i class="fa fa-chevron-down v3-block-chevron" aria-hidden="true"></i>
            </summary>
            <div class="v3-form-block-body">

        <div class="v3-pair-row" style="width: 100%; display: flex; gap: 10px">
            <label>ESTADO FISICO ASA </label>
            <select style="width: 20%;" class="form-control input-sm" name="estado_fisico" id="estado_fisico" onchange="actualizar();">
                <option value="">Seleccione</option>
                <option {{ $pre_anestesico->estado_fisico == "I" ? 'selected' : '' }} value="I">I</option>
                <option {{ $pre_anestesico->estado_fisico == "II" ? 'selected' : '' }} value="II">II</option>
                <option {{ $pre_anestesico->estado_fisico == "III" ? 'selected' : '' }} value="III">III</option>
                <option {{ $pre_anestesico->estado_fisico == "IV" ? 'selected' : '' }} value="IV">IV</option>
                <option {{ $pre_anestesico->estado_fisico == "V" ? 'selected' : '' }} value="V">V</option>
                <option {{ $pre_anestesico->estado_fisico == "VI" ? 'selected' : '' }} value="VI">VI</option>
            </select>


            <div style="margin-right: 50px;"></div>


            <label for="riesgo_cardiaco" class="col-md-20 control-label">RIESGO CARDÍACO</label>
            <input style="width: 45%;" type="text" name="riesgo_cardiaco" id="riesgo_cardiaco" size="20" class="form-control input-sm" value="{{$pre_anestesico->riesgo_cardiaco}}" required onchange="actualizar();">
        </div>

        <br>




        <div class="v3-pair-row" style="width: 100%; display: flex; gap: 5px">

            <label for="riesgo_pulmonar" class="col-md-20 control-label">RIESGO PULMONAR</label>
            <input style="width: 35%;" type="text" name="riesgo_pulmonar" id="riesgo_pulmonar" size="20" class="form-control input-sm" value="{{$pre_anestesico->riesgo_pulmonar}}" required onchange="actualizar();">

            <div style="margin-right: 50px;"></div>

            <label for="riesgo_tromboembolico" class="col-md-20 control-label">RIESGO TROMBOEMBÓLICO</label>
            <input style="width: 35%;" type="text" name="riesgo_tromboembolico" id="riesgo_tromboembolico" size="20" class="form-control input-sm" value="{{$pre_anestesico->riesgo_tromboembolico}}" required onchange="actualizar();">

        </div>

            </div>
        </details>

        <details class="v3-form-block" id="bloque-ayuno" open>
            <summary>
                <span class="v3-block-icon"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                <span class="v3-block-copy">Tiempo de última ingesta<small>Registre los tiempos de ayuno por tipo de alimento.</small></span>
                <i class="fa fa-chevron-down v3-block-chevron" aria-hidden="true"></i>
            </summary>
            <div class="v3-form-block-body">

        <div class="v3-pair-row" style="width: 100%; display: flex; gap: 5px">

            <label for="liquidos_claros" class="col-md-20 control-label">LIQUIDOS CLAROS</label>
            <input style="width: 35%;" type="text" name="liquidos_claros" id="liquidos_claros" size="20" class="form-control input-sm" value="{{$pre_anestesico->liquidos_claros}}" required onchange="actualizar();">

            <div style="margin-right: 50px;"></div>

            <label for="leche_materna" class="col-md-20 control-label">LECHE MATERNA</label>
            <input style="width: 35%;" type="text" name="leche_materna" id="leche_materna" size="20" class="form-control input-sm" value="{{$pre_anestesico->leche_materna}}" required onchange="actualizar();">

        </div>



        <div class="box-body">
            <label for="solidos" class="col-md-20 control-label">SOLIDOS</label>
            <input style="width: 35%;" type="text" name="solidos" id="solidos" size="20" class="form-control input-sm" value="{{$pre_anestesico->solidos}}" required onchange="actualizar();">
        </div>
            </div>
        </details>

        <details class="v3-form-block" id="bloque-plan" open>
            <summary>
                <span class="v3-block-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
                <span class="v3-block-copy">Plan anestésico y observaciones<small>Conducta planificada e información clínica complementaria.</small></span>
                <i class="fa fa-chevron-down v3-block-chevron" aria-hidden="true"></i>
            </summary>
            <div class="v3-form-block-body v3-form-grid">

        <div class="v3-field v3-field--wide">
            <label for="plan_anestesico" class="col-md-20 control-label">PLAN ANESTÉSICO</label>
            <textarea name="plan_anestesico" id="plan_anestesico" class="form-control input-sm" required onchange="actualizar();">{{$pre_anestesico->plan_anestesico}}</textarea>
        </div>

        <div class="v3-field v3-field--wide">
            <label for="observaciones" class="col-md-20 control-label">OBSERVACIONES</label>
            <textarea name="observaciones" id="observaciones" class="form-control input-sm" required onchange="actualizar();">{{$pre_anestesico->observaciones}}</textarea>
        </div>
            </div>
        </details>

    </form>
    <div class="v3-section" id="seccion-indicaciones">
    <div class="v3-section-title"><i class="fa fa-list-alt" aria-hidden="true"></i> Indicaciones</div>
    <p class="v3-helper">Información precargada desde el Formulario 018 y la evolución médica. Esta sección es de solo lectura.</p>
    <div id="tabla_indicacion">
        @include('rc_preanestesico.tabla_indicacion', ['indicaciones' => $indicaciones])
    </div>
    </div>

    <div class="v3-section" id="seccion-examenes">
    <div class="v3-section-title"><i class="fa fa-flask" aria-hidden="true"></i> Exámenes y resultados</div>
    <p class="v3-helper">Información precargada desde el Formulario 018 y Laboratorio. Esta sección es de solo lectura.</p>
    <div id="tabla_examen">
        @include('rc_preanestesico.tabla_examen', ['examen' => $examen])
    </div>
    </div>







    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="{{ asset ('/js/jquery-ui.js')}}"></script>
    <script>
        var jumpButtons = document.querySelectorAll('.v3-jump-nav [data-scroll-target]');

        jumpButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var target = document.getElementById(button.getAttribute('data-scroll-target'));

                if (target) {
                    if (target.tagName === 'DETAILS') {
                        target.open = true;
                    } else {
                        var parentDetails = target.closest('details');
                        if (parentDetails) parentDetails.open = true;
                    }

                    jumpButtons.forEach(function (item) { item.classList.remove('is-active'); });
                    button.classList.add('is-active');
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        if ('IntersectionObserver' in window) {
            var jumpObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;

                    jumpButtons.forEach(function (button) {
                        button.classList.toggle('is-active', button.getAttribute('data-scroll-target') === entry.target.id);
                    });
                });
            }, { rootMargin: '-145px 0px -65% 0px', threshold: 0 });

            jumpButtons.forEach(function (button) {
                var observedTarget = document.getElementById(button.getAttribute('data-scroll-target'));
                if (observedTarget) jumpObserver.observe(observedTarget);
            });
        }

        function actualizarEstadoGuardado(estado) {
            var status = document.getElementById('preanestesico-save-status');
            if (!status) return;

            var icon = status.querySelector('i');
            var label = status.querySelector('span');
            status.classList.remove('is-saving', 'is-saved', 'is-error');
            status.classList.add('is-' + estado);

            if (estado === 'saving') {
                icon.className = 'fa fa-circle-o-notch';
                label.textContent = 'Guardando...';
            } else if (estado === 'error') {
                icon.className = 'fa fa-exclamation-circle';
                label.textContent = 'Error al guardar';
            } else {
                icon.className = 'fa fa-check-circle';
                label.textContent = 'Guardado';
            }
        }

        function actualizar() {

            actualizarEstadoGuardado('saving');
            $.ajax({
                type: 'post',
                url: "{{route('formato.rc_preanestesico_update')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },

                datatype: 'json',
                data: $("#frm").serialize(),
                success: function(data) {
                    actualizarEstadoGuardado('saved');
                },
                error: function(data) {
                    actualizarEstadoGuardado('error');
                }
            });
        }

    </script>
</section>
@endsection
