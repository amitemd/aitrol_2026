@if($form['code'] === '003')
    @include('hospital.documentos.formulario-003-pdf')
@elseif(in_array($form['code'], ['018', '018A', '019'], true))
    @include('hospital.documentos.formulario-anestesia-pdf')
@elseif($form['code'] === '020')
    @include('hospital.documentos.formulario-020-pdf')
@elseif($form['code'] === '022')
    @include('hospital.documentos.formulario-022-pdf')
@elseif($form['code'] === '024')
    @include('hospital.documentos.formulario-024-pdf')
@else
    @include('hospital.documentos.formulario-episodio-pdf')
@endif
