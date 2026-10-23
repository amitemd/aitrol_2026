<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

@php
 $variable_timepicker = 'a'.rand(0,999);
@endphp
<style type="text/css">
    .parent{
        overflow-y:scroll;
        height: 600px;
    }
    .mostrar{
        position: fixed;
        bottom: 5px;
        right: 0;
        z-index: 999;
        width: 50%;
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

    .mce-edit-focus,
    .mce-content-body:hover {
        outline: 2px solid #2276d2 !important;
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
    .desabilitar{
        pointer-events: none;
        cursor:no-drop;
    }
    .fincita{
        background-color: #dc3545;
        /*background-color: #ef3838;*/
        padding: 10px 20px;
        margin-top: 15px;
        margin-left: -70%;
        text-align: center;
        font-weight: ;
        font-family: 'Helvetica general3';
        font-size: 15px;
        border-radius: 10px;
        color: white;
        display: block;
    }
    .inlines{
        display: initial;
        margin-right: 10px;
        padding: 10px 0px;
    }
</style>

<div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  Guardado Correctamente
</div>
<div class="modal fade" id="vademecum" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog " role="document" style="max-width: 80% !important;">
      <div class="modal-content">

      </div>
    </div>
</div>

<script type="text/javascript">

    var remoto_href = '';
    $('.vademecum').on('click', function() {
        if(remoto_href != $(this).data('remote')) {
            remoto_href2 = $(this).data('remote');
            $('#vademecum').removeData('bs.modal');
            contenido = $('#nombre_generico'+remoto_href2).val();
            if(contenido == ""){
                remoto_href = "{{ asset('hc4/revisar/informacion/vademecun/')}}/"+remoto_href2+"/torbi10";
            }else{
                remoto_href = "{{ asset('hc4/revisar/informacion/vademecun/')}}/"+remoto_href2+"/"+contenido;
            }

            $('#vademecum').find('.modal-body').empty();
            //$('#vademecum .modal-content').load(remoto_href);

            $.ajax({
                type: 'post',
                url:"{{ asset('hc4/revisar/informacion/vademecun/')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: { texto: contenido, variable: remoto_href2 },
                success: function(data){
                    $('#vademecum .modal-content').html(data);
                },
                error: function(data){
                }
            });
            $('#vademecum').modal('show')
        }
    });
    $('')
    function buscar_nombre_medicina(div){

        $.ajax({
            type: 'post',
            url:"{{route('buscar_nombre2.receta')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#nombre_generico"+div),
            success: function(data){
                console.log(data);
                if(data!='0'){
                    if(data.dieta == 1 ){
                        var dosis = data.dosis;
                        if(null == data.dosis){
                          dosis = '';
                        }
                        if(data.dosis == "null"){
                          dosis = '';
                        }
                        anterior = tinyMCE.get("tprescripcion"+div).getContent();
                        tinyMCE.get("tprescripcion"+div).setContent(anterior+ data.value +': \n' +dosis);
                        $("#prescripcion"+div).val(tinyMCE.get("tprescripcion"+div).getContent());
                          cambiar_receta_2(div);
                          //console.log("dieta");
                    }
                    if(data.dieta == 0){
                      Crear_detalle(data, div);
                      console.log("medicina");
                    }
                    $("#nombre_generico"+div).val('');

                }
            },
            error: function(data){
            }
        });
    }
    function Crear_detalle(med, div){
        var js_cedula = document.getElementById("id_paciente").value;
        var id = div.slice(0, -6);
        //alert (id);
        $.ajax({
            type: 'get',
            url:"{{url('detalle_receta/detalle_crear')}}"+"/"+id+"/"+med.id+"/"+js_cedula,
            datatype: 'json',
            success: function(data){
            if(data == 1){
                if(med.genericos == null){
                  anterior2 = tinyMCE.get('trp'+div).getContent();
                    var keywords = ['cie10-receta'];
                    var resultado = "";
                    var pos = -1;
                    keywords.forEach(function(element){
                    pos = anterior2.search(element.toString());
                      if(pos!=-1){
                        resultado += " Palabra "+element+ "encontrada en la posición "+pos;
                      }
                    });
                    var cantidad = med.cantidad;
                    if(null == med.cantidad){
                      cantidad = '';
                    }
                    if(med.cantidad == "null"){
                      cantidad = '';
                    }

                    var genericos = med.genericos;

                    if(null == med.genericos){
                      genericos = '';
                    }
                    if(med.genericos == "null"){
                      genericos = '';
                    }

                    //En caso de que no exista.
                    if(pos === -1 && resultado === ""){
                        if(genericos == ''){
                            tinyMCE.get('trp'+div).setContent(anterior2 +'\n'+ med.value +': ' +cantidad);
                            $('#rp'+div).val(tinyMCE.get('trp'+div).getContent());
                        }else{
                            tinyMCE.get('trp'+div).setContent(anterior2 +'\n'+ med.value +"("+genericos+")"+': ' +cantidad);
                            $('#rp'+div).val(tinyMCE.get('trp'+div).getContent());
                        }

                    }else{
                        pos = pos-12;
                        if(genericos == ''){
                            tinyMCE.get('trp'+div).setContent(anterior2.substr(0, pos) +'\n'+ med.value +':  ' +med.cantidad +anterior2.substr(pos));
                        }else{
                            tinyMCE.get('trp'+div).setContent(anterior2.substr(0, pos) +'\n'+ med.value +"("+genericos+")"+':  ' +med.cantidad +anterior2.substr(pos));
                        }
                        $('#rp'+div).val(tinyMCE.get('trp'+div).getContent());
                    }
                    //fin de receta
                    //anterior = $('#prescripcion').val();
                    anterior = tinyMCE.get('tprescripcion'+div).getContent();
                    //console.log(anterior);
                    //$('#prescripcion').empty().html(anterior +'\n'+ med.value +':  ' +med.dosis);
                    var dosis = med.dosis;
                    if(null == med.dosis){
                      dosis = '';
                    }
                    if(med.dosis == "null"){
                      dosis = '';
                    }
                    tinyMCE.get('tprescripcion'+div).setContent(anterior +'\n'+ med.value +':  ' +dosis);

                    $('#prescripcion'+div).val(tinyMCE.get('tprescripcion'+div).getContent());
                    cambiar_receta_2(div);
                }else{
                    //anterior2 = $('#rp').val();
                    anterior2 = tinyMCE.get('trp'+div).getContent();
                    //codigo cie10 de posicion de receta
                    var keywords = ['cie10-receta'];
                    var resultado = "";
                    var pos = -1;

                    keywords.forEach(function(element) {
                        //En caso de existir se asigna la posición en pos
                        pos = anterior2.search(element.toString());
                        //Si existe
                        if(pos!=-1){
                            resultado += " Palabra "+element+ "encontrada en la posición "+pos;
                        }

                    });

                    var cantidad = med.cantidad;
                    if(null == med.cantidad){
                      cantidad = '';
                    }
                    if(med.cantidad == "null"){
                      cantidad = '';
                    }

                    //En caso de que no exista.
                    if(pos === -1 && resultado === ""){

                        tinyMCE.get('trp'+div).setContent(anterior2 +'\n'+ med.value +" ("+med.genericos+")"+': ' +cantidad);
                        $('#rp'+div).val(tinyMCE.get('trp'+div).getContent());
                    }else{
                        pos = pos-12;
                        tinyMCE.get('trp'+div).setContent(anterior2.substr(0, pos) +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.cantidad +anterior2.substr(pos));
                        $('#rp'+div).val(tinyMCE.get('trp'+div).getContent());
                    }
                    //fin de receta cie10
                    //anterior = $('#prescripcion').val();
                    anterior = tinyMCE.get('tprescripcion'+div).getContent();
                    //$('#prescripcion').empty().html(anterior +'\n'+ med.value +"("+med.genericos+")"+':  ' +med.dosis);
                    var dosis = med.dosis;
                    if(null == med.dosis){
                      dosis = '';
                    }
                    tinyMCE.get('tprescripcion'+div).setContent(anterior +'\n'+ med.value +':  ' +dosis);
                    $('#prescripcion'+div).val(tinyMCE.get('tprescripcion'+div).getContent());
                    cambiar_receta_2(div);
                }
            }
            else{
                    $('#index'+div).empty().html(data);
                }

            },
            error: function(data){
                 console.log(data);
            }
        });
    }
    function det_delete(id, id_receta){
    var var_tiempo = document.getElementById("var_tiempo").value;
    //alert(var_tiempo);
    $.ajax({
        type: 'get',
        url:"{{url('hc4/receta_detalle/eliminar_detalle')}}/"+id_receta+"/"+id, //receta.editar_detalle
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: "html",
        success: function(datahtml){
            $("#index"+var_tiempo).html(datahtml);
        },
        error: function(data){
            }
    });  
  }
</script>
<script type="text/javascript">

    function guardar_consulta(id_paciente, id_agenda){
        //alert(id_agenda);
        $.ajax({
            //async: true,
            type: "GET",
            url: "{{route('consulta.crear_nueva_consulta')}}/"+id_paciente+'/'+id_agenda,
            data: "",
            datatype: "html",
            success: function(datahtml){
                //alert("!! CONSULTA CREADA !!");
                $("#area_trabajo").html(datahtml);
            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }
    function cambiar_receta_2(div){
        //alert("lalala");
        $("#bguardar"+div).click();
    }

    //Agregar Visita OMNI HOSPITAL
    function guardar_visita(id_paciente, id_agenda){
        $.ajax({
            type: "GET",
            url: "{{route('visita.crear_nueva_visita')}}/"+id_paciente+'/'+id_agenda,
            data: "",
            datatype: "html",
            success: function(datahtml){
                $("#area_trabajo").html(datahtml);
            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }
    //Fin


    function calcular_indice(id){
        var peso  =  document.getElementById('peso'+id).value;
        var estatura = document.getElementById('estatura'+id).value;
        var sexo = @if($paciente->sexo == 1){{$paciente->sexo}}@else{{"0"}}@endif;
        var edad = calcularEdad('{{$paciente->fecha_nacimiento}}');
        estatura2 = Math.pow((estatura/100), 2);
        peso_ideal = 21.45 * (estatura2);
        imc = peso/estatura2;
        gct = ((1.2 * imc) + (0.23 * edad) - (10.8 * sexo) - 5.4);
        var texto = "";
        if(imc < 16){
            texto = "Desnutrición";
        }
        else if(imc < 18){
            texto = "Bajo de Peso";
        }
        else if(imc < 25){
            texto = "Normal";
        }
        else if(imc < 27){
            texto = "Sobrepeso";
        }
        else if(imc < 30){
            texto = "Obesidad Tipo 1";
        }
        else if(imc < 40){
            texto = "Obesidad Clinica";
        }
        else{
            texto = "Obesidad Mordida";
        }
        $('#cimc'+id).val(texto);
        $('#gct'+id).val(gct.toFixed(2));
        $('#imc'+id).val(imc.toFixed(2));
        $('#peso_ideal'+id).val(peso_ideal.toFixed(2));
    }

    function datos_child_pugh(id){
        dato1 = parseInt($('#ascitis'+id).val());
        dato2 = parseInt($('#albumina'+id).val());
        dato3 = parseInt($('#encefalopatia'+id).val());
        dato4 = parseInt($('#bilirrubina'+id).val());
        dato5 = parseInt($('#inr'+id).val());
        cantidad = dato1+ dato2+dato3+dato4+dato5;
        $('#puntaje'+id).val(cantidad);
        if(cantidad >= 5 && cantidad<=6){
            $('#clase'+id).val('A');
            $('#sv1'+id).val('100%');
            $('#sv2'+id).val('85%');
        }else if(cantidad >= 7 && cantidad<=9){
            $('#clase'+id).val('B');
            $('#sv1'+id).val('80%');
            $('#sv2'+id).val('60%');
        }else if(cantidad >= 10 && cantidad<=15){
            $('#clase'+id).val('C');
            $('#sv1'+id).val('45%');
            $('#sv2'+id).val('35%');
        }
    }

    function cargar_tabla(id, variable_tiempo){
        $.ajax({
            url:"{{route('epicrisis.cargar2')}}/"+id,
            dataType: "json",
            type: 'get',
            success: function(data){
               // console.log(data);
                var table = document.getElementById("tdiagnostico"+id+variable_tiempo);

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
                    cell4.innerHTML = '<a href="javascript:eliminar('+value.id+', '+id+', '+variable_tiempo+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';
                    //alert(index);
                });
            }
        })
    }

    function eliminar(id_h, id, variable_tiempo){
        var i = document.getElementById('tdiag'+id_h).rowIndex;
        document.getElementById("tdiagnostico"+id+variable_tiempo).deleteRow(i);

        $.ajax({
          type: 'get',
          url:"{{url('cie10/eliminar')}}/"+id_h,  //epicrisis.eliminar
          datatype: 'json',

          success: function(data){
            //console.log(data);
          },
          error: function(data){
             //console.log(data);
          }
        });
    }

    function guardar_cie10_consulta(hcid, hc_id_procedimiento, div_receta, variable_tiempo){
        $.ajax({
         
            type: 'post',
            url:"{{route('hc4/epicrisis.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 
                    'codigo': $("#codigo"+hc_id_procedimiento+variable_tiempo).val(), 
                    'pre_def': $("#pre_def"+hc_id_procedimiento+variable_tiempo).val(), 
                    'hcid': hcid, 
                    'hc_id_procedimiento': hc_id_procedimiento, 
                    'in_eg': null, 'id_paciente': '{{$paciente->id}}' 
                },
            success: function(data){


                var indexr = data.count-1
                var table = document.getElementById("tdiagnostico"+hc_id_procedimiento+variable_tiempo);
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
                cell4.innerHTML = '<a href="javascript:eliminar('+data.id+', '+hc_id_procedimiento+', '+variable_tiempo+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';
                //$('#prescripcion').empty().html(anterior+ data.value +': \n' +data.dosis);
                //aqui va para la receta
                anterior = tinyMCE.get('trp'+div_receta).getContent();
                //$('#prescripcion').empty().html(anterior+ data.value +': \n' +data.dosis);
                tinyMCE.get('trp'+div_receta).setContent(anterior+ '<div class="cie10-receta" >'+data.cie10 +': \n' +data.descripcion+'</div>');
                $('#rp'+div_receta).val(tinyMCE.get('trp'+div_receta).getContent());
                console.log($('#rp'+div_receta).val());
                guardar_receta(hc_id_procedimiento);
                console.log('guardo');
                //();

            },
            error: function(data){
                    //console.log(data);
                }
        })
    }

    var edad;
    edad = calcularEdad('');

    function guardar_protocolo(id_hc_procedimiento, espid){
        //alert('Ingreso');
        if (espid=='8') {
            guardar_cardio(id_hc_procedimiento);
        }
        calcular_indice(id_hc_procedimiento);
        datos_child_pugh(id_hc_procedimiento);
        $.ajax({
          type: 'post',
          url:"{{route('consulta.modificacion')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm_evol"+id_hc_procedimiento).serialize(),
          success: function(data){
            //alert("!! CONSULTA ACTUALIZADA !!");
            console.log(data);
            $("#alerta_datos").fadeIn(1000);
            $("#alerta_datos").fadeOut(3000);
            //cargar_consulta();
          },
          error: function(data){
             console.log(data);
          }
        });
    }

    function guardar_receta(id_hc_procedimiento){
        $.ajax({
          type: 'post',
          url:"{{route('consulta.modificacion_receta')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm_evol"+id_hc_procedimiento).serialize(),
          success: function(data){
            //alert("!! CONSULTA ACTUALIZADA !!");
            console.log(data);
            $("#alerta_datos").fadeIn(1000);
            $("#alerta_datos").fadeOut(3000);
            //cargar_consulta();
          },
          error: function(data){
             console.log(data);
          }
        });
    }

    function guardar_cardio(id){
        calcular_indice(id);
        $.ajax({
          type: 'post',
          url:"{{route('cardiologia.crea_actualiza')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          data: $("#frm_evol"+id).serialize(),
          success: function(data){
            //console.log(data);
            var edad;
            fecha_nacimiento = $( "#fecha_nacimiento"+id ).val();
            edad = calcularEdad(fecha_nacimiento);
            $('#edad'+id).val( edad );
          },
          error: function(data){
          }
        });
    }
</script>
<input type="hidden" id="id_paciente" name="" value="{{$paciente->id}}">
<link rel="stylesheet" href="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.css")}}" />

