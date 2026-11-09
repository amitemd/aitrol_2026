<style>
  .po3{padding:22px}.po3 *{box-sizing:border-box}.po3-hero{display:flex;align-items:center;justify-content:space-between;gap:18px;margin-bottom:17px;padding:24px 27px;border-radius:17px;color:#fff;background:linear-gradient(115deg,#085b85,#0b86ae 62%,#16aaa5);box-shadow:0 12px 28px rgba(20,80,116,.15)}.po3-hero-main{display:flex;align-items:center;gap:16px}.po3-hero-icon{display:grid;place-items:center;width:58px;height:58px;flex:0 0 58px;border:1px solid rgba(255,255,255,.32);border-radius:15px;background:rgba(255,255,255,.12);font-size:25px}.po3-eyebrow{font-size:11px;font-weight:800;letter-spacing:1.1px}.po3-hero h1{margin:3px 0 5px;font-size:26px;font-weight:800}.po3-hero p{margin:0;color:#e7f8fa}.po3-patient{display:flex;align-items:center;gap:12px;min-width:300px;padding:11px 14px;border:1px solid rgba(255,255,255,.28);border-radius:12px;background:rgba(255,255,255,.1)}.po3-avatar{display:grid;place-items:center;width:40px;height:40px;flex:0 0 40px;border-radius:11px;background:#fff;color:#087da5}.po3-patient strong,.po3-patient small{display:block}.po3-patient small{margin-top:2px;color:#d9f4f5}
  .po3-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:11px;margin-bottom:17px}.po3-stat{display:flex;align-items:center;gap:12px;padding:14px 16px;border:1px solid #d3e5ec;border-radius:13px;background:#fff}.po3-stat>i{display:grid;place-items:center;width:40px;height:40px;border-radius:11px;color:#0785b2;background:#e2f5f9}.po3-stat strong,.po3-stat span{display:block}.po3-stat strong{color:#073c61;font-size:18px}.po3-stat span{color:#6d8798;font-size:11px}
  .po3-list{display:grid;gap:13px}.po3-order{overflow:hidden;border:1px solid #d2e4eb;border-radius:15px;background:#fff;box-shadow:0 7px 20px rgba(26,76,100,.06)}.po3-order[open]{border-color:#9fd3df}.po3-order-summary{display:grid;grid-template-columns:minmax(210px,1fr) minmax(210px,1fr) auto auto 18px;align-items:center;gap:15px;padding:16px 18px;cursor:pointer;list-style:none}.po3-order-summary::-webkit-details-marker{display:none}.po3-date strong,.po3-doctor strong{display:block;color:#103f5e}.po3-date small,.po3-doctor small{display:block;margin-bottom:3px;color:#78909f;font-size:10px;font-weight:800;text-transform:uppercase}.po3-type{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:17px;color:#087696;background:#e3f6f8;font-size:10px;font-weight:800}.po3-download{white-space:nowrap;border-radius:8px;font-weight:700}.po3-toggle{color:#0b82a9;transition:transform .2s}.po3-order[open] .po3-toggle{transform:rotate(180deg)}.po3-procedures{display:flex;flex-wrap:wrap;gap:7px;padding:0 18px 15px}.po3-chip{display:inline-flex;padding:6px 9px;border-radius:7px;color:#0b617b;background:#eaf7f9;font-size:11px;font-weight:700}.po3-body{padding:17px 18px 20px;border-top:1px solid #dce9ee;background:#fbfdfe}.po3-alert{display:flex;align-items:center;gap:8px;margin-bottom:13px;padding:10px 12px;border-radius:9px;color:#9a3b2f;background:#fff0ed;font-weight:800}.po3-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:11px}.po3-field{padding:12px;border:1px solid #dce8ed;border-radius:10px;background:#fff}.po3-field.full{grid-column:1/-1}.po3-field label{display:block;margin:0 0 6px;color:#507184;font-size:10px;font-weight:800;text-transform:uppercase}.po3-field div{color:#294e64;line-height:1.55}.po3-empty{padding:58px 20px;border:1px dashed #bfd8e2;border-radius:15px;background:#fff;text-align:center;color:#708b9c}.po3-empty i{display:block;margin-bottom:10px;color:#32a1bc;font-size:30px}.po3-empty strong{display:block;margin-bottom:4px;color:#174966;font-size:16px}
  @media(max-width:950px){.po3-hero{align-items:flex-start;flex-direction:column}.po3-patient{width:100%}.po3-order-summary{grid-template-columns:1fr 1fr}.po3-download{justify-self:start}}@media(max-width:640px){.po3{padding:12px}.po3-hero{padding:20px 17px}.po3-hero h1{font-size:22px}.po3-summary,.po3-grid,.po3-order-summary{grid-template-columns:1fr}.po3-toggle{display:none}.po3-download{width:100%}}
</style>
@php
  $secondLast = in_array($paciente->apellido2, array('(N/A)', 'N/A')) ? '' : $paciente->apellido2;
  $secondName = in_array($paciente->nombre2, array('(N/A)', 'N/A')) ? '' : $paciente->nombre2;
  $patientName = trim($paciente->apellido1.' '.$secondLast.' '.$paciente->nombre1.' '.$secondName);
  $typeLabels = array(0 => 'Endoscópica', 1 => 'Funcional', 2 => 'Imágenes');
@endphp
<main class="po3">
  <header class="po3-hero">
    <div class="po3-hero-main"><div class="po3-hero-icon"><i class="fas fa-notes-medical"></i></div><div><div class="po3-eyebrow">HISTORIA CLÍNICA · V3</div><h1>Órdenes de procedimientos</h1><p>Consulta el historial clínico y descarga las órdenes emitidas para el paciente.</p></div></div>
    <div class="po3-patient"><span class="po3-avatar"><i class="fas fa-user"></i></span><div><strong>{{$patientName ?: 'Paciente sin nombre'}}</strong><small>{{$paciente->id}} · {{$edad !== null ? $edad.' años' : 'Edad no disponible'}}</small></div></div>
  </header>
  <section class="po3-summary">
    <div class="po3-stat"><i class="fas fa-clipboard-list"></i><div><strong>{{$listado_ordenes->count()}}</strong><span>Órdenes registradas</span></div></div>
    <div class="po3-stat"><i class="fas fa-shield-alt"></i><div><strong>{{$seguro ? $seguro->nombre : 'Sin seguro'}}</strong><span>Cobertura del paciente</span></div></div>
    <div class="po3-stat"><i class="fas fa-calendar-check"></i><div><strong>{{$listado_ordenes->first() && $listado_ordenes->first()->fecha_orden ? date('d/m/Y', strtotime($listado_ordenes->first()->fecha_orden)) : 'Sin fecha'}}</strong><span>Última orden</span></div></div>
  </section>
  <section class="po3-list">
    @forelse($listado_ordenes as $order)
      @php
        $doctor = $order->doctor;
        $doctorName = $doctor ? trim($doctor->apellido1.' '.$doctor->apellido2.' '.$doctor->nombre1.' '.$doctor->nombre2) : 'Profesional no identificado';
        $procedureNames = array();
        foreach ($order->orden_tipo as $group) {
          foreach ($group->orden_procedimiento as $item) {
            if ($item->procedimiento) {
              $procedureNames[] = $item->procedimiento->observacion ?: $item->procedimiento->nombre;
            }
          }
        }
        $procedureNames = array_values(array_unique(array_filter($procedureNames)));
        $medicalPdfRoutes = array(
          0 => 'imprimir.ordenhc4_endoscopica',
          1 => 'imprimir.ordenhc4_funcional',
          2 => 'imprimir.ordenhc4_imagenes',
        );
        $medicalPdfRoute = isset($medicalPdfRoutes[(int) $order->tipo_procedimiento])
          ? $medicalPdfRoutes[(int) $order->tipo_procedimiento]
          : 'imprimir.ordenes_hc3';
      @endphp
      <details class="po3-order" {{$loop->first ? 'open' : ''}}>
        <summary class="po3-order-summary">
          <div class="po3-date"><small>Fecha de la orden</small><strong><i class="far fa-calendar-alt mr-1"></i>{{$order->fecha_orden ? date('d/m/Y', strtotime($order->fecha_orden)) : 'Sin fecha registrada'}}</strong></div>
          <div class="po3-doctor"><small>Profesional solicitante</small><strong>{{$doctorName}}</strong></div>
          <span class="po3-type"><i class="fas {{$order->tipo_procedimiento == 2 ? 'fa-x-ray' : 'fa-stethoscope'}}"></i>{{isset($typeLabels[$order->tipo_procedimiento]) ? $typeLabels[$order->tipo_procedimiento] : 'Procedimiento'}} · #{{$order->id}}</span>
          <a class="btn btn-outline-primary po3-download" href="{{route($medicalPdfRoute, array('id_orden' => $order->id))}}" target="_blank" rel="noopener" onclick="event.stopPropagation()" title="Abrir el PDF emitido desde la opción del médico"><i class="fas fa-file-pdf mr-1"></i> Ver PDF</a>
          <i class="fas fa-chevron-down po3-toggle"></i>
        </summary>
        <div class="po3-procedures">
          @forelse($procedureNames as $procedureName)<span class="po3-chip"><i class="fas fa-check-circle mr-1"></i>{{$procedureName}}</span>@empty<span class="po3-chip">Sin procedimientos detallados</span>@endforelse
        </div>
        <div class="po3-body">
          @if($order->necesita_valoracion === 'SI')<div class="po3-alert"><i class="fas fa-heartbeat"></i> Requiere valoración cardiológica</div>@endif
          <div class="po3-grid">
            @if($order->observacion_recepcion)<div class="po3-field full"><label>Observación de recepción</label><div>{!! $order->observacion_recepcion !!}</div></div>@endif
            <div class="po3-field"><label>Antecedentes patológicos</label><div>{!! $paciente->antecedentes_pat ?: 'No registrados' !!}</div></div>
            <div class="po3-field"><label>Antecedentes familiares</label><div>{!! $paciente->antecedentes_fam ?: 'No registrados' !!}</div></div>
            <div class="po3-field full"><label>Antecedentes quirúrgicos</label><div>{!! $paciente->antecedentes_quir ?: 'No registrados' !!}</div></div>
            <div class="po3-field full"><label>Motivo</label><div>{!! $order->motivo_consulta ?: 'No registrado' !!}</div></div>
            <div class="po3-field full"><label>Resumen de la historia clínica</label><div>{!! $order->resumen_clinico ?: 'No registrado' !!}</div></div>
            @if($order->diagnosticos)<div class="po3-field full"><label>Diagnóstico</label><div>{!! $order->diagnosticos !!}</div></div>@endif
            @if($order->observacion_medica)<div class="po3-field full"><label>Observación médica</label><div>{!! $order->observacion_medica !!}</div></div>@endif
          </div>
        </div>
      </details>
    @empty
      <div class="po3-empty"><i class="fas fa-folder-open"></i><strong>No hay órdenes de procedimientos</strong><span>Este paciente todavía no tiene órdenes activas registradas.</span></div>
    @endforelse
  </section>
</main>
