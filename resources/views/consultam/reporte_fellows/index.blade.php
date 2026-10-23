@extends('consultam/reporte_fellows/base')
@section('action-content')

<section class="content">
<div class="box">
<div class="box-header">

<form method="post" action="{{route('consultas.descargar_rfellows') }}">
 {{ csrf_field() }}

               
         <div cass="form-group row">
           <div class="form-group col-md-5 ">
            <label for="fellows" class="col-form-label-sm">Usuario Fellow</label>
            <select class="form-control " name="menu" value="" maxlength="2">
             @foreach($rfellow as $rfellow)
            <option value="{{$rfellow->id}}">{{$rfellow->apellido1}} {{$rfellow->apellido2}} {{$rfellow->nombre1}} {{$rfellow->nombre2}} </option>
            @endforeach
            </select>
            
            <div class="col-sm-6">
                    <br><button class="btn btn-primary" type="submit"> Descargar</button>
                </div>
            </div>
            </div>
</form>
            </div>
            </div>
@endsection    