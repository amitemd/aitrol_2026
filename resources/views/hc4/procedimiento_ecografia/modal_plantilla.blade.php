


    <div class="row">
        <div class="col-12">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        </div>
    </div>
    <div>
        <div class="col-12" style="text-align: center;"><h1>Plantillas de Procedimientos</h1></div>
    </div>
    <div class="col-12" style="padding: 15px;" >
        <form id="frm_plantilla">
            <center>
                <div class="col-12"  style="margin: 15px;">
                    <select class="form-control select2_agregar_plantilla"  name="proc_com" style="width: 100%;">
                        <option value="">Seleccione ...</option> 
                            @foreach($proc_completo as $value)    
                                <option 
                                    value="{{$value->id}}">{{$value->nombre_general}}
                                </option>
                            @endforeach    
                    </select>
                </div>
            <center>
        </form>
            <div class="col-10" style="margin-top: 10px;margin-bottom: 5px; padding-left: 15px" >
                <div class="col-12">
                <div class="row" style="text-align: center;">
                    <div class="col-6" style="margin-bottom: 15px">
                        <a class="btn btn-info btn_ordenes" style="color: white; height: 100%; width: 100%" onClick="cargar_plantilla('1',{{$id}});">
                            <div class="col-12" style="padding-left: 0px; padding-right: 0px">
                                <label style="font-size: 16px">Agregar</label> 
                            </div>
                        </a>    
                    </div>
                    <div class="col-6" style="margin-bottom: 15px;">    
                        <a class="btn btn-info btn_ordenes" class="close" data-dismiss="modal" style="color: white; height: 100%; width: 100%" onClick="">
                            <div class="col-12" style="padding-left: 0px; padding-right: 0px;">
                                <label style="font-size: 16px">Cerrar</label>
                            </div>  
                        </a>
                    </div> 
                    </div>
                </div>
            </div>
            </center>
        </center>
    </div>

<script type="text/javascript">

    $(document).ready(function(){

      $('.select2_agregar_plantilla').select2({
        tags: false
      });

      
    });

   
    function cargar_plantilla(actualiza, id){
       
        $.ajax({
          type: 'post',
          url:"{{route('procedimiento.tecnica_plantilla')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm_plantilla").serialize(),
          success: function(data){
            //console.log($walter);
            if(data.tecnica_quirurgica!=null){
                var tecnica = data.tecnica_quirurgica;
            }else{
                alert("No Existe Plantilla Precargada !!");
                var tecnica = "";
            }
            //var edad;
            //fecha_nacimiento = $( "#fecha_nacimiento" ).val();
            //edad = calcularEdad(fecha_nacimiento);
            if(actualiza=='1'){
                //alert(alerta);
                anterior1=  $('#hallazgos'+id).val();
                anterior2=  tinyMCE.get('thallazgos'+id).getContent();
                $('#hallazgos'+id).val(anterior1+tecnica);
                tinyMCE.get('thallazgos'+id).setContent(anterior2+tecnica);
            }   
          },
          error: function(data){
             //console.log(data);
          }
    });

    //guardar();
    }
</script>

    
