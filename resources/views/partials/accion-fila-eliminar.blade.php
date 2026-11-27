<form method="POST" action="{{ $href }}" data-ajax-action data-accion-confirmar="{{ $confirmar ?? '¿Eliminar este registro?' }}">
    @csrf @method('DELETE')
    <button type="submit" class="dropdown-item d-flex gap-2 align-items-center text-danger">
        <i class="bi bi-trash3" aria-hidden="true"></i>Eliminar
    </button>
</form>
