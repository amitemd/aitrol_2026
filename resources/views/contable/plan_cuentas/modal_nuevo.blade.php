<style type="text/css" >
.ui-autocomplete
        {
             overflow-x: hidden;
              max-height: 200px;
              width:1px;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            float: left;
            display: none;
            min-width: 160px;
            _width: 160px;
            padding: 4px 0;
            margin: 2px 0 0 0;
            list-style: none;
            background-color: #fff;
            border-color: #ccc;
            border-color: rgba(0, 0, 0, 0.2);
            border-style: solid;
            border-width: 1px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -webkit-background-clip: padding-box;
            -moz-background-clip: padding;
            background-clip: padding-box;
            *border-right-width: 2px;
            *border-bottom-width: 2px;
        }
</style>
<div class="modal-header" style="padding-top: 5px; padding-bottom: 1px; background-color: #bbb0ad;">
    <div class="col-md-11">
         <h4 class="modal-title">Crear Nueva Subcuenta de {{$padre->nombre}}</h4>
    </div>
    <div class="col-md-1">
        <button type="button" id="cerrar" oclass="close" data-dismiss="modal">&times;</button>
    </div>
</div>

<form action="" method="POST" id="form_cuenta" >
    {{ csrf_field() }}
    <div class="modal-body">
        <div class="col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style=" font-size: 12px;">
              <input type="hidden" id="id_cuenta" name="id_padre" value="{{$padre->id_plan}}">
              <tbody>
                <tr>
                  <td width="20%"><b>Codigo:</b></td>
                  <td width="80%">{{$padre->plan}}.<input type="text" name="id_cuenta" onkeypress="return numeros(event)"></td>
                </tr>
                <tr>
                  <td><b>Nombre:</b></td>
                  <td><input  style="width: 80%;font-size: 12px;" type="text" id="nombre" name="nombre" autocomplete="off"></td>
                </tr>
                <tr>
                  <td><b>Tipo:</b></td>
                  <td><select name="tipo" id="tipo" >
                        <option value="1"   >Grupo</option>
                        <option value="2"  >Detalle</option>
                      </select>
                  </td> 
                </tr>
                <tr>
                  <td><b>Calcula en:</b></td>
                  <td><select name="naturaleza_2" id="naturaleza_2" >
                        <option value="0" >Debe</option>
                        <option value="1" >Haber</option>
                      </select>
                  </td>
                </tr>
              </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <a type="button" onclick="guardar_modal()" class="btn btn-primary" style="margin-top: 40px;">GUARDAR</a>
    </div>
</form>
<script type="text/javascript">
    function cerrar(){
        if (confirm('¿Desea salir sin guardar las retenciones?')) {
            location.href ="{{route('compras_index')}}";
        }else{

        }

    }
    function numeros(e){
       var tecla = e.keyCode;

        if (tecla==8 || tecla==9 || tecla==13){
            return true;
        }

        var patron =/[0-9]/;
        var tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }
    function guardar_modal(){
        Swal.fire({
      title: '¿Desea guardar la cuenta?',
      text: "No puedes revertir esta acccion!",
      icon: 'info',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
            $.ajax({
                type: 'post',
                url:"{{route('plan_cuentas.guardar_nuevo')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'html',
                data: $("#form_cuenta").serialize(),
                success: function(data){
                    if(data != 'ok'){
                    console.log(data);
                    alert(data);
                    }else{
                        $('#editMaxPacientes').hide();
                        location.reload();
                    }

                },
                error: function(data){
                    console.log(data);
                }
            });
       }
    })
}

</script>
