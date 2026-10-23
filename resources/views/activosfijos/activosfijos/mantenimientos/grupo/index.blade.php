@extends('activosfijos.mantenimientos.grupo.base')
@section('action-content')
<!-- Ventana modal editar -->

<style type="text/css">
  #arbol a:hover{
    color: #3c8dbc;
    cursor: pointer;
  }
  #arbol a{
    color: #3c8dbc;
    mouse: pointer;
    font-size: 12px;
  }
  #arbol ul{
    list-style-type: none;
  }

  .active {
    display: block !important;
  }
  .treeview-menu {
    display: none;
  }
  #tabla_elementos{
    font-size: 12px;
  }
</style>
  <!-- Main content -->
  <section class="content">
    <div class="box" >
      <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
          <div class="col-md-6">
            <h3 class="box-title">Grupo</h3>
          </div>
      </div>

      <!-- /.box-header -->
      <div class="box-body">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="row">
            <div class="col-md-4"> 
              <div class="col-md-3 col-md-offset-9">
                <button type="button" class="btn btn-xs btn-primary" onclick="nuevo();">Agregar Nuevo</button>
              </div>
            </div>
          </div>
            <div class="row">
              <div class="table-responsive col-md-12">
                <div class="col-md-4">
                  <div id="arbol" >
                    @php echo $arbol; @endphp
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="col-md-12">
                    <h3>Informaci&oacute;n</h3>
                    <div class="col-md-12" id="informacion">
                      <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <tbody>
                          <tr>
                            <td style="width:50px"><b>C&oacute;digo:</b></td>
                            <td ></td>
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
                            <td><b>Padre:</b></td>
                            <td></td>
                          </tr>
                          <tr>
                            <td><b>Estado:</b></td>
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  <script type="text/javascript">
    $('#editMaxPacientes').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
    });
    function armar_arbol(){
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
    }
    abrir_arbol();

    function abrir_arbol(){
      var toggler = document.getElementsByClassName("treeview");
      var i;

      for (i = 0; i < toggler.length; i++) {
        toggler[i].addEventListener("click", function() {
          this.parentElement.querySelector(".treeview-menu").classList.toggle("active");
          this.parentElement.querySelector(".elemento").classList.toggle("oculto");
          this.parentElement.querySelector(".elemento2").classList.toggle("oculto");
          this.classList.toggle("caret-open");
        });
      }
    }

    function llamado(e){ 
      e.id; 

      $.ajax({
        type: 'post',
        url:"{{route('activofjo.grupo.info')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'html',
        data: {'id':e.id},
        success: function(data){
            $('#informacion').html(data);
        },
        error: function(data){
            console.log(data);
        }
      });
    }

    function nuevo(){  
      $.ajax({
        type: 'post',
        url:"{{route('activofjo.grupo.nuevo')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'html', 
        success: function(data){
            $('#informacion').html(data);
        },
        error: function(data){
            console.log(data);
        }
      });
    }

    function recargar(){ 
      $.ajax({
        type: 'post',
        url:"{{route('activofjo.grupo.reload')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'html',
        success: function(data){
            $('#arbol').html(data);
            abrir_arbol();
        },
        error: function(data){
            console.log(data);
        }
      });
    }
  </script>
  <!-- /.content -->


@endsection
