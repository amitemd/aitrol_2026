@extends('contable.rh_reporte_banco.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  

  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Nomina</a></li>
        <li class="breadcrumb-item active" aria-current="page">Reporte Banco</li>
      </ol>
    </nav>
    <div class="box">
      <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" for="title">REPORTE BANCO - EMPLEADOS POR EMPRESA</label>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="reporte_banco"> 
          {{ csrf_field() }}
        @php 
          $id_empresa = Session::get('id_empresa');
        @endphp
          <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="id_empresa">Empresa: </label>
          </div>
          <div class="form-group col-md-3 col-xs-10 container-4">
            <select class="form-control" id="id_empresa" name="id_empresa">
                <option value="">Seleccione...</option> 
                @foreach($empresas as $value)
            <option @if($value->id == $id_empresa) selected @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                @endforeach
            </select>
          </div>
          <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="year">Año</label>
          </div>
          <div class="form-group col-md-3 col-xs-10 container-4">
            <select id="year" name="year" class="form-control">
              <option value="">Seleccione...</option>  
              <?php
                for($i=2019;$i<=2030;$i++)
                {
                 echo "<option value='".$i."'>".$i."</option>";
                }
              ?>
            </select>
          </div>
          <div class="form-group col-md-1 col-xs-2">
            <label class="texto" for="mes">Mes</label>
          </div>
          <div class="form-group col-md-3 col-xs-10 container-4">
            <select id="mes" name="mes" class="form-control">
              <option value="">Seleccione...</option>  
              <?php    
                $Meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

                  for ($i=1; $i<=12; $i++) {
                  
                    echo '<option value="'.$i.'">'.$Meses[($i)-1].'</option>';
                  }
              ?>
            </select>
          </div>
          <div class="form-group col-md-4 col-xs-4"> 
            <button type="submit" class="btn btn-primary" formaction="{{route('reporte_datos.banco')}}">
              <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar Archivo Banco
            </button>  
          </div> 
        </form> 
      </div>
      <!--<div class="box box" style="border-radius: 8px;" id="area_trabajo">
      </div>-->
    </div>

  <script src="{{ asset ("/js/jquery-ui.js")}}"></script>
  
  </section>
@endsection
