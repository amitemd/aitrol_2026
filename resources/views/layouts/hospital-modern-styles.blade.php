<style>
/* Sistema visual compartido del area hospitalaria. */
.hospital-modern-ui {
  --hospital-navy: #07375f; --hospital-blue: #0b6fac; --hospital-cyan: #22b8cf;
  --hospital-bg: #f3f8fb; --hospital-surface: #fff; --hospital-soft: #eaf6fa;
  --hospital-border: #d5e7ef; --hospital-text: #17324d; --hospital-muted: #657b8f;
  background: var(--hospital-bg); color: var(--hospital-text);
}
.hospital-modern-ui .app-content.content {
  background: radial-gradient(circle at 92% 2%, rgba(34,184,207,.08), transparent 24rem), var(--hospital-bg);
}
.hospital-modern-ui .header-navbar-shadow { pointer-events: none; }
.hospital-modern-ui .hospital-company-brand {
  display: flex; width: 200px; height: 68px; align-items: center; justify-content: center;
  padding: .25rem .75rem !important; border-radius: 10px;
  transition: background-color .18s ease, transform .18s ease;
}
.hospital-modern-ui .hospital-company-brand:hover,
.hospital-modern-ui .hospital-company-brand:focus {
  background: #eef7fa; outline: none; transform: translateY(-1px);
}
.hospital-modern-ui .hospital-company-logo {
  display: block; width: auto; max-width: 176px; height: auto; max-height: 58px;
  object-fit: contain; object-position: center;
}
.hospital-modern-ui .hospital-company-logo-fallback {
  display: inline-flex; width: 46px; height: 46px; align-items: center; justify-content: center;
  border: 1px solid #b9e1ea; border-radius: 13px;
  background: linear-gradient(145deg,#eaf8fb,#ddf3f1); color: var(--hospital-blue);
  box-shadow: 0 6px 16px rgba(7,55,95,.1);
}
.hospital-modern-ui .hospital-company-logo-fallback[hidden] { display: none !important; }
.hospital-modern-ui .hospital-company-logo-fallback svg {
  width: 29px; height: 29px; fill: none; stroke: currentColor; stroke-width: 2.4;
  stroke-linecap: round; stroke-linejoin: round;
}
.hospital-modern-ui .hospital-company-logo-fallback .hospital-company-logo-cross { stroke: #16a99a; stroke-width: 2.8; }
.hospital-modern-ui .hospital-ambulatory-link {
  display: inline-flex; min-height: 36px; align-items: center; gap: .48rem; margin-left: .7rem;
  padding: .45rem .72rem; border: 1px solid #d1e4ec; border-radius: 9px;
  background: #f7fbfd; color: var(--hospital-navy); font-size: .74rem; font-weight: 700;
  text-decoration: none; transition: border-color .18s ease, background-color .18s ease, color .18s ease, transform .18s ease;
}
.hospital-modern-ui .hospital-ambulatory-link:hover,
.hospital-modern-ui .hospital-ambulatory-link:focus {
  border-color: #8bcbd8; background: #eaf7fa; color: var(--hospital-blue);
  outline: none; text-decoration: none; transform: translateY(-1px);
}
.hospital-modern-ui .hospital-ambulatory-link svg {
  width: 17px; height: 17px; fill: none; stroke: #16a99a; stroke-width: 2;
  stroke-linecap: round; stroke-linejoin: round;
}
.hospital-modern-ui .hospital-admin-visible-link {
  border-color: #d6def2; background: #f7f8fd; color: #334d91;
}
.hospital-modern-ui .hospital-admin-visible-link:hover,
.hospital-modern-ui .hospital-admin-visible-link:focus {
  border-color: #9eafe0; background: #eef1fb; color: #263f86;
}
.hospital-modern-ui .hospital-admin-visible-link svg { stroke: #526fd0; }
.hospital-modern-ui .content-wrapper {
  width: 100%; max-width: 1920px; margin-right: auto; margin-left: auto; padding: 1.4rem 1.5rem 2rem;
}
.hospital-modern-ui.hospital-patient-route .app-content.content {
  padding-top: 1rem !important;
}
.hospital-modern-ui .hospital-patient-summary-wrapper {
  position: relative !important; top: auto !important; z-index: 20; width: 100%;
  padding-top: 7rem; background: transparent !important;
}
.hospital-modern-ui .hospital-patient-summary {
  width: 100%; max-width: 1920px; margin: 0 auto; padding: 1.25rem 1.5rem 0;
}
.hospital-modern-ui .hospital-patient-summary__card {
  overflow: hidden; border: 1px solid #cde3ec; border-radius: 15px;
  background: var(--hospital-surface); box-shadow: 0 16px 38px rgba(7,55,95,.11);
}
.hospital-modern-ui .hospital-patient-summary__header {
  display: flex; min-height: 68px; align-items: center; gap: 14px; padding: 13px 20px;
  background: linear-gradient(105deg,#07375f 0%,#0b6fac 58%,#20aec6 100%); color: #fff;
}
.hospital-modern-ui .hospital-patient-summary__icon {
  display: inline-flex; width: 42px; height: 42px; flex: 0 0 42px; align-items: center; justify-content: center;
  border: 1px solid rgba(255,255,255,.3); border-radius: 12px; background: rgba(255,255,255,.13);
  color: #fff; font-size: 1.1rem; box-shadow: inset 0 1px 0 rgba(255,255,255,.18);
}
.hospital-modern-ui .hospital-patient-summary__eyebrow {
  display: block; margin-bottom: 2px; color: rgba(255,255,255,.75); font-size: .66rem;
  font-weight: 800; letter-spacing: .08em; line-height: 1.2; text-transform: uppercase;
}
.hospital-modern-ui .hospital-patient-summary__header h2 {
  margin: 0; color: #fff; font-size: 1.16rem; font-weight: 800; line-height: 1.25;
}
.hospital-modern-ui .hospital-patient-summary__grid {
  display: grid; grid-template-columns: .72fr 1.35fr .55fr .78fr 1.05fr .78fr 1.55fr;
}
.hospital-modern-ui .hospital-patient-summary__item {
  min-width: 0; min-height: 78px; padding: 13px 15px; border-right: 1px solid #e2edf2;
  background: #fff;
}
.hospital-modern-ui .hospital-patient-summary__item:last-child { border-right: 0; }
.hospital-modern-ui .hospital-patient-summary__item span {
  display: block; margin-bottom: 6px; color: #667d91; font-size: .66rem; font-weight: 800;
  letter-spacing: .045em; line-height: 1.25; text-transform: uppercase;
}
.hospital-modern-ui .hospital-patient-summary__item strong {
  display: block; color: var(--hospital-text); font-size: .8rem; font-weight: 650; line-height: 1.35;
  overflow-wrap: anywhere;
}
.hospital-modern-ui .hospital-patient-summary__item--accent {
  background: linear-gradient(145deg,#f1fbfd,#e8f6fa);
}
.hospital-modern-ui .hospital-patient-summary__item--accent strong {
  color: var(--hospital-blue); font-size: .95rem; font-weight: 800;
}
.hospital-modern-ui .hospital-patient-summary__item--history strong {
  font-size: .72rem; font-weight: 600; line-height: 1.3;
}
.hospital-modern-ui:not(.hospital-patient-route) .app-content.content {
  padding-top: 4.45rem !important;
}
.hospital-modern-ui .hospital-shared-breadcrumb {
  position: relative; z-index: 12; display: flex; align-items: center; gap: .55rem;
  min-height: 28px; margin: 0 0 .85rem; pointer-events: auto;
  color: var(--hospital-muted); font-size: .78rem; font-weight: 600;
}
.hospital-modern-ui .hospital-shared-breadcrumb a {
  display: inline-flex; align-items: center; gap: .4rem; color: var(--hospital-blue);
  font-weight: 800; text-decoration: none; transition: color .18s ease, transform .18s ease;
}
.hospital-modern-ui .hospital-shared-breadcrumb a:hover,
.hospital-modern-ui .hospital-shared-breadcrumb a:focus {
  color: var(--hospital-navy); text-decoration: none; transform: translateX(-2px);
}
.hospital-modern-ui .hospital-shared-breadcrumb__separator { color: #9db1c0; }
.hospital-modern-ui .hospital-shared-breadcrumb__current { color: var(--hospital-muted); }
.hospital-modern-ui.hospital-internal-route .module-breadcrumb,
.hospital-modern-ui.hospital-internal-route .pharmacy-breadcrumb { display: none !important; }
.hospital-modern-ui .dropdown-user-link {
  display: flex; align-items: center; gap: .7rem; padding: .42rem .6rem .42rem .85rem !important;
  border: 1px solid transparent; border-radius: 11px; transition: background .18s ease, border-color .18s ease;
}
.hospital-modern-ui .dropdown-user-link:hover,
.hospital-modern-ui .dropdown-user.show .dropdown-user-link {
  border-color: var(--hospital-border); background: #eef7fa;
}
.hospital-modern-ui .dropdown-user-link .user-name { color: var(--hospital-navy); }
.hospital-modern-ui .dropdown-user-link .user-status { color: var(--hospital-muted); font-size: .68rem; }
.hospital-modern-ui .hospital-user-menu {
  width: 350px !important; min-width: 350px !important; margin-top: .7rem !important; overflow: hidden; padding: 0 !important;
  border: 1px solid var(--hospital-border) !important; border-radius: 14px !important;
  background: #fff; box-shadow: 0 22px 50px rgba(7,55,95,.2) !important;
}
.hospital-modern-ui .hospital-user-card {
  display: flex; align-items: center; gap: 16px; padding: 20px; border-bottom: 1px solid var(--hospital-border);
  background: linear-gradient(135deg,#f8fcfe,#e7f4f9); text-align: left;
}
.hospital-modern-ui .hospital-user-card-avatar {
  position: relative; width: 68px; height: 68px; flex: 0 0 68px; margin: 0; padding: 4px;
  border: 1px solid #cfe2eb; border-radius: 50%; background: #fff;
  box-shadow: 0 8px 20px rgba(7,55,95,.12);
}
.hospital-modern-ui .hospital-user-card-avatar img {
  display: block; width: 100%; height: 100%; border-radius: 50%; object-fit: cover;
}
.hospital-modern-ui .hospital-user-card-avatar span {
  position: absolute; right: 3px; bottom: 6px; width: 15px; height: 15px;
  border: 3px solid #fff; border-radius: 50%; background: #24b36b;
}
.hospital-modern-ui .hospital-user-card-info { min-width: 0; flex: 1; }
.hospital-modern-ui .hospital-user-card p {
  display: inline-flex; align-items: center; gap: 6px; margin: 0 0 6px; color: #16845f; font-size: .7rem; font-weight: 700;
}
.hospital-modern-ui .hospital-user-card-info p span {
  width: 7px; height: 7px; border-radius: 50%; background: #24b36b;
  box-shadow: 0 0 0 3px rgba(36,179,107,.12);
}
.hospital-modern-ui .hospital-user-card strong {
  display: block; overflow: hidden; color: var(--hospital-navy); font-size: 1rem; font-weight: 800;
  line-height: 1.25; text-overflow: ellipsis; white-space: nowrap;
}
.hospital-modern-ui .hospital-user-card small {
  display: block; margin-top: 5px; color: var(--hospital-muted); font-size: .7rem; font-weight: 600; text-transform: uppercase;
}
.hospital-modern-ui .hospital-user-actions {
  display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 14px;
}
.hospital-modern-ui .hospital-user-action {
  display: inline-flex; min-height: 44px; align-items: center; justify-content: center; gap: 8px;
  padding: .7rem .65rem; border: 1px solid #cbdde7; border-radius: 10px;
  background: #f7fbfd; color: var(--hospital-navy); font-size: .8rem; font-weight: 700;
  text-decoration: none; transition: border-color .18s ease, background .18s ease, transform .18s ease;
}
.hospital-modern-ui .hospital-user-action:hover,
.hospital-modern-ui .hospital-user-action:focus {
  border-color: var(--hospital-blue); background: #edf7fb; color: var(--hospital-blue);
  text-decoration: none; transform: translateY(-1px);
}
.hospital-modern-ui .hospital-user-action svg { width: 17px; height: 17px; flex: 0 0 17px; }
.hospital-modern-ui .hospital-user-action span { white-space: nowrap; }
.hospital-modern-ui .hospital-v3-workspace-link {
  grid-column: 1 / -1; justify-content: flex-start; padding: .78rem .85rem;
  border-color: #9fdbe2; background: linear-gradient(120deg, #edfafd, #e6f7f5); color: #075d76;
  box-shadow: 0 5px 14px rgba(8, 124, 164, .09);
}
.hospital-modern-ui .hospital-v3-workspace-link:hover,
.hospital-modern-ui .hospital-v3-workspace-link:focus {
  border-color: #55bdc8; background: linear-gradient(120deg, #def5f9, #d7f2ed); color: #064e67;
}
.hospital-modern-ui .hospital-v3-workspace-link > svg:first-child {
  width: 30px; height: 30px; padding: 7px; border-radius: 8px; color: #fff;
  background: linear-gradient(135deg, #087ca4, #13a99f); box-shadow: 0 5px 11px rgba(8, 124, 164, .2);
}
.hospital-modern-ui .hospital-v3-workspace-link span { display: flex; min-width: 0; flex: 1; flex-direction: column; align-items: flex-start; }
.hospital-modern-ui .hospital-v3-workspace-link strong { font-size: .79rem; line-height: 1.2; }
.hospital-modern-ui .hospital-v3-workspace-link small { margin-top: 2px; color: #638595; font-size: .64rem; font-weight: 500; white-space: normal; }
.hospital-modern-ui .hospital-v3-workspace-link .hospital-v3-workspace-arrow { width: 14px; height: 14px; color: #1594a2; }
.hospital-modern-ui .hospital-user-signout {
  border-color: #efcfd2; background: #fff7f7; color: #b43d45;
}
.hospital-modern-ui .hospital-user-signout:hover,
.hospital-modern-ui .hospital-user-signout:focus {
  border-color: #d85a62; background: #fff0f1; color: #9f2831;
}
.hospital-modern-ui .hospital-profile-loading,
.hospital-modern-ui .hospital-profile-error {
  display: flex; min-height: 230px; align-items: center; justify-content: center; gap: 10px;
  flex-direction: column; padding: 2rem; background: #fff; color: var(--hospital-navy); text-align: center;
}
.hospital-modern-ui .hospital-profile-error i { color: #c84952; font-size: 2rem; }
.hospital-modern-ui .hospital-profile-error span { color: var(--hospital-muted); font-size: .8rem; }
.hospital-modern-ui .content-header {
  margin: 0 0 1rem; padding: 1.15rem 1.35rem; border: 1px solid var(--hospital-border);
  border-radius: 12px; background: linear-gradient(120deg,#fff 0%,#f2fbfd 100%);
  box-shadow: 0 10px 28px rgba(7,55,95,.06);
}
.hospital-modern-ui .content-header h1, .hospital-modern-ui .content-header h2,
.hospital-modern-ui .content-header h3, .hospital-modern-ui .content-header h4 {
  margin: 0; color: var(--hospital-navy); font-weight: 800; letter-spacing: -.02em;
}
.hospital-modern-ui .card {
  border: 1px solid var(--hospital-border); border-radius: 12px; background: var(--hospital-surface);
  box-shadow: 0 12px 30px rgba(7,55,95,.07);
}
.hospital-modern-ui .card-header {
  min-height: 52px; padding: 1rem 1.25rem; border-bottom: 1px solid var(--hospital-border);
  border-radius: 12px 12px 0 0; background: linear-gradient(90deg,#f7fcfe,#e9f7fb); color: var(--hospital-navy);
}
.hospital-modern-ui .card-header.bg-primary, .hospital-modern-ui .card-header.bg.bg-primary,
.hospital-modern-ui .card-header.colorbasic {
  border-bottom: 0; background: linear-gradient(90deg,var(--hospital-navy),var(--hospital-blue) 58%,var(--hospital-cyan)) !important; color: #fff;
}
.hospital-modern-ui .card-title, .hospital-modern-ui .card-header h3,
.hospital-modern-ui .card-header h4, .hospital-modern-ui .card-header label { color: inherit; font-weight: 800; }
.hospital-modern-ui .card-body { padding: 1.25rem; }
.hospital-modern-ui label, .hospital-modern-ui .control-label { color: var(--hospital-text); font-size: .78rem; font-weight: 700; }
.hospital-modern-ui .form-control,
.hospital-modern-ui .select2-container--default .select2-selection--single,
.hospital-modern-ui .select2-container--default .select2-selection--multiple {
  min-height: 40px; border-color: var(--hospital-border); border-radius: 8px;
  background-color: #fff; color: var(--hospital-text); box-shadow: none;
}
.hospital-modern-ui .form-control:focus, .hospital-modern-ui .select2-container--focus .select2-selection {
  border-color: var(--hospital-cyan) !important; box-shadow: 0 0 0 3px rgba(34,184,207,.14) !important;
}
.hospital-modern-ui .btn { border-radius: 8px; font-weight: 700; box-shadow: none; }
.hospital-modern-ui .btn-primary, .hospital-modern-ui .btn-info {
  border-color: var(--hospital-blue) !important; background: var(--hospital-blue) !important;
}
.hospital-modern-ui .btn-primary:hover, .hospital-modern-ui .btn-info:hover {
  border-color: var(--hospital-navy) !important; background: var(--hospital-navy) !important;
}
.hospital-modern-ui .table-responsive {
  border: 1px solid var(--hospital-border); border-radius: 10px; background: #fff;
}
.hospital-modern-ui .table { margin-bottom: 0; color: var(--hospital-text); }
.hospital-modern-ui .table thead th {
  padding: .9rem .85rem; border-top: 0; border-bottom: 1px solid var(--hospital-border);
  background: #edf7fa; color: var(--hospital-navy); font-size: .73rem; font-weight: 800;
  letter-spacing: .035em; text-transform: uppercase; white-space: nowrap;
}
.hospital-modern-ui .table tbody td { padding: .85rem; border-color: #e8f0f4; vertical-align: middle; }
.hospital-modern-ui .table-striped tbody tr:nth-of-type(odd) { background-color: #f8fbfc; }
.hospital-modern-ui .modal-content {
  overflow: hidden; border: 0; border-radius: 12px; box-shadow: 0 24px 60px rgba(7,55,95,.24);
}
.hospital-modern-ui .modal-header { border-bottom-color: var(--hospital-border); background: var(--hospital-soft); color: var(--hospital-navy); }
.hospital-modern-ui .dataTables_wrapper .dt-buttons {
  display: flex; flex-wrap: wrap; gap: .4rem; margin-bottom: .75rem;
}
.hospital-modern-ui .dataTables_wrapper .dt-buttons .btn { margin: 0; }
.hospital-modern-ui .badge { border-radius: 999px; padding: .42rem .65rem; font-weight: 700; }
/* Responsive safety net for legacy hospital screens. */
.hospital-modern-ui,
.hospital-modern-ui .app-content,
.hospital-modern-ui .content-wrapper,
.hospital-modern-ui .card,
.hospital-modern-ui .card-body,
.hospital-modern-ui .row > [class*="col-"] { min-width: 0; }
.hospital-modern-ui img,
.hospital-modern-ui svg,
.hospital-modern-ui video,
.hospital-modern-ui canvas { max-width: 100%; }
.hospital-modern-ui .table-responsive,
.hospital-modern-ui .dataTables_wrapper { width: 100%; max-width: 100%; }
.hospital-modern-ui .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.hospital-modern-ui .form-inline { display: flex; flex-wrap: wrap; gap: .55rem; align-items: flex-end; }
.hospital-modern-ui .form-inline .form-group { margin: 0; }
.hospital-modern-ui .card-header,
.hospital-modern-ui .modal-header,
.hospital-modern-ui .modal-footer { gap: .65rem; }
.hospital-modern-ui .card-header { flex-wrap: wrap; }
.hospital-modern-ui .modal-dialog { max-width: calc(100vw - 2rem); }
.hospital-modern-ui .select2-container { max-width: 100% !important; }
html.dark-layout .hospital-modern-ui {
  --hospital-bg: #12202c; --hospital-surface: #182b3a; --hospital-soft: #203746;
  --hospital-border: #315064; --hospital-text: #e4eff5; --hospital-muted: #a9bdc9;
}
html.dark-layout .hospital-modern-ui .content-header, html.dark-layout .hospital-modern-ui .card,
html.dark-layout .hospital-modern-ui .table-responsive, html.dark-layout .hospital-modern-ui .form-control {
  background: var(--hospital-surface); color: var(--hospital-text);
}
html.dark-layout .hospital-modern-ui .card-header, html.dark-layout .hospital-modern-ui .table thead th {
  background: var(--hospital-soft); color: #e9f7fb;
}
html.dark-layout .hospital-modern-ui .table { color: var(--hospital-text); }
html.dark-layout .hospital-modern-ui .table-striped tbody tr:nth-of-type(odd) { background-color: rgba(255,255,255,.025); }
html.dark-layout .hospital-modern-ui .dropdown-user-link:hover,
html.dark-layout .hospital-modern-ui .dropdown-user.show .dropdown-user-link,
html.dark-layout .hospital-modern-ui .hospital-user-menu,
html.dark-layout .hospital-modern-ui .hospital-user-card,
html.dark-layout .hospital-modern-ui .hospital-user-action {
  background: var(--hospital-surface); color: var(--hospital-text);
}
html.dark-layout .hospital-modern-ui .hospital-v3-workspace-link {
  border-color: #326b72; background: linear-gradient(120deg, #173f4c, #174844); color: #dffbfc;
}
html.dark-layout .hospital-modern-ui .hospital-v3-workspace-link small { color: #9ac3c8; }
html.dark-layout .hospital-modern-ui .hospital-company-logo-fallback {
  border-color: #3c6375; background: linear-gradient(145deg,#203b4a,#18303d); color: #78c9dd;
}
html.dark-layout .hospital-modern-ui .hospital-ambulatory-link {
  border-color: #315064; background: #192f3c; color: #dcebf2;
}
html.dark-layout .hospital-modern-ui .hospital-admin-visible-link {
  border-color: #465678; background: #222f49; color: #e0e6ff;
}
html.dark-layout .hospital-modern-ui .hospital-patient-summary__card {
  border-color: var(--hospital-border); background: var(--hospital-surface);
}
html.dark-layout .hospital-modern-ui .hospital-patient-summary__item {
  border-color: var(--hospital-border); background: var(--hospital-surface);
}
html.dark-layout .hospital-modern-ui .hospital-patient-summary__item--accent {
  background: #203746;
}
html.dark-layout .hospital-modern-ui .hospital-patient-summary__item span { color: #a9bdc9; }
@media (max-width: 575.98px) {
  .hospital-modern-ui .hospital-ambulatory-link { width: 38px; justify-content: center; margin-left: .35rem; padding: .45rem; }
  .hospital-modern-ui .hospital-ambulatory-link span { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; }
  .hospital-modern-ui .hospital-patient-summary { padding-right: .8rem; padding-left: .8rem; }
  .hospital-modern-ui .hospital-patient-summary__header { align-items: flex-start; padding: 14px; }
  .hospital-modern-ui .hospital-patient-summary__header h2 { font-size: 1rem; }
  .hospital-modern-ui .hospital-patient-summary__grid { grid-template-columns: 1fr; }
  .hospital-modern-ui .hospital-patient-summary__item,
  .hospital-modern-ui .hospital-patient-summary__item:last-child {
    min-height: auto; padding: 12px 14px; border-right: 0; border-bottom: 1px solid #e2edf2;
  }
  .hospital-modern-ui .hospital-patient-summary__item:last-child { border-bottom: 0; }
}
@media (min-width: 576px) and (max-width: 767.98px) {
  .hospital-modern-ui .hospital-patient-summary__grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
  .hospital-modern-ui .hospital-patient-summary__item { border-bottom: 1px solid #e2edf2; }
  .hospital-modern-ui .hospital-patient-summary__item--doctor,
  .hospital-modern-ui .hospital-patient-summary__item--history { grid-column: span 2; }
}
@media (max-width: 767.98px) {
  html, body { max-width: 100%; overflow-x: hidden; }
  .hospital-modern-ui .content-wrapper { padding: 1rem .8rem 1.5rem; }
  .hospital-modern-ui:not(.hospital-patient-route) .app-content.content {
    padding-top: 4.45rem !important;
  }
  .hospital-modern-ui .content-header, .hospital-modern-ui .card-body { padding: 1rem; }
  .hospital-modern-ui .table tbody td, .hospital-modern-ui .table thead th { padding: .72rem; }
  .hospital-modern-ui table.table:not(.dataTable) {
    display: block; width: 100%; max-width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;
  }
  .hospital-modern-ui .hospital-user-menu {
    width: calc(100vw - 24px) !important; min-width: 0 !important; max-width: 350px;
  }
  .hospital-modern-ui .content-header { display: flex; flex-wrap: wrap; gap: .65rem; align-items: center; }
  .hospital-modern-ui .content-header h1,
  .hospital-modern-ui .content-header h2,
  .hospital-modern-ui .content-header h3 { width: 100%; font-size: 1.25rem; line-height: 1.25; }
  .hospital-modern-ui .card-header > .card-tools,
  .hospital-modern-ui .card-header > .float-right { position: static; width: 100%; margin: 0 !important; }
  .hospital-modern-ui .dataTables_wrapper .row { margin-right: 0; margin-left: 0; }
  .hospital-modern-ui .dataTables_wrapper [class*="col-"] { width: 100%; padding-right: 0; padding-left: 0; }
  .hospital-modern-ui .dataTables_filter,
  .hospital-modern-ui .dataTables_length,
  .hospital-modern-ui .dataTables_info,
  .hospital-modern-ui .dataTables_paginate { float: none !important; width: 100%; margin: .35rem 0; text-align: left !important; }
  .hospital-modern-ui .dataTables_filter label,
  .hospital-modern-ui .dataTables_length label { display: flex; width: 100%; align-items: center; gap: .45rem; }
  .hospital-modern-ui .dataTables_filter input { min-width: 0; flex: 1; margin-left: 0 !important; }
  .hospital-modern-ui .pagination { flex-wrap: wrap; justify-content: flex-start !important; }
  .hospital-modern-ui .modal-dialog { width: auto; margin: .75rem auto; }
  .hospital-modern-ui .modal-body { max-height: calc(100vh - 9rem); overflow-y: auto; padding: 1rem; }
  .hospital-modern-ui .modal-footer { display: flex; flex-wrap: wrap; }
  .hospital-modern-ui .modal-footer .btn { flex: 1 1 auto; margin: 0 !important; }
  .hospital-modern-ui .btn-group { display: inline-flex; max-width: 100%; flex-wrap: wrap; }
  .hospital-modern-ui input[type="date"],
  .hospital-modern-ui input[type="datetime-local"],
  .hospital-modern-ui input[type="time"] { max-width: 100%; }
}
@media (max-width: 575.98px) {
  .hospital-modern-ui .content-wrapper { padding-right: .65rem; padding-left: .65rem; }
  .hospital-modern-ui .card-body,
  .hospital-modern-ui .content-header { padding: .85rem; }
  .hospital-modern-ui .card-header { padding: .85rem 1rem; }
  .hospital-modern-ui .form-inline,
  .hospital-modern-ui .form-inline .form-group,
  .hospital-modern-ui .form-inline .form-control,
  .hospital-modern-ui .form-inline .select2-container { width: 100% !important; }
  .hospital-modern-ui .card-body > .btn,
  .hospital-modern-ui .content-header > .btn { white-space: normal; }
  .hospital-modern-ui .hospital-user-actions { grid-template-columns: 1fr; }
}
@media (min-width: 768px) and (max-width: 1199.98px) {
  .hospital-modern-ui .hospital-patient-summary__grid { grid-template-columns: repeat(4,minmax(0,1fr)); }
  .hospital-modern-ui .hospital-patient-summary__item { border-bottom: 1px solid #e2edf2; }
  .hospital-modern-ui .hospital-patient-summary__item--doctor { grid-column: span 2; }
  .hospital-modern-ui .hospital-patient-summary__item--history { grid-column: span 2; }
}

/* Cobertura oscura compartida para los modulos hospitalarios heredados y modernos. */
html.dark-layout body.hospital-modern-ui,
html.dark-layout .hospital-modern-ui .app-content,
html.dark-layout .hospital-modern-ui .content-wrapper,
html.dark-layout .hospital-modern-ui .content-body {
  color: #dce9f0;
  background: #0f202c !important;
}
html.dark-layout .hospital-modern-ui .card,
html.dark-layout .hospital-modern-ui .box,
html.dark-layout .hospital-modern-ui .box-body,
html.dark-layout .hospital-modern-ui .modal-content,
html.dark-layout .hospital-modern-ui .dropdown-menu,
html.dark-layout .hospital-modern-ui .bed-board-wrap,
html.dark-layout .hospital-modern-ui .bed-panel,
html.dark-layout .hospital-modern-ui .bed-summary-card,
html.dark-layout .hospital-modern-ui .bed-occupancy,
html.dark-layout .hospital-modern-ui .bed-room-card,
html.dark-layout .hospital-modern-ui .floor-block,
html.dark-layout .hospital-modern-ui .module-workspace,
html.dark-layout .hospital-modern-ui .module-card-tool,
html.dark-layout .hospital-modern-ui .pharmacy-workspace,
html.dark-layout .hospital-modern-ui .pharmacy-card-tool,
html.dark-layout .hospital-modern-ui .hospital-patient-summary {
  border-color: #365465 !important;
  color: #dce9f0 !important;
  background: #182d3a !important;
  box-shadow: 0 8px 24px rgba(0,0,0,.2) !important;
}
html.dark-layout .hospital-modern-ui .bed-panel-body,
html.dark-layout .hospital-modern-ui .box-body,
html.dark-layout .hospital-modern-ui .card-body,
html.dark-layout .hospital-modern-ui .modal-body,
html.dark-layout .hospital-modern-ui .dataTables_wrapper,
html.dark-layout .hospital-modern-ui .table-responsive {
  border-color: #355160 !important;
  color: #d7e5ed !important;
  background: #182d3a !important;
}
html.dark-layout .hospital-modern-ui h1,
html.dark-layout .hospital-modern-ui h2,
html.dark-layout .hospital-modern-ui h3,
html.dark-layout .hospital-modern-ui h4,
html.dark-layout .hospital-modern-ui h5,
html.dark-layout .hospital-modern-ui h6,
html.dark-layout .hospital-modern-ui label,
html.dark-layout .hospital-modern-ui .box-title,
html.dark-layout .hospital-modern-ui .card-title,
html.dark-layout .hospital-modern-ui .bed-page-title,
html.dark-layout .hospital-modern-ui .bed-room-title { color: #e8f2f7 !important; }
html.dark-layout .hospital-modern-ui p,
html.dark-layout .hospital-modern-ui small,
html.dark-layout .hospital-modern-ui .text-muted,
html.dark-layout .hospital-modern-ui .bed-room-type,
html.dark-layout .hospital-modern-ui .patient-search-help,
html.dark-layout .hospital-modern-ui .module-empty-copy { color: #a8bdc9 !important; }
html.dark-layout .hospital-modern-ui input,
html.dark-layout .hospital-modern-ui textarea,
html.dark-layout .hospital-modern-ui select,
html.dark-layout .hospital-modern-ui .form-control,
html.dark-layout .hospital-modern-ui .custom-select,
html.dark-layout .hospital-modern-ui .select2-selection,
html.dark-layout .hospital-modern-ui .select2-dropdown,
html.dark-layout .hospital-modern-ui .select2-search__field,
html.dark-layout .hospital-modern-ui .flatpickr-input {
  border-color: #3c5a6a !important;
  color: #e3edf3 !important;
  background: #203845 !important;
}
html.dark-layout .hospital-modern-ui input::placeholder,
html.dark-layout .hospital-modern-ui textarea::placeholder { color: #829aa8 !important; }
html.dark-layout .hospital-modern-ui .select2-results__option { color: #dbe8ef; background: #1b313e; }
html.dark-layout .hospital-modern-ui .select2-results__option--highlighted { color: #fff; background: #1186a2 !important; }
html.dark-layout .hospital-modern-ui .table,
html.dark-layout .hospital-modern-ui table { color: #d9e6ed !important; background: #182d3a !important; }
html.dark-layout .hospital-modern-ui .table thead th,
html.dark-layout .hospital-modern-ui table thead th {
  border-color: #3a5666 !important;
  color: #edf7fa !important;
  background: #263c4b !important;
}
html.dark-layout .hospital-modern-ui .table td,
html.dark-layout .hospital-modern-ui table td { border-color: #304b5b !important; }
html.dark-layout .hospital-modern-ui .table-striped tbody tr:nth-of-type(odd),
html.dark-layout .hospital-modern-ui .table-hover tbody tr:hover { background: #203845 !important; }
html.dark-layout .hospital-modern-ui .alert-info,
html.dark-layout .hospital-modern-ui .patient-result-empty,
html.dark-layout .hospital-modern-ui .selected-patient-box,
html.dark-layout .hospital-modern-ui .bed-setup-empty {
  border-color: #2f6474 !important;
  color: #bfe2ea !important;
  background: #183c49 !important;
}
html.dark-layout .hospital-modern-ui .bed-occupancy-bar,
html.dark-layout .hospital-modern-ui .progress { background: #294451 !important; }
html.dark-layout .hospital-modern-ui .bed-chip {
  border-color: #3c5a69;
  color: #c9dae2;
  background: #1b3240;
}
html.dark-layout .hospital-modern-ui .bed-chip.active { border-color: #159bb0; color: #fff; background: #087ca4; }
html.dark-layout .hospital-modern-ui .pagination .page-link {
  border-color: #3a5666;
  color: #cde0e8;
  background: #1c3340;
}
html.dark-layout .hospital-modern-ui .pagination .active .page-link { color: #fff; background: #087ca4; }
html.dark-layout .hospital-modern-ui hr { border-color: #36505f; }
html.dark-layout body > .select2-container .select2-dropdown,
html.dark-layout body > .select2-container .select2-search__field,
html.dark-layout body > .flatpickr-calendar,
html.dark-layout body > .daterangepicker,
html.dark-layout body > .swal2-popup {
  border-color: #3b5868 !important;
  color: #dce9f0 !important;
  background: #182d3a !important;
}
html.dark-layout body > .select2-container .select2-results__option,
html.dark-layout body > .flatpickr-calendar .flatpickr-day,
html.dark-layout body > .daterangepicker td,
html.dark-layout body > .daterangepicker th { color: #d7e5ed; background: #182d3a; }
html.dark-layout body > .select2-container .select2-results__option--highlighted,
html.dark-layout body > .flatpickr-calendar .flatpickr-day.selected { color: #fff; background: #0b86a4 !important; }
</style>
