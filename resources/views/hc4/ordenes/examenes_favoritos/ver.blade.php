@include('hc4.ordenes.examenes_favoritos.editor_styles')
<div class="v3-favorite-view">
  <div class="v3-favorite-view__summary"><span>Perfil: <strong>{{$protocolo->nombre}}</strong></span><span><strong>{{$examenes_labs->count()}}</strong> exámenes seleccionados</span></div>
  @include('hc4.ordenes.examenes_favoritos.catalogo', ['soloLectura' => true])
</div>
