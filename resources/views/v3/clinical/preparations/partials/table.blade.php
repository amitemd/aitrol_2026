<table class="prep-table"><thead><tr><th>Preparación</th><th>Archivo</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
@forelse($models as $model)
  <tr><td><strong>{{ $model->nombre_preparaciones }}</strong><div class="prep-muted">{{ $model->nombre_archivo_original ?: 'Sin PDF cargado' }}</div></td>
  <td class="{{ $model->archivo_preparaciones ? 'prep-ok' : 'prep-pending' }}">{{ $model->archivo_preparaciones ? 'Disponible' : 'Pendiente' }}</td><td>{{ $model->estado ? 'Activo' : 'Inactivo' }}</td>
  <td><div class="prep-actions">
    @if($model->archivo_preparaciones)<a class="prep-btn" title="Descargar" href="{{ route('v3.preparations.file',$model->id) }}"><i class="fas fa-download"></i></a>@endif
    <button type="button" class="prep-btn prep-edit-open" title="Editar o cargar PDF" data-target="edit-{{ $model->id }}"><i class="fas fa-edit"></i></button>
    <form class="prep-ajax-form" method="post" action="{{ route('v3.preparations.toggle',$model->id) }}">{{ csrf_field() }}{{ method_field('PATCH') }}<button class="prep-btn" title="Cambiar estado"><i class="fas fa-power-off"></i></button></form>
  </div>
  <form id="edit-{{ $model->id }}" class="prep-ajax-form prep-edit-form" method="post" novalidate action="{{ route('v3.preparations.update',$model->id) }}" enctype="multipart/form-data" style="display:none;margin-top:10px">{{ csrf_field() }}{{ method_field('PATCH') }}
    <div class="prep-edit-grid">
      <div class="prep-edit-control"><label>Nombre de la preparación</label><input name="nombre_preparaciones" value="{{ $model->nombre_preparaciones }}" required maxlength="255"></div>
      <div class="prep-edit-control"><label>Reemplazar documento</label><label class="prep-edit-upload"><input class="prep-edit-file" type="file" name="archivo" accept="application/pdf,.pdf"><span class="prep-edit-upload-icon"><i class="fas fa-file-pdf"></i></span><span class="prep-edit-upload-copy"><strong>Nuevo archivo PDF</strong><span class="prep-edit-file-copy">Seleccionar o arrastrar · máximo 15 MB</span></span><button type="button" class="prep-edit-remove" title="Quitar archivo"><i class="fas fa-times"></i></button></label></div>
      <label class="prep-edit-active"><input type="checkbox" name="estado" value="1" {{ $model->estado ? 'checked' : '' }}> Preparación activa</label>
      <button class="prep-btn prep-edit-save"><i class="fas fa-save"></i> Guardar cambios</button>
    </div>
  </form>
  </td></tr>
@empty<tr><td colspan="4">No existen preparaciones con ese criterio.</td></tr>@endforelse
</tbody></table>
@if($models->lastPage() > 1)
<nav class="prep-pagination" aria-label="Paginación de preparaciones">
  <a class="prep-page {{ $models->onFirstPage() ? 'disabled' : '' }}" href="#" data-page="{{ max(1, $models->currentPage() - 1) }}"><i class="fas fa-chevron-left"></i> Anterior</a>
  @for($page = 1; $page <= $models->lastPage(); $page++)
    <a class="prep-page {{ $page === $models->currentPage() ? 'active' : '' }}" href="#" data-page="{{ $page }}">{{ $page }}</a>
  @endfor
  <a class="prep-page {{ $models->hasMorePages() ? '' : 'disabled' }}" href="#" data-page="{{ min($models->lastPage(), $models->currentPage() + 1) }}">Siguiente <i class="fas fa-chevron-right"></i></a>
</nav>
@endif
