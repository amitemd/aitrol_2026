<style>
  .v3-clinical-list{color:#294d63;font-family:"Helvetica Neue",Arial,sans-serif;padding:0}
  .v3-clinical-list *{box-sizing:border-box}
  .v3-clinical-list__card{background:#fff;border:1px solid #d2e3eb;border-radius:12px;box-shadow:0 7px 22px rgba(22,77,107,.11);overflow:hidden}
  .v3-clinical-list__head{align-items:center;background:linear-gradient(116deg,#08719c 0%,#118eae 60%,#17aaa5 100%);color:#fff;display:flex;justify-content:space-between;min-height:70px;padding:13px 20px}
  .v3-clinical-list__title{align-items:center;display:flex;min-width:0}
  .v3-clinical-list__icon{align-items:center;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.28);border-radius:9px;display:flex;flex:0 0 42px;font-size:18px;height:42px;justify-content:center;margin-right:12px}
  .v3-clinical-list__eyebrow{color:rgba(255,255,255,.78);font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase}
  .v3-clinical-list__head h3{color:#fff;font-size:18px;font-weight:700;margin:1px 0 2px}
  .v3-clinical-list__head p{color:rgba(255,255,255,.84);font-size:11px;margin:0}
  .v3-clinical-list__actions{display:flex;gap:8px;position:relative;z-index:1}
  .v3-clinical-list__button{align-items:center;background:rgba(255,255,255,.13);border:1px solid rgba(255,255,255,.55);border-radius:7px;color:#fff!important;cursor:pointer;display:inline-flex;font-size:11px;font-weight:700;height:35px;justify-content:center;padding:0 13px;text-decoration:none!important}
  .v3-clinical-list__button:hover,.v3-clinical-list__button:focus{background:#fff;color:#08749b!important}
  .v3-clinical-list__button i,.v3-clinical-list__button span{margin-right:6px}
  .v3-clinical-list__toolbar{align-items:end;background:#f7fbfd;border-bottom:1px solid #dbe8ee;display:grid;gap:12px;grid-template-columns:minmax(260px,520px) auto 1fr;padding:13px 18px}
  .v3-clinical-list__field label{color:#41677d;display:block;font-size:10px;font-weight:700;letter-spacing:.2px;margin:0 0 5px;text-transform:uppercase}
  .v3-clinical-list__input{position:relative}
  .v3-clinical-list__input i{color:#5e879d;left:12px;position:absolute;top:11px;z-index:2}
  .v3-clinical-list__input .form-control{border:1px solid #cbdde6;border-radius:7px;box-shadow:none;color:#294f65;font-size:12px;height:36px;padding-left:35px;width:100%}
  .v3-clinical-list__input .form-control:focus{border-color:#269fbe;box-shadow:0 0 0 3px rgba(25,147,180,.11)}
  .v3-clinical-list__search{align-items:center;background:#087ca7;border:0;border-radius:7px;color:#fff;cursor:pointer;display:inline-flex;font-size:11px;font-weight:700;height:36px;justify-content:center;padding:0 16px}
  .v3-clinical-list__search i{margin-right:6px}
  .v3-clinical-list__count{align-self:center;color:#688597;font-size:11px;justify-self:end}
  .v3-clinical-list__count strong{color:#087ca7}
  .v3-clinical-list__table-wrap{overflow-x:auto;width:100%}
  .v3-clinical-list table.v3-clinical-list__table{border-collapse:collapse!important;margin:0!important;min-width:760px;table-layout:auto;width:100%!important}
  .v3-clinical-list table.v3-clinical-list__table thead th{background:#edf5f8!important;border:0!important;border-bottom:1px solid #d5e4eb!important;color:#456c83!important;font-size:10px!important;font-weight:700!important;padding:10px 12px!important;text-align:left;text-transform:uppercase}
  .v3-clinical-list table.v3-clinical-list__table tbody td{border:0!important;border-bottom:1px solid #e5eef2!important;color:#3b5e73!important;font-size:11px!important;padding:10px 12px!important;vertical-align:middle}
  .v3-clinical-list table.v3-clinical-list__table tbody tr:hover{background:#f1fafb!important}
  .v3-clinical-list__name{color:#086f99;font-weight:700}
  .v3-clinical-list__badge{background:#eaf6fb;border-radius:12px;color:#087ca7;display:inline-block;font-weight:700;min-width:30px;padding:4px 8px;text-align:center}
  .v3-clinical-list__action{align-items:center;border-radius:6px;cursor:pointer;display:inline-flex;font-size:11px;font-weight:700;height:30px;justify-content:center;margin:2px;min-width:70px;padding:0 10px}
  .v3-clinical-list__action i,.v3-clinical-list__action span{margin-right:5px}
  .v3-clinical-list__action--edit{background:#fff7e8;border:1px solid #f0d18f;color:#99680f!important}
  .v3-clinical-list__action--edit:hover{background:#ffefca}
  .v3-clinical-list__action--view{background:#e4f6f3;border:1px solid #b8e2db;color:#167c72!important}
  .v3-clinical-list__action--view:hover{background:#d4f0eb}
  .v3-clinical-list__action--log{background:#edf5fb;border:1px solid #c7ddeb;color:#326f91!important}
  .v3-clinical-list__action--log:hover{background:#dfedf7}
  .v3-clinical-list__action--disabled{background:#f2f5f6;border:1px solid #dce5e9;color:#8a9ca7!important;cursor:not-allowed}
  .v3-clinical-list__action-stack{align-items:center;display:flex;flex-wrap:wrap;gap:4px;min-width:210px}
  .v3-clinical-list__action-stack .v3-clinical-list__action{margin:0;white-space:nowrap}
  .v3-clinical-list__empty{color:#748d9d!important;padding:30px!important;text-align:center}
  .v3-clinical-list__empty i{color:#8fb0c2;display:block;font-size:25px;margin-bottom:7px}
  .v3-clinical-list__footer{align-items:center;background:#fbfdfe;color:#688597;display:flex;font-size:11px;justify-content:space-between;min-height:48px;padding:8px 16px}
  .v3-clinical-list .dataTables_wrapper>.row{margin:0;width:100%}
  .v3-clinical-list .dataTables_wrapper>.row>[class*="col-"]{padding:0;width:100%}
  .v3-clinical-list .dataTables_empty{color:#748d9d!important;padding:30px!important;text-align:center}
  .v3-clinical-list .progress{background:#e5eef2;border-radius:8px;height:12px;margin:0;min-width:90px;overflow:hidden}
  .v3-clinical-list .progress-bar{font-size:9px;line-height:12px}
  @media(max-width:767px){.v3-clinical-list__head{align-items:flex-start;flex-direction:column;gap:12px}.v3-clinical-list__actions{width:100%}.v3-clinical-list__button{flex:1}.v3-clinical-list__toolbar{grid-template-columns:1fr}.v3-clinical-list__search{width:100%}.v3-clinical-list__count{justify-self:start}.v3-clinical-list__footer{align-items:flex-start;flex-direction:column;gap:8px}}
</style>
