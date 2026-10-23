@extends('insumos.ingreso.base')
@section('action-content')

      @php
       $iva = Sis_medico\Utilidades::porcentajeIva();   
      @endphp
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<style type="text/css">
  .ui-corner-all
        {
            -moz-border-radius: 4px 4px 4px 4px;
        }

        .ui-widget
        {
            font-family: Verdana,Arial,sans-serif;
            font-size: 15px;
        }
        .ui-menu
        {
            display: block;
            float: left;
            list-style: none outside none;
            margin: 0;
            padding: 2px;
        }
        .ui-autocomplete
        {
             overflow-x: hidden;
              max-height: 200px;
              width:1px;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            float: left;
            display: none;
            min-width: 160px;
            _width: 160px;
            padding: 4px 0;
            margin: 2px 0 0 0;
            list-style: none;
            background-color: #fff;
            border-color: #ccc;
            border-color: rgba(0, 0, 0, 0.2);
            border-style: solid;
            border-width: 1px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -webkit-background-clip: padding-box;
            -moz-background-clip: padding;
            background-clip: padding-box;
            *border-right-width: 2px;
            *border-bottom-width: 2px;
        }
        .ui-menu .ui-menu-item
        {
            clear: left;
            float: left;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .ui-menu .ui-menu-item a
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            cursor: pointer;
            background-color: #ffffff;
        }
        .ui-menu .ui-menu-item a:hover
        {
            display: block;
            padding: 3px 3px 3px 3px;
            text-decoration: none;
            color: White;
            cursor: pointer;
            background-color: #006699;
        }
        .ui-widget-content a
        {
            color: #222222;
        }
        .td_der{
          text-align: right;
        }
        .container-element{
          box-shadow: 0px 0px 5px 0px rgb(0 0 0 / 35%);
          -webkit-box-shadow: 0px 0px 5px 0px rgb(0 0 0 / 35%);
          -moz-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.35);
          padding: 18px 0px;
          border-radius: 7px;
          margin-bottom: 12px;

        }
        .input-text{
          border-radius: 9px;
        }

