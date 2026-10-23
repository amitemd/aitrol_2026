<div class="card">
    <div class="card-body" style="margin-bottom: 1px;padding: 0;">
        <br>
        <form id="form_epicrisis" method="POST">
            {{ csrf_field() }}
            <div class="row" style="padding-top: 10px;">
                <input type="hidden" name="solicitud_id" id="solicitud_id" value="{{$solicitud->id}}">

                <div class="col-md-12">
                    <label><b> {{trans('hospitalizacion.Evolución')}}: </b> </label>
                    <textarea name="n_epicrisis" id="n_epicrisis" class="form-control input-sm" rows="3">{{$epicrisis->cuadro_clinico}}</textarea>
                </div>
                <div class="col-md-12">
                    <label><b> Favorable/Desfavorable </b></label>
                    <textarea name="favorable_des" id="favorable_des" class="form-control input-sm" rows="3">{{$epicrisis->favorable_des}}</textarea>
                </div>
                <div class="col-md-12">
                    <label><b> Complicacion </b></label>
                    <textarea name="complicacion" id="complicacion" class="form-control input-sm" rows="3">{{$epicrisis->complicacion}}</textarea>
                </div>
                <div class="col-md-12">
                    <label><b> Hallazgo </b></label>
                    <textarea name="hallazgo" id="hallazgo" class="form-control input-sm" rows="3">{{$epicrisis->hallazgo}}</textarea>
                </div>
                
               
            </div>
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-6">
                    <button class="btn btn-primary" type="button" id="guardar_diagnostico" onclick="guardar_epicrisis();"> <span class="fa fa-save"> {{trans('hospitalizacion.Guardar')}}</span> </button>
                </div>
            </div>
        </form>
        <br>
        <br>
        <div class="form-group col-md-12">
            @php
            $alergias = $solicitud->paciente->a_alergias; $txt_al = '';$cont = 0;
            foreach($alergias as $alergia){
            if($cont < 2) { if($cont==0){ $txt_al=$alergia->principio_activo->nombre; }
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
                        <label>{{trans('paso2.Diagnostico')}}</label>
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

    </div>
</div>

<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
    $('#cie10').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{route('epicrisis.cie10_nombre')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    term: request.term
                },
                dataType: "json",
                type: 'post',
                success: function(data) {
                    response(data);
                }
            })
        },
        minLength: 2,
    });


    $('#cie10').change(function() {
        $.ajax({
            type: 'post',
            url: "{{route('epicrisis.cie10_nombre2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $('#cie10'),
            success: function(data) {
                if (data != '0') {

                    $('#codigo').val(data.id);
                }
            },
            error: function(data) {}
        })
    });

    $('#agregar_cie').click(function() {

        if ($('#cie10').val() != '') {
            if ($('#pre_def').val() != '') {
                guardar_cie10_consulta();

            } else {
                alert("Seleccione Presuntivo o Definitivo");
            }
        } else {
            alert("Seleccione CIE10");
        }

        $('#codigo').val('');
        $('#cie10').val('');


    });

    function guardar_cie10_consulta() {
        $.ajax({

            type: 'post',
            url: "{{route('hospital.agregar_cie10')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#form_diagnostico").serialize(),
            success: function(data) {

                var indexr = data.count - 1
                var table = document.getElementById("tdiagnostico");
                var row = table.insertRow(indexr);
                row.id = 'tdiag' + data.id;

                var cell1 = row.insertCell(0);
                cell1.innerHTML = '<b>' + data.cie10 + '</b>';

                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.descripcion;

                var vpre_def = '';
                if (data.pre_def != null) {
                    vpre_def = data.pre_def;
                }
                var cell3 = row.insertCell(2);
                cell3.innerHTML = vpre_def;

                var cell4 = row.insertCell(3);
                cell4.innerHTML = '<a href="javascript:eliminar_ing(' + data.id + ', ' + data.id_hcproc + ');" class="btn btn-xs btn-danger btn-xs"><i class="fa fa-trash"></i></a>';
                //$('#prescripcion').empty().html(anterior+ data.value +': \n' +data.dosis);                
                console.log('guardo');
                //();

            },
            error: function(data) {
                //console.log(data);
            }
        })
    }

    cargar_tabla_cie({{$solicitud -> id_hcproc}});


    function cargar_tabla_cie(id_hcproc) {
        $.ajax({
            url: `{{route('hospital.cargar_tabla_cie_hos')}}`,
            dataType: "json",
            type: 'get',
            data: {
                'id_hcproc': id_hcproc
            },
            success: function(data) {
                // console.log(data);
                var table = document.getElementById("tdiagnostico");

                $.each(data, function(index, value) {

                    var row = table.insertRow(index);
                    row.id = 'tdiag' + value.id;

                    var cell1 = row.insertCell(0);
                    cell1.innerHTML = '<b>' + value.cie10 + '</b>';

                    var cell2 = row.insertCell(1);
                    cell2.innerHTML = value.descripcion;

                    var vpre_def = '';
                    if (value.pre_def != null) {
                        vpre_def = value.pre_def;
                    }
                    var cell3 = row.insertCell(2);
                    cell3.innerHTML = vpre_def;

                    var cell4 = row.insertCell(3);
                    cell4.innerHTML = '<a href="javascript:eliminar_ing(' + value.id + ', ' + id_hcproc + ');" class="btn btn-xs btn-danger btn-xs"><i class="fa fa-trash"></i></a>';
                    //alert(index);
                });
            }

        })
    }

    function guardar_epicrisis() {


        $.ajax({
            type: 'post',
            url: "{{route('quirofano.guardar_epicrisis',['id_epi' => $epicrisis->id])}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#form_epicrisis").serialize(),
            success: function(data) {

                //console.log(data);
                return Swal.fire(`{{trans('proforma.GuardadoCorrectamente')}}`);

            },
            error: function(data) {}
        })
    }
</script>