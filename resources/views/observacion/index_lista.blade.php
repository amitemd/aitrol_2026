<div class="col-md-12">
  <!-- The time line -->
  <ul class="timeline">
    <!-- timeline time label -->
    @foreach($observaciones as $observacion)  
      @if($observacion->estado=='0')
        @php $idusuario = Auth::user()->id; @endphp
        @if($idusuario==$observacion->id_usuariocrea)
        
        @php $mes = substr($observacion->created_at,5,2);  if($mes=='01') $tmes = 'Ene'; elseif($mes=='02') $tmes = 'Feb'; elseif($mes=='03') $tmes = 'Mar'; elseif($mes=='04') $tmes = 'Abr'; elseif($mes=='05') $tmes = 'May'; elseif($mes=='06') $tmes = 'Jun'; elseif($mes=='07') $tmes = 'Jul'; elseif($mes=='08') $tmes = 'Ago'; elseif($mes=='09') $tmes = 'Sep'; elseif($mes=='10') $tmes = 'Oct'; elseif($mes=='11') $tmes = 'Nov'; elseif($mes=='12') $tmes = 'Dic'; @endphp
        <li class="time-label"><span @if(date('Y-m-d')==substr($observacion->created_at,0,10))class="bg-orange" @else class="bg-green" @endif>{{substr($observacion->created_at,8,2)}} {{$tmes}}.{{substr($observacion->created_at,0,4)}}</span></li>
        
        <li>
          <i class="fa fa-user bg-aqua"></i>

          <div class="timeline-item">
            <span class="time"><i class="fa fa-clock-o"></i> {{substr($observacion->created_at,11,10)}}</span>
            
            <h3 class="timeline-header no-border"><b >{{$observacion->usuario_crea()->first()->nombre1}} {{$observacion->usuario_crea()->first()->nombre2}} {{$observacion->usuario_crea()->first()->apellido1}}: </b>@if($observacion->estado=='0')<span style="color: red;">(*inactivo)</span>@endif {{$observacion->observacion}}</h3>
            @php $idusuario = Auth::user()->id; @endphp
            @if($observacion->estado=='0')
              @if($idusuario==$observacion->id_usuariocrea)
              <a href="{{route('observacion.inactiva',['id' => $observacion->id])}}" class="btn btn-danger btn-xs">Delete</a>
              <a href="javascript:activar({{$observacion->id}});" class="btn btn-success btn-xs">Activar</a>
              @endif
            @endif  
          </div>
        </li>
        @endif
      @elseif($observacion->estado=='1')
        @php $mes = substr($observacion->created_at,5,2);  if($mes=='01') $tmes = 'Ene'; elseif($mes=='02') $tmes = 'Feb'; elseif($mes=='03') $tmes = 'Mar'; elseif($mes=='04') $tmes = 'Abr'; elseif($mes=='05') $tmes = 'May'; elseif($mes=='06') $tmes = 'Jun'; elseif($mes=='07') $tmes = 'Jul'; elseif($mes=='08') $tmes = 'Ago'; elseif($mes=='09') $tmes = 'Sep'; elseif($mes=='10') $tmes = 'Oct'; elseif($mes=='11') $tmes = 'Nov'; elseif($mes=='12') $tmes = 'Dic'; @endphp
        <li class="time-label"><span @if(date('Y-m-d')==substr($observacion->created_at,0,10))class="bg-orange" @else class="bg-green" @endif>{{substr($observacion->created_at,8,2)}} {{$tmes}}.{{substr($observacion->created_at,0,4)}}</span></li>
        
        <li>
          <i class="fa fa-user bg-aqua"></i>

          <div class="timeline-item">
            <span class="time"><i class="fa fa-clock-o"></i> {{substr($observacion->created_at,11,10)}}</span>
            
            <h3 class="timeline-header no-border"><b >{{$observacion->usuario_crea()->first()->nombre1}} {{$observacion->usuario_crea()->first()->nombre2}} {{$observacion->usuario_crea()->first()->apellido1}}: </b>@if($observacion->estado=='0')<span style="color: red;">(*inactivo)</span>@endif {{$observacion->observacion}}</h3>
            
            @if($observacion->estado=='0')
              @if($idusuario==$observacion->id_usuariocrea)
              <a href="{{route('observacion.inactiva',['id' => $observacion->id])}}" class="btn btn-danger btn-xs">Delete</a>
              <a href="#" class="btn btn-success btn-xs">Activar</a>
              @endif
            @endif  
          </div>
        </li>
      @endif
    @endforeach    
    <li>
      <i class="fa fa-clock-o bg-gray"></i>
    </li>
  </ul>
</div>