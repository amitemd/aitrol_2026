
<div class="modal-header">
    <div class="row">
        <div class="col-md-4">
            <b>Detalle de Beneficios</b>
        </div>
        <div class="col-md-4">
            VALOR SALARIO BASICO: @if(!is_null($val_sal_basico))$ {{  number_format($val_sal_basico->valor, 2, ',', ' ') }} @endif
        </div>
        <div class="col-md-4">
            <button type="button" class="btn btn-warning" onclick="recalcular_beneficios()">Recalcular</button>
        </div>
    </div>    
    
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body" id="detalle_rol" name="detalle_rol">
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
        <div class="row">
            <div class="table-responsive col-md-12">
                <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%; font-size: 12px;">
                    <thead>
                        <tr class='well-dark'>
                          <th >Cédula</th>
                          <th >Empleado</th>
                          <th >Pasante</th>
                          <th >Jornada</th>
                          <th >Base Iess</th>
                          <th >Décimo Tercer</th>
                          <th >Décimo Cuarto</th>
                          <th >Vacaciones</th>
                          <!--th >Accion</th-->
                        </tr>
                      </thead>
                      <tbody>
                      @php $acum_13 = 0; $acum_14 = 0; $acum_vaca = 0;@endphp
                        @foreach ($roles as $rol)
                        
                          <tr>
                            <td>{{ $rol->ct_nomina->id_user }}</td>
                            <td>{{ $rol->ct_nomina->user->apellido1 }} {{ $rol->ct_nomina->user->apellido2 }}<br>{{ $rol->ct_nomina->user->nombre1 }} {{ $rol->ct_nomina->user->nombre2 }}</td>
                            <td style="text-align: center;">
                                <input type="checkbox" class="onoffswitch-checkbox" id="pas{{$rol->id}}" name="pas{{$rol->id}}" onclick="cambiar_pasante('{{$rol->id}}',this)" >
                            </td>
                            <td>
                                <select name="jor{{$rol->id}}" id="jor{{$rol->id}}" class="form-control input-sm" onchange="cambiar_jornada('{{$rol->id}}',this)" >
                                    <option  value="0" @if($rol->jornada==0) selected @endif>Completa</option>
                                    <option  value="1" @if($rol->jornada==1) selected @endif>Media</option>
                                </select> 
                            </td>@php $detalle = null; $detalle = $rol->detalle->first(); @endphp
                            <td style="text-align: right;">@if(!is_null($detalle)) $ {{ number_format($detalle->base_iess, 2, ',', ' ') }} @else 0.00 @endif</td>
                            <td style="text-align: right;">@if(!is_null($detalle)) $ {{ number_format($detalle->decimo_tercero_acumulado, 2, ',', ' ') }} @else 0.00 @endif</td>
                            <td style="text-align: right;">@if(!is_null($detalle)) $ {{ number_format($detalle->decimo_cuarto_acumulado, 2, ',', ' ') }} @else 0.00 @endif</td>
                            <td style="text-align: right;">@if(!is_null($detalle)) $ {{ number_format($detalle->valor_vacacion, 2, ',', ' ') }} @else 0.00 @endif</td>
                            @php $acum_13 += $detalle->decimo_tercero_acumulado; $acum_14 += $detalle->decimo_cuarto_acumulado; $acum_vaca += $detalle->valor_vacacion; @endphp
                            <!--td></td-->
                          </tr>
                        <script type="text/javascript">@if($rol->pasante == 1) $('#pas{{$rol->id}}').prop('checked', true); @endif</script>  
                        @endforeach
                        <tr>
                            <td>Total</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>$ {{ number_format($acum_13, 2, ',', ' ') }}</td>
                            <td>$ {{ number_format($acum_14, 2, ',', ' ') }}</td>
                            <td>$ {{ number_format($acum_vaca, 2, ',', ' ') }}</td>
                          </tr>
                      </tbody>
                      <tfoot>
                      </tfoot>

                    </table>
                  </div>
                </div>
                <div class="row">
                  
                </div>
              </div>
</div>
<div class="modal-footer">
    <!--button type="button" class="btn btn-primary">Save changes</button-->
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
        