<dialog class="cliente-dialog cliente-dialog-redisenado" data-compra-producto-dialogo aria-labelledby="compra-producto-titulo">
    <div class="cliente-dialog-cabecera d-flex justify-content-between align-items-center gap-3">
        <h2 class="h5 mb-0" id="compra-producto-titulo">Crear producto de inventario</h2>
        <button type="button" class="cliente-dialog-cerrar" data-compra-cerrar-producto aria-label="Cerrar">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
    </div>
    <form action="{{ route('inventarios.productos.store') }}" method="post">
        @csrf
        <div class="cliente-dialog-cuerpo row g-3 m-0">
            <div class="col-sm-4">
                <label class="form-label" for="compra-alta-codigo">Código</label>
                <input class="form-control" id="compra-alta-codigo" name="codigo" maxlength="60" required>
            </div>
            <div class="col-sm-8">
                <label class="form-label" for="compra-alta-nombre">Nombre</label>
                <input class="form-control" id="compra-alta-nombre" name="nombre" maxlength="160" required>
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="compra-alta-tipo">Tipo</label>
                <select class="form-select" id="compra-alta-tipo" name="tipo_producto" data-select2-nativo required>
                    @foreach (config('inventarios.tipos_producto') as $tipo => $etiqueta)
                        <option value="{{ $tipo }}">{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="compra-alta-unidad">Unidad de medida</label>
                @if ($unidadesProducto->isNotEmpty())
                    <select class="form-select" id="compra-alta-unidad" name="id_unidad" data-select2-nativo required>
                        <option value="">Seleccionar</option>
                        @foreach ($unidadesProducto as $unidad)
                            <option value="{{ $unidad->id }}">{{ $unidad->codigo }} · {{ $unidad->nombre }}</option>
                        @endforeach
                    </select>
                @else
                    <input class="form-control" id="compra-alta-unidad" name="unidad" maxlength="30" value="Unidad" required>
                @endif
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="compra-alta-categoria">Categoría</label>
                <select class="form-select" id="compra-alta-categoria" name="id_categoria" data-select2-nativo>
                    <option value="">Sin categoría</option>
                    @foreach ($categoriasProducto as $id => $nombre)
                        <option value="{{ $id }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="compra-alta-costo">Costo unitario de referencia</label>
                <input class="form-control" type="number" id="compra-alta-costo" name="costo_unitario_referencia" min="0" max="1000000" step="0.0001">
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="compra-alta-iva">IVA</label>
                <select class="form-select" id="compra-alta-iva" name="iva_codigo" data-select2-nativo>
                    <option value="">Seleccionar</option>
                    @foreach (\App\Services\FacturaVenta::IVA as $codigo => $tasa)
                        <option value="{{ $codigo }}">{{ \App\Services\FacturaVenta::etiquetaIva($codigo) }}</option>
                    @endforeach
                </select>
            </div>
            <p class="col-12 text-danger small mb-0" data-compra-producto-error role="alert" hidden></p>
        </div>
        <div class="cliente-dialog-pie d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" data-compra-cerrar-producto>Cancelar</button>
            <button type="submit" class="btn btn-primary">Crear y seleccionar</button>
        </div>
    </form>
</dialog>
