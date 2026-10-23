<div class="modal-body">
    <div class="col-md-12">
        <label> Formas de Pago</label>
        <input name="contador_pago" id="contador_pago" type="hidden" value="{{count($formapago)}}">
        <div class="row">
            <table id="ss" role="grid" class="table table-responsive" aria-describedby="example1_info" style="margin-top:0 !important">

                <thead>
                    <tr>
                        <th style="text-align: center;">Metodo</th>
                        <th style="text-align: center;">Fecha</th>
                        <th style="text-align: center;">Tipo</th>
                        <th style="text-align: center;">Número</th>
                        <th style="text-align: center;">Banco</th>
                        <th style="text-align: center;">Posee Fi</th>
                        <th style="text-align: center;">Cuenta</th>
                        <th style="text-align: center;">Girado</th>
                        <th style="text-align: center;">Valor</th>
                        <th style="text-align: center;">Valor B</th>
                        <th style="text-align: center;">
                            <button id="btn_pago" type="button" class="btn btn-success btn-xs btn-gray">

                                <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody id="agregar_pago">
                    @php
                    $conter=0;
                    @endphp
                    @foreach($formapago as $s)
                    <tr id="dato_pago{{$conter}}">
                        <td><select class="dogde" name="id_tip_pago{{$conter}}" id="id_tip_pago{{$conter}}" style="width: 100px;height:20px" onchange="revisar_componentes(this,{{$conter}});">
                                <option value="">Seleccione</option>@foreach($tipo_pago as $value)<option @if($s->id_tipo_pago==$value->id) selected='selected' @endif value="{{$value->id}}">{{$value->nombre}}</option>@endforeach
                            </select><input type="hidden" id="visibilidad_pago{{$conter}}" name="visibilidad_pago{{$conter}}" value="1"></td>
                        <td><input type="date" class="dogde input-number" value="{{$s->fecha}}" name="fecha_pago{{$conter}}" id="fecha_pago{{$conter}}" style="width: 120px;"></td>
                        <td><select id="tipo_tarjeta{{$conter}}" class="dogde" name="tipo_tarjeta{{$conter}}" style="width: 175px;height:20px">
                                <option value="">Seleccione...</option> @foreach($tipo_tarjeta as $tipo_t) <option @if($s->tipo_tarjeta==$tipo_t->id) selected='selected' @endif value="{{$tipo_t->id}}">{{$tipo_t->nombre}}@endforeach
                            </select></td>
                        <td><input type="text" name="numero_pago{{$conter}}" id="numero_pago{{$conter}}" style="width: 100px;" value="{{$s->numero}}"></td>
                        <td><select class="dogde" name="id_banco_pago{{$conter}}" id="id_banco_pago{{$conter}}" style="width: 90px;height:20px">
                                <option value="">Seleccione...</option>@foreach($lista_banco as $value)<option @if($s->banco==$value->id) selected='selected' @endif value="{{$value->id}}">{{$value->nombre}}</option>@endforeach
                            </select></td>
                        <td><input style="text-align:center;" type="checkbox" name="fi{{$conter}}" id="fi{{$conter}}" onchange="revision_total({{$conter}})" value="0"></td>
                        <td><input autocomplete="off" class="dogde" name="id_cuenta_pago{{$conter}}" id="id_cuenta_pago{{$conter}}" value="{{$s->cuenta}}"></td>
                        <td><input class="dogde" type="text" id="giradoa{{$conter}}" name="giradoa{{$conter}}"></td>
                        <td><input class="dogde text-right input-number fpago" type="text" id="valor{{$conter}}" name="valor{{$conter}}" style="width: 100px;" onblur="this.value=parseFloat(this.value).toFixed(2);" value="{{$s->valor}}" onchange="revision_total({{$conter}})" onkeypress="return soloNumeros(this);"></td>
                        <td><input class="dogde input-number" type="text" readonly id="valor_base{{$conter}}" name="valor_base{{$conter}}" onkeypress="return soloNumeros(event);" onchange="return redondea_valor_base(this,{{$conter}},2);" value="{{$s->valor}}"></td>
                        <td><button style="text-align:center;" type="button" onclick="eliminar_form_pag({{$conter}})" class="btn btn-danger btn-gray delete btn-xs"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>
                    </tr>
                    @php
                    $conter++;
                    @endphp
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
<div class="modal-footer">

</div>