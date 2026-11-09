<style>
  .plq-page{padding:22px}.plq-hero{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:27px 30px;margin-bottom:18px;border-radius:18px;color:#fff;background:linear-gradient(115deg,#145a91,#1388a8 58%,#11aa94);box-shadow:0 13px 30px rgba(18,83,116,.15)}.plq-hero__main{display:flex;align-items:center;gap:18px}.plq-hero__icon{display:grid;place-items:center;width:60px;height:60px;border:1px solid rgba(255,255,255,.3);border-radius:16px;background:rgba(255,255,255,.12);font-size:25px}.plq-hero h1{margin:2px 0 5px;font-size:28px;font-weight:800}.plq-hero p{margin:0;opacity:.94}.plq-eyebrow{font-size:11px;font-weight:800;letter-spacing:1.15px}.plq-counter{padding:8px 12px;border:1px solid rgba(255,255,255,.35);border-radius:9px;background:rgba(255,255,255,.12);font-size:12px;font-weight:800}
  .plq-card{overflow:hidden;margin-bottom:16px;border:1px solid #d6e5eb;border-radius:16px;background:#fff;box-shadow:0 8px 22px rgba(29,76,99,.06)}.plq-card__head{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #dce9ee;background:#fff}.plq-card__head h2{margin:0;color:#143e5e;font-size:18px;font-weight:800}.plq-card__head p{margin:3px 0 0;color:#6c899e;font-size:13px}.plq-filters{display:grid;grid-template-columns:185px 185px minmax(220px,1.35fr) minmax(215px,1.1fr) minmax(185px,.9fr) auto;align-items:end;gap:12px;padding:19px 20px}.plq-field{min-width:0}.plq-field label{display:block;margin-bottom:6px;color:#315873;font-size:11px;font-weight:800;text-transform:uppercase}.plq-field .form-control{height:40px;border-color:#c9dde7;border-radius:9px;font-size:13px}.plq-field .select2-container{display:block;width:100%!important}.plq-field .select2-container--bootstrap4 .select2-selection--single{position:relative!important;height:40px!important;min-height:40px!important;border-color:#c9dde7!important;border-radius:9px!important}.plq-field .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered{padding-left:12px!important;padding-right:34px!important;line-height:38px!important;color:#355970!important;font-size:13px}.plq-field .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow{position:absolute!important;top:0!important;right:5px!important;width:24px!important;height:38px!important}.plq-field .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow b{top:50%!important;margin-top:-2px!important}.plq-field .select2-selection__clear{margin-right:25px!important}.plq-input-group{display:flex}.plq-input-group .input-group-text{display:flex;align-items:center;justify-content:center;width:40px;border-color:#c9dde7;background:#eef7fb;color:#247da5}.plq-input-group .form-control{border-left:0}.plq-action{display:flex;align-items:flex-end}.plq-action .btn{height:40px;border-radius:9px;font-size:13px;font-weight:800;white-space:nowrap}.plq-tip{grid-column:1/-1;margin:0;padding-top:12px;border-top:1px solid #e2edf1;color:#6d899d;font-size:12px}
  .plq-table-wrap{overflow:auto}.plq-table{min-width:1180px;margin:0}.plq-table thead th{padding:11px 13px;border-top:0;background:#f1f7f9;color:#41637a;font-size:10px;font-weight:800;text-transform:uppercase}.plq-table td{padding:12px 13px;vertical-align:middle;color:#36566b;font-size:12px}.plq-table tbody tr:hover{background:#f8fcfd}.plq-patient{display:block;color:#164b6d;font-weight:800}.plq-procedure{max-width:240px}.plq-meta{display:block;margin-top:3px;color:#7c97a8;font-size:11px}.plq-badge{display:inline-block;padding:5px 9px;border-radius:16px;color:#087c69;background:#e2f6f0;font-size:10px;font-weight:800}.plq-badge--pending{color:#a36412;background:#fff2dc}.plq-actions{display:flex;gap:6px;white-space:nowrap}.plq-actions .btn{border-radius:7px;font-size:11px;font-weight:800}.plq-empty{padding:44px!important;text-align:center;color:#6e899d}.plq-footer{display:flex;align-items:center;justify-content:space-between;padding:15px 20px;border-top:1px solid #e4eef2;color:#6a8799;font-size:12px}.plq-footer .pagination{margin:0}.plq-footer .page-link{border-color:#d4e4ec;color:#1d769d}
  @media(max-width:1170px){.plq-filters{grid-template-columns:repeat(3,minmax(170px,1fr))}}@media(max-width:820px){.plq-page{padding:12px}.plq-hero{align-items:flex-start;flex-direction:column;padding:22px 18px}.plq-hero h1{font-size:23px}.plq-filters{grid-template-columns:repeat(2,minmax(140px,1fr))}}@media(max-width:520px){.plq-filters{grid-template-columns:1fr}.plq-action{align-items:stretch}.plq-action .btn{width:100%}.plq-footer{align-items:flex-start;flex-direction:column;gap:10px}}
</style>
<div class="plq-page">
  <section class="plq-hero">
    <div class="plq-hero__main"><div class="plq-hero__icon"><i class="fas fa-file-invoice-dollar"></i></div><div><div class="plq-eyebrow">CUENTAS Y FINANZAS · LIQUIDACIÓN</div><h1>Control de liquidación automática</h1><p>Consulta las atenciones listas para generar o revisar su planilla.</p></div></div>
    <span class="plq-counter"><i class="fas fa-list mr-1"></i> {{ $rows->total() }} registro(s)</span>
  </section>

  @if(session('info'))<div class="alert alert-info"><i class="fas fa-info-circle mr-1"></i>{{ session('info') }}</div>@endif
  <section class="plq-card">
    <header class="plq-card__head"><div><h2>Filtros de liquidación</h2><p>Filtra por fechas, paciente, seguro o tipo de atención.</p></div></header>
    <form class="plq-filters" method="get" action="{{ route('consultam.nuevaLiquidacionPublicaIndex') }}">
      <div class="plq-field"><label for="plq-fecha">Desde</label><div class="plq-input-group"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span><input id="plq-fecha" class="form-control" type="date" name="fecha" value="{{ $filters['fecha'] }}"></div></div>
      <div class="plq-field"><label for="plq-fecha-hasta">Hasta</label><div class="plq-input-group"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span><input id="plq-fecha-hasta" class="form-control" type="date" name="fecha_hasta" value="{{ $filters['fecha_hasta'] }}"></div></div>
      <div class="plq-field"><label for="plq-paciente">Paciente</label><div class="plq-input-group"><span class="input-group-text"><i class="fas fa-user"></i></span><input id="plq-paciente" class="form-control" name="paciente" value="{{ $filters['paciente'] }}" placeholder="Nombres o apellidos"></div></div>
      <div class="plq-field"><label for="plq-seguro">Seguro</label><select id="plq-seguro" class="form-control js-plq-select" name="seguro"><option value="">Todos los seguros</option>@foreach($insurances as $insurance)<option value="{{ $insurance->id }}" {{ $filters['seguro']==$insurance->id ? 'selected' : '' }}>{{ $insurance->nombre }}</option>@endforeach</select></div>
      <div class="plq-field"><label for="plq-tipo">Tipo</label><select id="plq-tipo" class="form-control js-plq-select" name="tipo"><option value="">Todos los tipos</option><option value="0" {{ $filters['tipo']==='0' ? 'selected' : '' }}>Consulta</option><option value="1" {{ $filters['tipo']==='1' ? 'selected' : '' }}>Procedimiento</option></select></div>
      <div class="plq-action"><button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i> Consultar</button></div>
      <div class="plq-tip"><i class="fas fa-shield-alt mr-1"></i> Solo se muestran atenciones públicas finalizadas que cumplen los criterios de liquidación.</div>
    </form>
  </section>

  <section class="plq-card">
    <header class="plq-card__head"><div><h2>Atenciones disponibles</h2><p>Genera una planilla nueva o abre la planilla existente de cada procedimiento.</p></div><span class="badge badge-info">{{ $rows->total() }} disponibles</span></header>
    <div class="plq-table-wrap"><table class="table table-hover plq-table"><thead><tr><th>Fecha</th><th>Paciente</th><th>Seguro</th><th>Tipo / especialidad</th><th>Doctor</th><th>Tipo cita</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
      @forelse($rows as $row)
        <?php
          $isProcedure = (string) $row->proc_consul === '1';
          $state = $row->hora_fin ? 'Finalizada' : ($row->hora_inicio ? 'Iniciada' : 'Pendiente');
          $planillaUrl = $row->planilla_id ? route('applanilla.ap_nuevo_edit', array('id' => $row->planilla_id)) : route('archivo_plano.planilla_hcid_create', array('hcid' => $row->procedure_id, 'id_seguro' => $row->insurance_id));
        ?>
        <tr><td><strong>{{ \Carbon\Carbon::parse($row->fechaini)->format('d/m/Y') }}</strong><span class="plq-meta">{{ \Carbon\Carbon::parse($row->fechaini)->format('H:i') }}</span></td><td><span class="plq-patient">{{ $row->patient_name ?: 'Paciente sin nombre' }}</span><span class="plq-meta">{{ $row->patient_id }}</span></td><td>{{ $row->insurance_name }}</td><td class="plq-procedure"><strong>{{ $isProcedure ? 'Procedimiento' : 'Consulta' }}</strong><span class="plq-meta">{{ $row->procedure_name ?: ($row->specialty_name ?: 'Sin especialidad') }}</span></td><td>{{ $row->doctor_name ?: 'Sin asignar' }}</td><td>{{ $row->tipo_cita ?: 'Sin definir' }}</td><td><span class="plq-badge {{ $state === 'Pendiente' ? 'plq-badge--pending' : '' }}">{{ $state }}</span></td><td><div class="plq-actions"><a href="{{ $planillaUrl }}" class="btn btn-sm {{ $row->planilla_id ? 'btn-outline-primary' : 'btn-warning' }} js-plq-planilla" data-title="{{ $row->planilla_id ? 'Abrir planilla' : 'Generar planilla' }}"><i class="fas {{ $row->planilla_id ? 'fa-folder-open' : 'fa-file-medical' }} mr-1"></i>{{ $row->planilla_id ? 'Abrir planilla' : 'Generar planilla' }}</a><a href="{{ route('v3.laboratory-orders.index', array('paciente' => $row->patient_id)) }}" class="btn btn-sm btn-outline-success" title="Consultar laboratorio del paciente"><i class="fas fa-flask mr-1"></i> Laboratorio</a></div></td></tr>
      @empty
        <tr><td colspan="8" class="plq-empty"><i class="fas fa-calendar-times fa-2x d-block mb-2"></i><strong>No hay atenciones disponibles con estos filtros.</strong><br>Prueba con otro periodo o consulta todos los seguros.</td></tr>
      @endforelse
    </tbody></table></div>
    <footer class="plq-footer"><span>Mostrando {{ $rows->firstItem() ?: 0 }} a {{ $rows->lastItem() ?: 0 }} de {{ $rows->total() }} registros</span>{{ $rows->links() }}</footer>
  </section>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (window.jQuery && jQuery.fn.select2) {
    jQuery('.js-plq-select').select2({ theme: 'bootstrap4', width: '100%', minimumResultsForSearch: 0 });
  }
  document.querySelectorAll('.js-plq-planilla').forEach(function (link) {
    link.addEventListener('click', function (event) {
      if (!window.Swal) { return; }
      event.preventDefault();
      Swal.fire({ title: link.dataset.title, text: 'Se abrirá el flujo de planilla para este procedimiento.', icon: 'question', showCancelButton: true, confirmButtonText: 'Continuar', cancelButtonText: 'Cancelar' }).then(function (result) { if (result.isConfirmed) { window.location.href = link.href; } });
    });
  });
});
</script>
@include('v3.layouts.liquidacion_i18n')
