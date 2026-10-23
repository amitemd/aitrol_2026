<div class="card">
    <div class="card-body" style="margin-bottom: 1px;padding: 0;">
        <br>
        <form id="form_evolucion" method="POST">
            {{ csrf_field() }}
            <div class="row" style="padding-top: 10px;">
                <input type="hidden" name="solicitud_id" id="solicitud_id" value="{{$solicitud->id}}">

                <div class="col-md-12">
                    <label><b> {{trans('hospitalizacion.Motivo')}} </b></label>
                    <textarea name="motivo" id="motivo" class="form-control input-sm" rows="3">@if(!is_null($evolucion)){{$evolucion->motivo}}@endif</textarea>
                </div>               
                <div class="col-md-12">
                    <label><b> {{trans('hospitalizacion.Evolución')}} </b> </label>
                    <textarea name="n_evolucion" id="n_evolucion" class="form-control input-sm" rows="3">@if(!is_null($evolucion)){{$evolucion->cuadro_clinico}}@endif</textarea>
                </div>
                <div class="col-md-12">
                    <label><b>{{trans('hospitalizacion.ExamenFisico')}}</b> </label>
                    <textarea name="examen_fisico" id="examen_fisico" class="form-control input-sm" rows="6">@if(!is_null($child_pugh)){{$child_pugh->examen_fisico}}@endif</textarea>
                </div>
            </div>
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-6">
                    <button class="btn btn-primary" type="button" id="guardar_diagnostico" onclick="guardar_evolucion();"> <span class="fa fa-save">  {{trans('hospitalizacion.Guardar')}}</span> </button>
                </div>
            </div>
        </form>
        <br>
        <br>
        <div class="form-group col-md-12">
            @php 
                $alergias = $solicitud->paciente->a_alergias; $txt_al = '';$cont = 0;
                foreach($alergias as $alergia){ 
                    if($cont < 2)  {  
                    if($cont==0){ $txt_al = $alergia->principio_activo->nombre; }
                    else{ $txt_al = $txt_al.' + '.$alergia->principio_activo->nombre; }
                    $cont++;
                    }
                }  
                $txt_al = $txt_al.' ...'  
            @endphp
            <label><b>{{trans('hospitalizacion.Alergias')}}:</b></label>
            <span class="badge badge-danger"> {{$txt_al}} </span>
        </div>
        <div class="col-md-12">
            <form id="form_diagnostico">
                {{ csrf_field() }}
            <div class="row" style="padding-top: 10px;">
                <input type="hidden" name="id_solicitud" id="id_solicitud" value="{{$solicitud->id}}">
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
        </div>
        <br>     
        <br>
        <br>
        <div class="col-md-12">
            <form id="form_medicina">
                {{ csrf_field() }}
                @php
                    $form008 = $solicitud->form008->first();
                @endphp
                <input type="hidden" name="solicitud_id" id="solicitud_id" value="{{$solicitud->id}}">
                <input type="hidden" name="id_seguro" id="id_seguro" value="{{$solicitud->id_seguro_publico}}">
                <br>
                <input type="hidden" name="contador" id="contador" value="1">
                <div class="form-group">
                    <label for="inputid" class="control-label">{{trans('paso2.Medicina')}}</label>
                    <div class="row">
                        <div class=" col-md-9 col-sm-9 col-12">
                            <br>
                            <select name="generico" id="generico" class="form-control select2" >
                                <option value="">{{trans('paso2.Seleccione')}}</option>
                            </select>
                            
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary" style="background-color: #004AC1;" onclick="buscar_nombre_medicina()">
                                <span class="fa fa-plus"></span>{{trans('paso2.Agregar')}}
                            </button>
                            <br>
                        </div>
                        <div class="col-md-12" style="margin-top: 10px;">
                        </div>
                        <div class="col-md-12" id="tabla_detalle">
                            <table role="table" aria-busy="false" aria-colcount="4" class="table b-table">
                                <thead role="rowgroup" class="">
                                    <tr role="row" class="">
                                        <th width="20%" role="columnheader" scope="col" aria-colindex="1" class=""><div>{{trans('paso2.MEDICAMENTO')}}</div></th>
                                        <th width="10%" role="columnheader" scope="col" aria-colindex="2" class=""><div>{{trans('paso2.Cantidad')}}</div></th>
                                        <th width="55%" role="columnheader" scope="col" aria-colindex="3" class=""><div>{{trans('paso2.POSOLOGIA')}}</div></th>
                                        <th width="15%" role="columnheader" scope="col" aria-colindex="4" class=""><div>{{trans('paso2.ACCION')}}</div></th>
                                    </tr>
                                </thead>
                                <tbody role="rowgroup">
                                    @foreach($detalles as $detalle)
                                        @php
                                            $desabilitar ="";
                                            if($detalle->descargo == 1){
                                                $desabilitar = "disabled";
                                            }
                                        @endphp
                                        <tr role="row" class="">
                                            <td aria-colindex="1" role="cell" class="b-table-sticky-column"><span class="text-info">@if(!is_null($detalle)){{$detalle->nombre}}@endif</span></td>
                                            <td aria-colindex="2" role="cell" class=""><input class="form-control" type="number" name="cantidad{{$detalle->id}}" id="cantidad{{$detalle->id}}" value="{{$detalle->cantidad}}" {{$desabilitar}} onchange="editar_medicina('{{$detalle->id}}')" ></td>
                                            <td aria-colindex="3" role="cell" class="">
                                                <textarea wrap="soft" class="mb-1 mb-xl-0 form-control" style="resize: none; overflow-y: scroll; height: 92px;" name="dosis{{$detalle->id}}" id="dosis{{$detalle->id}}" {{$desabilitar}} onchange="editar_medicina('{{$detalle->id}}')">@if(!is_null($detalle)){{$detalle->dosis}}@endif</textarea>
                                            </td>
                                            <td aria-colindex="4" role="cell" class="">
                                                @if($detalle->descargo == 0)
                                                    <button id="edit{{$detalle->id}}" type="button" class="btn btn-warning btn-sm" onclick="">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button id="del{{$detalle->id}}" type="button" class="btn btn-danger btn-sm" onclick="eliminar_medicina('{{$detalle->id}}');">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                @else
                                                    <span class="text-success">{{trans('paso2.Entregado')}}</span>
                                                @endif
                                                        
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                                               
                    </div>
                </div>

            </form>
        </div>  
        <br>
        <br>

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

    $('#agregar_cie').click( function(){

        if($('#cie10').val()!='' ){
            if($('#pre_def').val()!='' ){
                guardar_cie10_consulta();
                
            }else{
                alert("Seleccione Presuntivo o Definitivo");
            }
        }else{
            alert("Seleccione CIE10");
        }

        $('#codigo').val('');
        $('#cie10').val('');
        

    });

    function guardar_cie10_consulta(){
        $.ajax({
         
            type: 'post',
            url:"{{route('hospital.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data:  $("#form_diagnostico").serialize(),
            success: function(data){
                
                var indexr = data.count-1
                var table = document.getElementById("tdiagnostico");
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
                cell4.innerHTML = '<a href="javascript:eliminar_ing('+data.id+', '+data.id_hcproc+');" class="btn btn-xs btn-danger btn-xs"><i class="fa fa-trash"></i></a>';
                //$('#prescripcion').empty().html(anterior+ data.value +': \n' +data.dosis);                
                console.log('guardo');
                //();

            },
            error: function(data){
                    //console.log(data);
                }
        })
    }

    cargar_tabla_cie({{$solicitud->id_hcproc}});


    function cargar_tabla_cie(id_hcproc){
        $.ajax({
            url:`{{route('hospital.cargar_tabla_cie_hos')}}`,
            dataType: "json",
            type: 'get',
            data:{
                'id_hcproc' : id_hcproc 
            },
            success: function(data){
               // console.log(data);
                var table = document.getElementById("tdiagnostico");

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
                    cell4.innerHTML = '<a href="javascript:eliminar_ing('+value.id+', '+id_hcproc+');" class="btn btn-xs btn-danger btn-xs"><i class="fa fa-trash"></i></a>';
                    //alert(index);
                });
            }
        })
    }

    function guardar_evolucion(){


        $.ajax({
            type: 'post',
            url:"{{route('formulario005.guardar_evolucion',['id_evol' => $evolucion->id])}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data:  $("#form_evolucion").serialize(),
            success: function(data){

                //console.log(data);
                return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`); 
                
            },
            error: function(data){
            }
        })
    }


    $('.select2').select2({
    ajax: {
      url: '{{route("hospitalizacion.genericos")}}',
      data: function(params) {
        var query = {
          search: params.term,
          type: 'public'
        }
        return query;
      },
      processResults: function(data) {
        // Transforms the top-level key of the response object from 'items' to 'results'
        //console.log(data);

        return {
          results: data
        };
      }

    }
  });

  function buscar_nombre_medicina(){
    var id_solicitud = $('#solicitud_id').val();

      $.ajax({
        type: 'post',
        url:"{{route('formulario005.f05_medicina_guardar',['id_rec' => $receta->id])}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'html',
        data: { 
          'id_generico' : $("#generico").val(),
          'id_solicitud': id_solicitud,
        },

          success: function(data){

            console.log(data);
            $('#tabla_detalle').html(data)
            
            },
            error: function(data){
            }
      })
  }

  function eliminar_medicina(id_detalle){
    console.log(id_detalle);
    var id_solicitud = $('#solicitud_id').val();
    //var id_receta = $('#id_receta').val();

    $.ajax({
        async: true,
        type: "GET",
        url: "{{url('hospital/formu_005/eliminar_medicina')}}/"+id_detalle,
        data: {
          'id_solicitud': id_solicitud,
          //'id_receta': id_receta,
        },
        datatype: "html",
        success: function(datahtml){
            $("#tabla_detalle").html(datahtml);

        },
        error:  function(){
           // alert('error al cargar');
        }
    });
  }

  function editar_medicina(id_detalle){

    $.ajax({
        type: 'post',
        url: "{{url('hospital/form005/editar_medicina')}}/"+id_detalle,
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#form_medicina").serialize(),
        success: function(data){

        },
        error:  function(){
            alert('error al cargar');
        }
    });
  }





</script>

        

