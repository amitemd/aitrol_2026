<h2 class="px-6 py-4 font-medium text-gray-900 whitespace-normal max-w-[550px] ">
    1. ¿Cómo ha sido la información y resolución de dudas en recepción?
</h2>

<input type="hidden" class="pregunta" value="17">

<div class="{{$className}}">
    <input type="radio" id="super-happy17" name="17" class="check_17 checkEmoji" value="10.00">
    <label class="radio-label super-happy flex items-center gap-3" for="super-happy17">
        @include("laboratorio.nueva_encuesta_labs.icons.muy_bueno")
        Muy Bueno
    </label>
</div>


<div class="{{$className}}">
    <input type="radio" id="happy17" name="17" class="happy check_17" value="8.00">
    <label class="radio-label happy flex items-center gap-3" for="happy17">
        @include("laboratorio.nueva_encuesta_labs.icons.bueno")
        Bueno
    </label>
</div>



<div class="{{$className}}">
    <input type="radio" class="neutral check_17" name="17" id="neutral17" value="5.00">
    <label class="radio-label neutral flex items-center gap-3" for="neutral17">
        @include("laboratorio.nueva_encuesta_labs.icons.neutro")
        Ni bueno ni malo
    </label>
</div>


<div class="{{$className}}">
    <input type="radio" class="neutral-2 check_17" name="17" id="neutral-217" value="2.00">
    <label class="neutral-2 radio-label flex items-center gap-3" for="neutral-217">
        @include("laboratorio.nueva_encuesta_labs.icons.malo")
        Malo
    </label>
</div>


<div class="{{$className}}">
    <input type="radio" class="super-sad check_17" name="17" id="super-sad17" value="0.00">
    <label class="radio-label super-sad flex items-center gap-3" for="super-sad17">
        @include("laboratorio.nueva_encuesta_labs.icons.muy_malo")
        Muy malo
    </label>
</div>