<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
<div class="card" id="catorceavopaso">
    <div class="card-header bg bg-primary">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1">
                </div>
                <div class="col-md-9">
                    <label style="color: white;" class="control_label">{{trans('pasos.CondicionAlEgresoDeEmergencia')}}</label>
                </div>

                
            </div>


        </div>
    </div>
    <div class="card-body">
        <div class="col-md-12" style="margin-top: 10px;">
            <form action="{{route('hospital.guardar_catorceavo')}}" id="form" method="post">
                <input type="hidden" name="id" value="{{$id_solicitud}}" />
                {{ csrf_field() }}
                <div class="row">
                    <div class="form-group col-md-12">

                        <div class="row">
                            <div class="col-md-12">
                                <b>{{trans('paso2.CondicionesdeAlta')}}</b>

                            </div>
                            <div class="col-md-12">
                                &nbsp;
                            </div>
                            <div class="col-md-3">
                                {{trans('paso2.AltaDefinitiva')}}
                            </div>
                            <div class="col-md-3">
                                <input type="radio" class="luffy domici" name="nocheck[]" @if(!is_null($new)) @if($new->seccion==1) checked @endif @endif value="1">

                            </div>
                            <div class="col-md-3">
                                {{trans('paso2.ConsultaExterna')}}
                            </div>
                            <div class="col-md-3">
                                <input type="radio" class="luffy" name="nocheck[]" @if(!is_null($new)) @if($new->seccion==2) checked @endif @endif value="2">

                            </div>
                            <div class="col-md-3">
                                {{trans('paso2.ObservaciondeEmergencia')}}
                            </div>
                            <div class="col-md-3">
                                <input type="radio" class="luffy" name="nocheck[]" @if(!is_null($new)) @if($new->seccion==3) checked @endif @endif value="3">
                            </div>
                            <div class="col-md-3">
                                {{trans('paso2.Hospitalizacion')}}
                            </div>
                            <div class="col-md-3">
                                <input type="radio" class="luffy as" name="nocheck[]" @if(!is_null($new)) @if($new->seccion==4) checked @endif @else @endif value="4">
                            </div>
                            <div class="col-md-3">
                                {{trans('paso2.Referencia')}}
                            </div>
                            <div class="col-md-3">
                                <input type="radio" class="luffy" name="nocheck[]" @if(!is_null($new)) @if($new->seccion==5) checked @endif @endif value="5">
                            </div>
                            <div class="col-md-3">
                                {{trans('paso2.ReferenciaInversa')}}
                            </div>
                            <div class="col-md-3">
                                <input type="radio" class="luffy" name="nocheck[]" @if(!is_null($new)) @if($new->seccion==6) checked @endif @endif value="6">
                            </div>
                            <div class="col-md-3">
                                {{trans('paso2.Derivacion')}}
                            </div>
                            <div class="col-md-3">
                                <input type="radio" class="luffy" name="nocheck[]" @if(!is_null($new)) @if($new->seccion==7) checked @endif @endif value="7">
                            </div>
                            <div class="col-md-3">
                                {{trans('paso2.Vivo')}}
                            </div>
                            <div class="col-md-3">
                                <input type="radio" class="luffy" name="nocheck[]" @if(!is_null($new)) @if($new->seccion==8) checked @endif @endif value="8">
                            </div>
                            <div class="col-md-3">
                                {{trans('paso2.Estable')}}
                            </div>
                            <div class="col-md-3">
                                <input type="radio" class="luffy" name="nocheck[]" @if(!is_null($new)) @if($new->seccion==9) checked @endif @endif value="9">
                            </div>
                            <div class="col-md-3">
                                {{trans('paso2.Inestable')}}
                            </div>
                            <div class="col-md-3">
                                <input type="radio" class="luffy" name="nocheck[]" @if(!is_null($new)) @if($new->seccion==10) checked @endif @endif value="10">
                            </div>
                            <div class="col-md-3">
                                {{trans('paso2.Fallecido')}}
                            </div>
                            <div class="col-md-3">
                                <input type="radio" class="luffy" name="nocheck[]" @if(!is_null($new)) @if($new->seccion==11) checked @endif @endif value="11">
                            </div>
                        </div>
                    </div>
                    {{--<div class="form-group col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <b>{{trans('emergencia.SALA')}}</b>
                            </div>
                            <div class="col-md-12">
                                <select name="id_sala" id="lugar" class="form-control select_no">
                                    <option value="">{{trans('paso2.Seleccionee')}} ...</option>
                                    @foreach($sala as $x)
                                    <option @if(!is_null($new)) @if($new->id_sala==$x->id) selected="selected" @endif @endif value="{{$x->id}}">{{$x->nombre_sala}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="form-group col-md-6">
                        <input type="hidden" name="id" id="sevices" value="0">
                        <div class="row">
                            <div class="col-md-12">
                                <b>{{trans('paso2.CondiciondeAlta')}}</b>
                            </div>
                            <div class="col-md-12">
                                <select class="form-control select_no" name="id_condicion" id="condicion">
                                    <option value="">{{trans('paso2.Seleccionee')}} ...</option>
                                    @foreach($condiciones as $c)
                                    <option @if(!is_null($new)) @if($new->id_condicion==$c->id) selected="selected" @endif @endif value="{{$c->id}}">{{$c->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>--}}
                    <!-- poner trans despues -->
                    <div class="form-group col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <b>{{trans('hospitalizacion.Traspaso')}}</b>
                            </div>
                            <div class="col-md-12">
                                <select class="form-control select_no" name="paso" id="paso">
                                    <option value="">Seleccione</option>
                                    @foreach($pasos as $paso)
                                    <option @if(!is_null($new)) @if($new->seccion==$paso->id) selected='selected' @endif @endif value="{{$paso->id}}">{{$paso->nombre_paso}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <b>{{trans('paso2.DiasdeReposo')}}</b>
                            </div>
                            <div class="form-group col-md-12">
                                <input type="text" class="form-control" name="dias_reposo" id="dias_reposo" @if(!is_null($new)) value="{{$new->dias_reposo}}" @endif placeholder="{{trans('Ingrese días de reposo')}}">
                            </div>
                        </div>


                    </div>

                    {{--<div class="form-group col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <b> {{trans('paso2.ServiciodeReferencia')}} </b>
                            </div>

                            <div class="col-md-12">
                                <input type="text" class="form-control estable" name="servicio_reposo" @if(!is_null($new)) value="{{$new->servicio_reposo}}" @endif id="servicio_referencia" placeholder="{{trans('Ingrese servicios de referencia')}}">
                            </div>
                        </div>

                    </div>--}}

                    <div class="form-group col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <b>{{trans('paso2.Establecimiento')}}</b>
                            </div>
                            <div class="col-md-12">
                                <!-- select class="form-control select_no estable" name="id_establecimiento" id="establecimiento">
                                    <option value="">{{trans('paso2.Seleccionee')}} ...</option>
                                    @foreach($establecimientos as $h)
                                    <option @if(!is_null($new)) @if($new->id_establecimiento==$h->id) selected='selected' @endif @endif value="{{$h->id}}">{{$h->nombre}}</option>
                                    @endforeach
                                </select -->

                                <input type="text" class="form-control" name="establecimiento" @if(!is_null($new)) value="{{$new->establecimiento}}" @endif id="establecimiento">


                            </div>
                        </div>

                    </div>

                    {{--<div class="form-group col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <b>{{trans('paso2.Causa')}}</b>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="causa" @if(!is_null($new)) value="{{$new->causa}}" @endif id="causa" placeholder="{{trans('Escribe causa')}}">
                            </div>
                        </div>

                    </div>--}}

                    <div class="form-group col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <b>{{trans('paso2.Observaciones')}}</b>
                            </div>
                            <div class="col-md-12">
                                <textarea class="form-control" name="observaciones" cols="3" rows="3">@if(!is_null($new)) {{$new->observaciones}} @endif </textarea>
                            </div>
                        </div>

                    </div>

                    <div class="form-group col-md-6">
                        <b>{{trans('paso2.Fecha')}}</b>
                    </div>
                    <div class="form-group col-md-6">
                        <input type="text" data-input="true" class="form-control input-sm flatpickr-date-time active" name="fecha" id="fecha" @if(!is_null($new)) value="{{$new->fecha}}" @else value="{{date('Y-m-d H:s')}}" @endif autocomplete="off">
                    </div>
                    <div class="form-group col-md-6">
                        <b>{{trans('paso2.NombreProfesional')}}</b>
                    </div>
                    <div class="form-group col-md-6">
                        <select class="form-control" name="id_doctor" id="nombre_profesional">
                            <option value="">{{trans('paso2.Seleccionee')}} ...</option>
                            @foreach($doctores as $d)
                            <option @if($d->id==Auth::user()->id) selected='selected' @endif value="{{$d->id}}">Dr(a). {{$d->apellido1}} {{$d->nombre1}} {{$d->nombre2}} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12" style="text-align: center;">
                        <button class="btn btn-primary but" type="button" onclick="guardar14()"> <i class="fa fa-save"></i> &nbsp; @if(!is_null($new)) {{trans('paso2.Actualizar')}} @else {{trans('paso2.Guardar')}} @endif </button>
                    </div>
                </div>
            </form>

        </div>

    </div>

</div>
<script src="{{asset('ho/app-assets/js/core/app-menu.js')}}" defer></script>
<script src="{{asset('ho/app-assets/js/core/app.js')}}" defer> </script>
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{asset('ho/app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
<script src="{{asset('ho/app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
<script>
    $(document).ready(function() {
        $("#sevices").val($("#solicitudGeneral").val());
        $('.as').click();
        @if(!is_null($new))
        @if($new->paso == 4)
        $('.but').attr('disabled', 'disabled');
        @endif
        @endif
    });

    function regresar14() {
        console.log("add");
        $("#catorceavopaso").remove();
        $("#cambio14").show();
    }
    $('body').on('click', '.luffy', function() {
        var showcheck = $(this).val();
        //console.log(showcheck);
        if (showcheck == '1') {
            //$('#form input').attr('readonly', 'readonly');
            //$('.select_no').attr('disabled', 'disabled');
            $('#lugar').attr('readonly', true);
            $('#paso').attr('readonly', true);
            $('#lugar').attr('required', false);
            $('#lugar').attr('required', false);
            $('#servicio_referencia').attr('readonly', true);
            $('#servicio_referencia').attr('required', false);
        } else if (showcheck == '2') {
            /*  $('#form input').attr('readonly', false);
            
             $('.select_no').attr('disabled', false);
             $('.select_no').attr('required',false);
             $('.estable').attr('readonly', true); */
            $('#lugar').attr('readonly', false);
            $('#lugar').attr('required', false);
            $('#paso').attr('readonly', true);
            $('#paso').attr('required', true);
            $('#servicio_referencia').attr('readonly', true);
            $('#servicio_referencia').attr('required', false);
        } else if (showcheck == '3') {
            $('#lugar').attr('readonly', false);
            $('#paso').attr('readonly', true);
            $('#servicio_referencia').attr('readonly', true);
            $('#servicio_referencia').attr('required', false);
            /* $('#form input').attr('readonly', false);
           
            $('.select_no').attr('disabled', false); */
        } else if (showcheck == '4') {
            $('#lugar').attr('readonly', false);
            $('#paso').attr('readonly', false);
            $('#lugar').attr('required', true);
            $('#paso').attr('required', true);
            $('#servicio_referencia').attr('readonly', true);
            $('#servicio_referencia').attr('required', false);
            /*  $('#form input').attr('readonly', false);
             $('.select_no').attr('disabled', true); */
            //$('#form input').attr('required',true);
            //$('.estable').attr('readonly', true);
        } else if (showcheck == '5') {
            //$('#form input').attr('readonly', false);
            //$('#form input').attr('required',true);
            //$('.select_no').attr('disabled', false);
            //$('.estable').attr('readonly', false);'
            $('#lugar').attr('readonly', true);
            $('#paso').attr('readonly', true);
            $('#lugar').attr('required', false);
            $('#paso').attr('required', false);
            $('#servicio_referencia').attr('readonly', false);
            $('#servicio_referencia').attr('required', true);
        }

    });

    function guardar14() {
        var response = $('#paso').val();
        var check = 0;
        // var checks= $('.domici').attr('checked',true);
        //alert(checks);
        if ($('#form').valid()) {
            $.ajax({
                url: "{{route('hospital.guardar_catorceavo')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                data: $("#form").serialize(),
                success: function(data) {
                    alertas(data.respuesta, data.respuesta, data.msj)
                    if (data.respuesta == 'success') {
                        setTimeout(function() {
                            window.location.href = "{{url('hospital/modulos/index')}}/" + data.paso;
                        }, 1500)
                    }

                },
                error: function(data) {
                    console.log(data.responseText);
                }
            });
        }



    }
</script>