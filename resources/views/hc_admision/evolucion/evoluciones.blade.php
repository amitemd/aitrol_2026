
@extends('historiaclinica.base')

@section('action-content')


<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
} 

</style>



<div  class="modal fade fullscreen-modal" id="Crea_Actualiza" tabindex="-1" role="dialog" aria-labelledby="Crea_ActualizaLabel">
  <div class="modal-dialog" role="document" >
    <div class="modal-content" >

    </div>
  </div>
</div>

<div class="container-fluid" >
    <div class="row ">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{ route('agenda.detalle',['id' => $agenda->id]) }}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Detalles</button>
                    </a>
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('historia.historia',['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Historia Clinica</button>
                    </a>  
                    <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('procedimientos_historia.mostrar',['id' => $agenda->id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-triangle-left"></span> Procedimientos</button>
                    </a>
                    @php $especialidad=Sis_medico\Especialidad::find($agenda->espid); @endphp
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <td><b>Paciente:</b></td>
                            <td colspan="3">{{$agenda->id_paciente}} - {{$agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif</td>
                            <td><b>Edad:</b></td>
                            <td><span id="edad"></span></td>
                            <td><b>Seguro:</b></td>
                            <td>{{$seguro->nombre}}</td>
                        </tr>
                        <tr>
                            <td><b>Procedimientos:</b></td>
                            <td colspan="3">{{$procedimientos_completo->find($hc_procedimiento->id_procedimiento_completo)->nombre_general}}</td>
                        </tr>                        
                        </tbody>
                    </table>
                    <div class="w3-bar w3-blue">
                        <button class="w3-bar-item w3-button tablink"onclick="location.href = '{{route('epicrisis.diagnostico',['id' => $id])}}' ">DIAGNOSTICO</button>
                        <button class="w3-bar-item w3-button tablink"onclick="location.href = '{{route('anestesiologia.mostrar',['id' => $hc_procedimiento->id])}}' ">ANESTESIOLOGÍA</button>
                        <button class="w3-bar-item w3-button tablink" onclick="location.href = '{{route('protocolo.mostrar',['id' => $id])}}'">PROTOCOLO</button>  
                        <button class="w3-bar-item w3-button tablink w3-red" onclick="location.href = '{{route('evolucion.evolucion',['id' => $id])}}'">EVOLUCIÓN</button>
                        <button class="w3-bar-item w3-button tablink" onclick="location.href = '{{route('epicrisis.mostrar',['id' => $id])}}'">EPICRISIS</button>
                        <button class="w3-bar-item w3-button tablink " onclick="location.href = '{{route('hc_video.mostrar',['id' => $id])}}'">VIDEO</button>
                    </div>
                    <br>    
                    <a class="form-group col-md-3 col-md-offset-6 col-sm-3 col-xs-3" data-toggle="modal" data-target="#Crea_Actualiza" href="{{ route('evolucion.mostrar',['id' => $id, 'id_evolucion' => '0'])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-level-up"></span> Nuevo</button>
                    </a>
                    @if($evoluciones!='[]')
                        <a class="form-group col-md-3 col-sm-3 col-xs-3" href="{{route('evolucion.imprimir',['id' => $id])}}"><button type="button" class="btn btn-primary" ><span class="glyphicon glyphicon-download-alt"></span> Descargar</button>
                        </a>
                    @endif
  
                    <div class="col-md-12" id="tab4" >
                        <div class="table-responsive col-md-12">
                            <table class="table table-striped" >
                                <thead>
                                    <th width="5%">Ingreso</th>
                                    <th width="30%">Cuadro Clínico</th>
                                    <th width="30%">Laboratorio</th>
                                    <th width="25%">Indicaciones</th>
                                    <th width="10%">Acción</th>      
                                </thead>
                                <tbody>
                                @if($evoluciones!='[]')
                                @foreach($evoluciones as $value)
                                    <tr>
                                        <td style="font-size: 12px;">{{$value->fecha_ingreso}}</td>
                                        <td><?php echo $value->cuadro_clinico; ?></td>
                                        <td><?php echo $value->laboratorio; ?></td>
                                        <td>
                                            <?php $indicaciones = $value->indicaciones; ?>
                                            @foreach($indicaciones as $indicacion)
                                            {{$indicacion->secuencia}}.-{{$indicacion->descripcion}}<br>
                                            @endforeach      
                                        </td>
                                        <td><a href="{{ route('evolucion.mostrar',['hcid' => $id, 'id_evolucion' => $value->id])}}" data-toggle="modal" data-target="#Crea_Actualiza" class="btn btn-warning col-md-7 col-sm-7 col-xs-7 btn-margin">Editar</a>
                                        </td>
                                    </tr>
                                @endforeach
                                @endif    
                                </tbody>
                            </table>
                        </div> 

                        <!--div class="box " id="evolucion"> 
                            
                        </div--> 
                    </div>

                    



                </div>
            </div>
        </div>  

        
      
 
    </div>
</div>




<script>
 
    $(document).ready(function() {

        $('#Crea_Actualiza').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 

        $(".breadcrumb").append('<li><a href="{{ route('agenda.agenda2') }}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.detalle',['id' => $agenda->id]) }}"></i> Detalle</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.detalle2',['id' => $agenda->id]) }}"></i> Historia</a></li>');
        $(".breadcrumb").append('<li class="active">Atención</li>');    

        $('#favoritesModal2').on('hidden.bs.modal', function(){
            $(this).removeData('bs.modal');
        });
        $('#favoritesModal').on('hidden.bs.modal', function(){
            $(this).removeData('bs.modal');
        });
                
        var edad;
        edad = calcularEdad('<?php echo $paciente->fecha_nacimiento; ?>')+ "años";
                
        $('#edad').text( edad );

        
        $('#example2').DataTable({
            'paging'      : false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : true,
            'info'        : false,
            'autoWidth'   : false
        });


    });


    

</script>

@include('sweet::alert')
@endsection

