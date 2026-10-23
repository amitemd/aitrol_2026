@extends('laboratorio.privado.base')
@section('action-content')

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
<!-- Ventana modal editar -->
<div class="modal fade fullscreen-modal" id="doctor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-md-6">
          <h3 class="box-title">Listado de órdenes de Laboratorio</h3>
        </div>
        
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{route('privados.search')}}">
        {{ csrf_field() }}
        <div class="form-group col-md-4 col-xs-6">
            <label for="fecha" class="col-md-3 control-label">Desde</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
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
                <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                </div>   
              </div>
            </div>  
          </div>

          <div class="form-group col-md-4 col-xs-6">
            <label for="seguro" class="col-md-3 control-label">Seguro</label>
              <div class="col-md-9"> 
                <select id="seguro" name="seguro" class="form-control input-sm" onchange="buscar();">
                  <option value="">SEGUROS PRIVADOS</option>
                  @foreach ($seguros as $value)
                    <option @if(!is_null($seguro))@if($seguro == $value->id) selected @endif @endif value="{{$value->id}}">{{$value->nombre}}</option>
                  @endforeach
                </select>
              </div>
          </div>

          <div class="form-group col-md-4 col-xs-6">
            <label for="nombres" class="col-md-3 control-label">Paciente</label>
            <div class="col-md-9">
              <div class="input-group">
                <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Nombres y Apellidos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                </div>
              </div>  
            </div>
          </div>
          
        <div class="form-group col-md-2 col-xs-6">
          <button type="submit" class="btn btn-primary btn-xs" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar</button>
        </div>      

        <button type="submit" class="btn btn-primary btn-xs" formaction="{{route('privados.ordenes_rpt')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Reporte</button>
   
        <button type="submit" class="btn btn-primary btn-xs" formaction="{{route('privados.detalle_rpt')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Detalle de Ordenes</button>

        <div class="form-group col-md-2 col-xs-3">
          <button type="submit" style="width: 100%;" class="btn btn-primary btn-xs" formaction="{{route('orden.estad_mes')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Estadistico Exámenes</button>
        </div>
              
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12" style="min-height: 210px;">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
            <thead>
              <tr role="row">
                <th >Fecha</th>
                <th >Nombres</th>
                <th >Convenio</th>
                <th >Creada</th>
                <th >Modificada</th>
                <th >Cantidad</th>
                <th >Valor</th>
                <th >Exémenes Ingresados(%)</th>
                <th >Resultados</th>                 
                <th >Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($ordenes as $value)
              <tr role="row">
                <td>{{substr($value->fecha_orden,0,10)}}</td>
                <!--td>{{$value->id_paciente}}</td-->
                
                <td>{{$value->papellido1}} @if($value->papellido2=='N/A'||$value->papellido2=='(N/A)') @else{{ $value->papellido2 }} @endif {{$value->pnombre1}} @if($value->pnombre2=='N/A'||$value->pnombre2=='(N/A)') @else{{ $value->pnombre2 }} @endif </td>
                <td>{{$value->snombre}}/{{$value->nombre_corto}}</td>
                <td>{{substr($value->cnombre1,0,1)}}{{$value->capellido1}}</td>
                <td>{{substr($value->mnombre1,0,1)}}{{$value->mapellido1}}</td>
                <td>{{$value->cantidad}}</td>
                <td>{{$value->total_valor}}</td>
                <td>
                  <div class="progress progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$value->id}}">
                      <span id="sp{{$value->id}}" style="color: black;"></span>
                    </div>
                  </div>
                </td>
                <td>
                  @if($value->estado=='1')
                  <div class="col-md-12">
                    <div class="btn-group" >
                      <button type="button" class="btn btn-success btn-xs" onclick="descargar({{$value->id}});"><span >Resultados</span></button>
                      <button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu" role="menu" style="background-color: #00a65a;padding: 2px;min-width: 100px;">
                        <li ><a  href="{{ route('resultados.imprimir3',['id' => $value->id]) }}" >Formato Gastro</a></li>
                      </ul>
                    </div>
                  </div>
                  @endif  
                </td>
                <td>
                  <button type="button" class="btn btn-info btn-xs" onclick="window.open('{{ route('cotizador.imprimir', ['id' => $value->id]) }}','_blank');"><span >Orden</span></button>
                  <button type="button" class="btn btn-success btn-xs" onclick="window.open('{{ route('paciente.historia', ['id' => $value->id_paciente]) }}','_blank');"><span >Historia Clínica</span></button>
                  @if($value->realizado !='0')
                    <!--div class="btn-group" >
                      <button type="button" class="btn btn-success btn-xs" onclick="descargar({{$value->id}});"><span>Resultados</span></button>
                      <button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu" role="menu" style="background-color: #00a65a;padding: 2px;min-width: 100px;">
                        <li ><a  href="{{ route('resultados.imprimir3',['id' => $value->id]) }}" >Formato Gastro</a></li>
                        
                      </ul>
                    </div-->
                  @endif
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($ordenes->currentPage()-1)*$ordenes->perPage()}}  / @if(($ordenes->currentPage()*$ordenes->perPage())<$ordenes->total()){{($ordenes->currentPage()*$ordenes->perPage())}} @else {{$ordenes->total()}} @endif de {{$ordenes->total()}} registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $ordenes->appends(Request::only(['fecha', 'fecha_hasta', 'nombres']))->links()}}
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

  $(document).ready(function($){

    @foreach ($ordenes as $value)

      $.ajax({
        type: 'get',
        url:"{{ route('resultados.puede_imprimir',['id' => $value->id]) }}", 
        
        success: function(data){
          
            if(data.cant_par==0){
              var pct = 0;  
            }else{
              var pct = data.certificados/data.cant_par*100;  
            }
            //alert(pct);
            $('#td{{$value->id}}').css("width", Math.round(pct)+"%");
            $('#sp{{$value->id}}').text(Math.round(pct)+"%");
            if(pct < 10){
              $('#td{{$value->id}}').addClass("progress-bar-danger");
            }else if(pct >=10 && pct<90){
              $('#td{{$value->id}}').addClass("progress-bar-warning");  
            }else{
              $('#td{{$value->id}}').addClass("progress-bar-success");
            }
          

        },


        error: function(data){
          
           
        }
      });

    @endforeach

    $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            
            
            defaultDate: '{{$fecha}}',
            
            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            
            
            defaultDate: '{{$fecha_hasta}}',
            
            });
        $("#fecha").on("dp.change", function (e) {
            buscar();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            buscar();
        });

    $(".breadcrumb").append('<li class="active">Órdenes</li>');

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })



  });

   $('#doctor').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 


  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
  /*function descargar(id_or){
    location.href = '{{url('resultados/imprimir')}}/'+id_or;
  }*/
  function descargar(id_or){
    var cert = $('#sp'+id_or).text();
    if(cert=='0%'){
      alert("Sin Exámenes Ingresados");
    }else{
      //location.href = '{{url('resultados/imprimir')}}/'+id_or;
      window.open('{{url('resultados/imprimir')}}/'+id_or,'_blank');  
    }
    
  }

</script>  

@endsection