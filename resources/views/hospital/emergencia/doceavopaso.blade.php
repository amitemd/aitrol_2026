
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
                <div class="d-flex align-items-center col-md-12">
                    <h4 class="card-title ml-25 colorbasic">
                        {{trans('pasos.DiagnosticosDefinitivos')}}
                    </h4>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form id="form_paso12">
             {{ csrf_field() }}
        <div class="row" style="padding-top: 10px;">
           
            <div class="col-md-6">
                <label >{{trans('paso2.Diagnostico')}}</label>
                <input type="hidden" name="id_solicitud" id="id_solicitud" value="{{$id_sol}}">
                <input type="hidden" name="codigo" id="codigo" class="form-control input-sm">
                <input type="text" name="cie10" id="cie10" class="form-control input-sm">
            </div>
            <div class="col-md-4">
                <br>
                <select name="pre_def" id="pre_def" class="form-control"> 
                    <option value="">{{trans('paso2.Seleccionee')}}</option>
                    <option value="PRESUNTIVO">{{trans('paso2.PRESUNTIVO')}}</option>
                    <option value="DEFINITIVO">{{trans('paso2.DEFINITIVO')}}</option>
                </select>
            </div>
            <div class="col-md-2">
                <br>
                <button type="button" name="agregar_cie_alta" id="agregar_cie10_alta" class="btn btn-primary btn-sm">{{trans('paso2.Agregar')}}</button>
            </div>
            <div class="form-group col-12" style="padding: 1px;margin-bottom: 0px;">
                <table id="tdiagnostico_alta" class="table table-striped" style="font-size: 12px;">

                </table>
            </div>
            
        </div>
        </form>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">

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


    $('#agregar_cie10_alta').click( function(){

        if($('#cie10').val()!='' ){
            if($('#pre_def').val()!='' ){
                guardar_cie10_consulta_alta();
                
            }else{
                alert("Seleccione Presuntivo o Definitivo");
            }
        }else{
            alert("Seleccione CIE10");
        }

        $('#codigo').val('');
        $('#cie10').val('');
        

    });

    function guardar_cie10_consulta_alta(){
        $.ajax({
         
            type: 'post',
            url:"{{route('hospital.agregar_cie10_alta')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data:  $("#form_paso12").serialize(),
            success: function(data){
                console.log(data);
                var indexr = data.count-1
                var table = document.getElementById("tdiagnostico_alta");
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
                cell4.innerHTML = '<a href="javascript:eliminar_alt('+data.id+', '+data.id_hcproc+');" class="btn btn-xs btn-danger btn-xs"><i class="fa fa-trash"></i></a>';

                //$('#prescripcion').empty().html(anterior+ data.value +': \n' +data.dosis);                
                console.log('guardo');
                //();

            },
            error: function(data){
                    //console.log(data);
                }
        })
    }


    cargar_tabla_cie_alta({{$solicitud->id_hcproc}});


    function cargar_tabla_cie_alta(id_hcproc){
        $.ajax({
            url:`{{route('hospital.cargar_tabla_cie_alta')}}`,
            dataType: "json",
            type: 'get',
            data:{
                'id_hcproc' : id_hcproc 
            },
            success: function(data){
               // console.log(data);
                var table = document.getElementById("tdiagnostico_alta");

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
                    cell4.innerHTML = '<a href="javascript:eliminar_alt('+value.id+', '+id_hcproc+');" class="btn btn-xs btn-danger btn-xs"><i class="fa fa-trash"></i></a>';
                    //alert(index);

                });
            }
        })
    }


    function eliminar_alt(id_h, id){
        //var i = document.getElementById('tdiag'+id_h).rowIndex;
        //document.getElementById("tdiagnostico"+id).deleteRow(i);

        $.ajax({
          type: 'get',
          url:"{{url('cie10/eliminar')}}/"+id_h,  //epicrisis.eliminar
          datatype: 'json',

          success: function(data){
            //console.log(data);
            $('#boton_p12').click();
          },
          error: function(data){
             //console.log(data);
          }
        });
    }


</script>