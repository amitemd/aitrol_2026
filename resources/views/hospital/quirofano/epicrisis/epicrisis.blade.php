<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">

<style type="text/css">
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
</style>
<div class="card">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-7">
                    <label class="colorbasic" style="font-size: 16px" >{{trans('Epicrisis')}}</label>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-xs btn-success" onclick="agregar_epicrisis();"><i class="fa fa-plus"></i> {{trans('hospitalizacion.Agregar')}}</button>
                </div>
            </div>
        </div>
    </div>
     
</div>
 

<script src="{{asset('ho/app-assets/js/core/app.js')}}"></script>

<script type="text/javascript">

    function ver_epicrisis(id){
        var id_solicitud = $('#id_solicitud').val();
        $.ajax({
            async: true,
            type: "GET",
            url: "{{url('hospital/formu_005/evolucion/detalle')}}/"+id,
            data:{
                'id_solicitud': id_solicitud,
            },
            datatype: "html",
            success: function(datahtml){

                $("#evolucion_detalle"+id).html(datahtml);

            },
            error:  function(){
                alert('error al cargar');
            }
        });
        $("#plus"+id).hide();
        $("#min"+id).show();    
    }


    function ocultar_epicrisis(id){
        $("#evolucion_detalle"+id).html("<br>");
        $("#min"+id).hide();
        $("#plus"+id).show();
    }

    function agregar_epicrisis(){
        $.ajax({
            async: true,
            type: "GET",
            url: "{{route('quirofano.crear_epicrisis',[ 'id' => $solicitud->id ])}}",
            datatype: "html",
            success: function(datahtml){
                console.log(datahtml);

              $("#pasos").html(datahtml);

            },
            error:  function(){
                alert('error al cargar');
            }
        });

    }

</script>