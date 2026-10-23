@extends('contable.diario.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <div class="box">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
                <h3 class="box-title">Datos del Asiento Contable</h3>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-danger btn-gray"
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body dobra">
            <div class="box-body col-xs-12">
                <!--@if(!is_null($empresa->logo))
                  <img src="{{base_path().'/storage/app/logo/'.$empresa->logo}}"  alt="Logo Image"  style="width:80px;height:80px;" id="logo_empresa" >
                @endif-->
                @if(!is_null($empresa->logo))
                 <img src="{{asset('/logo').'/'.$empresa->logo}}"  alt="Logo Image"  style="width:100px;height:40px;" id="logo_empresa" >
                @endif
            </div>
            <div class="box-body col-xs-12">
                <label style="font-size: 14px">CALLE:{{$empresa->direccion}}</label><br/>
            </div>
            <!--<div class="col-xs-12">
                <label for="title">@if(isset($empresa)){{$empresa->razonsocial}}@endif</label>
            </div>-->
            <!--<div class="col-xs-6">
                <label for="title">Abel Romero Castillo y A.J.Tanca Marengo</label>
            </div>-->
            <!-- <div class="col-xs-6" style="text-align: right;">
                <label  for="asiento_nro">Asiento No:
                @if(!is_null($registro->detalles))
                 {{$registro->detalles[0]->id}}-{{$registro->fact_numero}}
                @endif
                </label>
            </div> -->
            <div class="box-body col-xs-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <span><b>Fecha de Registro:</b> {{date('Y-m-d',strtotime($registro->fecha_asiento))}}</span><br>
                                <span><b>Estado:</b> @if($registro->estado == '1') Activo @elseif($registro->estado =='2') Retenciones @elseif($registro->estado =='3') Comprobante Egreso @else Anulada @endif</span>
                                <p><b>Valor Registrado:</b> {{$registro->valor}}</p>
                                @php
                                    $usuario = \Sis_medico\User::find($registro->id_usuariocrea);
                                    $usuario_mod = \Sis_medico\User::find($registro->id_usuariomod);

                                @endphp
                                <p><b>Usuario Crea:</b> {{$usuario->nombre1}} {{$usuario->apellido1}} {{$usuario->apellido2}}</p>
                                <p><b>Usuario Modifica:</b> {{$usuario_mod->nombre1}} {{$usuario_mod->apellido1}} {{$usuario_mod->apellido2}}</p>
                                <p><b>Fecha Crea:</b> {{$registro->created_at}}</p>
                                <p><b>Detalle: </b> </br>
                                    {{$registro->observacion}}
                                </p>
                                <p><b>Sucursal: </b> {{$registro->sucursal}}</p>
                                @php
                                $log = Sis_medico\Log_Contable::where ('id_ant', $registro->id) -> orWhere ('id_referencia', $registro->id)->first();
                                @endphp
                                @if(!is_null($log))
                                    @if($log->id_ant == $registro->id)
                                        <label style="font-size: 15px;" class='label label-danger'>EL ASIENTO SE ENCUENTRA ANULADO</label> <br> <br>
                                        <label style="font-size: 15px;" class='label label-danger'>El ASIENTO QUE SE CREO POR LA ANULACIÓN ES: {{$log->id_referencia}}</label>
                                    @elseif($log->id_referencia == $registro->id)
                                        <label style="font-size: 15px;" class='label label-info'>ASIENTO QUE SE CREO POR UNA ANULACIÓN</label> <br>
                                        <label style="font-size: 15px;" class='label label-info'>EL ASIENTO ANULADO ES : {{$log->id_ant}}</label>
                                    @endif
                                @endif
                            </div>
                            <div class="col-md-12 table table-responsive">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                  <thead>
                                    <tr >
                                      <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Código</th>
                                      <th width="30%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cuenta</th>
                                      <th width="30%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Debe</th>
                                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Haber</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @php
                                        $contador=0;
                                    @endphp
                                    @foreach ($detalle as $value)
                                    <tr>
                                        <td>{{$value->id_plan_cuenta}}</td>
                                        <td>{{$value->nombre}}</td>
                                        <td>{{$value->descripcion}}</td>
                                        <td id="de{{$contador}}">$ {{$value->debe}}</td>
                                        <td id="h{{$contador}}">$ {{$value->haber}}</td>
                                        <input type="hidden" name="totales{{$contador}}" id="totales{{$contador}}" value="@if(($value->debe)!=0) {{$value->debe}} @elseif(($value->haber)!=0) {{$value->haber}} @else 0 @endif">
                                        <input type="hidden" name="debe{{$contador}}" id="debe{{$contador}}" value="{{$value->debe}}">
                                        <input type="hidden" name="haber{{$contador}}" id="haber{{$contador}}" value="{{$value->haber}}">
                                    </tr>
                                    @php $contador++; @endphp
                                    @endforeach
                                  </tbody>
                                  <tfoot>
                                    <thead>
                                        <tr>
                                            <th>TOTALES</th>
                                            <th>&nbsp;</th>
                                            <th>&nbsp;</th>
                                            <th> <span id="debe_total"></span> </th>
                                            <th><span id="haber_total"></span> </th>
                                        </tr>
                                    </thead>
                                  </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">

$(document).ready(function(){
    $('.select2_cuentas').select2({
        tags: false
    });
    sumadebe()
    sumahaber()
});

    function goBack() {
      window.history.back();
    }

    function sumadebe(){
        var contador= parseInt({{$contador}});
        var sumador=0;
        for(i=0; i<contador; i++){
            var totales= parseFloat($("#debe"+i).val());

            //alert(totales);
            if((totales)!=NaN){
              sumador+=totales;
              //alert(totales)
            }
            else{
                sumador=0;
            }
        }
        //alert(sumador);
        $("#debe_total").html('$ '+sumador.toFixed(2));
    }
    function sumahaber(){
        var contador= parseInt({{$contador}});
        var sumador=0;
        for(i=0; i<contador; i++){
            var totales= parseFloat($("#haber"+i).val());

            //alert(totales);
            if((totales)!=NaN){
              sumador+=totales;
              //alert(totales)
            }
            else{
                sumador=0;
            }
        }
        //alert(totales);
        $("#haber_total").html('$ '+sumador.toFixed(2));
    }
</script>
</section>
@endsection
