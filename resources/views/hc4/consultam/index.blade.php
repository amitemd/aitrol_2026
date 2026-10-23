<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<style>
/* unvisited link */
a:link {
    color: black;
}

/* visited link */
a:visited {
    color: lightgreen;
}

/* mouse over link */
a:hover {
    color: blue;
}
button{
  width: 100%;
}
</style>

<div class="container-fluid" style="padding-left: 0px; padding-right: 0px;">
  <div class="col-md-12" style="font-family: Helvetica;color: white; margin-top: 5px; padding: 10px; border-radius: 8px; background-image: linear-gradient(to right, #004AC1,#004AC1,#004AC1); margin-bottom: 10px">   
    <form method="POST" action="#">
      <div class="col-12"> 
        <div class="row">
        {{ csrf_field() }}  
          <div class="col-12"> 
            <h1 style="font-size: 15px; margin:0;">
              <b>M&aacute;ster de Consultas y Procedimientos Agendados</b>
            </h1>
          </div>
          <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                <label for="proc_consul" class="col-md-3 control-label">Tipo</label>
                <div class="col-md-9">
                  <select class="form-control input-sm" name="proc_consul" id="proc_consul">
                    <option>Todos</option>
                    <option>Consultas</option>
                    <option>Procedimientos</option>  
                  </select>
                </div>
          </div>
          <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="col-md-3 control-label">Desde</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''"></i>
                </div>   
              </div>
            </div>  
          </div>
          <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha_hasta" class="col-md-3 control-label">Hasta</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''"></i>
                </div>   
              </div>
            </div>  
          </div>
          <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
            <label for="cedula" class="col-md-3 control-label">Cédula</label>
            <div class="col-md-9">
              <div class="input-group">
                <input value="" type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="Cédula" >
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('cedula').value = ''"></i>
                </div>  
              </div>
            </div>
          </div>
          <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
            <label for="nombres" class="col-md-3 control-label">Paciente</label>
            <div class="col-md-9">
              <div class="input-group">
                <input value="" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Nombres y Apellidos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                </div>
              </div>  
            </div>
          </div>
          <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
            <label for="id_doctor1" class="col-md-3 control-label">Doctor</label>
            <div class="col-md-9">
              <select class="form-control input-sm" name="id_doctor1" id="id_doctor1" onchange="buscar();">
                <option value="">Seleccione ...</option>
                
              </select>
            </div>
          </div>
          <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
            <label for="id_seguro" class="col-md-3 control-label">Seguro</label>
            <div class="col-md-9">
              <select class="form-control input-sm" name="id_seguro" id="id_seguro" onchange="buscar();">
                <option value="">Seleccione ...</option>
                
              </select>
            </div>
          </div>
          <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
            <label for="espid" class="col-md-3 control-label">Especialidad</label>
            <div class="col-md-9">
              <select class="form-control input-sm" name="espid" id="espid" onchange="buscar();">
                <option value="">Todos ...</option>
             
              </select>
            </div>
          </div>
          <div class="col-3">
            <button type="submit" class="btn btn-danger" style="color:white; background-color: #004AC1; border-radius: 5px; border: 2px solid white;"> <i class="fa fa-search" aria-hidden="true">
            </i> &nbsp;&nbsp;&nbsp;BUSCAR&nbsp;&nbsp;&nbsp;</button>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div class="modal-body" style="border: 2px solid #004AC1;border-radius: 3px;"> 
    <div class="panel-body">
      <div class="col-12" > 
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="table-responsive col-md-12 col-xs-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Paciente</th>
                  <th>Cédula</th>
                  <th>Doctor</th>
                  <th>Sala</th>
                  <th>Procedimientos</th>
                  <th>Seguro/Convenio</th>
                  <th>Modifica</th>
                  <th>Estado</th>
                  <th>
                    <span data-toggle="tooltip" title="Ambulatorio/Hospitalizado">Amb/Hosp</span>
                  </th>
                  <th>
                    <span data-toggle="tooltip" title="Documentos Pendientes">P</span>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>  
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<div>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

  $(function () {
            
    $('#fecha').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD ',
    });

    $('#fecha_hasta').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD ',
    });

  });   

  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }

</script>