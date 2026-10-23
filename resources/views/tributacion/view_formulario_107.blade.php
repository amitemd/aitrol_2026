
@extends('tributacion.base')
@section('action-content')
<style>
    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 20px;
        z-index: 999999 !important;
        z-index: 999999999 !important;
        z-index: 99999999999999 !important;
        position: absolute;
        top: 0px;
        left: 0px;
        float: left;
        display: block;
        min-width: 160px;
        padding: 4px 0;
        margin: 0 0 10px 25px;
        list-style: none;
        background-color: #ffffff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
    }

    table {
        width: 100%;
    }

    @media print {
        .hidden-print {
            display: none !important;
        }
    }
</style>
<?php
$datosFormulario = '';
$periodo = '';
if (isset($info) && $info != '') {
    $datosFormulario = json_decode($info->datos);
}

?>
<div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="content">

        </div>
    </div>
</div>
<div class="modal fade" id="modalpedido" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"></div>
    </div>
</div>
<section class="content">
    <nav aria-label="breadcrumb" class="hidden-print">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Tributación</a></li>
            <li class="breadcrumb-item"><a href="#">Formulario 107</a></li>
        </ol>
    </nav>
    <div class="row" id="imprimible" style="background-color: white;">
        <form id="frm_" role="form" method="post" action="saveForm107">
            {{ csrf_field() }}
            <div class="col-lg-12" id="contenido">
                <table align="center" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="background-color: #CCCCFF;">
                            <table>
                                <tr>
                                    <td><img src="{{asset('/')}}/images/sri/logosri.png"></td>
                                </tr>
                                <tr>
                                    <td style="text-align: center;">Formulario 107</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td colspan="2" style="width: 100%;background-color: #CCCCFF;">
                                        <table>
                                            <tr>
                                                <td style="width: 100%;">
                                                    <table style="width: 100%;">
                                                        <tr>
                                                            <td style="width: 60%;text-align: center;padding: 0px 15px 0px 15px;">COMPROBANTE DE RETENCIONES EN LA FUENTE DEL IMPUESTO A LA RENTA POR INGRESOS DEL TRABAJO EN RELACIÓN DE DEPENDENCIA</td>
                                                            <td style="width: 10%;">No.</td>
                                                            <td style="width: 30%;"><input type="text" id="numero" name="numero" class="form-control" style="text-align: right;" onkeypress="return valideKey(event);" maxlength="20" readonly value="<?= isset($info) ? str_pad($info->num_formulario, 10, 0, STR_PAD_LEFT) : ''; ?>" /></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 35%;">
                                        <table>
                                            <tr>
                                                <td style="padding: 0px 15px 0px 15px;">Ejercicio fiscal</td>
                                                <td style="padding: 20px 5px 20px 5px;background-color: #CCCCFF;" align="center">102</td>
                                                <td style="text-align: center;">
                                                    <table style="width: 100%;text-align: center;">
                                                        <tr>
                                                            <td style="padding: 0px 0px 0px 0px;"><input type="text" id="periodo1" name="periodo1" class="form-control" style="width: 100%;text-align: center;" onkeypress="return valideKey(event);" maxlength="1" value="<?= isset($info) ? $datosFormulario->periodo1 : ''; ?>" /></td>
                                                            <td style="padding: 0px 0px 0px 0px;"><input type="text" id="periodo2" name="periodo2" class="form-control" style="width: 100%;text-align: center;" onkeypress="return valideKey(event);" maxlength="1" value="<?= isset($info) ?  $datosFormulario->periodo2 : ''; ?>" /></td>
                                                            <td style="padding: 0px 0px 0px 0px;"><input type="text" id="periodo3" name="periodo3" class="form-control" style="width: 100%;text-align: center;" onkeypress="return valideKey(event);" maxlength="1" value="<?= isset($info) ?  $datosFormulario->periodo3 : ''; ?>" /></td>
                                                            <td style="padding: 0px 0px 0px 0px;"><input type="text" id="periodo4" name="periodo4" class="form-control" style="width: 100%;text-align: center;" onkeypress="return valideKey(event);" maxlength="1" value="<?= isset($info) ?  $datosFormulario->periodo4 : ''; ?>" /></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="width: 65%;">
                                        <table>
                                            <tr>
                                                <td style="padding: 0px 15px 0px 15px;">Fecha de entrega</td>
                                                <td style="padding: 0px 5px 0px 5px;background-color: #CCCCFF;" align="center">103</td>
                                                <td>
                                                    <table>
                                                        <tr>
                                                            <td style="padding: 0px 0px 0px 0px;border: 1px solid gray;" align="center">Año</td>
                                                            <td style="padding: 0px 0px 0px 0px;border: 1px solid gray;" align="center">Mes</td>
                                                            <td style="padding: 0px 0px 0px 0px;border: 1px solid gray;" align="center">Día</td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <table cellspacing="0" cellpadding="0">
                                                                    <tr>
                                                                        <td style="padding: 0px 0px 0px 0px;"><input type="text" id="anio1" name="anio1" class="form-control" style="width: 100%;text-align: center;" onkeypress="return valideKey(event);" maxlength="1" value="<?= isset($info) ? $datosFormulario->anio1 : ''; ?>" /></td>
                                                                        <td style="padding: 0px 0px 0px 0px;"><input type="text" id="anio2" name="anio2" class="form-control" style="width: 100%;text-align: center;" onkeypress="return valideKey(event);" maxlength="1" value="<?= isset($info) ?  $datosFormulario->anio2 : ''; ?>" /></td>
                                                                        <td style="padding: 0px 0px 0px 0px;"><input type="text" id="anio3" name="anio3" class="form-control" style="width: 100%;text-align: center;" onkeypress="return valideKey(event);" maxlength="1" value="<?= isset($info) ? $datosFormulario->anio3 : ''; ?>" /></td>
                                                                        <td style="padding: 0px 0px 0px 0px;"><input type="text" id="anio4" name="anio4" class="form-control" style="width: 100%;text-align: center;" onkeypress="return valideKey(event);" maxlength="1" value="<?= isset($info) ?  $datosFormulario->anio4 : ''; ?>" /></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td>
                                                                <table cellspacing="0" cellpadding="0">
                                                                    <tr>
                                                                        <td style="padding: 0px 0px 0px 0px;"><input type="text" id="mes1" name="mes1" class="form-control" style="width: 100%;text-align: center;" onkeypress="return valideKey(event);" maxlength="1" value="<?= isset($info) ?  $datosFormulario->mes1 : ''; ?>" /></td>
                                                                        <td style="padding: 0px 0px 0px 0px;"><input type="text" id="mes2" name="mes2" class="form-control" style="width: 100%;text-align: center;" onkeypress="return valideKey(event);" maxlength="1" value="<?= isset($info) ?  $datosFormulario->mes2 : ''; ?>" /></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td>
                                                                <table cellspacing="0" cellpadding="0">
                                                                    <tr>
                                                                        <td style="padding: 0px 0px 0px 0px;"><input type="text" id="dia1" name="dia1" class="form-control" style="width: 100%;text-align: center;" onkeypress="return valideKey(event);" maxlength="1" value="<?= isset($info) ? $datosFormulario->dia1 : ''; ?>" /></td>
                                                                        <td style="padding: 0px 0px 0px 0px;"><input type="text" id="dia2" name="dia2" class="form-control" style="width: 100%;text-align: center;" onkeypress="return valideKey(event);" maxlength="1" value="<?= isset($info) ? $datosFormulario->dia2 : ''; ?>" /></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table style="width: 100%;">
                                <tr>
                                    <td colspan="4" style="background-color: #CCCCFF;">100 Identificación del Empleador (Agente de Retención)</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #CCCCFF;text-align: center;">105</td>
                                    <td style="width: 45%;">
                                        <table style="width: 100%;">
                                            <tr>
                                                <td>RUC</td>
                                            </tr>
                                            <tr>
                                                <?php
                                                $ructotal = strlen($empresa->id);
                                                ?>
                                                <td><input type="text" id="ruc1" name="ruc1" class="form-control" value="<?= $empresa->id[0]; ?>" style="width: 40px;" onkeyup="nextFocus('ruc1','ruc2',this.value);" onkeypress="return valideKey(event);" readonly /></td>
                                                <td><input type="text" id="ruc2" name="ruc2" class="form-control" value="<?= $empresa->id[1]; ?>" style="width: 40px;" onkeyup="nextFocus('ruc2','ruc3',this.value);" onkeypress="return valideKey(event);" readonly /></td>
                                                <td><input type="text" id="ruc3" name="ruc3" class="form-control" value="<?= $empresa->id[2]; ?>" style="width: 40px;" onkeyup="nextFocus('ruc3','ruc4',this.value);" onkeypress="return valideKey(event);" readonly /></td>
                                                <td><input type="text" id="ruc4" name="ruc4" class="form-control" value="<?= $empresa->id[3]; ?>" style="width: 40px;" onkeyup="nextFocus('ruc4','ruc5',this.value);" onkeypress="return valideKey(event);" readonly /></td>
                                                <td><input type="text" id="ruc5" name="ruc5" class="form-control" value="<?= $empresa->id[4]; ?>" style="width: 40px;" onkeyup="nextFocus('ruc5','ruc6',this.value);" onkeypress="return valideKey(event);" readonly /></td>
                                                <td><input type="text" id="ruc6" name="ruc6" class="form-control" value="<?= $empresa->id[5]; ?>" style="width: 40px;" onkeyup="nextFocus('ruc6','ruc7',this.value);" onkeypress="return valideKey(event);" readonly /></td>
                                                <td><input type="text" id="ruc7" name="ruc7" class="form-control" value="<?= $empresa->id[6]; ?>" style="width: 40px;" onkeyup="nextFocus('ruc7','ruc8',this.value);" onkeypress="return valideKey(event);" readonly /></td>
                                                <td><input type="text" id="ruc8" name="ruc8" class="form-control" value="<?= $empresa->id[7]; ?>" style="width: 40px;" onkeyup="nextFocus('ruc8','ruc9',this.value);" onkeypress="return valideKey(event);" readonly /></td>
                                                <td><input type="text" id="ruc9" name="ruc9" class="form-control" value="<?= $empresa->id[8]; ?>" style="width: 40px;" onkeyup="nextFocus('ruc9','ruc10',this.value);" onkeypress="return valideKey(event);" readonly /></td>
                                                <td><input type="text" id="ruc10" name="ruc10" class="form-control" value="<?= $empresa->id[9]; ?>" style="width: 40px;" onkeyup="nextFocus('ruc10','ruc11',this.value);" onkeypress="return valideKey(event);" readonly /></td>
                                                <td><input type="text" id="ruc11" name="ruc11" class="form-control" value="<?= $empresa->id[10]; ?>" style="width: 40px;" onkeyup="nextFocus('ruc11','ruc12',this.value);" onkeypress="return valideKey(event);" readonly /></td>
                                                <td><input type="text" id="ruc12" name="ruc12" class="form-control" value="<?= $empresa->id[11]; ?>" style="width: 40px;" onkeyup="nextFocus('ruc12','ruc13',this.value);" onkeypress="return valideKey(event);" readonly /></td>
                                                <td><input type="text" id="ruc13" name="ruc13" class="form-control" value="<?= $empresa->id[12]; ?>" style="width: 40px;" onkeyup="nextFocus('ruc13','razonsocial',this.value);" onkeypress="return valideKey(event);" readonly /></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="background-color: #CCCCFF;text-align: center;">106</td>
                                    <td style="width: 45%;text-align: left;">
                                        <table style="width: 100%;">
                                            <tr>
                                                <td>RAZÓN SOCIAL O APELLIDOS Y NOMBRES COMPLETOS</td>
                                            </tr>
                                            <tr>
                                                <td><input type="text" id="razonsocial" name="razonsocial" value="<?= $empresa->nombrecomercial; ?>" class="form-control" onkeyup="nextFocusSinLimite('razonsocial','cedula',this.value);cambiaMayuscula(this);" readonly value="<?= isset($info) ? $datosFormulario->razonsocial : ''; ?>" /></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table style="width: 100%;">
                                <tr>
                                    <td colspan="4" style="background-color: #CCCCFF;">200 Identificación del Trabajador (Contribuyente)</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #CCCCFF;text-align: center;">201</td>
                                    <td style="width: 45%;">
                                        <table style="width: 100%;">
                                            <tr>
                                                <td>CÉDULA O PASAPORTE</td>
                                            </tr>
                                            <tr>
                                                <td><input type="text" id="cedula" name="cedula" class="form-control" autocomplete="off" onkeyup="nextFocusSinLimite('cedula','nombre',this.value);" value="<?= isset($info) ? $datosFormulario->cedula : ''; ?>" /></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="background-color: #CCCCFF;text-align: center;">202</td>
                                    <td style="width: 45%;text-align: left;">
                                        <table style="width: 100%;">
                                            <tr>
                                                <td colspan="2">APELLIDOS Y NOMBRES COMPLETOS</td>
                                            </tr>
                                            <tr>
                                                <td><input type="text" id="apellido" name="apellido" class="form-control" onkeyup="cambiaMayuscula(this);" value="<?= isset($info) ? $datosFormulario->apellido : ''; ?>" /></td>
                                                <td><input type="text" id="nombre" name="nombre" class="form-control" onkeyup="cambiaMayuscula(this);" value="<?= isset($info) ? $datosFormulario->nombre : ''; ?>" /></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="background-color: #CCCCFF;border: 1px solid gray;">Liquidación de Impuesto</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">SUELDOS Y SALARIOS</td>
                                    <td style="background-color: #CCCCFF;text-align: center;width: 2.5%;border: 1px solid gray;">301</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">+</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="sueldo" name="sueldo" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('sueldo','sobresueldos',this.value);" value="<?= isset($info) ? $datosFormulario->sueldo : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">SOBRESUELDOS, COMISIONES, BONOS Y OTROS INGRESOS GRAVADOS</td>
                                    <td style="background-color: #CCCCFF;text-align: center;width: 2.5%;border: 1px solid gray;">303</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">+</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="sobresueldos" name="sobresueldos" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('sobresueldos','participacion',this.value);" value="<?= isset($info) ? $datosFormulario->sobresueldos : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">PARTICIPACIÓN UTILIDADES</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">305</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">+</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="participacion" name="participacion" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('participacion','ingresootroempleador',this.value);" value="<?= isset($info) ? $datosFormulario->participacion : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">INGRESOS GRAVADOS GENERADOS CON OTROS EMPLEADORES</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">307</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">+</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="ingresootroempleador" name="ingresootroempleador" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('ingresootroempleador','decimotercer',this.value);" value="<?= isset($info) ? $datosFormulario->ingresootroempleador : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">DÉCIMO TERCER SUELDO</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">311</td>
                                    <td style="background-color: #CCCCFF;width: 2.5%;border: 1px solid gray;">&nbsp;</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="decimotercer" name="decimotercer" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('decimotercer','decimocuarto',this.value);" value="<?= isset($info) ? $datosFormulario->decimotercer : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">DÉCIMO CUARTO SUELDO</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">313</td>
                                    <td style="background-color: #CCCCFF;width: 2.5%;border: 1px solid gray;">&nbsp;</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="decimocuarto" name="decimocuarto" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('decimocuarto','fondoreserva',this.value);" value="<?= isset($info) ? $datosFormulario->decimocuarto : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">FONDO DE RESERVA</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">315</td>
                                    <td style="background-color: #CCCCFF;width: 2.5%;border: 1px solid gray;">&nbsp;</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="fondoreserva" name="fondoreserva" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('fondoreserva','otrosingresos',this.value);" value="<?= isset($info) ? $datosFormulario->fondoreserva : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">OTROS INGRESOS EN RELACIÓN DE DEPENDENCIA QUE NO CONSTITUYEN RENTA GRAVADA </td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">317</td>
                                    <td style="background-color: #CCCCFF;width: 2.5%;border: 1px solid gray;">&nbsp;</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="otrosingresos" name="otrosingresos" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('otrosingresos','aportepersonaleste',this.value);" value="<?= isset($info) ? $datosFormulario->otrosingresos : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">(-) APORTE PERSONAL IESS CON ESTE EMPLEADOR (únicamente pagado por el trabajador)</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">351</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">-</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="aportepersonaleste" name="aportepersonaleste" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('aportepersonaleste','aportepatronalotro',this.value);" value="<?= isset($info) ? $datosFormulario->aportepersonaleste : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">(-) APORTE PERSONAL IESS CON OTROS EMPLEADORES (únicamente pagado por el trabajador)</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">353</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">-</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="aportepatronalotro" name="aportepatronalotro" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('aportepatronalotro','vivienda',this.value);" value="<?= isset($info) ? $datosFormulario->aportepatronalotro : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">(-) DEDUCCIÓN GASTOS PERSONALES - VIVIENDA</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">361</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">-</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="vivienda" name="vivienda" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('vivienda','turismo',this.value);" value="<?= isset($info) ? $datosFormulario->vivienda : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">(-) DEDUCCIÓN GASTOS PERSONALES - TURISMO</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">362</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">-</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="turismo" name="turismo" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('turismo','salud',this.value);" value="<?= isset($info) ? $datosFormulario->turismo : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">(-) DEDUCCIÓN GASTOS PERSONALES - SALUD</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">363</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">-</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="salud" name="salud" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('salud','educacion',this.value);" value="<?= isset($info) ? $datosFormulario->salud : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">(-) DEDUCCIÓN GASTOS PERSONALES - EDUCACIÓN, ARTE Y CULTURA</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">365</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">-</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="educacion" name="educacion" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('educacion','alimentacion',this.value);" value="<?= isset($info) ? $datosFormulario->educacion : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">(-) DEDUCCIÓN GASTOS PERSONALES - ALIMENTACIÓN</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">367</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">-</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="alimentacion" name="alimentacion" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('alimentacion','vestimenta',this.value);" value="<?= isset($info) ? $datosFormulario->alimentacion : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">(-) DEDUCCIÓN GASTOS PERSONALES - VESTIMENTA</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">369</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">-</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="vestimenta" name="vestimenta" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('vestimenta','discapacidad',this.value);" value="<?= isset($info) ? $datosFormulario->vestimenta : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">(-) EXONERACIÓN POR DISCAPACIDAD</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">371</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">-</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="discapacidad" name="discapacidad" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('discapacidad','terceraedad',this.value);" value="<?= isset($info) ? $datosFormulario->discapacidad : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">(-) EXONERACIÓN POR TERCERA EDAD</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">373</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">-</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="terceraedad" name="terceraedad" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('terceraedad','rentaasumida',this.value);" value="<?= isset($info) ? $datosFormulario->terceraedad : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">IMPUESTO A LA RENTA ASUMIDO POR ESTE EMPLEADOR</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">381</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">+</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="rentaasumida" name="rentaasumida" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('rentaasumida','basegrabada',this.value);" value="<?= isset($info) ? $datosFormulario->rentaasumida : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">BASE IMPONIBLE GRAVADA
                                        301+303+305+307-351-353-361-362-363-365-367-369-371-373+381 ≥ 0 </td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">399</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">=</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="basegrabada" name="basegrabada" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('basegrabada','impuestocausado',this.value);" value="<?= isset($info) ? $datosFormulario->basegrabada : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">IMPUESTO A LA RENTA CAUSADO </td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">401</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">=</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="impuestocausado" name="impuestocausado" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('impuestocausado','retencionasumidaotroperiodo',this.value);" value="<?= isset($info) ? $datosFormulario->impuestocausado : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">VALOR DEL IMPUESTO RETENIDO Y ASUMIDO POR OTROS EMPLEADORES DURANTE EL PERÍODO DECLARADO</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">403</td>
                                    <td style="background-color: #CCCCFF;width: 2.5%;border: 1px solid gray;">&nbsp;</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="retencionasumidaotroperiodo" name="retencionasumidaotroperiodo" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('retencionasumidaotroperiodo','impuestoasumido',this.value);" value="<?= isset($info) ? $datosFormulario->retencionasumidaotroperiodo : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">VALOR DEL IMPUESTO ASUMIDO POR ESTE EMPLEADOR</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">405</td>
                                    <td style="background-color: #CCCCFF;width: 2.5%;border: 1px solid gray;">&nbsp;</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="impuestoasumido" name="impuestoasumido" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('impuestoasumido','impuestotrabajador',this.value);" value="<?= isset($info) ? $datosFormulario->impuestoasumido : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">VALOR DEL IMPUESTO RETENIDO AL TRABAJADOR POR ESTE EMPLEADOR</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">407</td>
                                    <td style="background-color: #CCCCFF;width: 2.5%;border: 1px solid gray;">&nbsp;</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="impuestotrabajador" name="impuestotrabajador" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('impuestotrabajador','ingresosgravadoeste',this.value);" value="<?= isset($info) ? $datosFormulario->impuestotrabajador : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 50%;border: 1px solid gray;">INGRESOS GRAVADOS CON ESTE EMPLEADOR (informativo)
                                        301+303+305+381</td>
                                    <td style="background-color: #CCCCFF;text-align: center;border: 1px solid gray;">349</td>
                                    <td style="width: 2.5%;text-align: center;border: 1px solid gray;">=</td>
                                    <td style="width: 45%;border: 1px solid gray;"><input type="text" id="ingresosgravadoeste" name="ingresosgravadoeste" class="form-control input-number" style="text-align: right;" onblur="calcularformulario107();" onkeyup="nextFocusSinLimite('ingresosgravadoeste','impuestotrabajador',this.value);" value="<?= isset($info) ? $datosFormulario->ingresosgravadoeste : '0.00'; ?>" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: 1px solid gray;">
                            1.- El trabajador que, en el mismo período fiscal haya reiniciado su actividad con otro empleador, estará en la obligación de entregar el formulario 107 entregado por su anterior empleador a su nuevo empleador, para que aquel, efectúe el cálculo de las retenciones a realizarse en lo que resta del año.
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: 1px solid gray;">
                            2.- El campo 307 deberá ser llenado con la información registrada en el campo 349 del Formulario 107 entregado por el anterior empleador, y/o con la proyección de ingresos de otros empleadores actuales, en caso de que el empleador que registra y entrega el presente formulario haya efectuado la retención por los ingresos percibidos con éstos últimos.
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: 1px solid gray;">
                            3.- La deducción total por gastos personales no deberá superar el 50% del total de ingresos gravados, y en ningún caso será mayor al equivalente a 1.3 veces la fracción básica exenta de Impuesto a la Renta de personas naturales.
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: 1px solid gray;">
                            4.- A partir del año 2011 debe considerarse como cuantía máxima para cada tipo de gasto, el monto equivalente a la fracción básica exenta de Impuesto a la Renta en: vivienda 0.325 veces, educación 0.325 veces, alimentación 0.325 veces, vestimenta 0.325, salud 1.3 veces.
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: 1px solid gray;">
                            5.- El trabajador deberá presentar el Anexo de Gastos Personales que deduzca, de cumplir las condiciones establecidas por el Servicio de Rentas Internas.
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: 1px solid gray;">
                            6.- De conformidad con la Resolución No. NAC-DGER2008-0566 publicada en el Registro Oficial No. 342 el 21 de mayo del 2008, el beneficio de la exoneración por tercera edad se configura a partir del ejercicio en el cual el beneficiario cumpla los 65 años de edad. El monto de la exoneración será el equivalente al doble de la fracción básica exenta de Impuesto a la Renta.
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: 1px solid gray;">
                            7.- A partir del año 2013, conforme lo dispuesto en la Ley Orgánica de Discapacidades el monto de la exoneración por discapacidad será el equivalente al doble de la fracción básica exenta de Impuesto a la Renta.
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: 1px solid gray;">
                            8.- El presente formulario constituye la declaración de Impuesto a la Renta del trabajador, siempre que durante el período declarado la persona únicamente haya prestado sus servicios en relación de dependencia con el empleador que entrega este formulario, y no existan valores de gastos personales que deban ser reliquidados. En caso de pérdida de este documento el trabajador deberá solicitar una copia a su empleador. Por el contrario, el trabajador deberá presentar obligatoriamente su declaración de Impuesto a la Renta cuando haya obtenido rentas en relación de dependencia con dos o más empleadores o haya recibido además de su remuneración ingresos de otras fuentes como por ejemplo: rendimientos financieros, arrendamientos, ingresos por el libre ejercicio profesional, u otros ingresos, los cuales en conjunto superen la fracción básica exenta de Impuesto a la Renta de personas naturales, o cuando tenga que reliquidar gastos personales con aquellos efectivamente incurridos, teniendo presente los límites referidos en las notas 3 y 4 de este documento.
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;border: 1px solid gray;">
                            <b>DECLARO QUE LOS DATOS PROPORCIONADOS EN ESTE DOCUMENTO SON EXACTOS Y VERDADEROS, POR LO QUE ASUMO LA RESPONSABILIDAD LEGAL QUE DE ELLA SE DERIVEN (Art. 101 de la L.R.T.I.)</b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width: 100%;">
                            <table style="width: 100%;" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="text-align: center;border: 1px solid gray;width: 30%;"><b>FIRMA DEL AGENTE DE RETENCIÓN</b></td>
                                    <td style="text-align: center;border: 1px solid gray;width: 30%;"><b>FIRMA DEL TRABAJADOR CONTRIBUYENTE</b></td>
                                    <td style="text-align: center;border: 1px solid gray;width: 40%;"><b>FIRMA DEL CONTADOR</b></td>
                                </tr>
                                <tr>
                                    <td style="text-align: center;border: 1px solid gray;height: 120px;">&nbsp;</td>
                                    <td style="text-align: center;border: 1px solid gray;height: 120px;">&nbsp;</td>
                                    <td style="text-align: center;border: 1px solid gray;height: 120px;">
                                        <table style="width: 100%;" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="text-align: center;height: 120px;">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 100%;border: 1px solid gray;">
                                                    <table style="width: 100%;">
                                                        <tr>
                                                            <td style="width: 50px;background-color: #CCCCFF;">199</td>
                                                            <td>
                                                                <table style="width: 100%;">
                                                                    <tr>
                                                                        <td style="text-align: left;">RUC CONTADOR</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <table>
                                                                                <tr>
                                                                                    <td><input type="text" id="rucc1" name="rucc1" class="form-control" value="<?= $empresa->id_contador[0]; ?>" style="width: 40px;" onkeypress="return valideKey(event);" readonly /></td>
                                                                                    <td><input type="text" id="rucc2" name="rucc2" class="form-control" value="<?= $empresa->id_contador[1]; ?>" style="width: 40px;" onkeypress="return valideKey(event);" readonly /></td>
                                                                                    <td><input type="text" id="rucc3" name="rucc3" class="form-control" value="<?= $empresa->id_contador[2]; ?>" style="width: 40px;" onkeypress="return valideKey(event);" readonly /></td>
                                                                                    <td><input type="text" id="rucc4" name="rucc4" class="form-control" value="<?= $empresa->id_contador[3]; ?>" style="width: 40px;" onkeypress="return valideKey(event);" readonly /></td>
                                                                                    <td><input type="text" id="rucc5" name="rucc5" class="form-control" value="<?= $empresa->id_contador[4]; ?>" style="width: 40px;" onkeypress="return valideKey(event);" readonly /></td>
                                                                                    <td><input type="text" id="rucc6" name="rucc6" class="form-control" value="<?= $empresa->id_contador[5]; ?>" style="width: 40px;" onkeypress="return valideKey(event);" readonly /></td>
                                                                                    <td><input type="text" id="rucc7" name="rucc7" class="form-control" value="<?= $empresa->id_contador[6]; ?>" style="width: 40px;" onkeypress="return valideKey(event);" readonly /></td>
                                                                                    <td><input type="text" id="rucc8" name="rucc8" class="form-control" value="<?= $empresa->id_contador[7]; ?>" style="width: 40px;" onkeypress="return valideKey(event);" readonly /></td>
                                                                                    <td><input type="text" id="rucc9" name="rucc9" class="form-control" value="<?= $empresa->id_contador[8]; ?>" style="width: 40px;" onkeypress="return valideKey(event);" readonly /></td>
                                                                                    <td><input type="text" id="rucc10" name="rucc10" class="form-control" value="<?= $empresa->id_contador[9]; ?>" style="width: 40px;" onkeypress="return valideKey(event);" readonly /></td>
                                                                                    <td><input type="text" id="rucc11" name="rucc11" class="form-control" value="0" style="width: 40px;" onkeypress="return valideKey(event);" readonly /></td>
                                                                                    <td><input type="text" id="rucc12" name="rucc12" class="form-control" value="0" style="width: 40px;" onkeypress="return valideKey(event);" readonly /></td>
                                                                                    <td><input type="text" id="rucc13" name="rucc13" class="form-control" value="1" style="width: 40px;" onkeypress="return valideKey(event);" readonly /></td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
        <br />
        <div align="center">
            <button class="hidden-print btn btn-success" id="btnGuardar"><i class="fa fa-save"></i> Guardar</button>
            <button class="hidden-print btn btn-info" id="btnImprimir"><i class="fa fa-print"></i> Imprimir</button>
            <button class="hidden-print btn btn-danger" onclick="window.history.back();" id="btnCancelar"><i class="fa fa-arrow-left"></i> Regresar</button>
            &nbsp;
            <button class="hidden-print btn btn-danger" onclick="window.history.back();" id="btnAnular"><i class="fa fa-close"></i> Anular</button>
        </div>
    </div>
