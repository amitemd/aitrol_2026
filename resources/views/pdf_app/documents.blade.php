<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<style> 
     iframe {
            border: none;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }
</style>
<body>
    @php
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $docs = ["doc", "docx"];
    @endphp

    @if(in_array($format, $allowedExtensions))
        <img style="
        width: 100%;
        height: 100%;" src="{{ $file }}" alt="{{ $file }}" />
    @endif

    @if(in_array($format, $docs))
    <iframe id="miIFrame" src="https://view.officeapps.live.com/op/view.aspx?src=https://ieced.siaam.ec/sis_medico/{{$file}}"  style="border: none;"></iframe>
    @endif
</body>
</html>