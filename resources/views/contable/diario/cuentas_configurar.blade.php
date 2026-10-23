@extends('contable.diario.base')
@section('action-content')

<style>
    :root {
        --ancho: 250px;
    }

    .ancho {
        width: var(--ancho);
    }

    .select2-container--default {
        width: var(--ancho) !important;
    }
</style>

<section class="content">
    <div class="box-body dobra">
        <form method="POST" id="buscador_cuentas" action="{{route('librodiario.planConfiguraciones')}}">
            {{ csrf_field() }}
            <div class="panel panel-default col-md-12">
                <div class="panel-heading">
                    <label>{{trans('contableM.Buscadores')}}</label>
                </div>
                <div class="panel-body">
                    <div class="form-row">
                        <div class="form-group row col-md-6">
                            <label for="buscar_plan_cuenta" class="col-sm-4 col-form-label">{{trans('contableM.tipoTabla')}}</label>
                            <div class="col-md-6 container-4">
                                <input class="form-control " type="text" id="id" name="id" value="@if(isset($data['id'])){{$data['id']}}@endif" placeholder="Buscar por el tipo de la configuracion..." />
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group row col-md-6">
                            <button style="margin-left: 14px;" type="submit" id="buscar" class="btn btn-primary">
                                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>


        <div class="row">
            <div class="col-md-12">
                <table id="tabla" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                        <tr class='well-dark'>
                            <th width="3%">{{trans('contableM.id')}}</th>
                            <th width="10%">{{trans('contableM.cuentatabla')}}</th>
                            <th width="10%">{{trans('contableM.nombretabla')}}</th>
                            <th width="10%">{{trans('contableM.tipoTabla')}}</th>


                            <th width="10%">{{trans('contableM.cuentacontable')}}</th>
                            <th width="10%">Modulo</th>

                            <th width="10%">{{trans('contableM.NombredelaCuenta')}}</th>
                            <th width="10%">{{trans('contableM.cuentaanterior')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $cont = 0; @endphp
                        @foreach($cuentas as $value)
                        <tr>
                            <td>{{$value->id}}</td>
                            <td>
                                <div class="ancho">
                                    <select name="" id="" class="form-control select2_cuenta select2_cuenta{{$cont}}" onchange="updateCuenta({{$cont}}, {{$value->id}})">
                                        <option value="">Seleccione...</option>
                                        @foreach ($plan_cuentas as $plan)
                                        <option @if($value->id_plan == $plan->id_plan and !is_null($value->id_plan)) selected @endif  value="{{$plan->id_plan}}">{{$plan->plan}} | {{$plan->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td> {{$value->nombre}}</td>
                            <td>{{$value->tipo}}</td>
                            <td> @if(!is_null($value->id_plan)) @if(isset($value->cuenta_empresa)) {{$value->cuenta_empresa->plan}} @endif @endif</td>
                            <td> {{$value->modulo}}</td>
                            <td> @if(!is_null($value->id_plan)) @if(isset($value->cuenta_empresa)) {{$value->cuenta_empresa->nombre}} @endif @endif</td>
                            <td>{{$value->cuenta_ant}}</td>
                        </tr>
                        @php $cont++; @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="col-md-12">
            <div style="text-align: end;">
                {{ $cuentas->links() }}
            </div>

        </div>
    </div>

</section>

<script>
    $(document).ready(function() {

        $('.select2_cuenta').select2({
            tags: false
        });
    });

    const buscar = () => {
        $.ajax({
            type: 'get',
            url: `{{route('librodiario.planConfiguraciones')}}`,
            data: $("#buscador_cuentas").serialize(),
            datatype: 'json',
            success: function(data) {
                console.log(data)
            },
            error: function(data) {
                console.log(data)
            }

        })
    }

    const updateCuenta = (id_cuenta, id) => {
        let selectCuenta = document.querySelector('.select2_cuenta' + id_cuenta).value;
        console.log(selectCuenta);
        $.ajax({
            type: 'get',
            url: `{{route('LibroDiario.ActualiacionCuenta')}}`,
            data: {
                'cuenta': selectCuenta,
                'id': id
            },
            datatype: 'json',
            success: function(data) {
                console.log(data)
            },
            error(data) {

            }

        })
    }
</script>


@endsection