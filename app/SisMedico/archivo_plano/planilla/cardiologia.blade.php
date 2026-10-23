

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
    <!--<button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Cardiología</h4>-->
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
        <span id="Label8" style="color:#003366;font-family:Arial;font-size:14pt;font-weight:bold;">Órdenes de Cardiología</span>
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
            <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar_cardio();"></i>
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
            <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar_labs();"></i>
          </div>   
        </div>
      </div>  
    </div>       
  </form>
  <button type="button" class="btn btn-primary" id="boton_buscar">
    <span class="glyphicon glyphicon-search" aria-hidden="true"></span> 
  </button>

  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" >
    <div class="row" id="listado">
      <div class="table-responsive col-md-12" style="min-height: 210px;">
        <table id="example2_labs" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
          <thead>
            <tr role="row">
              <th width="10">Sel.</th>
              <th width="10">Fecha</th>
              <th width="10">Convenio</th>
              <th width="10">Especialidad</th>
              <th width="10">Doctor</th>
              <th width="10">Cant.</th>
              <th width="10">Codigo</th>
              <th width="20">Valor</th> 
            </tr>
          </thead>
          <tbody>
          @foreach($ic as $value)
          <tr>
            @foreach($value as $espe)

            @php
            //dd($ic);
            $codigo = '99203';
            $id_empresa = $espe->empresa_agenda;
            $id_seguro = $espe->seguro_historia;
            if($espe->id_empresa != null){
              $id_empresa =$espe->id_empresa;
            }
            if($espe->id_seguro){
              $id_seguro =$espe->id_seguro;
            }
            $convenio =  Sis_medico\Convenio::where('id_seguro',$id_seguro)->where('id_empresa', $id_empresa)->first();            
            //dd($convenio);
            $proc = Sis_medico\ApProcedimiento::where('ap_procedimiento.codigo', $codigo)->join('ap_procedimiento_nivel as apn','apn.id_procedimiento','ap_procedimiento.id')->where('apn.cod_conv', $convenio->id_nivel)->select('ap_procedimiento.descripcion', 'apn.uvr1','apn.prc1')->first();
            //dd($proc);
            $valor = round(($proc->uvr1*$proc->prc1),2);
            @endphp
            <td><button type="button" class="btn btn-xs btn-success" onclick="seleccionar_cardio('{{$espe->hcid}}','{{$a_plano->id}}','{{$espe->id_seguro}}','{{$espe->id_empresa}}','{{$espe->id_agenda}}')"><span class="glyphicon glyphicon-ok-sign"></span></button></td>
            <td>{{substr($espe->fechaini,0,10)}}</td>
            <td>{{$espe->nombre_seguro}} / @if($espe->id_empresa != null) {{$espe->nombre_corto}}@else {{$espe->empresa_agenda}}@endif</td>
            <td>{{$espe->nombre_espe}}</td>
            <td>Dr. {{$espe->nombre1}} {{$espe->apellido1}}</td>
            <td>1</td>
            <td>{{$codigo}}</td>
            <td>{{$valor}}</td>
            @endforeach
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  </div>
  <div class="modal-footer">
    <!--<button type="button" class="btn btn-primary"  data-dismiss="modal">Close</button>-->
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
        buscar_cardio();
    });

     $("#fecha_hasta").on("dp.change", function (e) {
        buscar_cardio();
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

</script>  

