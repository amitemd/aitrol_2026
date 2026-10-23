@extends('servicios_generales.limpieza_banos.base')
@section('action-content')
<style>
    .sepa{
        margin-top: 5px;
    }
</style>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
                <h5><b>Reporte de Limpieza de Baños</b></h5>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button class="btn btn-danger" onclick="goBack()"><i class="fa fa-reply" aria-hidden="true"></i> Regresar</button>
            </div>
        </div>
        <div class="box-body">
          <div style="display:flex;flex-direction:row;flex-wrap: wrap;gap: 10px;justify-content: center;">
              @foreach($nombre_piso as $value)
            <div class="col-md-3">
              <a style="font-size: 16px; text-align: center;font-weigth:bold;width:100%;height:100%;text-justify: auto" href="{{route('limpieza_banos.excel',['id'=>$value->id])}}" class="btn btn-primary"> {{$value->nombre}} </a>
            </div><br><br>
            @endforeach
          </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    function goBack() {
        window.history.back();
    }
</script>
@endsection
