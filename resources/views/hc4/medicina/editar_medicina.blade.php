<style type="text/css">
    .centered{
      text-align: center;
    }

    .boton-edit{
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
      <h4 class="modal-title" id="myModalLabel">Editar Medicina</h4>
      <!--<div class="col-3" style="text-align: right;">
        <button id="edit_crea_edita" class="btn btn-danger" style="color:white; background-color: #004AC1; border-radius: 5px; border: 2px solid white;"> <i class="glyphicon glyphicon-arrow-left" aria-hidden="true">
        </i> &nbsp;Regresar&nbsp;
        </button>
      </div>-->
  </div>
  <div class="box-body" style="border: 2px solid #004AC1;border-radius: 3px;">
    <form method="POST" id="act_medicina">
      {{ csrf_field() }}
      <input type="hidden" name="idmedicina" value="{{$medicina->id}}">
      <div class="row">
        <div class="form-group col-md-6 col-sm-12 col-12 cl_nombre">
          <label for="nombre" class="control-label">Nombre</label>
            <input id="nombre" class="form-control input-sm" type="text" name="nombre" maxlength="100"value=@if(old('nombre')!='')"{{old('nombre')}}" @else"{{$medicina->nombre}}" @endif">
            <span class="help-block">
              <strong id="str_nombre"></strong>
            </span>
        </div>
        <div class="form-group col-md-6 col-sm-12 col-12 cl_cantidad">
          <label for="cantidad" class="control-label">Cantidad a Prescribir</label>
            <input id="cantidad" class="form-control input-sm" type="text" name="cantidad" maxlength="50" value=@if(old('cantidad')!='')"{{old('cantidad')}}"@else"{{$medicina->cantidad}}"@endif">
            <span class="help-block">
              <strong id="str_cantidad"></strong>
            </span>
        </div>
        <div class="form-group col-md-6 col-sm-12 col-12 cl_dosis">
          <label for="dosis" class="control-label">Dosis</label>
          <textarea id="dosis" class="form-control " name="dosis" required>@if(old('dosis')!='') {{old('dosis')}} @else {{$medicina->dosis}} @endif</textarea>
          <span class="help-block">
            <strong id="str_dosis"></strong>
          </span>
        </div>
        <!--ESTADO-->
        <div class="form-group col-md-6 col-sm-12 col-12 cl_estado">
          <label for="estado" class="control-label">Estado</label>
            <select id="estado" name="estado" class="form-control" required>
              <option @if(old('estado')!='') @if(old('estado')== '0') selected @endif @else @if($medicina->estado == '0') selected @endif @endif value="0">INACTIVO</option>
              <option @if(old('estado')!='') @if(old('estado')== '1') selected @endif @else @if($medicina->estado == '1') selected @endif @endif value="1">ACTIVO</option>   
            </select>  
            <span class="help-block">
              <strong id="str_estado"></strong>
            </span>
        </div>
        <div class="form-group col-md-6 col-sm-12 col-12 cl_genericos">
          <label for="genericos" class="control-label">Gen&eacute;ricos </label>
            <select style="width: 100%; height: 100%" id="genericos" class="form-control  select2" name="genericos[]" multiple="multiple" data-placeholder="Seleccione" autocomplete="off" 
              @if(old('dieta')!='') 
                @if(old('dieta')== '0') 
                  required 
                @endif 
              @else 
                @if($medicina->dieta == '0') 
                  required 
                @endif 
              @endif>
              @foreach($genericos as $generico) 
                @php
                  $gid = $medicina_principio->where('id_principio_activo',$generico->id)->first(); 
                @endphp 
                <option @if(!is_null($gid)) selected @endif value="{{$generico->id}}">
                  {{$generico->nombre}}
                </option>
              @endforeach
            </select>
            <span class="help-block">
              <strong id="str_genericos"></strong>
            </span>
        </div>
        <div class="form-group col-md-3 col-sm-6 col-12  cl_dieta">
          <label for="dieta" class="control-label col-12">Es una Recomendacion</label>
            <select id="dieta" name="dieta" class="form-control" required>
              <option @if(old('dieta')!='') @if(old('dieta')== '0') selected @endif @else @if($medicina->dieta == '0') selected @endif @endif value="0">
                No
              </option>
              <option @if(old('dieta')!='') @if(old('dieta')== '1') selected @endif @else @if($medicina->dieta == '1') selected @endif @endif value="1">
               Si
              </option>
            </select>  
            <span class="help-block">
              <strong id="str_dieta"></strong>
            </span>
        </div>
        <div class="form-group col-md-3 col-sm-6 col-12 cl_iess_medicina">
          <label for="iess_medicina" class="control-label col-12">P&uacute;blica / Privada</label>
            <select id="iess_medicina" name="iess_medicina" class="form-control" required>
              <option @if ($medicina->iess == '1') selected @endif  value="1">Publica</option>
              <option @if ($medicina->iess == '0') selected @endif value="0">Privada</option> 
              <option @if ($medicina->iess == '2') selected @endif value="2">IESS</option> 
            </select>  
            <span class="help-block">
              <strong id="str_iess_medicina"></strong>
            </span>
        </div>
      </div>
      <br>
      <center>
        <div class="col-md-2 col-sm-5 col-5">
          <a id="crea" class="btn btn-info boton-edit"  style="color: white;" onclick="actualiza_medicina();">  
            <span class="glyphicon glyphicon-floppy-disk"></span>
            Actualizar
          </a>
        </div>
      </center>
    </form> 
  </div> 
</section>

<script type="text/javascript">

    $(document).ready(function() {

    });
    
    function actualiza_medicina(){
         $.ajax({
            type: 'post',
            url:"{{route('actualiza_medicina_hc4')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#act_medicina").serialize(),
            success: function(data){
                //alert("!! MEDICINA ACTUALIZADA !!");
                //$("#area_trabajo").html(datahtml);
                //location.href = '{{url('inicio/')}}'
                //location.href = "{{URL::previous()}}"
                $("#area_trabajo").html(data);
            },
            error:  function(data){
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
                if(data.responseJSON.estado!=null){
                  $('.cl_estado').addClass('has-error');
                  $('#str_estado').text(data.responseJSON.estado);
                }

            }
        }); 
    }

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

</script>

<script type="text/javascript">
    $('#edit_crea_edita').click(function(){

      $.ajax({
          type: 'get',
          url:"{{route('editar.medicina_hc4')}}",
          success: function(data){
            $("#info").html(data);
          },
          error: function(data){
            console.log(data);
          }
      });
      
    });
</script>   




