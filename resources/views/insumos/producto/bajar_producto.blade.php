@extends('insumos.producto.base')
@section('action-content')

<script type="text/javascript">
    function check(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }
    
    function goBack() {
      window.history.back();
    }

</script>


<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 80%;">
      <div class="modal-content" >

      </div>
    </div>  
</div>

<section class="content">
    <div class="box " style="background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
              <h3 class="box-title">{{trans('winsumos.dar_baja_producto')}}</h3>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;"> 
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('winsumos.regresar')}}
                </button>
            </div>
        </div>  
        
        <div class="box-body" style="background-color: #ffffff;">
            <form class="form-vertical" role="form" method="POST" action="{{ route('producto_dar_baja_guardar') }}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    <div class="form-group col-xs-12">
                        <label for="codigo" class="col-md-4 control-label" style="text-align: right;">{{trans('winsumos.serie')}}</label>
                        <div class="col-md-3">
                            <input id="serie" type="text" class="form-control" name="serie" value="" style="text-transform:uppercase;"  maxlength="25"  required autofocus >
                        </div>




                        <div class="col-md-2">
                            <button class="btn btn-danger" type="button" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;"> 
                               &nbsp;&nbsp;{{trans('winsumos.Buscar')}}
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button type="button" onclick="location.href='{{route('descarga.dar_baja.index')}}'" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                                <i aria-hidden="true"></i>Reporte
                            </button>
                       </div>
                    </div>
                   <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div id="area_trabajo"></div>
                    </div>
                </div>
            </form>
             
        </div>
    </div>
</section>
<script type="text/javascript">

    $('#foto').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
    });

    $("#serie").change( function(){
        //alert("sii");
       // var elemento = document.getElementById("serie").value;
          $.ajax({
            type: 'post',
            url:"{{route('producto.codigo_baja')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            data: $("#serie"),
            datatype: "html",
            success: function(datahtml){
                $("#area_trabajo").html(datahtml);
            },
            error: function(data){
                console.log(data);
            }
        })
    });
</script>


@endsection
