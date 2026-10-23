@extends('contable.nuevo_rol_pago.base')
@section('action-content')
@php
  $tipo_usuario  = Auth::user()->id;
@endphp
<style type="text/css">
  .form-control {
    width: 100% !important;
  }
</style>  
<!-- Ventana modal Rol Pago-->
<div class="modal fade" id="modal_rol_pago" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<div class="modal fade" id="visualizar_estado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="detalle_rol" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="col-md-9">@php $txt_mes = ['', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE']; @endphp
        <h5><b>CUENTAS DE LOS ROLES DEL PERIODO {{ $cuentas_rol->anio }} - {{ $txt_mes[$cuentas_rol->mes] }} -  Sucursal: {{$sucursal->nombre_sucursal}}</b></h5>
      </div>
          
      <div class="col-md-1 text-right">
        <button  onclick="goBack()" class="btn btn-default btn-gray">
          <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
        </button>
      </div>
      @if($tipo_usuario == '0922290697')
            <div class="col-md-2"><a href="{{route('nuevo_rol.actualiza_2_decimales_rol', ['id'=>$cuentas_rol->id])}}"  class="btn btn-warning"> RECALCULAR 2 DECIMALES</a></div>
      @endif
    </div>
    <div class="box-body dobra">
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto" >CUENTAS DEL ROL</label>
        </div>
      </div>
      <div class="box-body dobra">
        <div class="form-group col-md-12">
          <div class="form-row">
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row">
                  @if($cuentas_rol->id_asiento != null)
                  <div class="col-md-8 alert-success" >
                    ASIENTOS YA GENERADOS         
                  </div>
                  @endif
                  <div class="table-responsive col-md-12">
                    <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%">
                      <thead>
                        <tr class='well-dark'>
                          <th width="20%" >Cuenta</th>
                          <th width="20%" >Item</th>
                          <th width="20%" >Debe</th>
                          <th width="20%" >Haber</th>
                          <th width="20%" style="padding-left: 10px;" >Accion</th>
                        </tr>
                      </thead>
                      <tbody>@php  $debe = 0;$haber = 0;  @endphp
                      @if($cuentas_rol->anio == '2022' && $cuentas_rol->mes <= '11' && $cuentas_rol->id_asiento != null && $cuentas->count() <= 0 )
                          @php 
                            $detalles  = Sis_medico\Ct_Asientos_Detalle::where('id_asiento_cabecera', $cuentas_rol->id_asiento)->get();  
                          @endphp
                          @foreach ($detalles as $detalle)
                            <tr>
                                <td>{{$detalle->cuenta_empresa->plan}}</td>
                                <td>{{$detalle->descripcion}}</td>
                                <td style="text-align: right;" >$ {{$detalle->debe}}</td>
                                <td style="text-align: right;" >$ {{$detalle->haber}}</td>
                                <td></td>
                            </tr>
                            @php 
                              $debe += $detalle->debe; $haber += $detalle->haber;
                            @endphp
                          @endforeach
                        @else
                        @foreach ($cuentas as $value)
                          @php  
                            $debe += $value->debe; $haber += $value->haber; 
                            $plan_empresa = Sis_medico\Plan_Cuentas_Empresa::where('id_plan', $value->id_plan_cuentas)->where('id_empresa',$cuentas_rol->id_empresa)->first();
                          @endphp
                          <tr>
                            <td>{{ $plan_empresa->plan }}</td>
                            <td>{{ $value->item }}</td>
                            <!--td style="text-align: right;">$ {{ number_format($value->debe, 4, ',', ' ') }}</td>
                            <td style="text-align: right;">$ {{ number_format($value->haber, 4, ',', ' ') }}</td-->
                            @if($cuentas_rol->id_asiento != null)
                              <td style="text-align: right;">$ {{ number_format($value->debe, 4, ',', ' ') }}</td>
                              <td style="text-align: right;">$ {{ number_format($value->haber, 4, ',', ' ') }}</td>
                            @else
                                <td style="text-align: right;" >@if($value->editable == '1')$ <input type="text" value="{{$value->debe}}"  onchange="actualiza_valor('{{$value->id}}',this,'{{$value->editable}}')"> @else $ {{ number_format($value->debe, 4, ',', ' ') }} @endif</td>
                                <td style="text-align: right;" >@if($value->editable == '2')$ <input type="text" value="{{$value->haber}}" onchange="actualiza_valor('{{$value->id}}',this,'{{$value->editable}}')"> @else $ {{ number_format($value->haber, 4, ',', ' ') }} @endif</td>
                             @endif
                            <td></td>
                          </tr>
                        @endforeach
                        @endif  
                      </tbody>
                      <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="text-align: right;">$<span id="idt_debe"> {{ number_format($debe, 4, ',', ' ') }}</span></td>
                            <td style="text-align: right;">$<span id="idt_haber"> {{ number_format($haber, 4, ',', ' ') }}</span></td>
                            <td style="text-align: right;"><span id="idt_dif"> </span></td>
                          </tr>
                      </tfoot>

                    </table>
                  </div>
                </div>
                <div class="row">
                  <form class="form-horizontal" role="form" id="form_asientos_roles1">
                    {{ csrf_field() }}
                      <div class="form-group col-md-4 col-xs-3">
                          <label for="fecha_asiento1" class="col-md-4 texto">Fecha:</label>
                          <div class="col-md-7">
                              <input id="fecha_asiento1" type="date" class="form-control validar" name="fecha_asiento1" value="" required autofocus>
                          </div>
                      </div>
                  </form>  
                  @if($cuentas_rol->id_asiento == null)
                  <button type="button" onclick="generar_asiento()" class="btn btn-warning">Generar Asientos</button>
                  @else
                  <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',['id' => $cuentas_rol->id_asiento ])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                      <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diario
                  </a>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto" >PAGO DE SUELDOS Y SALARIOS</label>
        </div>
      </div>
      <div class="box-body dobra">
        <div class="form-group col-md-12">
          <div class="form-row">
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row">
                  <form class="form-horizontal" role="form" id="form_pagos_roles">
                    {{ csrf_field() }}
                    <div class="col-md-12">
                        <input type="hidden" name="rol_asientos" id="rol_asientos" value="{{$cuentas_rol->id}}">
                        
                        <div class="form-group col-md-4 col-xs-3">
                            <label for="fecha_creacion" class="col-md-4 texto">Fecha:</label>
                            <div class="col-md-8">
                                <input id="fecha_creacion" type="date" class="form-control validar" name="fecha_creacion" value="" required autofocus>
                            </div>
                        </div>

                        <div class="form-group col-md-4 col-xs-5">
                            <label for="tipo_pago" class="col-md-4 texto">Tipo de Pago</label>
                            <div class="col-md-8">
                                <select class="form-control validar" id="tipo_pago" name="tipo_pago" onchange="obtener_seleccion()">
                                    <option value="">Seleccione...</option>
                                    @foreach($tipo_pago_rol as $value)
                                    <option value="{{$value->id}}">{{$value->tipo}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Numero de Cuenta Beneficiario Cobra-->
                        <div id="num_cuenta" class="form-group col-md-4 col-xs-4">
                            <label for="numero_cuenta" class="col-md-4 texto ">N# Cuenta</label>
                            <div class="col-md-8">
                                <input id="numero_cuenta" type="text" class="form-control" name="numero_cuenta" value="" autocomplete="off">
                            </div>
                        </div>

                        
                        <!--Banco Beneficiario Cobra-->
                        <div id="id_banco" class="form-group col-md-4 col-xs-4">
                            <label for="banco" class="col-md-4 texto">Banco</label>
                            <div class="col-md-8">
                                <select class="form-control " id="banco" name="banco">
                                    <option value="">Seleccione...</option>
                                    @foreach($lista_banco as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!--Fecha_Cheque-->
                        <div id="fech_cheq" class="form-group col-md-4 col-xs-4">
                            <label for="fecha_cheque" class="col-md-4 texto">Fecha Cheque:</label>
                            <div class="col-md-8">
                                <input id="fecha_cheque" type="date" class="form-control " name="fecha_cheque" value="{{ old('fecha_cheque') }}" required autofocus>
                            </div>
                        </div>
                        <!--Numero de Cheque-->
                        <div id="num_che" class="form-group col-md-4 col-xs-4">
                            <label for="numero_cheque" class="col-md-4 texto">N # Cheque:</label>
                            <div class="col-md-8">
                                <input id="numero_cheque" type="text" class="form-control " name="numero_cheque" value="{{ old('numero_cheque') }}" onkeypress="return isNumberKey(event)" autocomplete="off">
                            </div>
                        </div>
                        <!--Cuenta Saliente Paga-->
                        <div id="id_cuenta_saliente" class="form-group col-md-4 col-xs-4">
                            <label for="cuenta_saliente" class="col-md-4 texto">Cuenta Saliente</label>
                            <div class="col-md-8">
                                <select class="form-control " id="cuenta_saliente" name="cuenta_saliente">
                                    <option value="">Seleccione...</option>
                                    @foreach($bancos as $value)
                                    <option value="{{$value->cuenta_mayor}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!--Numero de Cheque-->
                        <div id="conce1" class="form-group col-md-8 col-xs-8">
                            <label for="concepto1" class="col-md-2 texto">Concepto:</label>
                            <div class="col-md-10">
                                <input id="concepto1" type="text" class="form-control " name="concepto1" value="PAGO DE ROLES {{ $cuentas_rol->anio }} - {{ $txt_mes[$cuentas_rol->mes] }} -  Sucursal: {{$sucursal->nombre_sucursal}}" autocomplete="off">
                            </div>
                        </div>

                        <!--Numero de Cheque-->
                        <div id="ben1" class="form-group col-md-8 col-xs-8">
                            <label for="beneficiario1" class="col-md-2 texto">Beneficiario:</label>
                            <div class="col-md-10">
                                <input id="beneficiario1" type="text" class="form-control " name="beneficiario1" value="" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12" ><br></div>
                    <div class="col-md-12" id="tabla_detalle">
                        <div class="table-responsive col-md-12">
                            Todos <input type="checkbox" name="ctodos" id="ctodos" onclick="sel_todos()" >
                            <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%">
                                <thead>
                                    <tr class='well-dark'>
                                        <th width="10%">Sel.</th>
                                        <th width="10%">Id</th>
                                        <th width="20%">Identificacion</th>
                                        <th width="30%">Nombres</th>
                                        <th width="15%">SUELDOS Y SALARIOS</th>
                                        <th width="15%">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $acum = 0; @endphp
                                    @foreach($roles as $rol)
                                    @php 
                                      $detalle = $rol->detalle->first();
                                      $acum   += round($detalle->neto_recibido,4);
                                    @endphp
                                    <tr>
                                        <td>@if($rol->id_asiento_pago == null)<input type="checkbox" name="roles[]" value="{{$rol->id}}">@endif</td> 
                                        <td>{{$rol->id}}</td>
                                        <td>{{$rol->id_user}}</td>
                                        <td>{{$rol->ct_nomina->nombres}} </td>
                                        <td style="text-align: right;">$ {{number_format(round($detalle->neto_recibido,4),4, '.', '')}}</td>
                                        <td>
                                          @if($rol->id_asiento_pago != null)
                                            <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',['id' => $rol->id_asiento_pago ])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                                              Asiento
                                            </a>
                                          @endif    
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td><b> Total Registros:</b></td>
                                        <td>{{ $roles->count() }}</td>
                                        <td></td>
                                        <td><b>Total:</b></td>
                                        <td style="text-align: right;">$ {{number_format($acum,2, '.', '')}}</td>
                                        <td>
                                          
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="form-group col-md-12">
                        <a class="btn btn-info" name="boton_guardar" id="boton_guardar" onclick="guardar_pago(event);">Generar Pago</a>
                    </div>

                  </form>
                  
                </div>
                
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto" >ASIENTOS DE APORTE PATRONAL Y SECAP</label>
        </div>
      </div>
      <div class="box-body dobra">
        <div class="form-group col-md-12">
          <div class="form-row">
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row">
                  @if($cuentas_rol->id_asiento_aporte != null)
                  <div class="col-md-8 alert-success" >
                    ASIENTOS YA GENERADOS         
                  </div>
                  @endif
                  <div class="table-responsive col-md-12">
                  <form class="form-horizontal" role="form" id="form_pago_aporte_nuevo">
                    <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%">
                    <input type="hidden" name="id_asiento" id="id_asiento" value="{{$cuentas_rol->id}}">
                      <thead>
                        <tr class='well-dark'>
                          <th width="20%" >Cuenta</th>
                          <th width="20%" >Item </th>
                          <th width="20%" >Valor</th>
                          <th width="20%" style="padding-left: 10px;" >Accion</th>
                        </tr>
                      </thead>
                      <tbody>@php  $debe = 0;$haber = 0;  @endphp
                        @foreach ($cuentas_iess as $value)
                          @php  
                            $debe += $value->debe; $haber += $value->haber; 
                            $plan_empresa = Sis_medico\Plan_Cuentas_Empresa::where('id_plan', $value->id_plan_cuentas)->where('id_empresa',$cuentas_rol->id_empresa)->first();
                            
                          @endphp
                          <tr>
                            <td>{{ $plan_empresa->plan }}</td>
                            <td>{{ $value->item }}</td>
                            <td style="text-align: right;">$ {{ number_format($value->debe, 2, ',', ' ') }}</td>
                            <td></td>
                          </tr>
                        @endforeach
                      </tbody>@php  $debe = $debe - $sueldo_pasante  @endphp
                      <tfoot>
                        
                        <tr>
                          <td></td>
                          <td>Sueldo Pasantes: (-)</td>
                          <td style="text-align: right;">$ {{ number_format($sueldo_pasante, 2, ',', ' ') }}</td>
                          <td></td>
                        </tr>
                        <tr>
                          <td></td>
                          <td></td>
                          <td style="text-align: right;">$ {{ number_format($debe, 2, ',', ' ') }}</td>
                          <td></td>
                        </tr>
                        <tr>
                          <td style="text-align: right;">APORTE PATRONAL</td>
                          <td style="text-align: right;">{{ number_format($aporte_patronal, 6, ',', ' ') }}</td>
                          <td style="text-align: right;">
                            @if($cuentas_rol->id_asiento_aporte == null)
                              <div class="col-xs-9">
                                <input id="vt_aporte_patronal" type="text" class="form-control" name="vt_aporte_patronal"   @if(!is_null($cuentas_rol->aporte_patronal)) value= "{{round($cuentas_rol->aporte_patronal,4)}}" @else value="{{round($valor_aporte,4)}}" @endif required autofocus onchange="actualiza_aporte_patronal( this.value )" onkeypress="return soloNumeros(this);" readonly>       
                              </div>
                            @else 
                              {{ number_format($cuentas_rol->aporte_patronal, 4, ',', ' ') }} 
                            @endif
                         </td>
                          <td></td>
                        </tr>
                        <tr>
                          <td style="text-align: right;">APORTE IECE - SECAP</td>
                          <td style="text-align: right;"> {{ number_format($secap, 6, ',', ' ') }}</td>
                          <td style="text-align: right;">
                            @if($cuentas_rol->id_asiento_aporte == null)
                              <div class="col-xs-9">
                                <input id="vt_aporte_secap" type="text" class="form-control " name="vt_aporte_secap"    @if(!is_null($cuentas_rol->aporte_secap)) value="{{round($cuentas_rol->aporte_secap,4)}}" @else value="{{round($valor_secap,4)}}" @endif required autofocus onchange="actualiza_aporte_secap( this.value )"  onkeypress="return soloNumeros(this);" readonly>       
                              </div>
                            @else
                              {{ number_format($cuentas_rol->aporte_secap, 4, ',', ' ') }}
                            @endif
                          </td>
                          <td></td>
                        </tr>
                      </tfoot>
                    
                    </table>
                    </form>
                    <form class="form-horizontal" role="form" id="form_pago_aporte">
                      {{ csrf_field() }}
                      <div class="col-md-12">
                          <input type="hidden" name="rol_asientos_aporte" id="rol_asientos_aporte" value="{{$cuentas_rol->id}}">

                          <input type="hidden" name="total_aporte" id="total_aporte" value="{{$total_aporte}}">
                          <input type="hidden" name="p_patronal" id="p_patronal" value="{{$aporte_patronal}}">
                          <input type="hidden" name="p_secap" id="p_secap" value="{{$secap}}">
                          <input type="hidden" name="aporte_patronal" id="aporte_patronal" value="{{$valor_aporte}}">
                          <input type="hidden" name="aporte_secap" id="aporte_secap" value="{{$valor_secap}}">
                          
                          <div class="form-group col-md-4 col-xs-3">
                              <label for="fecha_asiento_aporte" class="col-md-4 texto">Fecha:</label>
                              <div class="col-md-7">
                                  <input id="fecha_asiento_aporte" type="date" class="form-control validar" name="fecha_asiento_aporte" value="" required autofocus>
                              </div>
                          </div>

                      </div>
                      
                    </form>
                  </div>
                </div>
                <div class="row">
                  @if($cuentas_rol->id_asiento_aporte == null)
                  <button type="button" onclick="generar_asiento_aporte()" class="btn btn-warning">Generar Asientos</button>
                  @else
                  <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',['id' => $cuentas_rol->id_asiento_aporte ])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                      <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diario
                  </a>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @if($cuentas_rol->id_asiento_aporte != null)
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto" >PAGO DE PLANILLAS</label>
        </div>
      </div>
      <div class="box-body dobra">
       
          <div class="form-row">
            <div id="contenedor">
                  @if($cuentas_rol->id_asiento_planilla != null)
                  <div class="col-md-8 alert-success" >
                    ASIENTOS YA GENERADOS         
                  </div>
                  @endif
                  
                  <form class="form-horizontal" role="form" id="form_pagos_planillas">
                    {{ csrf_field() }}
                    <input type="hidden" name="rol_planillas" id="rol_planillas" value="{{$cuentas_rol->id}}">
                    
                    
                    <div class="col-md-12" ><br></div>
                    <div class="col-md-12" id="tabla_detalle_plantilla">
                        <div class="table-responsive col-md-12">
                          <div class="table-responsive col-md-12">
                            <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%">
                              <thead>
                                <tr class='well-dark'>
                                  <!--th width="20%" >Cuenta</th-->
                                  <th width="20%" >Item</th>
                                  <th width="20%" >Valor</th>
                                  <th width="20%" style="padding-left: 10px;" >Accion</th>
                                </tr>
                              </thead>
                              <tbody>@php  $debe = 0;$haber = 0;$planilla = 0;  @endphp
                                @foreach ($cuentas_iess2 as $value)
                                  @php  
                                    $debe += $value->debe; $haber += $value->haber; 
                                    $plan_empresa = Sis_medico\Plan_Cuentas_Empresa::where('id_plan', $value->id_plan_cuentas)->where('id_empresa',$cuentas_rol->id_empresa)->first();
                                  @endphp
                                  <tr>
                                    <!--td>{{ $plan_empresa->plan }}</td-->
                                    <td>{{ $value->item }}</td>
                                    <td style="text-align: right;">$ {{ number_format($value->haber, 4, ',', ' ') }}</td>
                                    <td></td>
                                  </tr>
                                @endforeach
                                @php 
                                  $planilla += $haber; 

                                @endphp
                                <tr>
                                    <td>APORTE PATRONAL 11.15%</td>
                                    <td style="text-align: right;">$ {{ number_format($cuentas_rol->aporte_patronal, 4, ',', ' ') }}</td>
                                    <td></td>
                                  </tr>
                                  <tr>
                                    <td>APORTE SECAP 1%</td>
                                    <td style="text-align: right;">$ {{ number_format($cuentas_rol->aporte_secap, 4, ',', ' ') }}</td>
                                    <td></td>
                                  </tr>
                              </tbody>
                              <tfoot>
                                <tr>@php $planilla = $planilla + $cuentas_rol->aporte_patronal + $cuentas_rol->aporte_secap @endphp
                                  <td></td>
                                  <td style="text-align: right;">$ {{ number_format($planilla, 4, ',', ' ') }}</td>
                                  <td></td>
                                </tr>
                              </tfoot>

                            </table>
                    
                          </div> 
                          
                        </div>
                    </div>
                    <input type="hidden" name="total_planilla" id="total_planilla" value="{{$planilla}}">
                    @if(count($pago_rubros)==0)
                    <div class="form-group col-md-12">
                        <a class="btn btn-warning" name="boton_cargar" id="boton_cargar" onclick="cargar_pago(event);">Cargar Pago</a>
                    </div>
                    @endif
                    
                    @if(count($pago_rubros) != '0' && $cuentas_rol->id_asiento_planilla == null ) 
                    <div  class="form-group col-md-8 col-xs-9" style="align-content: right;"> <br> </div> 
                      
                    <div  class="form-group col-md-4 col-xs-3" style="align-content: right;">
                          <div style="display: flex;">
                            <select name="cuenta" id="cuenta" class="form-control select2" style="flex-grow: 1; margin-right: 10px; width: 100px;">
                                  <option value="">Seleccione...</option>
                                  @foreach($plan_cuentas as $value)
                                      <option value="{{$value->id}}">{{$value->pempresa->plan}} - {{$value->pempresa->nombre}}</option>
                                  @endforeach
                              </select>
                              <a class="btn btn-info" name="boton_agregar" id="boton_agregar" onclick="agregarRubro(event);">
                                  <i class="glyphicon glyphicon-plus"  aria-hidden="true"></i>&nbsp;&nbsp;Agregar
                              </a>
                          </div>
                    </div>
                    @endif

                    <div class="col-md-12" id="tabla_detalles" style="position: relative; margin-top: 20px;">

                        <div class="table-responsive col-md-12">
                          <div class="table-responsive col-md-12">
                            <table id="example3" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%; <?php count($pago_rubros)==0?'display: none;':''?>">
                              <thead>
                                <tr class='well-dark'>
                                  <th width="55%" >Detalle</th>
                                  <th width="20%" >Debe</th>
                                  <th width="20%" >Haber</th>
                                  <th width="5%" style="padding-left: 10px;" >Accion</th>
                                </tr>
                              </thead>
                              <tbody id="body">
                              @php $debe = 0; $haber = 0; @endphp
                              @if(count($pago_rubros)>0)
                                @if($cuentas_rol->id_asiento_planilla != null)
                                 
                                  @foreach ($pago_rubros as $value)
                                    @php $debe += $value->debe; $haber += $value->haber; @endphp
                                  <tr>
                                      <td>{{$value->id_plan_cuentas}} - {{$value->descripcion}}</td>
                                      <td style="text-align: right;">{{number_format($value->debe, 2, ',', ' ')}}</td>
                                      <td style="text-align: right;">{{number_format($value->haber, 2, ',', ' ')}}</td>
                                      <td>
                                          
                                      </td>
                                  </tr>
                                  @endforeach
                                  <tr>
                                    <td></td>
                                      <td style="text-align: right;">{{number_format($debe, 2, ',', ' ')}}</td>
                                      <td style="text-align: right;">{{number_format($haber, 2, ',', ' ')}}</td>
                                      <td>
                                          
                                      </td>  
                                  </tr>  
                                @else
                                  @foreach ($pago_rubros as $value)
                                    @php $debe += $value->debe; $haber += $value->haber; @endphp
                                    <tr>
                                        <td>{{$value->id_plan_cuentas}} - {{$value->descripcion}}</td>
                                        <td style="text-align: right;">
                                          <input type="text" id="debe_{{$value->id}}" name="debe[{{$value->id}}]" value="{{number_format($value->debe, 2, '.', ' ')}}" onchange="guardar('{{$value->id}}')" style="text-align: right;">
                                        </td>
                                        <td style="text-align: right;"><input type="text" id="haber_{{$value->id}}" name="haber[{{$value->id}}]" value="{{number_format($value->haber, 2, '.', ' ')}}" onchange="guardar('{{$value->id}}')" style="text-align: right;"></td>
                                        <td>
                                          <button type="button" class="btn btn-danger" onclick="anularRubro('{{$value->id}}')"><i class="glyphicon glyphicon-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                      <td></td>
                                        <td style="text-align: right;">{{number_format($debe, 2, ',', ' ')}}</td>
                                        <td style="text-align: right;">{{number_format($haber, 2, ',', ' ')}}</td>
                                        <td>
                                            
                                        </td>  
                                    </tr>
                                @endif
                              @endif
                              
                              </tbody>
                            </table>
                          </div>
                        </div>
                    </div>
                    
                    @if(is_null($cuentas_rol->id_asiento_planilla) && count($pago_rubros) > 0)
                    
                    <div class="col-md-12">
                                
                        
                        <div class="form-group col-md-4 col-xs-3">
                            <label for="fecha_planilla" class="col-md-4 texto">Fecha:</label>
                            <div class="col-md-8">
                                <input id="fecha_planilla" type="date" class="form-control validar" name="fecha_planilla" value="@if(!is_null($cuentas_rol->fecha_planilla)){{ date('Y-m-d', strtotime($cuentas_rol->fecha_planilla)) }}@endif" required autofocus>
                            </div>
                        </div>

                        <div class="form-group col-md-4 col-xs-5">
                            <label for="id_tipo_pago_planilla" class="col-md-4 texto">Tipo de Pago</label>
                            <div class="col-md-8">
                                <select class="form-control validar" id="id_tipo_pago_planilla" name="id_tipo_pago_planilla" onchange="obtener_seleccion_planilla()">
                                    <option value="">Seleccione...</option>
                                    @foreach($tipo_pago_rol as $value)
                                    <option value="{{$value->id}}" @if($cuentas_rol->id_tipo_pago_planilla==$value->id) selected @endif>{{$value->tipo}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Numero de Cuenta Beneficiario Cobra-->
                        <div id="pnum_cuenta" class="form-group col-md-4 col-xs-4">
                            <label for="numero_cuenta_planilla" class="col-md-4 texto ">N# Cuenta</label>
                            <div class="col-md-8">
                                <input id="numero_cuenta_planilla" type="text" class="form-control" name="numero_cuenta_planilla" value="@if(!is_null($cuentas_rol->numero_cuenta_planilla)){{$cuentas_rol->numero_cuenta_planilla}}@endif" autocomplete="off">
                            </div>
                        </div>

                        
                        <!--Banco Beneficiario Cobra-->
                        <div id="pid_banco" class="form-group col-md-4 col-xs-4">
                            <label for="id_banco_planilla" class="col-md-4 texto">Banco</label>
                            <div class="col-md-8">
                                <select class="form-control " id="id_banco_planilla" name="id_banco_planilla">
                                    <option value="">Seleccione...</option>
                                    @foreach($lista_banco as $value)
                                    <option value="{{$value->id}}"  @if($cuentas_rol->id_banco_planilla==$value->id) selected @endif>{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!--Fecha_Cheque-->
                        <div id="pfech_cheq" class="form-group col-md-4 col-xs-4">
                            <label for="fecha_cheque_planilla" class="col-md-4 texto">Fecha Cheque:</label>
                            <div class="col-md-8">
                                <input id="fecha_cheque_planilla" type="date" class="form-control " name="fecha_cheque_planilla" autofocus>
                            </div>
                        </div>
                        <!--Numero de Cheque-->
                        <div id="pnum_che" class="form-group col-md-4 col-xs-4">
                            <label for="numero_cheque_planilla" class="col-md-4 texto">N # Cheque:</label>
                            <div class="col-md-8">
                                <input id="numero_cheque_planilla" type="text" class="form-control " name="numero_cheque_planilla" value="@if(!is_null($cuentas_rol->numero_cheque_planilla)){{$cuentas_rol->numero_cheque_planilla}} @endif" onkeypress="return isNumberKey(event)" autocomplete="off">
                            </div>
                        </div>
                        <!--Cuenta Saliente Paga-->
                        <div id="pid_cuenta_saliente" class="form-group col-md-4 col-xs-4">
                            <label for="id_cuenta_planilla" class="col-md-4 texto">Cuent Saliente</label>
                            <div class="col-md-8">
                                <select class="form-control " id="id_cuenta_planilla" name="id_cuenta_planilla">
                                    <option value="">Seleccione...</option>
                                    @foreach($bancos as $value)
                                    <option value="{{$value->cuenta_mayor}}"  @if($cuentas_rol->id_cuenta_planilla==$value->cuenta_mayor) selected @endif>{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="conce2" class="form-group col-md-8 col-xs-8">
                            <label for="concepto2" class="col-md-2 texto">Concepto:</label>
                            <div class="col-md-10">
                                <input id="concepto2" type="text" class="form-control " name="concepto2" value="PAGO PLANILLAS {{ $cuentas_rol->anio }} - {{ $txt_mes[$cuentas_rol->mes] }} -  Sucursal: {{$sucursal->nombre_sucursal}}" autocomplete="off">
                            </div>
                        </div>
                        <div id="ben2" class="form-group col-md-8 col-xs-8">
                            <label for="beneficiario2" class="col-md-2 texto">Beneficiario:</label>
                            <div class="col-md-10">
                                <input id="beneficiario2" type="text" class="form-control " name="beneficiario2" value="@if(!is_null($cuentas_rol->beneficiario_planilla)){{$cuentas_rol->beneficiario_planilla}}@endif" autocomplete="off">
                            </div>
                        </div>
                   
                    </div>
             
                    <div class="form-group col-md-12">
                        <a class="btn btn-info" name="boton_guardar_pago" id="boton_guardar_pago" onclick="guardar_pago_rubros(event);">Generar Pago </a>
                    </div>
                    @elseif($cuentas_rol->id_asiento_planilla != null)
                    <a id="btnVerAsiento" name="btnVerAsiento" class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',['id' => $cuentas_rol->id_asiento_planilla ])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                        <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diario
                    </a>
                    @endif
                    
                    
                    <div class="row">
                      <div class="col-md-12">
                        <label>ASIENTOS BENEFICIOS</label>
                      </div>
                    </div>
                    <div class="col-md-12" id="tabla_detalle_beneficio">
                      <div class="form-group col-md-4 col-xs-3">
                          <label for="fecha_beneficio" class="col-md-4 texto">Fecha:</label>
                          <div class="col-md-7">
                              <input id="fecha_beneficio" type="date" class="form-control validar" name="fecha_beneficio" value="" required autofocus>
                          </div>
                      </div>
                      <div class="form-group col-md-4 col-xs-3">
                        <button type="button" class="btn btn-success" data-remote="{{ route('contable.nuevo_rol.detalle_beneficios',['id'=> $cuentas_rol->id])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#detalle_rol">Detalle</button>
                      </div>  
                        <div class="table-responsive col-md-12">
                          <div class="table-responsive col-md-12">
                            <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%">
                              <thead>
                                <tr class='well-dark'>
                                  <th width="20%" >Cuenta</th>
                                  <th width="20%" >Nombre</th>
                                  <th width="20%" >Debe</th>
                                  <th width="20%" >Haber</th>
                                  <!--th width="20%" style="padding-left: 10px;" >Accion</th-->
                                </tr>
                              </thead>
                              <tbody>@php $tdebe = 0; $thaber = 0;  @endphp
                                @foreach ($arr_beneficios as $value)
                                  <tr>@php $tdebe += $value['debe']; $thaber += $value['haber']; @endphp
                                    <td>{{ $value['id_cuenta'] }}</td>
                                    <td>{{ $value['nombre'] }}</td>
                                    <td style="text-align: right;">$ {{ number_format($value['debe'], 2, ',', ' ') }}</td>
                                    <td style="text-align: right;">$ {{ number_format($value['haber'], 2, ',', ' ') }}</td>
                                    <!--td> 
                                    </td-->
                                  </tr>
                                @endforeach
                              </tbody>
                              <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: right;">$ {{ number_format($tdebe, 4, ',', ' ') }}</td>
                                    <td style="text-align: right;">$ {{ number_format($thaber, 4, ',', ' ') }}</td>
                                    <td></td>
                                  </tr>
                              </tfoot>

                            </table>
                    
                          </div> 
                          
                        </div>
                        @if($cuentas_rol->id_asiento_beneficios == null)
                        <div class="form-group col-md-12">
                            <a class="btn btn-info" name="boton_guardar" id="boton_guardar" onclick="guardar_beneficio(event);">Generar Pago</a>
                        </div>
                        @else
                        <div class="form-group col-md-12">
                        <a class="btn btn-success btn-gray " data-remote="{{ route('compras.modal_estado',['id' => $cuentas_rol->id_asiento_beneficios ])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#visualizar_estado">
                            <i class="glyphicon glyphicon-eye-open"  aria-hidden="true"></i>&nbsp;&nbsp;Visualizar Asiento diario
                        </a>
                        </div>
                        @endif
                        </div>


                  </form>
                  
                
                
              
            </div>
          </div>
       
      </div>
      @endif


    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
          $('#example2').DataTable({
          'paging'      : false,
          'lengthChange': false,
          'searching'   : false,
          'ordering'    : false,
          'info'        : false,
          'autoWidth'   : false
        })

        $('.select2').select2({
            width: '100%', // Establece el ancho del select
            placeholder: 'Seleccione...', // Mensaje de placeholder
            search: true // Habilita la función de búsqueda
        });

        $('#modal_rol_pago').on('hidden.bs.modal', function(){
          $(this).removeData('bs.modal');
        });
        
        $('#visualizar_estado').on('hidden.bs.modal', function(){
          $(this).removeData('bs.modal');
        });

        $('#detalle_rol').on('hidden.bs.modal', function(){
          $(this).removeData('bs.modal');
        });

        $('.select2_cuentas').select2({
            tags: false
        });

        $('#id_tipo_pago_planilla').trigger('change');
        if($('#id_cuenta_planilla').val()!=null){
          $('#id_cuenta_planilla').val('{{$cuentas_rol->id_cuenta_planilla}}').trigger('change');
        }

        if($('#numero_cheque_planilla').val()!=null){
          $('#numero_cheque_planilla').val('{{$cuentas_rol->numero_cheque_planilla}}');
        }
        
    });
    
    function goBack() {
      window.history.back();
    }

    function actualiza_aporte_patronal( value){
      $('#aporte_patronal').val(value);
    }

    function actualiza_aporte_secap( value){
      $('#aporte_secap').val(value);
    }

    function generar_asiento(){
      let fecha_asiento1 = document.getElementById('fecha_asiento1').value;

      
      let msj = "";
      if (fecha_asiento1 == "") {
          msj += "Seleccione la Fecha de Creacion <br>";
      }

      if (msj != "") {
          alertas('error', 'Error!..', msj)
      } else {
      
        var confirmar = confirm("Desea Generar Asiento para el Periodo Actual");
        if(confirmar){
          
          $.ajax({
                type: 'post',
                url: "{{route('nuevo_rol.generar_asientos',[ 'id' => $cuentas_rol->id ])}}",
               
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#form_asientos_roles1").serialize(),
                success: function(data) {
                  console.log(data);
                    alertas(data.respuesta, data.titulos, data.msj);
                    if(data.respuesta == 'success'){
                        setTimeout(function(){
                            location.reload();
                        }, 1000);
                    }else{
                      swal({
                        title: "Error!",
                        type: "error",
                        html: data.msj
                      }); 
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });

        }else{
          location.reload();
        }
    }
  }

    function generar_asiento_aporte(){
      let fecha_asiento = document.getElementById('fecha_asiento_aporte').value;
        

      let msj = "";
      if (fecha_asiento == "") {
          msj += "Seleccione la Fecha de Creacion <br>";
      }

      if (msj != "") {
          alertas('error', 'Error!..', msj)
      } else {
        var confirmar = confirm("Desea Generar Asiento del Aporte Patronal y Secap");
        if(confirmar){
          $.ajax({
                type: 'post',
                url: "{{ route('nuevo_rol.aportes_patronales') }}",
               
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#form_pago_aporte").serialize(),
                success: function(data) {
                    alertas(data.respuesta, data.titulos, data.msj);
                    if(data.respuesta == 'success'){
                        setTimeout(function(){
                            location.reload();
                        }, 1000);
                    }else{
                      swal({
                        title: "Error!",
                        type: "error",
                        html: data.msj
                      }); 
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });

        }else{
          location.reload();
        }
      }  
    }

    
    function guardar_beneficio(){
      let fecha_beneficio = document.getElementById('fecha_beneficio').value;
        

      let msj = "";
      if (fecha_beneficio == "") {
          msj += "Seleccione la Fecha de Creacion <br>";
      }

      if (msj != "") {
          alertas('error', 'Error!..', msj)
      } else {
        var confirmar = confirm("Desea Generar Asiento de los Beneficios");
        if(confirmar){
          $.ajax({
                type: 'post',
                url: "{{ route('nuevo_rol.asientos_beneficios_sociales') }}",
               
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#form_pagos_planillas").serialize(),
                success: function(data) {
                    alertas(data.respuesta, data.titulos, data.msj);
                    if(data.respuesta == 'success'){
                        setTimeout(function(){
                            
                        }, 1000);
                    }
                    location.reload();
                },
                error: function(data) {
                    console.log(data);
                }
            });

        }else{
          location.reload();
        }
      }  
    }

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    function guardar_pago(e) {
        e.preventDefault();
        let fecha_asiento = document.getElementById('fecha_creacion').value;
        let tipo_pago = document.getElementById('tipo_pago').value;
        let cuenta_saliente = document.getElementById('cuenta_saliente').value;
        let fecha_cheque = document.getElementById('fecha_cheque').value;
        let numero_cheque = document.getElementById('numero_cheque').value;
        let numero_cuenta = document.getElementById('numero_cuenta').value;
        let banco = document.getElementById('banco').value;
        let concepto1 = document.getElementById('concepto1').value;
        let beneficiario1 = document.getElementById('beneficiario1').value;

        let msj = "";
        if (fecha_asiento == "") {
            msj += "Seleccione la Fecha de Creacion <br>";
        }
        if (tipo_pago == "") {
            msj += "Seleccione el Tipo de Pago <br>";
        }
        if (cuenta_saliente == "") {
            msj += "Seleccione la Cuenta Saliente <br>";
        }
        if(tipo_pago == '3'){ //CHEQUE
            if (fecha_cheque == "") {
                msj += "Seleccione la Fecha del Cheque <br>";
            }
            if (numero_cheque == "") {
                msj += "Escriba el Numero de Cheque <br>";
            }
            if (beneficiario1 == "") {
                msj += "Escriba el beneficiario <br>";
            }
        }

        if(tipo_pago == '1'){ //ACREDITACION 
            if (numero_cuenta == "") {
                msj += "Escriba el Numero de Cuenta <br>";
            }
            if (banco == "") {
                msj += "Seleccione el banco <br>";
            }
        }  
        if (concepto1 == "") {
            msj += "Ingrese el concepto <br>";
        }  


        if (msj != "") {
            alertas('error', 'Error!..', msj)
        } else {
          var confirmar = confirm('Desea Realizar el Pago de Sueldos');
          if(confirmar){
            $.ajax({
                type: 'post',
                url: "{{ route('nuevo_rol.pago_de_roles') }}",
               
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#form_pagos_roles").serialize(),
                success: function(data) {
                    alertas(data.respuesta, data.titulos, data.msj);
                    if(data.respuesta == 'success'){
                        setTimeout(function(){
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
          }
        }
    }

    function guardar_pago_planilla(e) {
        e.preventDefault();
        let fecha_asiento = document.getElementById('fecha_planilla').value;
        let tipo_pago = document.getElementById('id_tipo_pago_planilla').value;
        let cuenta_saliente = document.getElementById('id_cuenta_planilla').value;
        let fecha_cheque = document.getElementById('fecha_cheque_planilla').value;
        let numero_cheque = document.getElementById('numero_cheque_planilla').value;
        let numero_cuenta = document.getElementById('numero_cuenta_planilla').value;
        let banco = document.getElementById('id_banco_planilla').value;
        let concepto2 = document.getElementById('concepto2').value;
        let beneficiario2 = document.getElementById('beneficiario2').value;

        let msj = "";
        if (fecha_asiento == "") {
            msj += "Seleccione la Fecha de Creacion <br>";
        }
        if (tipo_pago == "") {
            msj += "Seleccione el Tipo de Pago <br>";
        }
        if (cuenta_saliente == "") {
            msj += "Seleccione la Cuenta Saliente <br>";
        }
        if(tipo_pago == '3'){ //CHEQUE
            if (fecha_cheque == "") {
                msj += "Seleccione la Fecha del Cheque <br>";
            }
            if (numero_cheque == "") {
                msj += "Escriba el Numero de Cheque <br>";
            }
            if (beneficiario2 == "") {
                msj += "Escriba el Beneficiario <br>";
            }
        }

        if(tipo_pago == '1'){ //ACREDITACION 
            if (numero_cuenta == "") {
                msj += "Escriba el Numero de Cuenta <br>";
            }
            if (banco == "") {
                msj += "Seleccione el banco <br>";
            }
        }   
        
        if (concepto2 == "") {
            msj += "Ingrese el concepto <br>";
        }  


        if (msj != "") {
            alertas('error', 'Error!..', msj)
        } else {
          var confirmar = confirm('Desea Realizar el Pago la Planilla');
          if(confirmar){
            $.ajax({
                type: 'post',
                url: "{{ route('nuevo_rol.pago_aportes_patronales') }}",
               
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: $("#form_pagos_planillas").serialize(),
                success: function(data) {
                    alertas(data.respuesta, data.titulos, data.msj);
                    if(data.respuesta == 'success'){
                        setTimeout(function(){
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
          }
        }
    }

    function validar_pago(){


        let fecha_asiento = document.getElementById('fecha_planilla').value;
        let tipo_pago = document.getElementById('id_tipo_pago_planilla').value;
        let cuenta_saliente = document.getElementById('id_cuenta_planilla').value;
        let fecha_cheque = document.getElementById('fecha_cheque_planilla').value;
        let numero_cheque = document.getElementById('numero_cheque_planilla').value;
        let numero_cuenta = document.getElementById('numero_cuenta_planilla').value;
        let banco = document.getElementById('id_banco_planilla').value;
        let concepto2 = document.getElementById('concepto2').value;
        let beneficiario2 = document.getElementById('beneficiario2').value;
        let documento = "";
        if(tipo_pago == 1){
          documento = "Nota Debito";
        }
        else {
          documento = "Comprobante de Egresos Varios";
        }
        let msj = "";
        if (fecha_asiento == "") {
            msj += "Seleccione la Fecha de Creacion <br>";
        }
        if (tipo_pago == "") {
            msj += "Seleccione el Tipo de Pago <br>";
        }
        if (cuenta_saliente == "") {
            msj += "Seleccione la Cuenta Saliente <br>";
        }
        if(tipo_pago == '3'){ //CHEQUE
            if (fecha_cheque == "") {
                msj += "Seleccione la Fecha del Cheque <br>";
            }
            if (numero_cheque == "") {
                msj += "Escriba el Numero de Cheque <br>";
            }
        }

        if(tipo_pago == '1'){ //ACREDITACION 
            if (numero_cuenta == "") {
                msj += "Escriba el Numero de Cuenta <br>";
            }
            if (banco == "") {
                msj += "Seleccione el banco <br>";
            }
        }   
        
        if (concepto2 == "") {
            msj += "Ingrese el concepto <br>";
        }  

        return [msj, documento];

    }

    function cargar_pago(e) {
        e.preventDefault();
        let msj = "";
        if (msj != "") {
            alertas('error', 'Error!..', msj)
        } else {
          var confirmar = confirm('Esta seguro que desea cargar el pago');
          if(confirmar){
            $.ajax({
                type: 'post',
                url: "{{ route('nuevo_rol.cargar_pagos') }}",
               
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                dataType: 'json',
                data: $("#form_pagos_planillas").serialize(),
                success: function(data) {
                    //alertas(data.respuesta, data.titulos, data.msj);
                    if(data.respuesta == 'success'){
                        // $('#body').empty();
                        // $('#body').append(data.data);
                        // $('#example3').show();
                        // $('#boton_cargar').hide();
                        // $('#boton_guardar_pago').show();
                        // $('#divSearch').show();
                        // $('#divSearch').css('display', 'flex');
                        location.reload();
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
          }
        }
    }

    function anularRubro(id) {
        // Enviar los datos al servidor mediante AJAX
        $.ajax({
            type: 'GET',
            url: "{{ url('nuevo_rol/rubro_pago/anular') }}/"+id,
            dataType: 'json',
            success: function(response) {
                if(response.status == 'success'){
                  carga_pantalla_rubros();
                } else {
                    alert('Error al anular los valores');
                    swal({
                        title: "Error!",
                        type: "error",
                        html: response.msj
                      }); 
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
                console.log('Se produjo un error al intentar anular elrubri');
            }
        });
    }

    function carga_pantalla_rubros(){

        $.ajax({
            type: 'GET',
            url: "{{ url('nuevo_rol/nueva_pantalla/pagos_rubros') }}/"+"{{$cuentas_rol->id}}",
            dataType: 'html',
            success: function(response) {
            
              $('#tabla_detalles').empty();
              $('#tabla_detalles').append(response);  
              
            },
            error: function(xhr, status, error) {
                console.log(error);
                console.log('Se produjo un error al intentar anular el rubro');
            }
        });


    }

    function obtener_seleccion() {

        var id_tipo = $("#tipo_pago").val();
        if (id_tipo == 1) { //ACREDITACION

            document.getElementById("id_cuenta_saliente").style.display = 'block';
            document.getElementById("id_banco").style.display = 'block';
            document.getElementById("num_cuenta").style.display = 'block';
            document.getElementById("num_che").style.display = 'none';
            document.getElementById("fech_cheq").style.display = 'none';

            $('#banco').val("");
            $('#numero_cuenta').val("");
            $('#numero_cheque').val("");
            $('#fecha_cheque').val("");

        } else if (id_tipo == 2) { //EFECTIVO

            document.getElementById("id_banco").style.display = 'none';
            document.getElementById("num_cuenta").style.display = 'none';
            document.getElementById("num_che").style.display = 'none';
            document.getElementById("fech_cheq").style.display = 'none';

            $('#numero_cheque').val("");
            $('#numero_cuenta').val("");
            $('#banco').val("");
            $('#cuenta_saliente').val("");

        } else if (id_tipo == 3) { //CHEQUE

            document.getElementById("id_banco").style.display = 'none';
            document.getElementById("num_cuenta").style.display = 'none';
            document.getElementById("num_che").style.display = 'block';
            document.getElementById("fech_cheq").style.display = 'block';

            $('#numero_cheque').val("");
            $('#numero_cuenta').val("");
            $('#banco').val("");
            $('#cuenta_saliente').val("");
            $('#fecha_cheque').val("");

        }

    }

    function obtener_seleccion_planilla() {

        var id_tipo = $("#id_tipo_pago_planilla").val();
        if (id_tipo == 1) { //ACREDITACION

            document.getElementById("pid_cuenta_saliente").style.display = 'block';
            document.getElementById("pid_banco").style.display = 'block';
            document.getElementById("pnum_cuenta").style.display = 'block';
            document.getElementById("pnum_che").style.display = 'none';
            document.getElementById("pfech_cheq").style.display = 'none';

            $('#id_banco_planilla').val("");
            $('#numero_cuenta_planilla').val("");
            $('#numero_cheque_planilla').val("");
            $('#fecha_cheque_planilla').val("");

        } else if (id_tipo == 2) { //EFECTIVO

            document.getElementById("pid_banco").style.display = 'none';
            document.getElementById("pnum_cuenta").style.display = 'none';
            document.getElementById("pnum_che").style.display = 'none';
            document.getElementById("pfech_cheq").style.display = 'none';

            $('#numero_cheque_planilla').val("");
            $('#numero_cuenta_planilla').val("");
            $('#id_banco_planilla').val("");
            $('#id_cuenta_planilla').val("");

        } else if (id_tipo == 3) { //CHEQUE

            document.getElementById("pid_banco").style.display = 'none';
            document.getElementById("pnum_cuenta").style.display = 'none';
            document.getElementById("pnum_che").style.display = 'block';
            document.getElementById("pfech_cheq").style.display = 'block';

            $('#numero_cheque_planilla').val("");
            $('#numero_cuenta_planilla').val("");
            $('#id_banco_planilla').val("");
            $('#id_cuenta_planilla').val("");
            $('#fecha_cheque_planilla').val("");

        }

    }

    

    function sel_todos(){
        // Get the checkbox
        var checkBox = document.getElementById("ctodos");

        // If the checkbox is checked, display the output text
        if (checkBox.checked == true){
            $(':checkbox').prop('checked', true);    
        } else {
            $(':checkbox').prop('checked', false);    
        }
    }

    /*function detalle_beneficios(tipo){

      $.ajax({
          type: 'post',
          url: "{{ route('contable.nuevo_rol.detalle_beneficios') }}",
          
          headers: {
              'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: {'tipo': tipo, 'id_rol_asiento': '{{$cuentas_rol->id}}'},
          success: function(data) {
              alertas(data.respuesta, data.titulos, data.msj);
              if(data.respuesta == 'success'){
                  setTimeout(function(){
                      location.reload();
                  }, 1000);
              }
          },
          error: function(data) {
              console.log(data);
          }
      });  

    }*/

    function cambiar_jornada(id, input){
      console.log(id);
      console.log(input.value);  
      $.ajax({
          type: 'post',
          url: "{{ route('contable.nuevo_rol.actualizar_jornada') }}",
          
          headers: {
              'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: {'id': id, 'value': input.value},
          success: function(data) {
              
              
          },
          error: function(data) {
              console.log(data);
          }
      }); 
    }

    function cambiar_pasante(id, input){
      console.log(id,input);
      if ( $('#pas'+id).is(':checked')) {
        cambiar_pasante2(id, '1');    
      } else {
        cambiar_pasante2(id, '0');    
      }

    }

    
    function cambiar_pasante2(id, val){
     
      console.log(val);
      $.ajax({
          type: 'post',
          url: "{{ route('contable.nuevo_rol.actualizar_pasante') }}",
          
          headers: {
              'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: {'id': id, 'value': val},
          success: function(data) {
              
              
          },
          error: function(data) {
              console.log(data);
          }
      });   
    }



    function recalcular_beneficios(){
      $.ajax({
          type: 'get',
          url: "{{ url('nrol_recalcular/decimo_cuarto') }}/{{ $cuentas_rol->anio }}/{{ $cuentas_rol->mes }}",
          
          headers: {
              'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
         
          success: function(data) {
            
            location.reload();
              
          },
          error: function(data) {
              console.log(data);
          }
      });  
    }

    
    function soloNumeros(e) {
            // capturamos la tecla pulsada
            var teclaPulsada = window.event ? window.event.keyCode : e.which;
            // capturamos el contenido del input
            var valor = e.value;
            if (teclaPulsada == 45 && valor.indexOf("-") == -1) {
                document.getElementById("inputNumero").value = "-" + valor;
            }
            // 13 = tecla enter
            // 46 = tecla punto (.)
            // Si el usuario pulsa la tecla enter o el punto y no hay ningun otro
            // punto
            if (teclaPulsada == 13 || (teclaPulsada == 46 && valor.indexOf(".") == -1)) {
                return true;
            }
            // devolvemos true o false dependiendo de si es numerico o no
            return /\d/.test(String.fromCharCode(teclaPulsada));
    }

    function guardar(id) {
        // Obtener los valores de los inputs
        alert
        var debe = document.getElementById('debe_' + id).value;
        var haber = document.getElementById('haber_' + id).value;

        // Preparar los datos a enviar
        var datos = {
            id: id,
            debe: debe,
            haber: haber,
            _token: $('input[name=_token]').val() // Si usas Laravel, necesitas el token CSRF
        };

        // Enviar los datos al servidor mediante AJAX
        $.ajax({
            type: 'POST',
            url: "{{ url('nuevo_rol/rubro_pago/update_valor_detalle') }}",
            data: datos,
            dataType: 'json',
            success: function(response) {
                if(response.status == 'success'){
                  carga_pantalla_rubros();
                } else {
                    alert('Error al guardar los valores');
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
                alert('Se produjo un error al intentar guardar los valores');
            }
        });
    }
    function actualiza_valor(id, element, edit){
      console.log(id, element, edit);
      $.ajax({
          type: 'post',
          url: "{{ route('nuevo_rol.actualiza_detalle_cuentas') }}",
          
          headers: {
              'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: {'id': id, 'value': element.value, 'edit': edit},
          success: function(data) {
            var xdebe = data.debe.toFixed(4);
            var xhaber = data.haber.toFixed(4);
            var xdif = xdebe - xhaber;
            xdif = xdif.toFixed(4);
            $('#idt_debe').text(xdebe);
            $('#idt_haber').text(xhaber);
            $('#idt_dif').text(xdif);    
              
          },
          error: function(data) {
              console.log(data);
          }
      });   
    }
    
    function agregarRubro(id_rol_asiento) {
        let id_rol_aiento = $('#rol_asientos').val();
        let id_cuenta = $('#cuenta').val();
        if (id_cuenta == "") {
            Swal.fire(`Alerta`, "Seleccione una cuenta por favor","warning");
        }
        else{
          $.ajax({
            type: 'post',
            url: "{{ route('nuevo_rol.agregar_rubro') }}",
            
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            dataType: 'json',
            data: {
              'id_rol_aiento': id_rol_aiento,
              'id_cuenta': id_cuenta,
            },
            success: function(data) {
                if(data.status == 'success'){
                    carga_pantalla_rubros();
                    $('#cuenta').val('').trigger('change');
                } else {
                    swal({
                      title: "Advertencia!",
                      type: "warning",
                      html: data.msj
                    }); 
                }
            },
            error: function(data) {
                console.log(data);
            }
          });
        }
    }

    function guardar_pago_rubros(e) {
        e.preventDefault();

        let arreglo_msn = validar_pago();
        if(arreglo_msn[0] != ''){
          swal({
            title: "Advertencia!",
            type: "warning",
            html: arreglo_msn[0],
          });   
        }else{

          var confirmar = confirm('Desea Realizar el Pago la Planilla');
          if(confirmar){
            let id_rol_aiento = $('#rol_asientos').val();
            let id_tipo_pago_planilla = $("#id_tipo_pago_planilla").val();
            let concepto2 = document.getElementById('concepto2').value;
            let beneficiario2 = document.getElementById('beneficiario2').value;
            $.ajax({
                type: 'post',
                url: "{{ route('nuevo_rol.guardar_pago_rubros') }}",
                
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                /*data: {
                  'id_rol_asiento': id_rol_aiento,
                  'id_tipo_pago_planilla': id_tipo_pago_planilla,
                  'concepto2': concepto2,
                  'beneficiario2': beneficiario2,

                },*/
                data: $('#form_pagos_planillas').serialize(),
                
                success: function(data) {
                    alertas(data.status, data.titulos, data.msj);
                    if(data.status == 'success'){
                      // $('#boton_guardar_pago').hide();
                      // $('#body').empty();
                      // $('#body').append(data.data);
                      // $('#btnVerAsiento').show();
                      location.reload();
                    }
                    else{
                      swal({
                        title: "Advertencia!",
                        type: "warning",
                        html: data.msj
                      });
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
          }

        }
        console.log(arreglo_msn);

        
    }


  </script>

@endsection