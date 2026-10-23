<table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
    <thead style="background-color: #337ab7;color:white;">
        <tr role="row" id="cabezera">
            <th>Camilla</th>
            <th>Nombre Paciente</th>
            <th>Hospital</th>
            <th>Desde</th>
            <th>Hasta</th>
            <th>Estado Cama</th>
            <th>Descarrgar</th>
        </tr>
    </thead>
    <tbody id="cuerpo">
        @php $cont = 1; @endphp
        @foreach($registro as $val)
        @php
        $paciente = Sis_medico\Camilla_Gestion::where('id',$val->id_camagestion)->first();
        $nombre = Sis_medico\Paciente::where('id',$paciente->id_paciente)->first();
        $edad = $nombre->fecha_nacimiento;
        $edad2 = new DateTime($edad);
        $fecha= date("Y-m-d");
        $fecha1 = new DateTime($fecha);
        $edad_nueva = $fecha1->diff($edad2);
        $edad1 = $edad_nueva->y;
        $cama = Sis_medico\Camilla::where('id',$paciente->camilla)->first();
        $hospital = Sis_medico\Hospital::where('id',2)->first();
        @endphp
        <tr style="text-align: center;">
            <td>{{$cont}}</td>
            <td>@if(empty($nombre)) @else {{$nombre->nombre1}} {{$nombre->nombre2}} {{$nombre->apellido1}} {{$nombre->apellido2}} @endif</td>
            <td>{{$hospital->nombre_hospital}}</td>
            <td>{{$val->fecha_cambio}}</td>
            <td>{{$val->fecha_cambio}}</td>
            <td @if($val->estado == 0 ) style="text-align:center;color:white;background:cadetblue" @else style="text-align:center;color:white;background:red" @endif>@if($val->estado == 0 ) ALTA @else PROCESO @endif</td>
            <td>@if (($edad1 >=13))
                <a href="{{route('riesgo.pdf',['id'=>$paciente->id_agenda])}}" target="_blank" class="btn btn-danger btn-gray"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                @elseif(($edad1 < 13)) <a href="{{route('riesgo_menor.pdf',['id'=>$paciente->id_agenda])}}" target="_blank" class="btn btn-danger btn-gray"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                    @endif
            </td>
        </tr>
        @php $cont ++; @endphp
        @endforeach
    </tbody>
</table>
<script text="text/javascript">
    $(document).ready(function() {
        $('#example2').DataTable({
            'paging': true,
            'lengthChange': false,
            'searching': false,
            'responsive': false,
            'ordering': false,
            'info': false,
            'autoWidth': false,
            'sInfoEmpty': false,
            'sInfoFiltered': false,
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        });
    });
</script>