@extends('archivo_plano.procedimientos.base')
@section('action-content')
<link rel="stylesheet" href="{{asset('v3/plugins/select2/css/select2.min.css')}}">
<style>
  #items-inline .box-header { padding: 16px 12px; }
  #items-inline .items-search { margin-bottom: 16px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
  #items-inline .items-search input { width: 280px; max-width: 100%; }
  #items-inline .items-filter { width: 150px; }
  #items-inline .select2-container { font-size: 12px; }
  #items-inline .select2-selection--single { height: 30px; border: 1px solid #d2d6de; border-radius: 0; }
  #items-inline .select2-selection--single .select2-selection__rendered { line-height: 28px; padding-left: 10px; }
  #items-inline .select2-selection--single .select2-selection__arrow { height: 28px; }
  #items-inline .item-invalid + .select2 .select2-selection { border-color: #dd4b39; box-shadow: 0 0 0 1px #dd4b39; }
  #frmpro { min-width: 1450px; table-layout: fixed; }
  #frmpro th { background: #f5f8fb; vertical-align: middle; }
  #frmpro td { vertical-align: top; padding: 8px; }
  #frmpro .form-control { width: 100%; font-size: 12px; }
  #frmpro textarea { resize: vertical; min-height: 32px; }
  #frmpro .item-quantity { display: flex; }
  #frmpro .item-quantity input { min-width: 45px; padding: 5px; text-align: center; border-radius: 0; }
  #frmpro .item-step { padding: 5px 8px; }
  #frmpro .item-actions { display: flex; gap: 5px; flex-wrap: wrap; }
  #frmpro .item-actions .btn { padding: 5px 8px; }
  #frmpro .item-status { display: block; margin-top: 5px; font-size: 11px; line-height: 1.4; overflow-wrap: break-word; }
  #frmpro .item-invalid { border-color: #dd4b39; box-shadow: 0 0 0 1px #dd4b39; }
  #frmpro tr.item-dirty { background: #fffdf3; }
  #frmpro tr.item-saving { background: #f0f8ff; }
