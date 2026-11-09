<style type="text/css">
    .parent{
        overflow-y:scroll;
        height: 600px;
    }
    .parent::-webkit-scrollbar {
        width: 8px;
    } /* this targets the default scrollbar (compulsory) */
    .parent::-webkit-scrollbar-thumb {
        background: #004AC1;
        border-radius: 10px;
    }
    .parent::-webkit-scrollbar-track {
        width: 10px;
        background-color: #004AC1;
        box-shadow: inset 0px 0px 0px 3px #56ABE3;
    } /* the new scrollbar will have a flat appearance with the set background color */
    .parent::-webkit-scrollbar-track-piece{
        width: 2px;
        background-color: none;
    }

    .parent::-webkit-scrollbar-button {
          background-color: none;
    } /* optionally, you can style the top and the bottom buttons (left and right for horizontal bars) */

    .parent::-webkit-scrollbar-corner {
          background-color: none;
    } /* if both the vertical and the horizontal bars appear, then perhaps the right bottom corner also needs to be styled */

    .btn-block{
      background-color: #004AC1;
    }
     .table>tbody>tr>td, .table>tbody>tr>th {
        padding: 0.4% ;
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

    .mce-edit-focus,
    .mce-content-body:hover {
        outline: 2px solid #2276d2 !important;
    }

    .select2-selection--multiple{
        background-color: white !important;
    }

    .btn_agregar_diag{
        color: white;
        background-color: green;
    }
    .alerta_correcto{
        position: absolute;
        z-index: 9999;
        top: 100px;
        right: 10px;
    }

    .hc4-surgical-procedures-view { border: 1px solid #d3e5ed !important; border-radius: 16px !important; overflow: hidden; background: #fff !important; }
    .hc4-surgical-procedures-view > .box-body { padding: 28px 34px !important; background: #fff !important; }
    .hc4-surgical-procedures-view .parent {
        display: block;
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 0 18px 0 0;
        overflow-x: hidden;
        box-sizing: border-box;
    }
    .hc4-surgical-procedures-view .parent > .col-12 {
        width: 100%;
        max-width: 100%;
        padding-right: 0;
        padding-left: 0;
        box-sizing: border-box;
    }
    .hc4-surgical-procedure-card, .hc4-surgical-inner-card, .hc4-surgical-evolution-history, .hc4-surgical-evolution-card {
        border: 1px solid #10a6b4 !important; border-radius: 16px !important; overflow: hidden; background: #fff !important;
        box-shadow: 0 8px 22px rgba(16, 93, 127, .08);
    }
    .hc4-surgical-procedure-card { width: 100%; max-width: 100%; margin-bottom: 22px; box-sizing: border-box; }
    .hc4-surgical-summary, .hc4-surgical-inner-header, .hc4-surgical-history-header, .hc4-surgical-evolution-header {
        position: relative; width: 100%; max-width: 100%; min-height: 86px; padding: 14px 18px !important; border: 0 !important; color: #fff !important;
        box-sizing: border-box;
        background: linear-gradient(105deg, #075b82 0%, #078cb7 55%, #18b2a8 100%) !important;
    }
    .hc4-surgical-summary-grid {
        display: grid;
        grid-template-columns: minmax(0, .95fr) minmax(0, 1.55fr) minmax(0, .8fr) max-content 44px;
        gap: 18px;
        align-items: center;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }
    .hc4-surgical-summary-grid > * { min-width: 0; }
    .hc4-surgical-summary-date { white-space: nowrap; font-size: 16px; font-weight: 400; }
    .hc4-surgical-summary-label { font-weight: 700; }
    .hc4-surgical-summary-text { min-width: 0; line-height: 1.45; }
    .hc4-surgical-summary-doctor { min-width: 0; white-space: nowrap; }
    .hc4-surgical-action, .hc4-surgical-toggle {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 42px;
        border: 1px solid rgba(255, 255, 255, .38) !important; border-radius: 12px !important;
        color: #fff !important; background: rgba(255, 255, 255, .15) !important; box-shadow: none !important; cursor: pointer;
    }
    .hc4-surgical-action { padding: 9px 14px; font-family: 'Helvetica general3'; font-size: 13px; white-space: nowrap; }
    .hc4-surgical-action--danger {
        width: 42px;
        min-width: 42px;
        max-width: 42px;
        height: 42px;
        padding: 0 !important;
        gap: 0;
        background: #c94856 !important;
        font-size: 15px;
        box-sizing: border-box;
    }
    .hc4-surgical-toggle { width: 42px; min-width: 42px; max-width: 42px; padding: 0 !important; font-size: 16px; box-sizing: border-box; }
    .hc4-surgical-summary .box-tools, .hc4-surgical-history-header .box-tools, .hc4-surgical-evolution-header .box-tools { position: static !important; float: none !important; margin: 0 !important; }
    .hc4-surgical-procedure-card > .box-body { width: 100%; max-width: 100%; padding: 20px !important; overflow-x: hidden; box-sizing: border-box; }
    .hc4-surgical-inner-header, .hc4-surgical-history-header, .hc4-surgical-evolution-header { min-height: 72px; }
    .hc4-surgical-inner-header-grid, .hc4-surgical-history-header-grid, .hc4-surgical-evolution-header-grid { display: grid; align-items: center; gap: 18px; width: 100%; }
    .hc4-surgical-inner-header-grid { grid-template-columns: 130px 1fr 130px; }
    .hc4-surgical-history-header-grid { grid-template-columns: 180px 1fr 46px; }
    .hc4-surgical-evolution-header-grid { grid-template-columns: 130px 1fr auto 46px; }
    .hc4-surgical-header-title { text-align: center; font-size: 16px; font-weight: 500; line-height: 1.25; text-transform: uppercase; }
    .hc4-surgical-inner-card > .box-body, .hc4-surgical-evolution-card > .box-body { width: 100%; max-width: 100%; padding: 24px 26px !important; overflow-x: hidden; box-sizing: border-box; }
    .hc4-surgical-inner-card .row, .hc4-surgical-evolution-history .row { max-width: 100%; }
    .hc4-surgical-inner-card > .box-body > .row,
    .hc4-surgical-evolution-card > .box-body > .row {
        width: 100%;
        margin-right: 0;
        margin-left: 0;
    }
    .hc4-surgical-evolution-card { margin-bottom: 16px !important; }
    @media (max-width: 1100px) {
        .hc4-surgical-summary-grid { grid-template-columns: 1fr auto 46px; gap: 12px 16px; }
        .hc4-surgical-summary-date, .hc4-surgical-summary-text, .hc4-surgical-summary-doctor { white-space: normal; }
        .hc4-surgical-summary-text { grid-column: 1 / -1; grid-row: 2; }
        .hc4-surgical-summary-doctor { grid-column: 1; grid-row: 3; }
        .hc4-surgical-summary-delete { grid-column: 2; grid-row: 3; }
        .hc4-surgical-summary-toggle { grid-column: 3; grid-row: 1; }
    }
</style>
@php
    $ip_cliente= $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;
@endphp
<script type="text/javascript">
    window.hc4SurgicalWorkAreaSelector = @json(isset($moduleWorkAreaSelector) ? $moduleWorkAreaSelector : '#area_trabajo');

    @if(isset($moduleReloadUrl))
    window.cargar_procedimiento_quirurgicos = function(mensaje) {
        $.ajax({
            type: 'GET',
            url: @json($moduleReloadUrl),
            dataType: 'html',
            success: function(datahtml) {
                $(window.hc4SurgicalWorkAreaSelector).html(datahtml);
                if (mensaje) {
                    $('#msn1').text(mensaje).show().delay(3000).fadeOut();
                }
            },
            error: function(xhr) {
                alert(xhr.responseText || 'Error al actualizar el historial de cirugías');
            }
        });
    };
    @endif

    function editarprocedimiento(id, id_paciente){
        var entra = id;
            $.ajax({
                type: "GET",
                url: "{{route('editar.procedimiento_quirurgico')}}/"+id+'/'+id_paciente,
                data: "",
                datatype: "html",
                success: function(datahtml, entra){
                    $("#procedimiento"+id).html(datahtml);
                },
                error:  function(xhr){
                    alert(xhr.responseText || 'Error al cargar el procedimiento quirúrgico');
                }
            });
    }

    function eliminar_evolucion(id_evolucion, id_procedimiento){
        var opcion = confirm("Estas Seguro que deseas borrar la evolucion?");
        if (opcion == true) {
            $.ajax({
                type: "DELETE",
                url: "{{asset('/')}}/inicio/procedimientos/evolucion/eliminar/"+id_evolucion,
                data: {'_token': '{{ csrf_token() }}'},
                datatype: "html",
                success: function(datahtml, entra){
                    document.getElementById( 'evolucion_eliminar'+id_evolucion ).style.display = 'none';
                },
                error:  function(){
                    alert('error al cargar');
                }
            });

        }
    }
    function eliminar_procedimiento(id_procedimiento){
        var opcion = confirm("Estas Seguro que deseas borrar el procedimiento?");
        if (opcion == true) {
            $.ajax({
                type: "DELETE",
                url: "{{asset('/')}}/inicio/procedimientos/eliminar/"+id_procedimiento,
                data: {'_token': '{{ csrf_token() }}'},
                datatype: "html",
                success: function(datahtml, entra){
                    document.getElementById( 'procedimiento_eliminado1'+id_procedimiento ).style.display = 'none';
                },
                error:  function(){
                    alert('error al cargar');
                }
            });

        }
    }
</script>
<script type="text/javascript">
    var remoto_href = '';
    jQuery('body').on('click', '[data-toggle="modal"]', function() {
        if(remoto_href != jQuery(this).data('remote')) {
            remoto_href = jQuery(this).data('remote');
            jQuery(jQuery(this).data('target')).removeData('bs.modal');

            jQuery(jQuery(this).data('target')).find('.modal-body').empty();
            jQuery(jQuery(this).data('target') + ' .modal-content').load(jQuery(this).data('remote'));
        }
    });
    function editar_imagenes22(id_protocolo){
        $.ajax({
            async: true,
            type: "GET",
            url: "{{route('paciente.imagenes_protocolo')}}"+'/'+id_protocolo,
            data: "",
            datatype: "html",
            success: function(datahtml){
                $(window.hc4SurgicalWorkAreaSelector).html(datahtml);
            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }
    function editar_imagenes(id_protocolo) {
      window.location.assign("{{route('paciente.imagenes_protocolo')}}"+'/'+id_protocolo);

    }
    // function agregar_procedimiento_quirugicos(){
    //     contador = parseInt($('#contador_endoscopicos').val());
    //     hc_endoscopico = parseInt($('#id_endoscopico').val());
    //     hc_funcional = parseInt($('#id_funcional').val());
    //     var url_datos = "";
    //     if(contador>0){
    //         agregar_procedimiento_hc('endoscopico_id');
    //     }else if (hc_endoscopico >0) {

    //         url_datos = "{{asset('/procedimiento2/selecciona2/')}}/0/{{$paciente->id}}/"+hc_endoscopico;
    //         agregar_procedimiento_tipo(url_datos);
    //         //console.log(url_datos);
    //     }else if (hc_funcional >0) {
    //         //alert(hc_funcional);
    //         url_datos = "{{asset('/procedimiento2/selecciona2/')}}/0/{{$paciente->id}}/"+hc_funcional;
    //         agregar_procedimiento_tipo(url_datos);
    //     }else{
    //       $.ajax({
    //             async: true,
    //             type: "GET",
    //             url: "{{route('hc4_procedimiento.selecciona_procedimiento',['tipo' => '5', 'paciente' => $paciente->id ])}}",
    //             data: "",
    //             datatype: "html",
    //             success: function(datahtml){
    //                 $("#area_trabajo").html(datahtml);
    //             },
    //             error:  function(){
    //                 alert('error al cargar');
    //             }
    //         });
    //     }
    //     //alert("agregar");
    // }
    


    function agregar_procedimiento_quirugicos(){
        contador = parseInt($('#contador_endoscopicos').val());
        hc_endoscopico = parseInt($('#id_endoscopico').val());
        hc_funcional = parseInt($('#id_funcional').val());
        var url_datos = "";
        
          $.ajax({
                async: true,
                type: "GET",
                url: "{{route('hc4_procedimiento.selecciona_quirurgicos',['tipo' => '5', 'paciente' => $paciente->id ])}}",
                data: "",
                datatype: "html",
                success: function(datahtml){
                    $(window.hc4SurgicalWorkAreaSelector).html(datahtml);
                },
                error:  function(){
                    alert('error al cargar');
                }
            });
        }
        //alert("agregar");
    

    function editarprocedimiento2(id){
        var entra = id;
            $.ajax({
            type: "GET",
            url: "{{route('editar.procedimiento_endoscopico2')}}/"+id,
            data: "",
            datatype: "html",
            success: function(datahtml, entra){
                $("#procedimiento"+id).html(datahtml);
            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }

    function editar_evolucion(id, id_paciente){
        var entra = id;
            $.ajax({
            type: "GET",
            url: "{{route('editar.procedimiento_evolucion')}}/"+id+'/'+id_paciente,
            data: "",
            datatype: "html",
            success: function(datahtml, entra){
                $("#evolucion"+id).html(datahtml);
            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }
    function agregar_evolucion(id, id_evolucion){
        var entra = id;
        $.ajax({
            type: "POST",
            url: "{{route('agregar.procedimiento_evolucion')}}/"+id,
            data: {'_token': '{{ csrf_token() }}', 'context': 'quirurgico'},
            datatype: "html",
            success: function(datahtml, entra){
                anterior  = $("#evolucion_agregar"+id).html();
                $("#evolucion_agregar"+id).html(datahtml+anterior);

            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }

    function cargar_tabla(id){
        $.ajax({
                url:"{{route('epicrisis.cargar22')}}/"+id,
                dataType: "json",
                type: 'get',
                success: function(data){
                    //console.log(data);
                    var table = document.getElementById("tdiagnostico"+id);

                    $.each(data, function (index, value) {

                        var row = table.insertRow(index);
                        row.id = 'tdiag'+value.id;
                        var cell1 = row.insertCell(0);
                        cell1.innerHTML = '<b>'+value.cie10+'</b>';
                        var cell2 = row.insertCell(1);
                        cell2.innerHTML = value.pre_def;
                        var cell3 = row.insertCell(2);
                        cell3.innerHTML = value.descripcion;
                        var cell4 = row.insertCell(3);
                        cell4.innerHTML = value.ingreso_egreso;
                        var cell5 = row.insertCell(4);
                        cell5.innerHTML = '<a href="javascript:eliminar('+value.id+', '+id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';

                    });

                }
            })
    }


    function cargar_tabla_biopsias(id){

        $.ajax({
                url:"{{route('biopsias.carga_prescripcion')}}/"+id,
                dataType: "json",
                type: 'get',
                success: function(data){
                    //console.log(data);
                    var table = document.getElementById("tbiopsia"+id);

                    $.each(data, function (index, value) {

                        var row = table.insertRow(index);
                        row.id = 'tbio'+value.id;
                        var cell1 = row.insertCell(0);
                        cell1.innerHTML = '<b>'+'Fco'+' '+value.numero_frasco+':'+'</b>';
                        var cell2 = row.insertCell(1);
                        cell2.innerHTML = value.descripcion;
                        if(value.observacion != null){
                            var cell3 = row.insertCell(2);
                            cell3.innerHTML = '<b>'+'Obs:'+' '+value.observacion+' '+'</b>';
                        }else{
                            var cell3 = row.insertCell(2);
                            cell3.innerHTML = value.observacion;
                        }

                        var cell4 = row.insertCell(3);
                        cell4.innerHTML = '<a href="javascript:eliminar_biopsia('+value.id+', '+id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';

                    });
                }

        })

    }

    function eliminar(id_h, id){
        var i = document.getElementById('tdiag'+id_h).rowIndex;
        document.getElementById("tdiagnostico"+id).deleteRow(i);

        $.ajax({
          type: 'delete',
          url:"{{url('cie10/eliminar')}}/"+id_h,  //epicrisis.eliminar
          data: {'_token': '{{ csrf_token() }}'},
          datatype: 'json',

          success: function(data){

          },
          error: function(data){

          }
        });
    }


    //Elimina la Prescripcion Biopsias
    function eliminar_biopsia(id_h, id){

        var i = document.getElementById('tbio'+id_h).rowIndex;
        document.getElementById("tbiopsia"+id).deleteRow(i);

        $.ajax({
          type: 'delete',
          url:"{{url('hc4_prescripcion_biopsia/eliminar')}}/"+id_h,  //Biopsias.eliminar
          data: {'_token': '{{ csrf_token() }}'},
          datatype: 'json',

          success: function(data){

          },
          error: function(data){

          }
        });

    }

    function imprimir_etiquetas_frascos(id_procedimiento, url){
        var tabla = document.getElementById('tbiopsia' + id_procedimiento);
        var frascos = tabla ? tabla.querySelectorAll('tr[id^="tbio"]') : [];

        if (frascos.length === 0) {
            alert('Agregue al menos un frasco de biopsia antes de imprimir las etiquetas.');
            return;
        }

        window.open(url, '_blank', 'noopener');
    }


    function guardar_biopsia_frasco(id_procedimiento, id_hc, div1, div2){
        $.ajax({
            type: 'post',
            url:"{{route('procedimiento.agregar_biopsia')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'hcid': id_hc,'hc_id_procedimiento': id_procedimiento,'descripcion_frasco': $("#descripcion_frasco"+id_procedimiento).val(),'detalle_observacion': $("#detalle_observacion"+id_procedimiento).val(),'id_paciente': '{{$paciente->id}}' },
            success: function(data){

                editarprocedimiento(id_procedimiento, '{{$paciente->id}}');

                console.log(data);
                var indexr = data.count-1
                var table = document.getElementById("tbiopsia"+id_procedimiento);
                var row = table.insertRow(indexr);
                row.id = 'tbio'+data.id;
                var cell1 = row.insertCell(0);
                cell1.innerHTML = '<b>'+'Fco'+' '+data.secuencia+':'+'</b>';
                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.biopsias;

                if(data.observacion != null){
                  var cell3 = row.insertCell(2);
                  cell3.innerHTML = '<b>'+'Obs:'+' '+data.observacion+' '+'</b>';
                }else{
                  var cell3 = row.insertCell(2);
                  cell3.innerHTML = data.observacion;
                }
                  var cell4 = row.insertCell(3);
                  cell4.innerHTML = '<a href="javascript:eliminar_biopsia('+data.id+', '+id_procedimiento+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';
                $('#'+div1).val('');
                $('#'+div2).val('');


             },
            error: function(data){

            }
        });
    }

    function cargar_cuad_diag(id_proc,variable_tiempo){

        $.ajax({
          type: 'post',
          url:"{{route('obtener.cuadclin_diagnostico')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          //data: $("#id_proced"+id_proc).serialize(),
          data: {'id_proced':id_proc},
          success: function(data){
            //console.log(data);
            tinyMCE.get('cuad_clinico'+id_proc+variable_tiempo).setContent(data.evol_cuadro_clinico);
            tinyMCE.get('diagnos'+id_proc+variable_tiempo).setContent(data.texto);

            //$('#cuad_clinico'+id_proc).val(data.evol_cuadro_clinico);
            //$('#diagnos'+id_proc).val(data.texto);

            //$("#evolucion_agregar"+id).html(datahtml+anterior);

          },
          error: function(data){
            console.log(data);
          }
        });

    }

    function actualiza_cuadclinico_biopsia(id_proc){

        $.ajax({
          type: 'post',
          url:"{{route('update.cuad_biopsia')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm_biopsia"+id_proc).serialize(),
          success: function(data){
            //console.log(data);
            $("#alerta_datos").fadeIn(1000);
            $("#alerta_datos").fadeOut(3000);
          },
          error: function(data){
            console.log(data);
          }
        });

    }

    function actualiza_diagnost_biopsia(id_proc){

        $.ajax({
          type: 'post',
          url:"{{route('update.diag_biopsia')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm_biopsia"+id_proc).serialize(),
          success: function(data){
            //console.log(data);
            $("#alerta_datos").fadeIn(1000);
            $("#alerta_datos").fadeOut(3000);
          },
          error: function(data){
            console.log(data);
          }
        });

    }


    function guardar_cie10_PRO(id_procedimiento, id_hc){
        //alert($("#pre_def").val());

        $.ajax({
            type: 'post',
            url:"{{route('procedimiento.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo"+id_procedimiento).val(), 'pre_def': $("#pre_def"+id_procedimiento).val(),'hcid': id_hc, 'hc_id_procedimiento': id_procedimiento, 'in_eg': $("#ing_egr"+id_procedimiento).val(), 'id_paciente': '{{$paciente->id}}'  },
            success: function(data){


                var indexr = data.count-1
                var table = document.getElementById("tdiagnostico"+id_procedimiento);
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
                cell5.innerHTML = '<a href="javascript:eliminar('+data.id+', '+id_procedimiento+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';

            },
            error: function(data){

                }
        })
    }

</script>
<div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  Guardado Correctamente
</div>
<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

      </div>
    </div>
</div>
<div class="modal fade" id="foto2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

      </div>
    </div>
</div>
<div class="modal fade" id="video" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">

      </div>
    </div>
</div>
<div class="box hc4-surgical-procedures-view">
  @include('hc4.partials.module_header', [
      'moduleTitle' => isset($moduleTitle) ? $moduleTitle : __('hc4.surgical_procedures'),
      'moduleIcon' => asset('/').'hc4/img/iconos/pendo.png',
      'paciente' => $paciente,
      'actionLabel' => isset($moduleActionLabel) ? $moduleActionLabel : __('hc4.agregarProcedimiento'),
      'actionOnClick' => 'agregar_procedimiento_quirugicos();'
  ])
  <!-- /.box-header -->
  <div class="box-body">
    <div class="col-12">
        <div class="row parent" >
            <span id="msn1" style="color: white; "></span>
            @foreach($procedimientos2 as $value)
                @php

                    $variable_tiempo  = rand(100000,999999);

                    if($paciente->sexo == 1){
                        $sexo = "MASCULINO";
                    }else{
                        $sexo = "FEMENINO";
                    }
                    $seguro =  \Sis_medico\Seguro::find($value->seguro_final);
                    $nombre_procedimiento = "";
                    $historia =\Sis_medico\Historiaclinica::find($value->id_hc);
                    $alergias = \Sis_medico\Paciente_Alergia::where('id_paciente', $historia->id_paciente)->get();
                    if($alergias == "[]"){
                        $alergia = "No";
                    }else{
                        $alergia  = "";
                        foreach ($alergias as $value_alergia) {
                            if($alergia == ""){
                                $alergia = $value_alergia->principio_activo->nombre;
                            }else{
                                $alergia = $alergia.", ".$value_alergia->principio_activo->nombre;
                            }

                        }
                    }

                    $procedimientos = \Sis_medico\hc_procedimientos::find($value->id_procedimiento);
                    //dd($value);
                    if($procedimientos->id_procedimiento != null){
                        $nombre_procedimiento = $procedimientos->procedimiento_completo->nombre_completo;
                    }
                    $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id_procedimiento)->get();

                    $mas = true;
                    $nombre_procedimiento = "";

                    foreach($adicionales as $value2)
                    {
                        if($mas == true){
                            $nombre_procedimiento = $nombre_procedimiento.$value2->procedimiento->nombre  ;
                            $mas = false;
                         }
                        else{
                            $nombre_procedimiento = $nombre_procedimiento.' + '.  $value2->procedimiento->nombre  ;
                         }
                    }
                    $edad = Carbon\Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
                    $contador = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('secuencia', 0)->orderBy('secuencia', 'DESC')->count();
                    if($contador == 0){
                        $cuadro_clinico ="<p>PACIENTE ".$sexo." DE ".$edad." AÑOS DE EDAD <br> APP: ".$paciente->antecedentes_pat." <br> APF: ".$paciente->antecedentes_fam."<br> APQX: ".$paciente->antecedentes_quir."<br> ALERGIAS: ".$alergia."<br></p>";
                        $input = [
                            'hc_id_procedimiento' => $value->id_procedimiento,
                            'hcid' => $value->id_hc,
                            'secuencia' => 0,
                            'cuadro_clinico' => $cuadro_clinico,
                            'motivo' => 'Evolucion Pre-Operatoria',
                            'fecha_ingreso' => ' ',
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                        ];
                        \Sis_medico\Hc_Evolucion::insert($input);
                    }
                    $contador2 = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('secuencia', 1)->orderBy('secuencia', 'DESC')->count();
                    if($contador2 == 0){
                        $input = [
                            'hc_id_procedimiento' => $value->id_procedimiento,
                            'hcid' => $value->id_hc,
                            'motivo' => 'Evolucion Post-Operatoria',
                            'secuencia' => 1,
                            'cuadro_clinico' => ' ',
                            'fecha_ingreso' => ' ',
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                        ];
                        \Sis_medico\Hc_Evolucion::insert($input);
                    }
                    //dd($value);
                @endphp
                <div class="col-12">
                    @php
                        if(!is_null($value->f_operacion)){
                            $fecha_r =  Date('Y-m-d',strtotime($value->f_operacion));
                        }else{
                            $fecha_r = Date('Y-m-d',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
                        }
                    @endphp
                    <div id="procedimiento_eliminado1{{$value->id_procedimiento}}" class="box hc4-surgical-procedure-card @if($fecha_r != date('Y-m-d') ) collapsed-box @endif">
                        <div class="box-header with-border hc4-surgical-summary">

                            @php
                                $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id_procedimiento)->get();
                                $mas = true;
                                $texto = "";

                                    foreach($adicionales as $value2)
                                    {
                                        if($mas == true){
                                         $texto = $texto.$value2->procedimiento->nombre  ;
                                         $mas = false;
                                         }
                                        else{
                                         $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                                         }
                                    }
                            @endphp
                            <div class="hc4-surgical-summary-grid">
                                <div class="hc4-surgical-summary-date">
                                    @if(!is_null($value->f_operacion))
                                        @php
                                        $dia =  Date('N',strtotime($value->f_operacion));
                                        $mes =  Date('n',strtotime($value->f_operacion)); @endphp
                                        <span>
                                        @if($dia == '1') Lunes
                                             @elseif($dia == '2') Martes
                                             @elseif($dia == '3') Miércoles
                                             @elseif($dia == '4') Jueves
                                             @elseif($dia == '5') Viernes
                                             @elseif($dia == '6') Sábado
                                             @elseif($dia == '7') Domingo
                                        @endif
                                            {{substr($value->f_operacion,8,2)}} de
                                        @if($mes == '1') Enero
                                             @elseif($mes == '2') Febrero
                                             @elseif($mes == '3') Marzo
                                             @elseif($mes == '4') Abril
                                             @elseif($mes == '5') Mayo
                                             @elseif($mes == '6') Junio
                                             @elseif($mes == '7') Julio
                                             @elseif($mes == '8') Agosto
                                             @elseif($mes == '9') Septiembre
                                             @elseif($mes == '10') Octubre
                                             @elseif($mes == '11') Noviembre
                                             @elseif($mes == '12') Diciembre
                                        @endif
                                            del {{substr($value->f_operacion,0,4)}}</span>
                                    @else
                                        @php
                                        $dia =  Date('N',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
                                        $mes =  Date('n',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini)); @endphp
                                        <span>
                                        @if($dia == '1') Lunes
                                             @elseif($dia == '2') Martes
                                             @elseif($dia == '3') Miércoles
                                             @elseif($dia == '4') Jueves
                                             @elseif($dia == '5') Viernes
                                             @elseif($dia == '6') Sábado
                                             @elseif($dia == '7') Domingo
                                        @endif
                                            {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,8,2)}} de
                                        @if($mes == '1') Enero
                                             @elseif($mes == '2') Febrero
                                             @elseif($mes == '3') Marzo
                                             @elseif($mes == '4') Abril
                                             @elseif($mes == '5') Mayo
                                             @elseif($mes == '6') Junio
                                             @elseif($mes == '7') Julio
                                             @elseif($mes == '8') Agosto
                                             @elseif($mes == '9') Septiembre
                                             @elseif($mes == '10') Octubre
                                             @elseif($mes == '11') Noviembre
                                             @elseif($mes == '12') Diciembre
                                        @endif
                                            del {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,0,4)}}</span>
                                    @endif
                                </div>
                                <div class="hc4-surgical-summary-text">
                                    <div>
                                        <span class="hc4-surgical-summary-label">Procedimiento:</span>
                                        <span>@if(!is_null($texto)) {{$texto}} @endif</span>
                                    </div>
                                </div>
                                <div class="hc4-surgical-summary-doctor">
                                    <div>
                                        <span class="hc4-surgical-summary-label">Dr (a)</span>
                                        <span>
                                        @if(!is_null($value->nombre1))
                                            {{$value->nombre1}} {{$value->apellido1}}
                                        @else
                                            @php
                                                $doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $value->hcid)->first();
                                            @endphp

                                            @if(!is_null($doc_id_hc))
                                                @if (!is_null($doc_id_hc->id_doctor1))
                                                    @php
                                                        $nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
                                                    @endphp

                                                    Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

                                                @else
                                                    @php
                                                        $doc_id_agenda = \Sis_medico\Agenda::where('id', $doc_id_hc->id_agenda)->first();

                                                        $nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
                                                    @endphp
                                                    Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
                                                @endif
                                            @endif
                                        @endif

                                        </span>
                                    </div>
                                </div>
                                <div class="hc4-surgical-summary-delete">
                                    <button type="button" class="hc4-surgical-action hc4-surgical-action--danger" onclick="eliminar_procedimiento({{$value->id_procedimiento}});" title="Eliminar procedimiento" aria-label="Eliminar procedimiento">
                                        <i class="fa fa-trash" aria-hidden="true"></i><span class="sr-only">Eliminar</span>
                                    </button>
                                </div>
                                <div class="box-tools hc4-surgical-summary-toggle">
                                    <button type="button" class="btn btn-info btn-sm hc4-surgical-toggle" data-widget="collapse" title="Alternar detalle" aria-label="Alternar detalle">
                                        <i class="fa @if($fecha_r != date('Y-m-d')) fa-plus @else fa-minus @endif"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <div class="box-body" style="background: white;">
                            <div class="row">

                                <div class="col-lg-12 col-sm-12 col-12 endo_interno" style="">
                                    <div class="box hc4-surgical-inner-card" style="margin-bottom: 0;">

                                        <div class="box-header with-border hc4-surgical-inner-header">
                                            <div class="hc4-surgical-inner-header-grid">
                                                <button type="button" class="hc4-surgical-action" onclick='editarprocedimiento(@json($value->id_procedimiento), @json((string) $paciente->id));'>
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i><span>Editar</span>
                                                </button>
                                                <span class="hc4-surgical-header-title">Detalles del procedimiento</span>
                                                <span aria-hidden="true"></span>
                                            </div>
                                        </div>
                                        <div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="procedimiento{{$value->id_procedimiento}}" >
                                            <div class="row">
                                                <div class="col-12">&nbsp;</div>
                                                <div class="col-12">
                                                    <span style="font-family: 'Helvetica general';">Procedimiento</span>
                                                </div>

                                                <div class="col-12">
                                                    <span>
                                                        {{$texto}}
                                                    </span>
                                                </div>

                                                <div class="col-12" style="padding-top: 10px">
                                                    <span style="font-family: 'Helvetica general';">Seguro</span>
                                                </div>
                                                @php
                                                    $hc_seguro = \Sis_medico\Seguro::where('id', $value->seguro_final)->first();
                                                    //dd($hc_seguro);
                                                @endphp
                                                <div class="col-12" style="padding-bottom: 10px"><span> @if(!is_null($hc_seguro)) {{$hc_seguro->nombre}} @endif</span>

                                                </div>



                                                <div class="col-12">&nbsp;</div>
                                                <div class="col-12">
                                                    <span style="font-family: 'Helvetica general';">Hallazgos</span>
                                                </div>
                                                <div class="col-12">
                                                    <span><?php echo strip_tags($value->hallazgos); ?></span>
                                                </div>
                                                <div class="col-12">&nbsp;</div>
                                                <div class="col-12">
                                                    <span style="font-family: 'Helvetica general';">Conclusiones</span>
                                                </div>
                                                <div class="col-12">
                                                    <span><?php if (!is_null($value->conclusion)) {echo strip_tags($value->conclusion);} else {echo ' &nbsp;&nbsp;';}?></span>
                                                </div>
                                                <div class="col-4">
                                                    <span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
                                                </div>
                                                <div class="col-4">
                                                    <span style="font-family: 'Helvetica general';">&nbsp;</span>
                                                </div>
                                                <div class="col-4">
                                                    <span style="font-family: 'Helvetica general';">&nbsp;</span>
                                                </div>
                                                <div class="col-4">
                                                    <span>Dr.
                                                        @if(!is_null($value->nombre1))
                                                            {{$value->nombre1}} {{$value->apellido1}}
                                                        @else
                                                            @php
                                                                $doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $value->hcid)->first();
                                                            @endphp

                                                            @if(!is_null($doc_id_hc))
                                                                @if (!is_null($doc_id_hc->id_doctor1))
                                                                    @php
                                                                        $nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
                                                                    @endphp

                                                                    Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

                                                                @else
                                                                    @php
                                                                        $doc_id_agenda = \Sis_medico\Agenda::where('id', $doc_id_hc->id_agenda)->first();

                                                                        $nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
                                                                    @endphp
                                                                    Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                
                                <!-- <div class="col-lg-4 col-sm-12 col-12 endo_interno2" style="">
                                    <div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
                                        <div class="box-header with-border" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
                                            <div class="row">
                                                <div class="col-3" style="margin-right: 10px">
                                                    <div class="btn" style="color: white">
                                                        <a class="fa fa-pencil-square-o "  onclick="editar_imagenes({{$value->id_protocolo}})" ><span style="font-size: 13px">&nbsp;Editar</span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-8" style="text-align: center">
                                                    <span>Imagenes Procedimientos</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                @php
                                                    $id_protocolo = $value->id_protocolo;
                                                    $imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->limit(8)->OrderBy('created_at', 'desc')->get()
                                                @endphp
                                                @foreach($imagenes as $imagen)
                                                <div class="col-md-6 col-sm-4 ">
                                                    @php
                                                        $explotar = explode( '.', $imagen->nombre);
                                                        $extension = end($explotar);
                                                    @endphp
                                                    @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG') || ($extension == 'bmp') || ($extension == 'BMP'))
                                                        <a data-toggle="modal" data-target="#foto2" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                            <img style="margin-bottom: 4px;"  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="98%">
                                                        </a><br>
                                                    @elseif(($extension == 'pdf') || ($extension == 'PDF'))
                                                        <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                            <img style="margin-bottom: 4px;"   src="{{asset('imagenes/pdf.png')}}" width="98%">
                                                        </a>
                                                    @elseif(($extension == 'mp4'))
                                                        <a data-toggle="modal" data-target="#video" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                            <img style="margin-bottom: 4px;"   src="{{asset('imagenes/video.png')}}" width="98%">
                                                        </a>
                                                    @else
                                                        @php
                                                            $variable = explode('/' , asset('/hc_ima/'));
                                                            $d1 = $variable[3];
                                                            $d2 = $variable[4];
                                                            $d3 = $variable[5];
                                                         @endphp
                                                        <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                            <img style="margin-bottom: 4px;"   src="{{asset('imagenes/office.png')}}" width="98%">
                                                        </a>
                                                    @endif
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div> -->




                                <div class="col-12" style="height: 5px;"></div>
                                <div class="col-12" style="padding-top: 15px">
                                    <div class="box box-primary hc4-surgical-evolution-history">
                                        <div class="box-header with-border hc4-surgical-history-header">
                                            <div class="hc4-surgical-history-header-grid">
                                                <button type="button" class="hc4-surgical-action" onClick="agregar_evolucion({{$value->id_procedimiento}});">
                                                    <img width="20" src="{{asset('/')}}hc4/img/iconos/agregar.png" alt=""><span>AGREGAR EVOLUCIÓN</span>
                                                </button>
                                                <span class="hc4-surgical-header-title">Historial de evoluciones del procedimiento</span>
                                                <div class="box-tools">
                                                    <button type="button" class="btn btn-info btn-sm hc4-surgical-toggle" data-widget="collapse" title="Alternar historial" aria-label="Alternar historial">
                                                        <i class="fa fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- /.box-tools -->
                                        </div>
                                        <div class="box-body" style="background: white;" id="evolucion_agregar{{$value->id_procedimiento}}">
                                            @php
                                                $evoluciones = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('estado', 1)->orderBy('secuencia', 'DESC')->get();

                                            @endphp
                                            @foreach($evoluciones as $evolucion)
                                            <div class="box hc4-surgical-evolution-card" id="evolucion_eliminar{{$evolucion->id}}">
                                                <div class="box-header with-border hc4-surgical-evolution-header">
                                                    <div class="hc4-surgical-evolution-header-grid">
                                                        <button type="button" class="hc4-surgical-action" onclick="editar_evolucion({{$evolucion->id}}, '{{$paciente->id}}');">
                                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i><span>Editar</span>
                                                        </button>
                                                        <span class="hc4-surgical-header-title">Detalles de la evolución</span>
                                                        <button type="button" class="hc4-surgical-action hc4-surgical-action--danger" onclick="eliminar_evolucion({{$evolucion->id}}, '{{$value->id_procedimiento}}');" title="Eliminar evolución" aria-label="Eliminar evolución">
                                                            <i class="fa fa-trash" aria-hidden="true"></i><span class="sr-only">Eliminar</span>
                                                        </button>
                                                        <div class="box-tools">
                                                            <button type="button" class="btn btn-info btn-sm hc4-surgical-toggle" data-widget="collapse" title="Alternar evolución" aria-label="Alternar evolución">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="evolucion{{$evolucion->id}}">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <span style="font-family: 'Helvetica general';">Motivo</span>
                                                        </div>
                                                        <div class="col-12">
                                                            <span>{{$evolucion->motivo}}</span>
                                                        </div>
                                                        <div class="col-12">&nbsp;</div>
                                                        <div class="col-12">
                                                            <span style="font-family: 'Helvetica general';">Detalle</span>
                                                        </div>
                                                        <div class="col-12">
                                                            <span><?php echo $evolucion->cuadro_clinico; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12" style="height: 5px;"></div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12" style="height: 5px;"></div>
                                <div class="col-12">
                                    <div class="box box-primary @if($fecha_r != date('Y-m-d') ) collapsed-box @endif" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                                        <div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                            <span> <b>Diagnostico CIE10</b></span>
                                            <div class="pull-right box-tools">
                                                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" aria-expanded="{{ $fecha_r == date('Y-m-d') ? 'true' : 'false' }}">
                                                    <i class="fa {{ $fecha_r == date('Y-m-d') ? 'fa-minus' : 'fa-plus' }}"></i></button>
                                            </div>
                                            <!-- /.box-tools -->
                                        </div>
                                        <div class="box-body" style="background: white;" >
                                            <form id="frm_cie{{$value->id_procedimiento}}">
                                                <div class="col-12">
                                                    <div class="row">

                                                        <input type="hidden" name="codigo" id="codigo{{$value->id_procedimiento}}">
                                                        <label for="cie10" class="col-lg-12 control-label" style="padding-left: 0px" ><b>Diagnóstico</b></label>

                                                        <div class="form-group col-lg-6 col-sm-12 " style="padding-left: 0px">
                                                            <input id="cie10{{$value->id_procedimiento}}" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="Diagnóstico" >
                                                        </div>
                                                        <div class="form-group col-lg-2 col-sm-4 " style="padding: 1px;">
                                                            <select id="pre_def{{$value->id_procedimiento}}" name="pre_def" class="form-control input-sm" required>
                                                                <option value="">Seleccione ...</option>
                                                                <option value="PRESUNTIVO">PRESUNTIVO</option>
                                                                <option value="DEFINITIVO">DEFINITIVO</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-lg-2 col-sm-4 " style="padding: 1px;">
                                                            <select id="ing_egr{{$value->id_procedimiento}}" name="pre_def" class="form-control input-sm" required>
                                                                <option value="">Seleccione ...</option>
                                                                <option value="INGRESO">INGRESO</option>
                                                                <option value="EGRESO">EGRESO</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-2 col-sm-4 " style="padding: 1px; padding-top: 3px">
                                                            <button type="button" id="bagregar{{$value->id_procedimiento}}" class="btn btn_agregar_diag btn-sm"><span class="glyphicon glyphicon-plus"> Agregar</span></button>
                                                        </div>


                                                        <div class="form-group col-md-12" >
                                                            <table id="tdiagnostico{{$value->id_procedimiento}}" class="table table-striped" style="font-size: 12px;">

                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!--Orden Biopsias-->
                                <div class="col-12" style="height: 5px;"></div>
                                <div class="col-12">
                                    <div class="box box-primary @if($fecha_r != date('Y-m-d') ) collapsed-box @endif" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                                        <div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                            <span> <b>Biopsias</b></span>
                                            <div class="pull-right box-tools">
                                                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" aria-expanded="{{ $fecha_r == date('Y-m-d') ? 'true' : 'false' }}">
                                                    <i class="fa {{ $fecha_r == date('Y-m-d') ? 'fa-minus' : 'fa-plus' }}"></i></button>
                                            </div>
                                        </div>
                                        <div class="box-body" style="background: white;">
                                            <form id="frm_biopsia{{$value->id_procedimiento}}">
                                                <input type="hidden" name="id_hc_proc" value="{{$value->id_procedimiento}}">
                                                <div class="col-12">
                                                    <div class="row">
                                                        <input type="hidden" name="codigo_biop" id="codigo_biop{{$value->id_procedimiento}}">
                                                        <div class="col-md-3 col-sm-3 col-4" style="padding-left: 0px">
                                                          <a target="_blank" class="btn btn-info btn_accion"  style=" width: 100%; height: 100%" href="{{ route('imprimir.biopsias_hc4', ['id' => $value->id_procedimiento,'id_hcid' => $value->id_hc,'id_doct' => $value->id]) }}">
                                                          <div class="col-md-12" style="text-align: center;">
                                                            <div class="row" style="margin-left: 0px; margin-right: 0px;">
                                                              <div class="class="col-12">
                                                                <img style="" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                                              </div>
                                                              <div class="col-md-6 col-sm-6 col-7">
                                                                <label style="font-size: 14px; ">Imprimir</label>
                                                              </div>
                                                            </div>
                                                          </div>
                                                          </a>
                                                        </div>

                                                        <div class="col-md-3 col-sm-3 col-4" style="padding-left: 0px">
                                                          <button type="button" class="btn btn-info btn_accion" style="width: 100%; height: 100%" onclick='imprimir_etiquetas_frascos(@json($value->id_procedimiento), @json(route("hc4.imprimir_etiquetas_frascos", ["id" => $value->id_procedimiento])));'>
                                                          <div class="col-md-12" style="text-align: center;">
                                                            <div class="row" style="margin-left: 0px; margin-right: 0px;">
                                                              <div class="class="col-12">
                                                                <img style="" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                                              </div>
                                                              <div class="col-md-6 col-sm-6 col-7">
                                                                <label style="font-size: 14px; ">Imprimir Etiquetas</label>
                                                              </div>
                                                            </div>
                                                          </div>
                                                          </button>
                                                        </div>


                                                        <div class="col-12" style="height: 8px;"></div>
                                                        <label for="pre" class="col-md-12 control-label" style="padding-left: 0px" ><b>Detalle</b></label>
                                                        <div class="form-group col-md-8 col-sm-12 " style="padding-left: 0px">
                                                           <input id="descripcion_frasco{{$value->id_procedimiento}}" type="text" class="form-control input-sm"  name="descripcion_frasco" value="{{old('descripcion_frasco')}}" required placeholder="Detalle Frasco">
                                                        </div>
                                                        <div class="form-group col-md-2 col-sm-4 " style="padding: 1px;">
                                                        </div>
                                                        <div class="form-group col-md-2 col-sm-4 " style="padding: 1px; padding-top: 3px">
                                                            <button onclick="guardar_biopsia_frasco('{{$value->id_procedimiento}}', '{{$value->id_hc}}', 'descripcion_frasco{{$value->id_procedimiento}}', 'detalle_observacion{{$value->id_procedimiento}}');" id="bagregar_frasco{{$value->id_procedimiento}}" type="button" class="btn btn_agregar_diag btn-sm"><span class="glyphicon glyphicon-plus"> Agregar</span></button>
                                                        </div>
                                                        <label for="observacion" class="col-md-12 control-label" style="padding-left: 0px" ><b>Observación</b></label>
                                                        <div class="form-group col-md-8 col-sm-12 " style="padding-left: 0px">
                                                           <input id="detalle_observacion{{$value->id_procedimiento}}" type="text" class="form-control input-sm"  name="detalle_observacion" value="{{old('detalle_observacion')}}" placeholder="Detalle Observación">
                                                        </div>
                                                        <div class="form-group col-md-12" >
                                                            <table id="tbiopsia{{$value->id_procedimiento}}" class="table table-striped" style="font-size: 12px;">
                                                            </table>
                                                        </div>
                                                        <div class="col-md-5">
                                                        </div>
                                                        <div class="col-md-7">
                                                            <a class="btn btn-info btn_ordenes" style="color: white; height: 100%;width: 80%" onClick="cargar_cuad_diag({{$value->id_procedimiento}},{{$variable_tiempo}});">
                                                                <div class="col-12">
                                                                    <label style="font-size: 16px;">Insertar Cuadro Cl&iacute;nico / Diagnostico</label>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <div class="col-md-12">
                                                          <span style="font-family: 'Helvetica general';font-size: 12px">CUADRO CLINICO:</span>
                                                          <div id="cuad_clinico{{$value->id_procedimiento}}{{$variable_tiempo}}" style="width: 100%; border: 2px solid #004AC1;">@if(!is_null($value->cuadro_clinico))<?php echo $value->cuadro_clinico ?>@endif
                                                          </div>
                                                          <input type="hidden" name="cua_cl" id="cua_cl{{$value->id_procedimiento}}{{$variable_tiempo}}" >
                                                        </div>
                                                        <div class="col-md-12">
                                                            <span style="font-family: 'Helvetica general';font-size: 12px">DIAGNOSTICO:</span>
                                                            <div id="diagnos{{$value->id_procedimiento}}{{$variable_tiempo}}" style="width: 100%; border: 2px solid #004AC1;">@if(!is_null($value->diagnosticos))<?php echo $value->diagnosticos ?>@endif
                                                            </div>
                                                            <input type="hidden" name="diag" id="diag{{$value->id_procedimiento}}{{$variable_tiempo}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Observacion Administrativa-->
                                <div class="col-12" style="height: 5px;"></div>
                                <div class="col-12">
                                    <div class="box box-primary @if($fecha_r != date('Y-m-d') ) collapsed-box @endif" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                                        <div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                            <span> <b>Observación Administrativa</b></span>
                                            <div class="pull-right box-tools">
                                                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" aria-expanded="{{ $fecha_r == date('Y-m-d') ? 'true' : 'false' }}">
                                                    <i class="fa {{ $fecha_r == date('Y-m-d') ? 'fa-minus' : 'fa-plus' }}"></i></button>
                                            </div>
                                            <!-- /.box-tools -->
                                        </div>
                                        <div class="box-body" style="background: white;" >
                                            @php
                                                $log_agenda = Sis_medico\Log_Agenda::where('id_agenda',$value->id_agenda)->where('descripcion','not like','%DESPLA%')->get();
                                                $cont = 0;
                                            @endphp
                                            @foreach($log_agenda as $log)
                                                    @if($cont == '0')
                                                        <p> {{$log->observaciones_ant}} {{$log->observaciones}}</p>
                                                    @else
                                                        <p> {{$log->observaciones}}</p>
                                                    @endif
                                                    @php $cont ++; @endphp


                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <!-- Egreso Paciente-->
                                <div class="col-12" style="height: 5px;"></div>
                                <div class="col-12">
                                    <div class="box box-primary @if($fecha_r != date('Y-m-d') ) collapsed-box @endif" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                                        <div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                            <span> <b>Egreso Paciente</b></span>
                                            <div class="pull-right box-tools">
                                                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" aria-expanded="{{ $fecha_r == date('Y-m-d') ? 'true' : 'false' }}">
                                                    <i class="fa {{ $fecha_r == date('Y-m-d') ? 'fa-minus' : 'fa-plus' }}"></i></button>
                                            </div>
                                            <!-- /.box-tools -->
                                        </div>
                                        <div class="box-body" style="background: white;" >
                                            @php
                                                $egreso = Sis_medico\HcEgreso::where('id_hc_procedimientos',$value->id_procedimiento)->first();
                                                if(is_null($egreso)){
                                                    $ip_cliente= $_SERVER["REMOTE_ADDR"];
                                                    $idusuario = Auth::user()->id;
                                                    $id_doctor_egreso = $value->id_doctor_examinador;
                                                    $id_egreso = Sis_medico\HcEgreso::insertGetId([
                                                        'id_hc_procedimientos'     => $value->id_procedimiento,
                                                        'ta'                       => '110/70',
                                                        'fc'                       => '80',
                                                        'fr'                       => "18",
                                                        'sato2'                    => '98',
                                                        't'                        => '37',
                                                        'condiciones'              => '1',
                                                        'conducido'                => '1',
                                                        'id_doctor_egreso'         => $id_doctor_egreso,
                                                        'ip_creacion'              => $ip_cliente,
                                                        'ip_modificacion'          => $ip_cliente,
                                                        'id_usuariocrea'           => $idusuario,
                                                        'id_usuariomod'            => $idusuario,
                                                    ]);
                                                    $egreso = Sis_medico\HcEgreso::find($id_egreso); 
                                                }
                                            @endphp
                                            <div class="col-12">
                                                <span style="font-family: 'Helvetica general';">Condiciones</span>
                                            </div>
                                            <div class="col-4">
                                                <select id="condiciones{{$value->id_procedimiento}}" name="condiciones" onchange="egreso({{$value->id_procedimiento}})">
                                                    <option @if($egreso->condiciones == 1) selected @endif value="1">Extubado</option>
                                                    <option @if($egreso->condiciones == 2) selected @endif value="2">Intubado</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <span style="font-family: 'Helvetica general';">Conducido</span>
                                            </div>
                                            <div class="col-4">
                                                <select id="conducido{{$value->id_procedimiento}}" name="conducido" onchange="egreso({{$value->id_procedimiento}})">
                                                    <option @if($egreso->conducido == 1) selected @endif value="1">Domicilio</option>
                                                    <option @if($egreso->conducido == 2) selected @endif value="2">Hospitalizacion</option>
                                                    <option @if($egreso->conducido == 3) selected @endif value="3">UCI</option>
                                                    <option @if($egreso->conducido == 4) selected @endif value="4">Critico ER</option>
                                                    <option @if($egreso->conducido == 5) selected @endif value="5">Morgue</option>
                                                </select>
                                            </div>
                                            @php /*
                                            <div class="col-12">
                                                <span style="font-family: 'Helvetica general';">Medico Egresa</span>
                                            </div>
                                            <div class="col-4">
                                                <select name="id_doctor_examinador" onchange="autoguardado({{$procedimiento->id}}, 1)">
                                                    @foreach($doctores as $value)
                                                        @if($value->training != 1)
                                                        <option
                                                        @if(!is_null($procedimiento->id_doctor_examinador))
                                                            @if($procedimiento->id_doctor_examinador == $value->id)
                                                                selected
                                                            @endif
                                                        @endif
                                                        value="{{$value->id}}"> {{$value->apellido1}} {{$value->apellido2}} {{$value->nombre1}} {{$value->nombre2}}
                                                        </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            */
                                            @endphp
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($fecha_r == date('Y-m-d') )
                <script type="text/javascript">
                    editarprocedimiento({{$value->id_procedimiento}}, '{{$paciente->id}}');
                </script>
                @endif

                <script type="text/javascript">

                    cargar_tabla_biopsias({{$value->id_procedimiento}});
                    cargar_tabla({{$value->id_procedimiento}});


                    $("#cie10{{$value->id_procedimiento}}").autocomplete({
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

                    $("#cie10{{$value->id_procedimiento}}").change( function(){
                        $.ajax({
                            type: 'post',
                            url:"{{route('epicrisis.cie10_nombre2')}}",
                            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                            datatype: 'json',
                            data: $("#cie10{{$value->id_procedimiento}}"),
                            success: function(data){
                                //console.log(data);
                                if(data!='0'){
                                    $('#codigo{{$value->id_procedimiento}}').val(data.id);
                                }

                            },
                            error: function(data){

                                }
                        })
                    });


                    $('#bagregar{{$value->id_procedimiento}}').click( function(event){
                        event.preventDefault();
                        if($('#pre_def{{$value->id_procedimiento}}').val()!='' ){
                            if($('#ing_egr{{$value->id_procedimiento}}').val()!='' ){
                                guardar_cie10_PRO({{$value->id_procedimiento}}, {{$value->id_hc}});
                                $('#ing_egr{{$value->id_procedimiento}}').val('');
                            }else{
                                alert("Seleccione Ingreso o Egreso");
                            }
                        }else{
                            alert("Seleccione Presuntivo o Definitivo");
                        }
                        $('#codigo{{$value->id_procedimiento}}').val('');
                        $('#cie10{{$value->id_procedimiento}}').val('');
                    });


                    tinymce.init({
                      selector: '#cuad_clinico{{$value->id_procedimiento}}{{$variable_tiempo}}',
                      inline: true,
                      menubar: false,
                      invalid_elements : 'table,tr,td',
                      content_style: ".mce-content-body {font-size:14px;}",

                      setup: function (editor) {
                          editor.on('init', function (e) {

                            var ed = tinyMCE.get('cuad_clinico{{$value->id_procedimiento}}{{$variable_tiempo}}');
                            $('#cua_cl{{$value->id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());


                          });
                      },

                      init_instance_callback: function (editor) {
                          editor.on('Change', function (e) {
                            var ed = tinyMCE.get('cuad_clinico{{$value->id_procedimiento}}{{$variable_tiempo}}');
                            $('#cua_cl{{$value->id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());
                            actualiza_cuadclinico_biopsia({{$value->id_procedimiento}})

                          });
                      }
                    });


                    tinymce.init({
                      selector: '#diagnos{{$value->id_procedimiento}}{{$variable_tiempo}}',
                      inline: true,
                      menubar: false,
                      invalid_elements : 'table,tr,td',
                      content_style: ".mce-content-body {font-size:14px;}",

                      setup: function (editor) {
                          editor.on('init', function (e) {

                            var ed = tinyMCE.get('diagnos{{$value->id_procedimiento}}{{$variable_tiempo}}');
                            $('#diag{{$value->id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());


                          });
                      },

                      init_instance_callback: function (editor) {
                          editor.on('Change', function (e) {
                            var ed = tinyMCE.get('diagnos{{$value->id_procedimiento}}{{$variable_tiempo}}');
                            $('#diag{{$value->id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());
                            actualiza_diagnost_biopsia({{$value->id_procedimiento}})


                          });
                      }
                    });
                    

                </script>

            @endforeach

           
        </div>
    </div>
  </div>
  <!-- box-footer -->
</div>
