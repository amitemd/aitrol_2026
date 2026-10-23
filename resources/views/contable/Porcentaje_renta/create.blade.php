@extends('contable.Porcentaje_renta.base')
@section('action-content')

<style type="text/css">
    .separator {
        width: 100%;
        height: 30px;
        clear: both;
    }
</style>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="{{ route('Porcentaje.index') }}">Porcentaje RENTA</a></li>
            <li class="breadcrumb-item active" aria-current="page">Crear</li>
        </ol>
    </nav>
    <form id="enviar_porcentaje_renta" class="form-vertical" role="form" method="POST" action="{{ route('Porcentaje.guardar') }}">
        {{ csrf_field() }}
        <div class="box">
            <div class="box-header color_cab">
                <div class="col-md-9">
                    <h5><b>CREAR PORCENTAJE RENTA</b></h5>
                </div>
                <div class="col-md-1 text-right">
                    <button onclick="goBack()" class="btn btn-primary btn-gray">
                        <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                    </button>
                </div>
            </div>
            <div class="separator"></div>
            <div class="box-body dobra p-5">
                <!--Porcentaje IR-->
                <div class="form-group  col-xs-6">
                    <label for="porcentaje" class="col-md-4 texto">Porcentaje RENTA:</label>
                    <div class="col-md-7">
                        <input id="porcentaje" name="porcentaje" type="text" minlength="1" maxlength="3" class="form-control" validar_porcentaje() onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onblur="blurFunction()" placeholder="Porcentaje RENTA" autocomplete="off" required autofocus>
                    </div>
                    <label for="porcentaje" class="col-md-1 texto">%</label>
                </div>
                <!--Año-->
                <div class="form-group  col-xs-6">
                    <label for="anio_porcentaje_r" class="col-md-4 texto">Año:</label>
                    <div class="col-md-7">
                        <input id="anio_porcentaje_r" minlength="4" name="anio_porcentaje_r" type="text" maxlength="4" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" placeholder="año" autocomplete="off" required autofocus>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>TIPO DE REGIMEN: </label>
                        <div class="col-md-10">
                            <select class="form-control" name="regimen" id="si_no">
                                <option value=''>Seleccione</option>
                                <option value="0">Rimpe popular</option>
                                <option value="1">Regimen general</option>
                                <option value="2">Rimpe emprendedor</option>
                            </select>
                        </div>
                    </div>
                </div>
                <br>
                <!--ESTADO Porcentaje IR-->
                <div class="form-group col-md-6">
                    <br>
                    <!--BUTTON AGREGAR Porcentaje IR-->
                    <div class="form-group col-xs-10 text-center">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-success btn-gray btn_add">
                                <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Agregar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    </form>
</section>
<script type="text/javascript">
    //Valida que solo ingrese numeros
    function check(e) {
        tecla = (document.all) ? e.keyCode : e.which;
        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }
        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }
    //Retorna a la pagina anterior
    function goBack() {
        window.history.back();
    }
</script>
@endsection