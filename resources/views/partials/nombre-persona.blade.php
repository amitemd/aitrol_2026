@foreach (\App\Models\Persona::COMPONENTES_NOMBRE as $campo => [$etiqueta, $limite])
    <div class="col-md-6">
        <label for="{{ $campo }}" class="form-label">{{ $etiqueta }}</label>
        <input id="{{ $campo }}" name="{{ $campo }}" class="form-control @error($campo) is-invalid @enderror"
            maxlength="{{ $limite }}" value="{{ old($campo, $componentesNombre[$campo] ?? null) }}">
        @error($campo)<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
@endforeach
<details class="col-12" @if(blank(old('primer_nombre', $componentesNombre['primer_nombre'] ?? null)) && filled(old('nombres', $nombresAnteriores))) open @endif>
    <summary>{{ $etiquetaNombreCompleto ?? 'Razón social o nombre completo anterior' }}</summary>
    <div class="row g-3 mt-1">
        <div class="col-md-6">
            <label for="nombres" class="form-label">Nombres o razón social</label>
            <input id="nombres" name="nombres" class="form-control @error('nombres') is-invalid @enderror"
                maxlength="{{ $limiteNombre ?? 160 }}" value="{{ old('nombres', $nombresAnteriores) }}">
            @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label for="apellidos" class="form-label">Apellidos</label>
            <input id="apellidos" name="apellidos" class="form-control" maxlength="100" value="{{ old('apellidos', $apellidosAnteriores) }}">
        </div>
    </div>
</details>
