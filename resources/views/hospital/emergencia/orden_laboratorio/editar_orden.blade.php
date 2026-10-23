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
@php $detalles = $orden->detalles; @endphp
<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-2">
            <i style="color: white;">{{trans('emergencia.OrdenNo.')}} {{$orden->id}}</i>
        </div>  
        <div class="col-md-10" style="text-align: center;color: white;">
            {{trans('emergencia.DetalledeOrden')}} {{$orden->seguro->nombre}}
        </div>
    </div>
    <div class="card-body" style="margin-bottom: 1px;padding: 0;">
        <!--form class="form-vertical" role="form" method="POST" action="{{ route('orden.update',['id' => $orden->id]) }}"-->
        <form id="orden_publica{{$orden->id}}" >
                    
            {{ csrf_field() }}
        
            <!--div class="form-group col-md-4 {{ $errors->has('id_protocolo') ? ' has-error' : '' }}">
                <label for="id_protocolo" class="col-md-12 control-label">Protocolo</label>
                <div class="col-md-12"> 
                    <select id="id_protocolo" name="id_protocolo" class="form-control input-sm" onchange="Protocolo();">
                        <option value="">Seleccione ...</option>
                    @foreach ($protocolos as $protocolo)
                        <option @if(old('id_protocolo') == $protocolo->id) selected @endif @if($orden->id_protocolo==$protocolo->id) selected @endif value="{{$protocolo->id}}">{{$protocolo->nombre}}</option>
                    @endforeach
                    </select>
                    @if ($errors->has('id_protocolo'))
                    <span class="help-block">
                        <strong>{{ $errors->first('id_protocolo') }}</strong>
                    </span>
                    @endif 
                </div>
            </div-->          

            @php $cont=count(old('examen')) @endphp 
          
             
            <div class="row" style="padding: 0;"> 
                @php $agrupador = $agrupadores->find(1);  @endphp                    
                <div class="col-md-6">    
                    <div class="mb-0 table-responsive">
                        <table class="table b-table" role="table" style="font-size: 12px;">
                            <tbody>
                                <tr role="row" class="">
                                    <th colspan="4" ><center>{{$agrupador->nombre}}</center></th>
                                </tr>
                                <tr role="row">
                                @php $contador=0;$filas = 1 @endphp
                                @php $examenes1 = $examenes->where('id_agrupador',1); @endphp
                                @foreach ($examenes1 as $value)
                                    @if($value->no_orden_pub=='1' && is_null($detalles->where('id_examen',$value->id)->first()))
                                    @else 
                                        @if((!is_null($detalles->where('id_examen',$value->id)->first()) && $value->id=='628')||$value->id!='628')
                                            <td><b>{{$value->nombre}}</b></td>
                                            <td>
                                                <input onclick="agregar_quitar_examen( this );" id="ch-{{$orden->id}}-{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch-{{$orden->id}}-{{$value->id}}" type="checkbox">
                                            </td> 
                                            @php $contador = $contador + 1; @endphp 
                                        @endif           
                                        @if($contador=='2')
                                            @php $contador=0;$filas ++;  @endphp
                                            </tr>@if($filas == 2)<tr role="row" class="">@php $filas = 0;@endphp @endif
                                        @endif
                                    @endif    
                                @endforeach
                                @if($contador=='1')
                                    <td></td>
                                    <td></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>  
                </div> 

                @php $agrupador = $agrupadores->find(3);  @endphp        
                <div class="col-md-6">    
                    <div class="mb-0 table-responsive">
                        <table class="table b-table" role="table" style="font-size: 12px;">
                            <tbody>   
                                <tr role="row" class="">
                                    <th width="90%"><b><center>{{$agrupador->nombre}}</center></b></th>
                                    <th >Sel.</th>
                                </tr>
                                @php $examenes2 = $examenes->where('id_agrupador',3);$filas = 0; @endphp
                                @foreach ($examenes2 as $value)
                                    @if((!is_null($detalles->where('id_examen',$value->id)->first()) && $value->id=='201')||$value->id!='201')
                                    <tr role="row" @if($filas == '1') class="" @endif>
                                        <td><b>{{$value->nombre}}</b></td>
                                        <td><input onclick="agregar_quitar_examen( this );" id="ch-{{$orden->id}}-{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch-{{$orden->id}}-{{$value->id}}" type="checkbox" ></td>         
                                    </tr>
                                    @php $filas ++; @endphp
                                    @endif
                                    
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @php $agrupador = $agrupadores->find(4);  @endphp  
                    <div class="mb-0 table-responsive">
                        <table class="table b-table" role="table" style="font-size: 12px;">
                            <tbody>
                                <tr role="row" class="">
                                    <th width="90%"><b><center>{{$agrupador->nombre}}</center></b></th>
                                    <th >Sel.</th>
                                </tr>
                                @php $examenes3 = $examenes->where('id_agrupador',4);$filas = 0; @endphp
                                @foreach ($examenes3 as $value)
                                <tr role="row" @if($filas == '1') class="" @endif>
                                    <td><b>{{$value->nombre}}</b></td>
                                    <td><input onclick="agregar_quitar_examen( this );" id="ch-{{$orden->id}}-{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch-{{$orden->id}}-{{$value->id}}" type="checkbox" ></td>         
                                </tr> 
                                @php $filas ++; @endphp
                                @if($filas == 2)
                                     @php $filas = 0; @endphp
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
    
                @php $agrupador = $agrupadores->find(2);  @endphp 
                <div class="col-md-6">    
                    <div class="mb-0 table-responsive">
                        <table class="table b-table" role="table" style="font-size: 12px;">
                            <tbody>
                                <tr role="row" class="">
                                    <th colspan="4" width="90%"><b><center>{{$agrupador->nombre}}</center></b></th>
                                </tr>
                                <tr role="row" >
                                    @php $contador=0;$examenes4 = $examenes->where('id_agrupador',2);$filas = 1; @endphp
                                    @foreach ($examenes4 as $value)
                                        <td><b>{{$value->nombre}}</b></td>
                                        <td><input onclick="agregar_quitar_examen( this );" id="ch-{{$orden->id}}-{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch{{$value->id}}" type="checkbox" ></td> 
                                        @php $contador = $contador + 1; @endphp        
                                    @if($contador=='2')
                                    @php $contador=0;$filas ++; @endphp
                                    </tr>@if($filas == 2)<tr role="row" class="">@php $filas = 0;@endphp @endif
                                    @endif  
                                    @endforeach
                                    @if($contador=='1')
                                    <td></td>
                                    <td></td>
                                    </tr>
                                    @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @php $agrupador = $agrupadores->find(5);  @endphp 
                <div class="col-md-6">                          
                    <div class="mb-0 table-responsive">
                        <table class="table b-table" role="table" style="font-size: 12px;">
                            <tbody>
                                <tr role="row" class="">
                                    <th colspan="4" width="90%"><b><center>{{$agrupador->nombre}}</center></b></th>
                                </tr>
                                <tr role="row">
                                @php $contador=0; $examenes5 = $examenes->where('id_agrupador',5);$filas = 1; @endphp
                                @foreach ($examenes5 as $value)
                               
                                    <td><b>{{$value->nombre}}</b></td>
                                    <td><input onclick="agregar_quitar_examen( this );" id="ch-{{$orden->id}}-{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch-{{$orden->id}}-{{$value->id}}" type="checkbox" ></td> 
                                    @php $contador = $contador + 1; @endphp        
                                @if($contador=='2')
                                @php $contador=0;$filas ++; @endphp
                                </tr>@if($filas == 2)<tr role="row" class="">@php $filas = 0;@endphp @endif
                                @endif
                                
                                
                                @endforeach
                                @if($contador=='1')
                                <td></td>
                                <td></td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div> 
                    @php $agrupador = $agrupadores->find(6);  @endphp    
                    <div class="mb-0 table-responsive">
                        <table class="table b-table" role="table" style="font-size: 12px;">
                            <tbody>
                                <tr role="row" class="">
                                    <th colspan="4" width="90%"><b><center>{{$agrupador->nombre}}</center></b></th>
                                </tr>
                                <tr role="row">
                                @php $contador=0;$filas = 1;$examenes6 = $examenes->where('id_agrupador',6) @endphp
                                @foreach ($examenes6 as $value)
                                
                                    <td><b>{{$value->nombre}}</b></td>
                                    <td><input onclick="agregar_quitar_examen( this );" id="ch-{{$orden->id}}-{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch-{{$orden->id}}-{{$value->id}}" type="checkbox" ></td> 
                                    @php $contador = $contador + 1; @endphp        
                                @if($contador=='2')
                                @php $contador=0;$filas ++; @endphp
                                </tr>@if($filas == 2)<tr role="row" class="">@php $filas = 0;@endphp @endif
                                @endif
                                
                                
                                @endforeach
                                @if($contador=='1')
                                <td></td>
                                <td></td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <div class="card"><b>{{trans('emergencia.IngresarOtrosExamenes')}}</b></div>
                    <div class="input-group">
                        <input id="buscador{{$orden->id}}" class="form-control input-sm" type="text" name="buscador{{$orden->id}}" value="" >
                        <!--button type="button" class="btn btn-primary"><i class="fa fa-check-circle"></i></button-->
                    </div> 
                </div>
                <br>
                <div class="col-md-6" id="otros{{$orden->id}}">
                   
                </div>  
            </div>     
                   
        </form>
    </div>
