@extends('contable.rol_pago.base')
@section('action-content')

<!-- Ventana modal Rol Pago-->
<div class="modal fade" id="modal_rol_pago" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<section class="content">
      <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Nomina</a></li>
        <li class="breadcrumb-item"><a href="{{route('nomina.index')}}">Empleado</a></li>
        <li class="breadcrumb-item active" aria-current="page">Rol Pago</li>
      </ol>
    </nav>
    <div class="box">
      <div class="box-header">
          <div class="col-md-7">
            <h5><b>ROL DE PAGO</b></h5>
          </div>
          <div class="col-md-3 text-right">
            <!--<a href="{{route('rol_pago.create', ['id' => $id_nomina])}}"  data-toggle="modal" data-target="#modal_rol_pago" class="btn btn-success btn-gray">Crear Rol Pago</a>-->
            <a href="{{route('rol_pago.create', ['id' => $id_nomina])}}" class="btn btn-success btn-gray">Crear Rol Pago</a>
          </div>
          <div class="col-md-1 text-right">
            <button  onclick="goBack()" class="btn btn-default btn-gray">
              <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
            </button>
          </div>
      </div>
      <div class="row head-title">
        <div class="col-md-12 cabecera">
          <label class="color_texto" for="title">BUSCADOR ROL DE PAGO</label>
        </div>
      </div>
      <div class="box-body dobra">
        <form method="POST" id="buscad_rol_pago" action="{{ route('rol_pago.buscar') }}">
          {{ csrf_field() }}

          <input  name="id_nomina" id="id_nomina" type="text" class="hidden" value="@if(!is_null($id_nomina)){{$id_nomina}}@endif">

          <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="identificacion">Identificaci&oacute;n:</label>
          </div>
          <div class="form-group col-md-2 col-xs-10 container-4" style="padding-left: 15px;">
            <input class="form-control" type="text" id="identificacion" name="identificacion"  placeholder="Ingrese Identificación..."  autocomplete="off" value="{{$empleado->id_user}}"/>
          </div>
          <!--<div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="identificacion">Empresa: </label>
          </div>
          <div class="form-group col-md-5 col-xs-10 container-4">
            <select class="form-control" id="id_empresa" name="id_empresa">
              <option>Seleccione...</option>
              <option value="0992704152001">GASTROCLINICA</option>
              <option value="0993075000001">HUMANLABS</option>
              <option value="0993094072001">MEDISCONSGROUP</option>
              <option value="1307189140001">DR. CARLOS ROBLES MEDRANDA</option>
              <option value="1314490929001">PITANGA LUKASHOK HANNAH</option>
            </select>
          </div>-->
          <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="year">Año</label>
          </div>
          <div class="form-group col-md-2 col-xs-10 container-4">
            <select id="year" name="year" class="form-control">
              <option value="">Seleccione...</option>
              <?php
for ($i = 2019; $i <= 2030; $i++) {
    echo "<option value='" . $i . "'>" . $i . "</option>";
}
?>
            </select>
          </div>
          <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="mes">Mes</label>
          </div>
          <div class="form-group col-md-2 col-xs-10 container-4">
            <select id="mes" name="mes" class="form-control">
              <option value="">Seleccione...</option>
              <?php
$Meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

