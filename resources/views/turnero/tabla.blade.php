<div class="row">
    <div class="col-md-12">
        <div class="col" style="text-align: center;">
            <h1>Turno actual</h1>
            <h2>@if(is_null($actual)) NO HAY TURNOS   @else {{strtoupper(substr($actual->letraproc,0,1))}}-{{$actual->turno}} @endif</h2>
        </div>
    </div>
</div>