@extends('enfermeria.base')
@section('action-content')
<!-- Main content -->


<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
}
</style>


<div class="container-fluid" >
    <div class="row ">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <td><b>Paciente:</b></td>
                            <td colspan="3">{{$agenda->id_paciente}} - {{$agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif {{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif</td>
                            <td><b>Edad:</b></td>
                            <td><span id="edad"></span></td>
                            <td><b>Seguro:</b></td>
                            <td>{{$agenda->hsnombre}}</td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="box-body">
                        <form class="form-vertical" role="form" method="POST" action="{{route('enfermeria.guardar')}}" >
                            {{ csrf_field() }}
                            <input type="hidden" name="id_paciente" value="{{$agenda->id_paciente}}">
                            <input type="hidden" name="hcid" value="{{$agenda->hcid}}">
                            <!--Grupo Sanguineo-->

                            <div class="col-md-3{{ $errors->has('gruposanguineo') ? ' has-error' : '' }}">
                                <label for="gruposanguineo" class="col-md-12 control-label" >G.Sanguíneo</label>
                                <select id="gruposanguineo" class="form-control input-sm" name="gruposanguineo"  required>
                                    <option value=" ">Seleccionar ..</option>
                                    <option @if(old('gruposanguineo')=="AB-"){{"selected"}}@elseif(old('gruposanguineo')=="" && $agenda->gruposanguineo == "AB-"){{"selected"}}@endif value="AB-">AB-</option>
                                    <option @if(old('gruposanguineo')=="AB+"){{"selected"}}@elseif(old('gruposanguineo')=="" && $agenda->gruposanguineo == "AB+"){{"selected"}}@endif value="AB+">AB+</option>
                                    <option @if(old('gruposanguineo')=="A-"){{"selected"}}@elseif(old('gruposanguineo')=="" && $agenda->gruposanguineo == "A-"){{"selected"}}@endif value="A-">A-</option>
                                    <option @if(old('gruposanguineo')=="A+"){{"selected"}}@elseif(old('gruposanguineo')=="" && $agenda->gruposanguineo == "A+"){{"selected"}}@endif value="A+">A+</option>
                                    <option @if(old('gruposanguineo')=="B-"){{"selected"}}@elseif(old('gruposanguineo')=="" && $agenda->gruposanguineo == "B-"){{"selected"}}@endif value="B-">B-</option>
                                    <option @if(old('gruposanguineo')=="B+"){{"selected"}}@elseif(old('gruposanguineo')=="" && $agenda->gruposanguineo == "B+"){{"selected"}}@endif value="B+">B+</option>
                                    <option @if(old('gruposanguineo')=="O-"){{"selected"}}@elseif(old('gruposanguineo')=="" && $agenda->gruposanguineo == "O-"){{"selected"}}@endif value="O-">O-</option>
                                    <option @if(old('gruposanguineo')=="O+"){{"selected"}}@elseif(old('gruposanguineo')=="" && $agenda->gruposanguineo == "O+"){{"selected"}}@endif value="O+">O+</option>
                                </select>
                                @if ($errors->has('gruposanguineo'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('gruposanguineo') }}</strong>
                                </span>
                                @endif
                            </div>

                            <!--presion-->
                            <div class="col-md-3{{ $errors->has('presion') ? ' has-error' : '' }}">
                                <label for="presion" class="col-md-12 control-label">Presión Arterial</label>
                                <input id="presion" min=0 type="text" step="any" class="form-control input-sm" name="presion" value=@if(old('presion')!='')"{{old('presion')}}"@elseif($agenda->presion!="")"{{ $agenda->presion }}" @else "{{0}}" @endif >
                                @if ($errors->has('presion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('presion') }}</strong>
                                </span>
                                @endif
                            </div>

                             <!--pulso-->
                            <div class="col-md-3{{ $errors->has('pulso') ? ' has-error' : '' }}">
                                <label for="pulso" class="col-md-12 control-label">Pulso</label>
                                <input id="pulso" min=0 type="number" step="any" class="form-control input-sm" name="pulso" value=@if(old('pulso')!='')"{{old('pulso')}}"@elseif($agenda->pulso!="")"{{ $agenda->pulso }}" @else "{{0}}" @endif >
                                @if ($errors->has('pulso'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('pulso') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-md-3{{ $errors->has('o2') ? ' has-error' : '' }}">
                                <label for="o2" class="col-md-12 control-label">Oxigeno</label>
                                <input id="o2" min=0 type="number" step="any" class="form-control input-sm" name="o2" value=@if(old('o2')!='')"{{old('o2')}}"@elseif($agenda->o2!="")"{{ $agenda->o2 }}" @else "{{0}}" @endif >
                                @if ($errors->has('o2'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('o2') }}</strong>
                                </span>
                                @endif
                            </div>

                            <!--temperatura-->
                            <div class="col-md-3{{ $errors->has('temperatura') ? ' has-error' : '' }}">
                                <label for="temperatura" class="col-md-12 control-label">Temperatura (ºC)</label>
                                <input id="temperatura" min=0 type="number" step="any" class="form-control input-sm" name="temperatura" value=@if(old('temperatura')!='')"{{old('temperatura')}}"@elseif($agenda->temperatura!="")"{{ $agenda->temperatura }}" @else "{{0}}" @endif >
                                @if ($errors->has('temperatura'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('temperatura') }}</strong>
                                </span>
                                @endif
                            </div>

                            <!--estatura-->
                            <div class="col-md-3{{ $errors->has('estatura') ? ' has-error' : '' }}">
                                <label for="estatura" class="col-md-12 control-label">Estatura (cm)</label>
                                <input id="estatura" min=0 type="number" class="form-control input-sm" name="estatura" value=@if(old('estatura')!='')"{{old('estatura')}}"@elseif($agenda->altura!="")"{{ $agenda->altura }}" @else "{{0}}" @endif onchange="calcular_indice();">
                                @if ($errors->has('estatura'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('estatura') }}</strong>
                                </span>
                                @endif
                            </div>

                            <!--peso-->
                            <div class="col-md-3{{ $errors->has('peso') ? ' has-error' : '' }}">
                                <label for="peso" class="col-md-12 control-label">Peso (Kg)</label>
                                <input id="peso" min=0 type="number" step="any" class="form-control input-sm" name="peso" value=@if(old('peso')!='')"{{old('peso')}}"@elseif($agenda->peso!="")"{{ $agenda->peso }}" @else "{{0}}" @endif onchange="calcular_indice();">
                                @if ($errors->has('peso'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('peso') }}</strong>
                                </span>
                                @endif
                            </div>

                            <!--peso ideal-->
                            <div class="col-md-3">
                                <label for="peso_ideal" class="control-label">Peso Ideal (kg)</label>
                                <input class="form-control input-sm" id="peso_ideal" name="peso_ideal" disabled >
                            </div>

                            <div class="col-md-3">
                                <label for="gct" class="control-label">% GCT RECOMENDADO</label>
                                <input class="form-control input-sm" id="gct" name="gct" disabled >
                            </div>

                            <div class="col-md-3">
                                <label for="imc" class="control-label">IMC</label>
                                <input class="form-control input-sm" id="imc" name="imc" disabled >
                            </div>

                            <div class="col-md-3">
                                <label for="cimc" class="control-label">Categoria IMC</label>
                                <input class="form-control input-sm" id="cimc" name="cimc" disabled >
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

        calcular_indice();

        /*$(".breadcrumb").append('<li><a href="{{ route('agenda.agenda2') }}"></i> Agenda</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.detalle',['id' => $agenda->id]) }}"></i> Detalle</a></li>');
        $(".breadcrumb").append('<li><a href="{{ route('agenda.detalle2',['id' => $agenda->id]) }}"></i> Historia</a></li>');*/
        $(".breadcrumb").append('<li class="active">Preparacion</li>');

        $('#favoritesModal2').on('hidden.bs.modal', function(){
            $(this).removeData('bs.modal');
        });
        $('#favoritesModal').on('hidden.bs.modal', function(){
            $(this).removeData('bs.modal');
        });

        var edad;
        edad = calcularEdad('<?php echo $agenda->fecha_nacimiento; ?>')+ "años";

        $('#edad').text( edad );

        /*$("#hijos_vivos2").val(document.getElementById("hijos_vivos").value);
        $("#hijos_muertos2").val(document.getElementById("hijos_muertos").value);
        $("#gruposanguineo2").val(document.getElementById("gruposanguineo").value);
        $("#transfusion2").val(document.getElementById("transfusion").value);
        $("#alcohol2").val(document.getElementById("alcohol").value);
        $("#alergias2").val(document.getElementById("alergias").value);
        $("#vacuna2").val(document.getElementById("vacuna").value);
        $("#antecedentes_pat2").val(document.getElementById("antecedentes_pat").value);
        $("#antecedentes_fam2").val(document.getElementById("antecedentes_fam").value);
        $("#antecedentes_quir2").val(document.getElementById("antecedentes_quir").value);*/

        $('#example2').DataTable({
            'paging'      : false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : true,
            'info'        : false,
            'autoWidth'   : false
        });


    });




    function calcular_indice(){
        var peso =  document.getElementById('peso').value;
        var estatura = document.getElementById('estatura').value;
        var sexo = @if($agenda->sexo == 1){{$agenda->sexo}}@else{{"0"}}@endif;
        var edad = calcularEdad('{{$agenda->fecha_nacimiento}}');
        //alert(edad);
        estatura2 = Math.pow((estatura/100), 2);
        peso_ideal = 21.45 * (estatura2);
        imc = peso/estatura2;
        gct = ((1.2 * imc) + (0.23 * edad) - (10.8 * sexo) - 5.4);
        var texto = "";
        if(imc < 16){
            texto = "Desnutrición";
        }
        else if(imc < 18){
            texto = "Bajo de Peso";
        }
        else if(imc < 25){
            texto = "Normal";
        }
        else if(imc < 27){
            texto = "Sobrepeso";
        }
        else if(imc < 30){
            texto = "Obesidad Tipo 1";
        }
        else if(imc < 40){
            texto = "Obesidad Clinica";
        }
        else{
            texto = "Obesidad Mordida";
        }
        $('#cimc').val(texto);
        $('#gct').val(gct.toFixed(2));
        $('#imc').val(imc.toFixed(2));
        $('#peso_ideal').val(peso_ideal.toFixed(2));
    }

</script>

@include('sweet::alert')
 @endsection