@php
  $nombre_seguro_publico = mb_strtoupper(trim($atencion->nombre_seguro), 'UTF-8');
  $es_seguro_publico_activo = (int) $atencion->tipo_seguro === 0 && (int) $atencion->seguro_inactivo === 1;
  $clase_seguro_publico = 'btn-primary';
  $ruta_seguro_publico = null;

  if ($es_seguro_publico_activo) {
      if ($nombre_seguro_publico === 'IESS') {
          $ruta_seguro_publico = route('nuevoArchivoPlano.crear_planilla', [
              'id_hc_procedimiento' => $atencion->id_hc_proced,
              'id_seguro' => $atencion->id_seguro,
          ]);
          $clase_seguro_publico = 'btn-success is-iess';
      } elseif ($nombre_seguro_publico === 'MSP') {
          $ruta_seguro_publico = route('archivo_plano.planilla_msp', [
              'hcid' => $atencion->id_hc_proced,
              'id_seguro' => $atencion->id_seguro,
          ]);
          $clase_seguro_publico = 'btn-warning is-msp';
      } else {
          $ruta_seguro_publico = route('archivo_plano.planilla_hcid', [
              'hcid' => $atencion->id_hc_proced,
              'id_seguro' => $atencion->id_seguro,
          ]);
          $clase_seguro_publico .= ' is-' . strtolower(preg_replace('/[^A-Z0-9]+/', '-', $nombre_seguro_publico));
      }
  }
@endphp

@if($ruta_seguro_publico)
  <a type="button" class="btn btn-xs planillas-action {{$clase_seguro_publico}}" href="{{$ruta_seguro_publico}}">{{$atencion->nombre_seguro}}</a>
@elseif((int) $atencion->tipo_seguro !== 0)
  <span class="text-muted">No aplica para seguro privado</span>
@else
  <span class="text-muted">Seguro público no disponible</span>
@endif
