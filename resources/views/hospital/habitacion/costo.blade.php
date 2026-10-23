@extends('hospital.base')
@section('action-content')

<style>
    .box{
        border-color: #FDFEFE; border-radius: 30px;
    }
    h3{
        font-family: 'Montserrat Bold';
    }
    h1, input{
        font-family: 'Montserrat Medium';
        color: white;
    }
</style>
<div class="modal fade" id="modalservicios" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalServicio" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
    </div>
  </div>
</div>

<div class="content">
    <div class="content-header">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-8 col-sm-6">
                    <h3>{{trans('hospitalizacion.Costoporpaciente')}}</h3>
                </div>
                <div class="col-4">
                    <button type="button" class="btn btn-primary"  onclick ="location.href='{{ URL::previous() }}'">{{trans('hospitalizacion.Regresar')}}</button>
                     <button type="button"
                                data-remote="{{ route('hospital.servicio',['id_paciente'=>$id_paciente])}}"
                                class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalservicios">{{trans('hospitalizacion.SeleccionarProducto')}}
                     </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border"style="border-radius: 30px; background-image: linear-gradient(to right, #3352ff 0%, #051eff 100%); margin-bottom: 5px">
                <h1 class="box-title">{{trans('hospitalizacion.Costosgenerados')}}</h1>

                <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                    <i class="fa fa-minus"></i>
                </button>
                </div>
            </div>
            <div class="box-body">
            @php
              $fechaActual = date("Y-m-d H:i:s");
              $f1          = new DateTime($fechaActual);
              $f2          = new DateTime($categoria_habitacion[0]->updated_at);
              
              $d    = $f1->diff($f2);
              $dias = 0;
              if ($d->format('%d') > 0) {
                  $dias = $dias + ($d->format('%d'));
              }
              if ($d->format('%m') > 0) {
                  $dias = $dias + ($d->format('%m'));
              }
              if ($d->format('%y') > 0) {
                  $dias = $dias + ($d->format('%y'));
              }
            
              
            @endphp
            
                <form action="{{ route('hospital.costos_generados')}}" method="post">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label" style="font-size: 13px;">{{trans('hospitalizacion.Paciente')}}</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control form-control-sm" name="paciente" id="paciente" disabled value="{{$categoria_habitacion[0]->nombre1}} {{$categoria_habitacion[0]->apellido1}}">
                            </div>
                            <label class="col-sm-2 col-form-label" style="font-size: 12px;"><?php  $fechaActual = date('d-m-Y'); echo  $fechaActual  ?></label>
                        </div>
                         <table class="table table-sm table-bordered">
                            <thead class="table-primary">
                                <tr> 
                                    <th scope="col">{{trans('hospitalizacion.Cedula')}}</th>
                                    <th scope="col">{{trans('hospitalizacion.Numerodelacama')}}</th>
                                    <th scope="col">{{trans('hospitalizacion.Díasdehospedaje')}}</th>
                                    <th scope="col">{{trans('hospitalizacion.PrecioporHabitación')}}</th>
                                    <th scope="col">{{trans('hospitalizacion.Total')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoria_habitacion as $value)
                                <tr>
                                    <td>{{$value->id_paciente}}</td>
                                    <td>{{$value->codigo}}</td>
                                    <td>{{$dias}} dias</td>
                                    <td>40$</td>
                                    <td>@php echo $dias*40 @endphp</td>
                                </tr>
                                  @endforeach
                            </tbody>
                            <thead class="table-primary">
                                <tr> 
                                    <th scope="col">{{trans('hospitalizacion.DESAYUNO')}}</th>
                                    <th scope="col">{{trans('hospitalizacion.Plato')}}</th>
                                    <th scope="col">{{trans('hospitalizacion.CantidadRequerida')}}</th>
                                    <th scope="col">{{trans('hospitalizacion.Costodelplato')}}</th>
                                    <th scope="col">{{trans('hospitalizacion.Total')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nombres as $value)
                                <tr>
                                    <td>{{$value->id_paciente}}</td>
                                    <td>{{$value->desayuno}}</td>
                                    <td>{{$value->cant_desayuno}}</td>
                                    <td>{{$value->precio_desayuno}}$</td>
                                    <td>{{$value->precio_desayuno}}$</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <thead class="table-primary">
                                <tr> 
                                    <th scope="col">{{trans('hospitalizacion.ALMUERZO')}}</th>
                                    <th scope="col">{{trans('hospitalizacion.Plato')}}</th>
                                    <th scope="col">{{trans('hospitalizacion.CantidadRequerida')}}</th>
                                    <th scope="col">{{trans('hospitalizacion.Costodelplato')}}</th>
                                    <th scope="col">{{trans('hospitalizacion.Total')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nombres as $value)
                                <tr>
                                    <td>{{$value->id_paciente}}</td>
                                    <td>{{$value->almuerzo}}</td>
                                    <td>{{$value->cant_almuerzo}}</td>
                                    <td>{{$value->precio_almuerzo}}$</td>
                                    <td>{{$value->precio_almuerzo}}$</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <thead class="table-primary">
                                <tr> 
                                    <th scope="col">{{trans('hospitalizacion.CENA')}}</th>
                                    <th scope="col">{{trans('hospitalizacion.Plato')}}</th>
                                    <th scope="col">{{trans('hospitalizacion.CantidadRequerida')}}</th>
                                    <th scope="col">{{trans('hospitalizacion.Costodelplato')}}</th>
                                    <th scope="col">{{trans('hospitalizacion.Total')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nombres as $value)
                                <tr>
                                    <td>{{$value->id_paciente}}</td>
                                    <td>{{$value->cena}}</td>
                                    <td>{{$value->cant_cena}}</td>
                                    <td>{{$value->precio_cena}}$</td>
                                    <td>{{$value->precio_cena}}$</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</div>



@endsection