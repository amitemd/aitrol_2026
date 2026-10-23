
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="myModalCosto">{{trans('hospitalizacion.Cirugia')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <form method="POST" id="form" action="{{route('hospitalizacion.save_quirofano')}}">
            {{ csrf_field() }}
            <input type="hidden" name="ho_tipo" value="1">
            <div class="row">
                <div class="form-group col-md-6">
                    <label>{{trans('hospitalizacion.Fecha')}}</label>
                    <input id="fecha" type="text" name="fecha" class="form-control flatpickr-date-time " required value="{{date('Y-m-d H:i:s')}}">
                    <input type="hidden" name="id_tipo" value="{{$id_tipo}}">
                    <input type="hidden" name="id_cama" value="{{$id_cama}}">
                    <input type="hidden" name="id_paciente" value="{{$id_paciente}}">
                    <input type="hidden" name="id_hospitalizacion" value="{{$id_hospitalizacion}}">
                </div>
                <div class="form-group col-md-6">
                    <label>{{trans('hospitalizacion.FechaFin')}}</label>
                    <input id="fechafin" type="text" name="fechafin" onchange="dates(this)" class="form-control flatpickr-date-time" required value="{{date('Y-m-d H:i:s')}}">
                </div>
                <div class="form-group col-md-6">
                    <label>Sala</label>
                    <select class="form-control select2" name="sala" id="sala" required>
                        <option value="">{{trans('hospitalizacion.Seleccione...')}}</option>
                        @foreach($sala as $s)
                            <option value="{{$s->id}}">{{$s->nombre_sala}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label> Doctor </label>
                    <select class="form-control select2" name="doctor" id="doctor" required>
                        <option value="">{{trans('hospitalizacion.Seleccione...')}}</option>
                        @foreach($users as $x)
                            <option @if(Auth::user()->id==$x->id) selected="selected" @endif value="{{$x->id}}"> {{$x->apellido1}} {{$x->nombre1}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Procedimiento</label>
                    <select class="form-control select2" name="procedimiento[]" multiple="multiple" id="procedimiento" required>
                        <option value="">{{trans('hospitalizacion.Seleccione...')}}</option>
                        @foreach($procedimientos as $z)
                            <option value="{{$z->id}}">{{$z->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>{{trans('hospitalizacion.Observaciones')}}</label>
                    <textarea class="form-control" name="observaciones" id="observaciones" cols="3" rows="3"></textarea>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-sm" onclick="send()"> <i class="fa fa-save"></i>{{trans('hospitalizacion.GUARDAR')}}</button>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{trans('hospitalizacion.Cerrar')}}</button>
    </div>

</div>

<script>
    function dates(e){
        var fecha= $('#fecha').val();
        var fechafin= $(e).val();

    }
    function send(){
        if($('#form').valid()){
            $('#form').submit();
        }
       
    }
    $('.select2').select2({ 
        tags:true
    });

</script>