</section>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    window.onload = function() {
        document.querySelector(".sidebar-toggle").click();
    }
    $(document).ready(function() {
        $('#btnImprimir').click(function() {
            window.print();
        });
        $('#btnAnular').click(function() {
            $.ajax({
                url: "anularFormulario107",
                dataType: "json",
                data: {
                    num: $('#numero').val(),
                },
                success: function(json) {
                    if (json == ok)
                        window.open('formularios_107');
                }
            });
        });
        $("#cedula").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "buscar_empreados_107",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                       response(data);
                    }
                });
            },
            minLength: 1,
            select: function(data, ui) {
                var periodo = $('#periodo1').val() + $('#periodo2').val() + $('#periodo3').val() + $('#periodo4').val();
                if (periodo.length == 4) {
                    var nombre = ui.item.value.split(' | ');
                    $('#nombre').val(nombre[1]);
                    $('#cedula').val(ui.item.id);
                    $.ajax({
                        url: 'obtenerDatos',
                        type: 'post',
                        data: {
                            _token: $('input[name=_token]').val(),
                            cedula: ui.item.id,
                            anio: periodo
                        },
                        dataType: 'json',
                        success: function(json) {
                            $('#sueldo').val(parseFloat(json.sueldo).toFixed(2));
                            $('#sobresueldos').val(parseFloat(json.sobreSueldo).toFixed(2));
                            $('#decimotercer').val(parseFloat(json.decimoTercero).toFixed(2));
                            $('#decimocuarto').val(parseFloat(json.decimoCuarto).toFixed(2));
                            $('#fondoreserva').val(parseFloat(json.fondoReserva).toFixed(2));
                            $('#aportepersonaleste').val(parseFloat(json.aportePersonal).toFixed(2));
                            calcularformulario107();
                        }
                    });
                    return false;
                } else {
                    alert('Ingrese el periodo a declarar.');
                    $('#nombre').val('');
                    $('#cedula').val('');
                    $('#periodo').focus();
                    return;
                }
            }
        });
        $('#btnGuardar').click(function() {
            var periodo = $('#periodo1').val() + $('#periodo2').val() + $('#periodo3').val() + $('#periodo4').val();
            var anioEntrega = $('#anio1').val() + $('#anio2').val() + $('#anio3').val() + $('#anio4').val();
            var mesEntrega = $('#mes1').val() + $('#mes2').val();
            var diaEntrega = $('#dia1').val() + $('#dia2').val();
            if (periodo <= 2021) {
                alert('El periodo debe ser mayor al 2021');
                return;
            } else if (anioEntrega != '' && anioEntrega.length != 4) {
                alert('La fecha de entrega tiene un formato incorrecto');
                return;
            } else if (mesEntrega != '' && mesEntrega.length != 2) {
                alert('La fecha de entrega tiene un formato incorrecto');
                return;
            } else if (diaEntrega != '' && diaEntrega.length != 2) {
                alert('La fecha de entrega tiene un formato incorrecto');
                return;
            } else if ($('#cedula').val() == '') {
                alert('Ingrese la cedula del trabajador');
                return;
            } else {
                $('#frm_').submit();
            }
        });
    });

    function calcularformulario107() {
        var sueldo = $('#sueldo').val();
        var sobresueldos = $('#sobresueldos').val();
        var participacion = $('#participacion').val();
        var ingresootroempleador = $('#ingresootroempleador').val();

        var rentaasumida = $('#rentaasumida').val();

        var aportepersonaleste = $('#aportepersonaleste').val();
        var aportepatronalotro = $('#aportepatronalotro').val();
        var vivienda = $('#vivienda').val();
        var turismo = $('#turismo').val();
        var salud = $('#salud').val();
        var educacion = $('#educacion').val();
        var alimentacion = $('#alimentacion').val();
        var vestimenta = $('#vestimenta').val();
        var discapacidad = $('#discapacidad').val();
        var terceraedad = $('#terceraedad').val();

        var basegrabada = (parseFloat(sueldo) + parseFloat(sobresueldos) + parseFloat(participacion) + parseFloat(ingresootroempleador) + parseFloat(rentaasumida)) - (parseFloat(aportepersonaleste) + parseFloat(aportepatronalotro) + parseFloat(vivienda) + parseFloat(turismo) + parseFloat(salud) + parseFloat(educacion) + parseFloat(alimentacion) + parseFloat(vestimenta) + parseFloat(discapacidad) + parseFloat(terceraedad));
        $('#basegrabada').val(parseFloat(basegrabada).toFixed(2));

        var ingresosgravadoeste = parseFloat(sueldo) + parseFloat(sobresueldos) + parseFloat(participacion) + parseFloat(ingresootroempleador) + parseFloat(rentaasumida);
        $('#ingresosgravadoeste').val(parseFloat(ingresosgravadoeste).toFixed(2));

        calcularImpuestoCausado(basegrabada);
    }

    function calcularImpuestoCausado(baseGrabada) {
        var fraccionBasica = 0;
        var impuestoFraccionExcedente = 0;
        var impuestoCausado = 0;
        var excedente = 0;
        baseGrabada = parseFloat(baseGrabada).toFixed(2);
        if (baseGrabada > 0) {
            var periodo = $('#periodo1').val() + $('#periodo2').val() + $('#periodo3').val() + $('#periodo4').val();
            if (periodo == 2022) {
                if (baseGrabada >= 0 && baseGrabada <= 11310.00) {
                    impuestoCausado = 0;
                } else if (baseGrabada >= 11310.01 && baseGrabada <= 14410.00) {
                    impuestoCausado = 0;
                    excedente = ((baseGrabada - 11310.01) * 5) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado);
                } else if (baseGrabada >= 14410.01 && baseGrabada <= 18010.00) {
                    fraccionBasica = 155.00;
                    excedente = ((baseGrabada - 14410.01) * 10) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado) + parseFloat(fraccionBasica);
                } else if (baseGrabada >= 18010.01 && baseGrabada <= 21630.00) {
                    fraccionBasica = 515.00;
                    excedente = ((baseGrabada - 18010.01) * 12) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado) + parseFloat(fraccionBasica);
                } else if (baseGrabada >= 21630.01 && baseGrabada <= 31630.00) {
                    fraccionBasica = 949.40;
                    excedente = ((baseGrabada - 21630.01) * 15) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado) + parseFloat(fraccionBasica);
                } else if (baseGrabada >= 31630.01 && baseGrabada <= 41630.00) {
                    fraccionBasica = 2449.40;
                    excedente = ((baseGrabada - 31630.01) * 20) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado) + parseFloat(fraccionBasica);
                } else if (baseGrabada >= 41630.01 && baseGrabada <= 51630.00) {
                    fraccionBasica = 4449.40;
                    excedente = ((baseGrabada - 41630.01) * 25) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado) + parseFloat(fraccionBasica);
                } else if (baseGrabada >= 51630.01 && baseGrabada <= 61630.00) {
                    fraccionBasica = 6949.40;
                    excedente = ((baseGrabada - 51630.01) * 30) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado) + parseFloat(fraccionBasica);
                } else if (baseGrabada >= 61630.01 && baseGrabada <= 100000.00) {
                    fraccionBasica = 9949.40;
                    excedente = ((baseGrabada - 61630.01) * 35) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado) + parseFloat(fraccionBasica);
                } else if (baseGrabada >= 100000.01) {
                    fraccionBasica = 100000.01;
                    excedente = ((baseGrabada - 100000.01) * 37) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado) + parseFloat(fraccionBasica);
                }
            } else if (periodo == 2023) {
                if (baseGrabada >= 0 && baseGrabada <= 11722.00) {
                    impuestoCausado = 0;
                } else if (baseGrabada >= 11722.01 && baseGrabada <= 14935.00) {
                    impuestoCausado = 0;
                    excedente = ((baseGrabada - 11722.01) * 5) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado);
                } else if (baseGrabada >= 14935.01 && baseGrabada <= 18666.00) {
                    fraccionBasica = 161.00;
                    excedente = ((baseGrabada - 14935.01) * 10) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado) + parseFloat(fraccionBasica);
                } else if (baseGrabada >= 18666.01 && baseGrabada <= 22418.00) {
                    fraccionBasica = 534.00;
                    excedente = ((baseGrabada - 18666.01) * 12) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado) + parseFloat(fraccionBasica);
                } else if (baseGrabada >= 22418.01 && baseGrabada <= 32783.00) {
                    fraccionBasica = 984.00;
                    excedente = ((baseGrabada - 22418.01) * 15) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado) + parseFloat(fraccionBasica);
                } else if (baseGrabada >= 32783.01 && baseGrabada <= 43147.00) {
                    fraccionBasica = 2539.00;
                    excedente = ((baseGrabada - 32783.01) * 20) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado) + parseFloat(fraccionBasica);
                } else if (baseGrabada >= 43147.01 && baseGrabada <= 53512.00) {
                    fraccionBasica = 4612.00;
                    excedente = ((baseGrabada - 43147.01) * 25) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado) + parseFloat(fraccionBasica);
                } else if (baseGrabada >= 53512.01 && baseGrabada <= 63876.00) {
                    fraccionBasica = 7203.00;
                    excedente = ((baseGrabada - 53512.01) * 30) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado) + parseFloat(fraccionBasica);
                } else if (baseGrabada >= 63876.01 && baseGrabada <= 103644.00) {
                    fraccionBasica = 10312.00;
                    excedente = ((baseGrabada - 63876.01) * 35) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado) + parseFloat(fraccionBasica);
                } else if (baseGrabada >= 103644.01) {
                    fraccionBasica = 24231.00;
                    excedente = ((baseGrabada - 103644.01) * 37) / 100;
                    impuestoCausado = parseFloat(excedente) + parseFloat(impuestoCausado) + parseFloat(fraccionBasica);
                }
            }
        } else {
            console.log(2);
        }
        $('#impuestocausado').val(parseFloat(impuestoCausado).toFixed(2));
    }

    function valideKey(evt) { // code is the decimal ASCII representation of the pressed key.
        var code = (evt.which) ? evt.which : evt.keyCode;
        if (code == 8) { // backspace.
            return true;
        } else if (code >= 48 && code <= 57) { // is a number.
            return true;
        } else { // other keys.
            return false;
        }
    }

    function nextFocus(inputF, inputS, value) {
        document.getElementById(inputF).addEventListener('keydown', function(event) {
            if (event.keyCode == 13 || value.length > 0) {
                document.getElementById(inputS).focus();
            }
        });
    }

    function nextFocusSinLimite(inputF, inputS) {
        document.getElementById(inputF).addEventListener('keydown', function(event) {
            if (event.keyCode == 13) {
                document.getElementById(inputS).focus();
                document.getElementById(inputS).select();
            }
        });
    }

    function cambiaMayuscula(elemento) {
        elemento.value = elemento.value.toUpperCase();
    }
</script>
@endsection