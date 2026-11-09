<div class="document-logo">
    <!--AITROL_PDF_BRANDING_SLOT-->
    <!--AITROL_PDF_BRANDING_FALLBACK_START--><img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/logo_certificado_borja.png'))) }}" alt="Logo"><!--AITROL_PDF_BRANDING_FALLBACK_END-->
</div>
<table class="identity official-identity">
    <tr><td colspan="12" class="identity-band">A. DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE</td></tr>
    <tr class="labels">
        <td colspan="2">INSTITUCIÓN DEL SISTEMA</td><td>UNICÓDIGO</td><td colspan="3">ESTABLECIMIENTO DE SALUD</td>
        <td colspan="3">NÚMERO DE HISTORIA CLÍNICA ÚNICA</td><td colspan="3">NÚMERO DE ARCHIVO</td>
    </tr>
    <tr>
        <td colspan="2">SALUD</td><td>{{ $empresa->unicodigo ?? '' }}</td><td colspan="3">{{ $companyName }}</td>
        <td colspan="3">{{ $solicitud->id_paciente }}</td><td colspan="3">{{ $solicitud->id }}</td>
    </tr>
    <tr class="labels">
        <td colspan="2" rowspan="2">PRIMER APELLIDO</td><td colspan="2" rowspan="2">SEGUNDO APELLIDO</td>
        <td colspan="2" rowspan="2">PRIMER NOMBRE</td><td colspan="2" rowspan="2">SEGUNDO NOMBRE</td>
        <td rowspan="2">SEXO</td><td rowspan="2">EDAD</td><td colspan="2">CONDICIÓN EDAD<br>(MARCAR)</td>
    </tr>
    <tr class="labels"><td>H / D / M</td><td>A</td></tr>
    <tr>
        <td colspan="2">{{ $patient->apellido1 ?? '' }}</td><td colspan="2">{{ $patient->apellido2 ?? '' }}</td>
        <td colspan="2">{{ $patient->nombre1 ?? '' }}</td><td colspan="2">{{ $patient->nombre2 ?? '' }}</td>
        <td>{{ $patientSex }}</td><td>{{ preg_replace('/\D+/', '', $patientAge) }}</td><td></td><td>{{ $patientAge !== '' ? 'X' : '' }}</td>
    </tr>
</table>
