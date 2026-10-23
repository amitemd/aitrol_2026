@extends('contable.plan_cuentas.base')
@section('action-content')
<!-- Ventana modal editar -->
<style type="text/css">
  #arbol a:hover {
    color: #3c8dbc;
    cursor: pointer;
  }

  #arbol a {
    color: #3c8dbc;
    mouse: pointer;
    font-size: 12px;
  }

  #arbol ul {
    list-style-type: none;
  }

  .active {
    display: block !important;
  }

  .treeview-menu {
    display: none;
  }

  #tabla_elementos {
    font-size: 12px;
  }
</style>
<!-- Main content -->
<section class="content">
  <div class="box">
    <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
      <div class="col-md-6">
        <h3 class="box-title">Plan de Cuentas</h3>
      </div>
      <div class="col-md-6 btn pull-right">
        <button type="button" class="btn btn-primary btn-gray" onclick="javascript:location.href='{{ route('plan_cuentas.exportar') }}'" id="btn_exportar">
          <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
        </button>
      </div>
    </div>

    <!-- /.box-header -->
    <div class="box-body dobra">
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12">
            <div class="col-md-4">
              <div id="arbol">
                <ul data-widget="tree">
                  @foreach($principales as $principal)
                  <li>
                    <a class="treeview" onclick="llamado(this)" id="{{$principal->id_plan}}"><i class="fa fa-plus elemento" aria-hidden="true"></i> <i class="fa fa-minus oculto elemento2" aria-hidden="true"></i> {{$principal->nombre}}</a>
                    <ul class="treeview-menu">
                      @php
                      $hijos = new Sis_medico\Http\Controllers\contable\Plan_CuentasController();
                      $elemento = $hijos->hijos($principal->id_plan);
                      echo $elemento;
                      @endphp
                    </ul>
                  </li>
                  @endforeach
                </ul>
              </div>
            </div>
            <div class="col-md-8">
              <div class="col-md-12">
                <h3>Elementos</h3>
                <div class="col-md-12" id="elementos">
                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr>
                        <th width="20%">Codigo</th>
                        <th width="40%">Nombre</th>
                        <th width="20%">Modificado</th>
                        <th width="20%">Usu. Modifica</th>
                      </tr>
                    </thead>
                    <tbody>
                      @for($i = 0; $i<=5; $i++) <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>
                        @endfor
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="col-md-12">
                <h3>Información de la cuenta</h3>
                <div class="col-md-12" id="informacion">
                  <h4>Catalogo de Plan de Cuentas: </h4>
                  <h6>Información General</h6>
                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <tbody>
                      <tr>
                        <td><b>Codigo:</b></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td><b>Nombre:</b></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td><b>Tipo:</b></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td><b>Estado:</b></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td><b>Naturaleza:</b></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td><b>Cierre de año:</b></td>
                        <td></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.box-body -->
  </div>
</section>

<script type="text/javascript">
  $('#editMaxPacientes').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
  });
  var toggler = document.getElementsByClassName("treeview");
  var i;

  for (i = 0; i < toggler.length; i++) {
    toggler[i].addEventListener("click", function() {
      this.parentElement.querySelector(".treeview-menu").classList.toggle("active");
      this.parentElement.querySelector(".elemento").classList.toggle("oculto");
      this.parentElement.querySelector(".elemento2").classList.toggle("oculto");
      this.classList.toggle("caret-down");
    });
  }

  function llamado(e) {
    e.id;
    $.ajax({
      type: 'post',
      url: "{{route('plan_cuentas.elementos')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'html',
      data: {
        'id_plan': e.id
      },
      success: function(data) {
        $('#elementos').html(data);

      },
      error: function(data) {
        console.log(data);
      }
    });

    $.ajax({
      type: 'post',
      url: "{{route('plan_cuentas.informacion')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'html',
      data: {
        'id_plan': e.id
      },
      success: function(data) {
        $('#informacion').html(data);
      },
      error: function(data) {
        console.log(data);
      }
    });
  }
</script>
<!-- /.content -->

@endsection