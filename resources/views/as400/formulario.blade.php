<form id="formulario_as400" class="form-vertical" role="form" method="POST" action="{{ route('as400.guardar_orden') }}">
    {{ csrf_field() }}
    <input type="hidden" name="numOrden" value="{{$fichero->numOrden}}">
    <input type="hidden" name="histClinica" value="{{$fichero->histClinica}}">
    <input type="hidden" name="cedula" value="{{$fichero->cedula}}">
    <input type="hidden" name="nombres" value="{{$fichero->apellidos}} {{$fichero->nombres}}">
    <div class="box-body col-xs-24">
        <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
            <label for="codigo" class="col-md-4 control-label"><b>Paciente</b></label>
            <div class="col-md-7">
                <span>{{$fichero->apellidos}} {{$fichero->nombres}}</span>
            </div>
        </div>
        <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
            <label for="codigo" class="col-md-4 control-label"><b>Cedula</b></label>
            <div class="col-md-7">
                <span> {{$fichero->cedula}}</span>
            </div>
        </div>
        <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
            <label for="codigo" class="col-md-4 control-label"><b>Numero de Orden</b></label>
            <div class="col-md-7">
                <span> {{$fichero->numOrden}}</span>
            </div>
        </div>
        <div class="form-group col-md-12">
            <span><b>Examenes Solicitados</b></span>
        </div>

        <div class="table-responsive">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr role="row">
                    <th width="10%" >Codigo Tarifario</th>
                    <th width="5%"  >Examen</th>
                  </tr>
                </thead>
                <tbody>
                    @php
                        $contador = 0;
                    @endphp
                @foreach ($fichero->examenes as $value)
                    @php
                        $examen = \Sis_medico\Examen::where('tarifario', $value)->where('estado', '1')->first();
                    @endphp
                    @if(!is_null($examen))
                        @php
                            $contador++;
                        @endphp
                        <tr>
                          <td >{{$value}}</td>
                          <td >{{$examen->nombre}}</td>
                        </tr>
                        <input type="hidden" name="examen{{$contador}}" value="{{$examen->id}}">
                    @endif
                @endforeach
                </tbody>
                <input type="hidden" name="contador" value="{{$contador}}">
            </table>
        </div>
        <div class="form-group col-md-12">
            <button type="button" onclick="guardar_as400()" id="validar_boton" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Validando Orden" >
                Guardar Orden
            </button>
        </div>
    </div>
</form>
<script type="text/javascript">
    function guardar_as400(){
        $.ajax({
            type: 'post',
            url:"{{route('as400.guardar_orden')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'html',
            data: $("#formulario_as400").serialize(),
            success: function(data){
                console.log(data);
            },
            error: function(data){
                console.log(data);
            }
        });
    }
</script>
