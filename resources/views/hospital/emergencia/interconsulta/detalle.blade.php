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
    <div class="card-header bg bg-primary">
        <div class="col-md-2">
            <i style="color: white;">{{trans('emergencia.InterconsultaNo.')}} {{$interconsulta->id}}</i>
        </div>  
        <!--div class="col-md-10" style="text-align: center;color: white;">
            <a class="btn btn-info btn-sm" href="{{route('decimo.imprimir_interconsulta',['id' => $interconsulta->id])}}" target="_blank">Formulario 007</a>    
        </div-->
    </div>
    <div class="card-body" style="margin-bottom: 1px;padding: 0;">

                    
            {{ csrf_field() }}          
            <span type="hidden" name="id_interconsulta" > 
            <div class="row" style="padding: 0;"> 
            
                <div class="col-md-12">
                    <label><b> {{trans('emergencia.Servicio')}}: </b> </label>
                    <span  id="servicio" >  {{$interconsulta->servicio}}</span>
                </div>
                <div class="col-md-12">
                    <label><b> {{trans('emergencia.Especialidad')}}: </b> </label>
                    <span  id="especialidad">{{$interconsulta->especialidad}}</span>
                </div>
                <div class="col-md-12">
                    <label><b> {{trans('emergencia.Evolución')}}: </b> </label>
                    <span  id="evolucion">@php echo($interconsulta->evolucion); @endphp</span>
                </div>
                <div class="col-md-12">
                    <label><b> {{trans('emergencia.Tarifario')}}: </b> </label>
                    <div class="col-md-12" id="listado" name="listado">
                    <div class="row">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <th>Codigo</th>
                                    <th>Nombre</th>
                                </thead>
                                <tbody>
                                    @foreach ($det_tarifario as $det)
                                    <tr>
                                        <td>@if(isset($det->ap_procedimento)) {{$det->ap_procedimento->codigo}} @endif</td>
                                        <td>@if(isset($det->ap_procedimento)) {{$det->ap_procedimento->descripcion}} @endif</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                   </div>
                </div> 
                <div class="col-md-12">
                
                    <label><b> {{trans('emergencia.Descripcion')}}: </b> </label>
                    <span  id="descripcion"> {{$interconsulta->descripcion}} </span>
                </div>

                <div class="col-md-12"><br>
                </div>    
                 
            </div>     
    </div>
</div>   
<script src="{{ asset ('/js/jquery.validate.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script type="text/javascript">

    function guardar(){

        $.ajax({
         
            type: 'post',
            url:"{{route('decimo.actualizar_interconsulta')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data:  $("#interconsulta{{$interconsulta->id}}").serialize(),
            success: function(data){
                alert("Guardado");

            },
            error: function(data){
                alert('error al cargar');
            }
        })

    }

    $("#buscador{{$interconsulta->id}}").autocomplete({
        source: function( request, response ) {

            $.ajax({
                url:"{{route('decimopaso.examenes_buscar_publicos_otros')}}",
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

    $("#tarifario").autocomplete({
      
      source: function(request,response){
        var nivel_conve = '2';
        var term = request.term;
        $.ajax({
          url:"{{route('item_iess.buscarxcodigo')}}",
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
        $("#descripcion").val(ui.item.descripcion);
        //$("#cantidad").val(ui.item.cantidad);
        //$("#iva").val(ui.item.iva);
        //$("#porcent_10").val(ui.item.porcent10);
        //obtener_data(ui.item.id_ap_proced,ui.item.tipo);

        
      },
      select:function(event, ui){
        //console.log(ui);
        //$("#tipo").val(ui.item.tipo);
        $("#descripcion").val(ui.item.descripcion);
        //$("#cantidad").val(ui.item.cantidad);
        //$("#iva").val(ui.item.iva);
        //$("#porcent_10").val(ui.item.porcent10);
        //obtener_data(ui.item.id_ap_proced,ui.item.tipo);

        
      },
      minLength: 2,
      appendTo: "#item",  //Linea nueva, agrego el id del modal
    }); 


    
    
    
</script> 
