@foreach (config('inventarios.cuentas_producto') as $rol => [$etiqueta, $clase])
    <div class="col-sm-6">
        <label class="form-label" for="inv-cuenta-{{ $rol }}">{{ $etiqueta }}</label>
        <select id="inv-cuenta-{{ $rol }}" name="cuentas[{{ $rol }}]" class="form-select @error('cuentas.'.$rol) is-invalid @enderror">
            <option value="">Seleccionar cuenta</option>
            @foreach ($ficha['cuentas']->where('clase', $clase) as $cuenta)
                @continue(!$cuenta->activo && (int) ($registro['cuentas'][$rol] ?? 0) !== (int) $cuenta->id)
                <option value="{{ $cuenta->id }}" @selected((int) old('cuentas.'.$rol, $registro['cuentas'][$rol] ?? 0) === (int) $cuenta->id)>
                    {{ $cuenta->codigo }} · {{ $cuenta->nombre }}{{ $cuenta->activo ? '' : ' (inactiva)' }}
                </option>
            @endforeach
        </select>
        @error('cuentas.'.$rol)<span class="invalid-feedback">{{ $message }}</span>@enderror
    </div>
@endforeach
