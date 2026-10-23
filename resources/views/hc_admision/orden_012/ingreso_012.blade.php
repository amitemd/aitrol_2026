@extends('hc_admision.historia.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<style type="text/css">
    
    .mce-edit-focus,
    .mce-content-body:hover {
        outline: 2px solid #2276d2 !important;
    }
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
<div class="container-fluid" >
    <div class="row">
    	<div class="col-md-12" style="padding-right: 6px;">
            <div class="box box-primary " style="margin-bottom: 5px;">
                <div class="box-header with-border" style="padding: 1px;">
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped" style="margin-bottom: 0px;">
                            <tbody>
                                <tr >
                                    <td><b>Paciente: </b></td><td style="color: red; font-weight: 700; font-size: 18px;"><b>{{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif</b></td>
                                    <td><b>Identificación</b></td><td>{{$paciente->id}}</td>
                                   
                                    <td style="text-align: right;background: #e6ffff;"><b>@if($paciente->proc_consul=='0')CONSULTA {{DB::table('especialidad')->find($paciente->espid)->nombre}} @elseif($paciente->proc_consul=='1')PROCEDIMIENTO @endif</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-body with-border" style="padding: 1px;">
                    <div class="col-md-12" style="padding: 1px;" id="div2">
                        <h4 style="text-align: center;background-color: cyan;"><b>Orden Formato 012 y 052</b></h4>
                        <div class="col-md-12" style="padding: 1px;background: #e6ffff;">
                            @php 
                                $dia =  Date('N',strtotime($orden_012->fecha_orden));
                                $mes =  Date('n',strtotime($orden_012->fecha_orden));
                            @endphp
                            <b>Fecha: </b>@if($dia == '1') Lunes @elseif($dia == '2') Martes @elseif($dia == '3') Miércoles @elseif($dia == '4') Jueves @elseif($dia == '5') Viernes @elseif($dia == '6') Sábado @elseif($dia == '7') Domingo @endif {{substr($orden_012->fecha_orden,8,2)}} de @if($mes == '1') Enero @elseif($mes == '2') Febrero @elseif($mes == '3') Marzo @elseif($mes == '4') Abril @elseif($mes == '5') Mayo @elseif($mes == '6') Junio @elseif($mes == '7') Julio @elseif($mes == '8') Agosto @elseif($mes == '9') Septiembre @elseif($mes == '10') Octubre @elseif($mes == '11') Noviembre @elseif($mes == '12') Diciembre @endif del {{substr($orden_012->fecha_orden,0,4)}} 
                        </div>
                        <a class="btn btn-success btn-xs" href="{{route('orden_012.imprimir_012_excel',['id' => $orden_012->id])}}" target="_blank">Descargar 012</a>
                        <!--<a class="btn btn-success btn-xs" href="{{route('orden_012.imprimir_012',['id' => $orden_012->id])}}" target="_blank">Descargar 012</a-->

                        <form id="frm_evol">
                            <input type="hidden" id="id_orden" name="id_orden" value="{{$orden_012->id}}">
                            
                            <div class="col-md-12">

                                
                                   
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="id_doctor_examinador" class="control-label">Doctor Firma</label>
                                    <select onchange="guardar_orden_012();"  class="form-control input-sm" style="width: 100%;" name="id_doctor_examinador" id="id_doctor_examinador">
                                        @foreach($doctores as $value)
                                            <option @if($orden_012->id_doctor_firma == $value->id) selected @endif value="{{$value->id}}" >{{$value->apellido1}} @if($value->apellido2 != "(N/A)"){{ $value->apellido2}}@endif {{ $value->nombre1}} @if($value->nombre2 != "(N/A)"){{ $value->nombre2}}@endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="id_seguro" class="control-label">Seguro</label>
                                    <select onchange="guardar_orden_012();"  class="form-control input-sm" style="width: 100%;" name="id_seguro" id="id_seguro">
                                        @foreach($seguros as $value)
                                            <option @if($orden_012->id_seguro == $value->id) selected @endif value="{{$value->id}}" >{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="id_empresa" class="control-label">Empresa</label>
                                    <select onchange="guardar_orden_012();"  class="form-control input-sm" style="width: 100%;" name="id_empresa" id="id_empresa">
                                        @foreach($empresas as $value)
                                            <option @if($orden_012->id_empresa == $value->id) selected @endif value="{{$value->id}}" >{{$value->nombrecomercial}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3" style="padding: 1px;">
                                    <label for="fecha_orden" class="col-md-6 control-label">Fecha Orden</label>
                                    <input class="form-control input-sm" type="text" name="fecha_orden" id="fecha_orden" required="required" onchange="guardar_orden_012();"> 
                                </div>
                                                
                                
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="descripcion" class="control-label">Descripción</label>
                                    <textarea name="descripcion" style="width: 100%;" rows="1" onchange="guardar_orden_012();" >{{$orden_012->descripcion}}</textarea>
                                </div>

                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="motivo" class="control-label">Motivo</label>
                                    <textarea name="motivo" style="width: 100%;" rows="1" onchange="guardar_orden_012();" >{{$orden_012->motivo}}</textarea>
                                </div>

                                <div class="col-md-12" style="padding: 1px;">
                                    <label for="thistoria_clinica" class="control-label">Evolución</label>
                                    <div id="thistoria_clinica" style="border: solid 1px;"><?php echo $orden_012->cuadro_clinico ?></div>
                                    <input type="hidden" name="historia_clinica" id="historia_clinica">
                                </div>

                                <input type="hidden" name="codigo" id="codigo">

                                <label for="cie10" class="col-md-8 control-label" style="padding-left: 0px;"><b>Diagnóstico </b></label>
                                <div class="form-group col-md-8" style="padding: 1px;">
                                    <input id="cie10" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Diagnóstico" >
                                </div>

                                <div class="form-group col-md-2" style="padding: 1px;">
                                    <select id="pre_def" name="pre_def" class="form-control input-sm" required>
                                        <option value="">Seleccione ...</option>
                                        <option value="PRESUNTIVO">PRESUNTIVO</option>
                                        <option value="DEFINITIVO">DEFINITIVO</option>
                                    </select>
                                </div>

                                <button id="bagregar" class="btn btn-success btn-sm col-md-2"><span class="glyphicon glyphicon-plus"> Agregar</span></button>


                                <div class="form-group col-md-12" style="padding: 1px;margin-bottom: 0px;">
                                    <table id="tdiagnostico" class="table table-striped" style="font-size: 12px;">
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
                                                <td></td>
                                                <td>{{$c10->descripcion}}</td>
                                                <td><a href="javascript:eliminar('{{$val_cie10->id}}');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a></td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                </div>    

            </div>
        </div>
        
    </div>
</div> 

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
    tinymce.init({
        selector: '#thistoria_clinica',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

       


        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('thistoria_clinica');
               //alert(ed.getContent());
                $("#historia_clinica").val(ed.getContent());

            });
        },

        

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('thistoria_clinica');
                $("#historia_clinica").val(ed.getContent());
                guardar_orden_012();

            });
          }
    });

    function guardar_orden_012(){

        $.ajax({
          type: 'post',
          url:"{{route('orden_012.actualizar')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          data: $("#frm_evol").serialize(),
          success: function(data){
            console.log(data);
            
          },
          error: function(data){

          }
        });
    }

    $("#cie10").autocomplete({
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

    $("#cie10").change( function(){
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.cie10_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#cie10"),
            success: function(data){
                if(data!='0'){
                    $('#codigo').val(data.id);

                }

            },
            error: function(data){

                }
        })
    });

    $('#bagregar').click( function(e){
        e.preventDefault();
       
        if($('#pre_def').val()!='' ){
            if($('#ing_egr').val()!='' ){
                guardar_cie10();
                $('#ing_egr').val('');
            }else{
                alert("Seleccione Ingreso o Egreso");
            }
        }else{
            alert("Seleccione Presuntivo o Definitivo");
        }
        

        $('#codigo').val('');
        $('#cie10').val('');
        $('#pre_def').val('');

    });

    function guardar_cie10(){
        $.ajax({
            type: 'post',
            url:"{{route('orden_012.carga_012_c10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo").val(), 'pre_def': $("#pre_def").val(), 'id_orden': $("#id_orden").val(), 'in_eg': $("#ing_egr").val() },
            success: function(data){
                console.log(data);


                var indexr = data.count-1
                var table = document.getElementById("tdiagnostico");
                var row = table.insertRow(indexr);
                row.id = 'tdiag'+data.id;
                var cell1 = row.insertCell(0);
                cell1.innerHTML = '<b>'+data.cie10+'</b>';
                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.pre_def;
                var cell3 = row.insertCell(2);
                cell3.innerHTML = data.descripcion;
                var cell4 = row.insertCell(3);
                cell4.innerHTML = data.in_eg;
                var cell5 = row.insertCell(4);
                cell5.innerHTML = '<a href="javascript:eliminar('+data.id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';

                //aqui va para la receta
                anterior = tinyMCE.get('trp').getContent();
                //$('#prescripcion').empty().html(anterior+ data.value +': \n' +data.dosis);
                tinyMCE.get('trp').setContent(anterior+ '<div class="cie10-receta" >'+data.cie10 +': \n' +data.descripcion+'</div>');
                $('#rp').val(tinyMCE.get('trp').getContent());
                cambiar_receta_2();

            },
            error: function(data){

                }
        })
    }

    function eliminar(id_h){


        var i = document.getElementById('tdiag'+id_h).rowIndex;

        document.getElementById("tdiagnostico").deleteRow(i);

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


    $(function () {
        $('#fecha_orden').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$orden_012->fecha_orden}}',
        });
    }); 

    $("#fecha_orden").on("dp.change", function (e) {
        guardar_orden_012();
    });   

    
</script>
@endsection   	