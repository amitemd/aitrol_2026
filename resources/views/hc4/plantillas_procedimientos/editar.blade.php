<section class="content">
    <div class="box "> 
        <div class="box-header ">
            <div class="col-12" style="background-color: #004AC1; padding: 10px">
                <label class="box-title" style="color: white; font-size: 20px">Editar Procedimiento</label>
            </div>
        </div>

    <div class="box-body">
       
        <form id="actualizar_frm" class="form-vertical" role="form" method="POST" >
         
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="id" value="{{ $procedimiento_completo->id }}">
           <div class="col-md-12">
               <center>
               <button type="button" class="btn btn-success" style="margin-bottom: 10px" onclick="plantilla_proc();" >
                    <span > 
                        Regresar
                    </span>
                </button>
                </center>
           </div>

            <div class="form-group col-md-12{{ $errors->has('nombre_general') ? ' has-error' : '' }}">
                <div class="row">
                <label for="nombre_general" class="col-md-4 " style="text-align: right;">Nombre a Mostrar</label>
                <div class="col-md-7">
                    <input id="nombre_general" type="text"  class="form-control" name="nombre_general" value="{{ $procedimiento_completo->nombre_general }}" required autofocus>
                    @if ($errors->has('nombre_general'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nombre_general') }}</strong>
                        </span>
                    @endif 
                </div>
                </div>
            </div>

            <div class="form-group col-md-12{{ $errors->has('nombre_completo') ? ' has-error' : '' }}">
                <div class="row">
                <label for="nombre_completo" class="col-md-4 " style="text-align: right;">Nombre Completo</label>
                <div class="col-md-7">
                    <input id="nombre_completo" type="text"  class="form-control" name="nombre_completo" value="{{ $procedimiento_completo->nombre_completo }}" required autofocus>
                    @if ($errors->has('nombre_completo'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nombre_completo') }}</strong>
                        </span>
                    @endif 
                </div>
                </div>
            </div>

            <div class="form-group col-md-12{{ $errors->has('estado_anestesia') ? ' has-error' : '' }}">
                <div class="row">
                <label for="estado_anestesia" class="col-md-4 " style="text-align: right;">Posee Record Anestesico</label>
                <div class="col-md-7">
                    <select id="estado_anestesia" name="estado_anestesia" class="form-control" >
                        <option {{$procedimiento_completo->estado_anestesia == 0 ? 'selected' : ''}} value="0">No</option>
                        <option {{$procedimiento_completo->estado_anestesia == 1 ? 'selected' : ''}} value="1">Si</option>            
                    </select>  
                    @if ($errors->has('estado_anestesia'))
                        <span class="help-block">
                            <strong>{{ $errors->first('estado_anestesia') }}</strong>
                        </span>
                    @endif
                </div>
                </div>
            </div>

            <div class="form-group col-md-12{{ $errors->has('estado') ? ' has-error' : '' }}">
                <div class="row">
                <label for="estado" class="col-md-4 " style="text-align: right;">Estado</label>
                <div class="col-md-7">
                    <select id="estado" name="estado" class="form-control" >
                        <option {{$procedimiento_completo->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                        <option {{$procedimiento_completo->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>            
                    </select>  
                    @if ($errors->has('estado'))
                        <span class="help-block">
                            <strong>{{ $errors->first('estado') }}</strong>
                        </span>
                    @endif
                </div>
                </div>
            </div>

            <div class="form-group col-md-12{{ $errors->has('id_grupo_procedimiento') ? ' has-error' : '' }}">
                <div class="row">
                <label for="id_grupo_procedimiento" class="col-md-4 " style="text-align: right;">Grupo al que pertenece</label>
                <div class="col-md-7">
                    <select id="id_grupo_procedimiento" name="id_grupo_procedimiento" class="form-control"  required>
                        @foreach($grupo_procedimiento as $value)
                        <option @if($procedimiento_completo->id_grupo_procedimiento == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                        @endforeach            
                    </select>  
                    @if ($errors->has('id_grupo_procedimiento'))
                        <span class="help-block">
                            <strong>{{ $errors->first('id_grupo_procedimiento') }}</strong>
                        </span>
                    @endif
                </div>
                </div>
            </div>
                                
            <!--Tecnicas-->
            <div class="form-group col-md-12{{ $errors->has('tecnica_quirurgica') ? ' has-error' : '' }}">
                <div class="row">
                <label for="tecnica_quirurgica" class="col-md-4 " style="text-align: right;">Tecnicas Quirugicas</label>
                <div class="col-md-7">
                <div id="ttecnica_quirurgica<?php echo e($procedimiento_completo->id); ?><?php echo e(date('his')); ?>" style="border: solid 1px;min-height: 200px;border-radius:3px;">
                <?php echo strip_tags($procedimiento_completo->tecnica_quirurgica); ?>
                </div> 
                <input type="hidden" name="tecnica_quirurgica" id="tecnica_quirurgica<?php echo e($procedimiento_completo->id); ?><?php echo e(date('his')); ?>"> 
                </div>
                </div>
            </div>
            <center>
            <div class="form-group col-md-12">
                <div class="col-md-6 col-md-offset-4">
                    <button type="button" class="btn btn-info" style="background-color: #004AC1" onclick="actualizar_plantilla_proc()">
                    Actualizar
                    </button>
                </div>
            </div> 
            </center>   
        </form>
        
    </div>  
</section>


<script>
  /*  tinymce.init({
        selector: '#tecnica_quirurgica<?php echo e($procedimiento_completo->id); ?><?php echo e(date('his')); ?>'
    }); 
    $(document).ready(function() 
    {
        //$(".breadcrumb").append('<li><a href="{{ url('tecnicas') }}" style="color: blue;"></i> Procedimientos</a></li>');
        //$(".breadcrumb").append('<li class="active">Editar</li>');
    });
*/
      function actualizar_plantilla_proc(){
            //alert("yes");
            $.ajax({
            type: "post",
            url: "{{route('hc4/plantilla_proc.update')}}",
            datatype: "json", 
            data: $("#actualizar_frm").serialize(),
            
            success: function(data){
                //console.log(data);
                //$("#area_trabajo").html(data);
                plantilla_proc();
            },
            error:  function(){
                alert('error al cargar');
            }
        }); 
    }


  tinymce.init({
    selector: '#ttecnica_quirurgica<?php echo e($procedimiento_completo->id); ?><?php echo e(date('his')); ?>',
    inline: true,
    menubar: false,
    content_style: ".mce-content-body {font-size:14px;}",
    //readonly: 1,
      
      setup: function (editor){
            editor.on('init', function (e){
               var ed = tinyMCE.get('ttecnica_quirurgica<?php echo e($procedimiento_completo->id); ?><?php echo e(date('his')); ?>');
                $("#tecnica_quirurgica<?php echo e($procedimiento_completo->id); ?><?php echo e(date('his')); ?>").val(ed.getContent());
            });
      },
       
      init_instance_callback: function (editor){
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('ttecnica_quirurgica<?php echo e($procedimiento_completo->id); ?><?php echo e(date('his')); ?>');
                $("#tecnica_quirurgica<?php echo e($procedimiento_completo->id); ?><?php echo e(date('his')); ?>").val(ed.getContent());
                cambiar_receta_2(); 
              
            });
      }
  });





</script> 