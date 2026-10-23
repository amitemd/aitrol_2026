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
                

                <div class="col-md-12"><br>
                    <center>    
                        <button class="btn btn-success btn-xs" type="button" onclick="guardar()">{{trans('emergencia.Guardar')}}</button>   
                        <a class="btn btn-info btn-sm" href="{{route('consulta.hc4_imprimir',['id' => $interconsulta->id])}}" target="_blank">Formulario 007</a>
                    </center>
                </div>    
                 
            </div>     
                   
        </form>
        <!--div class="col-md-12">
            <form id="form_diagnostico">
                {{ csrf_field() }}
            <div class="row" style="padding-top: 10px;">
            <input type="hidden" name="id_interconsulta" id="id_interconsulta" value="{{$interconsulta->id}}">
                <div class="col-md-6">
                    <label >{{trans('paso2.Diagnostico')}}</label>
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
        </div-->   
    </div>
</div>   
<script src="{{ asset ('/js/jquery.validate.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script type="text/javascript">

$('.select2').select2({
    tags: false
});

function changeTarifario(e){
    
    
    var descripcion_actual  =  document.getElementById('descripcion').value;
    var txt_descripcion = $('option:selected', e).data("descripcion");
    var txt_codigo = $('option:selected', e).data("codigo");
    $('#descripcion').val(descripcion_actual +' + '+ txt_descripcion +'('+ txt_codigo +')');
}

$('#cie10').autocomplete({
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


    $('#cie10').change( function()
        {
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.cie10_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $('#cie10'),
            success: function(data){
                if(data!='0'){

                    $('#codigo').val(data.id);
                }
            },
            error: function(data){
            }
        })
    });

    $('#agregar_cie').click( function(){

        if($('#cie10').val()!='' ){
            if($('#pre_def').val()!='' ){
                guardar_cie10_consulta({{$interconsulta->id}});
                
            }else{
                alert("Seleccione Presuntivo o Definitivo");
            }
        }else{
            alert("Seleccione CIE10");
        }

        $('#codigo').val('');
        $('#cie10').val('');
        

    });

   
    function guardar(){

        $.ajax({
         
            type: 'post',
            url:"{{route('consulta.hc4_guardar')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data:  $("#interconsulta{{$interconsulta->id}}").serialize(),
            success: function(data){
                alert("!! Guardado Correctamente !!");

            },
            error: function(data){
                alert('error al cargar');
            }
        })

    }

    $("#buscador{{$interconsulta->id}}").autocomplete({
        source: function( request, response ) {

            $.ajax({
                url:"{{route('consulta.examenes_buscar_publicos_otros')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {
                    term: request.term
                      },
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);
                    //console.log(data);

                },
                
            })
        },
        minLength: 3,
        select: function(data, ui){
            console.log(ui.item.id);
            //$('#examenpub_id').val(ui.item.id);
            cargar_buscador(ui.item.id);    
        }
    } ); 

    function agregar_eda(){
        var txt_des = document.getElementById('descripcion').value;
        txt_des = txt_des + ' ENDOSCOPIA CON BIOPSIA (70200003) \n';
        
        $("#descripcion").val(txt_des);
    }

    function agregar_colono(){
        var txt_des = document.getElementById('descripcion').value;
        txt_des = txt_des + ' COLONOSCOPIA CON BIOPSIA (70200004) \n';
        
        $("#descripcion").val(txt_des);
    }

    $("#tarifario").autocomplete({
      
      source: function(request,response){
        var nivel_conve = '2';
        var term = request.term;
        $.ajax({
          url:"{{route('item_iess.buscarxcodigo_2')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          data: {'term': term,'conv': nivel_conve},
          dataType: "json",
          type: 'post',
          success: function(data){
            response(data);
          }

        });

      },
      change:function(event, ui){
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
      select:function(event, ui){
        console.log(ui);
        //$("#tipo").val(ui.item.tipo);
        var txt_des = $("#descripcion").val() + '\n' + ui.item.value ;
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


    function eliminar_cie10(id){
        $.ajax({
            async: true,
            type: "GET",
            url: "{{url('hosp/decimo/cie/eliminar_cie')}}/"+id,
            data: "",
            datatype: "html",
            success: function(datahtml){

                cargar_tabla_cie({{$interconsulta->id}});

            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }  

    
    
</script> 
