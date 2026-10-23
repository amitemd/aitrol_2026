@extends('anamenesis.encabezado')
@section('action-content')


<section class="content">
    <div class="box">

        <div class="box-body">
            <input type="hidden" name="id" id="id" value="{{$id}}">

            <div class="text-right" style="margin-right: 20px;">
                <a id="formulario" href="{{ route('formulario.002', ['id' => $id]) }}" target="_blank" type="button" class="btn btn-danger">
                    <i class="fa fa-file-pdf-o" aria-hidder="true">&nbsp;&nbsp; PDF</i>
                </a>
            </div>
            <br>

            <table border="1" style="width: 100%">
                <thead>
                    <tr>
                        <td class="bg-purple" style="font-size: 10;"><b> MOTIVOS DE LA CONSULTA </b></td>
                    </tr>
                    <tr>

                        <td>
                            <select onchange="guardar_select(this)" style="width: 25%;" class="form-control input-sm" name="motivo_consulta" id="motivo_consulta" >
                                <option value="">Seleccione</option>
                                <option {{$anamnesis->motivo_consulta == '0' ? 'selected' : '' }} value="0">PRIMERA </option>
                                <option {{$anamnesis->motivo_consulta == '1' ? 'selected' : '' }} value="1">SUBSECUENTE </option>
                            </select>

                        </td>
                    </tr>
                    <tr>
                        <td class="bg-purple" style="font-size: 10;"><b> B. MOTIVOS DE LA CONSULTA  </b></td>
                    </tr>
                    <tr>
                        <td>
                            <input onchange="actualiza_datos_evo(this)" style="width: 100%; height: 45px;" type="text" name="motivo_evolucion" id="motivo_evolucion" value="@if(!is_null($anamnesis->motivo_evolucion)) {{$anamnesis->motivo_evolucion}} @endif">
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-purple" style="font-size: 10;"><b> E. ENFERMEDAD O PROBLEMA ACTUAL </b></td>
                    </tr>
                    <tr>
                        <td>
                            <div id="tproblema_actual" style="border: solid 1px;"><?php echo $anamnesis->problema_actual ?></div>
                            <input type="hidden" name="problema_actual" id="problema_actual">    
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-purple" style="font-size: 10;"><b> PLAN DE TRATAMIENTO </b></td>
                    </tr>
                    <tr>
                        <td>
                            <input onchange="actualiza_datos_evo(this)" style="width: 100%; height: 45px;" type="text" name="indicacion_evolucion" id="indicacion_evolucion" value="@if(!is_null($anamnesis->indicacion_evolucion)) {{$anamnesis->indicacion_evolucion}} @endif">
                        </td>
                    </tr>




                </thead>
            </table>

            <br>
            <table border="1" style="width: 100%">
                <tr>
                    <td colspan="12" class="bg-purple" style="font-size: 10;"><b> F. CONSTANTES VITALES Y ANTROPOMETRIA </b></td>
                </tr>
                <tr>
                    <td bgcolor="#BBFDAC" align="center">FECHA</td>
                    <td bgcolor="#BBFDAC" align="center">HORA</td>
                    <td bgcolor="#BBFDAC" align="center">Temperatura (Cº)</td>
                    <td bgcolor="#BBFDAC" align="center">Presión Arterial (mmHg)</td>
                    <td bgcolor="#BBFDAC" align="center">Talla (cm)</td>
                    <td bgcolor="#BBFDAC" align="center">Peso (Kg)</td>
                </tr>
                <td style="height: 20px;">
                    <input onchange="actualiza_datos_evo(this)" style="width: 100%; height: 45px;" type="text" name="fecha_atencion" id="fecha_atencion" value="@if(!is_null($anamnesis->historiaclinica->fecha_atencion)){{ substr($anamnesis->historiaclinica->fecha_atencion, 0, 10) }}@endif">
                </td>

                <td><input onchange="actualiza_datos_evo(this)" style="width: 100%; height: 45px;" type="text" name="hora_atencion" id="hora_atencion" value="@if(!is_null($anamnesis->historiaclinica->fecha_atencion)){{ substr($anamnesis->historiaclinica->agenda->fechaini,11) }}@endif"></td>
                <td><input onchange="actualiza_datos_evo(this)" style="width: 100%; height: 45px;" type="text" name="temperatura" id="temperatura" value="@if(!is_null($anamnesis->historiaclinica->temperatura)) {{$anamnesis->historiaclinica->temperatura}} @endif"></td>
                <td><input onchange="actualiza_datos_evo(this)" style="width: 100%; height: 45px;" type="text" name="presion" id="presion" value="@if(!is_null($anamnesis->historiaclinica->presion)) {{$anamnesis->historiaclinica->presion}} @endif"></td>
                <td style="height: 20px;"><input onchange="actualiza_datos_evo(this)" style="width: 100%; height: 45px;" type="text" name="altura" id="altura" value="@if(!is_null($anamnesis->historiaclinica->altura)) {{$anamnesis->historiaclinica->altura}} @endif"></td>
                <td><input onchange="actualiza_datos_evo(this)" style="width: 100%; height: 45px;" type="text" name="peso" id="peso" value="@if(!is_null($anamnesis->historiaclinica->peso)) {{$anamnesis->historiaclinica->peso}} @endif"></td>
                <tr>

                    <td bgcolor="#BBFDAC" align="center">Frecuencia Respiratoria</td>
                    <td bgcolor="#BBFDAC" align="center">IMC(Kg/mª)</td>
                    <td bgcolor="#BBFDAC" align="center">Perimetro Abdominal(cm)</td>
                    <td bgcolor="#BBFDAC" align="center">Hemoglobina Capilar (g/dl)</td>
                    <td bgcolor="#BBFDAC" align="center">Glucosa Capilar (mg/dl)</td>
                    <td bgcolor="#BBFDAC" align="center">Pulsioximetri</td>
                </tr>
                <tr>

                    <td><input onchange="actualiza_datos_evo(this)" style="width: 100%; height: 45px;" type="text" name="frecuencia_respiratoria" id="frecuencia_respiratoria" value="@if(!is_null($anamnesis->frecuencia_respiratoria)) {{$anamnesis->frecuencia_respiratoria}} @endif"></td>
                    <td><input onchange="actualiza_datos_evo(this)" style="width: 100%; height: 45px;" type="text" name="imc_kg" id="imc_kg"value="@if(!is_null($anamnesis->imc_kg)) {{$anamnesis->imc_kg}} @endif"></td>
                    <td><input onchange="actualiza_datos_evo(this)" style="width: 100%; height: 45px;" type="text" name="perimetro_abdominal" id="perimetro_abdominal" value="@if(!is_null($anamnesis->perimetro_abdominal)) {{$anamnesis->perimetro_abdominal}} @endif"></td>
                    <td><input onchange="actualiza_datos_evo(this)" style="width: 100%; height: 45px;" type="text" name="hemoglobina_capilar" id="hemoglobina_capilar" value="@if(!is_null($anamnesis->hemoglobina_capilar)) {{$anamnesis->hemoglobina_capilar}} @endif"></td>
                    <td><input onchange="actualiza_datos_evo(this)" style="width: 100%; height: 45px;" type="text" name="glucosa_capilar" id="glucosa_capilar" value="@if(!is_null($anamnesis->glucosa_capilar)) {{$anamnesis->glucosa_capilar}} @endif"></td>
                    <td><input onchange="actualiza_datos_evo(this)" style="width: 100%; height: 45px;" type="text" name="pulsioximetri" id="pulsioximetri" value="@if(!is_null($anamnesis->pulsioximetri)) {{$anamnesis->pulsioximetri}} @endif"></td>
                </tr>
            </table>

            <br>

            <table border="1" style="width: 100%">
                <thead>
                    <tr>
                        <td colspan="5" class="bg-purple" style="font-size: 10;"><b> ANTECEDENTES PATOLÓGICOS PERSONALES</b></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">
                            CARDIOPATÍA &nbsp;<input class="flat-orange" type="checkbox" @if($anamnesis->ap_cardiopatia == '1') checked @endif value="ap_cardiopatia" name="ap_cardiopatia" id="ap_cardiopatia" onchange="handlePublica(this)" id="cardiopatia" required="required">
                        </td>
                        <td class="text-center">
                            HIPERTENSIÓN &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="checkbox" @if($anamnesis->ap_hipertension == '1') checked @endif value="ap_hipertension" name="ap_hipertension" id="ap_hipertension" required="required">
                        </td>

                        <td class="text-center">
                            ENF.C.VASCULAR &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" @if($anamnesis->ap_vascular == '1') checked @endif value="ap_vascular" name="ap_vascular" id="ap_vascular" required="required">
                        </td>

                        <td class="text-center">
                            ENDÓCRINO METABOLICO&nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="checkbox" @if($anamnesis->ap_endocrino == '1') checked @endif value="ap_endocrino" name="ap_endocrino" id="ap_endocrino" required="required">
                        </td>

                        <td class="text-center">
                            CÁNCER &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="ap_cancer" name="ap_cancer" id="ap_cancer" required="required">
                        </td>
                    <tr>
                        <td class="text-center">
                            TUBERCULOSIS &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" @if($anamnesis->ap_tuberculosis == '1') checked @endif value="ap_tuberculosis" name="ap_tuberculosis" id="ap_tuberculosis" required="required">
                        </td>
                        <td class="text-center">
                            ENF.MENTAL&nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="ap_enf_mental" name="ap_enf_mental" id="ap_enf_mental" required="required">
                        </td>

                        <td class="text-center">
                            ENF.INFECCIOSA &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="ap_enf_infecciosa" name="ap_enf_infecciosa" id="ap_enf_infecciosa" required="required">
                        </td>
                        <td class="text-center">
                            MAL FORMACIÓN&nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="ap_mal_formacion" name="ap_mal_formacion" id="ap_mal_formacion" required="required">
                        </td>
                        <td class="text-center">
                            OTRO&nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="ap_otro" name="ap_otro" id="ap_otro" required="required">
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>

            <table border="1" style="width: 100%">
                <thead>
                    <tr>
                        <td colspan="5" class="bg-purple" style="font-size: 10;"><b> ANTECEDENTES PATOLÓGICOS FAMILIARES</b></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">
                            CARDIOPATÍA &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="af_cardiopatia" name="af_cardiopatia" id="af_cardiopatia" required="required">
                        </td>
                        <td class="text-center">
                            HIPERTENSIÓN &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="af_hipertension" name="af_hipertension" id="af_hipertension" required="required">
                        </td>
                        <td class="text-center">
                            ENF.C.VASCULAR &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="af_vascular" name="af_vascular" id="af_vascular" required="required">
                        </td>
                        <td class="text-center">
                            ENDÓCRINO METABOLICO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="af_endocrino" name="af_endocrino" id="af_endocrino" required="required">
                        </td>
                        <td class="text-center">
                            CÁNCER &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="af_cancer" name="af_cancer" id="af_cancer" required="required">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            TUBERCULOSIS &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="af_tuberculosis" name="af_tuberculosis" id="af_tuberculosis" required="required">
                        </td>
                        <td class="text-center">
                            ENF.MENTAL &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="af_enf_mental" name="af_enf_mental" id="af_enf_mental" required="required">
                        </td>
                        <td class="text-center">
                            ENF.INFECCIOSA &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="af_infecciosa" name="af_infecciosa" id="af_infecciosa" required="required">
                        </td>

                        <td class="text-center">
                            MAL FORMACIÓN &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="af_mal_formacion" name="af_mal_formacion" id="af_mal_formacion" required="required">
                        </td>

                        <td class="text-center">
                            OTRO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="af_otro" name="af_otro" id="af_otro" required="required">
                        </td>
                    </tr>

                </tbody>
            </table>

            <br>


            <table border="1" style="width: 100%">
                <thead>
                    <tr>
                        <td colspan="5" class="bg-purple" style="font-size: 10;"><b> REVISION ACTUAL DE ORGANOS Y SISTEMAS</b></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">
                            PIEL - ANEXOS &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="piel" name="piel" id="piel" required="required">
                        </td>
                        <td class="text-center">
                            ORGANOS DELOS SENTIDOS &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="organos" name="organos" id="organos" required="required">
                        </td>
                        <td class="text-center">
                            RESPIRATORIO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="respiratorio" name="respiratorio" id="respiratorio" required="required">
                        </td>
                        <td class="text-center">
                            CARDIO - VASCULAR &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="cardio_vascular" name="cardio_vascular" id="cardio_vascular" required="required">
                        </td>
                        <td class="text-center">
                            DIGESTIVO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="digestivo" name="digestivo" id="digestivo" required="required">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            GENITO - URINARIO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="genito" name="genito" id="genito" required="required">
                        </td>
                        <td class="text-center">
                            MÚSCULO - ESQUELÉTICO &nbsp; <input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="musculo" name="musculo" id="musculo" required="required">
                        </td>
                        <td class="text-center">
                            ENDOCRINO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="endocrino" name="endocrino" id="endocrino" required="required">
                        </td>
                        <td class="text-center">
                            HEMO - LINFÀTICO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="hemo_linfatico" name="hemo_linfatico" id="hemo_linfatico" required="required">
                        </td>
                        <td class="text-center">
                            NERVIOSO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="nervioso" name="nervioso" id="nervioso" required="required">
                        </td>
                    </tr>
                </tbody>
            </table>

            <br>


            <table border="1" style="width: 100%">
                <thead>
                    <tr>
                        <td colspan="5" class="bg-purple" style="font-size: 10;"><b> EXAMEN FÍSICO</b></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">
                            PIEL - FANERAS &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="piel_faneras" name="piel_faneras" id="piel_faneras" required="required">
                        </td>
                        <td class="text-center">
                            CABEZA &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="cabeza" name="cabeza" id="cabeza" required="required">
                        </td>
                        <td class="text-center">
                            OJOS &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="ojos" name="ojos" id="ojos" required="required">
                        </td>
                        <td class="text-center">
                            OÍDOS&nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="oidos" name="oidos" id="oidos" required="required">
                        </td>
                        <td class="text-center">
                            NARIZ&nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="nariz" name="nariz" id="nariz" required="required">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            BOCA &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="boca" name="boca" id="boca" required="required">
                        </td>
                        <td class="text-center">
                            OROFARINGE&nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="orofaringe" name="orofaringe" id="orofaringe" required="required">
                        </td>
                        <td class="text-center">
                            CUELLO&nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="cuello" name="cuello" id="cuello" required="required">
                        </td>
                        <td class="text-center">
                            AXILAS - MAMAS &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="axilad_mamas" name="axilad_mamas" id="axilad_mamas" required="required">
                        </td>
                        <td class="text-center">
                            TÓRAX &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="torax" name="torax" id="torax" required="required">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            ABDOMEN &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="abdomen" name="abdomen" id="abdomen" required="required">
                        </td>
                        <td class="text-center">
                            COLUMNA VERTEBRAL &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="columna_vertebral" name="columna_vertebral" id="columna_vertebral" required="required">
                        </td>
                        <td class="text-center">
                            INGLE-PERINÉ &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="ingle_perine" name="ingle_perine" id="ingle_perine" required="required">
                        </td>
                        <td class="text-center">
                            MIEMBROS SUPERIORES &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="miembros_superiores" name="miembros_superiores" id="miembros_superiores" required="required">
                        </td>
                        <td class="text-center">
                            MIEMBROS INFERIORES &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="miembros_inferiores" name="miembros_inferiores" id="miembros_inferiores" required="required">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            ÓRGANOS DE LOS SENTIDOS &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="organo_sentidos" name="organo_sentidos" id="organo_sentidos" required="required">
                        </td>
                        <td class="text-center">
                            RESPIRATORIO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="ex_respiratorio" name="ex_respiratorio" id="ex_respiratorio" required="required">
                        </td>
                        <td class="text-center">
                            CARDIO - VASCULAR &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="cardio_vascular" name="cardio_vascular" id="cardio_vascular" required="required">
                        </td>
                        <td class="text-center">
                            DIGESTIVO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="ex_digestivo" name="ex_digestivo" id="ex_digestivo" required="required">
                        </td>
                        <td class="text-center">
                            GENITAL &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="genital" name="genital" id="genital" required="required">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            URINARIO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="urinario" name="urinario" id="urinario" required="required">
                        </td>
                        <td class="text-center">
                            MÚSCULO - ESQUELÉTICO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="musculo_esqueletico" name="musculo_esqueletico" id="musculo_esqueletico" required="required">
                        </td>
                        <td class="text-center">
                            ENDÓCRINO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="ex_endocrino" name="ex_endocrino" id="ex_endocrino" required="required">
                        </td>
                        <td class="text-center">
                            HEMO - LINFÁTICO&nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="ex_hemo_linfatico" name="ex_hemo_linfatico" id="ex_hemo_linfatico" required="required">
                        </td>
                        <td class="text-center">
                            NEUROLÓGICO &nbsp;<input onchange="handlePublica(this)" class="flat-orange" type="checkbox" value="neurologico" name="neurologico" id="neurologico" required="required">
                        </td>
                    </tr>
                </tbody>
            </table>


            <br>
            <br>
            <br>

        </div>
    </div>

    <script>
       function handlePublica(input) {
            let nombre, valor;

            nombre = input.name;

           
            if (input.checked) {
                valor = 1; 
            } else {
                valor = 0; 
            }
            console.log(valor);
            console.log(nombre);
            $.ajax({
                url: "{{ route('formato.anamenesis_guardar') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                data: {
                    [nombre]: valor,
                    id: $("#id").val()
                },
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        
                    }
                },
                error: function(error) {
                    console.error(error);
                }
            });
        }
        function guardar_select(input) {
            let nombre, valor;

            nombre = input.name;

           
           valor = input.value;
            console.log(valor);
            console.log(nombre);
            $.ajax({
                url: "{{ route('formato.anamenesis_guardar') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                data: {
                    [nombre]: valor,
                    id: $("#id").val()
                },
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        
                    }
                },
                error: function(error) {
                    console.error(error);
                }
            });
        }
        function actualiza_datos_evo(input) {

            $.ajax({
                url: "{{ route('formulario007.anamenesis_guardar_evo') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                data: {
                    id: $("#id").val(),
                    motivo_evolucion: $("#motivo_evolucion").val(),
                    problema_actual: $("#problema_actual").val(),
                    indicacion_evolucion: $("#indicacion_evolucion").val(),

                    fecha_atencion: $("#fecha_atencion").val(),
                    hora_atencion: $("#hora_atencion").val(),
                    temperatura: $("#temperatura").val(),
                    presion: $("#presion").val(),
                    altura: $("#altura").val(),
                    peso: $("#peso").val(),
                    frecuencia_respiratoria: $("#frecuencia_respiratoria").val(),
                    imc_kg: $("#imc_kg").val(),
                    perimetro_abdominal: $("#perimetro_abdominal").val(),
                    hemoglobina_capilar: $("#hemoglobina_capilar").val(),
                    glucosa_capilar: $("#glucosa_capilar").val(),
                    pulsioximetri: $("#pulsioximetri").val(),
                },
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        
                    }
                },
                error: function(error) {
                    console.error(error);
                }
            });
        }

        
    tinymce.init({
        selector: '#tproblema_actual',
        inline: true,
        menubar: false,
        content_style: ".mce-content-body {font-size:14px;}",

        setup: function (editor) {
            editor.on('init', function (e) {
               var ed = tinyMCE.get('tproblema_actual');
               //alert(ed.getContent());
                $("#problema_actual").val(ed.getContent());

            });
        },

        init_instance_callback: function (editor) {
            editor.on('Change', function (e) {
                var ed = tinyMCE.get('tproblema_actual');
                $("#problema_actual").val(ed.getContent());
                actualiza_datos_evo();

            });
          }
    });
    </script>
</section>
@endsection