<div class="row head-title">
  <div class="col-md-12 cabecera">
    <label class="color_texto" >BUSCADOR ANTICIPOS EMPLEADOS</label>
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
                    <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="5" colspan="5" aria-label="Codigo: activate to sort column ascending">Accion</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($anticip_quince as $value)
                    @php
                      $user = Sis_medico\User::find($value->id_user);
                      $obtener_nombre = Sis_medico\Empresa::find($value->id_empresa);
                    @endphp
                    <tr  class="well">
                      <td>@if(!is_null($user->id)){{$user->id}}@endif</td>
                      <td>{{$user->apellido1}} @if($user->apellido2!='(N/A)'){{$user->apellido2}}@endif {{$user->nombre1}} @if($user->nombre2!='(N/A)'){{$user->nombre2}}@endif</td>
                      <td>@if(!is_null($obtener_nombre->nombrecomercial)){{$obtener_nombre->nombrecomercial}}@endif</td>              
                      <td>@if(!is_null($value->anio)){{$value->anio}}@endif</td>
                      <td>
                        @if($value->quincena == '1') Enero
                        @elseif($value->quincena == '2') Febrero
                        @elseif($value->quincena == '3') Marzo
                        @elseif($value->quincena == '4') Abril
                        @elseif($value->quincena == '5') Mayo
                        @elseif($value->quincena == '6') Junio
                        @elseif($value->quincena == '7') Julio
                        @elseif($value->quincena == '8') Agosto
                        @elseif($value->quincena == '9') Septiembre
                        @elseif($value->quincena == '10') Octubre
                        @elseif($value->quincena == '11') Noviembre
                        @elseif($value->quincena == '12') Diciembre
                        @endif
                      </td>
                      <td>@if(!is_null($value->sueldo)){{$value->sueldo}}@endif</td>
                      <td>@if(!is_null($value->porcentaje)){{$value->porcentaje}}@endif</td>
                      <td>@if(!is_null($value->valor_anticipo)){{$value->valor_anticipo}}@endif</td>
                      <td>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          @if($value->estado == '1')
                          <a href="{{ route('anticipos_quincena.editar', ['id' => $value->id,'idempleado' => $value->id_user,'idempresa' => $value->id_empresa]) }}" class="btn btn-success btn-gray">
                              <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                          </a>
                          @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot>
                </tfoot>
              </table>
              <label style="padding-left: 15px;font-size: 15px">Total de Registros: {{$anticip_quince->count()}}</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>