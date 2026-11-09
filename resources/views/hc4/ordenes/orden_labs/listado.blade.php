@php
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
  $gruposCatalogo = collect();

  foreach ($agrupador_labs as $agrupador) {
    $examenesGrupo = $examenes_labs
      ->where('id_agrupador', $agrupador->id)
      ->filter(function ($examen) use ($detalles_ch) {
        return $examen->estado != '0' || in_array($examen->ex_id, $detalles_ch);
      })
      ->sortBy(function ($examen) {
        return strtoupper(\Illuminate\Support\Str::ascii((string) ($examen->descripcion ?: $examen->nombre)));
      })
      ->values();

    if ($examenesGrupo->isEmpty()) {
      continue;
    }

    $cantidadExamenes = $examenesGrupo->count();
    $nombreAgrupador = strtoupper(\Illuminate\Support\Str::ascii(trim((string) $agrupador->nombre)));
    $esAgrupadorExtenso = $nombreAgrupador === 'OTROS' || $cantidadExamenes > 18;
    $columnasInternas = $cantidadExamenes > 120 ? 5 : ($cantidadExamenes > 60 ? 4 : 3);
    $indiceColor = abs((int) $agrupador->id) % count($paletaAgrupadores);

    $gruposCatalogo->push([
      'agrupador' => $agrupador,
      'examenes' => $examenesGrupo,
      'cantidad' => $cantidadExamenes,
      'extenso' => $esAgrupadorExtenso,
      'otros' => $nombreAgrupador === 'OTROS',
      'columnas' => $columnasInternas,
      'color' => $paletaAgrupadores[$indiceColor],
    ]);
  }

  // De menor a mayor; OTROS siempre queda al final aunque cambie la cantidad filtrada.
  $gruposCatalogo = $gruposCatalogo
    ->sort(function ($grupoA, $grupoB) {
      if ($grupoA['otros'] !== $grupoB['otros']) {
        return $grupoA['otros'] ? 1 : -1;
      }
      if ($grupoA['cantidad'] !== $grupoB['cantidad']) {
        return $grupoA['cantidad'] <=> $grupoB['cantidad'];
      }
      return strcasecmp((string) $grupoA['agrupador']->nombre, (string) $grupoB['agrupador']->nombre);
    })
    ->values();
@endphp

<div class="hc4-lab-catalog">
  @foreach($gruposCatalogo as $grupo)
    @php
      $agrupador = $grupo['agrupador'];
      $examenesGrupo = $grupo['examenes'];
      $color = $grupo['color'];
    @endphp

    <section class="hc4-lab-group @if($grupo['extenso']) hc4-lab-group--wide @endif"
             style="--group-bg:{{$color['fondo']}};--group-text:{{$color['texto']}};--group-selected:{{$color['seleccion']}};--group-columns:{{$grupo['columnas']}};">
      <h4 class="hc4-lab-group__title">
        <span>{{$agrupador->nombre}}</span>
        <small>{{$grupo['cantidad']}} exámenes</small>
      </h4>
      <div class="hc4-lab-group__items">
        @foreach($examenesGrupo as $examen)
          @php $seleccionado = in_array($examen->ex_id, $detalles_ch); @endphp
          <label class="hc4-lab-exam-row @if($seleccionado) is-selected @endif" for="ch{{$orden->id}}_{{$examen->ex_id}}">
            <span class="hc4-lab-exam-row__check">
              <input id="ch{{$orden->id}}_{{$examen->ex_id}}"
                     data-examen-id="{{$examen->ex_id}}"
                     type="checkbox"
                     class="hc4-lab-exam-check"
                     @if($seleccionado) checked @endif>
            </span>
            <span class="hc4-lab-exam-row__name">{{$examen->descripcion ?: $examen->nombre}}</span>
          </label>
        @endforeach
      </div>
    </section>
  @endforeach

  @if($gruposCatalogo->isEmpty())
    <div class="hc4-lab-catalog__empty">
      No se encontraron exámenes con los filtros ingresados.
    </div>
  @endif
</div>
