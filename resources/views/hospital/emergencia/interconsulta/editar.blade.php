<style type="text/css">
    table td,
    table th {
        padding: 5px !important;
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

    .mce-content-body:hover {
        outline: 2px solid #2276d2 !important;
    }

    .mce-content-body{

        background-color: inherit !important;

    }
</style>

<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-2">
            <i style="color: white;">{{trans('emergencia.InterconsultaNo.')}} {{$interconsulta->id}}</i>
        </div>
        <!--div class="col-md-10" style="text-align: center;color: white;">
            <a class="btn btn-info btn-sm" href="{{route('decimo.imprimir_interconsulta',['id' => $interconsulta->id])}}" target="_blank">Formulario 007</a>
        </div-->
        
    </div>
    <div class="card-body" style="margin-bottom: 1px;padding: 0;">
        <form id="interconsulta{{$interconsulta->id}}">

            {{ csrf_field() }}
            <input type="hidden" name="id_interconsulta" id="id_interconsulta" value="{{$interconsulta->id}}">
            <div class="row" style="padding: 0;">
                <div class="col-md-12">
                    <label><b> {{trans('emergencia.Servicio')}}: </b> </label>
                    <input name="servicio" id="servicio" class="form-control input-sm" value="{{$interconsulta->servicio}}">
                </div>
                <div class="col-md-12">
                    <label><b> {{trans('emergencia.Especialidad')}}: </b> </label>
                    <input name="especialidad" id="especialidad" class="form-control input-sm" value="{{$interconsulta->especialidad}}">
                </div>
                <!--div class="col-md-12">
                    <label><b> {{trans('emergencia.Evolución')}}: </b> </label>
                    <textarea name="evolucion" id="evolucion" class="form-control input-sm" rows="3">{{$interconsulta->evolucion}}</textarea>
                </div-->
                <div class="col-md-12">
                    <label><b> {{trans('emergencia.Evolución')}}: </b> </label>
                    <div id="tevolucion" style="border: solid 1px; border-color: #CED9DA; overflow:auto !important;max-height: 150px;resize:none;background-color: white;">@if(!is_null($interconsulta))<?php echo ($interconsulta->evolucion) ?> @endif</div>
                    <input type="hidden" name="evolucion" id="evolucion">
                </div>
                <div class="col-md-12">
                    <label><b> {{trans('emergencia.Tarifario')}}: </b> </label>
                    <div class="row">
                        <div class="col-md-9">
                            <select id="tarifario" name="tarifario" class="form-control input-sm select2_tarifario">
                                <option value="">{{trans('agenda.seleccione')}}...</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button name="btn_tarifario" id="btn_tarifario" type="button" class="btn btn-primary" onclick="agregar_tar();">{{trans('paso2.Agregar')}}</button>
                        </div>
                        <div class="col-md-12" id="listado" name="listado">

                        </div>
                        
                    </div>
                    
                </div>
                <div class="col-md-12">
                    <label><b> {{trans('emergencia.Descripcion')}}: </b> </label>
                    <textarea name="descripcion" id="descripcion" class="form-control input-sm" rows="3">{{$interconsulta->descripcion}}</textarea>
                </div>
                <div class="col-md-12">
                    <label><b> {{trans('emergencia.Resultadosdeexamenesyprocedimientosdiagnosticos')}}: </b> </label>
                    <textarea name="resultados_exa" id="resultados_exa" class="form-control input-sm" rows="3">{{$interconsulta->descripcion}}</textarea>
                </div>
                <div class="col-md-12">
                    <label><b> {{trans('emergencia.Planesterapeuticosyeducacionalesrealizados')}}: </b> </label>
                    <textarea name="plan_terapeuticos" id="plan_terapeuticos" class="form-control input-sm" rows="3">{{$interconsulta->descripcion}}</textarea>
                </div>


                <div class="col-md-12"><br>
                    <center>
                        <button class="btn btn-success btn-xs" type="button" onclick="guardar()">{{trans('emergencia.Guardar')}}</button>
                    </center>
                </div>

            </div>

        </form>
        <div class="col-md-12">
            <form id="form_diagnostico">
                {{ csrf_field() }}
                <div class="row" style="padding-top: 10px;">
                    <input type="hidden" name="id_interconsulta" id="id_interconsulta" value="{{$interconsulta->id}}">
                    <div class="col-md-6">
                        <label>{{trans('paso2.Diagnostico')}}</label>
                        <input type="hidden" name="codigo" id="codigo" class="form-control input-sm">
                        <input type="text" name="cie10" id="cie10" class="form-control input-sm">
                    </div>
                    <div class="col-md-4">
                        <br>
                        <select name="pre_def" id="pre_def" class="form-control">
                            <option value="">{{trans('paso2.Seleccione')}}</option>
                            <option value="PRESUNTIVO">{{trans('paso2.PRESUNTIVO')}}</option>
                            <option value="DEFINITIVO">{{trans('paso2.DEFINITIVO')}}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <br>
                        <button name="agregar_cie" id="agregar_cie" type="button" class="btn btn-primary btn-sm">{{trans('paso2.Agregar')}}</button>
                    </div>
                    <br>
                    <div class="form-group col-12" style="padding: 1px;margin-bottom: 0px;">
                        <table id="tdiagnostico" class="table table-striped" style="font-size: 12px;">

                        </table>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<script src="{{ asset ('/js/jquery.validate.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="{{ asset ("/js/tinymce/tinymce.min.js") }}"></script>
<script type="text/javascript">
    
    $( document ).ready(function() {
        listado_det();
        return tinymce.init({
            selector: '#tevolucion',
            height: 100,
            width: 550,
            inline: true,
            menubar: false,
            content_style: ".mce-content-body {font-size:12px;}",
            plugins: ['image', 'charmap', 'uploadimage'],
            toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link uploadimage | charmap ',
            theme: 'modern',
            relative_urls: false,
            remove_script_host: false,
            document_base_url: (!window.location.origin ? window.location.protocol + '//' + window.location.host : window.location.origin) + '/',
            setup: function(editor) {
                editor.on('init', function(e) {
                    var ed = tinyMCE.get('tevolucion');
                    //alert(ed.getContent());
                    $("#evolucion").val(ed.getContent());

                });
            },
            init_instance_callback: function(editor) {
                editor.on('Change', function(e) {
                    var ed = tinyMCE.get('tevolucion');
                    $("#evolucion").val(ed.getContent());
                    guardar();
                });
            }
        });
    });
    function agregar_tar(){
        var id_tarifario = document.getElementById('tarifario').value;
        var id_interconsulta = document.getElementById('id_interconsulta').value;
        $.ajax({
			type: 'GET',
			url: "{{route('decimopaso.guardar_det_tarifario')}}",
			headers: {
				'X-CSRF-TOKEN': $('input[name=_token]').val()
			},
			datatype: 'json',
			data: {
				'id_tarifario': id_tarifario,
				'id_interconsulta': id_interconsulta,
			},
			success: function(data) {
				console.log(data);
                listado_det();
				
			},
			error: function(data) {
				console.log(data);
			}
		});
    }

    function listado_det() {
        $.ajax({
            type: "GET",
            url: "{{route('decimopaso.inter_tarifario_det',['id'=>$interconsulta->id])}}",
            datatype: "html",
            success: function(datahtml) {
                //console.log(datahtml);
                $("#listado").html(datahtml);

            },
            error: function() {
                alert('error al cargar');
            }
        });
    }
    
    $('.select2_tarifario').select2({
        placeholder: "{{trans('agenda.seleccione')}}...",
        allowClear: true,
        ajax: {
            url: '{{route("decimopaso.buscarTarifario")}}',
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });

    $('#cie10').autocomplete({
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
                }
            })
        },
        minLength: 2,
    });


    $('#cie10').change(function() {
        $.ajax({
            type: 'post',
            url: "{{route('epicrisis.cie10_nombre2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $('#cie10'),
            success: function(data) {
                if (data != '0') {

                    $('#codigo').val(data.id);
                }
            },
            error: function(data) {}
        })
    });

    $('#agregar_cie').click(function() {

        if ($('#cie10').val() != '') {
            if ($('#pre_def').val() != '') {
                guardar_cie10_consulta({{$interconsulta->id}});

            } else {
                alert("Seleccione Presuntivo o Definitivo");
            }
        } else {
            alert("Seleccione CIE10");
        }

        $('#codigo').val('');
        $('#cie10').val('');


    });

    function guardar_cie10_consulta(id_interconsulta) {
        $.ajax({

            type: 'post',
            url: "{{url('hospital/emergencia/decimopaso/interconsulta/cie10')}}/" + id_interconsulta,
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#form_diagnostico").serialize(),
            success: function(data) {
                cargar_tabla_cie({{$interconsulta->id}});
                /*var indexr = data.count-1
                var table = document.getElementById("tdiagnostico");
                var row = table.insertRow(indexr);
                row.id = 'tdiag'+data.id;

                var cell1 = row.insertCell(0);
                cell1.innerHTML = '<b>'+data.cie10+'</b>';

                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.descripcion;

                var vpre_def = '';
                if(data.pre_def!=null){
                    vpre_def = data.pre_def;
                }
                var cell3 = row.insertCell(2);
                cell3.innerHTML = vpre_def;

                var cell4 = row.insertCell(3);
                cell4.innerHTML = '<a href="javascript:eliminar_ing('+data.id+', '+data.id+');" class="btn btn-xs btn-danger btn-xs"><i class="fa fa-trash"></i></a>';
                //$('#prescripcion').empty().html(anterior+ data.value +': \n' +data.dosis);                
                console.log('guardo');
                //();*/

            },
            error: function(data) {
                //console.log(data);
            }
        })
    }

    cargar_tabla_cie({{$interconsulta->id}});


    function cargar_tabla_cie(id) {
        $.ajax({
            url: `{{route('decimo.cargar_tabla_cie',['id' => $interconsulta->id])}}`,
            dataType: "html",
            type: 'get',

            success: function(data) {

                $('#tdiagnostico').html(data);
                //console.log(data);
                /*var table = document.getElementById("tdiagnostico");

                $.each(data, function (index, value) {

                    var row = table.insertRow(index);
                    row.id = 'tdiag'+value.id;

                    var cell1 = row.insertCell(0);
                    cell1.innerHTML = '<b>'+value.cie10+'</b>';

                    var cell2 = row.insertCell(1);
                    cell2.innerHTML = value.descripcion;

                    var vpre_def = '';
                    if(value.pre_def!=null){
                        vpre_def = value.pre_def;
                    }
                    var cell3 = row.insertCell(2);
                    cell3.innerHTML = vpre_def;

                    var cell4 = row.insertCell(3);
                    cell4.innerHTML = '<a href="javascript:eliminar_ing('+value.id+', '+id+');" class="btn btn-xs btn-danger btn-xs"><i class="fa fa-trash"></i></a>';
                    //alert(index);
                });*/
            }
        })
    }



    function guardar() {

        $.ajax({

            type: 'post',
            url: "{{route('decimo.actualizar_interconsulta')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#interconsulta{{$interconsulta->id}}").serialize(),
            success: function(data) {
                //$("#content").html(data);
                alertas(data.respuesta, `${data.respuesta.toUpperCase()}...`, data.msj)
                
            },
            error: function(data) {
                alert('error al cargar');
            }
        })

    }

    $("#buscador{{$interconsulta->id}}").autocomplete({
        source: function(request, response) {

            $.ajax({
                url: "{{route('decimopaso.examenes_buscar_publicos_otros')}}",
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

    // $("#tarifario").autocomplete({

    //     source: function(request, response) {
    //         var nivel_conve = '2';
    //         var term = request.term;
    //         $.ajax({
    //             url: "{{route('item_iess.buscarxcodigo')}}",
    //             headers: {
    //                 'X-CSRF-TOKEN': $('input[name=_token]').val()
    //             },
    //             data: {
    //                 'term': term,
    //                 'conv': nivel_conve
    //             },
    //             dataType: "json",
    //             type: 'post',
    //             success: function(data) {
    //                 response(data);
    //             }

    //         });

    //     },
    //     change: function(event, ui) {
    //         //console.log(ui);
    //         //$("#tipo").val(ui.item.tipo);
    //         $("#descripcion").val(ui.item.descripcion);
    //         //$("#cantidad").val(ui.item.cantidad);
    //         //$("#iva").val(ui.item.iva);
    //         //$("#porcent_10").val(ui.item.porcent10);
    //         //obtener_data(ui.item.id_ap_proced,ui.item.tipo);


    //     },
    //     select: function(event, ui) {
    //         //console.log(ui);
    //         //$("#tipo").val(ui.item.tipo);
    //         $("#descripcion").val(ui.item.descripcion);
    //         //$("#cantidad").val(ui.item.cantidad);
    //         //$("#iva").val(ui.item.iva);
    //         //$("#porcent_10").val(ui.item.porcent10);
    //         //obtener_data(ui.item.id_ap_proced,ui.item.tipo);


    //     },
    //     minLength: 2,
    //     appendTo: "#item", //Linea nueva, agrego el id del modal
    // });


    function eliminar_cie10(id) {
        $.ajax({
            async: true,
            type: "GET",
            url: "{{url('hosp/decimo/cie/eliminar_cie')}}/" + id,
            data: "",
            datatype: "html",
            success: function(datahtml) {

                cargar_tabla_cie({{$interconsulta->id}});

            },
            error: function() {
                alert('error al cargar');
            }
        });
    }
</script>