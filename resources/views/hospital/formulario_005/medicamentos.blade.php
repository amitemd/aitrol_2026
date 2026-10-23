<div class="card">
    
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1">
                    <label class="colorbasic sradio" > 3 </label> 
                </div>
                <div class="col-md-8">
                    <label class="colorbasic" style="font-size: 16px;"> {{trans('hospitalizacion.Medicamentos-Receta')}} </label>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-xs btn-success" onclick="agregar_receta();"><i class="fa fa-plus"></i> {{trans('hospitalizacion.Agregar')}}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
            <br>
            <input type="hidden" name="id_solicitud" id="id_solicitud" value="{{$solicitud->id}}">
            @foreach($recetas as $receta)
                <br><br>
                <div class="col-md-12" style="padding: 0;">
                    <div class="card-header bg bg-primary colorbasic">
                        <div class="col-md-1">
                           
                        </div>
                        <div class="col-md-5">
                            @if(!is_null($receta->created_at))
                                @php
                                 $dia =  Date('N',strtotime($receta->created_at));
                                 $mes =  Date('n',strtotime($receta->created_at));
                                @endphp

                                <b>
                                 {{Sis_medico\Utilidades::getDia($dia)}}
                                 {{substr($receta->created_at,8,2)}} de
                                 {{Sis_medico\Utilidades::getMes($mes)}}
                                del {{substr($receta->created_at,0,4)}}</b>
                            @endif
                        </div>
                        <div class="col-md-4">
                        
                        </div>
                        <div class="col-md-2">
                            <button id="plus{{$receta->id}}" type="button" class="btn btn-primary" onclick="ver_receta('{{$receta->id}}');">
                                <i class="fa fa-plus"></i>
                            </button>
                            <button id="min{{$receta->id}}" type="button" class="btn btn-primary" onclick="ocultar_receta('{{$receta->id}}');" style="display: none">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>

                    </div>
                    <div class="card-body" id="receta_detalle{{$receta->id}}" style="padding: 0px;">
                                      
                    </div>
                </div>
                <br>

            @endforeach
        

    </div>

</div>

<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">

    function ver_receta(id){
        var id_solicitud = $('#id_solicitud').val();
        $.ajax({
            async: true,
            type: "GET",
            url: "{{url('hospital/receta/detalle')}}/"+id,
            data:{
                'id_solicitud': id_solicitud,
            },
            datatype: "html",
            success: function(datahtml){

                $("#receta_detalle"+id).html(datahtml);

            },
            error:  function(){
                alert('error al cargar');
            }
        });
        $("#plus"+id).hide();
        $("#min"+id).show();    
    }


    function ocultar_receta(id){

        $("#receta_detalle"+id).html("<br>");
        $("#min"+id).hide();
        $("#plus"+id).show();
    }

    function agregar_receta(){

        $.ajax({
            async: true,
            type: "GET",
            url: "{{route('formulario005.crear_receta',[ 'id' => $solicitud->id ])}}",
            data: "",
            datatype: "html",
            success: function(datahtml){

                $("#pasos").html(datahtml);

            },
            error:  function(){
                alert('error al cargar');
            }
        });

    }



    </script>