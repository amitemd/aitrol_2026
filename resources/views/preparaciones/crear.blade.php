@extends('preparaciones.base')
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
<div class="modal fade" id="codigo_preparacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-11">
                    <h3 class="box-title">Crear Nueva Preparacion </h3>
                </div>
            </div>
        </div>
        <br>

        <form method="post" action="{{route('preparaciones.guardar')}}" enctype="multipart/form-data">

            {{ csrf_field() }}



            <div class="box-body">

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-20 control-label">Nombre</label>
                        <input type="text" name="nombre_preparaciones" size="20" class="form-control" value="{{ old('nombre_preparaciones') }}" required maxlength="100">
                    </div>
                </div>



                <div class="from-group col-md-2 col-xs-2">
                    <label class="texto" for="archivo_preparaciones">Agregar archivo</label>
                </div>
                <div class="from-group col-md-8 container-5" style="padding-left:15px;">
                    <input type="file" name="archivo_preparaciones" id="archivo_preparaciones" class="archivo_preparaciones form-control" required accept="aplication/vnd.openxmformats-officedocument.spreadsheetml.sheet" value="old('archivo_preparaciones') }}">
                </div>


                <div class="container">
                    <div class="form-group col-md-5 ">
                        <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                    </div>
                </div>

        </form>

</section>

</html>
@endsection