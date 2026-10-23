<div class="col-md-12">
    <h2>{{trans('hospital.HistorialArchivos')}}</h2>
    @php $count=0; @endphp
    @foreach($ho_archivos as $imagen)
    <div class="col-md-3" style='margin: 10px 0; text-align: center;'>
        @php
        $explotar = explode( '.', $imagen->nombre);
        $extension = end($explotar);
        @endphp
        @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png'))

        <img src="{{asset('hc_ima')}}/{{$imagen->nomdbre}}" width="90%" style="max-height: 140px;">
        <span>{{$imagen->nombre_archivo}}</span>

        <span>{{substr($imagen->created_at, 0, 10)}}</span><br>
        <a type="button" href="{{asset('hospital/descarga/formulario')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank">
            <i class="fa fa-download-alt"> </i>
        </a>
        <a type="button" onclick="eliminar2('{{$imagen->id}}');" class="btn btn-danger btn-sm" target="_blank">
            <i class="fa fa-trash"></i>
        </a>
        @elseif(($extension == 'pdf'))

        <img src="{{asset('imagenes/pdf.png')}}" width="90%" style="max-height: 140px;">
        <span>{{$imagen->nombre_archivo}}</span>

        <span>{{substr($imagen->created_at, 0, 10)}}</span><br>
        <a type="button" href="{{asset('hospital/descarga/formulario')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank">
            <i class="fa fa-download"></i>
        </a>
        <a type="button" onclick="eliminar2('{{$imagen->id}}');" class="btn btn-danger btn-sm" target="_blank">
            <i class="fa fa-trash"></i>
        </a>

        @else
        @php
        $variable = explode('/' , asset('/hc_ima/'));
        @endphp

        <img src="{{asset('imagenes/office.png')}}" width="50%" >
        <span>{{$imagen->nombre_archivo}}</span> <br>

        <span>{{substr($imagen->created_at, 0, 10)}}</span><br>
        <a type="button" href="{{asset('hospital/descarga/formulario')}}/{{$imagen->id}}" class="btn btn-primary btn-sm" target="_blank">
            <i class="fa fa-download"></i>
        </a>
        <a type="button" onclick="eliminar2('{{$imagen->id}}');" class="btn btn-danger btn-sm" target="_blank">
            <i class="fa fa-trash"></i>
        </a>

        <a href="{{route('hospital.ver_formulario',['id_archivo' => $imagen->id])}}" target="_blank" class="btn btn-info btn-sm">
            <i class="fa fa-eye"></i>
        </a>
        @endif
    </div>
    @endforeach

</div>