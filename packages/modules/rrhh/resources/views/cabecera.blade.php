@php([$titulo, $icono] = config('rrhh.secciones.'.$seccion))
@include('partials.listado-cabecera', [
    'titulo' => $titulo,
    'subtitulo' => 'Recursos humanos',
    'icono' => $icono,
    'crearUrl' => $crearUrl ?? null,
    'crearTexto' => $crearTexto ?? null,
])
