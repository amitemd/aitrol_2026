<audio id="audio" src="{{asset('musicaturnero.mp3')}}"></audio>
@foreach($actual as $value)
@if(!is_null($value->modulo))
<script type="text/javascript">
    var usuario = {
        id: <?php echo $value->id  ?>,
        modulo: <?php echo $value->modulo  ?>,
    };
    if ((localStorage.getItem(<?php echo $value->id  ?>) == null) == true) {
        console.log("asdasda");
        localStorage.setItem(<?php echo $value->id  ?>, JSON.stringify(usuario));
        var audio = document.getElementById("audio");
        audio.play();
    }
</script>';
@endif
<br><span style="color: white;font-weight:bold;font-size:45px;"> {{strtoupper(substr($value->letraproc, 0, 1))}} - {{$value->turno}} MÓDULO {{$value->modulo}} </span>
@endforeach