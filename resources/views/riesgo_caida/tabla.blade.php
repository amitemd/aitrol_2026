<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
    <thead style="background-color: #337ab7;color:white;">
        <tr>
            <th scope="col">Hospital</th>
            <th scope="col">Camilla</th>
            <th scope="col">Paciente</th>
            <th scope="col">Acción</th>
        </tr>
    </thead>
    <tbody>
        @php
        $calc = count($paciente_agenda);
        @endphp
        @if($calc < 1 ) @foreach($db1 as $value) @php $camilla_gestion=Sis_medico\Camilla_Gestion::where('id_paciente',$value->id_paciente)->where('id_agenda',$value->id)->where('num_atencion',1)->first();
            $nombre = Sis_medico\Paciente::where('id',$value->id_paciente)->first();
            $edad = $nombre->fecha_nacimiento;
            $edad2 = new DateTime($edad);
            $fecha= date("Y-m-d");
            $fecha1 = new DateTime($fecha);
            $edad_nueva = $fecha1->diff($edad2);
            $edad1 = $edad_nueva->y;
            $camilla_num = Sis_medico\Camilla::where('id', $camilla)->first();
            $hospi_num = Sis_medico\Hospital::where('id', $hospi)->first();
            @endphp
            @if(is_null($camilla_gestion))
            <tr style="text-align: center;">
                <td>{{$hospi_num->nombre_hospital}}</td>
                <td>{{$camilla_num->nombre_camilla}}</td>
                <td>{{$nombre->nombre1}} {{$nombre->nombre2}} {{$nombre->apellido1}} {{$nombre->apellido2}}</td>
                <td>@if (($edad1 >=13))
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" onclick="verificarEstadoCamilla(`{{$value->id_paciente}}`,`{{$camilla_num->id}}`,`{{$value->id}}`,1)" class="btn btn-primary">
                        Formulario
                    </button>
                    <!-- <a href="{{route('riesgo.form_mayor',[$value->id_paciente,$camilla_num->id,$value->id])}}" >
                        <i class="glyphicon glyphicon-edit" aria-hidden="true"> Formulario</i>
                    </a> -->
                    @elseif(($edad1 < 13)) <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <button type="button" onclick="verificarEstadoCamilla(`{{$value->id_paciente}}`,`{{$camilla_num->id}}`,`{{$value->id}}`,0)" class="btn btn-success">
                            Formulario
                        </button>
                        <!-- <a href="{{route('riesgo.form_menor',[$value->id_paciente,$camilla_num->id,$value->id])}}" class="btn btn-success">
                            <i class="glyphicon glyphicon-edit" aria-hidden="true"> Formulario</i>
                        </a> -->
                        @endif
                </td>
            </tr>
            @endif
            @endforeach
            @endif
            @if($calc >= 1)
            @foreach($db as $value)
            @php
            $nuevaCon = [];
            $camilla_gestion=Sis_medico\Camilla_Gestion::where('id_paciente',$value->id_paciente)->where('id_agenda',$value->id)->where('num_atencion',1)->where('sala','')->first();
            if(is_null($camilla_gestion)){
            $nuevaCon = Sis_medico\Camilla_Gestion::where('id_paciente',$value->id_paciente)->where('id_agenda',$value->id)->where('alta',1)->first();
            }
            if(is_null($nuevaCon)){
            $nuevaCon = Sis_medico\Camilla_Gestion::where('id_paciente',$value->id_paciente)->where('id_agenda',$value->id)->where('sala',1)->first();
            }
            $nombre = Sis_medico\Paciente::where('id',$value->id_paciente)->first();
            $edad = $nombre->fecha_nacimiento;
            $edad2 = new DateTime($edad);
            $fecha= date("Y-m-d");
            $fecha1 = new DateTime($fecha);
            $edad_nueva = $fecha1->diff($edad2);
            $edad1 = $edad_nueva->y;
            $camilla_num = Sis_medico\Camilla::where('id', $camilla)->first();
            $hospi_num = Sis_medico\Hospital::where('id', $hospi)->first();
            @endphp
            @if(is_null($camilla_gestion))
            <tr style="text-align: center;">
                <td>{{$hospi_num->nombre_hospital}}</td>
                <td>{{$camilla_num->nombre_camilla}}</td>
                <td>{{$nombre->nombre1}} {{$nombre->nombre2}} {{$nombre->apellido1}} {{$nombre->apellido2}}</td>
                <td>
                    @if(is_null($nuevaCon))
                    @if (($edad1 >=13))
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" onclick="verificarEstadoCamilla(`{{$value->id_paciente}}`,`{{$camilla_num->id}}`,`{{$value->id}}`,1)" class="btn btn-primary">
                        Formulario
                    </button>
                    <!-- <a href="{{route('riesgo.form_mayor',[$value->id_paciente,$camilla_num->id,$value->id])}}" class="btn btn-primary">
                        <i class="glyphicon glyphicon-edit" aria-hidden="true"> Formulario</i>
                    </a>-->
                    @elseif(($edad1 < 13)) <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="button" onclick="verificarEstadoCamilla(`{{$value->id_paciente}}`,`{{$camilla_num->id}}`,`{{$value->id}}`,0)" class="btn btn-primary">
                            Formulario
                        </button>
                       <!-- <a href="{{route('riesgo.form_menor',[$value->id_paciente,$camilla_num->id,$value->id])}}" class="btn btn-success">
                            <i class="glyphicon glyphicon-edit" aria-hidden="true"> Formulario</i>
                        </a> -->
                        @endif
                        @else
                        <a onclick="desabilitar()" id="botonG" href="{{route('camilla.guardar_sinriesgo',[$value->id_paciente,$camilla_num->id,$value->id])}}" class="btn btn-warning">
                            <i class="glyphicon glyphicon-edit" aria-hidden="true"> Ocupar</i>
                        </a>
                        @endif
                </td>
            </tr>
            @endif
            @endforeach
            @endif
    </tbody>
</table>
<script text="text/javascript">
    function desabilitar() {
        $("#botonG").attr("disabled", "disabled");
    }

    $(document).ready(function() {
        $('#example2').DataTable({
            'paging': true,
            'lengthChange': false,
            'searching': true,
            'responsive': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'sInfoEmpty': false,
            'sInfoFiltered': true,
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        });
    });

    function verificarEstadoCamilla(id_paciente, id_camilla, id_agenda, checkMm) {
        $.ajax({
            url: "{{route('camilla.verificacioncamilla')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                id_paciente,
                id_camilla,
                id_agenda
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data) {
                    Swal.fire({
                        position: "center",
                        icon: "warning",
                        title: "La cama esta ocupada",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload(true);
                    })


                } else {
                    let url = `{{route('riesgo.form_mayor',['id_paciente' => ':id_paciente', 'id_camilla' => ':id_camilla', 'id_agenda' => ':id_agenda'])}}`;
                    if (checkMm == 0) {
                        url = `{{route('riesgo.form_menor',['id_paciente' => ':id_paciente', 'id_camilla' => ':id_camilla', 'id_agenda' => ':id_agenda'])}}`;
                    }
                    url = url.replace(':id_paciente', id_paciente);
                    url = url.replace(':id_camilla', id_camilla);
                    url = url.replace(':id_agenda', id_agenda);
                    window.location.href = url;
                }
            },
            error: function(xhr, status) {
                alert('Existió un problema');
            },
        });
    }
</script>