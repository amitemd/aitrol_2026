<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\ClinicalDocument;
use Sis_medico\Ho_Form008;
use Sis_medico\Ho_Solicitud;

class ClinicalEmergencyFormService
{
    /**
     * Devuelve el único Formulario 008 de una solicitud. La solicitud se
     * bloquea durante la búsqueda para que dos peticiones simultáneas no
     * creen formularios paralelos.
     */
    public function ensure(Ho_Solicitud $solicitud, array $attributes = array())
    {
        return DB::transaction(function () use ($solicitud, $attributes) {
            $locked = Ho_Solicitud::where('id', $solicitud->id)->lockForUpdate()->first();
            if (is_null($locked)) {
                throw new \RuntimeException('No existe la solicitud clínica asociada al Formulario 008.');
            }

            $form = Ho_Form008::where('id_solicitud', $locked->id)
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (!is_null($form)) {
                return $form;
            }

            // Un mismo episodio puede conservar más de una solicitud legado
            // durante sus transiciones. El Formulario 008 sigue siendo único
            // para el episodio clínico canónico.
            if (!empty($locked->encounter_id) && Schema::hasColumn('ho_form008', 'encounter_id')) {
                $form = Ho_Form008::where('encounter_id', $locked->encounter_id)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->first();
                if (!is_null($form)) return $form;
            }

            $defaults = array(
                'id_solicitud' => $locked->id,
                'id_agenda' => $locked->id_agenda,
                'fecha_creacion' => $locked->fecha_ingreso ?: date('Y-m-d H:i:s'),
            );

            $data = array_merge($defaults, $attributes);
            $data['id_solicitud'] = $locked->id;
            return Ho_Form008::create($data);
        });
    }

    public function audit()
    {
        if (!Schema::hasTable('ho_form008')) {
            return array('available' => false);
        }

        $groups = $this->duplicateGroups('id_solicitud');
        $episodeGroups = Schema::hasColumn('ho_form008', 'encounter_id')
            ? $this->duplicateGroups('encounter_id') : collect();
        $documentEpisodeGroups = $this->duplicateDocumentEpisodeGroups();

        $duplicateForms = 0;
        $incomplete = 0;
        $incompleteEpisodeForms = 0;
        foreach ($groups as $group) {
            $duplicateForms += (int) $group->total;
            $forms = Ho_Form008::where('id_solicitud', $group->id_solicitud)->get();
            foreach ($forms as $form) {
                if (!$this->isFilled($form->motivo) || !$this->isFilled($form->condicion)) {
                    $incomplete++;
                }
            }
        }
        foreach ($episodeGroups as $group) {
            $forms = Ho_Form008::where('encounter_id', $group->encounter_id)->get();
            foreach ($forms as $form) {
                if (!$this->isFilled($form->motivo) || !$this->isFilled($form->condicion)) {
                    $incompleteEpisodeForms++;
                }
            }
        }

        return array(
            'available' => true,
            'duplicate_groups' => count($groups),
            'duplicate_episode_groups' => count($episodeGroups),
            'duplicate_document_episode_groups' => count($documentEpisodeGroups),
            'duplicate_forms' => $duplicateForms,
            'incomplete_duplicate_forms' => $incomplete,
            'incomplete_episode_forms' => $incompleteEpisodeForms,
        );
    }

