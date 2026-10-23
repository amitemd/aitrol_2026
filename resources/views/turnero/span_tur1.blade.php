<div style="text-align:center;margin-top:20px;">
    @foreach($actual as $value)
    <br><span style="color: white;font-weight:bold;font-size:45px;"> {{strtoupper(substr($value->letraproc, 0, 1))}} - {{$value->turno}} MÓDULO {{$value->modulo}} </span>
    @endforeach
</div>