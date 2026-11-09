<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ho_Solicitud;

class ClinicalEmergencyFormUpdateService
{
    private $patientData = array('nacionalidad', 'grupo_prioritario', 'especifique_grupo',
        'autoidentificacion_etnica', 'nacionalidad_etnica', 'pueblos', 'nivel_educacion',
        'estado_nivel_educacion', 'empresa_trabajo', 'provincia', 'canton', 'parroquia',
        'barrio', 'calle_principal', 'calle_secundaria', 'referencia', 'llamar_a',
        'parentesco_afinidad', 'telefono_inst_per_paci', 'forma_llegada', 'fuente_informacion',
        'inst_per_paci_entrega');

    private $eventData = array('condicion', 'motivo', 'lugar_evento', 'direccion_evento', 'custodia_policial');

    private $eventFlags = array('accidente_transito', 'caida', 'quemadura', 'mordedura',
        'ahogamiento', 'cuerpo_extrano', 'aplastamiento', 'otro_accidente', 'violencia_armf',
        'violencia_armcp', 'violencia_rina', 'violencia_familiar', 'abuso_fisico',
        'abuso_psicologico', 'abuso_sexual');

    public function update(Request $request, $includeEvent)
    {
        $request->validate(array('id_solicitud' => 'required|integer'));

        return DB::transaction(function () use ($request, $includeEvent) {
            $requestRow = Ho_Solicitud::where('id', $request->input('id_solicitud'))->lockForUpdate()->firstOrFail();
            $patient = $requestRow->paciente;
            if (!$patient) {
                throw new \RuntimeException('La solicitud no posee datos completos del paciente.');
            }
            $patientData = $patient->ho_datos_paciente;
            if (!$patientData) {
                throw new \RuntimeException('La solicitud no posee datos completos del paciente.');
            }

            $patientData->update($request->only($this->patientData));
            $patient->update(array('ocupacion' => $request->input('ocupacion')));

            if ($includeEvent) {
                $form = $requestRow->form008()->lockForUpdate()->first();
                if (!$form) {
                    throw new \RuntimeException('La solicitud no posee Formulario 008.');
                }
                $values = $request->only($this->eventData);
                foreach ($this->eventFlags as $flag) {
                    $values[$flag] = $request->has($flag) ? $request->input($flag) : 0;
                }
                $form->update($values);
            }

            return 'ok';
        });
    }
}
