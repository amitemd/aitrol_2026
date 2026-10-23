<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>COMPLAINT</title>
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.0/flowbite.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <script src="{{ asset('/hc4/js/jquery.js') }}" type="text/javascript"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    * {
      font-family: 'Poppins', sans-serif;
    }

    .buttonEnviar {
      background-color: #1E90FF !important;
      border: none;
      color: white;
      padding: 10px 30px;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      cursor: pointer;
      margin: 20px 0px;
      font-weight: 600;
    }
  </style>
</head>

<body style="background-image: url({{asset('/imagenes/3d.jpeg')}}); background-position: center center; background-size: 1900px auto; background-repeat: no-repeat;"></body>

<body>
  <div id="page_pdf">
    <div>
      <img style="height: 350px; width: 100%;" src="{{asset('/imagenes/contacto.jpeg')}}">
    </div>
    <div class="max-w-4xl my-0 mx-auto p-4" id="factura_head">
      <form id="enviar" name="enviar" method="post">
        <input type="hidden" name="token" id="token" value="{{ csrf_token() }}">
        @if (isset($id) && !empty($id)) <input type="hidden" value="{{$id}}" name="id" id="id"> @endif
        <h3 class="text-center"><span class="text-gray-600" style="font-size: 30px; font-weight: bold;">REPORTE COMPLAINT</span></h1>
          <div class="grid md:grid-cols-2 gap-4 mt-4">
            <div class="w-full">
              <label class="text-gray-600" for="nombre"><b>Nombre de la Institucion:</b></label>
              <input style="width: 100%; height: 40px;" class="rounded-md" type="text" name="nombre" id="nombre">
            </div>
            <div class="w-full">
              <label class="text-gray-600" for="medico"><b>Medico Responsable:</b></label>
              <input style="width: 100%; height: 40px;" class="rounded-md" type="text" name="medico" id="medico">
            </div>
            <div class="w-full">
              <label class="text-gray-600" for="especialista"><b> Especialista Responsable de la Cuenta:</b></label>
              <input style="width: 100%; height: 40px;" class="rounded-md" type="text" name="especialista" id="especialista">
            </div>
            <div class="w-full">
              <label class="text-gray-600" for="procedimiento"><b> Nombre del Procedimiento: </b></label>
              <input style="width: 100%; height: 40px;" class="rounded-md" type="text" name="procedimiento" id="procedimiento">
            </div>
            <div class="w-full">
              <label class="text-gray-600" for="codigo"><b> código:</b></label>
              <input style="width: 100%; height: 40px;" class="rounded-md" type="text" name="codigo" id="codigo">
            </div>
            <div class="w-full">
              <label class="text-gray-600" for="cantidad"><b> Cantidad:</b></label>
              <input style="width: 100%; height: 40px;" class="rounded-md" type="text" name="cantidad" id="cantidad">
            </div>
            <div class="w-full">
              <label class="text-gray-600" for="descripcion"><b> Descripcion del Dispositivo:</b></label>
              <input style="width: 100%; height: 40px;" class="rounded-md" type="text" name="descripcion" id="descripcion">
            </div>
            <div class="w-full">
              <label class="text-gray-600" for="numero_lote"><b> Numero de Lote:</b></label>
              <input style="width: 100%; height: 40px;" class="rounded-md" type="text" name="numero_lote" id="numero_lote">
            </div>
            <div class="w-full">
              <label class="text-gray-600" for="dispositivo"><b> Dispositivo Nuevo o Reutilizado:</b></label>
              <input style="width: 100%; height: 40px;" class="rounded-md" type="text" name="dispositivo" id="dispositivo">
            </div>
            <div class="w-full">
              <label class="text-gray-600" for="fecha_vencimiento"><b> Fecha de Vencimiento:</b></label>
              <input style="width: 100%; height: 40px;" class="rounded-md" type="date" name="fecha_vencimiento" id="fecha_vencimiento">
            </div>
            <div class="w-full">
              <label class="text-gray-600" for="fecha_reporte"><b> Fecha que se Reporta:</b></label>
              <input style="width: 100%; height: 40px;" class="rounded-md" type="date" name="fecha_reporte" id="fecha_reporte">
            </div>
            <div class="w-full">
              <label class="text-gray-600" for="fecha_danio"><b> Fecha que se Daño:</b></label>
              <input style="width: 100%; height: 40px;" class="rounded-md" type="date" name="fecha_danio" id="fecha_danio">
            </div>
            <div class="w-full">
              <label class="text-gray-600"><b> ¿Cuándo se produjo la denuncia?:</b></label>
              <div>
                <input type="radio" id="antes" name="momento_dano" value="antes">
                <label for="antes">Antes</label>
              </div>
              <div>
                <input type="radio" id="durante" name="momento_dano" value="durante">
                <label for="durante">Durante</label>
              </div>
              <div>
                <input type="radio" id="despues" name="momento_dano" value="despues">
                <label for="despues">Después</label>
              </div>
            </div>
            <div class="w-full">
              <label class="text-gray-600" for="observacion"><b> Observación:</b></label>
              <textarea style="width: 100%; height: 75px;" class="rounded-md" type="text" name="observacion" id="observacion"></textarea>
            </div>
            <div class="w-full">
              <label class="text-gray-600" for="resolucion"><b> Resolucion del Procedimiento:</b></label>
              <input style="width: 100%; height: 40px;" class="rounded-md" type="text" name="resolucion" id="resolucion">
            </div>
            <div>
              <label class="text-gray-600" for="condicion"><b> Condicion del paciente:</b></label>
              <input style="width: 100%; height: 40px;" class="rounded-md" type="text" name="condicion" id="condicion">
            </div>
            <div class="w-full">
              <label class="text-gray-600" for="descripcion_suceso"><b> Descripcion de lo sucedido con el Dispositivo:</b></label>
              <textarea style="width: 100%; height: 200px;" class="rounded-md" type="text" name="descripcion_suceso" id="descripcion_suceso"></textarea>
            </div>
            <div style=" border-radius: 12.5;">
              <label class="text-gray-600" for="files"><b>Documento de Entrega</b></label>
              <input style="border-radius: 12.5px;" type="file" name="files" id="files" accept=".jpg, .jpeg, .png">
            </div>
          </div>
          <div class="text-center">
            <button type="button" onclick="guardar()" class="rounded buttonEnviar">
              Enviar
            </button>
          </div>
    </div>
    <div>
      <img style="height: 280px; width: 100%;" src="{{asset('/imagenes/fondo_gastritech.jpeg')}}">
    </div>
