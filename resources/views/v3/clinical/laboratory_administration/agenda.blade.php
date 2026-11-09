@include('v3.clinical.laboratory_administration._shared')
<div class="co-page la-page">
  <section class="co-hero">
    <div class="co-hero__main">
      <div class="co-hero__icon"><i class="fas fa-calendar-check"></i></div>
      <div><div class="co-eyebrow">LABORATORIO · AGENDA V3</div><h1>Agenda de laboratorio</h1><p>Atenciones de las especialidades de laboratorio configuradas para la empresa activa.</p></div>
    </div>
  </section>
  <section class="co-card la-agenda-card" id="la-agenda-card" data-endpoint="{{route('v3.laboratory-agenda.index')}}" data-today="{{date('Y-m-d')}}">
    @include('v3.clinical.laboratory_administration._agenda_calendar')
    <div class="la-agenda-loading" hidden aria-hidden="true"><span class="la-spinner"></span><strong>Actualizando agenda…</strong></div>
  </section>
</div>

<style>
  .la-agenda-card{position:relative;min-height:210px}.la-agenda-results{padding:12px 16px}.la-agenda-loading{position:absolute;inset:0;z-index:5;display:flex;align-items:center;justify-content:center;gap:9px;background:rgba(255,255,255,.84);color:#0b5f89;border-radius:inherit}.la-agenda-loading[hidden]{display:none}.la-spinner{width:22px;height:22px;border:3px solid #c8e5ed;border-top-color:#0b8fb4;border-radius:50%;animation:la-spin .7s linear infinite}.la-agenda-error{margin:10px 16px;padding:9px 12px;border:1px solid #f2b8b8;border-radius:8px;background:#fff2f2;color:#9d2929;font-size:12px}@keyframes la-spin{to{transform:rotate(360deg)}}
</style>
<script defer src="{{ \Sis_medico\Support\V3Asset::url('v3/dist/js/laboratory-agenda.js') }}"></script>
