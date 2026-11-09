<style>
    .hospital-file-history__heading {
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 0 0 12px;
        color: #174866;
        font-size: 16px;
        font-weight: 700;
    }

    .hospital-file-history__heading i {
        color: #12a6bd;
    }

    .hospital-file-history__grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 12px;
    }

    .hospital-file-history__item {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        padding: 13px;
        border: 1px solid #dce8ef;
        border-radius: 10px;
        background: #fff;
    }

    .hospital-file-history__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #eaf7fa;
        color: #0d90b5;
        font-size: 19px;
    }

    .hospital-file-history__info {
        min-width: 0;
        flex: 1 1 auto;
    }

    .hospital-file-history__name,
    .hospital-file-history__date {
        display: block;
    }

    .hospital-file-history__name {
        overflow: hidden;
        color: #24475f;
        font-size: 12px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .hospital-file-history__date {
        margin-top: 3px;
        color: #7a909e;
        font-size: 10px;
    }

    .hospital-file-history__actions {
        display: flex;
        flex: 0 0 auto;
        gap: 5px;
    }

    .hospital-file-history__action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        padding: 0;
        border-radius: 7px;
    }

    .hospital-file-history__empty {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px;
        border: 1px dashed #c6dee8;
        border-radius: 10px;
        background: #f7fbfd;
        color: #718896;
        font-size: 12px;
    }
</style>

<div class="hospital-file-history">
    <h3 class="hospital-file-history__heading">
        <i class="fa fa-folder-open-o"></i> {{trans('hospital.HistorialArchivos')}}
    </h3>

    @if(count($ho_archivos) > 0)
    <div class="hospital-file-history__grid">
        @foreach($ho_archivos as $imagen)
        @php
            $extension = strtolower(pathinfo($imagen->nombre_archivo, PATHINFO_EXTENSION));
            $esImagen = in_array($extension, ['jpg', 'jpeg', 'png']);
            $esPdf = $extension === 'pdf';
        @endphp
        <div class="hospital-file-history__item">
            <span class="hospital-file-history__icon">
                @if($esImagen)
                    <i class="fa fa-file-image-o"></i>
                @elseif($esPdf)
                    <i class="fa fa-file-pdf-o"></i>
                @else
                    <i class="fa fa-file-text-o"></i>
                @endif
            </span>

            <span class="hospital-file-history__info">
                <span class="hospital-file-history__name" title="{{$imagen->nombre_archivo}}">{{$imagen->nombre_archivo}}</span>
                <span class="hospital-file-history__date">{{substr($imagen->created_at, 0, 10)}}</span>
            </span>

            <span class="hospital-file-history__actions">
                <a href="{{asset('hospital/descarga/formulario')}}/{{$imagen->id}}" class="btn btn-primary btn-sm hospital-file-history__action" target="_blank" title="Descargar">
                    <i class="fa fa-download"></i>
                </a>
                @if(!$esImagen && !$esPdf)
                <a href="{{route('hospital.ver_formulario',['id' => $imagen->id])}}" class="btn btn-info btn-sm hospital-file-history__action" target="_blank" title="Ver">
                    <i class="fa fa-eye"></i>
                </a>
                @endif
                <button type="button" onclick="eliminar2('{{$imagen->id}}');" class="btn btn-danger btn-sm hospital-file-history__action" title="Eliminar">
                    <i class="fa fa-trash"></i>
                </button>
            </span>
        </div>
        @endforeach
    </div>
    @else
    <div class="hospital-file-history__empty">
        <i class="fa fa-folder-open-o"></i>
        <span>Todavía no se han cargado archivos para este episodio.</span>
    </div>
    @endif
</div>
