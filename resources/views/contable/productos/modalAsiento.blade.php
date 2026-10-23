<style type="text/css">
    .modal-body .form-group {
        margin-bottom: 0px;
    }
</style>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
    <h4 class="modal-title" id="myModalLabel" style="text-align: center;">GENERAR ASIENTO CONTABLE</h4>
</div>
<div class="modal-body">
    <div class="col-md-12">
        <form method="POST" action="{{route('productos.storeAsiento')}}">
            {{ csrf_field() }}
            @php
            $contador=0;
            @endphp
            <div class="row">
                <div class="col-md-12">
                    <center>
                        <label> - {{$planilla->planilla->nombre}} -</label>
                    </center>
                </div>
                <div class="col-md-12">
                    <center>
                        <span> {{$planilla->codigo}} </span>
                    </center>
                </div>
                <div class="col-md-4">
                    <label>Debe</label>
                    <input type="hidden" name="id" id="id" value="{{$planilla->id}}">
                </div>
                <div class="col-md-4">
                    <select class="form-control select2cuentas" required name="debe[]">
                        <option value="">Seleccione ... </option>
                        @foreach($cuentas as $c)
                        <option @if($c->id=="5.1.01.06") selected="selected" @endif value="{{$c->id}}"> {{$c->id}} - {{$c->nombre}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <input class="form-control" type="text" name="valor_debe[]" value="{{$valor}}" onKeyPress="return soloNumeros(event)">
                </div>
                <div class="col-md-4">
                    <label> Haber </label>
                </div>
                <div class="col-md-4">
                    <select class="form-control select2cuentas" required name="haber[]">
                        <option value="">Seleccione ... </option>
                        @foreach($cuentas as $c)
                        <option @if($c->id=="1.01.03.01.02") selected="selected" @endif value="{{$c->id}}"> {{$c->id}} - {{$c->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input class="form-control" type="text" name="valor_haber[]" value="{{$valor}}" onKeyPress="return soloNumeros(event)">
                </div>
               <!--  @foreach($procedimiento as $p)
                @php
                $valor_procedimiento=0;
                $producto_procedimiento= Sis_medico\Ct_productos_procedimientos::where('id_procedimiento',$procedimiento)->where('id_seguro',$seguro)->first();
                //valor de producto por procedemiento y seguro
                if(is_null($producto_procedimiento)){
                    
                }else{
                    $valor_procedimiento=$producto_procedimiento->precio;
                    if(is_null($valor_procedimiento)){
                    $valor_procedimiento= 0;
                    }
                }

                @endphp
                <div class="col-md-4">
                    <label>Debe</label>

                </div>
                <div class="col-md-4">
                    <select class="form-control select2" required name="debe[]">
                        <option value="">Seleccione ... </option>
                        @foreach($cuentas as $c)
                        <option value="{{$c->id}}"> {{$c->id}} - {{$c->nombre}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <input class="form-control" type="text" name="valor_debe[]" value="{{number_format($valor_procedimiento,2,'.','')}}" onKeyPress="return soloNumeros(event)">
                </div>
                <div class="col-md-4">
                    <label> Haber </label>
                </div>
                <div class="col-md-4">
                    <select class="form-control select2" required name="haber[]">
                        <option value="">Seleccione ... </option>
                        @foreach($cuentas as $c)
                        <option value="{{$c->id}}"> {{$c->id}} - {{$c->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input class="form-control" type="text" name="valor_haber[]" value="{{number_format($valor_procedimiento,2,'.','')}}" onKeyPress="return soloNumeros(event)">
                </div>
                @php
                $contador++;
                @endphp
                @endforeach -->

                <div class="col-md-12">
                    &nbsp;
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <button class="btn btn-primary" type="submit"> Guardar </button>
                </div>
            </div>
        </form>

    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>
<script>
    $('.select2cuentas').select2({
        tags: false
    });

    function soloNumeros(e) {
        var key = window.Event ? e.which : e.keyCode
        return (key >= 48 && key <= 57)
    }
</script>