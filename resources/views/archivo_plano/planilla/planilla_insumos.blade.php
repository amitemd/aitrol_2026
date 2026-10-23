

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
} 
.dropdown-menu>li>a{
    color:white !important;
    padding-left: 3px !important;
    padding-right: 3px !important;
    font-size: 12px !important;
  }
 
  .dropdown-menu>li>a:hover{
    background-color:#008d4c !important;
  }
  .cot>li>a:hover{
    background-color:#00acd6 !important;
  }
</style>

<div class="modal-content" style="width: 100%;">
  <div class="modal-header" style="padding-top: 5px; padding-bottom: 1px;">
    <div class="row" style="border-bottom: 1px solid black;">
      <div class="col-md-2">
        <a class="btn btn-light" data-dismiss="modal">
          <h1 style="font-size: 12px; margin:0;">
              <img style="width: 30px;height:23px" src="{{asset('/')}}hc4/img/exit.png">
              <label style="font-size: 14px">Cerrar</label>
          </h1> 
        </a>
      </div>
      <div class="col-md-6">
      </div>
      <div class="col-md-4">
        <span id="Label8" style="color:#003366;font-family:Arial;font-size:14pt;font-weight:bold;">Insumos Usados</span>
      </div>
    </div>
  </div>
  <div class="modal-body">
    <form id="insumos_form">
    {{ csrf_field() }}
    <input type="hidden" name="id" value="{{$a_plano->id}}">
    <div class="form-group col-md-4 col-xs-6">
      <label for="fecha" class="col-md-3 control-label">Desde</label>
      <div class="col-md-9">
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off" placeholder="AAAA/MM/DD">
          <div class="input-group-addon">
            <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; "></i>
          </div>   
        </div>
      </div>  
    </div>
    <div class="form-group col-md-4 col-xs-6">
      <label for="fecha_hasta" class="col-md-3 control-label">Hasta</label>
      <div class="col-md-9">
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off" placeholder="AAAA/MM/DD">
          <div class="input-group-addon">
            <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; "></i>
          </div>   
        </div>
      </div>  
    </div>       
  </form>
  <button type="button" class="btn btn-primary" id="boton_buscar" onclick="buscar_planilla_detalle()">
    <span class="glyphicon glyphicon-search" aria-hidden="true"></span> 
  </button>
  <!--button type="button" class="btn btn-primary" id="boton_add_all" >
    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 
  </button-->
  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" >
    <div class="row" id="listado">
    <div class="table-responsive col-md-12" style="min-height: 210px;">
        <table id="example2_labs" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
          <thead>
            <tr role="row">
              <th >Sel.</th>
              <th >Fecha</th>
              <th >Procedimiento</th> 
              <th >Detalle</th> 
            </tr>
          </thead>
          <tbody>
          @foreach($insumos as $value)
              <tr>

                  <td><button type="button" class="btn btn-xs btn-success" onclick="seleccionar_insumo('{{$a_plano->id}}','{{$value->id}}')"><span class="glyphicon glyphicon-ok-sign"></span></button></td>
                  <td>{{substr($value->fecha,0,10)}}</td>
                  @php $procedimientos = $value->procedimiento->hc_procedimiento_f;  @endphp
                  <td>@foreach($procedimientos as $procedimiento) @if($procedimiento->procedimiento->grupo_procedimiento != null) {{ $procedimiento->procedimiento->nombre }} @endif @endforeach</td> 
                  <td style="text-align:left;">
                    @foreach($value->detalles as $detalle)
                      @if($detalle->check == 1 and $detalle->estado == 1)
                        @php $producto = Sis_medico\Producto::where('codigo',$detalle->codigo)->first(); @endphp
                        @if(!is_null($producto))
                        <span  @if(!is_null($producto->tipo_produc)) @if($producto->tipo_produc->nombre == 'INSUMOS RE' || $producto->tipo_produc->nombre == 'INSUMOS MEDICOS' ) style="background-color:#5DE2CA;" @endif @else style="color:red;"  @endif> {{$producto->codigo}} - {{$producto->nombre}} - <b> Precio: </b> {{$detalle->precio}} - @if(!is_null($producto->tipo_produc)) <b> {{$producto->tipo_produc->nombre}} {{$producto->tipo_produc->ap_insumo}} </b> @endif </span> <br>
                        @endif
                      @endif
                    @endforeach
                  </td>
              </tr>
          @endforeach
          
          </tbody>
        </table>
      </div>
    </div>
  </div>
  </div>
  <div class="modal-footer">
  </div>
</div>  
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset('/js/jquery-ui.js') }}"></script>
<script type="text/javascript">
    $('#fecha').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD',
        defaultDate: '{{$fecha}}',
    });

    $('#fecha_hasta').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD',
        defaultDate: '{{$fecha_hasta}}',
    });

    $("#fecha").on("dp.change", function (e) {
        //buscar_cardio();
    });

     $("#fecha_hasta").on("dp.change", function (e) {
        //buscar_cardio();
    });

    $('#example2_labs').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });

    function buscar_planilla_detalle(){
        $.ajax({
          type: 'post',
          url:"{{ route('nueva_planilla.cargarInsumosPlanillaBuscar', ['cabecera' => $a_plano->id ]) }}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#insumos_form").serialize(),
          success: function(data){
            console.log(data);
            $('#listado').empty().html(data);
          },
          error: function(data){
             console.log(data);
          }
        });
    }

    function seleccionar_insumo(cabecera,id_planilla){
      if (confirm('�Desea Agregar todos los items planillados?')) {
      $.ajax({
        type: 'post',
        url:"{{ route('nueva_planilla.agregar_insumo')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: {
          'cabecera':cabecera,
          'id_planilla':id_planilla,
        },
        success: function(data){
          if(data.status == 'success'){
            location.reload();
          }
          else{
            swal(data.status, data.msj, 'error');
          }
        },
        error: function(data){
            console.log(data);
        }
      });
      }else{
          location.reload();
      }
    }

    function add_all_insumos(cabecera){
      $.ajax({
        type: 'get',
        url:"{{ url('archivo_plano/nuevo/planilla/add/all/insumos')}}/"+cabecera,
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: {
        },
        success: function(data){
          if(data.status == 'success'){
            location.reload();
          }
          else{
            swal(data.status, data.msj, 'error');
          }
        },
        error: function(data){
            console.log(data);
        }
      });
    }
</script>