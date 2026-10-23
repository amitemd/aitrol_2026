@extends('laboratorio.orden.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
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
        <div class="col-md-12">
          <h3 class="box-title">Laboratorio Cotizaciones</h3>
        </div>
        <div class="col-md-12">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="table-responsive">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <tbody>
                <tr>
                  <td width="10%"><b>Fecha Desde:</b></td>
                  <td width="10%">{{$fecha}}</td>
                  <td width="10%"><b>Fecha Hasta:</b></td>
                  <td width="10%">{{$fecha_hasta}}</td>
                  <td width="10%"><b>Seguro:</b></td>
                  <td width="10%">@if(!is_null($seguro)){{$seguros->find($seguro)->nombre}}@else{{'TODOS'}}@endif</td>
                  <td width="10%"><b>Paciente:</b></td>
                  <td width="10%">@if(!is_null($nombres)){{$nombres}}@endif</td>  
                </tr>  
              </tbody>
            </table>
          </div>
          </div>
        </div>  

        
        <form id="formulario" method="POST" action="{{route('orden_cotizaciones.reporte_cot')}}">
        {{ csrf_field() }}
        
                <input type="hidden" class="form-control input-sm" name="fecha" id="fecha">
                

          
                <input type="hidden" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta">

                <input type="hidden" class="form-control input-sm" name="seguro" id="seguro" value="@if(!is_null($seguro)){{$seguro}}@endif">
                
                <input value="@if($nombres!=''){{$nombres}}@endif" type="hidden" class="form-control input-sm" name="nombres" id="nombres" placeholder="Nombres y Apellidos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                
          
        <div class="form-group col-md-2 col-xs-6">
        <button type="submit" class="btn btn-primary" id="boton_buscar">
              <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar</button>
        </div>

    

             
      </form>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <thead>
              <tr role="row">
                <th width="1%" >ID.</th>
                <th width="1%" >Año.</th>
                <th width="1%" >Mes.</th>
                <th width="5%" >Fecha Orden</th>
                <th width="5%" >Seguro</th>
                <th width="5%" >Cédula</th>
                <th width="24%" >Nombres</th>
                <th width="24%" >Teléfono</th>
                <th width="20%" >Mail 1</th>
                <th width="24%" >Mail 2</th> 
                <th width="24%">Fecha consulta</th>                              
              </tr>
            </thead>
            <tbody>
            @php $i=1; $total=0;@endphp  
            @foreach ($ordenes as $value)
              <tr role="row">
                <td>{{$value->id}}</td>
                <td>{{$value->anio}}</td>
                <td>{{$value->mes}}</td>
                <td>{{substr($value->fecha_orden,0,10)}}</td>
                <td>{{$value->snombre}}</td>
                <td>{{$value->id_paciente}}</td>
                <td>{{$value->pnombre1}} @if($value->pnombre2=='N/A'||$value->pnombre2=='(N/A)') @else{{ $value->pnombre2 }} @endif {{$value->papellido1}} @if($value->papellido2=='N/A'||$value->papellido2=='(N/A)') @else{{ $value->papellido2 }} @endif</td>
                <td>@if($value->telefono1=='N/A'||$value->telefono2=='(N/A)'||$value->telefono3=='(N/A)')@else{{ $value->telefono1 }}-{{ $value->telefono2 }}-{{ $value->telefono3}}@endif
                </td>
                <td>{{$value->mail1}}</td>
                <td>{{$value->mail2}}</td>
                <td>{{$fecha}}</td>
              </tr>
            @php $i = $i +1; $total = $total + $value->total_valor; @endphp  
            @endforeach
              <tr role="row">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>   
              </tr>
            </tbody>
          </table>
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



</script>  

@endsection