</style>
<section class="content" id="items-inline" data-csrf="{{csrf_token()}}">
  <div class="box">
    <div class="box-header">
      <div class="row">
        <div class="col-sm-6"><h3 class="box-title">Lista de Items</h3></div>
        <div class="col-sm-3"><a class="btn btn-primary" href="{{url('administracion/procedimientos/crear')}}">Agregar Nuevo Items</a></div>
        <div class="col-sm-3"><a class="btn btn-primary" href="{{route('procedimientos.nivel')}}">Asignar Nivel a Items</a></div>
      </div>
    </div>
    <div class="box-body">
      <form method="GET" action="{{url('administracion/procedimientos')}}" class="items-search">
        <label for="buscar-item">Nombre o código:</label>
        <input id="buscar-item" type="text" name="descripcion" value="{{$procedimiento}}" class="form-control input-sm" placeholder="Buscar ítems">
        <label for="filtro-tipo">Tipo:</label>
        <select id="filtro-tipo" name="tipo" class="form-control input-sm items-filter">
          <option value="">Todos</option>
          @foreach($tipos as $tipo)
            <option value="{{$tipo}}" @if($filtros['tipo'] === $tipo) selected @endif>{{$tipo}}</option>
          @endforeach
        </select>
        <label for="filtro-iva">IVA:</label>
        <select id="filtro-iva" name="iva" class="form-control input-sm items-filter">
          <option value="">Todos</option>
          <option value="si" @if($filtros['iva'] === 'si') selected @endif>Sí</option>
          <option value="no" @if($filtros['iva'] === 'no') selected @endif>No</option>
        </select>
        <label for="filtro-seguro">Seguro:</label>
        <select id="filtro-seguro" name="seguro" class="form-control input-sm items-filter">
          <option value="">Todos</option>
          @foreach($seguros as $seguro)
            <option value="{{$seguro->id}}" @if((string) $filtros['seguro'] === (string) $seguro->id) selected @endif>{{$seguro->nombre}}</option>
          @endforeach
        </select>
        <button class="btn btn-primary btn-sm" type="submit">Buscar</button>
        @if($procedimiento !== '' || $filtros['tipo'] !== '' || $filtros['iva'] !== '' || $filtros['seguro'] !== '')
          <a href="{{url('administracion/procedimientos')}}" class="btn btn-default btn-sm">Limpiar</a>
        @endif
      </form>
      <div class="table-responsive">
        <table id="frmpro" class="table table-bordered table-hover">
          <colgroup><col style="width: 8%"><col style="width: 13%"><col style="width: 25%"><col style="width: 9%"><col style="width: 9%"><col style="width: 6%"><col style="width: 14%"><col style="width: 16%"></colgroup>
          <thead>
            <tr><th>Tipo</th><th>Código</th><th>Descripción</th><th>Cantidad</th><th>Valor</th><th>IVA</th><th>Seguro</th><th>Acción</th></tr>
          </thead>
          <tbody>
            @forelse($procedimientos as $item)
              <tr data-item-id="{{$item->id}}" data-save-url="{{route('procedimientos.inline.guardar', ['id' => $item->id])}}" data-delete-url="{{route('procedimientos.inline.eliminar', ['id' => $item->id])}}">
                <td>
                  <select class="form-control input-sm item-field" data-field="tipo" aria-label="Tipo del ítem {{$item->id}}">
                    @if(!$tipos->contains($item->tipo))
                      <option value="{{$item->tipo}}" selected>{{$item->tipo}} (actual)</option>
                    @endif
                    @foreach($tipos as $tipo)
                      <option value="{{$tipo}}" @if($item->tipo == $tipo) selected @endif>{{$tipo}}</option>
                    @endforeach
                  </select>
                </td>
                <td><input type="text" inputmode="numeric" class="form-control input-sm item-field" data-field="codigo" value="{{$item->codigo}}" maxlength="19" pattern="[0-9]{1,19}" required aria-label="Código del ítem {{$item->id}}"></td>
                <td><textarea class="form-control input-sm item-field" data-field="descripcion" rows="2" maxlength="16000" required aria-label="Descripción del ítem {{$item->id}}">{{$item->descripcion}}</textarea></td>
                <td>
                  <div class="item-quantity">
                    <button type="button" class="btn btn-default btn-sm item-step" data-step="-1" aria-label="Disminuir cantidad del ítem {{$item->id}}">−</button>
                    <input type="number" class="form-control input-sm item-field" data-field="cantidad" value="{{(float) $item->cantidad > 0 ? $item->cantidad : 1}}" min="1" max="1000000" step="any" required aria-label="Cantidad del ítem {{$item->id}}">
                    <button type="button" class="btn btn-default btn-sm item-step" data-step="1" aria-label="Aumentar cantidad del ítem {{$item->id}}">+</button>
                  </div>
                </td>
                <td><input type="number" class="form-control input-sm item-field" data-field="valor" value="{{$item->valor}}" min="0" max="999999.9999" step="0.0001" required aria-label="Valor del ítem {{$item->id}}"></td>
                <td>
                  <select class="form-control input-sm item-field" data-field="aplica_iva" aria-label="IVA del ítem {{$item->id}}">
                    <option value="0" @if((float) $item->iva <= 0 && (float) $item->iva_nuevo <= 0) selected @endif>No</option>
                    <option value="1" @if((float) $item->iva > 0 || (float) $item->iva_nuevo > 0) selected @endif>Sí</option>
                  </select>
                </td>
                <td>
                  <select class="form-control input-sm item-field" data-field="id_seguro" aria-label="Seguro del ítem {{$item->id}}">
                    <option value="" @if($item->id_seguro === null) selected @endif></option>
                    @if($item->id_seguro !== null && !$seguros->contains('id', $item->id_seguro))
                      <option value="{{$item->id_seguro}}" selected>{{$item->nombre_seguro ?: 'Seguro #'.$item->id_seguro}} (actual)</option>
                    @endif
                    @foreach($seguros as $seguro)
                      <option value="{{$seguro->id}}" @if($item->id_seguro !== null && (string) $item->id_seguro === (string) $seguro->id) selected @endif>{{$seguro->nombre}}</option>
                    @endforeach
                  </select>
                </td>
                <td>
                  <div class="item-actions">
                    <button type="button" class="btn btn-success btn-sm item-save" title="Guardar fila"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm item-delete" title="Eliminar ítem"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar</button>
                  </div>
                  <span class="item-status text-muted" role="status" aria-live="polite"></span>
                </td>
              </tr>
            @empty
              <tr class="items-empty"><td colspan="8" class="text-center text-muted">No se encontraron ítems.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="row">
        <div class="col-sm-5"><p id="items-count" data-total="{{$procedimientos->total()}}">{{$procedimientos->count()}} ítems en esta página · {{$procedimientos->total()}} resultados</p></div>
        <div class="col-sm-7 text-right">{{ $procedimientos->links() }}</div>
      </div>
    </div>
  </div>
</section>
@include('archivo_plano.procedimientos.inline_script')
@endsection
