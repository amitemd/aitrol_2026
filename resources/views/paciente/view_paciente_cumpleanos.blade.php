@php
    $img = url('../storage/app/logo/cumpleanos.png');
    $texto = 'FAUSTO JAVIER ORTIZ SERRANO';
    $imagen = imagecreatefrompng($img);
    $color = imagecolorallocate($imagen, 255, 255, 255);
    $color1 = imagecolorallocate($imagen, 255, 255, 255);
    $fontPath = public_path('fonts/Jost-Black.ttf');;
    $tamanio = 20;
    $angulo = 0;
    $x = 400;
    $y = 200;
    imagettftext($imagen, 20, 0, 140, 100, $color, $fontPath, $texto);
    imagettftext($imagen, 20, 0, 140, 100, $color1, $fontPath, $texto);

    $rutaTemporal = tempnam(sys_get_temp_dir(), 'img_') . '.png';
    imagepng($imagen, $rutaTemporal);
    header('Content-Type: image/png');
    readfile($rutaTemporal);
    dd("ads")
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <!-- Muestra la imagen generada -->
    <img src="{{ $rutaTemporal }}" alt="Tu Imagen Generada">
</body>

</html>