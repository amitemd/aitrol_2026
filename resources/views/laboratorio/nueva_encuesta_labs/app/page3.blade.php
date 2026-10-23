<h2 class="px-6 py-4 font-medium text-gray-900 whitespace-normal max-w-[550px] ">
    3. ¿El personal fue amable y respetuoso?
</h2>
<input type="hidden" class="pregunta" value="20">

<div class="{{$className}}">
    <input type="radio" id="super-happy20" name="20" class="check_20 checkEmoji" value="10.00">
    <label class="radio-label super-happy flex items-center gap-3" for="super-happy20">
        @include("laboratorio.nueva_encuesta_labs.icons.check")
        Si
    </label>
</div>


<div class="{{$className}}">
    <input type="radio" id="super-sad20" name="20" class="super-sad check_20" value="8.00">
    <label class="radio-label super-sad flex items-center gap-3" for="super-sad20">
        @include("laboratorio.nueva_encuesta_labs.icons.x")
        No
    </label>
</div>