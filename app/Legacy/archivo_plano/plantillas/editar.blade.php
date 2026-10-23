@extends('archivo_plano.plantillas.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-6">
          <h3 class="box-title" style="margin-top: 7px">Actualizar Plantillas</h3>
        </div><br><hr>
 
    </div>
  </div> 
  <!-- /.box-header -->
    <div class="content">
          <form method="post" id="dynamic_form">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-md-6">
                <label>Código</label><br>
                <input type="hidden" name="chivo" value="{{ $fila->id }}">
                <input type="text" name="txtcodigo" value="{{ $fila->codigo }}" class="form-control" >
              </div>
              <div class="col-md-6">
                <label>Descripción</label><br>
                <input type="text" name="txtdescripcion" value="{{ $fila->descripcion }}" placeholder="Descripción"  class="form-control">
              </div>
              <div class="col-md-6"><br>
                <label>Descripción Completa</label><br>
                <input type="text" name="txtdescripcioncompleta" value="{{ $fila->desc_comp }}"  class="form-control">
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

<!-- bloque 5 -->
  
     <br />
   <div class="table-responsive">
                
                 <span id="result"></span>
                 <table class="table table-bordered table-striped" id="user_table">
               <thead>
                <tr>
                    <th width="30%">Item</th>
                    <th width="15%">Cantidad</th>
                    <th width="15%">Honorario</th>
                    <th width="15%">Orden</th>
                    <th width="15%">Separado</th>
                    <th width="10%">Action</th>
                </tr>
               </thead>
               <tbody>
               <div style="display:none">
               {{ $conti=1 }}
               </div>
               @foreach($item as $fila)

               <tr>
                    <td>
                        <input type="text" value="Codigo: {{ $fila->codigo }}| {{ $fila->descripcion }}"  onclick="pason({{  $conti }})" name="id_procedimiento[]" id="proced_ni1" class="form-control" />
                        <div id="proced_list1"></div>
                    </td>
                    <td>
                        <input type="text" name="cantidad[]" value="{{ $fila->cantidad }}" id="cantidad1" class="form-control" />
                    </td>
                    <td>
                        <select name="honorario[]" class="form-control"><option value="Ninguno">Ninguno</option><option value="100">100%</option><option value="50">50%</option></select>
                    </td>
                    <td>
                        <input type="number"  value="{{ $fila->orden }}" name="orden[]" class="form-control" />
                    </td>
                    <td>
                        <select name="separado[]" class="form-control">
                            <option value="No">No</option>
                            <option value="Si">Si</option>
                        </select>
                    </td>
                    
              </tr>
              <div style="display:none">
              {{ $conti++ }}
              </div>
            @endforeach
               </tbody>
               <tfoot>
                <tr>
                                <td colspan="5" align="right">&nbsp;</td>
                                <td>
             
                  <input type="submit" name="save" id="save" class="btn btn-primary" value="Guardar" />
                 </td>
                </tr>
               </tfoot>
           </table>
                
   </div>


</div>
<br>

              <div class="col-md-6"><br></div><div id="proced_list"></div>
              <!-- <input type="submit" name="" value="Guardar" class="btn btn-success">-->
              </div>
            
          </form>
      
    </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<script>
$(document).ready(function(){

 var count ={{ $itemc+1 }};

 dynamic_field(count);

 function dynamic_field(number)
 {
  html = '<tr>';

        html += '<td><input type="text" onclick="pason('+count+')" name="id_procedimiento[]" id="proced_ni'+count+'" class="form-control" /><div id="proced_list'+count+'"></div></td>';
        html += '<td><input type="text" name="cantidad[]"  id="cantidad'+count+'" class="form-control" /></td>';
        html += '<td><select name="honorario[]" class="form-control"><option value="Ninguno">Ninguno</option><option value="100">100%</option><option value="50">50%</option></select></td>';
        html += '<td><input type="number" name="orden[]" class="form-control" /></td>';
        html += '<td><select name="separado[]" class="form-control"><option value="No">No</option><option value="Si">Si</option></select></td>';
        if(number > 1)
        {
            html += '<td><button type="button" name="remove" id="" class="btn btn-danger remove">Eliminar</button></td></tr>';
            $('tbody').append(html);
        }
        else
        {   
            html += '<td><button type="button" name="add" id="add" class="btn btn-success">Nuevo Item</button></td></tr>';
            $('tbody').html(html);
        }
 }

 $(document).on('click', '#add', function(){
  count++;
  dynamic_field(count);

 });

 $(document).on('click', '.remove', function(){
  count--;
  $(this).closest("tr").remove();
 });

 $('#dynamic_form').on('submit', function(event){
        event.preventDefault();
        $.ajax({
            url:'{{ route("plantillas.update") }}',
            method:'post',
            data:$(this).serialize(),
            dataType:'json',
            beforeSend:function(){
                $('#save').attr('disabled','disabled');
            },
            success:function(data)
            {
                if(data.error)
                {
                    var error_html = '';
                    for(var count = 0; count < data.error.length; count++)
                    {
                        error_html += '<p>'+data.error[count]+'</p>';
                    }
                    $('#result').html('<div class="alert alert-danger">'+error_html+'</div>');
                }
                else
                {
                    dynamic_field(1);
                    $('#result').html('<div class="alert alert-success">'+data.success+'</div>');
                }
                $('#save').attr('disabled', false);
            }
        })
 });
});
</script>
<script>

function pason(para){
  var numi = para;
$(document).ready(function(){
 $('#proced_ni'+numi).keyup(function(){ 
        var query = $(this).val();
        if(query != '')
        {
         var _token = $('input[name="_token"]').val();
         $.ajax({
          url:"{{ route('plantillas.search') }}",
          method:"POST",
          data:{query:query, _token:_token, contador:numi},
          success:function(data){
           $('#proced_list'+numi).fadeIn();  
                    $('#proced_list'+numi).html(data);
          }
         });
        }
    });

 

});
}

function listo(paso, t){
  var numi2 = paso;
  $('#proced_ni'+numi2).val($(t).text());  
        $('#proced_list'+numi2).fadeOut();
         $("[id=cierrate]").hide();
         $('#cantidad'+numi2).focus();
        $( "#proced_ni" ).prop( "disabled", true );
        document.getElementById('cierrate').style.display="none";
        $(".dropdown-menu").hide();
        return false;
}
</script>

@endsection