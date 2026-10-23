@extends('rol.base')
@section('action-content')

<section class="content">
  <div class="box">
    <!--div class="box-header">
        <div class="row">
            <div class="col-sm-8">
              <h3 class="box-title"> Planillas Generadas</h3>
            </div>
        </div>
    </div-->
      <div class="box-body">
        <div class="row">
          <div class="form-group col-md-6 ">
              <div class="row" >
                <div class="form-group col-md-10 ">
                              <label for="cedula" class="col-md-4 control-label">Usuario:</label>
                              <div class="col-md-7">
                                  {{$usuario->apellido1}} {{$usuario->apellido2}} {{$usuario->nombre1}} {{$usuario->nombre2}}

                              </div>
                      </div>
              </div>
              </div>
        </div>

            <form method="POST" id="form_rol" action="{{route('rol.lista')}}">
            {{ csrf_field() }}
            <div class="form-group col-md-4 ">
            <div class="row" >
            <div class="form-group col-md-10 ">
                        <label for="cedula" class="col-md-4 control-label">Año:</label>
                        <div class="col-md-7">
                            <select class="form-control" name="anio" value="{{$anio}}">
                              @php $x=2018; $anio_actual=date('Y'); @endphp
                              @for($x=2018;$x<=$anio_actual;$x++)
                              <option @if($x==$anio) selected @endif>{{$x}}</option>
                              @endfor
                            </select>

                        </div>
                  </div>
            </div>
            </div>

            <div class="form-group col-md-4 ">
            <div class="row" >
            <div class="form-group col-md-10 ">
                        <label for="cedula" class="col-md-4 control-label">Mes:</label>
                        <div class="col-md-7">
                            <select class="form-control" name="mes">
                              <option value="1" @if($mes==1) selected @endif>Enero</option>
                              <option value="2" @if($mes==2) selected @endif >Febrero</option>
                              <option value="3" @if($mes==3) selected @endif >Marzo</option>
                              <option value="4" @if($mes==4) selected @endif >Abril</option>
                              <option value="5" @if($mes==5) selected @endif >Mayo</option>
                              <option value="6" @if($mes==6) selected @endif >Junio</option>
                              <option value="7" @if($mes==7) selected @endif >Julio</option>
                              <option value="8" @if($mes==8) selected @endif >Agosto</option>
                              <option value="9" @if($mes==9) selected @endif >Septiembre</option>
                              <option value="10" @if($mes==10) selected @endif >Octubre</option>
                              <option value="11" @if($mes==11) selected @endif>Noviembre</option>
                              <option value="12" @if($mes==12) selected @endif>Diciembre</option>
                            </select>

                        </div>
                  </div>
            </div>
            </div>

            <div class="form-group col-md-2 ">
                <div class="col-md-7">
                    <button id="buscar" type="submit" class="btn btn-primary"> <span class="glyphicon glyphicon-search"> Buscar</span></button>
                </div>
            </div>
          </form>

          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row" id="listado">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr>
                      <th style="text-align: center;">Nombres</th>
                      <th style="text-align: center;">Cedula</th>
                      <th style="text-align: center;">Año</th>
                      <th style="text-align: center;">Mes</th>
                      <th style="text-align: center;">Empresa</th>
                      <th style="text-align: center;">Sueldo</th>
                      <th style="text-align: center;">Total Ingresos</th>
                      <th style="text-align: center;">Total Egresos</th>
                      <th style="text-align: center;">Neto Recibido</th>
                      <th style="text-align: center;">Estado</th>
                      <th style="text-align: center;">Accion</th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($rol_pag as $rol)
                      <tr>
                        @php

                        $empresa =Sis_medico\Empresa::find($rol->id_empresa);
                        $detalle_rol = Sis_medico\Ct_Detalle_Rol::where('id_rol',$rol->id)->first();
                        @endphp
                        <td>{{$usuario->apellido1}} @if($usuario->apellido2!='(N/A)'){{$usuario->apellido2}}@endif {{$usuario->nombre1}} @if($usuario->nombre2!='(N/A)'){{$usuario->nombre2}}@endif</td>
                        <td>{{$rol->id_user}}</td>
                        <td>{{$rol->anio}}</td>
                        <td>
                          @if($rol->mes == '1') Enero
                            @elseif($rol->mes == '2') Febrero
                            @elseif($rol->mes == '3') Marzo
                            @elseif($rol->mes == '4') Abril
                            @elseif($rol->mes == '5') Mayo
                            @elseif($rol->mes == '6') Junio
                            @elseif($rol->mes == '7') Julio
                            @elseif($rol->mes == '8') Agosto
                            @elseif($rol->mes == '9') Septiembre
                            @elseif($rol->mes == '10') Octubre
                            @elseif($rol->mes == '11') Noviembre
                            @elseif($rol->mes == '12') Diciembre
                            @endif
                        </td>
                        <td>{{$empresa->razonsocial}}</td>
                        <td>{{$detalle_rol->sueldo_mensual}}</td>
                        <td>{{$detalle_rol->total_ingresos}}</td>
                        <td>{{$detalle_rol->total_egresos}}</td>
                        <td>{{$detalle_rol->neto_recibido}}</td>
                        <td>@if($rol->estado == '1') Activo @elseif($rol->estado =='0') Anulada @else Activo @endif</td>
                        <td>
                          @if($rol->estado == '1')
                          <a target="_blank" href="{{route('rol_pago.imprimir', ['id' => $rol->id])}}" class="btn btn-success  btn-xs"><span class="glyphicon glyphicon-download-alt"></span></a>
                          @endif
                        </td>
                      </tr>
                      @endforeach
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table>
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
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
</script>

@endsection
