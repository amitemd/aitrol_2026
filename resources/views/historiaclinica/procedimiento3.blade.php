
@extends('historiaclinica.base')

@section('action-content')


<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
} 

</style>


<br>
 
 <div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" style="width:1350px; " >
    <div class="modal-content"  id="imprimir3">
       <p>Hola Mundo</p>
    </div>
  </div>
</div>
<div class="modal fade" id="favoritesModal" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" >
    <div class="modal-content" >

    </div>
  </div>
</div>

<div class="container-fluid" >
    <div class="row ">
        <div class="col-md-12">
            <div class="box box-primary">
                victor touriz
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
                         
                        <button class="w3-bar-item w3-button tablink" onclick="openCity(event,'tab4')">EVOLUCIÓN</button>
                        <!--<button class="w3-bar-item w3-button tablink" onclick="openCity(event,'tab5')">TÉCNICAS</button>-->
                    </div>
  
                    <div class="col-md-12" id="tab4" style="display: none;" >  
                        <div class="box " id="evolucion"> 
                            
                        </div> 
                    </div>

                    


<script>

</script> 
                </div>
            </div>
        </div>  

        

        <!--div class="col-md-12">
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <h4>Fotos del Procedimiento</h4>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="col-md-10">
                        <div class="panel panel-default">
                            <div class="panel-heading">Listado de Fotos</div>
                            <div class="panel-body">
                                <div class="row">
                                @foreach($fotos as $thumbnail)
                                    <div class="col-sm-6 col-md-4">
                                        <div class="thumbnail">
                                            <a href="{{ route('procedimiento.imagen', ['id' => $thumbnail->id])}}" data-toggle="modal" data-target="#favoritesModal">
                                                <img src="{{asset($thumbnail->ruta.$thumbnail->archivo)}}" style="width: 120px; height: 120px;" alt="{{$thumbnail->tipo_documento}}">
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <form method="POST" action="{{route('historiaclinica.fotos')}}" class="dropzone" id="addimage"> 
                            <input type="hidden" name="_token" value="{{ csrf_token()}}">
                            <input type="hidden" name="id" value="{{ $hca[0]->hcid }}"> 
                            <input type="hidden" name="paciente" value="{{$agenda->id_paciente}}"> 
                        </form>
                    </div>                   
                </div>

            </div>
        </div-->        

        
        
        
        <div class="col-md-12" id="tab2" style="display: none;" >
            <div class="box box">
                <div class="box-header with-border">
                    <h4>Atención del Paciente</h4>
                   
                </div>
                <div class="box-body">
                    <div class="form-group col-md-12">
                        <form class="form-group" role="form" method="POST" action="{{ route('historiaclinica.guardar') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token()}}">
                            <input type="hidden" name="id" value="{{ $hca[0]->hcid }}">
                            <input id="hijos_vivos2" type="hidden" class="form-control" name="hijos_vivos" value="">
                                <input id="hijos_muertos2" type="hidden" class="form-control" name="hijos_muertos" value="">
                                <input id="gruposanguineo2" type="hidden" class="form-control" name="gruposanguineo" value="">
                                <input id="transfusion2" type="hidden" class="form-control" name="transfusion" value="">
                                <input id="alcohol2" type="hidden" class="form-control" name="alcohol" value="">
                                <input id="alergias2" type="hidden" class="form-control" name="alergias" value="">
                                <input id="vacuna2" type="hidden" class="form-control" name="vacuna" value="">
                                <input id="antecedentes_pat2" type="hidden" class="form-control" name="antecedentes_pat" value="">
                                <input id="antecedentes_fam2" type="hidden" class="form-control" name="antecedentes_fam" value="">
                                <input id="antecedentes_quir2" type="hidden" class="form-control" name="antecedentes_quir" value=""> 

                                <div class="form-inline col-md-12"> 
                            
                            </div>
                            <div>&nbsp</div>
                            <!--evolucion-->
                            <div class="form-group col-md-12 {{ $errors->has('evolucion') ? ' has-error' : '' }}">
                                <label for="evolucion" class="col-md-12 control-label">Hallazgos</label>
                                <textarea maxlength="300" rows="3" cols="50" id="evolucion" class="form-control" name="evolucion" required="required">@if(old('evolucion')!=''){{old('evolucion')}}@else{{$hca[0]->evolucion}}@endif</textarea>
                                @if ($errors->has('evolucion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('evolucion') }}</strong>
                                </span>
                                @endif   
                            </div>
                            <!--observaciones--> 
                            <div class="form-group col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                                <label for="observaciones" class="col-md-2 control-label">Conclusiones</label>
                                <textarea maxlength="250" rows="3" cols="50" id="observaciones" class="form-control" name="observaciones" required="required">@if(old('observaciones')!=''){{old('observaciones')}}@else{{$hca[0]->observaciones}}@endif</textarea>
                                @if ($errors->has('observaciones'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('observaciones') }}</strong>
                                </span>
                                @endif
                            </div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-9">
                                        <button type="submit" class="btn btn-primary">
                                           Guardar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                </div>
            </div>
        </div> 
    </div>
</div>




<script>
 
    $(document).ready(function() {

        ////// AGREGAR EVOLUCION //////
        evolucion();
        ////// AGREGAR EVOLUCION //////


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

        /*
        $("#hijos_muertos2").val(document.getElementById("hijos_muertos").value);
        $("#gruposanguineo2").val(document.getElementById("gruposanguineo").value);
        $("#transfusion2").val(document.getElementById("transfusion").value);
        $("#alcohol2").val(document.getElementById("alcohol").value);
        $("#alergias2").val(document.getElementById("alergias").value);
        $("#vacuna2").val(document.getElementById("vacuna").value);
        $("#antecedentes_pat2").val(document.getElementById("antecedentes_pat").value);
        $("#antecedentes_fam2").val(document.getElementById("antecedentes_fam").value);
        $("#antecedentes_quir2").val(document.getElementById("antecedentes_quir").value);
*/
        $('#example2').DataTable({
            'paging'      : false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : true,
            'info'        : false,
            'autoWidth'   : false
        });


    });

    /////////// AGREGAR EVOLUCION ////////
    function evolucion(){

        $.ajax({
                type: 'get',
                url:'{{ route('evolucion.mostrar',['hcid' => $hca[0]->hcid])}}',
                success: function(data){
                    $('#evolucion').empty().html(data);
                }
            })

    }
    /////////// AGREGAR EVOLUCION ////////

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

