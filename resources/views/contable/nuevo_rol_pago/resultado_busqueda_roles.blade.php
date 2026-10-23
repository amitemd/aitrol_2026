<style type="text/css">
  .valor{
    text-align: right;
  }
</style>

<div class="row head-title">
  <div class="col-md-12 cabecera">
    <label class="color_texto" >LISTADO ROL PAGO</label>
  </div>
</div>
<div class="box-body dobra">
  <div class="form-group col-md-12">
    <div class="form-row">
      <div id="contenedor">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
          <div class="row">
            <div class="table-responsive col-md-12">
              <table id="example2" class="table table-bordered table-hover">
                <thead>
                  <tr class='well-dark'>
                    <th >Sucursales</th> <!--0--> 
                    <th >Cedula</th>
                    <th >Nombres</th>
                    <th >Fecha Ingreso</th>
                    <th >FR</th>
                    <th >Fondo <br> Reserva</th>
                    <th >Decimo <br>Tercero</th>
                    <th >Decimo <br>Cuarto</th>
                    <th >Dias <br>Laborados</th>
                    <th >Horas <br> Extras 50%</th>
                    <th >Horas <br> Extras 100%</th>
                    <th >Sueldo <br> Mensual</th><!--9-->
                    <th >Horas <br> Extras 50%</th>
                    <th >Horas <br> Extras 100%</th>
                    <th >Bono</th>
                    <th >Bono <br> Imputable</th>
                    <th >Alimentación</th>
                    <th >Movilizacion</th>
                    <th >Parqueo</th>
                    <th >Fondo<br> Reserva</th>
                    <th >Decimo <br>Tercero</th>
                    <th >Decimo <br>Cuarto</th>
                    <th >Total <br> Ingreso</th><!--20--> 
                    <th >9.45%<br> Iess</th>
                    <th >Multa</th>
                    <th >Fondo Reserva <br>x Cobrar</th>
                    <th >Otros <br>Egresos</th>
                    <th >Seguro <br>Médico</th>
                    <th >Examen <br>Laboratorio</th>
                    <th >Impuesto <br>Renta</th>
                    <th >Prestamo <br>Empresa</th>
                    <th >Saldo <br>Inicial</th>
                    <th >Anticipo <br>1RA 15na</th>
                    <th >Otro <br>Anticipo</th>
                    <th >Prestamo <br>Hipotecario</th>
                    <th >Prestamo <br>Quirografario</th>
                    <th >Extension <br>Conyuge</th>
                    <th >Total <br>Egreso</th><!--35-->
		                <th >Neto <br>Recibido</th>
                    <th >Pasante </th>
                    <th >Jornada </th>
                    <th >Base <br> Iess </th>
                    <th >Fondo <br> Reserva <br> Acumulado</th>
                    <th >Fondo <br> Reserva <br> Total</th>
                    <th >Decimo <br> Tercero <br> Acumulado</th>
                    <th >Decimo <br> Cuarto <br> Acumulado</th>
                    <th >Vacaciones</th>
                    <th >Acción</th>
                  </tr>
                </thead>
                <tbody>
                  @php 
                    $total_sueldo = 0;$total_cantidad_50 = 0;   
                    $total_cantidad_100 = 0;$total_horas_50  = 0;   
                    $total_horas_100 = 0; $total_bono = 0; 
                    $total_bonoimp = 0; $total_exlaboratorio = 0;
                    $total_alimentacion = 0; 
                    $total_transporte = 0;
                    $total_parqueo = 0;
                    $total_fond_reserva = 0;
                    $total_decimo_tercero = 0; $total_decimo_cuarto = 0;
                    $total_ingreso = 0; $total_iess = 0;
                    $total_multa = 0; $total_seguro_privado = 0;$total_reserva_cobrar = 0;$total_otro_egreso = 0;$total_imp_renta = 0; $total_prestamos = 0;$total_sald_ini= 0;
                    $total_anticipos = 0;
                    $total_otro_anticipos = 0;$total_egresos = 0;$total_cuot_quir = 0;$total_cuot_hipot = 0;
                    $total_neto_recibido = 0;
                    $total_extension = 0;
                    $id_new = 0 ;
                    $tbaseIess = 0 ;
                    $tfondoAcum = 0;
                    $tdtercerAcum = 0;
                    $tdcuartoAcum = 0;
                    $tvacaciones = 0; 
                    $fdr_total = 0;
                  @endphp
                  @foreach ($rol_det_consulta as $value)
                    @php
                      $total_sueldo += $value->sueldo;
                      $total_cantidad_50 += $value->cantidad_horas_50;
                      $total_horas_50 += $value->valor_horas_50;
                      $total_cantidad_100 += $value->cantidad_horas_100;
                      $total_horas_100 += $value->valor_horas_100;
                      $total_bono += $value->bonificacion;
                      $total_transporte += $value->transporte;
                      $total_bonoimp += $value->bono_imputable;
                      $total_alimentacion += $value->alimentacion;
                      $total_parqueo += $value->parqueo;
                      $total_fond_reserva += $value->fondo_reserva;
                      $total_decimo_tercero += $value->decimo_tercero;
                      $total_decimo_cuarto += $value->decimo_cuarto;
                      $total_ingreso += $value->total_ingreso;
                      $total_iess += $value->porcentaje_iess;
                      $total_multa += $value->multa;
                      $total_reserva_cobrar += $value->fond_reserv_cobr;
                      $total_otro_egreso += $value->otro_egres;
                      $total_seguro_privado += $value->seguro_privado;
                      $total_exlaboratorio += $value->exam_laboratorio;
                      $total_imp_renta += $value->impuesto_renta;
                      $total_prestamos += $value->prestamo_empleado;
                      $total_sald_ini += $value->saldo_inicial;
                      $total_anticipos += $value->anticipo_quincena;
                      $total_otro_anticipos += $value->otro_anticipo;
                      $total_egresos += $value->total_egreso;
                      $total_cuot_quir += $value->total_quir;
                      $total_cuot_hipot += $value->total_hip;
                      $total_neto_recibido += $value->neto_recibido;
                      $user = Sis_medico\User::find($value->usuario);
                      $dat_nomina = Sis_medico\Ct_Nomina::find($value->id_nomina);
                      $sucursal = Sis_medico\Ct_Sucursales::find($value->id_sucursal);
                      $valor_base_iess = 0 ;
                      if($dat_nomina->extension_conyuge === 1 ){
                          $ct_rh_valores = Sis_medico\Ct_Rh_Valores::where("id_empresa",$dat_nomina->id_empresa)->where('tipo',5)->first();
                          $valor_base_iess = ($value->base_iess * $ct_rh_valores->valor) / 100;  
                      }
                      $total_extension += $valor_base_iess;
                      $tbaseIess += $value->base_iess ;
                      $tfondoAcum += $value->fondo_reserva_acumulado;
                      $tdtercerAcum += $value->decimo_tercero_acumulado;
                      $tdcuartoAcum += $value->decimo_cuarto_acumulado;
                      $tvacaciones += $value->valor_vacacion;

                      $fecha = date("Y-m-d");
                      $fec   = new DateTime($fecha);
                      $vt_fecha_ingreso = $value->fecha_ingreso;
                      if($value->fecha_anterior != null){ 
                        $vt_fecha_ingreso = $value->fecha_anterior;
                      }
                      $fec2  = new DateTime($vt_fecha_ingreso);
                      $diff  = $fec->diff($fec2);
                      $days  = $diff->days;
                      $intervalMeses = $diff->format("%m");      
                      $intervalAnos  = $diff->format("%y")*12;
                      $intervalDias  = $diff->format("%d");
                      $meses_totales = $intervalMeses + $intervalAnos;
                      if($meses_totales >= 12 ){
                        $txt = 'X';
                      }else{
                        $txt = ' ';  
                      }
                      
                    @endphp
                    <tr class="well">
                      <td class="">@if(!is_null($sucursal)){{$sucursal->nombre_sucursal}}@endif</td>
                      <td class="">@if(!is_null($value->usuario)) {{$value->usuario}} @endif</td>
                      <td class="">{{$user->apellido1}} @if($user->apellido2!='(N/A)'){{$user->apellido2}}@endif {{$user->nombre1}} @if($user->nombre2!='(N/A)'){{$user->nombre2}}@endif</td>
                      <td class="">@if($value->fecha_anterior != null) {{$value->fecha_anterior}} @else {{$value->fecha_ingreso}} @endif</td>
                      <td class="">@if($txt == 'X') <i class="fa fa-solid fa-check" style="font-size: 24px;color: green;"></i> @else <span style="font-size: 20px;color: red;">X</span> @endif</td>
                      <td class="">
                          @if($dat_nomina->pago_fondo_reserva == '1') Acumula
                          @elseif($dat_nomina->pago_fondo_reserva == '2') Mensualiza
                          @endif
                      </td>
                      <td class="">
                          @if($dat_nomina->decimo_tercero == '1') Acumula
                          @elseif($dat_nomina->decimo_tercero == '2') Mensualiza
                          @endif
                      </td>
                      <td class="">
                          @if($dat_nomina->decimo_cuarto == '1') Acumula
                          @elseif($dat_nomina->decimo_cuarto == '2') Mensualiza
                          @endif
                      </td>
                      <td class="">@if(!is_null($value->dias_laborados)) {{$value->dias_laborados}} @endif</td>
                      <td class="">@if(!is_null($value->cantidad_horas_50)){{$value->cantidad_horas_50}}@endif</td>
                     
                      <td class="">@if(!is_null($value->cantidad_horas_100)){{$value->cantidad_horas_100}}@endif</td>
                      <td class="valor">@if(!is_null($value->sueldo)){{number_format($value->sueldo,2)}} @else 0.00 @endif</td>
                      
                      <td class="valor">@if(!is_null($value->valor_horas_50)){{number_format($value->valor_horas_50,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->valor_horas_100)){{number_format($value->valor_horas_100,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->bonificacion)){{number_format($value->bonificacion,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->bono_imputable)){{number_format($value->bono_imputable,2)}} @else 0.00 @endif</td>
                      
                      <td class="valor">@if(!is_null($value->alimentacion)){{number_format($value->alimentacion,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->transporte)){{number_format($value->transporte,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->parqueo)){{number_format($value->parqueo,2)}} @else 0.00 @endif</td>
                      
                      <td class="valor">@if(!is_null($value->fondo_reserva)){{number_format($value->fondo_reserva,2)}} @else 0.00 @endif</td>
                      
                      <td class="valor">@if(!is_null($value->decimo_tercero)){{number_format($value->decimo_tercero,2)}} @else 0.00 @endif</td>
                      
                      <td class="valor">@if(!is_null($value->decimo_cuarto)){{number_format($value->decimo_cuarto,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->total_ingreso)){{number_format($value->total_ingreso,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->porcentaje_iess)){{number_format($value->porcentaje_iess,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->multa)){{number_format($value->multa,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->fond_reserv_cobr)){{number_format($value->fond_reserv_cobr,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->otro_egres)){{number_format($value->otro_egres,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->seguro_privado)){{number_format($value->seguro_privado,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->exam_laboratorio)){{number_format($value->exam_laboratorio,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->impuesto_renta)){{number_format($value->impuesto_renta,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->prestamo_empleado)){{number_format($value->prestamo_empleado,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->saldo_inicial)){{number_format($value->saldo_inicial,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->anticipo_quincena)){{number_format($value->anticipo_quincena,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->otro_anticipo)){{number_format($value->otro_anticipo,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->total_hip)){{number_format($value->total_hip,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->total_quir)){{number_format($value->total_quir,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($valor_base_iess)) {{number_format($valor_base_iess,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->total_egreso)){{number_format($value->total_egreso,2)}} @else 0.00 @endif</td>
                      <td class="valor">@if(!is_null($value->neto_recibido)){{number_format($value->neto_recibido,2)}} @else 0.00 @endif</td>
                      <td class="">@if($value->pasante == 1) PASANTE @endif</td>
                      <td class="">@if($value->jornada == 0) Completa @else Media @endif</td>
                      <td class="valor">{{number_format($value->base_iess,2)}} </td>
                      <td class="valor">{{number_format($value->fondo_reserva_acumulado,2)}} </td>
                      @php $vt1_fdr = $value->fondo_reserva + $value->fondo_reserva_acumulado; @endphp
                      <td class="valor">{{number_format($vt1_fdr,2)}} </td>
                      <td class="valor">{{number_format($value->decimo_tercero_acumulado,2)}} </td>
                      <td class="valor">{{number_format($value->decimo_cuarto_acumulado,2)}} </td>
                      <td class="valor">{{number_format($value->valor_vacacion,2)}} </td>
                      <td class="">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        @if($value->estado_rol == '1')
                          <!--a href="{{route('rol_pago.editar', ['id' => $value->id_rol])}}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a-->
                          <a href="{{route('rol_pago.imprimir', ['id' => $value->id_rol])}}" target="_blank" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-download-alt"></span></a>
                        @endif
                      </td>
                     
                    </tr>
                    @php
                      $id_new ++;
                    @endphp
                  @endforeach
                  <tr>
                    <td style="font-size: 12px;"><p style="font-weight: bold;">TOTALES &nbsp;</p></td>  
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="valor" style="font-size: 12px;@if($total_sueldo < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_sueldo ,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_horas_50 < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_horas_50,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_horas_100 < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_horas_100,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_bono < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_bono,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_bonoimp < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_bonoimp,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_alimentacion < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_alimentacion,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_transporte < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_transporte,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_parqueo < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_parqueo,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_fond_reserva < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_fond_reserva,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_decimo_tercero < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_decimo_tercero,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_decimo_cuarto < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_decimo_cuarto,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_ingreso < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_ingreso,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_iess < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_iess,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_multa < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_multa,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_reserva_cobrar < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_reserva_cobrar,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_otro_egreso < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_otro_egreso,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_seguro_privado < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_seguro_privado,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_seguro_privado < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_exlaboratorio,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_imp_renta < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_imp_renta,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_prestamos < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_prestamos,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_sald_ini < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_sald_ini,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_anticipos < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_anticipos,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_otro_anticipos < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_otro_anticipos,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_cuot_hipot < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_cuot_hipot,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_cuot_quir < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_cuot_quir,2)}}</p></td>
		                <td class="valor" style="font-size: 12px;@if($total_extension < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_extension,2)}} </td>
                    <td class="valor" style="font-size: 12px;@if($total_egresos < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_egresos,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;@if($total_neto_recibido < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_neto_recibido,2)}}</p></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    @php 
                      $fdr_total = $total_fond_reserva +  $tfondoAcum;
                    @endphp
                    <td class="valor" style="font-size: 12px;" ><p style="font-weight: bold;">{{number_format($tbaseIess,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;" ><p style="font-weight: bold;">{{number_format($tfondoAcum,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;" ><p style="font-weight: bold;">{{number_format($fdr_total,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;" ><p style="font-weight: bold;">{{number_format($tdtercerAcum,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;" ><p style="font-weight: bold;">{{number_format($tdcuartoAcum,2)}}</p></td>
                    <td class="valor" style="font-size: 12px;" ><p style="font-weight: bold;">{{number_format($tvacaciones,2)}}</p></td>
                    <td>&nbsp;</td>
                  </tr>
                </tbody>
              </table>
              <label style="padding-left: 15px;font-size: 15px">Total de Registros: {{$rol_det_consulta->count()}}</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>





<script type="text/javascript">
 

   

</script>