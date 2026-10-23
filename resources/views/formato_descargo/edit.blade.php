@extends('formato_descargo.base')
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
    <div class="box " style="background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
              <h3 class="box-title">Agregar Plantilla</h3>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-danger btn-gray">
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #ffffff;">
                        <form class="form-vertical" id="formi" role="form" method="POST" action="{{route('formatosProductos.update',['id'=>$id])}}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    <!--Código del Rubro-->
                    <!--Nombre o descripción del Rubro-->
                    <div class="form-group col-xs-6{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-4 control-label">Descripcion</label>
                        <div class="col-md-7">
                          <textarea class="form-control"  onkeyup="javascript:this.value=this.value.toUpperCase();" name="descripcion" id="descripcion" cols="10" rows="5">{{$formato->descripcion}}</textarea>
                        </div>
                    </div>
                  
                    <!--Nota adicional del Rubro.-->
                    <div class="form-group col-xs-6{{ $errors->has('nota') ? ' has-error' : '' }}">
                        <label for="nota" class="col-md-4 control-label">Nota</label>
                        <div class="col-md-7">
                            <textarea class="form-control" rows="2" name="nota" id="nota">{{$formato->nota}}</textarea>
                            @if ($errors->has('nota'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nota') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Establece si el Rubro está Activo. -->
                    <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-4 control-label">Estado</label>
                        <div class="col-md-7">
                             <select id="estado" name="estado" class="form-control" required>
                                <option @if($formato->estado==1) selected="selected" @endif value="1">ACTIVO</option>
                                <option @if($formato->estado==0) selected="selected" @endif  value="0">INACTIVO</option>
                            </select>
                            @if ($errors->has('estado'))
                            <span class="help-block">
                                <strong>{{ $errors->first('estado') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="table-responsive table-striped col-md-12" style="width: 100%;">
                            <table class="table table-striped" id="example3" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                <thead >
                                    <tr>
                                        <th style=" width: 40%; text-align: center;">Código</th>
                                        <th style="width : 20%; text-align: center;">Cantidad</th>
                                        <th style="width : 20%; text-align: center;">Precio</th>
                                        <th style=" width : 20%;text-align: center;">
                                            <button onclick="crea_td()" type="button" class="btn btn-success btn-gray">
                                                <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="det_recibido">
                                    @foreach($detalle_formato as $value)
                                        @php 
                                            //dd($value);
                                        @endphp
                                        <tr>
                                            <td><select style="width: 98%;" name="codigo[]" class="form-control select2_cuentas" required> <option value="">Seleccione...</option> @foreach($productos as $v) <option @if($v->id==$value->codigo) selected="selected" @endif value="{{$v->id}}">{{$v->codigo}} {{$v->descripcion}}</option> @endforeach</select>  <input type="hidden" name="id_detalle[]" value="{{$value->id}}" required></td>
                                            <td><input class="form-control" type="number" name="cantidad[]" value="{{$value->cantidad}}" required></td>
                                            <td><input class="form-control" type="text" name="precio[]" value="{{$value->precio}}" required ></td>
                                            <td style="text-align: center;"><button class="btn btn-warning btn-xs btn-gray" type="button" onclick="eliminar(this)"> <i class="fa fa-trash"></i> </button></td>
                                        </tr>
                                      
                                    @endforeach
                                    <tr id="mifila" style="display: none; margin-top:10px;">
                                        <td> <input type="hidden" name="id_detalle[]" value="-1"> <select class="form-control select2_cuentas" name="codigo[]" style="width: 98%;" required> <option value="">Seleccione ...</option> @foreach($productos as $x) <option value="{{$x->id}}">{{$x->codigo}} {{$x->descripcion}}</option>  @endforeach </select></td>
                                        <td><input style="width: 98%;" class="form-control" type="number" name="cantidad[]" placeholder="Ingrese cantidad " required></td>
                                        <td><input style="width: 98%;" class="form-control" type="text" name="precio[]" placeholder="Ingrese precio" required></td>
                                        <td style="text-align: center;"><button class="btn btn-warning btn-xs btn-gray" type="button" onclick="eliminar(this)"> <i class="fa fa-trash"></i> </button></td>
                                     </tr>
                                    <!--
                                    <tr style="margin-top:10px;">
                                        <td><select class="form-control select2_cuentas" name="codigo[]" style="width: 98%;" required> <option value="">Seleccione ...</option> @foreach($productos as $x) <option value="{{$x->id}}">{{$x->codigo}} {{$x->descripcion}}</option>  @endforeach </select></td>
                                        <td><input style="width: 98%;" class="form-control" type="number" name="cantidad[]" placeholder="Ingrese cantidad" required style="margin-left: 10px;"></td>
                                        <td><input style="width: 98%;" class="form-control" type="text" name="precio[]" placeholder="Ingrese precio" required style="margin-left: 10px;"></td>
                                        <td style="text-align: center;"><button type="button" class="btn btn-warning btn-xs btn-gray" onclick="eliminar(this)"> <i class="fa fa-trash"></i> </button></td>
                                    </tr>
                                    <tr id="mifila" style="display: none; margin-top:10px;">
                                        <td><select class="form-control select2_cuentas" name="codigo[]" style="width: 98%;" required> <option value="">Seleccione ...</option> @foreach($productos as $x) <option value="{{$x->id}}">{{$x->codigo}} {{$x->descripcion}}</option>  @endforeach </select></td>
                                        <td><input style="width: 98%;" class="form-control" type="number" name="cantidad[]" placeholder="Ingrese cantidad " required></td>
                                        <td><input style="width: 98%;" class="form-control" type="text" name="precio[]" placeholder="Ingrese precio" required></td>
                                        <td style="text-align: center;"><button class="btn btn-warning btn-xs btn-gray" type="button" onclick="eliminar(this)"> <i class="fa fa-trash"></i> </button></td>
                                     </tr>-->
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                    </div>
                    <div class="col-md-12" style="text-align: center;">
                        <button type="button" onclick="sumit()" class="btn btn-success btn-gray"> <i class="fa fa-save"></i> Actualizar </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.select2_cuentas').select2({
            tags: false
          });
    });
    var fila= $("#mifila").html();
    function crea_td(){
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("det_recibido").insertRow(-1);
        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        rowk.innerHTML = fila;
        //rowk.className="well";
        $('.select2_cuentas').select2({
            tags: false
        });
    }
    function eliminar(a){
        //alert("dada");
        $(a).parent().parent().remove();
    }
    function sumit(){
        if ($("#formi").valid()) {
            $("#formi").submit();
        }
       
    }

      
</script>

</section>
@endsection