<div class="box " style="border: 2px solid #004AC1; background-color: white; ">
    <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1; ">
        <div class="row">
            <div class="col-4">
                <h1 style="font-size: 15px; margin:0; background-color: #004AC1; color: white;" >
                    <img style="width: 35px; margin-left: 5px; margin-bottom: 5px" src="{{asset('/')}}hc4/img/iconos/pendo.png"> <b>CONSULTA</b>
                </h1>
            </div>
            <!--Agregar Visita OMNI HOSPITAL-->
            @if(($id_doctor_visita != 1307189140)&&($id_doctor_visita != 1314490929))
            <div class="col-4" style="padding-left: 0px">
                <div style="margin-bottom: 5px;text-align: left;">
                    <a class="btn btn-info btn-block" style="color: white;padding-left: 0px;padding-right: 0px; border: 2px solid white;color: white; background-color: green" onclick="guardar_visita('{{$paciente->id}}', 'no');">
                        <div class="row" style="margin-left: 0px; margin-right: 0px;">
                            <div class="col-12" style="padding-left: 0px;padding-right: 0px;">
                                <label style="font-size: 10px">VISITA OMNI HOSPITAL</label>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @else
              <div class="col-4" style="padding-left: 0px">
              </div>
            @endif
            <!-- Fin Agregar Visista OMNI HOSPITAL-->
            <div class="col-4" style="padding-left: 0px">
                <div style="margin-bottom: 5px;text-align: left;">
                    <a class="btn btn-info btn-block" style="color: white;padding-left: 0px;padding-right: 0px; border: 2px solid white;" onclick="guardar_consulta('{{$paciente->id}}', 'no');">
                        <div class="row" style="margin-left: 0px; margin-right: 0px; ">
                            <div class="col-12" style="padding-left: 0px;padding-right: 0px;">
                                <img width="20px" src="{{asset('/')}}hc4/img/iconos/agregar.png">
                                <label style="font-size: 10px">AGREGAR CONSULTA</label>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        @if(!is_null($paciente))
            <center>
                <div class="col-12" style="padding-top: 15px">
                    <h1 style="font-size: 14px; margin:0; background-color: #004AC1; color: white;padding-left: 20px" >
                        <b>PACIENTE : {{$paciente->apellido1}} {{$paciente->apellido2}}
                            {{$paciente->nombre1}} {{$paciente->nombre2}}
                        </b>
                    </h1>
                </div>
            </center>
        @endif
    </div>
    <div class="box-body" style="background-color: #56ABE3;">
        <div class="col-12">
            <div class="row parent infinite-scroll" >
                <span id="msn1" style="color: white; "></span>
                @if(count($procedimientos2)>0)
                    @foreach($procedimientos2 as $value)
                        @php
                        $DateAndTime = date('m-d-Y');  

                        $fechaini = date('m-d-Y', strtotime($value->fechaini));

                        //dd($DateAndTime);
                            $variable_tiempo  = rand(100000,999999);
                            $desabilitar ="";
                            if($value->proc_consul == 0 && is_null($value->hora_inicio) && $fechaini == $DateAndTime ){
                                $desabilitar = "disabled";
                            }
                            
                            if( $value->id_doctor_examinador == '1314490929'){
                                $desabilitar = "";
                            }
                        @endphp
                        <div  class="col-12" id="consulta{{$value->hc_id_procedimiento}}">
                            <div class="box @if(substr($value->fechaini,0,10) != date('Y-m-d'))  collapsed-box @endif " style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                                <div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                <div class="row">
                                   <div class="col-3">

                                    @php
                                        $evolucion = null;
                                        $evolucion = DB::table('hc_evolucion as e')->where('e.hcid',$value->hcid)->first();
                                        if($value->id_doctor_examinador != ""){
                                            $xdoctor = DB::table('users as us')->where('us.id', $value->id_doctor_examinador)->first();
                                        }else{
                                            $xdoctor = DB::table('users as us')->where('us.id', $value->id_doctor1)->first();
                                        }
                                        $fecha = "";
                                    @endphp
                                    @if(!is_null($value->fecha_atencion))
                                        @php
                                        $dia =  Date('N',strtotime($value->fecha_atencion));
                                        $mes =  Date('n',strtotime($value->fecha_atencion));
                                        @endphp
                                        <b>
                                        @php
                                            if($dia == '1'){
                                                $fecha = 'Lunes';
                                            }elseif($dia == '2'){
                                                $fecha = 'Martes';
                                            }elseif($dia == '3'){
                                                $fecha = 'Miércoles';
                                            }elseif($dia == '4'){
                                                $fecha = 'Jueves';
                                            }elseif($dia == '5'){
                                                $fecha = 'Viernes';
                                            }elseif($dia == '6'){
                                                $fecha = 'Sábado';
                                            }elseif($dia == '7'){
                                                $fecha = 'Domingo';
                                            }
                                            $fecha = $fecha.' '.substr($value->fecha_atencion,8,2).' de ';
                                            if($mes == '1'){
                                                $fecha = $fecha.'Enero';
                                            }elseif($mes == '2'){
                                                $fecha = $fecha.'Febrero';
                                            }elseif($mes == '3'){
                                                $fecha = $fecha.'Marzo';
                                            }elseif($mes == '4'){
                                                $fecha = $fecha.'Abril';
                                            }elseif($mes == '5'){
                                                $fecha = $fecha.'Mayo';
                                            }elseif($mes == '6'){
                                                $fecha = $fecha.'Junio';
                                            }elseif($mes == '7'){
                                                $fecha = $fecha.'Julio';
                                            }elseif($mes == '8'){
                                                $fecha = $fecha.'Agosto';
                                            }elseif($mes == '9'){
                                                $fecha = $fecha.'Septiembre';
                                            }elseif($mes == '10'){
                                                $fecha = $fecha.'Octubre';
                                            }elseif($mes == '11'){
                                                $fecha = $fecha.'Noviembre';
                                            }elseif($mes == '12'){
                                                $fecha = $fecha.'Diciembre';
                                            }
                                            $fecha = $fecha.' del '.substr($value->fecha_atencion,0,4);
                                        @endphp
                                        {{$fecha}}</b>
                                    @else
                                        @php
                                        $dia =  Date('N',strtotime($value->fechaini));
                                        $mes =  Date('n',strtotime($value->fechaini));
                                        @endphp
                                        <b>
                                        @php
                                            if($dia == '1'){
                                                $fecha = 'Lunes';
                                            }elseif($dia == '2'){
                                                $fecha = 'Martes';
                                            }elseif($dia == '3'){
                                                $fecha = 'Miércoles';
                                            }elseif($dia == '4'){
                                                $fecha = 'Jueves';
                                            }elseif($dia == '5'){
                                                $fecha = 'Viernes';
                                            }elseif($dia == '6'){
                                                $fecha = 'Sábado';
                                            }elseif($dia == '7'){
                                                $fecha = 'Domingo';
                                            }
                                            $fecha = $fecha.' '.substr($value->fechaini,8,2).' de ';
                                            if($mes == '1'){
                                                $fecha = $fecha.'Enero';
                                            }elseif($mes == '2'){
                                                $fecha = $fecha.'Febrero';
                                            }elseif($mes == '3'){
                                                $fecha = $fecha.'Marzo';
                                            }elseif($mes == '4'){
                                                $fecha = $fecha.'Abril';
                                            }elseif($mes == '5'){
                                                $fecha = $fecha.'Mayo';
                                            }elseif($mes == '6'){
                                                $fecha = $fecha.'Junio';
                                            }elseif($mes == '7'){
                                                $fecha = $fecha.'Julio';
                                            }elseif($mes == '8'){
                                                $fecha = $fecha.'Agosto';
                                            }elseif($mes == '9'){
                                                $fecha = $fecha.'Septiembre';
                                            }elseif($mes == '10'){
                                                $fecha = $fecha.'Octubre';
                                            }elseif($mes == '11'){
                                                $fecha = $fecha.'Noviembre';
                                            }elseif($mes == '12'){
                                                $fecha = $fecha.'Diciembre';
                                            }
                                            $fecha = $fecha.' del '.substr($value->fechaini,0,4);
                                        @endphp
                                        {{$fecha}}</b>
                                    @endif

                                    </div>
                                    <div class="col-4">
                                        <div>
                                            <span style="font-family: 'Helvetica general'; font-size: 12px">Especialidad: </span>
                                            <span style="font-size: 12px">   {{$value->espe_nombre}}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                       <div>
                                            <span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a):</span>
                                            <span style="font-size: 12px">
                                                {{$xdoctor->nombre1}} {{$xdoctor->apellido1}}
                                            </span>
                                       </div>
                                    </div>
                                    <div class="pull-right box-tools" style="padding-top: 4px;">
                                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili" onclick="mostrar_detalle_receta{{$value->hc_id_procedimiento}}{{$variable_tiempo}}()">
                                        <i class="fa @if(substr($value->fechaini,0,10) != date('Y-m-d')) fa-plus @else fa-minus @endif"></i></button>
                                    </div>
                                  </div>
                                </div>
                                <div class="box-body" style="background: white;">
                                    <form id="frm_evol{{$value->hc_id_procedimiento}}">
                                        <input type="hidden" name="id_paciente" value="{{$paciente->id}}">
                                        <input type="hidden" name="id_hc_procedimiento" value="{{$value->hc_id_procedimiento}}">
                                        <div class="col-12" style="padding: 1px;">
                                            <div class="row">
                                                <div class="col-7">
                                                    <b>Fecha Visita: </b>
                                                 
                                                    @if($value->proc_consul ==0 )
                                                        {{$fecha}}
                                                        <b><br>Hora: <input type="hidden" value="{{$value->fechaini}}" name="fecha_doctor"></b>{{substr($value->fechaini,10,10)}}@else
                                                        <div style="border: 2px solid #004AC1; padding-top: 1px" class="input-group date datetimepicker2{{$variable_timepicker}}" id="datetimepicker<?php echo e($value->hc_id_procedimiento); ?>{{$variable_tiempo}}" data-target-input="nearest" >
                                                            <input  class="form-control datetimepicker-input" data-target="#datetimepicker<?php echo e($value->hc_id_procedimiento); ?>{{$variable_tiempo}}" value="@if(!is_null($evolucion->fecha_doctor)){{date('Y/m/d h:i', strtotime($evolucion->fecha_doctor))}}@else{{date('Y/m/d h:i', strtotime($value->fechaini))}}@endif"  name="fecha_doctor"/>
                                                            <div class="input-group-append" data-target="#datetimepicker<?php echo e($value->hc_id_procedimiento); ?>{{$variable_tiempo}}" data-toggle="datetimepicker" @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" type="text" @endif>
                                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-5" style="font-size: 12px">
                                                    @if(substr($value->fechaini,0,10) == date('Y-m-d'))
                                                        @if($value->proc_consul == 0)
                                                           @if($value->id_doctor_examinador != '1314490929')
                                                                @php 
                                                                    $habInicio ="";
                                                                    $habFin="";
                                                                    $cursorIni="";
                                                                    $cursorFin="";
                                                                    $habilitar="";

                                                                    if(!is_null($value->hora_inicio)){
                                                                        $habInicio ="desabilitar";
                                                                        $cursorIni="cursor: no-drop;";
                                                                        $habilitar = "habilitado";                                                           
                                                                    }
                                                                    if($value->hora_fin != null || is_null($value->hora_inicio)){
                                                                        $habFin ="desabilitar";
                                                                        $cursorFin="cursor: no-drop;";
                                                                        $habilitar="desabilitado";
                                                                    }
                                                                    //$idusuario = Auth::user()->id;
                                                                    //dd($idusuario);
                                                                  
                                                                @endphp
                                                                
                                                                <div style="{{$cursorIni}}" id="divcursorIni" class="inlines" >
                                                                    <a style="{{$cursorIni}}"  type="button" id="btn_inicio" class="{{$habInicio}} btn btn-success btn-xs" onclick="inicio_cita({{$value->hcid}})"> Inicio Cita</a> 
                                                                </div>
                                                                <div style="{{$cursorFin}}" id="divcursorfin" class="inlines" >
                                                                    <a style="{{$cursorFin}} " type="button" id="btn_fin" class="{{$habFin}}  btn btn-danger btn-xs"  onclick="fin_cita({{$value->hcid}})">  Fin Cita </a>
                                                                </div>
                                                               <!-- @if(is_null($value->hora_fin) && !is_null($value->hora_inicio))
                                                                    <label id="alerta_fin" class="fincita col-md-12">No  ha finalizado la cita</label>
                                                                @endif -->

                                                            @endif
                                                        @endif  
                                                    @endif
                                                    <b>
                                                    @if($value ->proc_consul=='1')
                                                        Tipo:PROCEDIMIENTO
                                                        @if(!is_null($evolucion))
                                                            @php
                                                            $procedimiento_evolucion  =  Sis_medico\hc_procedimientos::find($evolucion->hc_id_procedimiento);
                                                                if($procedimiento_evolucion != null){
                                                                   if($procedimiento_evolucion->id_procedimiento_completo != null){
                                                                    echo $procedimiento_evolucion->procedimiento_completo->nombre_general;
                                                                   }
                                                                }
                                                            @endphp
                                                        @endif
                                                    @endif
                                                    </b>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="col-12" style="padding: 1px;">
                                        <div class="row">
                                               <div class="col-8"><h6><b>Datos Generales</b></h6></div>

                                               </div>
                                               <div class="col-12">
                                                   <div class="row">
                                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                                            <label for="id_doctor_examinador" class="control-label" style="font-size: 12px">Medico Examinador @if(substr($value->fechaini,0,10) != date('Y-m-d')) @endif</label>
                                                            <select class="form-control input-sm" style="width: 100%; font-size: 12px; border: 2px solid #004AC1;" name="id_doctor_examinador" id="id_doctor_examinador{{$value->hc_id_procedimiento}}" @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif   >
                                                                @foreach($doctores as $doc)
                                                                    <option @if($value->id_doctor_examinador == $doc->id) selected @elseif($value->id_doctor_examinador == "" && $doc->id == $value->id_doctor1) selected @endif value="{{$doc->id}}" >{{$doc->apellido1}} @if($doc->apellido2 != "(N/A)"){{ $doc->apellido2}}@endif {{ $doc->nombre1}} @if($doc->nombre2 != "(N/A)"){{ $doc->nombre2}}@endif</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                                            <label for="id_seguro" class="control-label" style="font-size: 12px">Seguro</label>
                                                            <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="id_seguro" id="id_seguro{{$value->hc_id_procedimiento}}" @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif  >
                                                                @foreach($seguros as $seg)
                                                                    <option @if($value->id_seguro == $seg->id) selected @endif value="{{$seg->id}}" >{{$seg->nombre}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                                            <label for="id_seguro" class="control-label" style="font-size: 12px">Cortesia</label>
                                                            <select {{$desabilitar}} id="consulta_cortesia_paciente{{$value->hc_id_procedimiento}}" name="consulta_cortesia_paciente" class="form-control input-sm" required style="background-color: #ccffcc; font-size: 11px; border: 2px solid #004AC1;">
                                                            @if(!is_null($value->cortesia))
                                                                <option @if($value->cortesia=='NO'){{'selected '}}@endif value="NO">NO</option>
                                                                <option @if($value->cortesia=='SI'){{'selected '}}@endif value="SI">SI</option>
                                                            @else
                                                                <option value="NO" selected >NO</option>
                                                                <option value="SI" >SI</option>
                                                            @endif
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3 col-6 has-error" style="padding: 1px;">
                                                            <label for="observaciones" class="control-label" style="font-size: 12px">Observaciones</label>
                                                            <textarea  class="form-control input-sm" id="observaciones{{$value->hc_id_procedimiento}}" name="observaciones" style="width: 100%;background-color: #ffffb3; border: 2px solid #004AC1;" rows="1" @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >{{strip_tags($value->observaciones)}}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Default box -->
                                              
                                                <input type="hidden"  value="{{$value->tipo_cita}}">
                                                <div class="box box-solid collapsed-box">
                                                    <div class="box-header">
                                                        <h3 class="box-title"><b>Información Administrativa</b></h3>
                                                        <div class="box-tools pull-right">
                                                            <button class="btn btn-default btn-sm" data-widget="collapse" onclick="ver_log_agenda('{{$value->id_agenda}}')"><i class="fa fa-plus"></i></button>
                                                            <button class="btn btn-default btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
                                                        </div>
                                                    </div>
                                                    <div style="display: none;" class="box-body">
                                                        <div id="log_agenda_{{$value->id_agenda}}"></div>
                                                    </div><!-- /.box-body -->
                                                </div><!-- /.box -->

                                                <!--Agregar Visita OMNI HOSPITAL-->
                                                @if(!is_null($value->procedencia))
                                                <input type="hidden" name="ubicacion_omni" value="{{$value->procedencia}}">
                                                    <div class="col-12">
                                                        <div class="row">
                                                            <div class="col-md-3 col-6" style="padding: 1px;">
                                                                <label for="ubicacion" class="control-label" style="font-family: 'Helvetica general'; font-size: 12px">Ubicaci&oacute;n</label><br>
                                                                <input readonly class="form-control input-sm" style="font-family: 'Helvetica general';width: 100%; border: 2px solid #004AC1;background-color: #ADFF2F;" value="{{$value->procedencia}}">
                                                            </div>
                                                            <div class="col-md-4 col-6" style="padding: 1px;">
                                                                <label for="sala" class="control-label" style="font-family: 'Helvetica general'; font-size: 12px">Sala</label>
                                                                <input class="form-control input-sm" name="sala" id="sala{{$value->hc_id_procedimiento}}" style="font-family: 'Helvetica general';width: 100%; border: 2px solid #004AC1;background-color: #ADFF2F;" rows="1" value="{{$value->sala_hospital}}" @if(substr($value->fechaini,0,10) == date('Y-m-d'))
                                                                onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif>
                                                            </div>
                                                            <div class="col-md-5 col-6" style="padding: 1px;">
                                                                <label for="estado" class="control-label" style="font-family: 'Helvetica general'; font-size: 12px">Estado</label>
                                                                <select id="estado_visita{{$value->hc_id_procedimiento}}" name="estado_visita" class="form-control input-sm" required style="font-family: 'Helvetica general';background-color: #ADFF2F; font-size: 12px; border: 2px solid #004AC1;" @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif>
                                                                @if(!is_null($value->estado_cita))
                                                                    <option @if($value->estado_cita=='4'){{'selected '}}@endif value="4">INGRESO</option>
                                                                    <option @if($value->estado_cita=='5'){{'selected '}}@endif value="5">ALTA</option>
                                                                    <option @if($value->estado_cita=='6'){{'selected '}}@endif value="6">EMERGENCIA</option>
                                                                    <option @if($value->estado_cita=='7'){{'selected '}}@endif value="7">POST POEM</option>
                                                                @else
                                                                    <option value="4" selected >INGRESO</option>
                                                                    <option value="5" >ALTA</option>
                                                                    <option value="6" >EMERGENCIA</option>
                                                                    <option value="7" >POST POEM</option>
                                                                @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <!-- Fin Agregar Visita OMNI HOSPITAL -->
                                        </div>
                                        <div class="col-12">
                                            <input type="hidden" name="id_agenda" value="{{$value->id_agenda}}">
                                            <input type="hidden" name="hcid" value="{{$evolucion->hcid}}">
                                            <input type="hidden" name="id_evolucion" value="{{$evolucion->id}}">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h6><b>Preparación</b></h6>
                                                    <div class="row">
                                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                                            <label for="presion" class="control-label" style="font-size: 12px">P. Arterial</label>
                                                            <input class="form-control input-sm" name="presion" id="pre{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->presion}}" @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif {{$desabilitar}} >
                                                        </div>
                                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                                            <label for="pulso" class="control-label" style="font-size: 12px">Pulso</label>
                                                            <input class="form-control input-sm" name="pulso" id="pul{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->pulso}}" @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif {{$desabilitar}}  >
                                                        </div>
                                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                                            <label for="temperatura" class="control-label" style="font-size: 12px">Temperatura (ºC)</label>
                                                            <input class="form-control input-sm" name="temperatura" id="tem{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->temperatura}}" @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif {{$desabilitar}} >
                                                        </div>
                                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                                            <label for="o2" class="control-label" style="font-size: 12px">SaO2:</label>
                                                            <input class="form-control input-sm" name="o2" id="sao{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->o2}}" @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif {{$desabilitar}} >
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                                            <label for="estatura" class="control-label" style="font-size: 12px">Estatura (cm)</label>
                                                            <input class="form-control input-sm" id="estatura{{$value->hc_id_procedimiento}}" name="estatura" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->altura}}" onchange="calcular_indice({{$value->hc_id_procedimiento}});" @if($value->estado_cita!='4') readonly="yes" @endif  @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif {{$desabilitar}} >
                                                        </div>
                                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                                            <label for="peso" class="control-label" style="font-size: 12px">Peso (kg)</label>
                                                            <input class="form-control input-sm" id="peso{{$value->hc_id_procedimiento}}" name="peso" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->peso}}" onchange="calcular_indice({{$value->hc_id_procedimiento}});" @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif {{$desabilitar}} >
                                                        </div>
                                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                                            <label for="perimetro" class="control-label" style="font-size: 12px">Perimetro Abdominal</label>
                                                            <input class="form-control input-sm" id="perimetro{{$value->hc_id_procedimiento}}" name="perimetro" style="width: 100%; border: 2px solid #004AC1;" rows="4"  value="{{$value->perimetro}}" @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif {{$desabilitar}} >
                                                        </div>
                                                        <div class="col-md-3 col-6" style="padding: 1px;" >
                                                            <label for="peso_ideal" class="control-label" style="font-size: 12px">Peso Ideal (kg)</label>
                                                            <input class="form-control input-sm" id="peso_ideal{{$value->hc_id_procedimiento}}" name="peso_ideal" disabled style="width: 100%; border: 2px solid #004AC1;" rows="4"  @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4 col-6" style="padding: 1px;">
                                                            <label for="gct" class="control-label" style="font-size: 12px">% GCT RECOMENDADO</label>
                                                            <input class="form-control input-sm" id="gct{{$value->hc_id_procedimiento}}" name="gct" disabled style="width: 100%; border: 2px solid #004AC1;" rows="4"  @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >
                                                        </div>
                                                        <div class="col-md-4 col-6" style="padding: 1px;">
                                                            <label for="imc" class="control-label" style="font-size: 12px">IMC</label>
                                                            <input class="form-control input-sm" id="imc{{$value->hc_id_procedimiento}}" name="imc" disabled style="width: 100%; border: 2px solid #004AC1;" rows="4"  @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >
                                                        </div>
                                                        <div class="col-md-4 col-6" style="padding: 1px;">
                                                            <label for="cimc" class="control-label" style="font-size: 12px">Categoria IMC</label>
                                                            <input class="form-control input-sm" id="cimc{{$value->hc_id_procedimiento}}" name="cimc" disabled style="width: 100%; border: 2px solid #004AC1;" rows="4"  @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >
                                                        </div>
                                                    </div>
                                                    <h6><b>Clasificación Child Pugh</b></h6>
                                                    @php
                                                        $idusuario  = Auth::user()->id;
                                                        $ip_cliente = $_SERVER["REMOTE_ADDR"];

                                                        $child_pugh = null;
                                                        $child_pugh = \Sis_medico\hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();

                                                        if (is_null($child_pugh) && (!is_null($evolucion))) {

                                                            $input_child_pugh = [
                                                                'id_hc_evolucion' => $evolucion->id,
                                                                'ip_modificacion' => $ip_cliente,
                                                                'id_usuariomod'   => $idusuario,
                                                                'id_usuariocrea'  => $idusuario,
                                                                'examen_fisico'   => 'ESTADO CABEZA Y CUELLO:
                                                                                                            ESTADO TORAX:
                                                                                                            ESTADO ABDOMEN:
                                                                                                            ESTADO MIEMBROS SUPERIORES:
                                                                                                            ESTADO MIEMBROS INFERIORES:
                                                                                                            OTROS: ',
                                                                'ip_creacion'     => $ip_cliente,
                                                                'created_at'      => date('Y-m-d H:i:s'),
                                                                'updated_at'      => date('Y-m-d H:i:s'),
                                                            ];
                                                            \Sis_medico\hc_child_pugh::insert($input_child_pugh);

                                                            $child_pugh = \Sis_medico\hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();
                                                        }
                                                    @endphp
                                                    <input type="hidden" name="id_child_pugh" value="{{$child_pugh->id}}">
                                                    <div class="row">
                                                        <!--<input type="hidden" name="id_child_pugh" value="">-->
                                                        <div class="col-md-2 col-6" style="padding: 1px;">
                                                            <label for="ascitis" class="control-label" style="font-size: 12px">Ascitis</label>
                                                            <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="ascitis" id="ascitis{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});" {{$desabilitar}}>

                                                                <option @if(!is_null($child_pugh)) @if($child_pugh->ascitis == 1) selected @endif @endif value="1" >Ausente</option>
                                                                <option @if(!is_null($child_pugh)) @if($child_pugh->ascitis == 2) selected @endif @endif value="2" >Leve</option>
                                                                <option @if(!is_null($child_pugh)) @if($child_pugh->ascitis == 3) selected @endif @endif value="3" >Moderada</option>

                                                            </select>
                                                        </div>
                                                        <div class="col-md-2 col-6" style="padding: 1px;">
                                                            <label for="encefalopatia" class="control-label" style="font-size: 12px">Encefalopatia</label>
                                                            <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="encefalopatia" id="encefalopatia{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});" {{$desabilitar}}>

                                                                <option @if(!is_null($child_pugh)) @if($child_pugh->encefalopatia == 1) selected @endif @endif value="1" >No</option>
                                                                <option @if(!is_null($child_pugh)) @if($child_pugh->encefalopatia == 2) selected @endif @endif value="2" >Grado 1 a 2</option>
                                                                <option @if(!is_null($child_pugh)) @if($child_pugh->encefalopatia == 3) selected @endif @endif value="3" >Grado 3 a 4</option>

                                                            </select>
                                                        </div>
                                                        <div class="col-md-2 col-6" style="padding: 1px;">
                                                            <label for="albumina" class="control-label" style="font-size: 12px">Albúmina(g/l)</label>
                                                            <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="albumina" id="albumina{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});"{{$desabilitar}}>

                                                                <option @if(!is_null($child_pugh)) @if($child_pugh->albumina == 1) selected @endif @endif value="1" >&gt; 3.5</option>
                                                                <option @if(!is_null($child_pugh)) @if($child_pugh->albumina == 2) selected @endif @endif value="2" >2.8 - 3.5</option>
                                                                <option @if(!is_null($child_pugh)) @if($child_pugh->albumina == 3) selected @endif @endif value="3" >&lt; 2.8</option>

                                                            </select>
                                                        </div>
                                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                                            <label for="bilirrubina" class="control-label" style="font-size: 12px">Bilirrubina(mg/dl)</label>
                                                            <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="bilirrubina" id="bilirrubina{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});"{{$desabilitar}}>

                                                                <option @if(!is_null($child_pugh)) @if($child_pugh->bilirrubina == 1) selected @endif @endif value="1" >&lt; 2</option>
                                                                <option @if(!is_null($child_pugh)) @if($child_pugh->bilirrubina == 2) selected @endif @endif value="2" >2 - 3</option>
                                                                <option @if(!is_null($child_pugh)) @if($child_pugh->bilirrubina == 3) selected @endif @endif value="3" >&gt; 3</option>

                                                            </select>
                                                        </div>
                                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                                            <label for="inr" class="control-label" style="font-size: 12px">Protrombina% (INR)</label>
                                                            <select   class="form-control input-sm" style="width: 100%; border: 2px solid #004AC1;" name="inr" id="inr{{$value->hc_id_procedimiento}}" onchange="datos_child_pugh({{$value->hc_id_procedimiento}});" {{$desabilitar}}>

                                                                <option @if(!is_null($child_pugh)) @if($child_pugh->inr == 1) selected @endif @endif value="1" >&gt; 50 (&lt; 1.7)</option>
                                                                <option @if(!is_null($child_pugh)) @if($child_pugh->inr == 2) selected @endif @endif value="2" >30 - 50 (1.8 - 2.3)</option>
                                                                <option @if(!is_null($child_pugh)) @if($child_pugh->inr == 3) selected @endif @endif value="3" >&lt; 30 (&gt; 2.3)</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                                            <label for="puntaje" class="control-label" style="font-size: 12px">Puntaje</label>
                                                            <input class="form-control input-sm" id="puntaje{{$value->hc_id_procedimiento}}" name="puntaje" disabled style="width: 100%; border: 2px solid #004AC1;" readonly="yes" >
                                                        </div>
                                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                                            <label for="clase" class="control-label" style="font-size: 12px">Clase</label>
                                                            <input class="form-control input-sm" id="clase{{$value->hc_id_procedimiento}}" disabled style="width: 100%; border: 2px solid #004AC1;"  readonly="yes">
                                                        </div>
                                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                                            <label for="sv1" class="control-label" style="font-size: 12px">SV1 Año:</label>
                                                            <input class="form-control input-sm" id="sv1{{$value->hc_id_procedimiento}}" disabled style="width: 100%; border: 2px solid #004AC1;"  readonly="yes">
                                                        </div>
                                                        <div class="col-md-3 col-6" style="padding: 1px;">
                                                            <label for="sv2" class="control-label" style="font-size: 12px">SV2 años:</label>
                                                            <input class="form-control input-sm" id="sv2{{$value->hc_id_procedimiento}}" disabled style="width: 100%; border: 2px solid #004AC1;" readonly="yes">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12" style="padding: 1px;">
                                            <label for="motivo" class="control-label" style="font-size: 14px"><b>Motivo</b></label>
                                            <textarea {{$desabilitar}} name="motivo" id="motivo{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="3"  @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif > @if(!is_null($value)){{$value->motivo}}@endif</textarea>
                                        </div>
                                        <div class="col-12" style="padding: 1px;">
                                            <label for="thistoria_clinica" class="control-label" style="font-size: 14px"><b>Evolución</b></label>
                                            <div  id="thistoria_clinica{{$value->hc_id_procedimiento}}{{$variable_tiempo}}" style="border: 2px solid #004AC1;">@if(!is_null($value))<?php echo $value->cuadro_clinico ?>@endif</div>
                                            <input type="hidden" name="historia_clinica" id="historia_clinica{{$value->hc_id_procedimiento}}{{$variable_tiempo}}" >
                                        </div>
                                        <div class="col-12" style="padding: 1px;">
                                            <label for="tresultado_exam" class="control-label" style="font-size: 14px"><b>Resultados de Exámenes y Procedimientos Diagnósticos</b></label>
                                            <div  id="tresultado_exam<?php echo e($value->hc_id_procedimiento); ?>{{$variable_tiempo}}" style="border: 2px solid #004AC1;">@if(!is_null($value))<?php echo $value->resultado ?>@endif</div>
                                            <input type="hidden" name="resultado_exam" id="resultado_exam<?php echo e($value->hc_id_procedimiento); ?>{{$variable_tiempo}}"{{$desabilitar}} >
                                        </div>

                                        <div class="col-12" style="padding: 1px;">
                                            <label for="examen_fisico" class="control-label" style="font-size: 14px"><b>Examen Fisico</b></label>
                                            <textarea {{$desabilitar}} id="examen_fisico{{$value->hc_id_procedimiento}}{{$variable_tiempo}}" name="examen_fisico" style="width: 100%; border: 2px solid #004AC1;" rows="7"  @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif > @if(!is_null($child_pugh)){{strip_tags($child_pugh->examen_fisico)}}@endif</textarea>
                                        </div>
                                        @if($value->espid=='8')
                                            @php
                                                $cardiologia = DB::table('hc_cardio')->where('hcid',$value->hcid)->first();
                                            @endphp
                                            <div class="col-12" style="padding: 1px;">
                                                <label for="resumen" class="control-label"><b>Resumen</b></label>
                                                <textarea {{$desabilitar}} id="resumen{{$value->hc_id_procedimiento}}" name="resumen" style="width: 100%; border: 2px solid #004AC1;" rows="1"  @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >@if(!is_null($cardiologia)){{$cardiologia->resumen}}@endif {{$desabilitar}}</textarea>
                                            </div>
                                            <div class="col-12" style="padding: 1px;">
                                                <label for="plan_diagnostico" class="control-label"><b>Plan Diagnóstico</b></label>
                                                <textarea {{$desabilitar}} id="plan_diagnostico{{$value->hc_id_procedimiento}}" name="plan_diagnostico" style="width: 100%; border: 2px solid #004AC1;" rows="1" @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >@if(!is_null($cardiologia)){{$cardiologia->plan_diagnostico}}@endif {{$desabilitar}}</textarea>
                                            </div>
                                            <div class="col-12" style="padding: 1px;">
                                                <label for="plan_tratamiento" class="control-label"><b>Plan Tratamiento</b></label>
                                                <textarea {{$desabilitar}} id="plan_tratamiento{{$value->hc_id_procedimiento}}" name="plan_tratamiento" style="width: 100%; border: 2px solid #004AC1;" rows="1" @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >@if(!is_null($cardiologia)){{$cardiologia->plan_tratamiento}}@endif {{$desabilitar}}</textarea>
                                            </div>
                                        @endif
                                        <input type="hidden" name="codigo" id="codigo{{$value->hc_id_procedimiento}}{{$variable_tiempo}}">
                                        <div class="col-12" style="padding: 1px;">
                                            <label for="indicacion" class="control-label" style="font-size: 14px"><b>Indicaciones</b></label>
                                            <textarea {{$desabilitar}} name="indicacion" id="indicacion{{$value->hc_id_procedimiento}}" style="width: 100%; border: 2px solid #004AC1;" rows="3"@if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif>@if(!is_null($value)){{$value->indicaciones}}@endif</textarea>
                                        </div>
                                        <label for="cie10" class="col-12 control-label" style="padding-left: 0px; @if($value->proc_consul=='1') display: none; @endif"><b>Diagnóstico</b>
                                        </label>
                                        <div class="row">
                                            <div class="form-group col-md-6 col-sm-6 col-12" style="padding: 15px; @if($value->proc_consul=='1') display: none; @endif">
                                                <input id="cie10{{$value->hc_id_procedimiento}}{{$variable_tiempo}}" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase; border: 2px solid #004AC1;" onkeyup="javascript:this.value=this.value.toUpperCase(); " required placeholder="Diagnóstico" @if($value->estado_cita!='4') readonly="yes" @endif {{$desabilitar}}>
                                            </div>

                                             <div class="form-group col-md-3 col-sm-6 col-6" style=" padding: 15px; @if($value->proc_consul=='1') display: none; @endif">
                                                <select id="pre_def{{$value->hc_id_procedimiento}}{{$variable_tiempo}}" name="pre_def" class="form-control input-sm" {{$desabilitar}}>
                                                    <option value="">Seleccione ...</option>
                                                    <option value="PRESUNTIVO">PRESUNTIVO</option>
                                                    <option value="DEFINITIVO">DEFINITIVO</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 col-sm-12 col-6" >
                                                <center>
                                                    <div class="col-md-12 col-sm-6 col-12" style="padding: 15px; ">
                                                        @if($value->estado_cita=='4')
                                                        <button id="bagregar{{$value->hc_id_procedimiento}}{{$variable_tiempo}}" class="btn btn_agregar_diag btn-sm col-10" style=" color: white; @if($value->proc_consul=='1') display: none; @endif"><span class="glyphicon glyphicon-plus"> Agregar</span>
                                                        </button>
                                                        @endif
                                                    </div>
                                                </center>
                                            </div>
                                        </div>
                                        <div class="form-group col-12" style="padding: 1px;margin-bottom: 0px;">
                                            <table id="tdiagnostico{{$value->hc_id_procedimiento}}{{$variable_tiempo}}" class="table table-striped" style="font-size: 12px;">

                                            </table>
                                        </div>
                                        <div class="col-12" style="padding: 1px;">
                                            <label for="examenes_realizar" class="control-label" style="font-size: 14px"><b>Examenes a Realizar</b></label>
                                            <textarea {{$desabilitar}} id="examenes_realizar{{$value->hc_id_procedimiento}}" name="examenes_realizar" style="width: 100%; border: 2px solid #004AC1;" rows="2"  @if($value->estado_cita!='4') readonly="yes" @endif @if(substr($value->fechaini,0,10) == date('Y-m-d')) onchange="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})" @endif >@if(!is_null($value)){{$value->examenes_realizar}}@endif </textarea>
                                        </div>
                                        <!-- RECETA-->
                                        @php

                                            $rec = \Sis_medico\hc_receta::where('id_hc', $value->hcid)->OrderBy('created_at', 'desc')->first();
                                            if(is_null($rec)){
                                                $ip_cliente= $_SERVER["REMOTE_ADDR"];
                                                $input_hc_receta = [
                                                    'id_hc' => $value->hcid,
                                                    'ip_creacion' => $ip_cliente,
                                                    'id_usuariocrea' => '9666666666',
                                                    'ip_modificacion' => $ip_cliente,
                                                    'id_usuariomod' => '9666666666',
                                                    'created_at' => date('Y-m-d H:i:s'),
                                                    'updated_at' => date('Y-m-d H:i:s'),
                                                ];
                                               $id_receta = \Sis_medico\hc_receta::insertGetId($input_hc_receta);
                                            }else{
                                                $id_receta = $rec->id;
                                            }


                                        @endphp
                                        <input type="hidden" name="id_receta" value="{{$id_receta}}">

                                        <script type="text/javascript">
                                            @if(substr($value->fechaini,0,10) == date('Y-m-d'))  
                                                $( document ).ready(function() {
                                                    //console.log( "ready!" );
                                                    mostrar_detalle_receta{{$value->hc_id_procedimiento}}{{$variable_tiempo}}();
                                                });   
                                            @endif
                                            function mostrar_detalle_receta{{$value->hc_id_procedimiento}}{{$variable_tiempo}}()
                                            {
                                                $.ajax({
                                                    type: 'get',
                                                    url:"{{route('recetas.receta_detalle_index',['id_receta' => $id_receta])}}",
                                                    datatype: 'json',
                                                    success: function(datahtml){
                                                        $("#index{{$id_receta}}{{$variable_tiempo}}").html(datahtml);
                                                    },
                                                    error: function(data){
                                                    }
                                                });
                                            }    
                                        </script>    
                                        <div class="col-md-12" >
                                            <div class="row">
                                                <div class="col-md-4 col-sm-4 col-4" style="margin: 10px; padding: 0px">
                                                  <a target="_blank" class="btn btn-info btn_accion"  style=" width: 100%; height: 100%" href="{{ route('hc_receta.imprime_hc4', ['id' => $id_receta, 'tipo' => '2']) }}">
                                                  <div class="col-md-12" style="text-align: center; ">
                                                    <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                                      <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                                        <img style="" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                                      </div>
                                                      <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                                                        <label style="font-size: 14px; ">Imprimir Membretada</label>
                                                      </div>
                                                    </div>
                                                  </div>
                                                  </a>
                                                </div>
                                                <div class="col-md-4 col-sm-4 col-4" style="margin: 10px; padding: 0px">
                                                  <a target="_blank" class="btn btn-info btn_accion"  style=" width: 100%; height: 100%" href="{{ route('hc_receta.imprime_hc4', ['id' => $id_receta, 'tipo' => '1']) }}">
                                                    <div class="col-md-12" style="text-align: center">
                                                      <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                                        <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                                          <img style="color: black" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                                        </div>
                                                        <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                                                          <label style="font-size: 14px">Imprimir</label>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </a>
                                                </div>
                                                @if($value->espid==5)
                                                <div class="col-md-2 col-sm-2 col-2" style="margin: 10px; padding: 0px">
                                                  <a target="_blank" class="btn btn-success"  style=" width: 100%; height: 100%" href="{{ route('hc_receta.imprime_hc4', ['id' => $id_receta, 'tipo' => '3']) }}">
                                                    <div class="col-md-12" style="text-align: center">
                                                      <div class="row" style="padding-left: 0px; padding-right: 0px;">
                                                        <div class="col-md-2" style="padding-left: 0px; padding-right: 5px" >
                                                          <img style="color: black" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                                        </div>
                                                        <div class="col-md-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
                                                          <label style="font-size: 14px">CIR</label>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </a>
                                                </div>
                                                @endif
                                            </div>
                                        </div>



                                        <div class="col-md-11 col-sm-11 col-11" style="margin-left: 8px;margin-right: 14px;margin-left: 14px;padding-right: 0px;padding-left: 0px;border-radius: 3px;">
                                          <!--Contenedor Historial de Recetas-->
                                            <div  style=" color: white; font-family: 'Helvetica general'; font-size: 16px; ">
                                                <div class="box-title" style=" margin-left: 10px">
                                                <div class="row">
                                                    <div class="col-md-4 col-sm-4 col-4" style="margin-left: 0px; ">

                                                    </div>
                                                    <div class="col-12">
                                                        <div class="row">
                                                          <div class="col-12">

                                                            <div class="form-group">
                                                              <label style="font-family: 'Helvetica general';" for="inputid" class="control-label">Medicina</label>
                                                              <div class="row">
                                                                <div class=" col-md-9 col-sm-9 col-12">
                                                                  <input style="margin-bottom: 10px" value="" type="text" class="form-control" name="nombre_generico" id="nombre_generico{{$id_receta}}{{$variable_tiempo}}" placeholder="Nombre" {{$desabilitar}}>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <button type="button" class="btn btn-primary col-md-8 col-sm-8 col-12" style="background-color: #004AC1;"
                                                                    onClick="buscar_nombre_medicina('{{$id_receta}}{{$variable_tiempo}}')">
                                                                        <span class="fa fa-plus"></span> Agregar
                                                                   </button>
                                                                   <br>
                                                                   <a  style="background-color: #004AC1;" class="btn btn-primary col-md-8 col-sm-8 col-12 vademecum" data-toggle="modal_vade" data-target="#vademecum" data-remote="{{$id_receta}}{{$variable_tiempo}}">
                                                                        Revisar Vademecum
                                                                    </a>
                                                                </div>
                                                              </div>
                                                            </div>
                                                          </div>
                                                        </div>
                                                    </div>
                                                    <div style="font-family: 'Helvetica general'; color: black" class="col-md-2">Alergias:</div>
                                                    <div class="col-md-10">
                                                      @if($alergiasxpac->count()==0)
                                                       <b>NO TIENE </b>
                                                      @else
                                                        @foreach($alergiasxpac  as $ale)<span style="margin-bottom: 20px; padding-left: 10px; padding-right: 10px; border-radius: 5px;background-color: red;color: white"> {{$ale->principio_activo->nombre}}</span>&nbsp;&nbsp;
                                                        @endforeach
                                                      @endif
                                                    </div>
                                                    <div id="index{{$id_receta}}{{$variable_tiempo}}" class="col-md-12" style="padding: 0;">
                                                    </div>
                                                </div>
                                                </div>
                                            </div>

                                            <div class="contenedor2" id="receta{{$id_receta}}{{$variable_tiempo}}" style="padding-bottom: 20px; padding-right: 15px">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b style="font-family: 'Helvetica general';" class="box-title">Rp</b></span>
                                                            <div  id="trp{{$id_receta}}{{$variable_tiempo}}" style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #004AC1;">
                                                              <?php if (!is_null($rec)): ?>
                                                                <?php echo $rec->rp ?>
                                                              <?php endif;?>
                                                            </div>

                                                            <input type="hidden" value="{{$rec->rp}}" name="rp" id="rp{{$id_receta}}{{$variable_tiempo}}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span><b style="font-family: 'Helvetica general';" class="box-title">Prescripcion</b></span>
                                                            <div  id="tprescripcion<?php echo ($id_receta); ?>{{$variable_tiempo}}"  style="border: solid 1px;min-height: 200px;border-radius:3px;border: 2px solid #004AC1;">
                                                                <?php if (!is_null($rec)): ?>
                                                                <?php echo $rec->prescripcion ?>
                                                                <?php endif;?>
                                                            </div>

                                                            <input type="hidden" value="{{$rec->prescripcion}}" name="prescripcion" id="prescripcion{{$id_receta}}{{$variable_tiempo}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        
                                        <input type="hidden" id="var_tiempo" value="{{$id_receta}}{{$variable_tiempo}}">
                                        <script type="text/javascript">

                                            cargar_tabla({{$value->hc_id_procedimiento}}, '{{$variable_tiempo}}');
                                            calcular_indice({{$value->hc_id_procedimiento}});
                                            datos_child_pugh({{$value->hc_id_procedimiento}});

                                            $('#edad{{$value->hc_id_procedimiento}}').val( edad );

                                            tinymce.init({
                                            selector: '#thistoria_clinica{{$value->hc_id_procedimiento}}{{$variable_tiempo}}',
                                            inline: true,
                                            menubar: false,
                                            content_style: ".mce-content-body {font-size:14px;}",

                                            //aqui ando
                                            @if($value->proc_consul == 0 && $value->hora_inicio == null)
                                                readonly: 1,
                                            @endif
                                            @if( $value->id_doctor_examinador == '1314490929')
                                                readonly: 0,
                                            @endif

                                            

                                            @if($value->estado_cita!='4')
                                            readonly: 1,
                                            @else

                                            @if($desabilitar =="")
                                                readonly: 0,
                                            @endif


                                            setup: function (editor) {
                                                editor.on('init', function (e) {
                                                   var ed = tinyMCE.get('thistoria_clinica{{$value->hc_id_procedimiento}}{{$variable_tiempo}}');
                                                   //alert(ed.getContent());
                                                    $('#historia_clinica{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());

                                                });
                                            },
                                            @endif

                                            init_instance_callback: function (editor) {
                                                editor.on('Change', function (e) {
                                                    var ed = tinyMCE.get('thistoria_clinica{{$value->hc_id_procedimiento}}{{$variable_tiempo}}');
                                                    $('#historia_clinica{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());
                                                    //guardar_protocolo({{$value->hc_id_procedimiento}});
                                                    @if(substr($value->fechaini,0,10) == date('Y-m-d'))
                                                        guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})
                                                    @endif
                                                });
                                              }
                                            });


                                            tinymce.init({
                                            selector: '#tresultado_exam{{$value->hc_id_procedimiento}}{{$variable_tiempo}}',
                                            inline: true,
                                            menubar: false,
                                            content_style: ".mce-content-body {font-size:14px;}",
                                            //aqui ando
                                            @if($value->proc_consul == 0 && $value->hora_inicio == null)
                                                readonly: 1,
                                            @endif

                                            @if( $value->id_doctor_examinador == '1314490929')
                                                readonly: 0,
                                            @endif
                                            

                                            @if($value->estado_cita!='4')
                                           
                                            readonly: 1,
                                            @else
                                            @if($desabilitar =="")
                                                readonly: 0,
                                            @endif

                                            setup: function (editor) {
                                                editor.on('init', function (e) {
                                                   var ed = tinyMCE.get('tresultado_exam{{$value->hc_id_procedimiento}}{{$variable_tiempo}}');
                                                   //alert(ed.getContent());
                                                    $('#resultado_exam{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());

                                                });
                                            },
                                            @endif

                                            init_instance_callback: function (editor) {
                                                editor.on('Change', function (e) {
                                                    var ed = tinyMCE.get('tresultado_exam{{$value->hc_id_procedimiento}}{{$variable_tiempo}}');
                                                    $('#resultado_exam{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());
                                                    //guardar_protocolo({{$value->hc_id_procedimiento}});
                                                    @if(substr($value->fechaini,0,10) == date('Y-m-d'))
                                                        guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})
                                                    @endif
                                                });
                                              }
                                            });



                                            $('#cie10{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').autocomplete({
                                            source: function( request, response )
                                            {
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
                                            });


                                            $('#cie10{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').change( function()
                                            {
                                            $.ajax({
                                                type: 'post',
                                                url:"{{route('epicrisis.cie10_nombre2')}}",
                                                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                                                datatype: 'json',
                                                data: $('#cie10{{$value->hc_id_procedimiento}}{{$variable_tiempo}}'),
                                                success: function(data){
                                                    if(data!='0'){

                                                        $('#codigo{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val(data.id);
                                                        // guardar_cie10({{$value->hc_id_procedimiento}}, {{$value->hcid}}, {{$value->hc_id_procedimiento}});
                                                    }
                                                },
                                                error: function(data){
                                                }
                                            })
                                            });


                                            $('#bagregar{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').click( function(){

                                                if($('#cie10{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val()!='' ){
                                                    if($('#pre_def{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val()!='' ){
                                                        @php
                                                                $rec = \Sis_medico\hc_receta::where('id_hc', $value->hcid)->OrderBy('created_at', 'desc')->first();
                                                        @endphp
                                                        guardar_cie10_consulta({{$value->hcid}}, {{$value->hc_id_procedimiento}}, '{{$rec->id}}{{$variable_tiempo}}', '{{$variable_tiempo}}');
                                                    }else{
                                                        alert("Seleccione Presuntivo o Definitivo");
                                                    }
                                                }else{
                                                    alert("Seleccione CIE10");
                                                }

                                                $('#codigo{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val('');
                                                $('#cie10{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val('');
                                                $('#pre_def{{$value->hc_id_procedimiento}}{{$variable_tiempo}}').val('');

                                            });
                                            tinymce.init({
                                                //aqui ando
                                            selector: '#tprescripcion{{$id_receta}}{{$variable_tiempo}}',
                                            inline: true,
                                            menubar: false,
                                            content_style: ".mce-content-body {font-size:14px;}",
                                            //readonly: 1,
                                           
                                            @if($value->proc_consul == 0 && $value->hora_inicio == null)
                                                readonly: 1,
                                            @endif
                                            @if( $value->id_doctor_examinador == '1314490929')
                                                readonly: 0,
                                            @endif
                                            @if($desabilitar =="")
                                                readonly: 0,
                                            @endif

                                              setup: function (editor){
                                                    editor.on('init', function (e){
                                                       var ed = tinyMCE.get('tprescripcion<?php echo e($id_receta); ?>{{$variable_tiempo}}');
                                                        $("#prescripcion<?php echo e($id_receta); ?>{{date('his')}}").val(ed.getContent());
                                                    });
                                              },

                                              init_instance_callback: function (editor){
                                                    editor.on('Change', function (e) {
                                                        var ed = tinyMCE.get('tprescripcion<?php echo e($id_receta); ?>{{$variable_tiempo}}');
                                                        $("#prescripcion<?php echo e($id_receta); ?>{{$variable_tiempo}}").val(ed.getContent());
                                                        guardar_receta('{{$value->hc_id_procedimiento}}');

                                                    });
                                              }
                                          });


                                            tinymce.init({
                                            selector: '#trp<?php echo e($id_receta); ?>{{$variable_tiempo}}',
                                            inline: true,
                                            menubar: false,
                                            content_style: ".mce-content-body {font-size:14px;}",
                                            //readonly: 1,
                                            //aqui ando
                                            @if($value->proc_consul == 0 && $value->hora_inicio == null)
                                                readonly: 1,
                                            @endif
                                            @if( $value->id_doctor_examinador == '1314490929')
                                                readonly: 0,
                                            @endif
                                            @if($desabilitar =="")
                                                readonly: 0,
                                            @endif
                                           

                                              setup: function (editor){
                                                  editor.on('init', function (e) {
                                                     var ed = tinyMCE.get('trp<?php echo e($id_receta); ?>{{$variable_tiempo}}');
                                                      $("#rp<?php echo e($id_receta); ?>{{date('his')}}").val(ed.getContent());
                                                  });
                                              },

                                              init_instance_callback: function (editor){
                                                  editor.on('Change', function (e) {
                                                      var ed = tinyMCE.get('trp<?php echo e($id_receta); ?>{{$variable_tiempo}}');
                                                      $("#rp<?php echo e($id_receta); ?>{{$variable_tiempo}}").val(ed.getContent());
                                                      guardar_receta('{{$value->hc_id_procedimiento}}');
                                                  });
                                              }
                                          });

                                              $("#nombre_generico<?php echo e($id_receta); ?>{{$variable_tiempo}}").autocomplete({
                                                source: function( request, response ) {
                                                  $.ajax({
                                                    url:"{{route('buscar_nombre.receta')}}",
                                                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                                                    data: {
                                                        term: request.term,
                                                        seguro: {{$value->id_seguro}}
                                                          },
                                                          dataType: "json",
                                                          type: 'post',
                                                          success: function(data){
                                                            response(data);
                                                          }
                                                        })
                                                    },
                                                minLength:2,
                                              });






                                            $("#prescripcion<?php echo e($id_receta); ?>{{$variable_tiempo}}").change( function(){
                                                guardar_receta('{{$value->hc_id_procedimiento}}');
                                            });

                                            $("#rp<?php echo e($id_receta); ?>{{$variable_tiempo}}").change( function(){
                                                guardar_receta('{{$value->hc_id_procedimiento}}');
                                            });

                                            @if(is_null($value->hora_inicio))
                                                @if (substr($value->fechaini,0,10) == date('Y-m-d')) 
                                                    $('#frm_evol{{$value->hc_id_procedimiento}} :input').prop('enabled', true);//Fausto
                                                    //tinymce.activeEditor.setMode('readonly');
                                                    //tinyMCE.get('thistoria_clinica').getBody().setAttribute('contenteditable', false);
                                                    //tinyMCE.get('tresultado_exam').getBody().setAttribute('contenteditable', false);
                                                    //tinyMCE.get('tprescripcion').getBody().setAttribute('contenteditable', false);
                                                    //tinyMCE.get('trp').getBody().setAttribute('contenteditable', false);
                                                @endif
                                            @endif

                                           
                                           
                                            function inicio_cita (hcid){
                                              //  $('#btn_inicio').addClass("desabilitar");
                                              //  $('#btn_inicio').css("display", "block");

                                                //$('#btn_fin').css("display", "block");
                                                
                                                $.ajax({
                                                  type: 'get',
                                                  url:"{{url('consulta/inicio')}}/"+hcid,
                                                  datatype: 'json',
                                                  success: function(data){
                                                    
                                                    location.reload();
                                                   //document.getElementById('divcursorIni').style.cursor ='no-drop';
                                                /*  $('#divcursorIni').css('cursor: no-drop');
                                                   document.getElementById('btn_inicio').className += 'desabilitar';
                                                   document.getElementById('alerta_fin').style.display ='block';*/
                                                   

                                                  },
                                                  error: function(data){

                                                  }
                                                });
                                            }

                                            function fin_cita (hcid){
                                                $.ajax({
                                                  type: 'get',
                                                  url:"{{url('consulta/fin')}}/"+hcid,
                                                  datatype: 'json',
                                                  success: function(data){
                                                    location.reload();
                                                   /* $('#divcursorfin').css('cursor: no-drop');
                                                    document.getElementById('btn_fin').className += 'desabilitar';
                                                    document.getElementById('alerta_fin').style.display ='none';*/
                                                   
                                                  },
                                                  error: function(data){

                                                  }
                                                });
                                            }
                                            document.getElementById('btn')
                                        </script>
                                        <div class="col-12">
                                            <center>
                                                <div class="col-4">
                                                    <button style="font-size: 15px; margin-bottom: 15px; height: 100%; width: 100%"  type="button" class="btn btn-info btn_ordenes" id="bguardar{{$id_receta}}{{$variable_tiempo}}" onclick="guardar_protocolo({{$value->hc_id_procedimiento}}, {{$value->espid}})"  ><span class="fa fa-floppy-o"></span>&nbsp;Guardar
                                                    </button>
                                               </div>
                                            </center>
                                        </div>
                                        @php
                                            $tipo_seguro  =  Sis_medico\Seguro::find($value->id_seguro);
                                        @endphp
                                        <div class="contenedor2" id="receta{{$id_receta}}{{$variable_tiempo}}" style="padding-bottom: 20px; padding-right: 15px">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <button type="button" class="btn btn-success" onclick="crear_interconsulta('{{$value->hcid}}')">Crear Interconsulta</button>
                                                        
                                                        <button type="button" class="btn btn-warning" onclick="crear_orden_publica('{{$value->id_agenda}}','{{$value->hcid}}')">Crear Orden Publica</button>
                                                       
                                                    </div>
                                                    <div class="col-md-12" id="div_interconsulta{{$value->hcid}}">
                                                    </div>
                                                </div>        
                                            </div>                    
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div id="paginationLinks" class="hidden-paginator oculto">{{ $procedimientos2->appends(Request::all())->render() }}</div>
                @endif
            </div>
        </div>
  </div>
<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>
<script type="text/javascript">
    
    

    function ver_log_agenda(id_agenda){
        $.ajax({
          type: 'get',
          url:"{{url('consulta/agenda/hc4/logagenda')}}/"+id_agenda,
          datatype: 'json',
          success: function(data){
            $('#log_agenda_'+id_agenda).empty().html(data);
          },
          error: function(data){
            alert("Error al cargar log agenda");
          }
        });
    }

    $('.datetimepicker2{{$variable_timepicker}}').datetimepicker({
        format: 'YYYY/MM/DD hh:mm',
    });

    $('.datetimepicker1{{$variable_timepicker}}').datetimepicker({
        format: 'YYYY/MM/DD hh:mm',
    });

    $('.infinite-scroll').jscroll({
        autoTrigger: true,
        loadingHtml: '<img class="center-block" src="{{asset("/loading.gif")}}" width="50px" alt="Loading..." />',
        padding: 0,
        nextSelector: '.pagination li.active + li a',
        contentSelector: 'div.infinite-scroll',
        callback: function() {

            $('div.paginationLinks').remove();

        }
    });

    function crear_interconsulta(hcid){

        $.ajax({
            type: 'get',
            url:"{{url('hc4_interconsulta/crear')}}/"+hcid,
            datatype: 'json',
            success: function(data){

                $('#div_interconsulta'+hcid).html(data);

            },
            error: function(data){
            }
        });    
    }

    function crear_orden_publica(id_agenda, hcid){

        $.ajax({
            type: 'get',
            url:"{{url('nuevo_laboratorio/publico/crear/agenda')}}/"+id_agenda,
            datatype: 'json',
            success: function(data){

                $('#div_interconsulta'+hcid).html(data);

            },
            error: function(data){

            }
        });

    }
    
</script>