<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>correo</title>
   <style>

   .titulo {
    color: #1e80b6;
    padding-top: 20px;
    padding-bottom: 10px;
    padding-left: 20px;
    padding-right: 20px;
    }

    .body{
     background-color: #ECECEC;	
    }


    .div_contenido{
    color: #1e80b6;
    padding-top: 20px;
    padding-bottom: 10px;
    padding-left: 20px;
    padding-right: 20px;
    background-color: #ffffff !important;
   }

   </style>

</head> 

<body class="body">

<div class="titulo" ><h1>Ticket de Soporte Tecnico</h1></div>
<hr>
<div class=".div_contenido" >Tiene un nuevo requerimiento de  <?php echo $nombrePersona ; ?> su requerimiento es  <?php echo $requerimiento; ?> del area <?php echo $area; ?>.La fecha del requerimiento es <?php echo $fecha;?></div>
</body>
</html>