*{
  font-family: var(--fuentePrincipal)
}
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">

    <!-- Main content -->
    <section class="content">
      <div class="box">
        <div class="box-header">
          <div class="row">
              <div class="col-sm-4">
                <h3 class="box-title">Ingreso a Bodega de Producto</h3>
              </div>
              <div class="col-md-8" style="text-align: right;">
                        <a type="button" href="{{URL::previous()}}" class="btn btn-primary btn-sm">
                          <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
                        </a>
              </div>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <form method="POST" id="ingreso">
              <div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title">Ingreso de producto</h3>
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                          <label for="inputid" class="col-sm-3 control-label">Código</label>
                          <div class="col-sm-9">
                            <input value="" type="text" class="form-control input-text" name="codigo" id="codigo" placeholder="{{trans('winsumos.codigo')}}"  style="text-transform:uppercase;">
                          </div>
                      </div>
                    </div>
                    <div class="col-md-1">
                      <button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-search"></span></button>    
                    </div> 
                    <div class="col-md-6">
                      <div class="form-group">
                          <label for="inputapellido" class="col-sm-3 control-label">Nombre</label>
                          <div class="col-sm-9">
                            <input value="" type="text" class="form-control input-text"  id="nombre" name="nombre" id="inputapellido" placeholder="{{trans('winsumos.nombre')}}" style="text-transform:uppercase;">
                          </div>
                      </div>
                    </div>
                    <br>
                    <input type="hidden" value="codigo_siempre" id="codigo_siempre" >
                    <input type="hidden" value="kardex_uso_aux" id="kardex_uso_aux">



                    <div class="col-md-12" style="padding:20px">

                      <div class="col-md-12 container-element">
                        <div style="margin-top: 15px" class="col-md-2">
                          <div class="form-group">
                              <label for="cantidad_codigo" class="col-sm-6 control-label">{{trans('winsumos.cantidad')}}: </label>
                              <div class="col-sm-6">
                                <input  type="number" class="form-control input-text" id="cantidad_codigo" name="cantidad_codigo" value="1">
                              </div>
                          </div>
                        </div>

                        <div style="margin-top: 15px" class="col-md-2">
                          <div class="form-group">
                              <label for="lote_aux" class="col-sm-3 control-label">{{trans('winsumos.lote')}}: </label>
                              <div class="col-sm-9">
                                <input  type="text" class="form-control input-text" id="lote_aux" name="lote_aux" value="">
                              </div>
                          </div>
                        </div>

                        <div style="margin-top: 15px" class="col-md-3">
                          <div class="form-group">
                              <label for="fecha_cad_aux" class="col-sm-4 control-label">Fecha Caducidad: </label>
                              <div class="col-sm-8">
                                <input  type="date" class="form-control input-text" id="fecha_cad_aux" name="fecha_cad_aux" >
                              </div>
                          </div>
                        </div>

                        <div style="margin-top: 15px" class="col-md-3">
                          <div class="form-group">
                              <label for="descuento_aux" class="col-sm-4 control-label">Descuento: </label>
                              <div class="col-sm-8">
                                <input  type="text" class="form-control input-text" id="descuento_aux" name="descuento_aux" value="0" onblur='this.value=parseFloat(this.value).toFixed(0);' onkeypress="return isNumberKey(event)">
                              </div>
                          </div>
                        </div>

                        <div style="margin-top: 15px" class="col-md-2">
                          <div class="form-group">
                              <label for="precio_aux" class="col-sm-5 control-label">{{trans('winsumos.precio')}}: </label>
                              <div class="col-sm-7">
                                <input  type="text" class="form-control input-text" id="precio_aux" onblur='this.value=parseFloat(this.value);'  onkeypress="return isNumberKey(event)" name="precio_aux" value="0">
                              </div>
                          </div>
                        </div>


                      </div>                    

                    </div>









                    

                  </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                  <button type="button" id="busqueda" class="btn btn-primary"  style="display:none;" onclick="cantidadInsumos()">
                    Agregar
                  </button>
                </div>
              </div>
          </form>
          <div class="panel panel-default">
            <form method="POST"  name="frm" id="frm">
              <div class="panel-heading">
                <div class="row">
                    <!-- Fecha -->
                    <div class="form-group col-md-6">
                        <label for="fecha" class="col-md-4 control-label">Fecha Pedido</label>
                        <div class="col-md-8">
                          <div class="input-group date">
                            <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" value="" name="fecha" class="form-control" id="fecha"  placeholder="AAAA/MM/DD">
                          </div>
                        </div>
                    </div>
                    <!-- Numero de Pedido -->
                    <div class="form-group cl_pedido col-md-6 {{ $errors->has('pedido') ? ' has-error' : '' }}">
                        <label for="pedido" class="col-md-4 control-label">Número de pedido</label>
                        <div class="col-md-8">
                          <input id="pedido" type="text" class="form-control input-text" name="pedido" value="{{ old('pedido') }}"  onkeyup="valida(event);" required autofocus>
                        </div>
                        <span class="help-block">
                          <strong id="str_pedido"></strong>
                        </span>
                    </div>

                    <div class="form-group cl_pedido col-md-6 {{ $errors->has('num_factura') ? ' has-error' : '' }}">
                        <label for="num_factura" class="col-md-4 control-label">Número de Documento</label>
                        <div class="col-md-8">
                          <input id="num_factura" type="text" class="form-control input-text" name="num_factura" value="{{ old('num_factura') }}" required autofocus>
                        </div>
                    </div>
                    <!-- Vencimiento -->
                    <div class="form-group col-md-6{{ $errors->has('vencimiento') ? ' has-error' : '' }}">
                        <label for="vencimiento" class="col-md-4 control-label">Fecha de Vencimiento</label>
                        <div class="col-md-8">
                          <div class="input-group date">
                            <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" value="" name="vencimiento" class="form-control input-text" id="vencimiento"  placeholder="AAAA/MM/DD">
                          </div>
                        </div>
                    </div>
                    <!-- Proveedor -->
                    <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                        <label for="id_proveedor" class="col-md-4 control-label">Proveedor</label>
                        <div class="col-md-8">
                          <select name="id_proveedor" style="width: 100%;" class="form-control select2" required="" id="id_proveedor">
                              <option value="">Seleccione..</option>
                            @foreach($proveedores as $value)
                              <option value="{{$value->id}}" >{{$value->nombrecomercial}}</option>
                            @endforeach
                          </select>
                        </div>
                    </div>
                    <!-- update headers type date: 17 Nov 2020  -->
                    <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                      <label for="bodega_recibe" class="col-md-4 control-label">Bodega Recibe</label>
                      <div class="col-md-8">
                        @php
                          $id_empresa_bodega= Session::get('id_empresa');
                          $empresa_bodega= Sis_medico\Empresa::find($id_empresa_bodega);
                        @endphp
                        <select name="bodega_recibe" class="form-control select2 " style="width: 100%;" required="required" name="bodega_recibe" id="bodega_recibe">
                          <!--l-->
                          <option value="">Seleccione la bodega ...</option>
                          <option value="@if(!is_null($bodega_principal)) {{$bodega_principal->id}} @endif" >@if(!is_null($bodega_principal)) {{$bodega_principal->nombre}} - {{$empresa_bodega->nombrecomercial}} @endif</option>
                          <!-- termina l-->
                          <!-- codigo antiguo
                          <option value="">Seleccione..</option>
                          @foreach($bodegas as $value)
                          <option value="{{$value->id}}" @if(env('BODEGA_PRINCIPAL',19)==$value->id) selected @endif>{{$value->nombre}}&nbsp;@if(isset($value->empresa))-&nbsp;{{$value->empresa->nombrecomercial}}@endif</option>
                          @endforeach
                          termina antiguo -->
                      </select>
                      </div>
                  </div>
                    <div class="form-group col-md-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                        <label for="tipo_" class="col-md-4 control-label">Tipo</label>
                        <div class="col-md-8">
                          {{-- <select name="tipo_" class="form-control select2 " style="width: 100%;" required="" name="tipo_" id="tipo_">
                              <option value="">Seleccione..</option>
                              <option value="1">Guia de Remision </option>
                              <option value="2">Factura Contra Entrega</option>
                              <option value="3">Factura</option>
                          </select> --}}
                          <select name="tipo_" class="form-control select2 " style="width: 100%;" required="" name="tipo_" id="tipo_">
                            <option value="">Seleccione..</option>
                            @foreach($doc_bodega as $value)
                            <option value="{{$value->id}}" >{{$value->documento}} @if($value->tipo == 'C') (CONCESION) @endif</option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    <!-- MULTIPLE EMPRESA
                    <div class="form-group col-md-6{{ $errors->has('id_empresa') ? ' has-error' : '' }}">
                        <label for="id_empresa" class="col-md-4 control-label">Empresa</label>
                        <div class="col-md-8">
                          <select id="id_empresa" class="form-control select2" style="width: 100%;" name="id_empresa">
                              <option value="">Seleccione..</option>
                            @foreach($empresa as $value)
                              <option value="{{$value->id}}" >{{$value->nombrecomercial}}</option>
                            @endforeach
                          </select>
                        </div>
                    </div> -->
                    <!-- MULTIPLE EMPRESA-->
                    <!--div class="form-group col-md-6{{ $errors->has('consecion') ? ' has-error' : '' }}">
                        <label for="consecion" class="col-md-4 control-label">Posee Consecion</label>
                        <div class="col-md-8">
                          <input id="consecion" name="consecion" type="checkbox" value="1" class="flat-blue"  style="position: absolute; opacity: 0;">
                        </div>
                    </div-->
                    <!-- Observaciones -->
                    <div class="form-group  col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                        <label for="observaciones" class="col-md-2 control-label">{{trans('winsumos.observacion')}}</label>
                        <div class="col-md-10">
                          <input id="observaciones" type="text" class="form-control input-text" name="observaciones" value="{{ old('observaciones') }}" autofocus>
                        </div>
                    </div>
                </div>
              </div>

              <div class="general form-group col-md-12 ">
                <div class="row">
                <div class="col-md-6">
                <span class="help-block">
                  <strong id="lote_errores"></strong>
                </span>
                </div>
                <div class="col-md-6">

                <span class="help-block">
                  <strong id="fecha_errores"></strong>
                </span>
                </div>

                </div>

              </div>

              <div class="box-body">
                <div class="table-responsive col-md-12">
                  <input name='contador' type="hidden" value="0" id="contador">
                  <table id="example2" class="display compact responsive"  role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
                    <thead>
                      <tr role="row">
                        <th>{{trans('winsumos.codigo')}}</th>
                        <th>{{trans('winsumos.nombre')}}</th>
                        <th>{{trans('winsumos.cantidad')}}</th>
                        <th>{{trans('winsumos.serie')}}</th>
                        <!--th width="7.69%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Serie: activate to sort column descending" aria-sort="sorting">Estado Producto</th-->
                        <!--th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Precio de compra</th-->
                        <th>{{trans('winsumos.bodegas')}}</th>
                        <th>{{trans('winsumos.lote')}}</th>
                        <th>{{trans('winsumos.registro_sanitario')}}</th>
                        <th>{{trans('contableM.fechacaducidad')}}</th>
                        <th>% {{trans('winsumos.descuento')}}</th>
                        <th>{{trans('winsumos.precio_unitario')}}</th>
                        <th>{{trans('winsumos.precio_final')}}</th>
                        {{-- <th width="7.69%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Consecion: activate to sort column ascending">{{trans('winsumos.consecion')}}</th> --}}
                        <th>Iva</th>
                        <th>{{trans('winsumos.accion')}}</th>
                      </tr>
                    </thead>
                    <tbody id="crear">

                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="8"></td>
                        <td colspan="3" class="td_der">{{trans('winsumos.subtotal')}} {{$iva->iva *100}}%:</td>
                        <td><input type="hidden" name="subtotal_12" id="subtotal_12"> <input  class="td_der" style="width: 55px;border: 0px;" type="text" readonly id="subtotal_12_1"></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td colspan="8"></td>
                        <td colspan="3" class="td_der">{{trans('winsumos.subtotal')}} 0%:</td>
                        <td><input type="hidden" name="subtotal_0" id="subtotal_0"> <input class="td_der" style="width: 55px;border: 0px;" type="text" readonly id="subtotal_0_1"></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td colspan="8"></td>
                        <td colspan="3" class="td_der">{{trans('winsumos.descuento')}}</td>
                        <td><input type="hidden" name="descuent" id="descuent"> <input  class="td_der" style="width: 55px;border: 0px;" type="text" readonly id="descuentx" name="descuentx"></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td colspan="8"></td>
                        <td colspan="3" class="td_der">{{trans('winsumos.iva')}}</td>
                        <td><input type="hidden" name="iva" id="iva"> <input class="td_der" style="width: 55px;border: 0px;" type="text" readonly id="iva_1"></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td colspan="8"></td>
                        <td colspan="3" class="td_der">{{trans('winsumos.total')}}</td>
                        <td><input type="hidden" name="total" id="total"> <input class="td_der" style="width: 55px;border: 0px;" type="text" readonly id="total_1"></td>
                        <td></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="box-footer" style="text-align: center;">
                <button type="button" id="envio" onclick="enviar()" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando Informacion">
                <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('winsumos.guardar')}}
                </button>
              </div>
            </form>
          </div>
        </div>
    </section>
    <!-- /.content -->
                           

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
  <script src="{{ asset ("/js/icheck.js") }}"></script>
  <script>

