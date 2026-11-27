<label class="form-check form-switch ficha-estado">
    <input type="hidden" name="activo" value="0">
    <input type="checkbox" name="activo" value="1" class="form-check-input" role="switch"
        aria-label="{{ $etiquetaEstado ?? 'Activo' }}" @checked((bool) old('activo', $activo ?? true))>
    <span class="ficha-estado-activo" aria-hidden="true">Activo</span>
    <span class="ficha-estado-inactivo" aria-hidden="true">Inactivo</span>
</label>
