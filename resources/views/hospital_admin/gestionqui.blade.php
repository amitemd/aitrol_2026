@extends('hospital_admin.base')
@section('action-content')

<div class="row">
  <div class="col-md-12">
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Quir&oacute;fano</h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="collapseCardExample">
        <div class="card-body">

          <form>
            <div class="form-row">
              <div class="col-md-6">
                <h6 class="font-weight-bold text-primary">Paciente</h6>
              </div>
              <div class="col-md-6 form-group row">
                <label for="inputPassword" class="col-sm-3 col-form-label">Buscar</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control form-control-sm" id="filtrar" placeholder="Datos pacientes">
                </div>
              </div>
            </div>
          </form>

          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered" >
                  <tr class="text-dark">
                    <th>Fecha Inicio:</th>
                    <th>Fecha Fin:</th>
                    <th>Nombres:</th>
                    <th>Apellidos:</th>
                    <th>Tipo:</th>
                    <th>Seguro:</th>
                    <th>Doctor:</th>
                    <th>Precio Operacion:</th>
                    <th>Estado:</th>
                  </tr>
                  @foreach($agenda as $value)
                  <tbody class="buscar">
                    <td>{{$value->fechaini}}</td>
                    <td>{{$value->fechafin}}</td>
                    <td>{{$value->paciente->nombre1}} {{$value->paciente->nombre2}}</td>
                    <td>{{$value->paciente->apellido1}} {{$value->paciente->apellido2}}</td>
                    <td>{{$value->observaciones}}</td>
                    <td>{{$value->paciente->seguro->nombre}}</td>
                    <td>{{$value->doctor->apellido1}} {{$value->doctor->apellido2}} {{$value->doctor->nombre1}} {{$value->doctor->nombre2}}</td>
                    <td>${{$value->costo}}</td>
                    <td><a href="{{ route('hospital_admin.resultadoquirofano',['id'=>$value->id])}}" class="btn btn-sm btn-warning">Editar Estado</a></td>
                  </tr>
                </tbody>
                @endforeach
              </table>
              {{ $agenda->links() }}
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
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
</script>

<script type="text/javascript">
  function buscar(){
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
</script>

<script type="text/javascript">

  function buscar(){
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
</script>

<script type="text/javascript">
$(document).ready(function () {
 
    (function ($) {
 
        $('#filtrar').keyup(function () {

          var rex = new RegExp($(this).val(), 'i');
          $('.buscar tr').hide();
          $('.buscar tr').filter(function () {
            return rex.test($(this).text());
          }).show();
 
        })
 
    }(jQuery));
 
});
</script>

@endsection