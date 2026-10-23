<div style="margin:3%">
  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
    <thead>
      <tr>
        <th class="text-center">{{trans('limpieza_equipof.identification')}}</th>
        <th class="text-center">{{trans('limpieza_equipof.surname')}}</th>
        <th class="text-center">{{trans('limpieza_equipof.name')}}</th>
        <th class="text-center">Fecha Antes</th>
        <th class="text-center">Fecha Despues</th>
        <th class="text-center">Foto Antes</th>
        <th class="text-center">Foto Después</th>
        <th class="text-center">{{trans('limpieza_equipof.action')}}</th>
      </tr>
    </thead>
    <tbody>
      @foreach($pacientes as $value)

      @php
      $emplace=asset('/');
      $remplace= str_replace('public','storage',$emplace);
      $LimpiezaEquipo = Sis_medico\LimpiezaPentax::where('id_paciente',$value->paciente->id)->where('id_pentax',$value->id_pentax)->first();
      @endphp
      <tr>
        <td class="text-center">{{$value->paciente->id}}</td>
        <td class="text-center">{{$value->paciente->apellido1}} {{$value->paciente->apellido2}}</td>
        <td class="text-center">{{$value->paciente->nombre1}} {{$value->paciente->nombre2}} </td>
        <td class="text-center"> @if(isset($LimpiezaEquipo->created_at) == true) {{ substr($LimpiezaEquipo->created_at,10,15)}} @endif</td>
        <td class="text-center"> @if(isset($LimpiezaEquipo->updated_at) == true) @if(isset($LimpiezaEquipo->path_despues)==true ){{ substr($LimpiezaEquipo->updated_at,10,15)}} @else @endif @endif</td>
        <td class="text-center">
            @if(isset($LimpiezaEquipo->path_antes) == true)
            <img  id="{{$value->id}}"  class="foto"  src="{{$remplace.'app/avatars/'.$LimpiezaEquipo->path_antes}}"  width="50" onclick="modalfoto({{$LimpiezaEquipo->id}},1)">
            @endif
        </td>
        <td class="text-center">
          @if(isset($LimpiezaEquipo->path_despues) == true)
          <img id="{{$value->id}}"  class="foto" src="{{$remplace.'app/avatars/'.$LimpiezaEquipo->path_despues}}" width="50" onclick="modalfoto({{$LimpiezaEquipo->id}},2)" >
          @endif
        </td>
        <td class="text-center">

          @if(isset($LimpiezaEquipo->path_despues) == true && isset($LimpiezaEquipo->path_antes) == true)
          <button type="button" class="btn btn-light">Completado</button>
          @elseif(is_null($LimpiezaEquipo))
          <a href="{{route('created_pentax_limpieza',['id'=> $value->paciente->id,'id_sala'=>$sala_id,'id_pentax'=>$value->id_pentax])}}" data-toggle="modal" data-target="#nuevo" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i>  Registro Limpieza</a>
          @else
            <a href="{{route('updated_pentax_limpieza',['id'=> $LimpiezaEquipo])}}" data-toggle="modal" data-target="#editar" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i>  Editar Limpieza</a>
        </td>
        @endif
      </tr>
      @endforeach

    </tbody>
  </table>
</div>
