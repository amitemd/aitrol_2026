@extends('pentax.base2')
@section('action-content')
<!-- Ventana modal editar -->
<div class="modal fade" id="Estados_pentax" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>


<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
      <div class="col-md-12">
        
        <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
        <div class="form-group col-md-6 {{ $errors->has('fecha') ? ' has-error' : '' }} " >
            <label class="col-md-3 control-label">Buscar fecha</label>
            <div class="col-md-6">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" name="fecha" class="form-control" id="fecha" required>
              </div> 
            </div>
            <div class="col-md-1">
                  <button type="submit" class="btn btn-primary">
                    Buscar
                  </button>
              </div>
        </div>         
        <!--div class="col-md-6">
          <a href="javascript:carga_reporte()" class="btn btn-primary">Reporte de Pentax</a>
        </div--> 
      </div>
      
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box box-success">
    <div class="box-header with-border">
      <h3 class="box-title">Procedimientos Endoscópicos</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <div class="box-body">
      <div id="index_pentax"></div>    
        
      
    </div>
  </div>  
   <div class="box box-success">
    <div class="box-header with-border">
      <h3 class="box-title">Procedimientos Funcionales</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div>
    <div class="box-body">
      <div id="index_pentax_109"></div>    
        
      
    </div>
  </div>  
   
</div>
    </section>
     
  </div>
  

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>


<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>


<script type="text/javascript"  >



$("#body2").addClass('sidebar-collapse');

$(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            
            @if($fecha == '0')
                defaultDate: '{{date("Y/m/d")}}'
            @else
            <?php 
            
            $fecha  = substr($fecha, 0,10);
            $fecha2 = date('Y/m/d', $fecha);
            ?>
                defaultDate: '{{$fecha2}}'
            @endif
            });
        $("#fecha").on("dp.change", function (e) {
            findex_pentax();
            findex_pentax_109();
        });
    });

/*var vartiempo = setTimeout(function(){ location.reload(); }, 30000);*/

  

var findex_pentax = function ()
{

    var fecha = document.getElementById('fecha').value;    
    var unix =  Math.round(new Date(fecha).getTime()/1000);
    //console.log(unix);
    $.ajax({
        type: 'get',
        url:'{{ route('pentax.indextv_dr')}}/'+unix,
        success: function(data){
          console.log("cambio");
            $('#index_pentax').empty().html(data);
        }
    })
    
}

var findex_pentax_109 = function ()
{

    var fecha = document.getElementById('fecha').value;    
    var unix =  Math.round(new Date(fecha).getTime()/1000);
    //console.log(unix);
    $.ajax({
        type: 'get',
        url:'{{ url('pentaxtv_dr/index_109')}}/'+unix,
        success: function(data){
            $('#index_pentax_109').empty().html(data);
        }
    })
    
}


function carga_reporte ()
{
    var fecha = document.getElementById('fecha').value;    
    var unix =  Math.round(new Date(fecha).getTime()/1000);
    //console.log(fecha);
    //console.log(unix);
    location.href ="{{route('pentax.reporte')}}/"+unix;
    
}

var vartiempo = setInterval(function(){ findex_pentax();findex_pentax_109(); }, 20000);
 


$('#Estados_pentax').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 


$(document).ready(function(){

  findex_pentax();
  findex_pentax_109();

  console.log(vartiempo);

  
  $("#body2").addClass('sidebar-collapse');

    $("p").click(function(){
      $(this).hide();
    });

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });


    
    
});

function cambia_estado(id,value){
  
  clearInterval(vartiempo);
  console.log(vartiempo);
  var vfecha = document.getElementById('fecha').value;    
  var unix =  Math.round(new Date(vfecha).getTime()/1000);

  document.getElementById('cambios_pentax').href="{{ route('pentax.edit')}}/"+id+"/"+value+"/"+unix;
  $('#cambios_pentax').click(); 
  /*location.href =" {{route('pentax.actualiza')}}/"+id+"/"+value+"/"+unix;*/
} 

function cambia_sala(id,vsala){

  
  var vfecha = document.getElementById('fecha').value;    
  var unix =  Math.round(new Date(vfecha).getTime()/1000);

  location.href =" {{route('pentax.actualiza_sala')}}/"+id+"/"+vsala+"/"+unix;

} 



</script>  
@endsection