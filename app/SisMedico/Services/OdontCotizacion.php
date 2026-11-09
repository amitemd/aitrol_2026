<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;

class OdontCotizacion
{
    // A quotation line represents one procedure on one tooth, regardless of the
    // number of surfaces or diagnoses that refer to that same treatment.
    public static function filas($payload, $catalogo, $atenciones, $hcid, $denticion = null)
    {
        $filas = array();
        $piezas = isset($payload['odontograma']['piezas']) ? $payload['odontograma']['piezas'] : array();
        foreach ($piezas as $fdi => $pieza) {
            if (($denticion === 'temporal' && (int) $fdi < 50) || ($denticion === 'permanente' && (int) $fdi >= 50)) { continue; }
            foreach (isset($pieza['detalles']) ? $pieza['detalles'] : array() as $detalle) {
                $diagnosticos = isset($detalle['diagnosticos']) ? $detalle['diagnosticos'] : array();
                if (!$diagnosticos && !empty($detalle['id_procedimiento'])) {
                    $diagnosticos = array(array('id_procedimiento' => $detalle['id_procedimiento'], 'cie10' => '', 'descripcion' => ''));
                }
                foreach ($diagnosticos as $dx) {
                    $id = isset($dx['id_procedimiento']) ? (string) $dx['id_procedimiento'] : '';
                    if ($id === '' || !isset($catalogo[$id])) { continue; }
                    $fecha = isset($detalle['fecha']) ? $detalle['fecha'] : '';
                    $clave = $hcid.':'.$fdi.':'.$fecha.':'.$id;
                    if (!isset($filas[$clave])) {
                        $filas[$clave] = array('clave' => $clave, 'fecha' => $fecha, 'pieza' => (string) $fdi,
                            'caras' => array(), 'diagnosticos' => array(), 'id_procedimiento' => $id,
                            'procedimiento' => $catalogo[$id]['nombre'], 'precio' => $catalogo[$id]['precio'],
                            'id_agenda' => null, 'atendido' => false);
                    }
                    if (!empty($detalle['nombre_cara'])) { $filas[$clave]['caras'][] = $detalle['nombre_cara']; }
                    elseif (!empty($detalle['cara'])) { $filas[$clave]['caras'][] = $detalle['cara']; }
                    if (!empty($dx['cie10'])) {
                        $filas[$clave]['diagnosticos'][] = !empty($dx['descripcion']) ? $dx['descripcion'] : $dx['cie10'];
                    }
                    foreach ($atenciones as $atencion) {
                        $vinculo = !empty($dx['id_paciente_procedimiento']) ? (string) $dx['id_paciente_procedimiento'] : '';
                        if ((string) $atencion->id_procedimiento_catalogo !== $id) { continue; }
                        if ($vinculo !== '') {
                            if ((string) $atencion->id !== $vinculo || ($atencion->pieza_fdi && (string) $atencion->pieza_fdi !== (string) $fdi)) { continue; }
                        } else {
                            // Legacy records are matched conservatively. A separate
                            // procedure visit must be linked explicitly in the editor.
                            if ((string) $atencion->hcid !== (string) $hcid ||
                                (string) $atencion->pieza_fdi !== (string) $fdi ||
                                substr((string) $atencion->fecha, 0, 10) !== $fecha) { continue; }
                        }
                        if ($atencion->id_agenda && $atencion->agenda_id) {
                            $filas[$clave]['id_agenda'] = $atencion->agenda_id;
                            if ((string) $atencion->estado_cita === '4') { $filas[$clave]['atendido'] = true; }
                        }
                    }
                }
            }
        }
        foreach ($filas as &$fila) {
            $fila['caras'] = implode(', ', array_unique($fila['caras']));
            $fila['diagnosticos'] = implode(' / ', array_unique($fila['diagnosticos']));
        }
        unset($fila);
        return array_values($filas);
    }

    public static function seleccionados($hcid, $configuracion = array())
    {
        $tablaFormulario = isset($configuracion['tabla_formulario']) ? $configuracion['tabla_formulario'] : 'odont_formulario033';
        $patronEspecialidad = isset($configuracion['patron_especialidad']) ? $configuracion['patron_especialidad'] : '%odontolog%';
        $formulario = DB::table($tablaFormulario)->where('hcid', $hcid)->orderBy('id', 'desc')->first();
        if (!$formulario) { return array(); }
        $especialidadIds = \Sis_medico\Especialidad::query()
            ->where('estado', 1)
            ->whereRaw('LOWER(nombre) LIKE ?', array($patronEspecialidad))
            ->pluck('id')
            ->all();
        if (!$especialidadIds) { return array(); }
        $catalogo = array();
        foreach (DB::table('procedimiento')->whereIn('id_especialidad', $especialidadIds)->where('estado', 1)->whereNull('deleted_at')->get(array('id', 'nombre', 'precio')) as $p) {
            $catalogo[(string) $p->id] = array('nombre' => $p->nombre, 'precio' => $p->precio);
        }
        $paciente = DB::table('paciente')->where('id', $formulario->id_paciente)->first();
        $denticion = $paciente && $paciente->fecha_nacimiento && \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age <= 11 ? 'temporal' : 'permanente';
        // La asociación con agenda queda fuera de este flujo por ahora. La
        // cotización refleja únicamente lo asignado en el Formulario 033.
        return self::paraCotizacion(self::filas(json_decode($formulario->snapshot_json, true) ?: array(), $catalogo, array(), $hcid, $denticion));
    }

    public static function pendientes($hcid, $configuracion = array())
    {
        return self::seleccionados($hcid, $configuracion);
    }

    public static function paraCotizacion($filas)
    {
        return array_values($filas);
    }

    public static function resumen($q)
    {
        return array('id' => $q->id, 'hcid' => $q->hcid, 'fecha' => substr($q->created_at, 0, 10),
            'estado' => $q->estado, 'total' => $q->total, 'version' => $q->version,
            'filas' => json_decode($q->detalle_json, true) ?: array());
    }
}
