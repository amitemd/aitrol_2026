@extends('layouts.app-template')
@section('content')
<style>
  .amortizacion-wrapper { min-height: calc(100vh - 150px) !important; height:auto !important; background: #f4f7fb; padding-bottom: 28px; overflow:visible; }
  .amortizacion-wrapper .content-header { padding: 24px 30px 10px; }
  .amortizacion-wrapper .content-header h1 { margin: 0; color: #17324d; font-size: 25px; font-weight: 600; }
  .amortizacion-wrapper .content-header p { color: #6b7c8f; margin: 6px 0 0; }
  .amortizacion-wrapper .content { padding: 0 30px 30px; }
  .amortizacion-card { border: 0; border-radius: 8px; box-shadow: 0 2px 12px rgba(26, 54, 80, .08); }
  .amortizacion-card .box-header { padding: 18px 22px; border-bottom: 1px solid #e8eef4; }
  .amortizacion-card .box-title { color: #17324d; font-size: 17px; font-weight: 600; }
  .amortizacion-card .box-body { padding: 22px; }
  .amortizacion-kpi { border-left: 4px solid #2478bd; background: #fff; border-radius: 7px; padding: 16px 18px; min-height: 82px; box-shadow: 0 2px 10px rgba(26, 54, 80, .06); }
  .amortizacion-kpi small { display: block; color: #718096; text-transform: uppercase; font-size: 10px; letter-spacing: .4px; }
  .amortizacion-kpi strong { display: block; color: #17324d; font-size: 20px; margin-top: 5px; }
  .amortizacion-table thead th { background: #244e70; color: #fff; border: 0; font-size: 11px; text-transform: uppercase; letter-spacing: .2px; white-space: nowrap; }
  .amortizacion-table thead th.text-right { text-align: right; }
  .amortizacion-table tfoot td { background:#dceaf7; font-weight:700; border-top:2px solid #244e70; }
  .capital-pendiente { position:relative; min-width:145px; overflow:hidden; text-align:right; }
  .capital-pendiente .capital-bar { position:absolute; top:2px; bottom:2px; left:0; background:linear-gradient(90deg,#a53f9d,#ead5e8); opacity:.95; z-index:0; }
  .capital-pendiente .capital-value { position:relative; z-index:1; display:block; padding:0 4px; }
  .amortizacion-table tbody td { vertical-align: middle; border-color: #edf1f5; }
  .amortizacion-table tbody tr:hover { background: #f7fbff; }
  .amortizacion-table tbody tr.amortizacion-cancelado { opacity:.65; background:#fff7f7; }
  .amortizacion-form label { color: #41566b; font-weight: 600; font-size: 12px; }
  .amortizacion-form .form-control { border-radius: 5px; border-color: #cbd7e3; box-shadow: none; }
  .amortizacion-section { color: #2478bd; border-bottom: 2px solid #e7eef5; padding-bottom: 9px; margin: 8px 0 18px; font-weight: 600; }
  .amortizacion-note { color: #6b7c8f; font-size: 12px; }
  .amortizacion-linea { border:1px solid #e3ebf3; border-left:4px solid #2478bd; border-radius:6px; padding:14px 12px 8px; margin-bottom:12px; background:#fbfdff; }
  .amortizacion-linea label { font-size:11px; }
  .amortizacion-wrapper .btn { border-radius: 5px; }
  .amortizacion-wrapper .btn-action { min-width: 104px; font-weight: 600; box-shadow: 0 2px 4px rgba(30,60,90,.12); }
  .amortizacion-wrapper .btn-action i { margin-right: 3px; }
  .amortizacion-wrapper .fa { display:inline-block; width:14px; font-size:13px; line-height:1; text-align:center; vertical-align:middle; }
  .amortizacion-wrapper .btn[disabled] { cursor: not-allowed; opacity: .55; }
  .amortizacion-actions { white-space: nowrap; }
  .amortizacion-actions form { display:inline-block; margin:0 2px 3px 0; }
  .amortizacion-actions .btn { margin:0 2px 3px 0; }
  .amortizacion-disabled-note { display:block; color:#9a6b00; font-size:10px; margin-top:2px; }
  .swal2-container .swal2-popup .swal2-icon { box-sizing:border-box !important; width:82px !important; height:82px !important; margin:1.5em auto .6em !important; }
  .swal2-container .swal2-popup .swal2-icon.swal2-warning { overflow:hidden !important; border-width:4px !important; font-size:48px !important; line-height:74px !important; }
  .swal2-container .swal2-popup .swal2-icon.swal2-warning:before { font-size:48px !important; line-height:74px !important; }
  .swal2-container .swal2-popup .swal2-icon.swal2-warning .swal2-icon-content { font-size:48px !important; line-height:74px !important; }
  .swal2-container .swal2-popup .swal2-title { font-size:25px !important; }
  .main-footer { background: #fff; border-top: 1px solid #dfe8f1; color: #607386; margin-left:0 !important; min-height:52px; box-sizing:border-box; display:flex; align-items:center; justify-content:center; padding:16px 30px; font-size:12px; clear:both; }
  .main-footer strong { color: #17324d; }
  @media (max-width: 767px) { .amortizacion-wrapper .content-header, .amortizacion-wrapper .content { padding-left: 15px; padding-right: 15px; } }
</style>
<div class="content-wrapper cnt_wrapper amortizacion-wrapper">
  @yield('action-content')
</div>
<script>
$(function () {
  $('.amortizacion-wrapper form[data-confirm]').on('submit', function (event) {
    event.preventDefault();
    var form = this;
    Swal.fire({
      icon: 'warning',
      title: 'Confirmar acción',
      text: $(form).data('confirm'),
      showCancelButton: true,
      confirmButtonText: 'Sí, continuar',
      cancelButtonText: 'Cancelar',
      reverseButtons: true
    }).then(function (result) {
      if (result.value) form.submit();
    });
  });
  @if(session('success'))
    Swal.fire({ icon: 'success', title: 'Operación exitosa', text: {!! json_encode(session('success')) !!}, confirmButtonText: 'Aceptar' });
  @endif
  @if($errors->any())
    Swal.fire({ icon: 'error', title: 'No se pudo completar la operación', html: '<ul style="text-align:left;margin:0;padding-left:20px">' + {!! json_encode(implode('</li><li>', array_map('e', $errors->all()))) !!}.replace(/^/, '<li>').replace(/$/, '</li>') + '</ul>', confirmButtonText: 'Entendido' });
  @endif
});
</script>
@endsection
