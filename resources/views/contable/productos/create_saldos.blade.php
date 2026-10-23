@extends('contable.rubros_acreedores.base')
@section('action-content')

<script type="text/javascript">
    function check(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function goBack() {
      window.history.back();
    }

</script>
<section class="content">
    <div class="box ">
        <div class="box-header color_cab" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
               <h5><b>AGREGAR SALDO INICIAL</b></h5>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-danger btn-gray">
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body dobra">
            <form class="form-vertical" role="form" method="POST" action="{{route('productos.store_iniciales')}}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    <!--Código del Rubro-->
                    <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" class="col-md-4 control-label">Código</label>
                        <div class="col-md-7">
                            <select class="form-control select2" name="id_producto" style="width: 100%;" id="id_producto" required>
                                <option value=""> Seleccione ...</option>
                                @foreach($productos as $p)
                                    <option value="{{$p->id}}"> {{$p->codigo}} {{$p->nombre}} </option>
                                @endforeach
                            </select>
                            @if ($errors->has('codigo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('codigo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <!--Nombre o descripción del Rubro-->
                    <div class="form-group col-xs-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-4 control-label">Descripcion</label>
                        <div class="col-md-7">
                            <input id="descripcion" type="text" class="form-control" name="descripcion" required value="{{ old('descripcion') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus autocomplete="off">
                            @if ($errors->has('nombre'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Cuenta de Débito que estará relacionada con el Rubro-->
                    <div class="form-group col-xs-6">
                        <label for="cantidad" class="col-md-4 control-label">Cantidad</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" style="text-align: right;" required name="cantidad" id="cantidad" onkeypress="return isNumberKey(event)" onchange="calcular(this)" value="0.00">
                        </div>
                    </div>
                    <!--Cuenta de Crédito que estará relacionada con el Rubro-->
                    <div class="form-group col-xs-6">
                        <label for="cuenta_haber" class="col-md-4 control-label">Costo</label>
                        <div class="col-md-7">
                        <input type="text" class="form-control" style="text-align: right;" required name="costo" id="costo" onkeypress="return isNumberKey(event)" onchange="calcular(this)" value="0.00">
                        </div>
                    </div>
                    <!--Si el rubro es de Débito o Crédito-->
                    <div class="form-group col-xs-6{{ $errors->has('tipo') ? ' has-error' : '' }}">
                        <label for="total" class="col-md-4 control-label">Total</label>
                        <div class="col-md-7">
                           <input type="text" class="form-control" style="text-align: right;" name="total" id="total" readonly value="0.00">
                            @if ($errors->has('total'))
                            <span class="help-block">
                                <strong>{{ $errors->first('total') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Nota adicional del Rubro.-->
                    <div class="form-group col-xs-6{{ $errors->has('nota') ? ' has-error' : '' }}">
                        <label for="nota" class="col-md-4 control-label">Nota</label>
                        <div class="col-md-7">
                            <textarea class="form-control" rows="2" name="nota" id="nota"></textarea>
                            @if ($errors->has('nota'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nota') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-6{{ $errors->has('nota') ? ' has-error' : '' }}">
                        <label for="nota" class="col-md-4 control-label">Fecha Inicial</label>
                        <div class="col-md-7">
                            <input class="form-control" type="date" name="fecha" id="fecha" required>
                        </div>
                    </div>
                    <!--Establece si el Rubro está Activo. -->
                  
                    <div class="form-group col-xs-10" style="text-align: center;">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary btn-gray">
                                Agregar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.select2').select2({
            tags: false
          });
      });
      function calcular(e){
       var cantidad= parseFloat($('#cantidad').val());
       $('#cantidad').val(cantidad.toFixed(2,2));

       var costo= parseFloat($('#costo').val());
       $('#costo').val(costo.toFixed(2,2));
       var total= cantidad*costo;
       $('#total').val(total.toFixed(2,2));
      }
      function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
                return false;

            return true;
        }
</script>

</section>
@endsection
