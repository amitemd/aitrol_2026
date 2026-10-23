<div style="margin:3%">
  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
    <thead>
      <tr>
        <th class="text-center">Cedula</th>
        <th class="text-center">Apellidos</th>
        <th class="text-center">Nombres</th>
        <th class="text-center">Fecha</th>
        <th class="text-center">Acción</th>
      </tr>
    </thead>
    <tbody>
      @foreach($pacientes as $value)

      @php
      //dd($sala_id);
      $controleEquipo = Sis_medico\Control_Equipo::where('id_paciente',$value->paciente->id)->where('id_pentax',$value->id_pentax)->first();
      @endphp
      <tr>
        <td class="text-center">{{$value->paciente->id}}</td>
        <td class="text-center">{{$value->paciente->apellido1}} {{$value->paciente->apellido2}}</td>
        <td class="text-center">{{$value->paciente->nombre1}} {{$value->paciente->nombre2}} </td>
        <td class="text-center">{{$value->fechaini}}</td>
        <td class="text-center">
          @if(is_null($controleEquipo))
          <a href="{{route('limpieza_equipo.registro',['id'=> $value->paciente->id,'id_sala'=>$sala_id,'id_pentax'=>$value->id_pentax])}}" class="btn btn-danger"><i class="fa fa-floppy-o" aria-hidden="true"></i> Registro Equipo</a>
          @else
          <a href="{{route('limpieza_control.editar',['id'=> $controleEquipo->id])}}" class="btn btn-danger"><i class="fa fa-floppy-o" aria-hidden="true"></i> Editar Equipo</a>
        </td>
        @endif
      </tr>
      @endforeach

    </tbody>
  </table>
</div>