</body>

</html>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
  //  formulario.complaint_guardar
  function guardar() {
    var momentoDanioValue = $('input[name="momento_dano"]:checked').val();
    var campos = ['#nombre', '#medico', '#especialista', '#procedimiento', '#codigo', '#descripcion', '#numero_lote', '#dispositivo', '#fecha_vencimiento', '#resolucion', '#condicion', '#descripcion_suceso', '#fecha_reporte', '#fecha_danio'];
    var formData = new FormData();
    //formData.append('files', $('#files')[0].files[0]);
    formData.append('nombre', $('#nombre').val());
    formData.append('medico', $('#medico').val());
    formData.append('especialista', $('#especialista').val());
    formData.append('procedimiento', $('#procedimiento').val());
    formData.append('codigo', $('#codigo').val());
    formData.append('descripcion', $('#descripcion').val());
    formData.append('numero_lote', $('#numero_lote').val());
    formData.append('observacion', $('#observacion').val());
    formData.append('cantidad', $('#cantidad').val());
    formData.append('dispositivo', $('#dispositivo').val());
    formData.append('fecha_vencimiento', $('#fecha_vencimiento').val());
    formData.append('resolucion', $('#resolucion').val());
    formData.append('condicion', $('#condicion').val());
    formData.append('descripcion_suceso', $('#descripcion_suceso').val());
    formData.append('fecha_reporte', $('#fecha_reporte').val());
    formData.append('id', $('#id').val());
    formData.append('fecha_danio', $('#fecha_danio').val());
    formData.append('momento_dano', momentoDanioValue || '');

    var camposValidos = campos.every(function(campo) {
      var valor = $(campo).val().trim();
      formData.append(campo.substring(1), valor);
      return valor !== '';
    });
    if (!camposValidos) {
      return Swal.fire({
        text: 'Llenar todos los campos, por favor.',
        icon: "error",
        title: "error"
      });
    } else {
      $.ajax({
        url: "{{ url('/reporte/complaint/guardar') }}",
        headers: {
          'X-CSRF-TOKEN': $('#token').val()
        },
        data: formData,
        type: 'post',
        processData: false,
        contentType: false,
        success: function(data) {
          if (data.msj === "error") {
            return Swal.fire({
              title: "ERROR",
              text: data.msj,
              icon: data.status
            })
          } else {
            Swal.fire({
              title: "Exito",
              text: data.msj,
              icon: data.status
            }).then((result) => {
              if (result.isConfirmed) {
                location.href = '{{route("complaint.index")}}';
              }
            });
          }
        },
        error: function(xhr, status, error) {
          console.log('Error: ' + xhr.responseText);
        },
        complete: function() {
          $('#enviar2').prop('disabled', false);
        }
      });
    }
  }
</script>