</div>   
<script src="{{ asset ('/js/jquery.validate.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script type="text/javascript">
    ver_otros();
    function agregar_quitar_examen( element ){
        var array = element.id.split("-");
        var id_orden  = array[1].trim();
        var id_examen = array[2].trim();

        //alert(id_orden);alert(id_examen);
        //console.log( array );
        if(element.checked){
            agregar_examen(id_orden,id_examen);
        }else{
            quitar_examen(id_orden,id_examen);
        }

    }

    function agregar_examen( id_orden, id_examen ){
        //alert("ingreso");
        $.ajax({
            async: true,
            type: "GET",
            url: "{{url('hospital/emergencia/decimopaso/laboratorio/detalle/solic/crear/pub/agregar/examen')}}/"+id_orden+"/"+id_examen,
            data: "",
            datatype: "html",
            success: function(datahtml){


            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }  

    function quitar_examen( id_orden, id_examen ){
        //alert("quitar");
        $.ajax({
            async: true,
            type: "GET",
            url: "{{url('hospital/emergencia/decimopaso/laboratorio/detalle/solic/crear/pub/quitar/examen')}}/"+id_orden+"/"+id_examen,
            data: "",
            datatype: "html",
            success: function(datahtml){


            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }  

    $("#buscador{{$orden->id}}").autocomplete({
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

    function cargar_buscador(id_examen){

        $.ajax({
            async: true,
            type: "GET",
            url: "{{url('hospital/emergencia/decimopaso/laboratorio/detalle/solic/crear/pub/agregar/examen')}}/{{$orden->id}}/"+id_examen,
            data: "",
            datatype: "html",
            success: function(datahtml){

                ver_otros();
            },
            error:  function(){
                alert('error al cargar');
            }
        });   

    }

    function ver_otros(){

        $.ajax({
            async: true,
            type: "GET",
            url: "{{route('hospital.decimo_laboratorio_listar_otros',[ 'id' => $orden->id])}}",
            data: "",
            datatype: "html",
            success: function(datahtml){
                $("#otros{{$orden->id}}").html(datahtml);

            },
            error:  function(){
                alert('error al cargar');
            }
        });   


    }
</script> 
