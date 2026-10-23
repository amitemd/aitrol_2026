
<div class="box-body">
    <div class="col-md-12" style="margin-top: 10px;">
        <form method="POST" id="buscador_modal_vade">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-md-8">
              <div class="form-group">
                <label id="nombre_generico_vade" for="inputnombre" class="col-md-3 control-label">Nombre</label>
                <div class="col-md-9">
                   <input type="text" name="texto" id="inputnombre" class="form-control" placeholder="Nombre" value="{{$texto}}">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <button type="button" class="btn btn-primary" onclick="buscar_nuevo_modal();">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                 Buscar
              </button>
            </div>
          </div>
        </form>
    </div>
</div>


<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
    <thead>
      <tr role="row">
         <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Nombre</th>
        <th width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Presentacion</th>
        <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Dosis Recomendada</th>
        <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Dosis Pedriatica</th>
        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Laboratorio</th>
        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Indicaciones</th>
        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Contra indicaciones</th>
        <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending" style="font-size: 12px;">P. Unitario</th>
        <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending" style="font-size: 12px;">P. Total</th>
        <!--<th width="5%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">Acción</th>-->
      </tr>
    </thead>
    <tbody id="modificar">
        @foreach($nombre as $value)
            <tr>
                @if(is_null($value->laboratorio))
                    <td>{{$value->nombre}}</td>
                @else
                    <td>{{$value->nombre}}</td>
                    <td>{{$value->presentacion}}</td>
                    <td>{{$value->dosis}}</td>
                    <td>{{$value->dosis_pediatrica}}</td>
                    <td>{{$value->laboratorio}}</td>
                    <td>{{$value->indicaciones}}</td>
                    <td>{{$value->contraindicaciones}}</td>
                    <td>{{$value->precio_unitario}}</td>
                    <td>{{$value->precio_total}}</td>
                    <!--<td><a onclick="enviar_a_receta();" class="btn btn-primary" style="color: white;">Enviar a Receta</a></td>-->
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
<script type="text/javascript">
    function enviar_a_receta(){
        alert('enviar');
    }

    function buscar_nuevo_modal(){
        $.ajax({
            type: 'post',
            url:"{{ asset('hc4/revisar/informacion/ventana/')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            data: $('#buscador_modal_vade').serialize(),
            success: function(data){
                $('#modificar').html(data);
            },
            error: function(data){
            }
        });
    }

    $("#nombre_generico_vade").autocomplete({
        source: function( request, response ) {
          $.ajax({
            url:"{{route('buscar_nombre.receta')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            data: {
                term: request.term,
                  },
                  dataType: "json",
                  type: 'post',
                  success: function(data){
                    response(data);
                  }
                })
            },
        minLength:2,
      });
</script>
