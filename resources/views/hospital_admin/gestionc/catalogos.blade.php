@extends('layouts.app-template-h')
@section('content')

<style>
  .room-catalog-page, .room-catalog-page * { box-sizing:border-box; }
  .room-catalog-page { min-height:calc(100vh - 74px); padding:24px; background:#f2f8fb; color:#173b58; }
  .room-catalog-shell { max-width:1500px; margin:0 auto; }
  .room-catalog-hero { display:flex; align-items:center; justify-content:space-between; gap:18px; padding:22px 25px; border-radius:16px; background:linear-gradient(112deg,#073f6b,#087bb5 60%,#13a0a4); color:#fff; box-shadow:0 15px 35px rgba(7,63,107,.14); }
  .room-catalog-hero-main { display:flex; align-items:center; gap:14px; }
  .room-catalog-hero-icon { display:flex; width:54px; height:54px; align-items:center; justify-content:center; border:1px solid rgba(255,255,255,.28); border-radius:14px; background:rgba(255,255,255,.14); font-size:23px; }
  .room-catalog-hero h1 { margin:0; color:#fff; font-size:23px; font-weight:900; }
  .room-catalog-hero p { margin:4px 0 0; color:#d5f0f2; font-size:12px; }
  .room-catalog-back { display:inline-flex; align-items:center; gap:7px; padding:9px 14px; border:1px solid rgba(255,255,255,.55); border-radius:9px; color:#fff; font-size:12px; font-weight:800; }
  .room-catalog-back:hover { background:#fff; color:#0877ae; }
  .room-catalog-grid { display:grid; grid-template-columns:1fr 1.15fr; gap:18px; margin-top:18px; }
  .room-catalog-card { overflow:hidden; border:1px solid #cfe2eb; border-radius:15px; background:#fff; box-shadow:0 9px 25px rgba(7,63,107,.07); }
  .room-catalog-head { display:flex; align-items:center; gap:11px; padding:16px 18px; border-bottom:1px solid #dbeaf0; background:linear-gradient(100deg,#f9fdfe,#eaf8fa); }
  .room-catalog-head i { display:flex; width:38px; height:38px; align-items:center; justify-content:center; border-radius:10px; background:#dff3f7; color:#0878b5; }
  .room-catalog-head h2 { margin:0; color:#073f6b; font-size:16px; font-weight:900; }
  .room-catalog-head p { margin:2px 0 0; color:#718797; font-size:10px; }
  .room-catalog-form { display:grid; grid-template-columns:1fr auto; gap:10px; padding:17px 18px; border-bottom:1px solid #e2edf2; }
  .room-catalog-form.type { grid-template-columns:1fr 1.15fr 150px 115px auto; }
  .room-catalog-input { width:100%; height:40px; padding:8px 11px; border:1px solid #c8dce6; border-radius:8px; color:#21465f; font-size:12px; }
  .room-catalog-input[type="text"], .room-catalog-input:not([type]) { text-transform:uppercase; }
  .room-catalog-input:focus { border-color:#18a6b5; outline:0; box-shadow:0 0 0 3px rgba(24,166,181,.12); }
  .room-catalog-save { display:inline-flex; min-height:40px; align-items:center; gap:6px; padding:8px 14px; border:0; border-radius:8px; background:#0878b5; color:#fff; font-size:12px; font-weight:800; }
  .room-catalog-list { padding:7px 18px 15px; }
  .room-catalog-row { display:grid; grid-template-columns:minmax(0,1fr) auto auto; align-items:center; gap:12px; padding:11px 4px; border-bottom:1px solid #e8f0f4; }
  .room-catalog-row:last-child { border-bottom:0; }
  .room-catalog-name { color:#173f5d; font-size:12px; font-weight:900; text-transform:uppercase; }
  .room-catalog-detail { display:block; margin-top:2px; color:#7d909e; font-size:10px; font-weight:500; text-transform:uppercase; }
  .room-catalog-badge { padding:5px 9px; border-radius:20px; background:#e5f7ef; color:#178353; font-size:9px; font-weight:900; text-transform:uppercase; }
  .room-catalog-badge.off { background:#f1f3f5; color:#77838c; }
  .room-catalog-toggle { padding:6px 9px; border:1px solid #cbdde6; border-radius:7px; background:#fff; color:#45677d; font-size:10px; font-weight:800; }
  .room-catalog-actions { display:flex; align-items:center; gap:7px; }
  .room-catalog-edit { display:inline-flex; align-items:center; gap:5px; padding:6px 9px; border:1px solid #e8bd72; border-radius:7px; background:#fff8ea; color:#a66400; font-size:10px; font-weight:800; }
  .room-catalog-empty { padding:28px 18px; color:#7b8f9e; text-align:center; font-size:12px; }
  .room-catalog-alert { margin:15px 0 0; border-radius:10px; }
  .room-catalog-modal .modal-dialog { width:560px; max-width:calc(100% - 28px); margin-top:8vh; }
  .room-catalog-modal .modal-content { overflow:hidden; border:0; border-radius:15px; box-shadow:0 24px 70px rgba(4,47,72,.28); }
  .room-catalog-modal-head { display:flex; align-items:center; justify-content:space-between; gap:15px; padding:17px 20px; border-bottom:1px solid #cfe3ec; background:#effafd; }
  .room-catalog-modal-title { display:flex; align-items:center; gap:11px; }
  .room-catalog-modal-title>i { display:flex; width:40px; height:40px; align-items:center; justify-content:center; border-radius:10px; background:#dff3f7; color:#0878b5; }
  .room-catalog-modal-title h3 { margin:0 0 2px; color:#073f6b; font-size:17px; font-weight:900; }
  .room-catalog-modal-title p { margin:0; color:#718797; font-size:10px; }
  .room-catalog-modal-close { width:36px; height:36px; border:1px solid #c7dfe9; border-radius:9px; color:#55768a; background:#fff; font-size:19px; line-height:1; }
  .room-catalog-modal-body { padding:20px; }
  .room-catalog-field { margin-bottom:15px; }
  .room-catalog-field:last-child { margin-bottom:0; }
  .room-catalog-field label { display:block; margin-bottom:6px; color:#21465f; font-size:11px; font-weight:900; }
  .room-catalog-help { display:block; margin-top:6px; color:#78909f; font-size:10px; }
  .room-catalog-modal-foot { display:flex; justify-content:flex-end; gap:9px; padding:14px 20px; border-top:1px solid #d8e8ef; background:#f8fbfc; }
  .room-catalog-cancel { min-height:39px; padding:8px 14px; border:1px solid #cbdde6; border-radius:8px; color:#526f81; background:#fff; font-size:11px; font-weight:800; }
  @media(max-width:1100px){ .room-catalog-grid{grid-template-columns:1fr;} }
  @media(max-width:760px){ .room-catalog-page{padding:13px;} .room-catalog-hero{align-items:flex-start; flex-direction:column;} .room-catalog-form,.room-catalog-form.type{grid-template-columns:1fr;} }
</style>

<main class="room-catalog-page">
  <div class="room-catalog-shell">
    <header class="room-catalog-hero">
      <div class="room-catalog-hero-main">
        <span class="room-catalog-hero-icon"><i class="fa fa-layer-group"></i></span>
        <div><h1>Configuración de espacios clínicos</h1><p>Administra pisos y recursos clínicos: habitaciones, consultorios y quirófanos.</p></div>
      </div>
      <a class="room-catalog-back" href="{{route('hospital_admin.gestionh')}}"><i class="fa fa-arrow-left"></i> Gestión de habitaciones</a>
    </header>

    @if(session('success'))<div class="alert alert-success room-catalog-alert"><i class="fa fa-check-circle"></i> {{session('success')}}</div>@endif
    @if($errors->any())<div class="alert alert-danger room-catalog-alert"><i class="fa fa-exclamation-circle"></i> {{$errors->first()}}</div>@endif
    @if(!$tieneCantidadCamas)<div class="alert alert-warning room-catalog-alert">Ejecute <strong>php artisan migrate</strong> para habilitar la cantidad configurable de camas.</div>@endif

    <div class="room-catalog-grid">
      <section class="room-catalog-card">
        <div class="room-catalog-head"><i class="fa fa-building"></i><div><h2>Pisos</h2><p>Ubicación física de las habitaciones.</p></div></div>
        <form class="room-catalog-form" method="POST" action="{{route('hospital_admin.guardarpiso')}}">
          {{csrf_field()}}
          <input class="room-catalog-input" name="nombre_piso" maxlength="80" value="{{old('nombre_piso')}}" placeholder="Ej.: Piso 1, Planta baja, UCI" required>
          <button class="room-catalog-save" type="submit"><i class="fa fa-plus"></i> Agregar piso</button>
        </form>
        <div class="room-catalog-list">
          @forelse($pisos as $piso)
            @php $activo = !$tieneEstadoPiso || (int)$piso->estado === 1; @endphp
            <div class="room-catalog-row">
              <span class="room-catalog-name">{{$piso->nombre_piso}}<small class="room-catalog-detail">ID {{$piso->id}}</small></span>
              <span class="room-catalog-badge {{$activo ? '' : 'off'}}">{{$activo ? 'Activo' : 'Inactivo'}}</span>
              <div class="room-catalog-actions">
                <button class="room-catalog-edit js-edit-floor" type="button" data-toggle="modal" data-target="#editFloorModal" data-url="{{route('hospital_admin.actualizarpiso',['id'=>$piso->id])}}" data-name="{{$piso->nombre_piso}}"><i class="fa fa-pencil"></i> Editar</button>
                @if($tieneEstadoPiso)
                <form method="POST" action="{{route('hospital_admin.estadocatalogohabitacion',['catalogo'=>'piso','id'=>$piso->id])}}">{{csrf_field()}}<input type="hidden" name="estado" value="{{$activo ? 0 : 1}}"><button class="room-catalog-toggle" type="submit">{{$activo ? 'Desactivar' : 'Activar'}}</button></form>
                @endif
              </div>
            </div>
          @empty <div class="room-catalog-empty"><i class="fa fa-building"></i><br>No hay pisos registrados todavía.</div> @endforelse
        </div>
      </section>

      <section class="room-catalog-card">
        <div class="room-catalog-head"><i class="fa fa-bed"></i><div><h2>Tipos de recurso clínico</h2><p>Define si es habitación, consultorio o quirófano y su cantidad de camas.</p></div></div>
        <form class="room-catalog-form type" method="POST" action="{{route('hospital_admin.guardartipohabitacion')}}">
          {{csrf_field()}}
          <input class="room-catalog-input" name="nombre" maxlength="60" value="{{old('nombre')}}" placeholder="Ej.: Individual" required>
          <input class="room-catalog-input" name="descripcion" maxlength="150" value="{{old('descripcion')}}" placeholder="Descripción clínica" required>
          <select class="room-catalog-input js-resource-category" name="categoria" required>
            <option value="habitacion">Habitación</option>
            <option value="consultorio">Consultorio</option>
            <option value="quirofano">Quirófano</option>
          </select>
          <input class="room-catalog-input" type="number" name="cantidad_camas" min="0" max="10" value="{{old('cantidad_camas',1)}}" title="Cantidad de camas" required>
          <input type="hidden" name="icono" value="fa-bed">
          <input type="hidden" name="imagen" value="cama.png">
          <button class="room-catalog-save" type="submit" {{$tieneCantidadCamas ? '' : 'disabled'}}><i class="fa fa-plus"></i> Agregar tipo</button>
        </form>
        <div class="room-catalog-list">
          @forelse($tipos as $tipo)
            @php $activo = !$tieneEstadoTipo || (int)$tipo->estado === 1; $cantidad = $tieneCantidadCamas ? (int)$tipo->cantidad_camas : (($tipo->id == 2) ? 2 : (($tipo->id == 4) ? 3 : 1)); $categoria = isset($tipo->categoria) && $tipo->categoria ? $tipo->categoria : 'habitacion'; $habitacionesAsociadas = (int)$habitacionesPorTipo->get($tipo->id, 0); @endphp
            <div class="room-catalog-row">
              <span class="room-catalog-name"><i class="fa {{$tipo->icono ?: ($categoria === 'consultorio' ? 'fa-stethoscope' : ($categoria === 'quirofano' ? 'fa-procedures' : 'fa-bed'))}}"></i> {{$tipo->nombre}}<small class="room-catalog-detail">{{$categoria}} · {{$tipo->descripcion}} · {{$cantidad}} cama(s)</small></span>
              <span class="room-catalog-badge {{$activo ? '' : 'off'}}">{{$activo ? 'Activo' : 'Inactivo'}}</span>
              <div class="room-catalog-actions">
              <button class="room-catalog-edit js-edit-room-type" type="button" data-toggle="modal" data-target="#editRoomTypeModal" data-url="{{route('hospital_admin.actualizartipohabitacion',['id'=>$tipo->id])}}" data-name="{{$tipo->nombre}}" data-description="{{$tipo->descripcion}}" data-beds="{{$cantidad}}" data-category="{{$categoria}}" data-icon="{{$tipo->icono}}" data-image="{{$tipo->imagen}}" data-in-use="{{$habitacionesAsociadas > 0 ? 1 : 0}}" data-room-count="{{$habitacionesAsociadas}}"><i class="fa fa-pencil"></i> Editar</button>
                @if($tieneEstadoTipo)
                <form method="POST" action="{{route('hospital_admin.estadocatalogohabitacion',['catalogo'=>'tipo','id'=>$tipo->id])}}">{{csrf_field()}}<input type="hidden" name="estado" value="{{$activo ? 0 : 1}}"><button class="room-catalog-toggle" type="submit">{{$activo ? 'Desactivar' : 'Activar'}}</button></form>
                @endif
              </div>
            </div>
          @empty <div class="room-catalog-empty"><i class="fa fa-bed"></i><br>No hay tipos de recurso registrados todavía.</div> @endforelse
        </div>
      </section>
    </div>
  </div>
</main>

<div class="modal fade room-catalog-modal" id="editFloorModal" tabindex="-1" role="dialog" aria-labelledby="editFloorTitle">
  <div class="modal-dialog" role="document">
    <form class="modal-content" id="editFloorForm" method="POST" autocomplete="off">
      {{csrf_field()}}
      <div class="room-catalog-modal-head">
        <div class="room-catalog-modal-title"><i class="fa fa-building"></i><div><h3 id="editFloorTitle">Editar piso</h3><p>Actualiza el nombre utilizado para ubicar las habitaciones.</p></div></div>
        <button class="room-catalog-modal-close" type="button" data-dismiss="modal" aria-label="Cerrar">&times;</button>
      </div>
      <div class="room-catalog-modal-body">
        <div class="room-catalog-field">
          <label for="editFloorName">Nombre del piso</label>
          <input class="room-catalog-input" id="editFloorName" name="nombre_piso" maxlength="80" required>
          <small class="room-catalog-help">Ejemplos: Piso 1, Planta baja o Torre norte.</small>
        </div>
      </div>
      <div class="room-catalog-modal-foot">
        <button class="room-catalog-cancel" type="button" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
        <button class="room-catalog-save" type="submit"><i class="fa fa-save"></i> Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade room-catalog-modal" id="editRoomTypeModal" tabindex="-1" role="dialog" aria-labelledby="editRoomTypeTitle">
  <div class="modal-dialog" role="document">
    <form class="modal-content" id="editRoomTypeForm" method="POST" autocomplete="off">
      {{csrf_field()}}
      <div class="room-catalog-modal-head">
        <div class="room-catalog-modal-title"><i class="fa fa-bed"></i><div><h3 id="editRoomTypeTitle">Editar tipo de habitación</h3><p>Actualiza su identificación y configuración de camas.</p></div></div>
        <button class="room-catalog-modal-close" type="button" data-dismiss="modal" aria-label="Cerrar">&times;</button>
      </div>
      <div class="room-catalog-modal-body">
        <div class="room-catalog-field">
          <label for="editRoomTypeName">Nombre</label>
          <input class="room-catalog-input" id="editRoomTypeName" name="nombre" maxlength="60" required>
        </div>
        <div class="room-catalog-field">
          <label for="editRoomTypeDescription">Descripción clínica</label>
          <input class="room-catalog-input" id="editRoomTypeDescription" name="descripcion" maxlength="150" required>
        </div>
        <div class="room-catalog-field">
          <label for="editRoomTypeCategory">Categoría</label>
          <select class="room-catalog-input" id="editRoomTypeCategory" name="categoria" required><option value="habitacion">Habitación</option><option value="consultorio">Consultorio</option><option value="quirofano">Quirófano</option></select>
          <label for="editRoomTypeBeds">Cantidad de camas por recurso</label>
          <input class="room-catalog-input" id="editRoomTypeBeds" type="number" name="cantidad_camas" min="0" max="10" required>
          <input type="hidden" id="editRoomTypeIcon" name="icono">
          <input type="hidden" id="editRoomTypeImage" name="imagen">
          <small class="room-catalog-help" id="editRoomTypeBedsHelp">Puedes asignar entre 0 y 10 camas.</small>
        </div>
      </div>
      <div class="room-catalog-modal-foot">
        <button class="room-catalog-cancel" type="button" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
        <button class="room-catalog-save" type="submit"><i class="fa fa-save"></i> Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

<script>
  $(document).on('click', '.js-edit-floor', function () {
    $('#editFloorForm').attr('action', $(this).data('url'));
    $('#editFloorName').val($(this).data('name'));
  });

  $(document).on('click', '.js-edit-room-type', function () {
    var $button = $(this);
    var inUse = Number($button.data('in-use')) === 1;
    var roomCount = Number($button.data('room-count')) || 0;

    $('#editRoomTypeForm').attr('action', $button.data('url'));
    $('#editRoomTypeName').val($button.data('name'));
    $('#editRoomTypeDescription').val($button.data('description'));
    $('#editRoomTypeCategory').val($button.data('category') || 'habitacion');
    $('#editRoomTypeIcon').val($button.data('icon') || 'fa-bed');
    $('#editRoomTypeImage').val($button.data('image') || 'cama.png');
    $('#editRoomTypeBeds').val($button.data('beds')).prop('readonly', inUse);
    $('#editRoomTypeBedsHelp').text(inUse
      ? 'La cantidad está protegida porque este tipo se utiliza en ' + roomCount + ' habitación(es).'
      : 'Puedes asignar entre 0 y 10 camas. Consultorios y quirófanos normalmente usan 0.');
  });

  function syncResourceVisual($form) {
    var category = $form.find('[name="categoria"]').val();
    var icon = category === 'consultorio' ? 'fa-stethoscope' : (category === 'quirofano' ? 'fa-procedures' : 'fa-bed');
    var image = category === 'consultorio' ? 'office.png' : (category === 'quirofano' ? 'quirofano1.png' : 'cama.png');
    $form.find('[name="icono"]').val(icon);
    $form.find('[name="imagen"]').val(image);
  }

  $('.js-resource-category').on('change', function () {
    syncResourceVisual($(this).closest('form'));
    if ($(this).val() !== 'habitacion') $(this).closest('form').find('[name="cantidad_camas"]').val(0);
  });

  $('#editRoomTypeCategory').on('change', function () {
    var category = $(this).val();
    $('#editRoomTypeIcon').val(category === 'consultorio' ? 'fa-stethoscope' : (category === 'quirofano' ? 'fa-procedures' : 'fa-bed'));
    $('#editRoomTypeImage').val(category === 'consultorio' ? 'office.png' : (category === 'quirofano' ? 'quirofano1.png' : 'cama.png'));
    if (category !== 'habitacion') $('#editRoomTypeBeds').val(0);
  });

  $('.room-catalog-form, .room-catalog-modal form').on('submit', function () {
    $(this).find('input[type="text"], input:not([type])').each(function () {
      this.value = this.value.toUpperCase();
    });
    $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');
  });
</script>
@endsection
