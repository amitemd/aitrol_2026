@extends('servicios_generales.limpieza_area.base')
@section('action-content')
@php
$date = date('Y-m-d');
@endphp
<style>
    .sepa{
        margin-top: 5px;
    }
</style>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
                <h5><b>Reporte de Limpieza de Áreas</b></h5>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button class="btn btn-danger" onclick="goBack()"><i class="fa fa-reply" aria-hidden="true"></i> {{trans('tecnicof.return')}}</button>
            </div>
        </div>
        <div class="box-body">
            <form action="{{route('limpieza_areas.excel')}}" method="POST">
                {{ csrf_field() }}
                {{--<input type="hidden" name="id" value="{{$id}}">--}}
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-1">
                            <label for="">{{trans('tecnicof.from')}}</label>
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" type="date" name="desde">
                        </div>
                        <div class="col-md-1">
                            <label for="">{{trans('tecnicof.to')}}</label>
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" type="date" name="hasta">
                        </div>
                        <div class="col-md-1">
                            <label for="">{{trans('tecnicof.type')}}</label>
                        </div>
                        <div class="col-md-2">
                            <select name="tipo" class="form-control" id="tipo">
                                <option value="">{{trans('tecnicof.select')}}</option>
                                <!-- <option value="0">CRM</option> -->
                                <option value="1" selected>Endoscopynet</option>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label for="">{{trans('tecnicof.user')}}</label>
                        </div>
                        <div class="col-md-2">
                            <select id="encargados" name="encargados" class="form-control" autofocus>
                                <option value="">{{trans('tecnicof.select')}}</option>
                                @foreach($encargados as $encargado)
                                <option value="{{$encargado->id}}">{{$encargado->nombre1}} {{$encargado->apellido1}} {{$encargado->apellido2}}</option>
                                @endforeach
                                
                            </select>
                        </div>
                        
                        <div class="col-md-1">
                            <label for="edificio">Edificio</label>
                        </div>
                        <div class="col-md-2" style="margin-top:5px;">
                            <select id="edificio" name="edificio" class="form-control" autofocus >
                              <option value="">Seleccione</option>
                              @<?php foreach ($edificio as $key => $value): ?>
                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                              <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label for="piso">Áreas</label>
                        </div>
                        <div class="col-md-2" style="margin-top:5px;">
                            <select id="piso" name="piso" class="form-control" autofocus >
                              <option value="">Seleccione</option>
                              @<?php foreach ($piso as $key => $value): ?>
                                <option value="{{$value->id}}">{{$value->nombre_oficina}}</option>
                              <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2 sepa">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> {{trans('tecnicof.save')}}</button> 
                        </div>

                    </div>
                </div>
              </form>
        </div>
</section>
<script type="text/javascript">

    window.addEventListener("load", (event) => {
        alert("Porfavor recuerde seleccionar el edificio y área.")
    });

    function goBack() {
        window.history.back();
    }
</script>
@endsection