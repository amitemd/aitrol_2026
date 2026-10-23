@extends('contable.diario.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <div class="box " style="background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
                <h3 class="box-title">Datos del Asiento Contable</h3>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #ffffff;">
            <div class="box-body col-xs-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <span><b>Fecha de Registro:</b> {{$registro->fecha_asiento}}</span><br>
                                <span><b>Estado:</b> @if($registro->estado == '1') Activo @else Anulado @endif</span>
                                <p><b>Valor Registrado:</b> {{$registro->valor}}</p>
                                <p><b>Detalle: </b> </br>
                                    {{$registro->observacion}}
                                </p>
                                <h3>Asientos</h3>
                            </div>
                            <div class="col-md-12">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                  <thead>
                                    <tr >
                                      <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" ># de Cuenta</th>
                                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cuenta</th>
                                      <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Descripcion</th>
                                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Debe</th>
                                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Haber</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @foreach ($registro->detalles as $value)
                                    <tr>
                                        <td>{{$value->id_plan_cuenta}}</td>
                                        <td>{{$value->cuenta->nombre}}</td>
                                        <td>{{$value->descripcion}}</td>
                                        <td>$ {{$value->debe}}</td>
                                        <td>$ {{$value->haber}}</td>
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
        </div>
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">

$(document).ready(function(){
    $('.select2_cuentas').select2({
        tags: false
    });
});

    function goBack() {
      window.history.back();
    }

</script>
</section>
@endsection
