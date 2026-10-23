<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORDEN -{{$id}}</title>
</head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<body>
    @php 
        $surl= "https://ieced.siaam.ec/sis_medico/public/api/previsualizedExam/".$id;
    @endphp
    <div class="col-md-12">
    <embed src="https://drive.google.com/viewerng/viewer?embedded=true&url={{$surl}}#:0.page.20" style="width: 98%; height:100vh;">
    </div>
    <div class="col-md-12">
        &nbsp;
    </div>
    <div class="col-md-12" style="text-align: center;">
        
        <a class="btn btn-outline-dark" target="_blank"  href="{{$surl}}"> <i class="bi bi-download"></i> </a>
    </div>
    <div class="col-md-12">
        &nbsp;
    </div>

</body>
</html>