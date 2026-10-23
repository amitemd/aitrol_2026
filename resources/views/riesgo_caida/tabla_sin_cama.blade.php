<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
    <thead style="background-color: #337ab7;color:white;">
        <tr>
            <th scope="col">Hospital</th>
            <th scope="col">Paciente</th>
            <th scope="col">Acción</th>
        </tr>
    </thead>
    <tbody>
        @php
        $calc = count($paciente_agenda);
        @endphp
        @if($calc < 1 ) 
        @foreach($db1 as $value) @php $nombre=Sis_medico\Paciente::where('id',$value->id_paciente)->first();
            $edad = $nombre->fecha_nacimiento;
            $edad2 = new DateTime($edad);
            $fecha= date("Y-m-d");
            $fecha1 = new DateTime($fecha);
            $edad_nueva = $fecha1->diff($edad2);
            $edad1 = $edad_nueva->y;
            $hospi_num = Sis_medico\Hospital::where('id', 2)->first();
            $camilla_gestion=Sis_medico\Camilla_Gestion::where('id_paciente',$value->id_paciente)->where('id_agenda',$value->id)->where('num_atencion',1)->where('sala',' ')->first();
            @endphp
            @if(is_null($camilla_gestion))
            <tr style="text-align: center;">
                <td>{{$hospi_num->nombre_hospital}}</td>
                <td>{{$nombre->nombre1}} {{$nombre->nombre2}} {{$nombre->apellido1}} {{$nombre->apellido2}}</td>
                <td>@if (($edad1 >=13))
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{route('riesgo.mayorsincama',[$value->id_paciente,$value->id])}}" class="btn btn-primary">
                        <i class="glyphicon glyphicon-edit" aria-hidden="true"> Formulario</i>
                    </a>
                    @elseif(($edad1 < 13)) <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{route('riesgo.menorsincama',[$value->id_paciente,$value->id])}}" class="btn btn-success">
                            <i class="glyphicon glyphicon-edit" aria-hidden="true"> Formulario</i>
                        </a>
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
            $nombre = Sis_medico\Paciente::where('id',$value->id_paciente)->first();
            $edad = $nombre->fecha_nacimiento;
            $edad2 = new DateTime($edad);
            $fecha= date("Y-m-d");
            $fecha1 = new DateTime($fecha);
            $edad_nueva = $fecha1->diff($edad2);
            $edad1 = $edad_nueva->y;
            $hospi_num = Sis_medico\Hospital::where('id', 2)->first();
            $camilla_gestion=Sis_medico\Camilla_Gestion::where('id_paciente',$value->id_paciente)->where('id_agenda',$value->id)->where('num_atencion',1)->where('sala','')->first();
            @endphp
            @if(is_null($camilla_gestion))
            <tr style="text-align: center;">
                <td>{{$hospi_num->nombre_hospital}}</td>
                <td>{{$nombre->nombre1}} {{$nombre->nombre2}} {{$nombre->apellido1}} {{$nombre->apellido2}}</td>
                <td>
                    @if (($edad1 >=13))
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{route('riesgo.mayorsincama',[$value->id_paciente,$value->id])}}" class="btn btn-primary">
                        <i class="glyphicon glyphicon-edit" aria-hidden="true"> Formulario</i>
                    </a>
                    @elseif(($edad1 < 13)) <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{route('riesgo.menorsincama',[$value->id_paciente,$value->id])}}" class="btn btn-success">
                            <i class="glyphicon glyphicon-edit" aria-hidden="true"> Formulario</i>
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
</script>