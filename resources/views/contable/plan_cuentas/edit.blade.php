@extends('contable.configuracion.base')
@section('action-content')

<section class="content">
    <div class="box " style="background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
                <h3 class="box-title">Editar Configuracion del Sistema</h3>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #ffffff;">
            <form class="form-vertical" role="form" method="POST" action="{{ route('configuraciones.guardar') }}">
                <div class="box-body col-xs-24">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="id" value="{{$configuracion->id}}">
                    <h3>Cuenta: {{$configuracion->nombre}}</h3>
                    <div class="form-group col-md-12">
                        <div class="col-md-12" id="id_cuenta">
                            <label for="id_plan" class="col-md-4 control-label" >Seleccione una Cuenta:</label>
                            <div class="col-md-8">
                                <select id="id_plan" name="id_plan" class="form-control select2_cuentas" style="width: 100%;" >
                                    @foreach($cuentas as $value)
                                        <option {{$configuracion->id_plan == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        @if(($configuracion->id_plan) == "4.1.01.02")
                            <div class="col-md-12" id="iva">
                                <label for="iva" class="col-md-4 control-label">Iva:</label>
                                <div class="col-md-8">
                                     <input id="iva" name="iva" type="text" class="form-control" value="@if(!is_null($configuracion)){{$configuracion->iva}}@endif">
                                </div>
                            </div>
                        @endif
                    </div>
                    <br><br>
                    <!--Button Actualizar-->
                    <div class="form-group col-xs-10" style="text-align: center;">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                            Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

<script type="text/javascript">

    $(document).ready(function(){
        $('.select2_cuentas').select2({
            tags: false
          });

    });

    function validarRango(elemento){
        var numero = parseInt(elemento.value,10);
          //Validamos que se cumpla el rango
        if(numero<1 ){
            alert("La cantidad debe ser mayor o igual a 1");
            $('#minimo').val("1");
            return false;
        }
        return true;
    }


    function validarRango_uso(elemento){
        var numero = parseInt(elemento.value,10);
          //Validamos que se cumpla el rango
        if(numero<1 ){
            alert("La cantidad debe ser mayor o igual a 1");
            $('#uso').val("1");
            return false;
        }
        return true;
    }

    function goBack() {
      window.history.back();
    }
</script>

</section>
@endsection
