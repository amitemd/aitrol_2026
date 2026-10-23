@extends('archivo_plano.plantillas.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-6">
          <h3 class="box-title" style="margin-top: 7px">Crear Plantillas</h3>
        </div><br><hr>
 
    </div>
  </div> 
  <!-- /.box-header -->
    <div class="content">
          <form method="POST" action="frmcrear">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-md-6">
                <label>Código</label><br>
                <input type="text" name="txtcodigo" placeholder="Código"  class="form-control">
              </div>
              <div class="col-md-6">
                <label>Descripción</label><br>
                <input type="text" name="txtdescripcion" placeholder="Descripción"  class="form-control">
              </div>
              <div class="col-md-6"><br>
                <label>Descripción Completa</label><br>
                <input type="text" name="txtdescripcioncompleta" placeholder="Descripción Completa"  class="form-control">
              </div>
              <div class="col-md-6"><br>
                <label>Estado</label><br>
                <select name="txtestado" class="form-control">
                  <option value="1">Activo</option>
                  <option value="0">Inactivo</option>
                </select>
              </div>
<br>
<div class="col-md-12"><br>
<h4 align="center" style="color:#00A65A"><i class="fa fa-list"></i> Items de Plantilla</h4><br>
<div class="row">
<div id='TextBoxesGroup'>
<div id="TextBoxDiv1" class="col-md-12">
  <div class="col-md-2"><label>Item #1 : </label><br><input type="text" name="textbox1" id="textbox1" value="" class="form-control"></div>
  <div class="col-md-2"><label>Cantidad: </label><br><input type="text" name="cantidad1" id="textbox1" value=" " class="form-control"></div>
  <div class="col-md-3"><label>Honorario: </label><br><input type="text" name="cantidad1" id="textbox1" value="" class="form-control"></div>
  <div class="col-md-2"><label>Orden: </label><br><input type="text" name="cantidad1" id="textbox1" value="" class="form-control"></div>
  <div class="col-md-3"><label>Separado: </label><br><input type="text" name="orden1" id="textbox1" value="" class="form-control"></div>
  </div>
</div>
</div><br>
<input type='button' value='Agregar Nuevo Item' id='addButton' class="btn btn-info">
<input type='button' value='Eliminar item' id='removeButton' class="btn btn-danger">
<!--<input type='button' value='Get TextBox Value' id='getButtonValue'>-->
</div>
<br>

              <div class="col-md-6"><br>
               <input type="submit" name="" value="Guardar" class="btn btn-success">
              </div>
            </div>
          </form>
      
    </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>

<script type="text/javascript">
$(document).ready(function(){
    var counter = 2;
    $("#addButton").click(function () {        
 
  var newTextBoxDiv = $(document.createElement('div'))
       .attr("id", 'TextBoxDiv' + counter)
       .addClass("col-md-12");
  newTextBoxDiv.after().html('<div class="col-md-2"><label>Item #'+ counter + ' : </label><br>' +
        '<input type="text" name="textbox' + counter + 
        '" id="textbox' + counter + '" value=""  class="form-control"></div><div class="col-md-2"><label>Cantidad: </label><br>' +
        '<input type="text" name="cantidad' + counter + 
        '" id="textbox' + counter + '" value=""  class="form-control"></div><div class="col-md-3"><label>Honorario: </label><br>' +
        '<input type="text" name="cantidad' + counter + 
        '" id="textbox' + counter + '" value=""  class="form-control"></div><div class="col-md-2"><label>Orden: </label><br>' +
        '<input type="text" name="cantidad' + counter + 
        '" id="textbox' + counter + '" value=""  class="form-control"></div><div class="col-md-3"><label>Separado: </label><br>' +
        '<input type="text" name="orden' + counter + 
        '" id="textbox' + counter + '" value=""  class="form-control"></div>');

  newTextBoxDiv.appendTo("#TextBoxesGroup");
  counter++;
     });
     $("#removeButton").click(function () {
  if(counter==2){
          alert("Debe tener mínimo un item");
          return false;
       }   
  counter--;
        $("#TextBoxDiv" + counter).remove();
     });
     $("#getButtonValue").click(function () {
  var msg = '';
  for(i=1; i<counter; i++){
      msg += "\n Textbox #" + i + " : " + $('#textbox' + i).val();
  }
        alert(msg);
     });
  });
</script>

@endsection