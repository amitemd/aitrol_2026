@extends('layouts.app-template-h')
@section('content')
<div class="content">
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-9">
                <h3>
                    {{trans('farmacia.MasterFarmacia')}}
                </h3>
            </div>

        </div>
    </section>
    <div class="card card-primary">
        <div class="card-header with-border">
            <h3 class="card-title">{{trans('farmacia.Farmacia')}}</h3>
            <div class="card-tools pull-right">
                <button type="button" class="btn btn-card-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="form_buscar" method="POST" action="{{route('farmacia.buscar_medicina')}}">
                        {{ csrf_field() }}
                        <div class="form-row">
                            <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                                <label class="col-md-4 control-label">{{trans('farmacia.Medicamento')}}</label>
                                <div class="col-md-9">
                                    <input type="text" style="text-transform:uppercase" class="form-control input-sm" name="producto" id="producto" autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                                <label class="col-md-4 control-label">{{trans('farmacia.FechaDesde')}}</label>
                                <div class="col-md-9">
                                    <input type="date" data-input="true" class="form-control input-sm flatpickr-basic active" name="fecha_desde" id="fecha_desde" autocomplete="off" value="{{$fecha_desde}}">
                                </div>
                            </div>
                            <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                                <label class="col-md-4 control-label">{{trans('farmacia.FechaHasta')}}</label>
                                <div class="col-md-9">
                                    <input type="date" data-input="true" class="form-control input-sm flatpickr-basic active" name="fecha_hasta" id="fecha_hasta" autocomplete="off" value="{{$fecha_hasta}}">
                                </div>
                            </div>

                            <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                                <label class="col-md-3 control-label">{{trans('farmacia.Bodega')}}</label>
                                <div class="col-md-9">
                                    <select class="form-control" id="bodega" name="bodega" old="{{ old('bodega') }}">
                                        <option value="">HOSPITAL</option>
                                        @foreach($bodegas as $b)
                                        <option value="{{$b->id}}">{{$b->nombre}}</option>
                                        @endforeach
                                        

                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-2">
                                <button class="btn btn-info btn-sm" type="submit"><i class="fa fa-search"></i></button>
                            </div>
                            <div class="form-group col-md-2">
                                <a href="{{route('hospital.invoice')}}" class="btn btn-primary btn-sm"> <i class="fa fa-receipt"></i> </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{trans('farmacia.Fecha')}}</th>
                                <th>{{trans('farmacia.Medicamento')}}</th>
                                <th>{{trans('farmacia.Bodega')}}</th>
                                <th>{{trans('farmacia.ExistenciadelProducto')}}</th>
                                <th>Informacion</th>
                                <th>{{trans('farmacia.Accion')}}</th>



                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicamento as $m)
                            <tr>
                                <td>{{$m->created_at}}</td>
                                <td>{{$m->producto->nombre}}</td>
                                <td>{{$m->nombre}}</td>
                                <td>{{$m->existencia}}</td>
                                <td>@if($m->estado == 1) En farmacia @endif</td>
                               
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </div>
</div>
@endsection