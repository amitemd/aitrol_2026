<script src="{{ asset('/js/hc4-consultations.js') }}?v={{ filemtime(public_path('js/hc4-consultations.js')) }}"></script>
<script>
    function guardar_consulta(id_paciente, id_agenda){
        window.Hc4Consultations.create({
            patientId: String(id_paciente),
            createUrl: "{{route('consulta.crear_nueva_consulta')}}/" + encodeURIComponent(id_paciente) + '/' + encodeURIComponent(id_agenda || 'no'),
            listUrl: "{{url('inicio/cargar/consultas')}}/" + encodeURIComponent(id_paciente),
            token: '{{ csrf_token() }}'
        });
    }
</script>
