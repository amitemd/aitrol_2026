<div id="camas">
    <div class="col-sm-1">
        <label for="camas_estado" class="col-md-3 texto">Camillas:</label>
    </div>
    <div class="form-group col-sm-5" style="display:flex;align-items:center;">
        @foreach($cuadro as $value)
        @php
        //dd($value);
        $camita = $estado = Sis_medico\Camilla_Gestion::where('camilla',$value->id)->where('num_atencion',1)->first();
        //dd($value);
        @endphp
        @if(empty($camita->estado_uso))
        <a target="_blank" class="btn btn-primary" style="margin: 8px;" href="{{route('background_estado',[$value->id,1])}}">
            <div id="cuadrado">
                <span style="font-weight: bold;font-size:10px;">{{$value->nombre_camilla}}</span>
                <img src="{{asset('/')}}hc4/img/hospital_disponible.png" alt="">
            </div>
        </a>
        @elseif(($camita->estado_uso)==2)
        <a target="_blank" class="btn btn-primary" style="margin: 8px;" href="{{route('background_estado',[$camita->id_paciente,$camita->estado_uso])}}">
            <div id="cuadrado" style="margin-left:8px;padding:2px;">
                <span style="font-weight: bold;font-size:10px;">{{$value->nombre_camilla}}</span>
                <img src="{{asset('/')}}hc4/img/hospital_ocupada.png" alt="">

            </div>
        </a>
        @elseif(($camita->estado_uso)==3)
        <a target="_blank" class="btn btn-primary" style="margin: 8px;" href="{{route('background_estado',[$value->id,3])}}">
            <div id="cuadrado" style="margin-left:8px;padding:2px;">
                <span style="font-weight: bold;font-size:10px;">{{$value->nombre_camilla}}</span>

                <img src="{{asset('/')}}hc4/img/hospital_preparacion.png" alt="">

            </div>
        </a>
        @elseif(($camita->estado_uso)==1)
        <a target="_blank" class="btn btn-primary" style="margin: 8px;" href="{{route('background_estado',[$value->id,1])}}">
            <div id="cuadrado" style="margin-left:8px;padding:2px;">
                <span style="font-weight: bold;font-size:10px;">{{$value->nombre_camilla}}</span>

                <img src="{{asset('/')}}hc4/img/hospital_disponible.png" alt="">

            </div>
        </a>
        @elseif(($camita->estado_uso)==4)
        <a target="_blank" class="btn btn-primary" style="margin: 8px;" href="{{route('background_estado',[$value->id,4])}}">
            <div id="cuadrado" style="margin-left:8px;padding:2px;">
                <span style="font-weight: bold;font-size:10px;">{{$value->nombre_camilla}}</span>

                <img src="{{asset('/')}}hc4/img/simple_block.png" alt="">

            </div>
        </a>
        @endif
        @endforeach
    </div>
</div>