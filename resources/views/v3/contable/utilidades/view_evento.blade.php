<div class="row">
    <div class="col-12 col-sm-12">
        <div class="form-group">
            <label>Nombre del evento</label>
            <div class="input-group mb-3">
                <input type="text" id="nombre_evento" name="nombre_evento" class="form-control" placeholder="Nombre del evento a ingresar">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <label>Desde</label>
            <div class="input-group mb-3">
                <input type="date" id="desde_evento" name="desde_evento" class="form-control text-right" placeholder="dd/mm/aaaa">
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <label>Hora</label>
            <div class="input-group mb-3">
                <input type="time" id="hora_desde_evento" name="hora_desde_evento" class="form-control">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <label>Hasta</label>
            <div class="input-group mb-3">
                <input type="date" id="hasta_evento" name="hasta_evento" class="form-control text-right" placeholder="dd/mm/aaaa">
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <label>Hora</label>
            <div class="input-group mb-3">
                <input type="time" id="hora_hasta_evento" name="hora_hasta_evento" class="form-control">
            </div>
        </div>
    </div>
</div>
<div class="col-12 col-sm-6">
    <div class="form-group">
        <label>Tiempo de Alerta</label>
        <div class="input-group mb-3">
            <select id="selectalert" name="selectalert" class="form-control">
                <option value="0">Seleccione...</option>
                @for($i = 1; $i <= 5; $i++) <option value="{{ $i }}">{{ $i }} día{{ $i > 1 ? 's' : '' }} antes</option>
                    @endfor
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-sm-12">
        <div class="form-group">
            <label>Descripción</label>
            <div class="input-group mb-3">
                <textarea class="form-control" id="descripcion_evento" name="descripcion_evento" rows="3" placeholder="descripción del evento"></textarea>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-sm-12">
        <div class="form-group">
            <label>Url</label>
            <div class="input-group mb-3">
                <input type="text" id="url_evento" name="url_evento" class="form-control" placeholder="Link del evento">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-sm-6">
        <label>Color de fondo</label>
        <div class="input-group my-colorpicker2_fondo mb-3">
            <input type="text" class="form-control" id="color_fondo_evento" name="color_fondo_evento" value="#FFFFFF">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-square"></i></span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <label>Color de Texto</label>
        <div class="input-group my-colorpicker2_texto mb-3">
            <input type="text" class="form-control" id="color_texto_evento" name="color_texto_evento" value="#000000">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-square"></i></span>
            </div>
        </div>
    </div>
</div>