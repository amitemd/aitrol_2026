<div class="row head-title">
  <div class="col-md-12 cabecera">
    <label class="color_texto" >RESULTADO VALOR ANTICIPO</label>
  </div>
</div>
<div class="box-body dobra">
  <div class="form-group col-md-12">
    <div class="form-row">
      <div id="contenedor">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
          <div class="row">
            <div class="table-responsive col-md-12">
              <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr class='well-dark'>
                  <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Identificacion</th>
                    <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Nombres</th>
                    <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Empresa</th>
                    <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Año</th>
                    <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Quincena</th>
                    <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Sueldo Mensual</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">% Porcentaje</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor Anticipo</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($empl_rol as $value)
                    @php
                      $user = Sis_medico\User::find($value->id_user);
                      $obtener_nombre = Sis_medico\Empresa::find($value->id_empresa);
                    @endphp
                    <tr  class="well">
                      <td>@if(!is_null($user->id)){{$user->id}}@endif</td>
                      <td>{{$user->apellido1}} @if($user->apellido2!='(N/A)'){{$user->apellido2}}@endif {{$user->nombre1}} @if($user->nombre2!='(N/A)'){{$user->nombre2}}@endif</td>
                      <td>@if(!is_null($obtener_nombre->nombrecomercial)){{$obtener_nombre->nombrecomercial}}@endif</td>              
                      <td>
                        @if(!is_null($id_anio)){{$id_anio}}@endif
                      </td>
                      <td>
                        @if($id_mes == '1') Enero
                        @elseif($id_mes == '2') Febrero
                        @elseif($id_mes == '3') Marzo
                        @elseif($id_mes == '4') Abril
                        @elseif($id_mes == '5') Mayo
                        @elseif($id_mes == '6') Junio
                        @elseif($id_mes == '7') Julio
                        @elseif($id_mes == '8') Agosto
                        @elseif($id_mes == '9') Septiembre
                        @elseif($id_mes == '10') Octubre
                        @elseif($id_mes == '11') Noviembre
                        @elseif($id_mes == '12') Diciembre
                        @endif
                      </td>
                      <td>@if(!is_null($value->sueldo_neto)){{$value->sueldo_neto}}@endif</td>
                      <td>@if(!is_null($valor_porcentaje)){{$valor_porcentaje}}@endif</td>
                      <td><?php echo round(((($value->sueldo_neto)*($valor_porcentaje))/100), 2);?></td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot>
                </tfoot>
              </table>
              <label style="padding-left: 15px;font-size: 15px">Total de Registros: {{$empl_rol->count()}}</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>