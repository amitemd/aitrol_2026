<label class="form-check form-switch ficha-estado">
    @unless ($estadoBloqueado ?? false)
        <input type="hidden" name="activo" value="0">
    @endunless
    <input type="checkbox" name="activo" value="1" class="form-check-input" role="switch"
        aria-label="{{ $etiquetaEstado ?? 'Activo' }}" @disabled($estadoBloqueado ?? false)
        @checked((bool) (($estadoBloqueado ?? false) ? ($activo ?? true) : old('activo', $activo ?? true)))>
    <span class="ficha-estado-activo" aria-hidden="true">{{ __('Activo') }}</span>
    <span class="ficha-estado-inactivo" aria-hidden="true">{{ __('Inactivo') }}</span>
</label>
