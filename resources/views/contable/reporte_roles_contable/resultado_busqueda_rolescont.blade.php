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
                    <th width="50%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Nombres</th>
                    <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha de Ingreso</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Dias Laborados</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Sueldo Mensual</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Base IESS</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Bono</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Alimentación</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Movilizacion</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Prop Fondo Reserva</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor Decimo IV</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total Ingreso</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Aportes 9.45% Iess</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Seguro Privado</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Impuesto Renta</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Multa</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Prestamo Empresa</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Saldo Prestamo</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Otro Egreso</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Anticipo Quincena</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Otro Anticipo</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Prestamo Hipotecario</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Prestamo Quirografario</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total Egreso</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Neto Recibido</th>
                    <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="5" colspan="5" aria-label="Codigo: activate to sort column ascending">Acción</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    $total_sueldo = 0;
                    $total_base_iess = 0;
                    $total_bono = 0;
                    $total_alimentacion = 0;
                    $total_transporte = 0;
                    $total_fond_reserva = 0;
                    $total_decimo_tercero = 0;
                    $total_decimo_cuarto = 0;
                    $total_ingreso = 0;
                    $total_iess = 0;
                    $total_seguro_privado = 0;
                    $total_imp_renta = 0;
                    $total_multa = 0;
                    $total_prestamos = 0;
                    $total_otro_egre = 0;
                    $total_exlaboratorio = 0;
                    $total_sald_ini= 0;
                    $total_anticipos = 0;
                    $total_otro_anticipos = 0;
                    $total_cuot_hipot = 0;
                    $total_cuot_quir = 0;
                    $total_egresos = 0;
                    $total_neto_recibido = 0;
                  @endphp
                  @foreach ($rol_det_consulta as $value)
                    @php
                      $total_sueldo += $value->sueldo;
                      $total_base_iess += $value->base_iess;
                      $total_bono += $value->bonificacion;
                      $total_alimentacion += $value->alimentacion;
                      $total_transporte += $value->transporte;
                      $total_fond_reserva += $value->fondo_reserva;
                      $total_decimo_tercero += $value->decimo_tercero;
                      $total_decimo_cuarto += $value->decimo_cuarto;
                      $total_ingreso += $value->total_ingreso;
                      $total_iess += $value->porcentaje_iess;
                      $total_seguro_privado += $value->seguro_privado;
                      $total_imp_renta += $value->impuesto_renta;
                      $total_multa += $value->multa;
                      $total_otro_egre += $value->otro_egres;
                      $total_prestamos += $value->prestamo_empleado;
                      $total_exlaboratorio += $value->exam_laboratorio;
                      $total_sald_ini += $value->saldo_inicial;
                      $total_anticipos += $value->anticipo_quincena;
                      $total_otro_anticipos += $value->otro_anticipo;
                      $total_cuot_hipot += $value->total_hip;
                      $total_cuot_quir += $value->total_quir;
                      $total_egresos += $value->total_egreso;
                      $total_neto_recibido += $value->neto_recibido;

                      $user = Sis_medico\User::find($value->usuario);
                      $dat_nomina = Sis_medico\Ct_Nomina::find($value->id_nomina);

                    @endphp
                    <tr class="well">
                      <td>{{$user->apellido1}} @if($user->apellido2!='(N/A)'){{$user->apellido2}}@endif {{$user->nombre1}} @if($user->nombre2!='(N/A)'){{$user->nombre2}}@endif</td>
                      <td>@if(!is_null($dat_nomina->fecha_ant)){{$dat_nomina->fecha_ant}} @else {{$dat_nomina->fecha_ingreso}} @endif</td>
                      <td>@if(!is_null($value->dias_laborados)){{$value->dias_laborados}} @endif</td>
                      <td>@if(!is_null($value->sueldo)){{$value->sueldo}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->base_iess)){{$value->base_iess}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->bonificacion)){{$value->bonificacion}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->alimentacion)){{$value->alimentacion}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->transporte)){{$value->transporte}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->fondo_reserva)){{$value->fondo_reserva}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->decimo_cuarto)){{$value->decimo_cuarto}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->total_ingreso)){{$value->total_ingreso}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->porcentaje_iess)){{$value->porcentaje_iess}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->seguro_privado)){{$value->seguro_privado}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->impuesto_renta)){{$value->impuesto_renta}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->multa)){{$value->multa}} @else 0.00 @endif</td>
                      <td>@if((!is_null($value->prestamo_empleado))||(!is_null($value->exam_laboratorio))){{($value->prestamo_empleado)+($value->exam_laboratorio)}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->saldo_inicial)){{$value->saldo_inicial}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->otro_egres)){{$value->otro_egres}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->anticipo_quincena)){{$value->anticipo_quincena}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->otro_anticipo)){{$value->otro_anticipo}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->total_hip)){{$value->total_hip}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->total_quir)){{$value->total_quir}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->total_egreso)){{$value->total_egreso}} @else 0.00 @endif</td>
                      <td>@if(!is_null($value->neto_recibido)){{$value->neto_recibido}} @else 0.00 @endif</td>
                      <td>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        @if($value->estado_rol == '1')
                          <!-- <a href="{{route('rol_pago.editar', ['id' => $value->id_rol])}}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a> -->
                          <a href="{{route('rol_pago.imprimir', ['id' => $value->id_rol])}}" target="_blank" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-download-alt"></span></a>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                  <tr>
                    <td style="font-size: 10px;"><p style="font-weight: bold;">TOTALES &nbsp;</p></td>
                    <td style="font-size: 10px;"></td>
                    <td style="font-size: 10px;"></td>
                    <td style="font-size: 10px;@if($total_sueldo < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_sueldo ,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_base_iess < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_base_iess,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_bono < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_bono,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_alimentacion < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_alimentacion,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_transporte < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_transporte)}}</p></td>
                    <td style="font-size: 10px;@if($total_fond_reserva < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_fond_reserva,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_decimo_cuarto < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_decimo_cuarto,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_ingreso < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_ingreso,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_iess < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_iess,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_seguro_privado < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_seguro_privado,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_imp_renta < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_imp_renta,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_multa < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_multa,2)}}</p></td>
                    <td style="font-size: 10px" ><p style="font-weight: bold;">{{number_format($total_prestamos+$total_exlaboratorio,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_sald_ini < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_sald_ini,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_otro_egre < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_otro_egre,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_anticipos < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_anticipos,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_otro_anticipos < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_otro_anticipos,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_cuot_hipot < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_cuot_hipot,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_cuot_quir < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_cuot_quir,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_egresos < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_egresos,2)}}</p></td>
                    <td style="font-size: 10px;@if($total_neto_recibido < 0) color:red; @endif" ><p style="font-weight: bold;">{{number_format($total_neto_recibido,2)}}</p></td>
                    <td>&nbsp;</td>
                  </tr>
                </tbody>
                <tfoot>
                </tfoot>
              </table>
              <label style="padding-left: 15px;font-size: 15px">Total de Registros: {{$rol_det_consulta->count()}}</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">

    $('#example2').DataTable({
    'paging'      : false,
    'lengthChange': true,
    'searching'   : false,
    'ordering'    : true,
    'info'        : true,
    'autoWidth'   : true,
    'sInfoEmpty':  true,
    'sInfoFiltered': true,
    'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    }
    });



</script>
