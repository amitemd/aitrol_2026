@extends('contable.rol_provisiones_sociales.base')
@section('action-content')


<section class="content">
      <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item active" aria-current="page">Provisiones Sociales</li>
      </ol>
    </nav>
    <div class="box">
      <div class="box-header header_new">
          <div class="col-md-7">
            <h3 class="box-title">Provisiones Sociales</h3>
          </div>
          <!--<div class="col-md-1 text-right">
            <button  onclick="goBack()" class="btn btn-default btn-gray">
              <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
            </button>
          </div>-->
      </div>
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto" for="title">BUSCADOR DE PROVISIONES SOCIALES</label>
        </div>
      </div>
      <div class="box-body dobra">
        <form method="POST" id="buscad_rol_pago" action="{{route('rol_provisiones.buscar')}}">
          {{ csrf_field() }}
          <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="identificacion">Identificaci&oacute;n:</label>
          </div>
          <div class="form-group col-md-4 col-xs-10 container-4" style="padding-left: 15px;">
                <input class="form-control" type="text" id="identificacion" name="identificacion"  placeholder="Ingrese Identificación..."  value="@if(isset($searchingVals)){{$searchingVals['id_user']}}@endif"  />
          </div>
          <div class="form-group col-md-1 col-xs-2">
              <label class="texto" for="identificacion">Empresa: </label>
          </div>
          <div class="form-group col-md-4 col-xs-10 container-4">
            <select class="form-control" id="id_empresa" name="id_empresa" onchange="buscarIdentificacion()">
              <option value=""> -TODOS- </option>      
                @foreach($empresas as $value)
                @if(isset($searchingVals)){{$searchingVals['id_user']}}@endif
                  <option value="{{$value->id}}" @if(isset($searchingVals) && (($searchingVals['id_empresa'] == $value->id))) selected="selected" @endif  >{{$value->nombrecomercial}}</option>
                @endforeach
                </select>
          </div>
          <div class="col-xs-2">
            <button type="submit" id="buscarEmpleado" class="btn btn-primary">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
            </button>
          </div>
        </form>
      </div>
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto" >LISTADO DE PROVISIONES SOCIALES</label>
        </div>
      </div>
      <div class="box-body dobra">
        <div class="form-group col-md-12">
          <div class="form-row">
            <div id="resultados">
            </div> 
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row">
                  <div class="table-responsive col-md-12">
                    <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                      <thead>
                        <tr class='well-dark'>
                          <th width="12%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Cedula</th>
                          <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Empleado</th>
                          <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Empresa</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Año</th>
                          <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Mes</th>
                          <th width="8%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Sueldo</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Horas Extras 50%</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Horas Extras 100%</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total Horas Extras</th>
                          <!--<th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Aporte Patronal IESS</th>-->
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Décimo Tercero</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Décimo Cuarto</th>
                          <th width="4%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Vacaciones</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fondos de Reserva</th>
                        
                          <!--<th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Vacaciones</th>-->
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total Ingreso</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Aporte Personal IESS</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Anticipo</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total Egreso</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">A Recibir</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Aporte Patronal IESS</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($registros as $value)
                            
                          @php
                            $user = Sis_medico\User::find($value->id_usuario);
                            $obtener_nombre = Sis_medico\Empresa::find($value->ident_emp);

                            $val_sal_bas = Sis_medico\Ct_Rh_Valores::where('id_empresa',$value->ident_emp)
                                                                   ->where('tipo',3)->first();

                            $val_aport_pers = Sis_medico\Ct_Rh_Valores::where('id_empresa',$value->ident_emp)
                                                                   ->where('tipo',1)->first();


                            $val_fond_reserv = Sis_medico\Ct_Rh_Valores::where('id_empresa',$value->ident_emp)
                                                                   ->where('tipo',4)->first();

                            $val_aport_patronal = Sis_medico\Ct_Rh_Valores::where('id_empresa',$value->ident_emp)
                                                                   ->where('tipo',2)->first();

                          
                          @endphp

                            <tr class="well">
                              <td>@if(!is_null($value->id_usuario)){{$value->id_usuario}}@endif</td>
                              <td>{{$user->apellido1}} @if($user->apellido2!='(N/A)'){{$user->apellido2}}@endif {{$user->nombre1}} @if($user->nombre2!='(N/A)'){{$user->nombre2}}@endif</td>
                              <td>@if(!is_null($obtener_nombre->nombrecomercial)){{$obtener_nombre->nombrecomercial}}@endif</td>
                              <td>{{$value->anio_rol}}</td>
                              <td>
                                @if($value->mes_rol == '1') Enero
                                  @elseif($value->mes_rol == '2') Febrero
                                  @elseif($value->mes_rol == '3') Marzo
                                  @elseif($value->mes_rol == '4') Abril
                                  @elseif($value->mes_rol == '5') Mayo
                                  @elseif($value->mes_rol == '6') Junio
                                  @elseif($value->mes_rol == '7') Julio
                                  @elseif($value->mes_rol == '8') Agosto
                                  @elseif($value->mes_rol == '9') Septiembre
                                  @elseif($value->mes_rol == '10') Octubre
                                  @elseif($value->mes_rol == '11') Noviembre
                                  @elseif($value->mes_rol == '12') Diciembre
                                @endif
                              </td>
                              <td>@if(!is_null($value->sueldo)){{$value->sueldo}}@endif</td>
                              <td>@if(!is_null($value->hor_ext50)){{$value->hor_ext50}}@endif</td>
                              <td>@if(!is_null($value->hor_ext100)){{$value->hor_ext100}}@endif</td>
                              <td>{{($value->hor_ext50)+($value->hor_ext100)}}</td>
                              <td><?php echo round(((($value->sueldo)+($value->hor_ext50)+($value->hor_ext100))/12), 2);?></td>
                              <!--<td>{{((($value->sueldo)+($value->hor_ext50)+($value->hor_ext100))/12)}}</td>-->
                              <td><?php echo round(($val_sal_bas->valor/12), 2);?></td>
                              <td><?php echo round(((($value->sueldo)+($value->hor_ext50)+($value->hor_ext100))/24), 2);?></td>
                              <td><?php echo round(((($value->sueldo)+($value->hor_ext50)+($value->hor_ext100))*($val_fond_reserv->valor)/100), 2);?></td>
                              
                              <td><?php echo round(((($value->sueldo)+($value->hor_ext50)+($value->hor_ext100))+((($value->sueldo)+($value->hor_ext50)+($value->hor_ext100))*($val_fond_reserv->valor)/100)), 2);?></td>
                              <td><?php echo round(((($value->sueldo)+($value->hor_ext50)+($value->hor_ext100))*($val_aport_pers->valor)/100), 2);?></td>
                              <td>@if(!is_null($value->anticipo)){{$value->anticipo}}@endif</td>
                              <td><?php echo round((((($value->sueldo)+($value->hor_ext50)+($value->hor_ext100))*($val_aport_pers->valor)/100)+($value->anticipo)), 2);?></td>
                              <td><?php echo round(((((($value->sueldo)+($value->hor_ext50)+($value->hor_ext100))+((($value->sueldo)+($value->hor_ext50)+($value->hor_ext100))*($val_fond_reserv->valor)/100)))-(((($value->sueldo)+($value->hor_ext50)+($value->hor_ext100))*($val_aport_pers->valor)/100)+($value->anticipo)))
                                , 2);?></td>
                              <td><?php echo round(((($value->sueldo)+($value->hor_ext50)+($value->hor_ext100))*($val_aport_patronal->valor)/100), 2);?></td>
                          </tr>
                        @endforeach
                      </tbody>
                      <tfoot>
                      </tfoot>
                    </table>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-5">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($registros->currentPage() - 1) * $registros->perPage())}} / {{count($registros) + (($registros->currentPage() - 1) * $registros->perPage())}} de {{$registros->total()}} registros
                    </div>
                  </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      {{ $registros->appends(Request::only(['id_user','id_empresa']))->links() }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> 
    </div>
</section>
 
  <script type="text/javascript">
    
    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : false,
      'info'        : false,
      'autoWidth'   : false
    })

    /*function goBack() {
      window.history.back();
    }*/
  
  </script> 

@endsection