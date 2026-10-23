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
                    <h3 class="box-title">Actualizacion de Medicinas</h3>
                </div>
            </div>
        </div>
        <br>
        
        <form action="{{route('procedimientos.update')}}" method="POST">
            {{ csrf_field() }}

           <input type="hidden" name="id" id="id" value="{{$id}}">
            
            <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-20 control-label">Descripcion de la Medicina</label>
                        <input type="text" name="descripcion" size="20" class="form-control" value="{{$medicina->descripcion}}" required maxlength="100">
                </div>
            </div>

            <div class="box-body">
                <div class="form-group col-xs-6{{ $errors->has('codigo_medicina') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-20 control-label">Código de la Medicina</label>
                        <input type="text" name="codigo_medicina" size="20" class="form-control" value="{{$medicina->codigo_medicina}}" required maxlength="500">
                </div>
            </div>

            <div class="box-body">
            <div class="form-group col-xs-6{{ $errors->has('valor') ? ' has-error' : '' }}">
                        <label for="valor" class="col-md-20 control-label">Valor</label>
                        <input type="number" step="0.0001" name="valor" class="form-control" value="{{$medicina->valor}}">
                </div>
            </div>
            
            <div class="container">
                <div class="form-group col-md-5"> 
                    <button type= 'submit' class="btn btn-primary btn-sm col-sm-3 col-xs-8 btn-margin">Actualizar</button>
                </div>
            </div>
        </form>

</section>

</html>

@endsection
