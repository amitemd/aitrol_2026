<div class="modal-content" style="width:auto">
    <div class="modal-header">
        <input type="hidden">
        <button style="line-height: 30px;" id="button" type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="text-align: center;color:black;font-size:30pxt;font-weight:bold" class="modal-title">ORDEN  DE BIOPSIA</h3>
        <h3 style="text-align: center;color:black;font-size:30pxt;font-weight:bold" class="modal-title">Paciente:  {{$orden->pacientes->nombre1}} {{$orden->pacientes->apellido1}} {{$orden->pacientes->apellido2}}</h3>
    </div>
    <div class="modal-body">
        <div class="box-body">
            <table id="tableGuias" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th># Frasco</th>
                        <th>Descripcion</th>
                        <th>Observacion</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($biopsias as $biopsia)
                        <tr>
                            <td>{{$biopsia->id}}</td>
                            <td>{{$biopsia->numero_frasco}}</td>
                            <td>{{$biopsia->descripcion_frasco}}</td>
                            <td>{{$biopsia->observacion}}</td>
                            <td><a class="btn btn-primary" href="{{route('pdf_hospital.informe_bioxias_crear', ['id'=>$biopsia->id])}}">Ingreso de Resultado</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>