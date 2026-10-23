@extends('comercial.proforma.base')

@section('action-content')

<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.css')}}" rel="stylesheet">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">
<link href="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('/css/bootstrap-datetimepicker.css')}}">
<link href="{{asset('/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .alerta_correcto {
        position: absolute;
        z-index: 9999;
        bottom: 100px;
        right: 20px;
    }

    .alerta_guardado {
        position: absolute;
        z-index: 9999;
        bottom: 100px;
        right: 20px;
    }

    .disableds {
        display: none;
    }

    .disableds2 {
        display: none;
    }

    .disableds3 {
        display: none;
    }

    .has-cc span img {
        width: 2.775rem;
    }

    .has-cc .form-control-cc {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;

    }

    .has-cc .form-control-cc2 {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;
    }

    .cvc_help {
        cursor: pointer;
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: white;
    }

    .card2 {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: #f1f1f1;
    }

    .swal-title {
        margin: 0px;
        font-size: 16px;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
        margin-bottom: 28px;
    }

    .card-header {
        border-radius: 6px 6px 0 0;
        background-color: #3c8dbc;
        border-color: #b2b2b2;
        padding: 8px;
        font-family: 'Roboto', sans-serif;
    }

    .col-md-6 {
        margin-top: 7px;
    }
</style>
<style type="text/css">
    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
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
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }

    .disableds {
        display: none;
    }

    .dogde {
        width: 100%;
        height: 20px;
    }

    .disableds2 {
        display: none;
    }

    .wells {
        background-color: #E3F2FD;
    }

    .disableds3 {
        display: none;
    }

    .has-cc span img {
        width: 2.775rem;
    }

    .has-cc .form-control-cc {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;

    }

    .has-cc .form-control-cc2 {
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 1.8rem;
        text-align: center;
        pointer-events: none;
        color: #444;
        font-size: 1.5em;
        float: right;
        margin-right: 1px;
    }

    .cvc_help {
        cursor: pointer;
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: white;
    }

    .card2 {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        padding: 16px;
        text-align: center;
        background-color: #f1f1f1;
    }

    .swal-title {
        margin: 0px;
        font-size: 16px;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
        margin-bottom: 28px;
    }

    .cabecera {
        background-color: #3c8dbc;
        border-radius: 8px;
        color: white;
    }

    .borde {
        border: 2px solid #3c8dbc;
    }

    .s {
        font-size: 17px !important;
    }

    .visible {
        display: none;
    }