for ($i = 1; $i <= 12; $i++) {

    echo '<option value="' . $i . '">' . $Meses[($i) - 1] . '</option>';
}
?>
            </select>
          </div>
          <div class="col-xs-2">
            <button type="submit" class="btn btn-primary">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
            </button>
          </div>
        </form>
      </div>
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
                    <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                      <thead>
                        <tr class='well-dark'>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Nombres </th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cedula</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Año</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Mes</th>
                          <!--<th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Empresa</th>-->
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Sueldo</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total Ingresos</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total Egresos</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Neto Recibido</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                          <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Accion</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($rol_pag as $value)
                          @php
                            $user = Sis_medico\User::find($value->id_user);
                            $obtener_nombre = Sis_medico\Empresa::find($value->id_empresa);
                            $obtener_detalle = Sis_medico\Ct_Detalle_Rol::where('id_rol',$value->id)->first();
                          @endphp
                          <tr>
                            <td>{{$user->apellido1}} @if($user->apellido2!='(N/A)'){{$user->apellido2}}@endif {{$user->nombre1}} @if($user->nombre2!='(N/A)'){{$user->nombre2}}@endif</td>
                            <td>@if(!is_null($user->id)){{$user->id}}@endif</td>
                            <td>@if(!is_null($value->anio)){{$value->anio}}@endif</td>
                            <td>
                              @if($value->mes == '1') Enero
                                @elseif($value->mes == '2') Febrero
                                @elseif($value->mes == '3') Marzo
                                @elseif($value->mes == '4') Abril
                                @elseif($value->mes == '5') Mayo
                                @elseif($value->mes == '6') Junio
                                @elseif($value->mes == '7') Julio
                                @elseif($value->mes == '8') Agosto
                                @elseif($value->mes == '9') Septiembre
                                @elseif($value->mes == '10') Octubre
                                @elseif($value->mes == '11') Noviembre
                                @elseif($value->mes == '12') Diciembre
                              @endif

                            </td>
                            <!--<td>@if(!is_null($obtener_nombre->razonsocial)){{$obtener_nombre->razonsocial}}@endif</td>-->
                            <td>@if(!is_null($obtener_detalle)){{$obtener_detalle->sueldo_mensual}}@endif</td>
                            <td>@if(!is_null($obtener_detalle)){{$obtener_detalle->total_ingresos}}@endif</td>
                            <td>@if(!is_null($obtener_detalle)){{$obtener_detalle->total_egresos}}@endif</td>
                            <td>@if(!is_null($obtener_detalle)){{$obtener_detalle->neto_recibido}}@endif</td>
                            <td>@if($value->estado == '1') Activo @elseif($value->estado =='0') Anulada @else Activo @endif
                            </td>
                            <td>
                              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                              @if($value->estado == '1')
                                <a href="{{route('rol_pago.editar', ['id' => $value->id])}}" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a>
                                <a target="_blank" href="{{route('rol_pago.imprimir', ['id' => $value->id])}}" class="btn btn-success  btn-xs"><span class="glyphicon glyphicon-download-alt"></span></a>
                                <a href="{{route('rol_pago.anular',['id' => $value->id])}}" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>
                                <a  class="btn btn-primary  btn-xs" onclick="reenviar_mail('{{$value->id}}')"><span class="fa fa-envelope"></span></a>
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
                <div class="row">
                  <div class="col-sm-5">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($rol_pag->currentPage() - 1) * $rol_pag->perPage())}} / {{count($rol_pag) + (($rol_pag->currentPage() - 1) * $rol_pag->perPage())}} de {{$rol_pag->total()}} registros</div>
                  </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      {{ $rol_pag->appends(Request::only(['identificacion','id_empresa','year', 'mes']))->links() }}
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
  <script type="text/javascript">

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : false,
      'info'        : false,
      'autoWidth'   : false
    })

    $('#modal_rol_pago').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
    });

    function goBack() {
      window.history.back();
    }

    function reenviar_mail(id){
      Swal.fire({
        title: 'Estas Seguro de Reenviar el Mail de Rol?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Enviar`,
        denyButtonText: `No Enviar`,
        showLoaderOnConfirm: true,
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          $.ajax({
            url:"{{asset('contable/rol/pago/envio/correo/')}}/"+id,
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'GET',
            success: function(data){
                if(data == 'ok'){
                  Swal.fire('Enviado Correctamente!', '', 'success');
                }

            },
            error: function(data){
                Swal.fire('Error al Enviar el Correo!', '', 'error');
            }
          });

        }
      });
    }

  </script>

@endsection