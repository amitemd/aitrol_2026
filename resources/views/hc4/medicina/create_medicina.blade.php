<style type="text/css">
    .centered{
      text-align: center;
    }

    .boton-create{
      font-size: 15px ;
      width: 100%;
      height: 100%;
      background-color: #004AC1;
      color: white;
      border-radius: 5px;
    }

</style>

<section class="content" style="padding-left: 0px; padding-right: 0px;padding-top: 0px;">
  <div class="modal-header" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004ac1;">
      <h4 class="modal-title" id="myModalLabel">Crear Medicina</h4>
     <!-- <div class="col-3" style="text-align: right;">
        <button id="return_crea_edita" class="btn btn-danger" style="color:white; background-color: #004AC1; border-radius: 5px; border: 2px solid white;"> <i class="glyphicon glyphicon-arrow-left" aria-hidden="true">
        </i> &nbsp;Regresar&nbsp;</button>
      </div>-->
  </div>
  <div class="box-body" style="border: 2px solid #004AC1;border-radius: 3px;">
    <form method="POST" id="guard_medicina">
      {{ csrf_field() }}
      <div class="row">
        <div class="form-group col-md-6 col-12 cl_nombre">
          <label for="nombre" class="control-label col-12">Nombre</label>
            <input id="nombre" class="form-control input-sm" type="text" name="nombre"  value="{{old('nombre')}}" maxlength="100">
            <span class="help-block">
                <strong id="str_nombre"></strong>
            </span>
        </div>
        <div class="form-group col-md-6 col-12 cl_cantidad">
          <label for="cantidad" class="control-label col-12">Cantidad a Prescribir</label>
            <input id="cantidad" class="form-control input-sm" type="text" name="cantidad"  value="{{old('cantidad')}}" maxlength="50">
            <span class="help-block">
              <strong id="str_cantidad"></strong>
            </span>
        </div>
        <div class="form-group col-md-12 col-12 cl_dosis">
          <label for="dosis" class="control-label col-12" >Dosis</label>
          <textarea id="dosis" class="form-control " name="dosis">
            {{old('dosis')}}
          </textarea>
          <span class="help-block">
              <strong id="str_dosis"></strong>
          </span>
        </div>
        <div class="form-group col-md-6 col-12 cl_genericos">
          <label for="genericos" class="control-label col-12" >Gen&eacute;ricos </label>
            <select style="width: 100%; height: 100%" id="genericos" class="form-control select2" name="genericos[]" multiple="multiple" data-placeholder="Seleccione" autocomplete="off" @if(old('dieta') != '1') required @endif >
            @foreach($genericos as $generico) 
              <option value="{{$generico->id}}">{{$generico->nombre}}</option>
            @endforeach
            </select>
            <span class="help-block">
              <strong id="str_genericos"></strong>
            </span>
        </div>
        <!--ESTADO-->
        <div class="form-group col-md-3 col-12 cl_dieta">
          <label for="dieta" class="control-label col-12">Es una Recomendacion</label>
            <select id="dieta" name="dieta" class="form-control" required>
              <option @if(old('dieta')== '0') selected @endif value="0">No</option>
              <option @if(old('dieta')== '1') selected @endif value="1">Si</option> 
            </select>  
            <span class="help-block">
              <strong id="str_dieta"></strong>
            </span>
        </div>

        <div class="form-group col-md-3 col-12 cl_iess_medicina">
          <label for="iess_medicina" class="control-label col-12">P&uacute;blica / Privada</label>
            <select id="iess_medicina" name="iess_medicina" class="form-control">
              <option   value="1">Publica</option>
              <option selected value="0">Privada</option> 
            </select>  
            <span class="help-block">
              <strong id="str_iess_medicina"></strong>
            </span>
        </div>

      </div>
      <br>
      <div class="centered">
        <a id="id_crea_medicina" class="btn btn-info boton-create col-md-2 col-4" style="color: white;" onclick="store_medicina();">
            <span class="glyphicon glyphicon-floppy-disk"></span> 
            Crear Medicina
        </a>
      </div>
    </form> 
  </div> 
</section>

<script type="text/javascript">

    $('.select2').select2({
        tags: true,
        createTag: function (params) {
            var term = $.trim(params.term);
            return {
                id: term.toUpperCase()+'xnose',
                text: term.toUpperCase(),
                newTag: true, // add additional parameters
            }
        }
    });

    $('#dieta').on('change', function(){
        dieta =  $('#dieta').val();
        if(dieta == 0){
            $('#genericos').prop("required", true);
        }
        if(dieta == 1){
            $('#genericos').removeAttr("required");
        }
    })


    function store_medicina(){

        $('.cl_nombre').removeClass('has-error');
        $('#str_nombre').text('');

        $('.cl_cantidad').removeClass('has-error');
        $('#str_cantidad').text('');

        $('.cl_dosis').removeClass('has-error');
        $('#str_dosis').text('');

         $.ajax({
            type: 'post',
            url:"{{route('guarda.medicina')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#guard_medicina").serialize(),
            success: function(data){
                //alert("!! MEDICINA ACTUALIZADA !!");
                //$("#area_trabajo").html(data);
                $("#area_trabajo").html(data);
                //location.href = '{{url('inicio/')}}'
                //location.href = "{{URL::previous()}}"
            },
            error: function(data){
              //alert('error al cargar');
                if(data.responseJSON.nombre!=null){
                  $('.cl_nombre').addClass('has-error');
                  $('#str_nombre').text(data.responseJSON.nombre);
                }
                if(data.responseJSON.cantidad!=null){
                  $('.cl_cantidad').addClass('has-error');
                  $('#str_cantidad').text(data.responseJSON.cantidad);
                }
                if(data.responseJSON.dosis!=null){
                  $('.cl_dosis').addClass('has-error');
                  $('#str_dosis').text(data.responseJSON.dosis);
                }
                if(data.responseJSON.genericos!=null){
                  $('.cl_genericos').addClass('has-error');
                  $('#str_genericos').text(data.responseJSON.genericos);
                }
            }
        }); 
    }

</script>

<script type="text/javascript">
    $('#return_crea_edita').click(function(){

      $.ajax({
          type: 'get',
          url:"{{route('agregar_edit.medicina')}}",
          success: function(data){
            $("#info").html(data);
          },
          error: function(data){
            console.log(data);
          }
      });
      
    });
</script>   

