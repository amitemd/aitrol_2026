@extends('laboratorio.orden.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
} 
</style>

    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-md-6">
          <h3 class="box-title">Detalle de la Orden</h3>
        </div>
        <!--a class="btn btn-primary" href="{{URL::previous()}}"><span class="glyphicon glyphicon-arrow-left"></span> Regresar</a-->
        <button class="btn btn-primary" onclick="goBack();"><span class="glyphicon glyphicon-arrow-left"></span> Regresar</button>
        
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      
      
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-6">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <thead>
              <tr role="row">
                <th width="5%">No.</th>
                <th width="75%">Examen</th>
                <th width="20%">Valor</th>
              </tr>
            </thead>
            <tbody>
              @php $total = 0; $i = 1; @endphp
            @foreach ($detalles as $value)
              <tr role="row">
                <td>{{$i}}</td>
                <td>{{$value->examen->descripcion}}</td>
                <td style="text-align: right;">$ {{number_format($value->valor,2)}}</td>
                @php $total = $total + $value->valor; $i = $i + 1; @endphp
              </tr>
            @endforeach
              <tr role="row">
                <td><b>{{$i}}</b></td>
                <td><b>Total</b></td>
                <td style="text-align: right;"><b>$ {{number_format($total,2)}}</b></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        
        
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

  function goBack() {
    window.history.go(-1);
  }

</script>  

@endsection