    /**
     * Completa únicamente metadatos faltantes cuando todos los hermanos de la
     * misma solicitud o episodio ofrecen un único valor no vacío. No elimina
     * registros, no sobrescribe valores y nunca modifica documentos firmados.
     */
    public function repairDuplicates()
    {
        $result = array('groups' => 0, 'forms_updated' => 0, 'fields_copied' => 0, 'signed_skipped' => 0, 'conflicts' => 0);
        if (!Schema::hasTable('ho_form008')) return $result;

        $ids = $this->duplicateGroups('id_solicitud')->pluck('id_solicitud');

        foreach ($ids as $solicitudId) {
            DB::transaction(function () use ($solicitudId, &$result) {
                Ho_Solicitud::where('id', $solicitudId)->lockForUpdate()->first();
                $forms = Ho_Form008::where('id_solicitud', $solicitudId)->orderBy('id')->lockForUpdate()->get();
                if ($forms->count() < 2) return;

                $result['groups']++;
                $fields = array('id_agenda', 'encounter_id', 'motivo', 'condicion', 'id_usuariocrea');
                $canonical = array();
                foreach ($fields as $field) {
                    $values = array();
                    foreach ($forms as $form) {
                        $value = $form->getAttribute($field);
                        if ($this->isFilled($value)) $values[(string) $value] = $value;
                    }
                    if (count($values) === 1) $canonical[$field] = reset($values);
                    elseif (count($values) > 1) $result['conflicts']++;
                }

                foreach ($forms as $form) {
                    $changes = array();
                    foreach ($canonical as $field => $value) {
                        if (!$this->isFilled($form->getAttribute($field))) $changes[$field] = $value;
                    }
                    if (!count($changes)) continue;

                    if ($this->isSigned($form->id)) {
                        $result['signed_skipped']++;
                        continue;
                    }

                    $form->fill($changes);
                    $form->save();
                    $result['forms_updated']++;
                    $result['fields_copied'] += count(array_intersect_key($changes, array_flip($fields)));
                }
            });
        }

        if (Schema::hasColumn('ho_form008', 'encounter_id')) {
            $encounterIds = $this->duplicateGroups('encounter_id')->pluck('encounter_id');
            foreach ($encounterIds as $encounterId) {
                DB::transaction(function () use ($encounterId, &$result) {
                    $forms = Ho_Form008::where('encounter_id', $encounterId)->orderBy('id')->lockForUpdate()->get();
                    if ($forms->count() < 2) return;
                    $result['groups']++;
                    $this->mergeMissing($forms, array('motivo', 'condicion'), $result);
                });
            }
        }

        // El expediente transversal es la fuente de verdad para el cierre.
        // También repara vínculos históricos cuyo encounter_id quedó ausente
        // o divergente en la tabla legado ho_form008.
        foreach ($this->duplicateDocumentEpisodeGroups() as $group) {
            DB::transaction(function () use ($group, &$result) {
                $sourceIds = ClinicalDocument::where('source_type', 'ho_form008')
                    ->where('encounter_id', $group->encounter_id)
                    ->orderBy('id')
                    ->pluck('source_id')
                    ->map(function ($id) { return (int) $id; })
                    ->all();
                $forms = Ho_Form008::whereIn('id', $sourceIds)->orderBy('id')->lockForUpdate()->get();
                if ($forms->count() < 2) return;
                $result['groups']++;
                $this->mergeMissing($forms, array('motivo', 'condicion'), $result);
            });
        }

        return $result;
    }

    private function duplicateGroups($field)
    {
        return DB::table('ho_form008')
            ->select($field, DB::raw('COUNT(*) as total'))
            ->whereNotNull($field)
            ->groupBy($field)
            ->havingRaw('COUNT(*) > 1')
            ->get();
    }

    private function duplicateDocumentEpisodeGroups()
    {
        if (!Schema::hasTable('clinical_documents')) return collect();
        return DB::table('clinical_documents')
            ->select('encounter_id', DB::raw('COUNT(*) as total'))
            ->where('source_type', 'ho_form008')
            ->whereNotNull('encounter_id')
            ->groupBy('encounter_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();
    }

    private function mergeMissing($forms, array $fields, array &$result)
    {
        $canonical = array();
        foreach ($fields as $field) {
            $values = array();
            foreach ($forms as $form) {
                $value = $form->getAttribute($field);
                if ($this->isFilled($value)) $values[(string) $value] = $value;
            }
            if (count($values) === 1) $canonical[$field] = reset($values);
            elseif (count($values) > 1) $result['conflicts']++;
        }

        foreach ($forms as $form) {
            $changes = array();
            foreach ($canonical as $field => $value) {
                if (!$this->isFilled($form->getAttribute($field))) $changes[$field] = $value;
            }
            if (!count($changes)) continue;
            if ($this->isSigned($form->id)) {
                $result['signed_skipped']++;
                continue;
            }
            $form->fill($changes);
            $form->save();
            $result['forms_updated']++;
            $result['fields_copied'] += count($changes);
        }
    }

    private function isSigned($formId)
    {
        if (!Schema::hasTable('clinical_documents')) return false;
        return ClinicalDocument::where('source_type', 'ho_form008')
            ->where('source_id', (string) $formId)
            ->whereNotNull('signed_at')
            ->exists();
    }

    private function isFilled($value)
    {
        if (is_bool($value) || is_numeric($value)) return true;
        return trim(str_replace('&nbsp;', ' ', strip_tags((string) $value))) !== '';
    }
}
