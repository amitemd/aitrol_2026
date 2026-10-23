@extends('laboratorio.orden.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
    text-align: right;
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
        <div class="col-md-9">
          @php $a_mes = ['', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];@endphp
          <h3 class="box-title">Estadistica de Exámenes de Laboratorio del mes {{$a_mes[$mes]}}/{{$anio}} por Examen</h3>
        </div>
        
          <a class="btn btn-primary" href="{{route('orden.to_excel',['mes' => $mes, 'anio' => $anio])}}"><span class="glyphicon glyphicon-download-alt"></span> Descargar</a>
         
        
          <a class="btn btn-primary" onclick="goBack()"><span class="glyphicon glyphicon-arrow-left"></span> Regresar</a>
          
        
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      
      
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">

        <div class="table-responsive col-md-12">
          <h4 style="background-color: cyan;text-align: center;">CANTIDAD</h4>
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <thead>
              
              <tr role="row">
                <th >Examen</th>
                @foreach($convenios as $convenio)
                <th >{{$convenio->nombre}}-{{$convenio->nombre_corto}}</th>
                @endforeach
                <th >PART</th>
                <th style="background-color: #ffe6e6">TOTAL</th> 
                                                         
              </tr>
            </thead>
            <tbody>
            @foreach ($estadistico as $value)
              @if($value['cantidad']>0)
              <tr role="row">
                <td style="text-align: left !important;"><b>{{$value['examen']}}</b></td>
                @foreach($value['convenios'] as $convenio)
                <td >{{$convenio['cantidad']}}</td>
                @endforeach
                <td >{{$value['cant_part']}}</td>
                <td style="background-color: #ffe6e6">{{$value['cantidad']}}</td>
                
              </tr>
              @endif
            @endforeach
            </tbody>
            <tr role="row">
                <td style="text-align: left !important;"><b>TOTAL</b></td>
                @foreach($total_conv as $tcon)
                <td ><b>{{$tcon['cantidad']}}</b></td>
                @endforeach
                <td ><b>{{$total_part_cant}}</b></td>
                <td style="background-color: #ffe6e6"><b>{{$total_cantidad}}</b></td>
                
              </tr>
          </table>
        </div>

        <div class="table-responsive col-md-12">
          <h4 style="background-color: cyan;text-align: center;">VALOR</h4>
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <thead>
              
              <tr role="row">
                <th >Examen</th>
                 
                @foreach($convenios as $convenio)
                <th >{{$convenio->nombre}}-{{$convenio->nombre_corto}}</th>
                @endforeach
                <th >PART</th>
                <th style="background-color: #ffe6e6">TOTAL</th>                                          
              </tr>
            </thead>
            <tbody>
            @foreach ($estadistico as $value)
              @if($value['cantidad']>0)
              <tr role="row">
                <td style="text-align: left !important;"><b>{{$value['examen']}}</b></td>
                
                @foreach($value['convenios'] as $convenio)
                <td>$ {{number_format(round($convenio['valor'],2), 2) }}</td>
                @endforeach
                <td >$ {{number_format(round($value['val_part'],2), 2)}}</td>
                <td style="background-color: #ffe6e6">$ {{number_format(round($value['valor'],2), 2) }}</td>
              </tr>
              @endif
            @endforeach
            </tbody>
            <tr role="row">
                <td style="text-align: left !important;"><b>TOTAL</b></td>
                
                @foreach($total_conv as $tcon)
                <td ><b>$ {{number_format(round($tcon['valor'],2), 2) }}</b></td>
                @endforeach
                <td ><b>$ {{number_format(round($total_part_valor,2), 2)}}</b></td>
                <td style="background-color: #ffe6e6"><b>$ {{number_format(round($total_valor,2), 2) }}</b></td>
              </tr>
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

    $('tr[data-href]').on("click", function() {
      document.location = $(this).data('href');
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
  function goBack() {
    window.history.back();
  }

   



  

</script>


@endsection