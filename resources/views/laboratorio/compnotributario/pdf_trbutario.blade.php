<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>

    *{
        margin: 6px;
        text-transform: uppercase;
       font-family: sans-serif;
       font-size: 23px;
       font-weight: bolder;

    }
    .flex{
        display: flex;
    }
    .center{
        text-align: center;
    }
    .izquierda{
        text-align: right;
    }

    .text_peq p{
        font-size: 18px!important ;
        font-weight: none;
    }
</style>
@php
//librerias
    use \Milon\Barcode\DNS1D;
    use \Milon\Barcode\DNS2D;

@endphp
<body class="tamaño">
    <div class="center">
        @if(!is_null($empresa->logo))
            <img src="{{base_path().'/storage/app/logo/logo_factura_labs.jpg'}}" style="width:300px;height: 150px">
        @endif
        <p>{{$empresa->razonsocial}}</p>
        <p>{{$empresa->nombrecomercial}}</p>
    </div>

    <div>
        <p>factura: {{$examen_orden->comprobante}}</p>
        <p> CI o RUC: {{$empresa->id}}</p>
        <p>Ciudad: {{$empresa->ciudad}}</p>
        <p>direccion: {{$empresa->direccion}}</p>
        <p>email: {{$empresa->email}}</p>

        <p>...................................................................................</p>
        <p>Cliente</p>
        <p>...................................................................................</p>
        <p>identificacion: {{$examen_orden->cedula_factura}}</p>
        <p>cliente: {{$examen_orden->nombre_factura}}</p>
        <p>telefono cliente: {{$examen_orden->cedula_factura}}</p>
        <p>direccion: {{$examen_orden->direccion_factura}}</p>
        <p>correo: {{$examen_orden->email_factura}}</p>

        <table style="width :100%;">
            <tr>
                <th>c</th>
                <th>nombre</th>
                <th>p/u</th>
                <th>d</th>
                <th>total</th>
            </tr>
            @foreach($examen_orden->detalles as $value)
            <tr>
                <td>1</td>
                <td>{{$value->examen->nombre}}</td>
                <td class="izquierda">{{number_format($value->valor , 2, '.', '')}}</td>
                <td class="izquierda">{{number_format($value->valor_descuento, 2, '.', '')}}</td>
                <td class="izquierda">{{number_format($value->valor - $value->valor_descuento, 2, '.', '')}}</td>
            </tr>
            @endforeach
            @if($examen_orden->recargo_valor > 0 )
            <tr>
                <td>1</td>
                <td>Fee Administrativo</td>
                <td class="izquierda">{{number_format($examen_orden->recargo_valor , 2, '.', '')}}</td>
                <td class="izquierda">0.00</td>
                <td class="izquierda">{{number_format($examen_orden->recargo_valor, 2, '.', '')}}</td>
            </tr>
            @endif
        </table>
        <div style="text-align: right;">
            <p>subtotal 12%: 0.00</p>
            <p>Subtotal 0%: {{number_format($examen_orden->total_valor, 2, '.', '')}}</p>
            <p>Total sin Impuestos: {{number_format($examen_orden->total_valor, 2, '.', '')}}</p>
            <p>Total Descuento: {{number_format($examen_orden->descuento_valor, 2, '.', '')}}</p>
            <p>iva: 0.00</p>
            <p>total: {{number_format($examen_orden->total_valor, 2, '.', '')}}</p>
        </div>
        <div class="text_peq">
            <p>...................................................................................</p>
                <p>Estimado cliente: por favor verifique los datos de su factura,
                únicamente se aceptarán cambios el mismo día de emisión.</p>
            <p>...................................................................................</p>
        </div>

        <!--CREAR EL CODIGO DE BARRA PARA SACAR LA FACTURA DE LA CONSULTA-->
        <div  style="text-align: center;">
            @php
            /*
            1) creamos una ruta en routes/sin_restriccion.php, donde pediremos por get el comprobante
            2) creamos una funcion en controller/servicios/ServiciosController.php la funcion se llamara comprobante_externo
            3) La variable comprobante ontendra el nro comprobante y lo encriptaremos 2 veces y se la pasamos a la funcion
            en la funcion la desencriptamos
            */
            $url = url('/');
            //dd($url);

            $url_2 ='https://app.labs.ec/auth';
            @endphp
            <img  style="width: 200px;height: 200px;" src="data:image/png;base64, {{ DNS2D::getBarcodePNG($url, 'QRCODE')}}" alt="barcode"   />
        </div>

        <div class="text_peq">
              <p>Para obtener sus resultados puede visitar nuestra pagina web: <span style="text-transform: lowercase;">http://www.labs.ec/</span> o Descargate nuestra App "LABS"</p>
              <div style="width:100%;text-align:center;">
                  <img  style="width: 200px;height: 200px; text-align: center;" src="data:image/png;base64, {{ DNS2D::getBarcodePNG($url_2, 'QRCODE')}}" alt="barcode"   />
              </div>
              <p>si es primera vez que ingresa puede  acceder con:<br><span>usuario:</span>  {{$usuario_mail->email}}<br><span>clave:</span>  {{$usuario_mail->id}} </p>
        </div>

</body>
</html>
