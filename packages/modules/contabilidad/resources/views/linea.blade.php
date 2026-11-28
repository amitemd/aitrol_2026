<fieldset class="rounded border p-3" data-comercial-linea>
    <legend class="visually-hidden">Partida contable</legend>
    <div class="row g-2">
        <div class="col-sm-6">
            <label class="form-label" for="co-{{ $indice }}-cuenta">Cuenta auxiliar</label>
            <select id="co-{{ $indice }}-cuenta" name="lineas[{{ $indice }}][id_cuenta]" data-select2-min="0" data-select2-url="{{ route('contabilidad.asientos.opciones') }}" class="form-select" required><option value="">{{ __('Seleccionar') }}</option></select>
        </div>
        @foreach (['debe' => 'Debe', 'haber' => 'Haber'] as $campo => $etiqueta)
            <div class="col-6 col-sm-3">
                <label class="form-label" for="co-{{ $indice }}-{{ $campo }}">{{ __($etiqueta) }}</label>
                <input id="co-{{ $indice }}-{{ $campo }}" name="lineas[{{ $indice }}][{{ $campo }}]" type="number" step="0.01" min="0" max="1000000000" class="form-control" value="{{ $fila[$campo] ?? '' }}">
            </div>
        @endforeach
        <div class="col-sm">
            <label class="form-label" for="co-{{ $indice }}-detalle">{{ __('Detalle') }}</label>
            <input id="co-{{ $indice }}-detalle" maxlength="250" name="lineas[{{ $indice }}][detalle]" class="form-control" value="{{ $fila['detalle'] ?? '' }}">
        </div>
        <div class="col-sm">
            <label class="form-label" for="co-{{ $indice }}-centro">Centro de costos</label>
            <select id="co-{{ $indice }}-centro" name="lineas[{{ $indice }}][id_centro_costo]" class="form-select">
                <option value="">Sin centro asignado</option>
                @foreach ($centros as $centro)
                    <option value="{{ $centro->id }}" @selected((int) ($fila['id_centro_costo'] ?? 0) === (int) $centro->id)>{{ $centro->codigo }} · {{ $centro->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm">
            <label class="form-label" for="co-{{ $indice }}-patrimonio">Movimiento de patrimonio</label>
            <select id="co-{{ $indice }}-patrimonio" name="lineas[{{ $indice }}][movimiento_patrimonio]" class="form-select">
                <option value="">Sin clasificar</option>
                @foreach (config('contabilidad.patrimonio') as $valor => $etiqueta)<option value="{{ $valor }}" @selected(($fila['movimiento_patrimonio'] ?? null) === $valor)>{{ __($etiqueta) }}</option>@endforeach
            </select>
        </div>
        <div class="col-auto align-self-end"><button type="button" data-comercial-quitar class="btn btn-outline-danger" aria-label="Quitar partida"><i class="bi bi-trash3" aria-hidden="true"></i></button></div>
    </div>
</fieldset>
