
@extends('historiaclinica.base2')

@section('action-content')

<style type="text/css">
    
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
}  


</style>

<div class="modal fade fullscreen-modal" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document"  >
    <div class="modal-content"  id="imprimir3">
        
    </div>
  </div>
</div>

<div class="container-fluid" >
    <div class="row">

        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4 class="form-group col-md-6">Historial Clínico Ingresada en la Agenda</h4>
                    @if(!is_null($historiaclinica))
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{ route('agenda.detalle',['id' => $agenda->id]) }}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Detalles</button>
                    </a>
                    <!--a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('agenda.detalle2', ['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Atención Procedimiento</button>
                    </a-->
                    @endif   
                </div>
                <div class="box-body" >
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <table id="example2" class="table table-bordered table-hover">
                                    <tbody>
                                        <tr>
                                        @foreach($hcagenda as $value)
                                            <td><embed src="{{asset($value->ruta.$value->archivo)}}">   
                                            <a class="button" data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.hcagenda', ['id' => $value->id])}}"><button type="button" class="btn btn-primary btn-xs" >Abrir</button></a>
                                            </td> 
                                        @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>  
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4>Historial Clínico del Paciente</h4>   
                </div>
                <div class="box-body" >
                    <div class="row">
                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                            <div class="table-responsive col-md-12">
                                <table id="example" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                    <thead>
                                      <tr>
                                        <th>Fecha</th>
                                        <th>Especialidad</th>
                                        <th>Seguro</th>
                                        <th>Tipo</th>
                                        <th>Doctor </th>
                                      </tr>
                                    </thead-->
                                    <tbody>
                                    @foreach ($hcp as $value)
                                        @if($value->tipo_cita == 0)
                                        <tr>
                                            <td><a  href="{{ route('agenda.mostrar', ['id' => $value->id_agenda])}}" data-toggle="modal" data-target="#favoritesModal2" >{{ substr($value->fechainicio, 0, -3)}}</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar', ['id' => $value->id_agenda])}}">{{ $value->especialidad}}</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar', ['id' => $value->id_agenda])}}">{{ $value->snombre}}</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar', ['id' => $value->id_agenda])}}"> Consulta</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar', ['id' => $value->id_agenda])}}">Dr(a). {{ $value->dnombre1 }} {{ $value->dapellido1 }} </a></td>  
                                        </tr>
                                        @elseif($value->tipo_cita == 1)
                                        <tr>
                                            <td><a  href="{{ route('agenda.mostrar2', ['id' => $value->id_agenda])}}" data-toggle="modal" data-target="#favoritesModal2" >{{ substr($value->fechainicio, 0, -3)}}</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar2', ['id' => $value->id_agenda])}}">{{ $value->especialidad}}</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar', ['id' => $value->id_agenda])}}">{{ $value->snombre}}</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar2', ['id' => $value->id_agenda])}}"> Procedimientos</a></td>
                                            <td><a data-toggle="modal" data-target="#favoritesModal2"  href="{{ route('agenda.mostrar2', ['id' => $value->id_agenda])}}">Dr(a). {{ $value->dnombre1 }} {{ $value->dapellido1 }} </a></td>  
                                        </tr>
                                        @endif 
                                    @endforeach
                                    </tbody>


                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>



<script type="text/javascript">

   
$(document).ready(function(){

    $(".breadcrumb").append('<li><a href="{{ route('agenda.agenda2') }}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.detalle',['id' => $agenda->id]) }}"></i> Detalle</a></li>');
        $(".breadcrumb").append('<li class="active">Historia</li>');

    $('#example').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
       
    });

    var edad;
                edad = calcularEdad('<?php echo $agenda->fecha_nacimiento; ?>')+ " años";
                $('#edad').text(edad);

    

});
 /*
    $(document).ready(function(){

$("#example").DataTable({
    'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      

    });

});*/


function actualiza(e){
    cortesia = document.getElementById("cortesia").value;
    
    if (cortesia == "SI"){

        location.href ="{{ route('vdoctor.cortesia', ['id' => $agenda->id, 'c' => 1])}}";

    }
    else if(cortesia == "NO"){
        location.href ="{{ route('vdoctor.cortesia', ['id' => $agenda->id, 'c' => 0])}}";
    }

} 

function Mostrar(){
    alert("hola");
}   




</script>                     
                           

                        
                           

                       

                                           



	
</section>

@include('sweet::alert')
@endsection
