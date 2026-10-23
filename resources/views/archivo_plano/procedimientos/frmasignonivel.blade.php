@extends('archivo_plano.procedimientos.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-6">
          <h3 class="box-title" style="margin-top: 7px">Asignar Procedimientos a Nivel</h3>
        </div><br><hr>

    </div>
  </div> 
  <!-- /.box-header -->
    <div class="content">
          <form method="POST" action="{{route('procedimientos.asigno_nivel')}}">
               {{ csrf_field() }}
            <div class="row">
              <div class="col-md-12">
                <label>Procedimiento</label><br>
                <input type="text" name="proced_ni" id="proced_ni" class="form-control input-lg" placeholder="Buscar Procedimiento" />
                <div id="proced_list">
                </div>
              </div>
              <div class="col-md-4"><br>
                <label>Nivel de Convenio</label><br>
                <select name="txtnivel" class="form-control">
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                </select>
              </div>
              <div class="col-md-4"><br>
                <label>Des Conv</label><br>
                <select name="txtdes" class="form-control">
                  <option value="SNSENE2015N1">SNSENE2015N1</option>
                  <option value="SNSENE2015N2">SNSENE2015N2</option>
                  <option value="SNSENE2015N3">SNSENE2015N3</option>
                </select>
              </div>
              <div class="col-md-4"><br>
                <label>Anexo</label><br>
                <select name="txtanexo" class="form-control">
                  <option value="SNSENE2015N1">SNSENE2015N1</option>
                  <option value="SNSENE2015N2">SNSENE2015N2</option>
                  <option value="SNSENE2015N3">SNSENE2015N3</option>
                </select>
              </div>

              <div class="col-md-4"><br>
                <label>UVR1</label><br>
                <input type="text" name="uvr1" placeholder="Valor UVR1" class="form-control">
              </div>
              <div class="col-md-4"><br>
                <label>UVR2</label><br>
                <input type="text" name="uvr2" placeholder="Valor UVR2" class="form-control">
              </div>
              <div class="col-md-4"><br>
                <label>UVR3</label><br>
                <input type="text" name="uvr3" placeholder="Valor UVR3" class="form-control">
              </div>

              <div class="col-md-4"><br>
                <label>PRC1</label><br>
                <input type="text" name="prc1" placeholder="Valor PRC1" class="form-control">
              </div>
              <div class="col-md-4"><br>
                <label>PRC2</label><br>
                <input type="text" name="prc2" placeholder="Valor PRC2" class="form-control">
              </div>
              <div class="col-md-4"><br>
                <label>PRC3</label><br>
                <input type="text" name="prc3" placeholder="Valor PRC3" class="form-control">
              </div>

              <div class="col-md-4"><br>
                <label>UVR1A</label><br>
                <input type="text" name="uvr1a" placeholder="Valor UVR1A" class="form-control">
              </div>
              <div class="col-md-4"><br>
                <label>UVR2A</label><br>
                <input type="text" name="uvr2a" placeholder="Valor UVR2A" class="form-control">
              </div>
              <div class="col-md-4"><br>
                <label>UVR3A</label><br>
                <input type="text" name="uvr3a" placeholder="Valor UVR3A" class="form-control">
              </div>

              <div class="col-md-4"><br>
                <label>PRC1A</label><br>
                <input type="text" name="prc1a" placeholder="Valor UVR3A" class="form-control">
              </div>
              <div class="col-md-4"><br>
                <label>PRC2A</label><br>
                <input type="text" name="prc2a" placeholder="Valor PRC2A" class="form-control">
              </div>
              <div class="col-md-4"><br>
                <label>PRC3A</label><br>
                <input type="text" name="prc3a" placeholder="Valor PRC3A" class="form-control">
              </div>

              <div class="col-md-6"><br>
                <label>Separado</label><br>
                <select name="txtseparado" class="form-control">
                  <option value="F">F</option>
                </select>
              </div>
              <div class="col-md-6"><br>
                <label>Estado</label><br>
                <select name="txtestado" class="form-control">
                  <option value="1">Activo</option>
                  <option value="0">Inactivo</option>
                </select>
              </div>

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

<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<script>
$(document).ready(function(){

 $('#proced_ni').keyup(function(){ 
        var query = $(this).val();
        if(query != '')
        {
         var _token = $('input[name="_token"]').val();
         $.ajax({
          url:"{{ route('procedimientosd.fetch') }}",
          method:"POST",
          data:{query:query, _token:_token},
          success:function(data){
           $('#proced_list').fadeIn();  
                    $('#proced_list').html(data);
          }
         });
        }
    });

    $(document).on('click', 'li', function(){  
        $('#proced_ni').val($(this).text());  
        $('#proced_list').fadeOut();
         $("[id=cierrate]").hide();
        //$( "#proced_ni" ).prop( "disabled", true );
        $(".dropdown-menu").hide();

    });  

});
</script>

@endsection