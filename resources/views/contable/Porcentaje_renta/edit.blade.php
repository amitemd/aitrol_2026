@extends('contable.Porcentaje_renta.base')
@section('action-content')
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css">
<style type="text/css">
    .separator {
        width: 100%;
        height: 30px;
        clear: both;

    }

    .ui-datepicker-calendar {
        display: none;
    }
</style>
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
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="{{ route('Porcentaje.index') }}">Porcentaje IR</a></li>
            <li class="breadcrumb-item active" aria-current="page">Actualizar</li>
        </ol>
    </nav>
    <form class="form-vertical" role="form" method="POST" action="{{ route('Porcentaje.actualizar') }}">
        {{ csrf_field() }}
        <input name="id_porcentaje_r" id="id_porcentaje_r" type="text" class="hidden" value="@if (!is_null($porcentaje_r)){{ $porcentaje_r->id }}@endif">
        <div class="box">
            <div class="box-header color_cab">
                <div class="col-md-9">
                    <h5><b>DETALLE PORCENTAJE RENTA</b></h5>
                </div>
                <div class="col-md-1 text-right">
                    <button onclick="goBack()" class="btn btn-default btn-gray">
                        <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                    </button>
                </div>
            </div>
            <div class="separator"></div>
            <div class="box-body dobra">
                <!--Porcentaje IR-->
                <div class="form-group  col-xs-6">
                    <label for="porcentaje" class="col-md-4 texto">Porcentaje RENTA:</label>
                    <div class="col-md-4">
                        <input id="porcentaje" name="porcentaje" type="number" class="form-control" value="@if (!is_null($porcentaje_r)){{ $porcentaje_r->porcentaje }}@endif" autocomplete="off" required autofocus>
                    </div>
                </div>
                <!--CODIGO-->
                <div class="form-group  col-xs-3">
                    <label for="anio_porcentaje_r" class="col-md-4 texto">Año:</label>
                    <div class="col-md-7">
                        <input id="anio_porcentaje_r" name="anio_porcentaje_r" type="text" maxlength="4" minlength="4" class="form-control date-picker " onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="@if (!is_null($porcentaje_r)){{ $porcentaje_r->anio }}@endif" autocomplete="off" required autofocus>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>TIPO DE REGIMEN: </label>
                        <select class="form-control" name="regimen" id="si_no">
                            <option value=''>Seleccione</option>
                            <option @if($porcentaje_r->regimen_especial == 0) selected @endif value="0">Rimpe popular</option>
                            <option @if($porcentaje_r->regimen_especial == 1) selected @endif value="1">Regimen general</option>
                            <option @if($porcentaje_r->regimen_especial == 2) selected @endif value="2">Rimpe emprendedor</option>
                        </select>
                    </div>
                </div>
                <!--BUTTON AGREGAR Porcentaje IR-->
                <div class="form-group col-xs-10 text-center">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" id="btn_add" class="btn btn-default btn-gray btn_add">
                            <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Actualizar
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