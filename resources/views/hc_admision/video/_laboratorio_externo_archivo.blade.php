@php
    $extension = strtolower(pathinfo((string) $imagen->nombre, PATHINFO_EXTENSION));
    $esImagen = in_array($extension, array('jpg', 'jpeg', 'png', 'gif'), true);
    $esPdf = $extension === 'pdf';
    $esOffice = in_array($extension, array('doc', 'docx', 'xls', 'xlsx'), true);
    $esTexto = $extension === 'txt';
    $icono = $esPdf ? 'fa-file-pdf-o' : ($esImagen ? 'fa-file-image-o' : ($esOffice ? (in_array($extension, array('xls', 'xlsx'), true) ? 'fa-file-excel-o' : 'fa-file-word-o') : ($esTexto ? 'fa-file-text-o' : 'fa-file-o')));
    $claseIcono = $esImagen ? 'is-image' : ($esOffice ? 'is-office' : ($esTexto ? 'is-text' : ''));
    $nombreOriginal = trim((string)($imagen->nombre_original ?: $imagen->nombre_anterior));
    $nombreVisible = $nombreOriginal !== '' ? $nombreOriginal : preg_replace('/^hc_biopsias_/i', 'lab_externo_', (string) $imagen->nombre);
    $sePuedeVisualizar = $esPdf || $esImagen || $esTexto;
@endphp
<article class="lab-file-card" data-file-id="{{$imagen->id}}">
    <a class="lab-file-preview js-lab-preview" href="{{url('laboratorio_externo_descarga/'.$imagen->id)}}" data-name="{{$nombreVisible}}" data-previewable="{{$sePuedeVisualizar?'1':'0'}}" data-download="{{url('laboratorio_externo_descarga/'.$imagen->id)}}">
        <span class="lab-extension-icon {{$claseIcono}}"><i class="fa {{$icono}}"></i><span class="lab-extension-badge">{{$extension?:'FILE'}}</span></span>
        <span class="lab-file-preview-copy"><i class="fa fa-eye"></i> Ver documento</span>
    </a>
    <div class="lab-file-info">
        <a class="lab-file-name js-lab-preview" href="{{url('laboratorio_externo_descarga/'.$imagen->id)}}" data-name="{{$nombreVisible}}" data-previewable="{{$sePuedeVisualizar?'1':'0'}}" data-download="{{url('laboratorio_externo_descarga/'.$imagen->id)}}">{{$nombreVisible}}</a>
        <span class="lab-file-date"><i class="fa fa-calendar-o"></i> {{substr((string) $imagen->created_at, 0, 10)}}</span>
        <div class="lab-file-actions">
            <a href="{{url('laboratorio_externo_descarga/'.$imagen->id)}}" class="lab-action lab-action-download" title="Descargar archivo"><i class="fa fa-download"></i> Descargar</a>
            <button type="button" class="lab-action lab-action-delete js-delete-external" data-url="{{route('laboratorio_externo.eliminar', array('id'=>$imagen->id))}}" title="Eliminar archivo"><i class="fa fa-trash"></i> Eliminar</button>
        </div>
    </div>
</article>
