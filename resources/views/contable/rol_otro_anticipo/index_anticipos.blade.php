@extends('contable.rol_anticipo_quincena.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">

  <!-- Main content -->
  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item active" aria-current="page">Anticipos a Empleado</li>
      </ol>
    </nav>
      <div class="box">
        <div class="box-header">
            <div class="col-md-9">
              <h5><b>ANTICIPOS A EMPLEADOS</b></h5>
            </div>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">BUSCADOR DE ANTICIPOS</label>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
          <form method="POST" id="buscar_anticipo" action="{{ route('anticipo_empleado.search') }}">
            {{ csrf_field() }}
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="identificacion">Identificaci&oacute;n:</label>
                </div>
                <div class="form-group col-md-4 col-xs-10 container-4" style="padding-left: 15px;">
                      <input class="form-control" type="text" id="identificacion" name="identificacion"  placeholder="Ingrese Identificación..."  value="@if(isset($searchingVals)){{$searchingVals['id_empl']}}@endif" autocomplete="off" />
                </div>
                <div class="col-xs-2">
                  <button type="submit" id="buscaranticipo" class="btn btn-primary">
                      <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                  </button>
                </div>
          </form>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" >LISTADO DE ANTICIPOS</label>
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
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Cedula</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Empleado</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Monto Anticipo</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha Creacion</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo Rol</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Mes Cobro Anticipo</th>                        
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Año Cobro Anticipo</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo Pago</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Numero Cuenta Beneficiario</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Banco Beneficiario</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cuenta Empresa</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Numero Cheque</th>
                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                              </tr>
                            </thead>
                            <tbody>
                            @foreach ($registros as $value)

                              @php 
                                $nom_cuenta = Sis_medico\Plan_Cuentas::where('id',$value->cuenta_saliente)->first();
                                $inf_usuario = Sis_medico\User::where('id',$value->id_empl)->first();
                              @endphp
                              
                              <tr class="well">
                                <td >@if(!is_null($value->id_empl)){{$value->id_empl}}@endif</td>
                                <td >{{$inf_usuario->nombre1}}  @if($inf_usuario->nombre2!='(N/A)'){{ $inf_usuario->nombre2 }}@endif {{ $inf_usuario->apellido1 }} @if($inf_usuario->apellido2!='(N/A)'){{$inf_usuario->apellido2}}@endif</td>
                                <td >@if(!is_null($value->monto_anticipo)){{$value->monto_anticipo}}@endif</td>
                                <td >@if(!is_null($value->fecha_creacion)){{$value->fecha_creacion}}@endif</td>
                                <td >@if(!is_null($value->tipo_rol)){{$value->tipo_rol}}@endif</td>
                                <td >@if($value->mes_inicio_cobro == '1') 
                                      Enero 
                                     @elseif($value->mes_cobro_anticipo == '2') 
                                      Febrero
                                     @elseif($value->mes_cobro_anticipo == '3')
                                      Marzo
                                     @elseif($value->mes_cobro_anticipo == '4')
                                      Abril
                                     @elseif($value->mes_cobro_anticipo == '5')
                                      Mayo
                                     @elseif($value->mes_cobro_anticipo == '6')
                                      Junio
                                     @elseif($value->mes_cobro_anticipo == '7')
                                      Julio
                                     @elseif($value->mes_cobro_anticipo == '8')
                                      Agosto
                                     @elseif($value->mes_cobro_anticipo == '9')
                                      Septiembre
                                     @elseif($value->mes_cobro_anticipo == '10')
                                      Octubre 
                                     @elseif($value->mes_cobro_anticipo == '11') 
                                      Noviembre
                                     @elseif($value->mes_cobro_anticipo == '12')
                                      Diciembre
                                     @endif  
                                  </td>
                                  <td >@if(!is_null($value->anio_cobro_anticipo)){{$value->anio_cobro_anticipo}}@endif</td>
                                  <td >
                                       @if($value->id_tipo_pago == '1') 
                                         ACREDITACION 
                                       @elseif($value->id_tipo_pago == '2') 
                                         EFECTIVO
                                       @elseif($value->id_tipo_pago == '3')
                                         CHEQUE
                                       @endif  
                                  </td>
                                  <td >@if(!is_null($value->num_cuenta_benef)){{$value->num_cuenta_benef}}@endif</td>
                                  <td >
                                       @if($value->banco_beneficiario == '1') 
                                       Banco Pichincha 
                                       @elseif($value->banco_beneficiario == '2') 
                                       Banco del Pacífico
                                       @elseif($value->banco_beneficiario == '3')
                                       Banco Guayaquil
                                       @elseif($value->banco_beneficiario == '4')
                                       Banco Internacional 
                                       @elseif($value->banco_beneficiario == '5')
                                       Banco Bolivariano
                                       @elseif($value->banco_beneficiario == '6')
                                       Produbanco
                                       @elseif($value->banco_beneficiario == '7')
                                       Banco del Austro
                                       @elseif($value->banco_beneficiario == '8')
                                       Banco Solidario
                                       @elseif($value->banco_beneficiario == '9')
                                       Banco General Rumiñahui
                                       @elseif($value->banco_beneficiario == '10')
                                       Banco de Loja 
                                       @endif 
                                    </td>
                                    <td >@if(!is_null($nom_cuenta->nombre)){{$nom_cuenta->nombre}}@endif</td>
                                    <td >
                                      @if(!is_null($value->num_cheque))
                                        {{$value->num_cheque}}
                                      @else
                                        <a onclick="#" class="btn btn-success btn-xs boton_egreso">
                                          <span id="p_egreso" style="font-size: 9px">NO POSEE</span>
                                        </a>
                                      @endif
                                    </td>
                                    <td >@if($value->estado == '1') Activo @elseif($value->estado =='0') Anulada @else Activo @endif</td>  
                                </tr>
                            @endforeach  
                            </tbody>
                         </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
 
  </section>
  
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">
  
  $(document).ready(function(){
 
      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : false,
        'info'        : false,
        'autoWidth'   : false
      });

  });

</script>
@endsection
