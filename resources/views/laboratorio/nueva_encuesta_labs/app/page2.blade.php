<h2 class="px-6 py-4 font-medium text-gray-900 whitespace-normal max-w-[550px] ">
    2. ¿Cómo ha sido la explicación sobre los medios para consultar los resultados?
</h2>
<input type="hidden" class="pregunta" value="19">

<div class="{{$className}}">
    <input type="radio" id="super-happy19" name="19" class="check_19 checkEmoji" value="10.00">
    <label class="radio-label super-happy flex items-center gap-3" for="super-happy19">
        @include("laboratorio.nueva_encuesta_labs.icons.muy_bueno")
        Muy Bueno
    </label>
</div>


<div class="{{$className}}">
    <input type="radio" id="happy19" name="19" class="happy check_19" value="8.00">
    <label class="radio-label happy flex items-center gap-3" for="happy19">
        @include("laboratorio.nueva_encuesta_labs.icons.bueno")
        Bueno
    </label>
</div>



<div class="{{$className}}">
    <input type="radio" class="neutral check_19" name="19" id="neutral19" value="5.00">
    <label class="radio-label neutral flex items-center gap-3" for="neutral19">
        @include("laboratorio.nueva_encuesta_labs.icons.neutro")
        Ni bueno ni malo
    </label>
</div>


<div class="{{$className}}">
    <input type="radio" class="neutral-2 check_19" name="19" id="neutral-219" value="2.00">
    <label class="neutral-2 radio-label flex items-center gap-3" for="neutral-219">
        @include("laboratorio.nueva_encuesta_labs.icons.malo")
        Malo
    </label>
</div>


<div class="{{$className}}">
    <input type="radio" class="super-sad check_19" name="19" id="super-sad19" value="0.00">
    <label class="radio-label super-sad flex items-center gap-3" for="super-sad19">
        @include("laboratorio.nueva_encuesta_labs.icons.muy_malo")
        Muy malo
    </label>
</div>