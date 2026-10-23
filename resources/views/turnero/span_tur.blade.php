<div style="text-align:center;margin-top:50px;">
    <label style="color: white;font-weight:bold;font-size:60px;" for="">Turnos Atendidos</label>
    @foreach($registro as $value)
    <br><span style="color: white;font-weight:bold;font-size:45px;margin-top:10px ;"> {{strtoupper(substr($value->letraproc, 0, 1))}} - {{$value->turno}} MÓDULO {{$value->modulo}} @if(($value->estado)==2) ATENDIDO @endif
</span>
    @endforeach
</div>