@extends('archivo_plano.medicinapublica.base')
@section('action-content')
<!DOCTYPE html>
<html lang="en">

<head> 
    <style>
        @page {
            margin: 45px 80px;
        }

        .table>tbody>tr>td {
            padding: 0;
        }

        #paginacion {
            border: 1px solid #CCC;
            background-color: #E0E0E0;
            padding: .5em;
            overflow: hidden;
        }

        .texto {
            font-size: 15.0px;
            font-family: Arial;
        }
    </style>
</head>

<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-11">
                    <h3 class="box-title">{{ trans('contableM.crearNuevaMedicina')}}</h3>
                </div>
            </div>
        </div>
        <br>

        <form method="post"  action="{{ route('procedimientos.store') }}">
        {{ csrf_field() }}

        <div class="box-body">
        <div class="form-group col-xs-6">
            <div class="card-body">
                <div class="form-group col-md-20">
                    <label for="descripcion">{{ trans('contableM.medicina_listado')}}</label>
                    <input type="text" name="descripcion" size="20" class="form-control" value="" required  maxlength="100">
                </div>
            </div>
        </div>
        </div> 

        <div class="box-body">
            <div class="form-group col-xs-6">
                <div class="card-body">
                    <div class="form-group col-md-20">
                        <label for="codigo_medicina">{{ trans('contableM.codigo_medicina_tipo')}}</label>
                        <input type="text" id="codigo_medicina" name="codigo_medicina" size="20" class="form-control" value="" maxlength="50">
                    </div>
                </div>
        </div>
        </div> 


            <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('valor') ? ' has-error' : '' }}">
                        <label for="nombre1" class="col-md-20 control-label">{{ trans('contableM.valor_medicina')}}</label>
                        <input type="number" step="0.0001" name="valor" size="20" class="form-control" value="{{ old('Valor') }}" required maxlength="100">
                </div>
            </div>

            <div class="container">
                <div class="form-group col-md-5 ">
                    <button type="submit" class="btn btn-primary btn-sm">{{ trans('contableM.guardar_medicina')}}</button>
                </div>
            </div>

        </form>

</section>
@endsection
</html>