</style>
<style type="text/css">
    .icheckbox_flat-orange.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }

    td {
        padding: 3px !important;

    }

    div.formgroup.col-md-4 {
        margin-bottom: 0px !important;
    }

    .ui-corner-all {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget {
        font-family: Verdana, Arial, sans-serif;
        font-size: 12px;
    }

    .ui-menu {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
        opacity: 1;
    }

    .ui-autocomplete {
        opacity: 1;
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 470px !important;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
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
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }

    .ui-menu .ui-menu-item {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .ui-menu .ui-menu-item a {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }

    .ui-menu .ui-menu-item a:hover {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }

    .ui-widget-content a {
        color: #222222;
    }

    tfoot tr,
    tfoot td {
        border: none !important;
        background: white;
    }

    .select2-selection--single {
        height: 37px !important;
        height: 36px !important;
    }

    /*#example2 input {
        padding: 15px;
    }

    #example2 label {
        font-size: 13px !important;
    }*/
</style>
<div class="modal fade" id="informacion_factura" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="forma_pago_gas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-lg" id="modal_datosfacturas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="datos_factura">

        </div>
    </div>
</div>

<div class="modal fade" id="forma_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<!-- Ventana Modal Pago -->
<div class="modal fade" id="mail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="width: 95%;" id="ax_mail">
        </div>
    </div>
</div>

<form class="form-vertical" role="form" id="form_aglabs">
    {{ csrf_field() }}
    <input type="hidden" name="id_orden" id="id_orden" value="{{$orden->id}}">
    <section class="content">
        <div class="modal fade bs-example-modal-lg" id="modal_privados" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" id="agenda_privados">

                </div>
            </div>
        </div>
</form>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="col-md-5">
                    <h3 class="box-title">{{trans('proforma.cotizacion')}}</h3>
                </div>
                <div class="col-md-5">
                    <!--<h4 style="color: red;">{{trans('proforma.Paciente')}}: {{$orden->id_paciente}} - {{ $orden->paciente->apellido1 }} {{ $orden->paciente->apellido2 }} {{ $orden->paciente->nombre1 }} {{ $orden->paciente->nombre2 }}</h4>-->
                </div>
                <div class="col-md-3">
                    @if($res['estado']=='ok')
                        <label style="font-style: 20px;color: red;"><div class="alert-success" style="text-align: right;padding-left: 10px;padding-right:  10px;">MEMBRESIA: {{ $res['nombre'] }}</div></label>
                    @endif
                </div>
                <div class="col-md-2">
                <a href="{{route('comercial.proforma.index_proforma')}}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-arrow-left"></span>{{trans('proforma.regresar')}}</a>
                </div>
            </div>

            <div class="box-body">


                <span class="text-red" name="mensaje">{{old('mensaje')}}</span>

                <form class="form-vertical" id="formulario" role="form" method="POST" enctype="multipart/form-data" accept-charset="UTF-8">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_orden" value="{{$orden->id}}">
                    <input id="id" maxlength="10" type="hidden" class="form-control input-sm" name="id" value="{{$orden->id_paciente}}" required autofocus onkeyup="validarCedula(this.value);" onchange="buscapaciente();" readonly>
                    <input id="nombre1" class="form-control input-sm" type="hidden" name="nombre1" value="{{ $orden->paciente->nombre1 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus readonly>
                    <input id="nombre2" type="hidden" class="form-control input-sm nombrecode dropdown-toggle" name="nombre2" value="{{ $orden->paciente->nombre2 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required readonly>
                    <input id="apellido1" type="hidden" class="form-control input-sm" name="apellido1" value="{{ $orden->paciente->apellido1 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus readonly>
                    <input id="apellido2" type="hidden" class="form-control input-sm nombrecode dropdown-toggle" name="apellido2" value="{{ $orden->paciente->apellido2 }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required readonly>
                    <!--div style="margin-bottom: 0px;" class="form-group col-md-3">
                        <label for="sexo" class="control-label">{{trans('proforma.sexo')}}</label>

                        <p class=""> @if($orden->paciente->sexo == 1) {{trans('proforma.masculino')}} @elseif($orden->paciente->sexo == 2) {{trans('proforma.femenino')}} @endif</p>



                    </div-->

                    <!--fecha_nacimiento-->
                    <!--div style="margin-bottom: 0px;" class="form-group col-md-3">
                        <label class="control-label">{{trans('proforma.fechanacimiento')}}</label>

                        <div class="input-group date">
                            <p>{{$orden->paciente->fecha_nacimiento}}</p>
                        </div>

                    </div-->
                     <div style="margin-bottom: 0px;" class="form-group col-md-3 {{ $errors->has('id_tipo') ? ' has-error' : '' }}">
                        <label for="id_tipo" class="control-label">Tipo Seguro</label>
                        <select class="form-control input-sm" name="id_tipo" id="id_tipo" required onchange="cargar_seguro();guardarCabecera();">
                            <!--option value="">Seleccione ...</option-->
                            @foreach($tipos_seguros as $ts)
                            <option @if($orden->seguro != null ) @if($orden->seguro->id_seguro_tipos != null) @if($orden->seguro->id_seguro_tipos == $ts->id) selected @endif @endif @endif value="{{$ts->id}}">{{$ts->nombre}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 0px;" class="form-group col-md-3 {{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                        <label for="id_seguro" class="control-label">{{trans('proforma.Seguro')}}</label>

                        <select id="id_seguro" name="id_seguro" class="form-control input-sm" required onchange="cargar_nivel();">
                            <option value="">{{trans('proforma.seleccion')}}...</option>
                            <? /*@foreach ($seguros as $seguro)
                            <option @if($orden->id_seguro == $seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                            @endforeach */ ?>
                        </select>

                    </div>


                    <div style="margin-bottom: 0px;" class="form-group col-md-3 {{ $errors->has('id_nivel') ? ' has-error' : '' }}">
                        <label class="control-label">{{trans('proforma.Nivel')}}</label>

                        <div id="niveles">
                            @if(!is_null($orden->nivel))
                            <select onchange="alertaNivel()" class="form-control input-sm" id="id_nivel" name="id_nivel">
                                @foreach($convenios as $con)
                                <option @if($orden->nivel == $con->id_nivel) selected @endif value='{{$con->id_nivel}}'> {{$con->nombre}} </option>
                                @endforeach
                            </select>
                            @else
                            <select class="form-control input-sm" id="id_nivel" name="id_nivel">
                                <option value="0"></option>
                            </select>
                            @endif
                        </div>

                    </div>

                    <div style="margin-bottom: 0px;" class="form-group col-md-3 {{ $errors->has('numero_oda') ? ' has-error' : '' }}">

                        <label for="numero_oda" class="control-label"># {{trans("proforma.oda")}} </label>
                        <input type="text" value="{{$orden->numero_oda}}" name="numero_oda" class="form-control input-sm" onchange="guardarCabecera()" placeholder="# ODA" id="oda">
                    </div>

                    <div style="margin-bottom: 0px;" class="form-group col-md-6 {{ $errors->has('observacion_cab') ? ' has-error' : '' }}">
                        <label for="observacion_cab" class="control-label">{{trans('proforma.observacion')}}</label>
                        <input type="text" value="Cobro por historia clinica" name="observacion_cab" class="form-control input-sm validar" onchange="guardarCabecera()" placeholder="Observacion" id="observacion">
                    </div>

                    <div style="margin-bottom: 0px;" class="form-group col-md-3 {{ $errors->has('fecha_caducidad') ? ' has-error' : '' }}">
                        <label class="control-label">{{trans('proforma.fechacaducidad')}}</label>
                        <input type="date" value="{{$orden->fecha_caducidad}}" name="fecha_caducidad" onchange="guardarCabecera()" class="form-control pull-right input-sm validar" id="fecha_caducidad" required>
                    </div>
                    <div style="margin-bottom: 0px;" class="form-group col-md-3">
                          <label class="control-label">Subir Oda</label>
                            <div class="col-md-12">
                                <div class="col-md-10">
                                  <input type="file" class="form-control" name="file_documento" id="file_documento"  onchange="cargarDocumento()">
                                </div>
                                <div class="col-md-2">
                                  @if(!is_null($orden->path_imagen))
                                   <a target="_blank" href="{{route('update_path_imagen.view',['id'=>$orden->id])}}" class="btn btn-md btn-success"><i class="fa fa-cloud-download" aria-hidden="true"></i></a>
                                  @endif
                                </div>
                            </div>
                    </div>

                    <!-- 
                    <div style="margin-bottom: 0px;" class="form-group col-md-4">
                        <label for="descuento_p" class="control-label">Descuento %</label>
                        <input type="number" required name="descuento_p" id="descuento_p" min="0" max="100" step="0.01" value="=orden->descuento_p class="form-control input-sm" onchange="cotizador_recalcular();">
                    </div> -->



                    <input type="hidden" name="cotizacion" value="{{$orden->id}}">

                    <div class="col-md-12">&nbsp;</div>
                    <!-- BUSCADOR COTIZADOR-->

                    <div class="col-md-12" id="detalles_orden">

                    </div>

                </form>
                @if($orden->estado == -1)
                <div class="col-md-12" style="text-align: center;">
                    <button type="button" onclick="proformaLista(event)" class="btn btn-primary">{{trans('proforma.emitir')}}</button>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>

</section>


<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/moment.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/jquery.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/fullcalendar.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/vertical-resource-view_files/scheduler.min.js.descarga')}}"></script>
<script src="{{asset('/plugins/fullcalendar/es.js')}}"></script>
<script src="{{asset('/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{asset('/plugins/colorpicker/bootstrap-colorpicker.js')}}"></script>
<script src="{{asset('/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.js') }}"></script>
<script src="{{ asset ('plugins/sweetalert2_6_11/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset ('/js/jquery.validate.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>

<script>
    function descargarDocumento(){
    }
    function cargarDocumento(){
          var formData = new FormData();
          formData.append('file', $('#file_documento')[0].files[0]);
          formData.append('id', $("#id_orden").val());
          $.ajax({
             url: "{{route('update_path_imagen.update')}}",
             headers: {
                 'X-CSRF-TOKEN': $('input[name=_token]').val()
             },
             data: formData,
            processData: false, // tell jQuery not to process the data
            contentType: false, // tell jQuery not to set contentType
             type: 'POST',
             dataType: 'json',
             success: function(data) {
                 if(data.status == 200){
                    Swal.fire("Bien!", data.msj, "success")
                 }
                 if(data.status == 500){
                    Swal.fire("Error!", data.msj, "error")
                 }
             },
             error: function(xhr, status) {
                 alert('Existi� un problema');
             },
         });
    }
    cargar_seguro_primeravez();
    $('#fecha_nacimiento').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD',
        //Important! See issue #1075
    });

    lista_productos();
    @if(is_null($orden->nivel))
    @endif

    /*$('#fecha_caducidad').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD',
        //Important! See issue #1075
    });*/

    function lista_productos() {
        $.ajax({
            url: "{{route('comercial.proforma.detalles',['id' => $orden->id])}}",
            type: 'get',
            datatype: 'html',
            success: function(data) {
                $('#detalles_orden').empty().html(data);
            },
            error: function(data) {
                //console.log(data);
            }
        })
    }

    const seleccionar_producto = () => {
        var producto_nuevo = $('#producto_nuevo').val();
        
        $.ajax({
            type: 'post',
            url: "{{route('comercial.proforma.guardar_producto')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'producto_nuevo': producto_nuevo,
                'id': '{{ $orden->id }}',
                'id_nivel': document.getElementById('id_nivel') != null ? document.getElementById('id_nivel').value : null,
            },
            success: function(data) {
                lista_productos();
                //console.log(data);
            },
            error: function(data) {
                // console.log(data);
                alert("No se pudo agregar el producto");
            }
        });

    }

    function actualizar_valor(id) {

        var cantidad = $('#cantidad' + id).val();
        var precio = $('#precio' + id).val();
        var p_cpac = $('#p_cpac' + id).val();
        var cobrar_paciente = $('#cobrar_paciente' + id).val();
        var p_dcto = $('#p_dcto' + id).val();
        var descuento = $('#descuento' + id).val();
        //var iva = $('#iva'+id).val();
        var id_producto = $('#id_producto' + id).val();
        var deducible = $('#valor_deducible' + id).val();

        cantidad = parseFloat(cantidad);
        precio = parseFloat(precio);
        p_cpac = parseFloat(p_cpac);
        cobrar_paciente = parseFloat(cobrar_paciente);
        p_dcto = parseFloat(p_dcto);
        descuento = parseFloat(descuento);
        deducible = parseFloat(deducible);
        //iva = parseFloat(iva);

        $.ajax({
            type: 'post',
            url: "{{route('comercial.proforma.actualizar_producto')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': id,
                'id_producto': id_producto,
                'cantidad': cantidad,
                'precio': precio,
                'p_cpac': p_cpac,
                'cobrar_paciente': cobrar_paciente,
                'p_dcto': p_dcto,
                'descuento': descuento,
                'deducible': deducible,
            },
            success: function(data) {
                lista_productos();
                console.log(data);
            },
            error: function(data) {
                console.log(data);
                alert("No se pudo agregar el producto");
            }
        });
    }

    function actualizar_p_cobro(id) {
        var cantidad = $('#cantidad' + id).val();
        var precio = $('#precio' + id).val();
        var cobrar_paciente = $('#cobrar_paciente' + id).val();

        cantidad = parseFloat(cantidad);
        precio = parseFloat(precio);
        cobrar_paciente = parseFloat(cobrar_paciente);

        var neto = cantidad * precio;
        var pct_cobra = cobrar_paciente / neto;

        pct_cobra = pct_cobra * 100;
        pct_cobra = Math.round(pct_cobra * 100) / 100;

        $('#p_cpac' + id).val(pct_cobra);

        actualizar_valor(id);

    }


    function actualizar_p_dcto(id) {
        var cantidad = $('#cantidad' + id).val();
        var precio = $('#precio' + id).val();
        var descuento = $('#descuento' + id).val();

        cantidad = parseFloat(cantidad);
        precio = parseFloat(precio);
        descuento = parseFloat(descuento);

        var neto = cantidad * precio;
        var pct_dcto = descuento / neto;

        pct_dcto = pct_dcto * 100;
        pct_dcto = Math.round(pct_dcto * 100) / 100;

        $('#p_dcto' + id).val(pct_dcto);

        actualizar_valor(id);

    }

    function actualizarPaciente() {
        $.ajax({
            type: 'post',
            url: "{{ route('comercial.proforma.updatePaciente') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#formulario").serialize(),
            success: function(data) {

            },
            error: function(data) {

            }
        })
    }

    function eliminar_detalle(id) {

        $.ajax({
            type: 'post',
            url: "{{route('comercial.proforma.eliminar_producto')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': id
            },
            success: function(data) {
                lista_productos();
                console.log(data);
            },
            error: function(data) {
                console.log(data);
                alert("No se pudo agregar el producto");
            }
        });

    }

    function cargar_nivel() {

        $.ajax({
            type: 'post',
            url: "{{route('comercial.proforma.nivel')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_seguro': document.getElementById('id_seguro').value
            },
            success: function(data) {

                let contenedor = document.createElement('niveles');
                let id_nivel = document.getElementById('id_nivel');


                if (id_nivel == null) {
                    if (data.cont == 1 || data.cont == 0) {
                        alertaNivel()
                    }
                    $('#niveles').append(data.selects);
                } else {
                    if (data.cont == 1 || data.cont == 0) {
                        alertaNivel()
                    }
                    id_nivel.parentElement.removeChild(id_nivel)
                    $('#niveles').append(data.selects);
                }

                console.log(data)
            },
            error: function(data) {

            }
        })
    }

    function alertaNivel() {
        Swal.fire({
            title: 'Si realiza esta accion se procedera a recalcular esta proforma',
            text: "Esta seguro?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, deseo continuar'
        }).then((result) => {
            if (result.isConfirmed) {
                actualizarValor();
            } else {
                window.location.reload();
            }
        })
    }

    const guardarCabecera = () => {
        $.ajax({
            type: 'post',
            url: "{{ route('comercial.proforma.updateCabecera') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#formulario").serialize(),
            success: function(data) {
                //console.log(data);
            },
            error: function(data) {

            }
        })
    }

    const actualizarValor = () => {
        $.ajax({
            type: 'post',
            url: "{{ route('comercial.proforma.actualizarNivel') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_orden': '{{$orden->id}}',
                'id_nivel': $('#id_nivel').val(),
                'id_seguro': $('#id_seguro').val(),
            },
            success: function(data) {
                console.log(data);

                lista_productos();

            },
            error: function(data) {

            }
        })
    }


    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    const proformaLista = (e) => {
        e.preventDefault();
        $('#crear_cotizacion').prop("disabled", true);
        guardarCabecera();
        if (!validar_campos()) {
            const fechaActual = new Date();
            let fecha = document.getElementById('fecha_caducidad').value;
            let fechaCaducidad = new Date(fecha);

            if (fechaCaducidad < fechaActual) {
                Swal.fire({
                    title: 'La fecha de caducidad de la proforma es menor a la fecha actual',
                    text: "Esta seguro que desa guardar?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, Guardar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (!validarProductos()) {
                            storeProformaLista()
                        }

                    } else {
                        $('#crear_cotizacion').prop("disabled", false);
                    }
                })
            } else {
                if (!validarProductos()) {
                    storeProformaLista()
                }
            }
        } else {
            $('#crear_cotizacion').prop("disabled", false);
            alertas('error', 'ERROR', `{{trans('proforma.camposvacios')}}`)
        }
    }

    function storeProformaLista() {
        $.ajax({
            type: 'post',
            url: "{{ route('comercial.proforma.proformaLista_nuevo') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_orden': `{{$orden->id}}`
            },
            success: function(data) {
                console.log(data);
                if (data.status == 'success') {
                    alertas(data.status, "Exito...", data.msj);
                    setTimeout(function() {
                        window.location.href = `{{url('comercial/proforma/index_proforma_nuevo')}}`;
                    })
                } else {
                    alertas(data.status, "Error...", data.msj);
                    $('#crear_cotizacion').prop("disabled", false);
                }
            },
            error: function(data) {

            }
        })
    }

    function validar_campos() {
        let campo = document.querySelectorAll(".validar")
        let validar = false;

        for (let i = 0; i < campo.length; i++) {

            if (campo[i].value.trim() <= 0) {
                campo[i].style.border = '3px solid #CD6155';
                campo[i].style.borderRadius = '4px';
                validar = true;
            } else {
                campo[i].style.border = '1px solid #d2d6de';
                campo[i].style.borderRadius = '0px';
            }
        }
        return validar;
    }

    const validarProductos = () => {
        let producto = document.querySelectorAll('.id_producto');
        let validate = false;
        if (producto.length == 0) {
            validate = true;
            alertas('error', 'Error...', 'No ha elegido producto')
        }

        return validate;
    }

    function agregar_deducible(id) {

        $.ajax({
            url: "{{url('comercial/deducible/crear/item/xseguro')}}/" + id,
            type: 'get',
            datatype: 'html',
            success: function(data) {
                lista_productos();

            },
            error: function(data) {
                console.log(data);
            }
        })

    }

    function seleccionar_agrupador() {

        Swal.fire({
            title: 'Si realiza esta accion se procedera a agregar esta plantilla',
            text: "Esta seguro?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, deseo continuar'
        }).then((result) => {
            if (result.isConfirmed) {

                var agrupador = $('#agrupador').val();

                $.ajax({
                    type: 'post',
                    url: "{{route('proforma.guardar_agrupador')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: {
                        'agrupador': agrupador,
                        'id': '{{ $orden->id }}',
                    },
                    success: function(data) {
                        lista_productos();
                        //console.log(data);
                    },
                    error: function(data) {
                        // console.log(data);
                        alert("No se pudo seleccionar el agrupador");
                    }
                });
            }
        })


    }
    function cargar_seguro() {
        var id_tipo   = $('#id_tipo').val();
        $.ajax({
            type: 'post',
            url: "{{route('facturaagenda.tipifica_seguros')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_tipo'   : id_tipo,
            },
            success: function(data) {
                if (data.value != 'no') {
                    var contador = 0;
                    @if($orden->seguro != null ) var contador = 1; @endif
                    $("#id_seguro").empty();
                    $.each(data, function(key, registro) {
                        if(contador == 0){
                            $("#id_seguro").append('<option selected value=' + registro.id + '>' + registro.nombre + '</option>');
                            $('#id_seguro').val(registro.id);
                            contador ++;
                        }else{
                            $("#id_seguro").append("<option value=" + registro.id + ">" + registro.nombre + "</option>");
                        }
                    });
                    @if($orden->seguro != null )
                    $("#id_seguro option[value='{{$orden->id_seguro}}']").prop('selected', true);
                    @endif
                    cargar_nivel();
                } else {
                    $("#id_seguro").empty();
                }
            },
            error: function(data) {}
        });
    }
    function cargar_seguro_primeravez(){
        var id_tipo   = $('#id_tipo').val();
        //var nivel_contenido = document.getElementById('id_nivel').value;
        //alert(nivel_contenido);
        $.ajax({
            type: 'post',
            url: "{{route('facturaagenda.tipifica_seguros')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id_tipo'   : id_tipo,
            },
            success: function(data) {
                if (data.value != 'no') {
                    var contador = 0;
                    @if($orden->seguro != null ) var contador = 1; @endif
                    $("#id_seguro").empty();
                    $("#id_seguro").append("<option selected value=''>" + 'Seleccione...' + "</option>");
                    $.each(data, function(key, registro) {
                        if(contador == 0){
                            $("#id_seguro").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                            $('#id_seguro').val(registro.id);
                            contador ++;
                        }else{
                            $("#id_seguro").append("<option value=" + registro.id + ">" + registro.nombre + "</option>");
                        }
                    });
                    @if($orden->seguro != null )
                    $("#id_seguro option[value='{{$orden->id_seguro}}']").prop('selected', true);
                    @endif
                    //cargar_nivel();
                } else {
                    $("#id_seguro").empty();
                }
            },
            error: function(data) {}
        });
    }
</script>

@endsection