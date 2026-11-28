<div data-selector-persona="{{ route('personas.formularios.buscar', $formularioPersona) }}">
    <input class="form-control @error('identificacion') is-invalid @enderror" id="{{ $idPersonaCampo }}" name="identificacion"
        maxlength="{{ $maximoPersona ?? 30 }}" value="{{ $valorPersona }}" autocomplete="off" spellcheck="false"
        role="combobox" aria-autocomplete="list" aria-expanded="false" data-identificacion-persona
        @required($personaRequerida ?? false) @if ($enfocarPersona ?? false) autofocus @endif>
    <div class="list-group mt-1 overflow-auto" style="max-height: 13rem" role="listbox" aria-label="Personas encontradas" data-persona-resultados hidden></div>
    <p class="form-text mb-0" data-persona-estado role="status" hidden></p>
</div>
