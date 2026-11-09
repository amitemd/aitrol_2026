<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
@include('hc4.consulta.creation_script')
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

    /* El listado se carga por AJAX tanto desde HC4 como desde hospitalización.
       Estas reglas viven en el componente para no depender del contenedor padre. */
    #area_trabajo .hc4-consultations-view > .box-body {
        padding-right: 16px !important;
        padding-left: 16px !important;
    }

    #area_trabajo .hc4-consultations-view > .box-body > .col-12 {
        width: 100% !important;
        max-width: none !important;
        padding: 0 !important;
    }

    #area_trabajo .hc4-consultations-list {
        display: block !important;
        box-sizing: border-box;
        width: 100% !important;
        max-width: none !important;
        margin-right: 0 !important;
        margin-left: 0 !important;
        padding: 12px 0 !important;
        overflow-x: hidden;
        overflow-y: auto;
        scrollbar-color: #0b91b8 #dceef3;
        scrollbar-width: thin;
    }

    #area_trabajo .hc4-consultations-list > [id^="consulta"],
    #area_trabajo .hc4-consultations-list > .infinite-scroll,
    #area_trabajo .hc4-consultations-list > .infinite-scroll > [id^="consulta"] {
        box-sizing: border-box;
        width: 100% !important;
        max-width: none !important;
        margin-right: 0 !important;
        margin-left: 0 !important;
        padding-right: 0 !important;
        padding-left: 0 !important;
    }

    #area_trabajo .hc4-consultations-list::-webkit-scrollbar {
        width: 8px;
    }

    #area_trabajo .hc4-consultations-list::-webkit-scrollbar-track {
        border-radius: 10px;
        background: #dceef3 !important;
        box-shadow: none !important;
    }

    #area_trabajo .hc4-consultations-list::-webkit-scrollbar-thumb {
        border: 2px solid #dceef3;
        border-radius: 10px;
        background: linear-gradient(#087eae, #16aaa5) !important;
    }

    #area_trabajo .hc4-consultations-list::-webkit-scrollbar-thumb:hover {
        background: #07547e !important;
    }

    #area_trabajo .hc4-consultations-list .hc4-consultation-record-header {
        padding: 12px 14px !important;
    }

    #area_trabajo .hc4-consultations-list .hc4-consultation-record-header > .row {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) 42px;
        align-items: center;
        gap: 12px;
        width: 100%;
        margin: 0 !important;
    }

    #area_trabajo .hc4-consultations-list .hc4-consultation-record-header > .row > [class*="col-"] {
        float: none !important;
        width: 100% !important;
        max-width: none !important;
        min-width: 0;
        padding: 0 !important;
        line-height: 1.45;
    }

    #area_trabajo .hc4-consultations-list .hc4-consultation-record-header > .row > [class*="col-"]:nth-child(1) {
        text-align: left !important;
    }

    #area_trabajo .hc4-consultations-list .hc4-consultation-record-header > .row > [class*="col-"]:nth-child(2) {
        text-align: center !important;
    }

    #area_trabajo .hc4-consultations-list .hc4-consultation-record-header > .row > [class*="col-"]:nth-child(3) {
        text-align: right !important;
    }

    #area_trabajo .hc4-consultations-list .hc4-consultation-record-header > .row > .box-tools {
        position: static !important;
        float: none !important;
        justify-self: end;
        width: 42px;
        margin: 0 !important;
        padding: 0 !important;
        text-align: right;
    }

    @media (max-width: 767px) {
        #area_trabajo .hc4-consultations-list .hc4-consultation-record-header > .row {
            grid-template-columns: 1fr 42px;
            gap: 8px 12px;
        }

        #area_trabajo .hc4-consultations-list .hc4-consultation-record-header > .row > [class*="col-"] {
            grid-column: 1;
            text-align: left !important;
        }

        #area_trabajo .hc4-consultations-list .hc4-consultation-record-header > .row > .box-tools {
            grid-column: 2;
            grid-row: 1 / span 3;
            align-self: center;
        }
    }

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
        position: fixed !important;
        z-index: 10050;
        top: 92px;
        right: 24px;
        width: auto !important;
        min-width: 230px;
        max-width: 320px;
        margin: 0 !important;
        padding: 11px 38px 11px 14px !important;
        border: 1px solid #7bcfb2 !important;
        border-radius: 11px !important;
        color: #155e4a !important;
        background: #effaf6 !important;
        box-shadow: 0 10px 28px rgba(0, 83, 95, 0.18);
        font-size: 13px;
    }
    .alerta_correcto .hc4-save-toast-content {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alerta_correcto .hc4-save-toast-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 28px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        color: #fff;
        background: #19a974;
        font-size: 14px;
    }
    .alerta_correcto .hc4-save-toast-text {
        line-height: 1.2;
    }
    .alerta_correcto .hc4-save-toast-text strong,
    .alerta_correcto .hc4-save-toast-text span {
        display: block;
    }
    .alerta_correcto .hc4-save-toast-text span {
        margin-top: 2px;
        color: #427565;
        font-size: 11px;
    }
    .alerta_correcto .close {
        position: absolute;
        top: 7px;
        right: 10px;
        color: #155e4a;
        opacity: .65;
    }
    @media (max-width: 575px) {
        .alerta_correcto {
            top: 78px;
            right: 12px;
            left: 12px;
            max-width: none;
        }
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

<div id="alerta_datos" class="alert alert-success alerta_correcto" role="status" aria-live="polite" style="display:none;">
    <button type="button" class="close" aria-label="Cerrar" onclick="$('#alerta_datos').stop(true, true).fadeOut(150);">&times;</button>
    <div class="hc4-save-toast-content">
        <span class="hc4-save-toast-icon"><i class="fa fa-check"></i></span>
        <span class="hc4-save-toast-text">
            <strong>Cambios guardados</strong>
            <span>La consulta se actualizó correctamente.</span>
        </span>
    </div>
</div>
<div class="modal fade" id="vademecum" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog " role="document" style="max-width: 80% !important;">
      <div class="modal-content">

      </div>
    </div>
</div>

<script type="text/javascript">

    function mostrar_alerta_guardado() {
        $('#alerta_datos').stop(true, true).fadeIn(180).delay(1400).fadeOut(250);
    }

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
    @include('hc4.recetas._medicine_search_alert_js')
    function buscar_nombre_medicina(div){
        var medicineField = $("#nombre_generico"+div);
        var medicineName = $.trim(medicineField.val());
        if (!medicineName) {
            window.hc4MedicineNotFound('');
            medicineField.focus();
            return;
        }

        $.ajax({
            type: 'post',
            url:"{{route('buscar_nombre2.receta')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: medicineField,
            success: function(data){
                console.log(data);
                if(data == '0'){
                    window.hc4MedicineNotFound(medicineName);
                    medicineField.focus();
                    return;
                }
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
                window.hc4MedicineSearchError(data);
            }
        });
    }
    function Crear_detalle(med, div){
        var js_cedula = document.getElementById("id_paciente").value;
        var id = div.slice(0, -6);
        //alert (id);
        $.ajax({
            type: 'post',
            url:"{{url('detalle_receta/detalle_crear')}}"+"/"+id+"/"+med.id+"/"+js_cedula,
            headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
            datatype: 'json',
            success: function(data){

                //console.log(data);
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
                    console.log(cantidad);
                    console.log(med.cantidad);

                    //En caso de que no exista.
                    if(pos === -1 && resultado === ""){
                        tinyMCE.get('trp'+div).setContent(anterior2 +'\n'+ med.value +"("+med.genericos+")"+': ' +cantidad);
                        $('#rp'+div).val(tinyMCE.get('trp'+div).getContent());
                    }else{
                        pos = pos-12;
                        tinyMCE.get('trp'+div).setContent(anterior2.substr(0, pos) +'\n'+ med.value +"("+med.genericos+")"+':  ' +cantidad +anterior2.substr(pos));
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
                    console.log(cantidad);
                    console.log(med.cantidad);

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
        type: 'delete',
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

    function cambiar_receta_2(div){
        //alert("lalala");
        $("#bguardar"+div).click();
    }

    //Agregar Visita OMNI HOSPITAL
    function guardar_visita(id_paciente, id_agenda){
        $.ajax({
            type: "POST",
            url: "{{route('visita.crear_nueva_visita')}}/"+id_paciente+'/'+id_agenda,
            data: {'_token': '{{ csrf_token() }}'},
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
        var peso = parseFloat(document.getElementById('peso'+id).value);
        var estatura = parseFloat(document.getElementById('estatura'+id).value);
        var sexo = @if($paciente->sexo == 1){{$paciente->sexo}}@else{{"0"}}@endif;
        var edad = parseFloat(calcularEdad('{{$paciente->fecha_nacimiento}}'));

        if (!Number.isFinite(peso) || !Number.isFinite(estatura) || peso <= 0 || estatura <= 0) {
            $('#cimc'+id).val('');
            $('#gct'+id).val('0.00');
            $('#imc'+id).val('0.00');
            $('#peso_ideal'+id).val('0.00');
            return;
        }

        var estatura2 = Math.pow((estatura/100), 2);
        var peso_ideal = 21.45 * estatura2;
        var imc = peso/estatura2;
        var gct = Number.isFinite(edad)
            ? ((1.2 * imc) + (0.23 * edad) - (10.8 * sexo) - 5.4)
            : 0;
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
            texto = "Obesidad Mórbida";
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
				if (!table) {
					return;
				}

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
          type: 'delete',
          url:"{{url('cie10/eliminar')}}/"+id_h,  //epicrisis.eliminar
          data: {'_token': '{{ csrf_token() }}'},
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
            data: { 'codigo': $("#codigo"+hc_id_procedimiento+variable_tiempo).val(), 'pre_def': $("#pre_def"+hc_id_procedimiento+variable_tiempo).val(), 'hcid': hcid, 'hc_id_procedimiento': hc_id_procedimiento, 'in_eg': null, 'id_paciente': '{{$paciente->id}}' },
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
            mostrar_alerta_guardado();
            //cargar_consulta();
          },
          error: function(xhr){
             var data = xhr.responseJSON || {};
             if (data.estado === 'conflicto_clinico') {
                if (!data.puede_autorizar) {
                    Swal.fire('Episodio clínico activo', data.msn + ' Solicita autorización a un médico responsable o a Sistemas.', 'warning');
                    return;
                }
                Swal.fire({
                    icon: 'warning',
                    title: 'Confirmar excepción clínica',
                    html: data.msn + '<br><br><b>La autorización quedará auditada.</b>',
                    input: 'textarea',
                    inputPlaceholder: 'Motivo clínico de la excepción (mínimo 10 caracteres)',
                    showCancelButton: true,
                    confirmButtonText: 'Autorizar y continuar',
                    inputValidator: function(value) {
                        if (!value || $.trim(value).length < 10) return 'Debes registrar un motivo clínico suficiente.';
                    }
                }).then(function(result) {
                    if (result.value) {
                        var form = $('#frm_evol' + id_hc_procedimiento);
                        form.find('[name=clinical_override]').val('1');
                        form.find('[name=clinical_override_reason]').val($.trim(result.value));
                        guardar_protocolo(id_hc_procedimiento, espid);
                    }
                });
                return;
             }
             console.log(xhr);
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
            mostrar_alerta_guardado();
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

<div class="box hc4-consultations-view" data-patient-id="{{$paciente->id}}" style="border: 2px solid #004AC1; background-color: white; ">
    <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1; ">
        <div class="row hc4-consultations-header-row">
            <div class="col-4 hc4-consultations-title">
                <h1 style="font-size: 15px; margin:0; color: white;" >
                    <img style="width: 35px; margin-left: 5px; margin-bottom: 5px" src="{{asset('/')}}hc4/img/iconos/pendo.png"> <b>@lang('hc4.consulta')</b>
                </h1>
            </div>
            <!--Agregar Visita OMNI HOSPITAL-->
            @if(($id_doctor_visita != 1307189140)&&($id_doctor_visita != 1314490929))
            <div class="col-4" style="padding-left: 0px; display: none;">
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
              <div class="col-4" style="padding-left: 0px; display:none;">
              </div>
            @endif
            <!-- Fin Agregar Visista OMNI HOSPITAL-->
            <div class="col-4 hc4-consultations-add-wrap" style="padding-left: 0px">
                <div style="margin-bottom: 5px;">
                    <button type="button" class="btn btn-info hc4-add-consultation-button" onclick="guardar_consulta('{{$paciente->id}}', 'no');">
                        <img width="18px" src="{{asset('/')}}hc4/img/iconos/agregar.png" alt="">
                        <span>@lang('hc4.agregarConsulta-')</span>
                    </button>
                </div>
            </div>
        </div>
        @if(!is_null($paciente))
            <center>
                <div class="col-12" style="padding-top: 15px">
                    <h1 style="font-size: 14px; margin:0; color: white; padding-left: 20px" >
                        <b>@lang('hc4.paciente') : {{$paciente->apellido1}} {{$paciente->apellido2}}
                            {{$paciente->nombre1}} {{$paciente->nombre2}}
                        </b>
                    </h1>
                </div>
            </center>
        @endif
    </div>
    <div class="box-body" style="background-color: #56ABE3;">
        <div class="col-12">
            <div class="row parent hc4-consultations-list" >
                <span id="msn1" style="color: white; "></span>
                @if(count($procedimientos2)>0)
                    @include('hc4.consulta.records')
                @endif
            </div>
        </div>
  </div>
<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>
<script type="text/javascript">

    function inicializarSelect2Consulta(contenedor){
        if(!$.fn.select2){ return; }

        $(contenedor || '#area_trabajo').find('.hc4-consultation-form select').each(function(){
            var $select = $(this);
            if($select.hasClass('select2-hidden-accessible')){ return; }

            $select.select2({
                width: '100%',
                minimumResultsForSearch: $select.find('option').length > 6 ? 0 : Infinity,
                dropdownParent: $('#area_trabajo')
            });
        });
    }

    inicializarSelect2Consulta();

    /* Abrir primero el historial y desplegar el formulario únicamente cuando
       el profesional seleccione uno de sus registros. */
    $('.hc4-consultation-record').each(function(){
        var $registro = $(this);
        $registro.addClass('collapsed-box');
        $registro.children('.box-body').hide();
        $registro.find('> .box-header [data-widget="collapse"] i')
            .removeClass('fa-minus')
            .addClass('fa-plus');
    });

    function inicializarCamposConsulta(contenedor){
        inicializarSelect2Consulta(contenedor);
        if ($.fn.datetimepicker) {
            $(contenedor).find('.hc4-consultation-form .input-group.date').each(function(){
                if (!$(this).data('datetimepicker')) {
                    $(this).datetimepicker({format: 'YYYY/MM/DD hh:mm'});
                }
            });
        }
    }
    inicializarCamposConsulta('#area_trabajo');

    function crear_interconsulta(hcid){

    $.ajax({
        type: 'post',
        url:"{{url('hc4_interconsulta/crear')}}/"+hcid,
        data: {'_token': '{{ csrf_token() }}'},
        datatype: 'json',
        success: function(data){

            $('#div_interconsulta'+hcid).html(data);

        },
        error: function(data){
        }
    });    
    }
</script>
