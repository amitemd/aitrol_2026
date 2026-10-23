@extends('hospital/tipoemergencia/base')
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
                    <h3 class="box-title">Editar Tipo de Emergencia</h3>
               
        </div>
        </div>
        <form action="{{route('tipoemergencia.actualizar')}}" method="POST">
{{ csrf_field() }}
<input type="hidden" value="{{$tipo->id}}" name="idtipo">
    <div class="row justify-content-center">
	    <div class="card-body">
	    	<div class="form-group col-md-10">
                <label for="nombre">Nombre</label>
				<input type="text" name="nombre" size="20" class="form-control" value="{{$tipo->nombre}}" required  maxlength="100">
			</div>
		</div>
     </div>  

     <div class="row justify-content-center">
	    <div class="card-body">
	    	<div class="form-group col-md-10">
                <label for="estado">Estado</label>
				<input type="double" name="estado" size="20" class="form-control" value="{{$tipo->estado}}" required  maxlength="100">
			</div>
		</div>
     </div>
 
    

     <div class="container">
    <div class="form-group col-md-5 " >
    <button  type="submit" class="btn btn-primary btn-sm">Actualizar</button>         
    </div>
 </div>

 </form>
 </div>
 </div>

 </section> 
@endsection