const cantidadInsumos = () => {
          let cantidad_codigo = document.getElementById("cantidad_codigo").value 
          let precio_aux = document.getElementById("precio_aux").value
          let lote_aux = document.getElementById("lote_aux").value
          let fecha_cad_aux = document.getElementById("fecha_cad_aux").value
          let descuento_aux = document.getElementById("descuento_aux").value

          let codigo_siempre = document.getElementById("codigo_siempre").value
          let kardex_uso_aux = document.getElementById("kardex_uso_aux").value


          if (codigo_siempre == 1 && kardex_uso_aux == 1) {
          // busquedaInsumo({
          //   'precio_aux': precio_aux,
          //   'lote_aux': lote_aux,
          //   'descuento': descuento_aux,
          //   'fecha_exp': fecha_cad_aux,
          //   'cant_aux': cantidad_codigo
          // })
          for (let i = 0; i < cantidad_codigo; i++) {
            busquedaInsumo({
              'precio_aux': precio_aux,
              'lote_aux': lote_aux,
              'descuento': descuento_aux,
              'fecha_exp': fecha_cad_aux
            })
          }
        } else if(codigo_siempre == 1) {
          busquedaInsumo({
            'precio_aux': precio_aux,
            'lote_aux': lote_aux,
            'descuento': descuento_aux,
            'fecha_exp': fecha_cad_aux,
            'cant_aux': cantidad_codigo
          })
        }else{
          busquedaInsumo({
            'precio_aux': precio_aux,
            'lote_aux': lote_aux,
            'descuento': descuento_aux,
            'fecha_exp': fecha_cad_aux,
            'cant_aux': cantidad_codigo,
            'tipo_generador': 1
          })
        }

        limpiarCampos()
          setTimeout(function(){
            let class_precios = document.querySelectorAll(".class_precios");
          console.log(class_precios)
          for(let i = 0 ; i < class_precios.length; i++){
            total_calculo(i)
          }
          }, 1500)
        


      }

      const busquedaInsumo = ({precio_aux, lote_aux, fecha_exp, descuento, cant_aux = 1, tipo_generador = 0}) => {
        
        // $('#busqueda').click(function(event){
        if($('#codigo').val()!=""){
          $.ajax({
              type: 'post',
              url:"{{route('ingreso.formulario')}}",
              headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
              datatype: 'json',
              data: $("#ingreso").serialize(),
              success: function(data){
                

                  var costo_promedio = 0;


                  if(data[0].despacho == 0)//Código de producto
                  {
                    id= document.getElementById('contador').value;
                    var midiv = document.createElement("tr");
                    midiv.setAttribute("id","dato"+id);
                    var f = new Date();
                    var dia = ('0' + (f.getDate())).slice(-2);
                    var mes = ('0' + (f.getMonth()+1)).slice(-2);
                    var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                    var id2 = id.slice(-1);

                    var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;
                    var adicional = '';
                    // console.log(data[0].kardex_uso);
                    if(data[0].kardex_uso){
                      adicional = '<br><b>Cantidad de usos: ' + data[0].cantidad_usos_kardex;  
                    }

                    let select = "";

                    if (data[0].iva ==1) {
                      select = "checked";
                    }
                    midiv.innerHTML = `
                    <input type='hidden' id='iva${id}' value='${data[0].iva}'>
                    <td>
                      <input type='hidden' id='visibilidad${id}' name='visibilidad${id}' value='1' >
                      <input name='id${id}' type='hidden' value='${data[0].id}' > 
                      <input name='usos${id}' type='hidden' value='${data[0].usos}'>${data[0].codigo}
                    </td>
                    <td>
                        ${data[0].nombre}${adicional}
                    </td>
                    <td> 
                      <input type='text' value='${cant_aux}' class='input-number td_der'  onkeypress='return valida(event)' onkeyup='total_calculo(${id})' style='width: 60px;text-align: right;' id='cantidad${id}' name='cantidad${id}'>
                    </td>
                    <td> 
                      <input type='hidden' value='${serie}' name='serie${id}' id='s${id}' >${serie}</td> 
                    <td>
                      <input type='text' size='10' value='@if (isset($bodega_principal->id )) {{$bodega_principal->nombre}} @endif' readonly> 
                      <input type='hidden' id='id_bodega${id}' name='id_bodega${id}' value='@if(isset($bodega_principal->id )){{$bodega_principal->id}}@endif' >
                    <td>
                      <div class='form-group  lote_error${id}'>
                        <input style='width: 90px;' type='text' name='lote${id}' value="${lote_aux}"  required>
                      </div> 
                    </td>
                    <td>
                      <div class='form-group' >${data[0].registro_sanitario}</div> 
                    </td>
                    <td> 
                      <div class='form-group fecha_vencimiento_error${id}'> 
                        <input type='date' class='input-number' value='${fecha_exp}' name='fecha_vencimiento${id}' > 
                      </div>
                    </td>
                    <td>
                      <input name='descuento${id}' id='descuento${id}' class='input-number td_der' style='width: 50%;'  value='${descuento}' onkeyup='total_calculo(${id})'> 
                      <input type='hidden' name='descuentof${id}' id='descuentof${id}' value='${descuento}'>
                    </td>
                    <td>
                      <input class='td_der class_precios' onkeyup='total_calculo(${id})' id='precio${id}' type='text' style='width: 60px;' name='precio${id}' value="${precio_aux}" >
                    </td>
                    <td id='precio_final${id}'>
                    </td> 
                    <td> <input name='check_iva${id}' id='check_iva${id}' type='checkbox' ${select} disabled> </td>
                    <td>
                      <button type='button' onclick='eliminardato(${id})' class='btn btn-warning btn-margin'>{{trans('winsumos.eliminar')}}</button>
                    </td>`;
                    document.getElementById('crear').appendChild(midiv);
                    window.onbeforeunload = confirmarSalida;
                    id = parseInt(id);
                    id = id+1;
                    document.getElementById('contador').value = id;
                  }
                  if(data[0].despacho == 1)//Código de serie
                  {
                    for (let i = 0; i < cant_aux; i++) {
                      
                      id= document.getElementById('contador').value;
                      var midiv = document.createElement("tr");
                      midiv.setAttribute("id","dato"+id);
                      console.log("verificacion");
                      console.log(id);
                      var f = new Date();
                      var dia = ('0' + (f.getDate())).slice(-2);
                      var mes = ('0' + (f.getMonth()+1)).slice(-2);
                      var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                      var id2 = id.slice(-1);
                      var adicional = '';
                      if(data[0].kardex_uso){
                        adicional = '<br><b>Cantidad de usos: ' + data[0].cantidad_usos_kardex;  
                      }
                      var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;
                      let select = "";
                      if (data[0].iva ==1) {
                        select = "checked";
                      }
                      midiv.innerHTML = `
                      <input type='hidden' id='iva${id}' value='${data[0].iva}'>
                      <td> 
                        <input type='hidden' id='visibilidad${id}' name='visibilidad${id}' value='1' >
                        <input name='id${id}' type='hidden' value='${data[0].id}'>
                        <input name='usos${id}' type='hidden' value='${data[0].usos}' >${data[0].codigo}
                      </td>
                      <td>${data[0].nombre + adicional }</td>
                      <td  style='text-align: right;'>
                          <input type='hidden'  value='1' id='cantidad${id}' name='cantidad${id}' > 1

                      </td>
                      <td>
                        <input type='hidden' value='${serie}' name='serie${id}' id='s${id}' >${serie}
                      </td>
                      <td>
                        <input type='text' size='10' value='@if (isset($bodega_principal->id )) {{$bodega_principal->nombre}} @endif' readonly>
                        <input type='hidden' id='id_bodega${id}'  name='id_bodega${id}' value='@if(isset($bodega_principal->id )){{$bodega_principal->id}}@endif' >
                      <td> 
                        <div class='form-group  lote_error${id}'>
                          <input style='width: 90px;' type='text' name='lote${id}' id='lote${id}' value="${lote_aux}" required>
                        </div>
                      </td>
                      <td>
                        <div class='form-group'>${data[0].registro_sanitario}</div>
                      </td>
                      <td>
                        <div class='form-group fecha_vencimiento_error${id}'>
                          <input type='date' class='input-number' value='${fecha_exp}' name='fecha_vencimiento${id}' >
                        </div>
                      </td>
                      <td>
                        <input style='width: 50%;' class='td_der' name='descuento${id}' id='descuento${id}' onkeyup='total_calculo(${id})' value='${descuento}'>
                        <input type='hidden' name='descuentof${id}' id='descuentof${id}' value='0.00'>
                      </td>
                      <td>
                        <input type='text' name='precio${id}' class='td_der class_precios' id='precio${id}' style='width: 60px;' value='${precio_aux}' onkeyup='total_calculo(${id})' >
                      </td>
                      <td id='precio_final${id}' >
                      </td>
                      <td> <input name='check_iva${id}' id='check_iva${id}' type='checkbox' ${select} disabled> </td>
                      <td>
                        <button type='button' onclick='eliminardato(${id})' class='btn btn-warning btn-margin'>{{trans('winsumos.eliminar')}}</button>
                      </td>`;

                      console.log(midiv);
                      // total_calculo(id)
                      document.getElementById('crear').appendChild(midiv);
                      window.onbeforeunload = confirmarSalida;
                      id = parseInt(id);
                      id = id+1;
                      document.getElementById('contador').value = id;
                    }
                  }               

                  
              },
              error: function(data){
                  console.log(data);
              }
          })
        }

      // });
      }


    function enviar(){
         Swal.fire({
            title: 'Esta seguro que desea guardar?',
            text: "",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, guardar!'
         }).then((result) => {
            if (result.isConfirmed) {
              //  store()
               validarNoPedido();
            }
         })
    }
    const validarNoPedido = () => {
      let pedido = document.getElementById("pedido").value;
      let id_proveedor = document.getElementById("id_proveedor").value
      $.ajax({
        url: `{{ route('insumos.ingreso.validarNumeroPedido') }}`,
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        type: 'POST',
        datatype: 'json',
        data: {
          'pedido':       pedido,
          'id_proveedor': id_proveedor
        },
        success: function(data) {
          console.log(data);
          if(data.status == 'success'){
            store()
          }else{
            alertas(data.status, "Error...", data.msj.toUpperCase())
          }
        },
        error: function(data) {
          alertas(data.status, "Error...", "Error")
        }
      })
    }


    function store() {
        $('#envio').button('loading');
        console.log($("#frm"));
        var formulario = document.forms["frm"];
        var pedido = formulario.pedido.value;
        var num_factura = formulario.num_factura.value;
        var fecha = formulario.fecha.value;
        var vencimiento = formulario.vencimiento.value;
        var id_proveedor = formulario.id_proveedor.value;
        var bodega_recibe = formulario.bodega_recibe.value;
        // var id_empresa = formulario.id_empresa.value;
        var contador = formulario.contador.value;
        var tipo_= formulario.tipo_.value;
        var msj = "";
        if(pedido == "")
            msj += "Por favor, ingrese el numero del Pedido <br/>";
        if(num_factura == "")
            msj += "Por favor, ingrese el numero del Factura <br/>";
        if(fecha == "")
            msj += "Por favor, ingrese la fecha de la Orden <br/>";
        if(vencimiento == "")
            msj += "Por favor, ingrese la fecha de vencimiento <br/>";
        if(id_proveedor == "")
            msj += "Por favor, seleccione el proveedor <br/>";
        if(bodega_recibe == "")
            msj += "Por favor, seleccione la bodega <br/>";
        //  if(id_empresa == "")
        //     msj += "Por favor, seleccione la empresa <br/>";
        if(tipo_ == "")
            msj += "Por favor, seleccione el tipo <br/>".tipo_;
        if(contador == 0)
            msj += "Por favor, ingrese al menos un producto <br/>";

        if(msj == "")
        {
            $.ajax({
              type: 'post',
              url:"{{route('ingreso.guardar')}}",
              headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

              datatype: 'json',
              data: $("#frm").serialize(),
              beforeSend: function(){
                @if(Auth::user()->id=='0931563241' ||Auth::user()->id=='0924383631')
                Swal.fire({
                  title: 'Loading...',
                  width: 600,
                  padding: '3em',
                  background: '#fff',
                  backdrop: `
                    rgba(0,0,123,0.4)
                    url("https://c.tenor.com/DHkIdy0a-UkAAAAC/loading-cat.gif")
                    top
                    no-repeat
                  `
                })
                @endif
              },
              success: function(data){
                console.log(data);
                if(data.msj=='error'){
                  Swal.fire('Error!','Ocurrio un error al guardar el ingreso del pedido. <br> '+data.error ,'error');
                }else{
                  $('#envio').button('reset');
                  var dato_url = "{{ url('codigo/barras')}}";
                  // /window.onbeforeunload = beforeVoid;
                  Swal.fire('Correcto!','Datos Ingresados Correctamente','success');
                  setTimeout(function(){ location.href = dato_url; }, 1500);
                }

              },
              error: function(data){
                 Swal.close();
                 console.log(data);
                  $('#envio').button('reset');
                  var errores = data.responseJSON;
                  var contador = document.getElementById('contador').value;
                  var error ;
                  for(i=0; i<contador; i++) {
                    if(errores['lote'+i] != undefined){
                      error = errores['lote'+i];
                      $('.lote_error'+i).addClass("has-error");

                      $('.general').addClass("has-error");
                      $('#lote_errores').empty().html(errores['lote'+i]);

                    }
                    console.log(errores['fecha_vencimiento'+i]);
                    if(errores['fecha_vencimiento'+i]!= undefined){
                      //console.log('aqui');
                      $('.general').addClass("has-error");
                      $('.fecha_vencimiento_error'+i).addClass("has-error");
                      $('#fecha_errores').empty().html(errores['fecha_vencimiento'+i]);
                    }
                  }
                  if(data.responseJSON.pedido!=null){
                      $(".cl_pedido").addClass("has-error");
                      $('#str_pedido').empty().html(data.responseJSON.pedido);
                  }
                  if(data.responseJSON.id_proveedor!=null){
                      $(".cl_id_proveedor").addClass("has-error");
                      $('#str_id_proveedor').empty().html(data.responseJSON.pedido);
                  }
              }
            })
        }
        else{
          $('#envio').button('reset');
          Swal.fire("Mensaje : ",msj,"error");
        }
        $('#envio').button('reset');


      }
    $(document).ready(function(){
      $('#consecion').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        increaseArea: '20%' // optional
      });
      $('#consecion_det').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        increaseArea: '20%' // optional
      });
      $('.select2').select2({
      tags: false
    });
    });


    $('#example2').DataTable({
            'paging': false,
            dom: 'lBrtip',
            'lengthChange': false,
            'searching': true,
            'ordering': false,
            'responsive': true,
            'info': false,
            'autoWidth': true,
            'columnDefs': [
                { "width": "5%", "targets": 0 },
                { "width": "5%", "targets": 2 },
                { "width": "10%", "targets": 6 },
                { "width": "5%", "targets": 8 }
            ],
            language: {
                zeroRecords: " "
            },
            buttons: [{
            extend: 'copyHtml5',
            footer: true
            },

            {
            extend: 'excelHtml5',
            footer: true,
            title: "{{trans('winsumos.pedidos')}}"
            },
            {
            extend: 'csvHtml5',
            footer: true
            },
            {
            extend: 'pdfHtml5',
            orientation: 'landscape',
            pageSize: 'LEGAL',
            footer: true,
            title: "{{trans('winsumos.pedidos')}}",
            customize: function(doc) {
                doc.styles.title = {
                color: 'black',
                fontSize: '17',
                alignment: 'center'
                }
            }
            }
        ],
        });

  </script>
  <script type="text/javascript">


    function total_calculo(id){
      cantidad = parseInt($('#cantidad'+id).val());
      valor = parseFloat($('#precio'+id).val());
      descuento= parseFloat($('#descuento'+id).val());
      total = cantidad * valor;
      if(descuento>0){
        descf=(total*descuento/100);
        total = (cantidad * valor) - descf;
        console.log("el valor del descuento es"+descf);
              $("#descuentof"+id).val(descf.toFixed(2,2));
      }else{
        $("#descuentof"+id).val(0);
      }


      $('#precio_final'+id).text(parseFloat(total).toFixed(2));
      suma_totales();
    }

    function suma_totales(){

      
      contador  =  0;
      subtotal_0 = 0;
      subtotal_12 = 0;
      iva = 0;
      total = 0;
      desc=0;
      $("#crear tr").each(function(){
        $(this).find('td')[0];
        // visibilidad = $(this).find('#visibilidad'+contador).val();
        visibilidad = $('#visibilidad'+contador).val();
        if(visibilidad == 1){
          cantidad = parseInt($('#cantidad'+contador).val());
          valor = parseFloat($('#precio'+contador).val());
          descuento = parseFloat($('#descuentof'+contador).val());
          total = (cantidad * valor) - descuento;
          desc+=descuento;
          console.log(descuento);
          iva = $('#iva'+contador).val();
          if(iva == 1){
            subtotal_12 = subtotal_12 + total;
          }else{
            subtotal_0 = subtotal_0 + total;
          }

        }
        contador = contador+1;
      });
      iva = subtotal_12 * {{$iva->iva}};
      if(desc>0){
        total= subtotal_12+ subtotal_0 +iva ;
      }else{
        total = subtotal_12 + subtotal_0 + iva;
      }
      $('#subtotal_12_1').val(parseFloat(subtotal_12).toFixed(2));
      $('#subtotal_0_1').val(parseFloat(subtotal_0).toFixed(2));
      $('#iva_1').val(parseFloat(iva).toFixed(2));
      $('#total_1').val(parseFloat(total).toFixed(2));
      $("#descuentx").val(desc.toFixed(2,2));
      $('#subtotal_12').val(subtotal_12);
      $('#subtotal_0').val(subtotal_0);
      $('#iva').val(iva);
      $('#total').val(total);
    }

    $(document).ready(function()
    {
      $('#fecha').datetimepicker({
                useCurrent: false,
                format: 'YYYY/MM/DD',
                defaultDate: new Date()
                 //Important! See issue #1075

            });
      $('#vencimiento').datetimepicker({
                useCurrent: false,
                format: 'YYYY/MM/DD',
                defaultDate: new Date()

                 //Important! See issue #1075

            });
      src = "{{route('producto.listar')}}";

      $("#codigo").autocomplete({
          source: function( request, response ) {
            $.ajax( {
              url: src,
              dataType: "json",
              data: {
                term: request.term
              },
              success: function( data ) {
                response(data);
              }
            } );
          },
          minLength: 1,
          select: function(data, ui) {
          
         // actualiza_nombre_al_seleccionar(); 
          $('#nombre').val(ui.item.nombre);//

        }
        } );

      $("#codigo").change( function(){
          $.ajax({
            type: 'GET',
            url:"{{route('producto.codigo2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            datatype: 'json',
            data: $("#codigo"),
            success: function(data){
                console.log(data);
               if(data.id != '')
               {
                $('#nombre').val(data);
                // verificarCodSiempre()
              }else{
                $('#nombre').val('No se encuentra Producto');
               }
               verificarCodSiempre()

            },
            error: function(data){
                console.log(data);
            }
          });
      });
      

      $('#_busqueda').click(function(event){
        if($('#codigo').val()!=""){
          $.ajax({
              type: 'post',
              url:"{{route('ingreso.formulario')}}",
              headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

              datatype: 'json',
              data: $("#ingreso").serialize(),
              success: function(data){
                console.log(data);
                id= document.getElementById('contador').value;
                var midiv = document.createElement("tr");
                  midiv.setAttribute("id","dato"+id);

                  var costo_promedio = 0;
                  // if (data[0].inv_costo.costo_promedio != null) {
                  //   costo_promedio = parseFloat(data[0].inv_costo.costo_promedio);
                  // }

                  if(data[0].despacho == 0)//Código de producto
                  {
                    var f = new Date();
                    var dia = ('0' + (f.getDate())).slice(-2);
                    var mes = ('0' + (f.getMonth()+1)).slice(-2);
                    var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                    var id2 = id.slice(-1);
                    var select = [];

                    var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;

                    if (data[0].iva ==1) {
                      select = "checked";
                    }

                    // midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td><input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> <td> <input type='text' value='1' class='input-number' onkeypress='return valida(event)' onkeyup='total_calculo("+id+")' style='width: 60px;' id='cantidad"+id+"' name='cantidad"+id+"' ></td> <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td> "+" <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> <td><div class='form-group  lote_error"+id+"'> <input   type='text' name='lote"+id+"'  required> </div> </td> <td><div class='form-group' >"+data[0].registro_sanitario+"</div> </td> <td> <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' ></td><td> <input name='descuento"+id+"' id='descuento"+id+"' style='width: 50%;'  value='0.00' onkeyup='total_calculo("+id+")'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'> </td><td><input onkeyup='total_calculo("+id+")' value='0' id='precio"+id+"' type='number' style='width: 60px;' name='precio"+id+"' ></td><td id='precio_final"+id+"' ></td> <td> <input id='consecion_det"+id+"' name='consecion_det"+id+"' type='checkbox' value='1' class='flat-blue' style='position: absolute;'> </td> <td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>Eliminar</button></td> ";
                    midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td><input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >  <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> "
                    +" <td>"+data[0].nombre+"</td> <td> <input type='text' value='1' class='input-number td_der'  onkeypress='return valida(event)' onkeyup='total_calculo("+id+")' style='width: 60px;text-align: right;' id='cantidad"+id+"' name='cantidad"+id+"' ></td> "
                    +" <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td> "
                    // +" <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' @if(env('BODEGA_PRINCIPAL',13)==$value->id) selected @endif>{{$value->nombre}}</option> @endforeach</select></td>
                    +" <td><input type='text' size='10' value='@if (isset($bodega_principal->id )) {{$bodega_principal->nombre}} @endif' readonly> <input type='hidden' id='id_bodega"+id+"' name='id_bodega"+id+"' value='@if(isset($bodega_principal->id )){{$bodega_principal->id}}@endif' >"
                    +" <td><div class='form-group  lote_error"+id+"'> <input style='width: 90px;' type='text' name='lote"+id+"'  required> </div> </td> "
                    +" <td><div class='form-group' >"+data[0].registro_sanitario+"</div> </td> "
                    +" <td> <div class='form-group fecha_vencimiento_error"+id+"'> <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' > </div></td> "
                    +" <td> <input name='descuento"+id+"' id='descuento"+id+"' class='input-number td_der' style='width: 50%;'  value='0.00' onkeyup='total_calculo("+id+")'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'> </td> "
                    +" <td><input class='td_der' onkeyup='total_calculo("+id+")' value='"+costo_promedio+"' id='precio"+id+"' type='text' style='width: 60px;' name='precio"+id+"' ></td> "
                    
                    +" <td id='precio_final"+id+"' ></td> "
                    +"<td> <input name='check_iva"+id+"' id='check_iva"+id+"' type='checkbox' "+select+" disabled> </td>"
                    +"<button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>{{trans('winsumos.eliminar')}}</button></td> ";
                  }
                  if(data[0].despacho == 1)//Código de serie
                  {
                      var f = new Date();
                      var dia = ('0' + (f.getDate())).slice(-2);
                      var mes = ('0' + (f.getMonth()+1)).slice(-2);
                      var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                      var id2 = id.slice(-1);

                      var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;
                      
                      if (data[0].iva ==1) {
                        select = "checked";
                      }
                      midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td> <input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> "
                      +" <td  style='text-align: right;'> <input type='hidden'  value='1' id='cantidad"+id+"' name='cantidad"+id+"' > 1 </td> "
                      +" <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td>  "
                      // +" <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> "
                      +" <td><input type='text' size='10' value='@if (isset($bodega_principal->id )) {{$bodega_principal->nombre}} @endif' readonly> <input type='hidden' id='id_bodega"+id+"'  name='id_bodega"+id+"' value='@if(isset($bodega_principal->id )){{$bodega_principal->id}}@endif' >"
                      +" <td> <div class='form-group  lote_error"+id+"'><input style='width: 90px;' type='text' name='lote"+id+"' id='lote"+id+"' required></div></td> "
                      +" <td> <div class='form-group'>"+data[0].registro_sanitario+"</div></td> "
                      +" <td> <div class='form-group fecha_vencimiento_error"+id+"'><input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' ></div></td> "
                      +" <td> <input style='width: 50%;' class='td_der' name='descuento"+id+"' id='descuento"+id+"' onkeyup='total_calculo("+id+")' value='0.00'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'></td> "
                      +" <td><input type='text' name='precio"+id+"' class='td_der' id='precio"+id+"' style='width: 60px;' value='"+costo_promedio+"' onkeyup='total_calculo("+id+")' ></td> "
                      +" <td id='precio_final"+id+"' ></td> " 
                      +"<td> <input name='check_iva"+id+"' id='check_iva"+id+"' type='checkbox' "+select+" disabled> </td>"
                      +"<td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>{{trans('winsumos.eliminar')}}</button></td>  ";
                  }

                  /*
                  if($('#tipo_').val()!="1")//Mientras sea distinto a guia de remisión
                  {
                    if(data[0].despacho == 0)//Código de producto
                    {
                    var f = new Date();
                    var dia = ('0' + (f.getDate())).slice(-2);
                    var mes = ('0' + (f.getMonth()+1)).slice(-2);
                    var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                    var id2 = id.slice(-1);

                    var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;

                    midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td><input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> <td> <input type='text' value='1' class='input-number' onkeypress='return valida(event)' onkeyup='total_calculo("+id+")' style='width: 60px;' id='cantidad"+id+"' name='cantidad"+id+"' ></td> <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td> "+" <td><input type='hidden' id='entrada"+id+"' name='entrada"+id+"' value='1'><select name='estado_prod"+id+"' > <option value='1' >Por Consignación</option> <option value='2' selected>Ingreso Directo</option></select></td> <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> <td><div class='form-group  lote_error"+id+"'> <input   type='text' name='lote"+id+"'  required> </div> </td> <td><div class='form-group' >"+data[0].registro_sanitario+"</div> </td> <td> <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' ></td><td> <input name='descuento"+id+"' id='descuento"+id+"' style='width: 50%;'  value='0.00' onkeyup='total_calculo("+id+")'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'> </td><td><input onkeyup='total_calculo("+id+")' value='0' id='precio"+id+"' type='number' style='width: 60px;' name='precio"+id+"' ></td><td id='precio_final"+id+"' ></td> <td> <input id='consecion_det"+id+"' name='consecion_det"+id+"' type='checkbox' value='1' class='flat-blue' style='position: absolute;'> </td> <td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>Eliminar</button></td> ";
                    }
                    if(data[0].despacho == 1)//Código de serie
                    {
                      var f = new Date();
                      var dia = ('0' + (f.getDate())).slice(-2);
                      var mes = ('0' + (f.getMonth()+1)).slice(-2);
                      var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                      var id2 = id.slice(-1);

                      var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;

                      midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td> <input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> <td> <input type='hidden'  value='1' id='cantidad"+id+"' name='cantidad"+id+"' > 1 </td> <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td>  "+" <td><input type='hidden' id='entrada"+id+"' name='entrada"+id+"' value='1'><select name='estado_prod"+id+"' > <option value='1' >Por Consignación</option> <option value='2' selected>Ingreso Directo</option></select></td> <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> <td> <div class='form-group  lote_error"+id+"'><input type='text' name='lote"+id+"' id= required></div></td> <td> <div class='form-group'>"+data[0].registro_sanitario+"</div></td> <td> <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' ></td><td> <input style='width: 50%;' name='descuento"+id+"' id='descuento"+id+"' onkeyup='total_calculo("+id+")' value='0.00'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'></td><td><input type='number' name='precio"+id+"' id='precio"+id+"' style='width: 60px;' value='0' onkeyup='total_calculo("+id+")' ></td><td id='precio_final"+id+"' ></td> <td> <input id='consecion_det"+id+"' name='consecion_det"+id+"' type='checkbox' value='1' class='flat-blue' style='position: absolute;'> </td> <td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>Eliminar</button></td>  ";
                    }
                  }
                  if($('#tipo_').val()=="1")//Si el documento es guía de remisión
                  {
                    if(data[0].despacho == 0)//Código de producto
                    {
                    var f = new Date();
                    var dia = ('0' + (f.getDate())).slice(-2);
                    var mes = ('0' + (f.getMonth()+1)).slice(-2);
                    var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                    var id2 = id.slice(-1);

                    var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;

                    midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td><input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> <td> <input type='text' value='1' class='input-number' onkeypress='return valida(event)' onkeyup='total_calculo("+id+")' style='width: 60px;' id='cantidad"+id+"' name='cantidad"+id+"' ></td> <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td> "+" <td><input type='hidden' id='entrada"+id+"' name='entrada"+id+"' value='1'><select name='estado_prod"+id+"' > <option value='1' >Por Consignación</option> <option value='2'>Ingreso Directo</option></select></td> <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> <td><div class='form-group  lote_error"+id+"'> <input   type='text' name='lote"+id+"'  required> </div> </td> <td><div class='form-group' >"+data[0].registro_sanitario+"</div> </td> <td> <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' ></td><td> <input name='descuento"+id+"' id='descuento"+id+"' style='width: 50%;'  value='0.00' onkeyup='total_calculo("+id+")'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'> </td><td><input onkeyup='total_calculo("+id+")' value='0' id='precio"+id+"' type='number' style='width: 60px;' name='precio"+id+"' ></td><td id='precio_final"+id+"' ></td> <td> <input id='consecion_det"+id+"' name='consecion_det"+id+"' type='checkbox' value='1' class='flat-blue' style='position: absolute;'> </td> <td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>Eliminar</button></td> ";
                    }
                    if(data[0].despacho == 1)//Código de serie
                    {
                      var f = new Date();
                      var dia = ('0' + (f.getDate())).slice(-2);
                      var mes = ('0' + (f.getMonth()+1)).slice(-2);
                      var segundos = ('0' + (f.getMilliseconds())).slice(-1);
                      var id2 = id.slice(-1);

                      var serie = f.getFullYear().toString()+mes.toString()+dia.toString()+f.getHours().toString()+f.getMinutes().toString()+f.getSeconds().toString()+segundos.toString()+id2;

                      midiv.innerHTML = "<input type='hidden' id='iva"+id+"' value='"+data[0].iva+"' ><td> <input type='hidden' id='visibilidad"+id+"' name='visibilidad"+id+"' value='1' >   <input name='id"+id+"' type='hidden' value='"+data[0].id+"' > <input name='usos"+id+"' type='hidden' value='"+data[0].usos+"' >"+data[0].codigo+"</td> <td>"+data[0].nombre+"</td> <td> <input type='hidden'  value='1' id='cantidad"+id+"' name='cantidad"+id+"' > 1 </td> <td> <input type='hidden' value='"+serie+"' name='serie"+id+"' id='s"+id+"' >"+serie+"</td>  "+" <td><input type='hidden' id='entrada"+id+"' name='entrada"+id+"' value='1'><select name='estado_prod"+id+"' > <option value='1' >Por Consignación</option> <option value='2'>Ingreso Directo</option></select></td> <td><select name='id_bodega"+id+"' > @foreach($bodegas as $value) <option value='{{$value->id}}' >{{$value->nombre}}</option> @endforeach</select></td> <td> <div class='form-group  lote_error"+id+"'><input type='text' name='lote"+id+"' id= required></div></td> <td> <div class='form-group'>"+data[0].registro_sanitario+"</div></td> <td> <input type='date' class='input-number' value='{{date("Y-m-d")}}' name='fecha_vencimiento"+id+"' ></td><td> <input style='width: 50%;' name='descuento"+id+"' id='descuento"+id+"' onkeyup='total_calculo("+id+")' value='0.00'> <input type='hidden' name='descuentof"+id+"' id='descuentof"+id+"' value='0.00'></td><td><input type='number' name='precio"+id+"' id='precio"+id+"' style='width: 60px;' value='0' onkeyup='total_calculo("+id+")' ></td><td id='precio_final"+id+"' ></td> <td> <input id='consecion_det"+id+"' name='consecion_det"+id+"' type='checkbox' value='1' class='flat-blue' style='position: absolute;'> </td> <td> <button type='button' onclick='eliminardato("+id+")' class='btn btn-warning btn-margin'>Eliminar</button></td>  ";
                    }
                  }
                  */
                  document.getElementById('crear').appendChild(midiv);
                  window.onbeforeunload = confirmarSalida;
                  id = parseInt(id);
                  id = id+1;
                  document.getElementById('contador').value = id;
                  // $("#codigo").val("");
                  // $("#nombre").val("");
              },
              error: function(data){
                  console.log(data);
              }
          })
        }

      });

      

   
      src2 = "{{route('producto.nombre')}}";

      $("#nombre").autocomplete({
        source: function( request, response ) {
          $.ajax( {

            url: src2,
            dataType: "json",
            data: {
              term: request.term
            },
            success: function( data ) {
              response(data);
            }
          } );
        },
        minLength: 3,
        select: function(data, ui) {
          $('#codigo').val(ui.item.codigo)
          
          //actualiza_codigo_al_seleccionar();

        }
      });

      $("#nombre").change( function(){
          $.ajax({
            type: 'post',
            url:"{{route('producto.nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            datatype: 'json',
            data: $("#nombre"),
            success: function(data){
              if(data.id != ''){
                $('#codigo').val(data);
                verificarCodSiempre()
              }else{
                $("#codigo").val("No se encontro el producto")
              }
            },
            error: function(data){
                console.log(data);
            }
        })
      });
    });

    function actualiza_codigo_al_seleccionar(){
        $.ajax({
          type: 'post',
          url:"{{route('producto.nombre2')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          type: 'POST',
          datatype: 'json',
          data: $("#nombre"),
          success: function(data){
              $('#codigo').val(data);
              verificarCodSiempre()
          },
          error: function(data){
              console.log(data);
          }
      })
    }

    function actualiza_nombre_al_seleccionar(){
        $.ajax({
          type: 'post',
          url:"{{route('producto.codigo2')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          type: 'POST',
          datatype: 'json',
          data: $("#codigo"),
          success: function(data){
              $('#nombre').val(data);
              verificarCodSiempre()
          },
          error: function(data){
              console.log(data);
          }
      })
    }

    const verificarCodSiempre = () => {
      $.ajax({
        type: 'post',
          url:`{{route('insumos.producto.verificarCodSiempre')}}`,
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          type: 'POST',
          datatype: 'json',
          data: $("#codigo"),
          success: function(data){
            console.log(data);
            if(data.status == "success"){
              document.getElementById("codigo_siempre").value = data.valor;
              document.getElementById("kardex_uso_aux").value = data.kardex_uso;
              document.getElementById('busqueda').style.display = "initial"
            }else{
              document.getElementById("codigo_siempre").value = "";
              document.getElementById("kardex_uso_aux").value = '';
              document.getElementById('busqueda').style.display = "none"
            }
          },
          error: function(data){
              console.log(data);
          }
      })
    }

    const limpiarCampos = () => {
          cantidad_codigo = document.getElementById("cantidad_codigo").value  = "1"
          precio_aux = document.getElementById("precio_aux").value = "0"
          lote_aux = document.getElementById("lote_aux").value = ""
          fecha_cad_aux = document.getElementById("fecha_cad_aux").value = ""
          descuento_aux = document.getElementById("descuento_aux").value = "0"
          codigo_siempre = document.getElementById("codigo_siempre").value = ""
          document.getElementById("busqueda").style.display = "none"
          document.getElementById("codigo").value= ""
          document.getElementById("nombre").value= ""

    }


    function confirmarSalida()
    {
        return "Va a abandonar esta página. Cualquier cambio no guardado se perderá";
    }
    function beforeVoid()
    {}

    function valida(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla==8){
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros
        patron =/[0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function eliminardato(valor)
    {
      var nombre1 = "dato"+valor;
      var nombre2 = 'visibilidad'+valor;
      document.getElementById(nombre1).style.display='none';
      document.getElementById(nombre2).value = 0;
      // document.getElementById(nombre1).remove();
      suma_totales();
    }

    function calcular() {
        // obtenemos todas las filas del tbody
      var filas=document.querySelectorAll("#tbl_detalles tbody tr");

      var total=0;

      // recorremos cada una de las filas
      filas.forEach(function(e) {

          // obtenemos las columnas de cada fila
          var columnas=e.querySelectorAll("td");

          // obtenemos los valores de la cantidad y importe
          var cantidad=parseFloat(columnas[1].textContent);
          var importe=parseFloat(columnas[2].textContent);

          // mostramos el total por fila
          columnas[3].textContent=(cantidad*importe).toFixed(2);

          total+=cantidad*importe;
      });

      // mostramos la suma total
      var filas=document.querySelectorAll("#tbl_detalles tfoot tr td");
      filas[1].textContent=total.toFixed(2);
    }


    function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
                return false;

            return true;
        }


  </script>
@endsection
