@php
  $seleccionadosFavoritos = isset($detalles_ch) ? $detalles_ch : [];
  $soloLecturaCatalogo = isset($soloLectura) && $soloLectura;
  $paletaAgrupadores = [
    ['fondo' => '#dbeafe', 'texto' => '#1e3a5f', 'seleccion' => '#eff6ff'],
    ['fondo' => '#dcfce7', 'texto' => '#285943', 'seleccion' => '#f0fdf4'],
    ['fondo' => '#fef3c7', 'texto' => '#6b4f16', 'seleccion' => '#fffbeb'],
    ['fondo' => '#fce7f3', 'texto' => '#6b3150', 'seleccion' => '#fdf2f8'],
    ['fondo' => '#ede9fe', 'texto' => '#493d72', 'seleccion' => '#f5f3ff'],
    ['fondo' => '#cffafe', 'texto' => '#245766', 'seleccion' => '#ecfeff'],
    ['fondo' => '#ffedd5', 'texto' => '#6b4630', 'seleccion' => '#fff7ed'],
    ['fondo' => '#e2e8f0', 'texto' => '#34445a', 'seleccion' => '#f8fafc'],
  ];
  $gruposFavoritos = collect();

  foreach ($agrupador_labs as $agrupador) {
    $examenesGrupo = $examenes_labs
      ->where('id_agrupador', $agrupador->id)
      ->sortBy(function ($examen) {
        return strtoupper(\Illuminate\Support\Str::ascii((string) ($examen->descripcion ?: $examen->nombre)));
      })
      ->values();

    if ($examenesGrupo->isEmpty()) {
      continue;
    }

    $cantidad = $examenesGrupo->count();
    $nombreNormalizado = strtoupper(\Illuminate\Support\Str::ascii(trim((string) $agrupador->nombre)));
    $gruposFavoritos->push([
      'agrupador' => $agrupador,
      'examenes' => $examenesGrupo,
      'cantidad' => $cantidad,
      'extenso' => $nombreNormalizado === 'OTROS' || $cantidad > 18,
      'otros' => $nombreNormalizado === 'OTROS',
      'columnas' => $cantidad > 120 ? 5 : ($cantidad > 60 ? 4 : 3),
      'color' => $paletaAgrupadores[abs((int) $agrupador->id) % count($paletaAgrupadores)],
    ]);
  }

  $gruposFavoritos = $gruposFavoritos->sort(function ($grupoA, $grupoB) {
    if ($grupoA['otros'] !== $grupoB['otros']) {
      return $grupoA['otros'] ? 1 : -1;
    }
    if ($grupoA['cantidad'] !== $grupoB['cantidad']) {
      return $grupoA['cantidad'] <=> $grupoB['cantidad'];
    }
    return strcasecmp((string) $grupoA['agrupador']->nombre, (string) $grupoB['agrupador']->nombre);
  })->values();
@endphp

<div class="hc4-lab-catalog">
  @foreach($gruposFavoritos as $grupo)
    @php $color = $grupo['color']; @endphp
    <section class="hc4-lab-group @if($grupo['extenso']) hc4-lab-group--wide @endif" style="--group-bg:{{$color['fondo']}};--group-text:{{$color['texto']}};--group-selected:{{$color['seleccion']}};--group-columns:{{$grupo['columnas']}};">
      <h4 class="hc4-lab-group__title"><span>{{$grupo['agrupador']->nombre}}</span><small>{{$grupo['cantidad']}} exámenes</small></h4>
      <div class="hc4-lab-group__items">
        @foreach($grupo['examenes'] as $examen)
          @php $seleccionado = in_array($examen->ex_id, $seleccionadosFavoritos); @endphp
          @if($soloLecturaCatalogo)
            <div class="hc4-lab-exam-row hc4-lab-exam-row--readonly"><span class="hc4-lab-exam-row__check"><i class="fas fa-check" aria-hidden="true"></i></span><span class="hc4-lab-exam-row__name">{{$examen->descripcion ?: $examen->nombre}}</span></div>
          @else
            <label class="hc4-lab-exam-row @if($seleccionado) is-selected @endif" for="ch{{$examen->ex_id}}"><span class="hc4-lab-exam-row__check"><input id="ch{{$examen->ex_id}}" name="ch{{$examen->ex_id}}" type="checkbox" class="flat-orange" @if($seleccionado) checked @endif></span><span class="hc4-lab-exam-row__name">{{$examen->descripcion ?: $examen->nombre}}</span></label>
          @endif
        @endforeach
      </div>
    </section>
  @endforeach

  @if($gruposFavoritos->isEmpty())
    <div class="hc4-lab-catalog__empty">No se encontraron exámenes con los filtros ingresados.</div>
  @endif
</div>

@if(!$soloLecturaCatalogo)
<script type="text/javascript">
  (function(){
    var $checks=$('#xbuscador_fav input[type="checkbox"].flat-orange');
    $checks.iCheck({checkboxClass:'icheckbox_flat-orange',radioClass:'iradio_flat-orange'});
    $checks.off('.favoriteCatalog')
      .on('ifChecked.favoriteCatalog',function(){$(this).closest('.hc4-lab-exam-row').addClass('is-selected');crear_examen_favorito(this.name.substring(2));})
      .on('ifUnchecked.favoriteCatalog',function(){$(this).closest('.hc4-lab-exam-row').removeClass('is-selected');eliminar_examen_favorito(this.name.substring(2));});
  })();
</script>
@endif
