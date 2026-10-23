@extends('hc_admision.controldoc.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Listado de Documentos</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{route('controldoc.create')}}">Agregar Documento</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      <form method="POST" action="{{route('controldoc.search')}}">
        {{ csrf_field() }}
        <!--proc/consulta-->
        <div class="form-group col-md-4 {{ $errors->has('proc_consul') ? ' has-error' : '' }}">
          <label for="proc_consul" class="col-md-3 control-label">Con/Proc</label>
          <div class="col-md-7">
            <select id="proc_consul" name="proc_consul" class="form-control" required onchange="buscar();">
              <option @if($proc_consul=='0') selected @endif value="0">Consulta</option>
              <option @if($proc_consul=='1') selected @endif value="1">Procedimiento</option>
            </select>     
          </div>
        </div>
        <button type="submit" class="btn btn-primary" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span>Buscar</button>
         
      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div >
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" >
            <thead>
              <tr >
                <th >Orden</th>
                <th >Nombre</th>
                <th >Código</th>
                <th >Con/Proc</th>
                <th >Tipo Seguro</th>
                <th >Seguro</th>
                <th >Sub-Seguro</th>
                <th >Dpto. Entrega</th>
                <th >Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($documentos as $value)
                <tr role="row" class="odd">
                  <td ><input style="width: 50%; text-align: center;" type="number" name="secuencia" value="{{$value->secuencia}}" min="1" onchange="actualiza_sec(this.value,{{$value->id}});"></td>
                  <td >{{ $value->nombre}}</td>
                  <td >{{ $value->codigo}}</td>
                  <td >@if($value->proc_consul=='0') {{"Consulta"}} @elseif($value->proc_consul=='1') {{"Procedimiento"}} @elseif($value->proc_consul=='2') {{"Ambos"}} @endif</td>
                  <td >@if($value->tipo_seguro=='0') {{"Público"}} @elseif($value->tipo_seguro=='1') {{"Privado"}} @elseif($value->tipo_seguro=='2') {{"Particular"}} @endif</td>
                  <td >{{ $value->snombre}}</td>
                  <td >{{ $value->subnombre}}</td>
                  <td >{{ $value->tnombre}}</td>
                  <td >  
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{ route('controldoc.edit', ['id' => $value->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                        Actualizar
                    </a>
                  </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al {{count($documentos)}} de {{$documentos->total()}} Registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $documentos->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>

  <script type="text/javascript">



  $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
  

function buscar()
{
  var obj = document.getElementById("boton_buscar");
  obj.click();
}

function actualiza_sec(value, id)
{
  
    location.href ="{{url('controldoc/act_sec')}}/"+id+"/"+value;

}


 </script> 
@endsection