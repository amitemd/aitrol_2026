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
<div class="content" id="area_cambiar">
  <div class="content-header">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-11 col-sm-6">
          <h3>{{trans('hospitalizacion.Medicaciondelpaciente')}}</h3>
        </div>
        <div class="col-1">
          <a type="button" href="{{ url()->previous() }}" class="btn btn-primary btn-sm">{{trans('hospitalizacion.Regresar')}}</a>
        </div>
      </div>
    </div>
  </div>
 <div class="col-md-12">
    <div class="box">
      <div class="box-header with-border"style="border-radius: 30px; background-image: linear-gradient(to right, #3352ff 0%, #051eff 100%); margin-bottom: 5px">
        <h1 class="box-title">{{trans('hospitalizacion.PrescripcionDoctor:')}}</h1>
          <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
            <i class="fa fa-minus"></i>
          </button>
        </div>
    </div>
</div>
@endsection