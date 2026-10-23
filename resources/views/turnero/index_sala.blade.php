<style type="text/css">
    td,
    th {
        font-weight: bold;
        padding: 4px !important;
        text-align: center;
    }
</style>
<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
    <div class="row">
        <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Turno</th>
                        <th>Sala del Procedimiento</th>
                        <th>Hospital del Procedimiento</th>
                        <th>Tipo</th>
                        <th>Módulo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($registro as $value)
                    @php
                    if(!empty($value->id_paciente)){
                    $nombrepaciente = Sis_medico\User::where('id',$value->id_paciente)->first();
                    }else{
                    $nombrepaciente = Sis_medico\User::where('id',$value->cedula)->first();
                    }
                    $sala = Sis_medico\Sala::where('id',$value->id_sala)->first();
                    $hospital = Sis_medico\Hospital::where('id',$value->id_hospital)->first();
                    @endphp

                    <tr>
                        <td>@if($nombrepaciente == null)  @else {{ substr($nombrepaciente->apellido1,0,1)}}. @if($nombrepaciente->apellido2!='(N/A)'){{ substr($nombrepaciente->apellido2,0,1)}}.@endif {{ substr($nombrepaciente->nombre1,0,1)}}. @if($nombrepaciente->nombre2!='(N/A)'){{ substr($nombrepaciente->nombre2,0,1)}}.@endif @endif</td>
                        <td>{{ $value->turno}}{{substr($value->letraproc,0,1)}}</td>
                        <td>{{ $sala->nombre_sala }}</td>
                        <td>{{ $hospital->nombre_hospital }}</td>
                        <td>@if(!empty($value->id_paciente)) AGENDADO @else NUEVO @endif</td>
                        <td>@if(($value->modulo)==1) MODULO 1 @elseif(($value->modulo)==2) MODULO 2  @endif</td>
                        <td>@if($value->estado==0) EN ESPERA @elseif($value->estado == 1) AVANZE AL MODULO {{$value->modulo}} @endif</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>