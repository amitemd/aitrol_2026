@extends('hc4.ordenes.orden_ingresada_formato012.base')
@section('action-content')

<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<link rel="stylesheet" href="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.css")}}" />
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<style type="text/css">
    .ui-corner-all
    {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget
    {
        font-family: Verdana,Arial,sans-serif;
        font-size: 15px;
    }
    .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
    }
    .ui-autocomplete
    {
        overflow-x: hidden;
        max-height: 200px;
        width:1px;
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
        font-size: 10px;
        overflow:hidden;
        white-space:nowrap;
        text-overflow: ellipsis;
    }
    .ui-menu .ui-menu-item
    {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .ui-menu .ui-menu-item a
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }
    .ui-menu .ui-menu-item a:hover
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }
    .ui-widget-content a
    {
        color: #222222;
    }
</style>
@php $rd = rand(); @endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Actualizar Orden 012</h3>

                    <div class="box-tools pull-right">
                        <a class="btn btn-sm btn-primary" href="{{ route('orden_ingresada_formato012') }}"><i class="fa fa-reply" aria-hidden="true"></i> Regresar</a>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form id="frm_evol{{$ordenes_012->id}}">
                                <input type="hidden" id="id_orden{{$ordenes_012->id}}" name="id_orden" value="{{$ordenes_012->id}}">
                                <div class="col-md-12">
                                              
                                    <div class="col-md-3" style="padding: 1px;">
                                        <label for="id_doctor_examinador" class="control-label">Doctor Solicita</label>
                                        <select onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');" class="form-control input-sm" style="width: 100%;" name="id_doctor_examinador" id="id_doctor_examinador{{$ordenes_012->id}}">
                                            @foreach($doctores as $value)
                                                <option @if($ordenes_012->id_doctor_firma == $value->id) selected @endif value="{{$value->id}}" >{{$value->apellido1}} @if($value->apellido2 != "(N/A)"){{ $value->apellido2}}@endif {{ $value->nombre1}} @if($value->nombre2 != "(N/A)"){{ $value->nombre2}}@endif</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3" style="padding: 1px;">
                                        <label for="id_empresa" class="control-label">Empresa</label>
                                        <select onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');" class="form-control input-sm" style="width: 100%;" name="id_empresa" id="id_empresa{{$ordenes_012->id}}">
                                            @foreach($empresas as $value)
                                                <option value="{{ $value->id }}" {{ $ordenes_012->id_empresa == $value->id ? 'selected' : ''}} >{{ $value->nombrecomercial }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3" style="padding: 1px;">
                                        <label>Fecha Orden</label>
                                        <div class="input-group date" id="datetimepicker1{{$ordenes_012->id}}" data-target-input="nearest">
                                            <div class="input-group-addon" ata-target="#datetimepicker1{{$ordenes_012->id}}" data-toggle="datetimepicker">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" name="fecha_orden" id="fecha_orden{{$ordenes_012->id}}" class="form-control input-sm datetimepicker-input" data-target="#datetimepicker1{{$ordenes_012->id}}" onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}')" value="{{$ordenes_012->fecha_orden}}">
                                            <div class="input-group-addon">
                                                <i class="glyphicon glyphicon-remove-circle"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2" style="padding: 1px;">
                                        <label for="fecha_orden" class="col-md-12 control-label">Persona Refiere</label>
                                        <input class="form-control input-sm" type="text" name="referido" id="referido" onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');" value="{{ $ordenes_012->referido }}"> 
                                    </div>

                                    <div class="col-md-1" style="padding: 1px;">
                                        <label for="fecha_orden" class="col-md-6 control-label">Servicio</label>
                                        <input class="form-control input-sm" type="text" name="servicio" id="servicio" onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');" value="{{ $ordenes_012->servicio }}">
                                    </div>  

                                </div>

                                <div class="col-md-12" style="top: 15px;">
                                
                                    <div class="form-group col-md-3">
                                        <input class="flat-orange" type="checkbox" value="1" name="urgente" id="urgente{{$ordenes_012->id}}" required="required" @if($ordenes_012->urgente) checked @endif onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');">Urgente
                                    </div>
                                    <div class="form-group col-md-3">
                                        <input class="flat-orange" type="checkbox" value="1" name="rutina" id="rutina{{$ordenes_012->id}}" required="required" @if($ordenes_012->rutina) checked @endif onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');">Rutina
                                    </div>
                                    <div class="form-group col-md-3">
                                        <input class="flat-orange" type="checkbox" value="1" name="control" id="control{{$ordenes_012->id}}" required="required" @if($ordenes_012->control) checked @endif onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');">Control 
                                    </div>
                                
                                </div>

                                

                                <div class="col-md-12" style="padding: 1px; top: 12px;">
                                    <b style="margin-left: 10px;">1. Estudio Solicitado</b>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <input class="flat-orange" type="checkbox" value="1" name="rx_convencional" id="rx_convencional{{$ordenes_012->id}}" required="required" @if($ordenes_012->rx_convencional) checked @endif onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');">RX-Convencional 
                                        </div> 
                                        <div class="form-group col-md-3">   
                                            <input class="flat-orange" type="checkbox" value="1" name="tomografia" id="tomografia{{$ordenes_012->id}}" required="required" @if($ordenes_012->tomografia) checked @endif onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');">Tomografía 
                                        </div>
                                        <div class="form-group col-md-3">    
                                            <input class="flat-orange" type="checkbox" value="1" name="resonancia" id="resonancia{{$ordenes_012->id}}" required="required" @if($ordenes_012->resonancia) checked @endif onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');">Resonancia
                                        </div>
                                        <div class="form-group col-md-3">     
                                            <input class="flat-orange" type="checkbox" value="1" name="ecografia" id="ecografia{{$ordenes_012->id}}" required="required" @if($ordenes_012->ecografia) checked @endif onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');">Ecografía 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="padding: 1px;">
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <input class="flat-orange" type="checkbox" value="1" name="procedimiento" id="procedimiento{{$ordenes_012->id}}" required="required" @if($ordenes_012->procedimiento) checked @endif onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');">Procedimientos 
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input class="flat-orange" type="checkbox" value="1" name="otros" id="otros{{$ordenes_012->id}}" required="required" @if($ordenes_012->otros) checked @endif onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');">Otros 
                                        </div>
                                        <div class="form-group col-md-6">
                                            <input class="form-control input-sm" type="text" name="texto_otros" id="texto_otros{{$ordenes_012->id}}" required="required" onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');" value="{{$ordenes_012->texto_otros}}">
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="descripcion" class="control-label">Descripción</label>
                                        <textarea name="descripcion" id="descripcion{{$ordenes_012->id}}" style="width: 100%;" rows="1" onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');">{{$ordenes_012->descripcion}}</textarea>
                                    </div>


                                    <div class="col-md-12" style="padding: 1px;">
                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <input class="flat-orange" type="checkbox" value="1" name="puede_mover" id="puede_mover{{$ordenes_012->id}}" required="required" @if($ordenes_012->puede_mover) checked @endif onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');">Puede Movilizarse     
                                            </div>
                                            <div class="form-group col-md-3">
                                                <input class="flat-orange" type="checkbox" value="1" name="puede_retirar" id="puede_retirar{{$ordenes_012->id}}" required="required" @if($ordenes_012->puede_retirar) checked @endif onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');">Puede retirarse vendas apósitos o yesos     
                                            </div>
                                            <div class="form-group col-md-3">
                                                <input class="flat-orange" type="checkbox" value="1" name="medico_presente" id="medico_presente{{$ordenes_012->id}}" @if($ordenes_012->medico_presente) checked @endif required="required" onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');">El medico estará presente en el examen    
                                            </div>
                                            <div class="form-group col-md-3">
                                                <input class="flat-orange" type="checkbox" value="1" name="toma_radio" id="toma_radio{{$ordenes_012->id}}" required="required" @if($ordenes_012->toma_radio) checked @endif onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');">Toma de radriografía en la cama     
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="motivo" class="control-label">2. Motivo de la Solicitud</label>
                                        <textarea name="motivo" id="motivo{{$ordenes_012->id}}" style="width: 100%;" rows="1" onchange="guardar_orden_012('frm_evol{{$ordenes_012->id}}');">{{ $ordenes_012->motivo }}</textarea>
                                    </div>

                                    <div class="col-md-12" style="padding: 1px;">
                                        <label for="motivo" class="control-label">3. Resumen Clínico:</label>
                                        <div id="thistoria_clinica{{$ordenes_012->id}}-{{$rd}}"  style="width: 100%; border: 2px solid #004AC1;">
                                            <?php echo $ordenes_012->cuadro_clinico ?>
                                        </div>
                                        <input type="hidden" name="historia_clinica" id="historia_clinica{{$ordenes_012->id}}">
                                    </div>

                                    <input type="hidden" name="codigo_012" id="codigo_012{{$ordenes_012->id}}">

                                    <label class="col-md-9 control-label" style="padding-left: 0px;"><b>4. Diagnósticos</b></label>
                                    <div class="form-row">
                                        <div class="form-group col-md-9" style="padding: 1px;">
                                            <input id="cie10_012{{$ordenes_012->id}}" type="text" class="form-control input-sm ui-autocomplete-input" style="text-transform:uppercase;" placeholder="Diagnóstico">
                                        </div>

                                        <div class="form-group col-md-2" style="padding: 1px;">
                                            <select id="pre_def{{$ordenes_012->id}}" name="pre_def" class="form-control input-sm" required="">
                                                <option value="">Seleccione ...</option>
                                                <option value="PRESUNTIVO">PRESUNTIVO</option>
                                                <option value="DEFINITIVO">DEFINITIVO</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-1" style="padding: 1px;">
                                            <button id="bagregar_012{{$ordenes_012->id}}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus"></span></button>
                                        </div>    
                                    </div>

                                    <div class="form-group col-md-12" style="padding: 1px;margin-bottom: 0px;">
                                        <table id="tdiagnostico{{$ordenes_012->id}}" class="table table-striped" style="font-size: 12px;">
                                            @foreach($orden_012_cie10 as $val_cie10)
                                                <tr id="tdiag{{$val_cie10->id}}">
                                                    <td><b>{{$val_cie10->cie10}}</b></td>
                                                    @php 
                                                        $c10 = Sis_medico\Cie_10_3::find($val_cie10->cie10);
                                                        if(is_null($c10)){
                                                            $c10 = Sis_medico\Cie_10_4::find($val_cie10->cie10);
                                                        }
                                                    @endphp
                                                    <td>{{$val_cie10->presuntivo_definitivo}}</td>
                                                    <td>{{$c10->descripcion}}</td>
                                                    <td><a href="javascript:eliminar('{{$val_cie10->id}}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash" ></span></a></td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-2">
                                            <center>
                                            <button style="font-size: 14px; margin-bottom: 15px; height: 10%; width: 15%"  type="button" class="btn btn-sm btn-primary" onclick="guardar_orden_012('frm_evol{{$ordenes_012->id}}')"><span class="fa fa-floppy-o"></span>&nbsp;&nbsp;Actualizar
                                            </button>
                                            </center>
                                        </div>
                                     </div>                       
                                </div>

                            </form>
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                
                </div>
            </div>

        </div>
    </div>
</div>

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>


<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>


<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<!-- <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script> -->

<script type="text/javascript">

    $("#cie10_012{{$ordenes_012->id}}").autocomplete({
        source: function( request, response ) {

            $.ajax({
                url:"{{route('epicrisis.cie10_nombre')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {
                    term: request.term
                      },
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);

                }
            })
        },
        minLength: 2,
    } );

    $("#cie10_012{{$ordenes_012->id}}").change( function(){
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.cie10_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'cie10': $("#cie10_012{{$ordenes_012->id}}").val()},
            success: function(data){
                console.log(data);
                if(data!='0'){
                    $('#codigo_012{{$ordenes_012->id}}').val(data.id);

                }

            },
            error: function(data){

                }
        })
    });
    
    $('#bagregar_012{{$ordenes_012->id}}').click( function(e){
        e.preventDefault();
       
        if($('#pre_def{{$ordenes_012->id}}').val()!='' ){
            guardar_cie10();
        }else{
            alert("Seleccione Presuntivo o Definitivo");
        }
        

    });

    function guardar_cie10(){
        //alert("guardar");
        $.ajax({
            type: 'post',
            url:"{{route('orden_012.carga_012_c10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo_012{{$ordenes_012->id}}").val(), 'pre_def': $("#pre_def{{$ordenes_012->id}}").val(), 'id_orden': '{{$ordenes_012->id}}', 'in_eg': '' },
            success: function(data){
                console.log(data);
                var indexr = data.count-1
                var table = document.getElementById("tdiagnostico{{$ordenes_012->id}}");
                var row = table.insertRow(indexr);
                row.id = 'tdiag'+data.id;
                var cell1 = row.insertCell(0);
                cell1.innerHTML = '<b>'+data.cie10+'</b>';
                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.pre_def;
                var cell3 = row.insertCell(2);
                cell3.innerHTML = data.descripcion;
                var cell4 = row.insertCell(3);
                cell4.innerHTML = '<a href="javascript:eliminar('+data.id+');" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
                $('#codigo_012{{$ordenes_012->id}}').val('');
                $('#cie10_012{{$ordenes_012->id}}').val('');
                $('#pre_def{{$ordenes_012->id}}').val('');
            },
            error: function(data){

                }
        })
    }

    function eliminar(id_h){

        var i = document.getElementById('tdiag'+id_h).rowIndex;
        document.getElementById("tdiagnostico{{$ordenes_012->id}}").deleteRow(i);

        $.ajax({
        type: 'get',
        url:"{{url('orden_012/cargar/ci10/eliminar')}}/"+id_h,
        datatype: 'json',

        success: function(data){

        },
        error: function(data){

        }
        });
    }

    tinymce.init({
        selector: '#thistoria_clinica{{$ordenes_012->id}}-{{$rd}}',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('thistoria_clinica{{$ordenes_012->id}}-{{$rd}}');
               //alert(ed.getContent());
                $("#historia_clinica{{$ordenes_012->id}}").val(ed.getContent());

            });
        },

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('thistoria_clinica{{$ordenes_012->id}}-{{$rd}}');
                $("#historia_clinica{{$ordenes_012->id}}").val(ed.getContent());
                guardar_orden_012('frm_evol{{$ordenes_012->id}}');

            });
          }
    });

    

    $(function () {
        $('#datetimepicker1{{$ordenes_012->id}}').datetimepicker({
            format: 'YYYY/MM/DD hh:mm',
        });
    });

    $("#datetimepicker1{{$ordenes_012->id}}").on("dp.change", function (e) {
        guardar_orden_012('frm_evol{{$ordenes_012->id}}');
    });

    $('input[type="checkbox"].flat-orange').iCheck({
        checkboxClass: 'icheckbox_flat-orange',
        radioClass   : 'iradio_flat-orange'
    }); 

    $('input[type="checkbox"].flat-orange').on('ifChecked', function(event){
        guardar_orden_012('frm_evol{{$ordenes_012->id}}');
    });

    $('input[type="checkbox"].flat-orange').on('ifUnchecked', function(event){
        guardar_orden_012('frm_evol{{$ordenes_012->id}}');
    });
</script>
@endsection