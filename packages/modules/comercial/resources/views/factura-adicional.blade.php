<div class="d-flex flex-wrap flex-sm-nowrap align-items-center gap-2" data-factura-adicional>
    <input type="text" class="form-control" name="informacion_adicional[{{ $indice }}][nombre]"
        maxlength="299" aria-label="Nombre del dato adicional" placeholder="Nombre" value="{{ $adicional['nombre'] ?? '' }}">
    <input type="text" class="form-control" name="informacion_adicional[{{ $indice }}][valor]"
        maxlength="299" aria-label="Valor del dato adicional" placeholder="Dato" value="{{ $adicional['valor'] ?? '' }}">
    <button type="button" class="btn btn-outline-danger" data-factura-adicional-quitar aria-label="Quitar dato adicional">
        <i class="bi bi-trash" aria-hidden="true"></i>
    </button>
</div>
