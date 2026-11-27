<div class="ficha-bloque" data-galeria-producto data-imagenes="{{ $galeria['imagenes']->toJson() }}">
    <h2 class="h6 d-flex align-items-center justify-content-between gap-2">
        <span><i class="bi bi-images me-2" aria-hidden="true"></i>Galería del producto</span>
        <span class="badge rounded-pill text-bg-secondary" data-galeria-contador>{{ $galeria['imagenes']->count() }} / 8</span>
    </h2>
    <input type="hidden" name="version_fotos" value="{{ $galeria['version'] }}" disabled>
    <input type="hidden" name="orden_fotos" value="[]" disabled>
    <div class="producto-zona-carga" data-galeria-zona>
        <span class="producto-zona-icono" aria-hidden="true"><i class="bi bi-cloud-arrow-up"></i></span>
        <span class="fw-semibold">Arrastra tus imágenes aquí</span>
        <label class="btn btn-sm btn-outline-primary position-relative">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Seleccionar imágenes
            <input class="visually-hidden" type="file" accept="image/jpeg,image/png,image/webp" name="fotos_nuevas[]" multiple>
        </label>
        <small class="text-body-secondary">Hasta 8 imágenes JPG, PNG o WebP de 3 MB cada una.</small>
    </div>
    <p class="form-text mb-2 mt-3">La primera imagen es la portada. Arrastra las tarjetas o usa las flechas para reordenarlas.</p>
    <p class="small mb-2" data-galeria-estado role="status" hidden></p>
    <ol class="producto-galeria list-unstyled mb-0" data-galeria-lista></ol>
</div>
