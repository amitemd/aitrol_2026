

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
  .modal-lg {
    width: 1500px;
  }
</style>

<div class="modal-content">
  <div class="modal-header" >
    <div class="row" >
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
        <span id="Label8" style="color:#003366;font-family:Arial;font-size:14pt;font-weight:bold;">Medicinas Usadas</span>
      </div>
    </div>
  </div>
  <div class="modal-body">
    <form id="cardio_form">
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
    <button type="button" class="btn btn-primary" id="boton_buscar" onclick="buscar_recetas()">
      <span class="glyphicon glyphicon-search" aria-hidden="true"></span> 
    </button>
    <!--button type="button" class="btn btn-primary" id="boton_add_all" onclick="add_all_medicinas('{{$a_plano->id}}')">
      <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 
    </button-->
    <div class="row">
      <div class="col-md-9">
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" >
        <div class="row" id="listado">
          <div class="table-responsive col-md-12" style="min-height: 210px;">
            <table id="example2_labs" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
              <thead>
                <tr role="row">
                  <th width="10">Sel.</th>
                  <th width="10">Fecha</th>
                  <th width="10">Medico</th>
                  <th width="10">Accion</th>
                  <!--th width="25">Descripcion</th>
                  <th width="10">Cant.</th>
                  <th width="10">Valor</th--> 
                </tr>
              </thead>
              <tbody>
              @foreach($medicinas as $value)
                @php 
                  $receta = Sis_medico\hc_receta::find($value->id);
                @endphp
                  <tr>
                      <td><button type="button" class="btn btn-xs btn-success" onclick="seleccionar_medicina('{{$a_plano->id}}','{{$value->id}}')"><span class="glyphicon glyphicon-ok-sign"></span></button></td>
                      <td>{{substr($value->created_at,0,10)}}</td>
                      <td>@if($receta->doctor != null){{$receta->doctor->apellido1}} {{$receta->doctor->nombre1}}@endif</td>
                      <!--td>{{$value->descripcion_procedimiento}}</td>
                      <td>{{$value->cant}}</td-->
                      <td><button class="btn btn-success" onclick="ver_detalle('{{$receta->id}}')">Detalle</button></td>
                  </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      </div>
      <div class="col-md-3" id="vt_area"></div>
    </div>  
  </div> 
  <div class="modal-footer">
  </div>
</div>  
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

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

    function buscar_cardio(){
        $.ajax({
          type: 'post',
          url:"{{ route('planilla.busca_cardiologia') }}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#cardio_form").serialize(),
          success: function(data){
            console.log(data);
            $('#listado').empty().html(data);
          },
          error: function(data){
             console.log(data);
          }
        });
    }

    
    function buscar_recetas(){
        $.ajax({
          type: 'post',
          url:"{{ route('nueva_planilla.cargarMedicinasPlanilla_div',['id' => $a_plano->id]) }}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#cardio_form").serialize(),
          success: function(data){
            console.log(data);
            $('#listado').empty().html(data);
          },
          error: function(data){
             console.log(data);
          }
        });
    }

    function seleccionar_medicina(cabecera,id_receta){
      $.ajax({
        type: 'post',
        url:"{{ route('nueva_planilla.agregar_medicina')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: {
          'cabecera':cabecera,
          'id_receta':id_receta,
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

    function add_all_medicinas(cabecera){
      $.ajax({
        type: 'get',
        url:"{{ url('archivo_plano/nuevo/planilla/add/all/medicinas')}}/"+cabecera,
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

    function ver_detalle(id){
      $.ajax({
        type: 'get',
        url:"{{ url('vt_nuevo_archivo/medicina/detalle')}}/"+id,
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: {
        },
        success: function(data){

          $('#vt_area').empty().html(data);

        },
        error: function(data){
            console.log(data);
        }
      });
    }
</script>