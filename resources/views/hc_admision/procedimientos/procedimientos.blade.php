
@extends('hc_admision.visita.base')

@section('action-content')

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
} 
</style>


 
 <div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" style="width:1350px; " >
    <div class="modal-content"  id="imprimir3">
       <p>Hola Mundo</p>
    </div>
  </div>
</div>
<div class="modal fade" id="favoritesModal" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" style="width: 70%;">
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
                        </tbody>
                    </table>
                    <div class="w3-bar w3-blue">
                        
                        <button class="w3-bar-item w3-button tablink w3-red" onclick="location.href = '{{route('procedimientos_historia.mostrar',['id' => $agenda->id])}}'">PROCEDIMIENTOS</button>
                        <button class="w3-bar-item w3-button tablink" onclick="location.href = '{{route('preparacion.mostrar',['id' => $agenda->id])}}'">PREPARACIÓN</button>
                        <button class="w3-bar-item w3-button tablink" onclick="location.href = '{{route('cardiologia.mostrar',['id' => $agenda->id])}}'">CARDIOLOGIA</button>
                        <button class="w3-bar-item w3-button tablink" onclick="location.href = '{{route('receta.mostrar',['id' => $agenda->id])}}'">RECETA</button>
                    </div>
                    <div id="tab1" class="w3-container w3-border city">     
                        <div class="box ">  
                            <div class="box-header with-border">
                                <div class="col-md-8">
                                    <h4>PROCEDIMIENTOS REALIZADOS</h4>
                                </div>
                                <div class="col-md-2 ">
                                  <a data-toggle="modal" data-target="#favoritesModal"  class="btn btn-primary" href="{{ route('procedimientos_hc.agregar', ['hc_id' => $hca->hcid]) }}" style="width: 100%;">Agregar Procedimiento</a>
                                </div>
                            </div>
                            <?php $bandera= 0;?>
                            <div class="box-header">
                                <h6>PROCEDIMIENTOS AGENDADOS:
                                    @foreach($procedimientos_pentax as $value)
                                    <span class="bg-blue" style="border-radius: 4px;padding: 3px;margin-right: 5px;">{{$value->procedimiento->observacion}}</span>
                                    @endforeach</h6>
                            </div>
                            <div class="box-body">
                                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                  <div class="row">
                                    <div class="table-responsive col-md-12">
                                      <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                        <thead>
                                          <tr >
                                            <th width="70%">Nombre</th>
                                            <th width="30%">Acción</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($procedimientos_hc as $value)
                                            @if($value->estado == 1)
                                            <tr >
                                              <td >{{ $value->procedimiento_completo->nombre_general}}</td>
                                              <td>
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <a  href="{{ route('anestesiologia.mostrar', ['id' => $value->id]) }}" class="btn btn-warning col-md-6 col-xs-6 btn-margin">
                                                    Ingresar Informacion
                                                    </a>
                                                    <button  onclick="confirmardatos('{{$value->id}}')" class="btn btn-danger col-md-6 col-xs-6 btn-margin">
                                                    Eliminar
                                                    </button>
                                              </td>
                                            </tr>
                                            @endif
                                        @endforeach 

                                        </tbody>
                                        <tfoot>
                                          
                                        </tfoot>
                                      </table>
                                    </div>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>

<script>

</script> 
                </div>
            </div>
        </div>  

        
         

        
         
    </div>
</div>
<script>
    function confirmardatos(id) {
        var r = confirm("Esta Seguro que desea eliminar el procedimiento");
        if (r == true) {
            window.location.href = "{{route('procedimientos_hc.eliminar')}}/"+id;
        } 
    }
    $(document).ready(function() {

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

        $("#hijos_vivos2").val(document.getElementById("hijos_vivos").value);
        $("#hijos_muertos2").val(document.getElementById("hijos_muertos").value);
        $("#gruposanguineo2").val(document.getElementById("gruposanguineo").value);
        $("#transfusion2").val(document.getElementById("transfusion").value);
        $("#alcohol2").val(document.getElementById("alcohol").value);
        $("#alergias2").val(document.getElementById("alergias").value);
        $("#vacuna2").val(document.getElementById("vacuna").value);
        $("#antecedentes_pat2").val(document.getElementById("antecedentes_pat").value);
        $("#antecedentes_fam2").val(document.getElementById("antecedentes_fam").value);
        $("#antecedentes_quir2").val(document.getElementById("antecedentes_quir").value);

        $('#example2').DataTable({
            'paging'      : false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : true,
            'info'        : false,
            'autoWidth'   : false
        });


    });

    
    function openCity(evt, cityName) {
        var i, x, tablinks;
        x = document.getElementsByClassName("city");
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablink");
        for (i = 0; i < x.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" w3-red", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " w3-red";
    }

    var drogasadministradas = function ()
    {

        var id_record = document.getElementById('id_record').value;    
        
        //console.log(unix);
        $.ajax({
            type: 'get',
            url:'{{ url('historia/drogasadministradas')}}/'+id_record,//historia.drogasadministradas
            success: function(data){
                console.log(data);
                $('#drogas').empty().html(data);
            }
        })
    
    }

</script>

@include('sweet::alert')
@endsection

