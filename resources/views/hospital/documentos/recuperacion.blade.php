@extends('layouts.app-template-h')
@section('content')
<section class="content">
    <h3>Postanestesico - Procedimiento {{ $procedure->id }}</h3>
    <p>Registro clinico interno del episodio {{ $solicitud->id }}.</p>
    <a class="btn btn-default" href="{{ route('hospital.documents.index', ['id_solicitud' => $solicitud->id]) }}"><i class="fa fa-arrow-left"></i> Documentos</a>
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <p>Estado: {{ $document && $document->signed_at ? 'Firmado' : ($source ? 'Sin firma' : 'No registrado') }}</p>
    @php
        $postoperativeDestination = isset($fields['conducido_a']) ? (string) $fields['conducido_a'] : '';
        $requiresPostoperativeTransfer = in_array($postoperativeDestination, ['1', '2'], true);
    @endphp
    @if($document && $document->signed_at)
        <div class="alert alert-success">
            <strong>Recuperacion postanestesica firmada.</strong>
            @if($postoperativeDestination === '1') El destino registrado es Hospitalizacion.
            @elseif($postoperativeDestination === '2') El destino registrado es Cuidados intensivos.
            @elseif($postoperativeDestination === '3') El destino registrado es Criticos de emergencia.
            @elseif($postoperativeDestination === '4') El destino registrado es Domicilio.
            @elseif($postoperativeDestination === '5') El destino registrado es Morgue.
            @else Registre el destino postoperatorio.
            @endif
        </div>
        @if($requiresPostoperativeTransfer && in_array((int) auth()->user()->id_tipo_usuario, [1, 3, 9], true))
            <a class="btn btn-primary" href="{{ route('hospital.traspaso_modulo', ['id_solicitud' => $solicitud->id]) }}">
                <i class="fa fa-bed"></i> Continuar con traslado a destino
            </a>
        @endif
    @endif
    <form method="POST" action="{{ route('hospital.documents.recovery.save', ['id_solicitud' => $solicitud->id, 'procedure' => $procedure->id]) }}">
        {{ csrf_field() }}
        <fieldset @if($readOnly) disabled @endif>
            <div class="row">
                @foreach($labels as $key => $label)
                    <div class="form-group col-md-6">
                        <label for="{{ $key }}">{{ $label }}</label>
                        @if(isset($options[$key]))
                            <select id="{{ $key }}" name="{{ $key }}" class="form-control">
                                <option value="">Seleccione</option>
                                @foreach($options[$key] as $value => $name)
                                    <option value="{{ $value }}" @if((string) old($key, isset($fields[$key]) ? $fields[$key] : '') === (string) $value) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                        @else
                            <textarea id="{{ $key }}" name="{{ $key }}" class="form-control" rows="3" maxlength="250">{{ old($key, isset($fields[$key]) ? $fields[$key] : '') }}</textarea>
                        @endif
                    </div>
                @endforeach
            </div>
            @if($document && $document->signed_at)
                <div class="form-group"><label for="amendment_reason">Motivo de adenda</label><input id="amendment_reason" name="amendment_reason" class="form-control" required minlength="5" maxlength="2000"></div>
            @endif
            @unless($readOnly)
                @if(!$document || !$document->signed_at)<button class="btn btn-default" name="status" value="DRAFT"><i class="fa fa-save"></i> Guardar borrador</button>@endif
                <button class="btn btn-primary" name="status" value="SIGNED"><i class="fa fa-pencil"></i> {{ $document && $document->signed_at ? 'Firmar adenda' : 'Firmar recuperacion' }}</button>
            @endunless
        </fieldset>
    </form>
</section>
@endsection
