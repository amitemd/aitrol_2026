@extends('seguro.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Lista de Subseguros</h3><hr>
          <h3 class="box-title">Seguro Principal: @php echo $seguro[0]->nombre; @endphp</h3><br>
          <h3 class="box-title">Descripcion: @php echo $seguro[0]->descripcion; @endphp</h3>
        </div>
        <div class="col-sm-4">
          <a class="btn btn-primary" href="{{route('subseguro.create', ['id' => $seguro[0]->id])}}">Agregar nuevo Subseguro</a>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Nombre</th>
                <th width="30%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Descripcion</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Fecha de Creado</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Birthdate: activate to sort column ascending">Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($subseguro as $value)
                <tr role="row" class="odd">
                  <td class="sorting_1">{{ $value->nombre}}</td>
                  <td >{{ $value->descripcion}}</td>
                  <td >{{ $value->created_at }}</td>
                  <td>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('subseguro.edit', ['id' => $value->id]) }}" class="btn btn-warning col-md-8 col-sm-8 col-xs-8 btn-margin">
                        Actualizar
                        </a>
                  </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
@endsection