<div class="col-sm-1">
    <label for="camas_estado" class="col-md-3 texto">Camillas:</label>
</div>
<div class="form-group col-sm-5" style="display:flex;align-items:center;">
    @foreach($cuadro as $value)
    @php
    $camita = $estado = Sis_medico\Camilla_Gestion::where('camilla',$value->id)->where('num_atencion',1)->first();
    @endphp
    @if(empty($camita->estado_uso))
    <div class="btn btn-primary"  style="margin: 8px;" id="cuadrado" style="margin-left:8px;">
        <a href="{{route('background_estado',[1,$value->nombre_camilla,1,$value->id_hospital])}}" target="_blank">
            <span style="font-weight: bold;font-size:10px;color:white">{{$value->nombre_camilla}}</span>
            <img src="{{asset('/')}}hc4/img/hospital_disponible.png" alt="">
        </a>
    </div>
    @elseif(($camita->estado_uso)==2)
    <div class="btn btn-primary" style="margin: 8px;" id="cuadrado" style="margin-left:8px;">
        <a target="_blank" href="{{route('background_estado',[$camita->id_paciente,$value->nombre_camilla,$camita->estado_uso,$value->id_hospital])}}">
            <span style="font-weight: bold;font-size:10px;color:white">{{$value->nombre_camilla}}</span>
            <img src="{{asset('/')}}hc4/img/hospital_ocupada.png" alt="">
        </a>
    </div>
    @elseif(($camita->estado_uso)==3)
    <div class="btn btn-primary" style="margin: 8px;" id="cuadrado" style="margin-left:8px;">
        <a target="_blank" href="{{route('background_estado',[1,$value->nombre_camilla,$camita->estado_uso,$value->id_hospital])}}">
            <span style="font-weight: bold;font-size:10px;color:white">{{$value->nombre_camilla}}</span>
            <img src="{{asset('/')}}hc4/img/hospital_preparacion.png" alt="">
        </a>
    </div>
    @elseif(($camita->estado_uso)==1)
    <div class="btn btn-primary" style="margin: 8px;" id="cuadrado" style="margin-left:8px;">
        <a target="_blank" href="{{route('background_estado',[1,$value->nombre_camilla,$camita->estado_uso,$value->id])}}">
            <span style="font-weight: bold;font-size:10px;color:white">{{$value->nombre_camilla}}</span>
            <img src="{{asset('/')}}hc4/img/hospital_disponible.png" alt="">
        </a>
    </div>
    @elseif(($camita->estado_uso)==4)
    <div class="btn btn-primary" style="margin: 8px;" id="cuadrado" style="margin-left:8px;">
        <a target="_blank" href="{{route('background_estado',[1,$value->nombre_camilla,$camita->estado_uso,$value->id_hospital])}}">
            <span style="font-weight: bold;font-size:10px;color:white">{{$value->nombre_camilla}}</span>
            <img src="{{asset('/')}}hc4/img/simple_block.png" alt="">
        </a>
    </div>
    @endif
    @endforeach
</div>