<h2 class="px-6 py-4 font-medium text-gray-900 whitespace-normal max-w-[550px]">
    4. ¿Las instalaciones estaban limpias y ordenadas?
</h2>
<input type="hidden" class="pregunta" value="21">

<div class="{{$className}}">
    <input type="radio" id="super-happy21" name="21" class="check_21 checkEmoji" value="10.00">
    <label class="radio-label super-happy flex items-center gap-3" for="super-happy21">
        @include("laboratorio.nueva_encuesta_labs.icons.check")
        Si
    </label>
</div>


<div class="{{$className}}">
    <input type="radio" id="super-sad21" name="21" class="super-sad check_21" value="8.00">
    <label class="radio-label super-sad flex items-center gap-3" for="super-sad21">
        @include("laboratorio.nueva_encuesta_labs.icons.x")
        No
    </label>
</div>