<style type="text/css">
    table td, table th{
        padding: 5px !important;
    }
    .ui-corner-all
    {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget
    {
        font-family: Verdana,Arial,sans-serif;
        font-size: 12px;
    }
    .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
        opacity : 1;
    }
    .ui-autocomplete
    {
        opacity : 1;
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

<div class="card">    
    <br>        
    <div class="card-body" style="margin-bottom: 1px;padding: 0;">
        <form id="interconsulta{{$interconsulta->id}}" >
                    
            {{ csrf_field() }}          
            <input type="hidden" name="id_interconsulta" value="{{$interconsulta->id}}"> 
            <div class="row" style="padding: 0;"> 
                @php $workflowPriority = $interconsulta->clinicalWorkflow ? $interconsulta->clinicalWorkflow->priority : 'ROUTINE'; @endphp
                <div class="col-md-12">
                    <label><b>Prioridad clínica y tiempo de respuesta:</b></label>
                    <select name="clinical_priority" class="form-control input-sm">
                        <option value="ROUTINE" {{ $workflowPriority == 'ROUTINE' ? 'selected' : '' }}>Rutina (24 horas)</option>
                        <option value="URGENT" {{ $workflowPriority == 'URGENT' ? 'selected' : '' }}>Urgente (60 minutos)</option>
                        <option value="STAT" {{ $workflowPriority == 'STAT' ? 'selected' : '' }}>Inmediata (15 minutos)</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label><b> {{trans('emergencia.Servicio')}}: </b> </label>
                    <input name="servicio" id="servicio" class="form-control input-sm" value="{{$interconsulta->servicio}}" >
                </div>
                <div class="col-md-12">
                    <label><b> {{trans('emergencia.Especialidad')}}: </b> </label>
                    <input name="especialidad" id="especialidad" class="form-control input-sm" value="{{$interconsulta->especialidad}}" >
                </div>
                
                <!-- <div class="col-md-12">
                    <label><b> {{trans('emergencia.Tarifario')}}: </b> </label>
                    <input name="tarifario" id="tarifario" class="form-control input-sm" value="{{$interconsulta->tarifario}}" >
                </div>  -->

                <div class="col-md-12">
                     <label><b> Tarifario: </b> </label>
                     <div class="row"> 
                        <div class="col-md-9"> 
                           <input name="tarifario" id="tarifario" class="form-control input-sm" >
                        </div>   
                        <div class="col-md-3">
                           <button type="button" class="btn btn-primary btn-xs">Agregar</button>
                        </div>
                    </div>
                </div> 
               <div class="col-md-12">&nbsp;
               </div>    
                    <div class="col-md-2">
                        <a class="btn btn-success" style="color: white" onclick="agregar_eda();">EDA</a>
                    </div>
                    <div class="col-md-2">
                        <a class="btn btn-success" style="color: white" onclick="agregar_colono();">COLONO</a>
                    </div>
                <div class="col-md-12">
                    <label><b> {{trans('emergencia.Descripcion')}}: </b> </label>
                    <textarea name="descripcion" id="descripcion" class="form-control input-sm" rows="3">{{$interconsulta->descripcion}}</textarea>
                </div>



                <input type="hidden" name="codigo_07" id="codigo_07{{$interconsulta->id}}">

                <label class="col-12 control-label"><b>@lang('hc4.diagnosticos')</b></label>
                <div class="row">
                    <div class=" col-md-7">
                        <input id="cie10_07{{$interconsulta->id}}" type="text" class="form-control input-sm ui-autocomplete-input" style="text-transform:uppercase;" placeholder="@lang('hc4.diagnostico')">
                    </div>

                    <div class="col-md-4">
                        <select id="pre_def{{$interconsulta->id}}" name="pre_def" class="form-control input-sm" required="">
                            <option value="">@lang('hc4.seleccione') ...</option>
                            <option value="PRESUNTIVO">@lang('hc4.presuntivo')</option>
                            <option value="DEFINITIVO">@lang('hc4.definitivo')</option>
                        </select>
                    </div>
                    <div class=" col-md-1">
                        <button id="agregar_cie_07{{$interconsulta->id}}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus"></span></button>
                    </div>
                </div>
                <div class="form-group col-md-12" style="padding: 1px;margin-bottom: 0px;">
                    <table id="tdiagnostico{{$interconsulta->id}}" class="table table-striped" style="font-size: 12px;">
                        @foreach($interconsulta->detalle as $val_cie10)
                        <tr id="tdiag{{$val_cie10->id}}">
                            <td><b>{{$val_cie10->cie10}}</b></td>
                            @php
                            $c10 = Sis_medico\Cie_10_3::find($val_cie10->cie10);
                            if(is_null($c10)){
                            $c10 = Sis_medico\Cie_10_4::find($val_cie10->cie10);
                            }
                            @endphp
                            <td>@if(isset($c10)) {{$c10->descripcion}} @endif</td>
                            <td>{{$val_cie10->presuntivo_definitivo}}</td>
                            <td><a href="javascript:eliminar('{{$val_cie10->id}}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a></td>
                        </tr>
                        @endforeach
                    </table>
                </div>

                <!--div class="form-group col-12" style="padding: 1px;margin-bottom: 0px;">
                                <table id="tdiagnostico" class="table table-striped" style="font-size: 12px;">
                                    @foreach($interconsulta->detalle as $detalle)

                                    <tr>
                                        <td>
                                            {{$detalle->cie10}}
                                        </td>

                                        <td>
                                            {{$detalle->ingreso_egreso}}
                                        </td>

                                        <td>
                                            {{$detalle->presuntivo_definitivo}}
                                        </td>
                                    </tr>

                                    @endforeach
                                </table>
                            </div-->

                <div class="col-md-12"><br>
                    <center>
                        <button class="btn btn-success btn-xs" type="button" onclick="guardar()">{{trans('emergencia.Guardar')}}</button>
                        <a class="btn btn-info btn-sm" href="{{route('consulta.hc4_imprimir',['hcid' => $interconsulta->id])}}" target="_blank">Formulario 007</a>
                        <a href="{{route('formulario.007',['id' => $interconsulta->id])}}" target="_blank" class="btn btn-danger">Solicitud007&nbsp;<i class="fa fa-file-pdf-o" aria-hidder="true"></i></a>
                        <a href="{{route('informe.007',['id' => $interconsulta->id])}}" target="_blank" class="btn btn-danger">informe007&nbsp;<i class="fa fa-file-pdf-o" aria-hidder="true"></i></a>
                    </center>
                </div>

            </div>

        </form>

    </div>
</div>
<script src="{{ asset ('/js/jquery.validate.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script type="text/javascript">
    $('.select2').select2({
        tags: false
    });

    function changeTarifario(e) {


        var descripcion_actual = document.getElementById('descripcion').value;
        var txt_descripcion = $('option:selected', e).data("descripcion");
        var txt_codigo = $('option:selected', e).data("codigo");
        $('#descripcion').val(descripcion_actual + ' + ' + txt_descripcion + '(' + txt_codigo + ')');

    }


    $("#cie10_07{{$interconsulta->id}}").autocomplete({
        source: function(request, response) {

            $.ajax({
                url: "{{route('epicrisis.cie10_nombre')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    term: request.term
                },
                dataType: "json",
                type: 'post',
                success: function(data) {
                    response(data);
                    console.log('response'+data);

                }
            })
        },
        minLength: 2,
    });

    $("#cie10_07{{$interconsulta->id}}").change(function() {
        $.ajax({
            type: 'post',
            url: "{{route('epicrisis.cie10_nombre2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'cie10': $("#cie10_07{{$interconsulta->id}}").val()
            },
            success: function(data) {
                console.log(data);
                if (data != '0') {
                    $('#codigo_07{{$interconsulta->id}}').val(data.id);

                }

            },
            error: function(data) {

            }
        })
    });

    $('#agregar_cie_07{{$interconsulta->id}}').click(function(e) {
        e.preventDefault();

        if ($('#pre_def{{$interconsulta->id}}').val() != '') {
            guardar_cie10_consulta();
        } else {
            alert("Seleccione Presuntivo o Definitivo");
        }


    });

    //     $('#cie10').autocomplete({
    //     source: function( request, response )
    //     {
    //         $.ajax({
    //             url:"{{route('epicrisis.cie10_nombre')}}",
    //             headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
    //             data: {
    //                 term: request.term
    //                   },
    //             dataType: "json",
    //             type: 'post',
    //             success: function(data){
    //                 response(data);
    //             }
    //         })
    //     },
    //         minLength: 2,
    // });


    // $('#cie10').change( function()
    //     {
    //     $.ajax({
    //         type: 'post',
    //         url:"{{route('epicrisis.cie10_nombre2')}}",
    //         headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
    //         datatype: 'json',
    //         data: $('#cie10'),
    //         success: function(data){
    //             if(data!='0'){

    //                 $('#codigo').val(data.id);
    //             }
    //         },
    //         error: function(data){
    //         }
    //     })
    // });

    // $('#agregar_cie{{$interconsulta->id}}').click( function(e){
    //     e.preventDefault();

    //     if($('#pre_def{{$interconsulta->id}}').val()!='' ){
    //         guardar_cie10_();
    //     }else{
    //         alert("Seleccione Presuntivo o Definitivo");
    //     }


    // });



    function guardar_cie10_consulta() {
       
        $.ajax({
            type: 'post',
            url: "{{route('hc4.epicrisis.agregar_cie10_007')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'codigo': $("#codigo_07{{$interconsulta->id}}").val(),
                'pre_def': $("#pre_def{{$interconsulta->id}}").val(),
                'id_orden': '{{$interconsulta->id}}',
                'in_eg': ''
            },
            success: function(data) {
                console.log(data);
                var indexr = data.count - 1
                var table = document.getElementById("tdiagnostico{{$interconsulta->id}}");
                var row = table.insertRow(indexr);
                row.id = 'tdiag' + data.id;
                var cell1 = row.insertCell(0);
                cell1.innerHTML = '<b>' + data.cie10 + '</b>';
                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.descripcion;
                var cell3 = row.insertCell(2);
                cell3.innerHTML = data.pre_def;
                var cell4 = row.insertCell(3);
                cell4.innerHTML = '<a href="javascript:eliminar(' + data.id + ');" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';
                $('#codigo_07{{$interconsulta->id}}').val('');
                $('#cie10_07{{$interconsulta->id}}').val('');
                
                $('#pre_def{{$interconsulta->id}}').val('');
            },
            error: function(data) {

            }
        })
    }


    function guardar() {

        $.ajax({

            type: 'post',
            url: "{{route('consulta.hc4_guardar')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#interconsulta{{$interconsulta->id}}").serialize(),
            success: function(data) {
                alert("!! Guardado Correctamente !!");

            },
            error: function(data) {
                alert('error al cargar');
            }
        })

    }

    $("#buscador{{$interconsulta->id}}").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{route('consulta.examenes_buscar_publicos_otros')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    term: request.term
                },
                dataType: "json",
                type: 'post',
                success: function(data) {
                    response(data);
                    //console.log(data);
                },
            })
        },
        minLength: 3,
        select: function(data, ui) {
            console.log(ui.item.id);
            //$('#examenpub_id').val(ui.item.id);
            cargar_buscador(ui.item.id);
        }
    });

    function agregar_eda() {
        var txt_des = document.getElementById('descripcion').value;
        txt_des = txt_des + ' ENDOSCOPIA CON BIOPSIA (70200003) \n';

        $("#descripcion").val(txt_des);
    }

    function agregar_colono() {
        var txt_des = document.getElementById('descripcion').value;
        txt_des = txt_des + ' COLONOSCOPIA CON BIOPSIA (70200004) \n';

        $("#descripcion").val(txt_des);
    }

    $("#tarifario").autocomplete({

        source: function(request, response) {
            var nivel_conve = '2';
            var term = request.term;
            $.ajax({
                url: "{{route('item_iess.buscarxcodigo_2')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    'term': term,
                    'conv': nivel_conve
                },
                dataType: "json",
                type: 'post',
                success: function(data) {
                    response(data);
                }

            });

        },
        change: function(event, ui) {
            //console.log(ui);
            //$("#tipo").val(ui.item.tipo);
            //var txt_des = ui.item.descripcion + ' (' + term + ' )';
            //$("#descripcion").val(txt_des);
            //$("#cantidad").val(ui.item.cantidad);
            //$("#iva").val(ui.item.iva);
            //$("#porcent_10").val(ui.item.porcent10);
            //obtener_data(ui.item.id_ap_proced,ui.item.tipo);

            $("#tarifario").val('');
            document.getElementById('tarifario').innerHTML = '';

        },
        select: function(event, ui) {
            console.log(ui);
            //$("#tipo").val(ui.item.tipo);
            var txt_des = $("#descripcion").val() + '\n' + ui.item.value;
            $("#descripcion").val(txt_des);
            $("#tarifario").val('');
            document.getElementById('tarifario').innerHTML = '';
            //$("#cantidad").val(ui.item.cantidad);
            //$("#iva").val(ui.item.iva);
            //$("#porcent_10").val(ui.item.porcent10);
            //obtener_data(ui.item.id_ap_proced,ui.item.tipo);


        },
        minLength: 2,
        //appendTo: "#item",  //Linea nueva, agrego el id del modal
    });

    function eliminar(id_h) {

        var i = document.getElementById('tdiag' + id_h).rowIndex;
        document.getElementById("tdiagnostico{{$interconsulta->id}}").deleteRow(i);

        $.ajax({
            type: 'delete',
            url: "{{url('hosp/decimo/cie/eliminar_cie')}}/" + id_h,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            datatype: 'json',

            success: function(data) {

            },
            error: function(data) {

            }
        });
    }

    // function eliminar_cie10(id){
    //     $.ajax({
    //         async: true,
    //         type: "GET",
    //         url: "{{url('hosp/decimo/cie/eliminar_cie')}}/"+id,
    //         data: "",
    //         datatype: "html",
    //         success: function(datahtml){

    //             cargar_tabla_cie({{$interconsulta->id}});

    //         },
    //         error:  function(){
    //             alert('error al cargar');
    //         }
    //     });
    // }  
</script>
