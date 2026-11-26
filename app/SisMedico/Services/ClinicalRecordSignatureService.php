<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Sis_medico\ClinicalDocument;
use Sis_medico\ClinicalEncounter;
use Sis_medico\Hc_Anestesiologia;
use Sis_medico\hc_child_pugh;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Epicrisis_Final;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Evolucion_Anestesiologo;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Ho_Form008;
use Sis_medico\Ho_Hc_Receta_Evolucion;
use Sis_medico\HospitalEpisodeFormDraft;
use Sis_medico\Rc_antecedentes;
use Sis_medico\Rc_indicaciones;
use Sis_medico\Rc_pre_anestesica;
use Sis_medico\Rc_resultados;

/** Firma y verifica registros clínicos con HMAC y, cuando existe, evidencia P12 del profesional. */
class ClinicalRecordSignatureService
{
    const VERSION = 1;

    const EVOLUTION = 'hc_evolution';

    const EMERGENCY = 'emergency_evolution';

    const FORM_008 = 'emergency_form_008';

    const FORM_006 = 'hospital_epicrisis_form_006';

    const FORM_017 = 'hospital_surgical_protocol_form_017';

    const FORM_017_DRAFT = 'hospital_surgical_protocol_draft_017';

    const FORM_018 = 'hospital_preanesthetic_form_018';

    const FORM_018A = 'hospital_transanesthetic_form_018a';

    const FORM_019 = 'hospital_postanesthetic_form_019';

    public function issueEvolution(Hc_Evolucion $evolution)
    {
        return $this->issue([
            'v' => self::VERSION,
            'type' => self::EVOLUTION,
            'id' => (string) $evolution->id,
            'hash' => $this->evolutionHash($evolution),
        ]);
    }

    public function issueEmergency($entryId)
    {
        $entry = Schema::hasTable('clinical_emergency_care_entries')
            ? DB::table('clinical_emergency_care_entries')->where('id', $entryId)->first()
            : null;
        if (! $entry) {
            return null;
        }

        return $this->issue([
            'v' => self::VERSION,
            'type' => self::EMERGENCY,
            'id' => (string) $entry->id,
            'hash' => $this->emergencyHash($entry),
        ]);
    }

    public function issueForm008(Ho_Form008 $form)
    {
        return $this->issue([
            'v' => self::VERSION,
            'type' => self::FORM_008,
            'id' => (string) $form->id,
            'hash' => $this->form008Hash($form),
        ]);
    }

    /**
     * Emite la prueba visible del F-006 únicamente cuando la epicrisis ya fue
     * firmada en el expediente clínico longitudinal.
     */
    public function issueForm006(Hc_Epicrisis_Final $epicrisis, ?ClinicalDocument $document = null)
    {
        $document = $document ?: $this->signedEpicrisisDocument($epicrisis->id);
        if (! $document || $document->source_type !== 'hc_epicrisis_final'
            || (string) $document->source_id !== (string) $epicrisis->id
            || ! $document->signed_at || ! $document->signed_by || ! $document->content_hash) {
            return null;
        }
        if (! hash_equals((string) $document->content_hash, $this->epicrisisHash($epicrisis))) {
            return null;
        }

        return $this->issue([
            'v' => self::VERSION,
            'type' => self::FORM_006,
            'id' => (string) $epicrisis->id,
            'document_id' => (string) $document->id,
            'hash' => (string) $document->content_hash,
            'signed_by' => (string) $document->signed_by,
            'signed_at' => $this->dateValue($document->signed_at),
        ]);
    }

    public function issueForm018(Rc_pre_anestesica $record)
    {
        return $this->issue([
            'v' => self::VERSION,
            'type' => self::FORM_018,
            'id' => (string) $record->id,
            'hash' => $this->form018Hash($record),
        ]);
    }

    public function issueForm017(hc_protocolo $record)
    {
        $document = $this->signedProtocolDocument($record->id);
        $currentHash = $this->protocolHash($record);
        if (! $document || ! $document->content_hash) {
            return null;
        }

        $effectiveHash = $this->effectiveDocumentHash($document);
        if (! $effectiveHash || ! hash_equals($effectiveHash, $currentHash)) {
            return null;
        }

        return array_merge($this->issue([
            'v' => self::VERSION,
            'type' => self::FORM_017,
            'id' => (string) $record->id,
            'document_id' => (string) $document->id,
            'hash' => $currentHash,
            'signed_by' => (string) $document->signed_by,
            'signed_at' => $this->dateValue($document->signed_at),
            'certificate_fingerprint' => trim((string) $document->electronic_certificate_fingerprint),
        ]), [
            'author_id' => (string) $document->signed_by,
            'signed_at' => $this->dateValue($document->signed_at),
        ]);
    }

    public function issueForm017Draft(HospitalEpisodeFormDraft $entry)
    {
        if ($entry->form_code !== '017') {
            return null;
        }
        $currentHash = $this->draftHash($entry);
        $document = Schema::hasTable('clinical_documents')
            ? ClinicalDocument::where('source_type', 'hospital_episode_form_draft')
                ->where('source_id', (string) $entry->id)
                ->whereNotNull('signed_at')->whereNotNull('signed_by')->first()
            : null;
        if (! $document || ! $document->content_hash || ! $document->electronic_signature) {
            return null;
        }

        if (! hash_equals((string) $document->content_hash, $currentHash)
            || ! $this->validCertificateEvidence($document, $currentHash, [
                'certificate_fingerprint' => $document->electronic_certificate_fingerprint,
            ])) {
            return null;
        }

        return array_merge($this->issue([
            'v' => self::VERSION,
            'type' => self::FORM_017_DRAFT,
            'id' => (string) $entry->id,
            'document_id' => (string) $document->id,
            'hash' => $currentHash,
            'signed_by' => (string) $document->signed_by,
            'signed_at' => $this->dateValue($document->signed_at),
            'certificate_fingerprint' => (string) $document->electronic_certificate_fingerprint,
        ]), [
            'author_id' => (string) $document->signed_by,
            'signed_at' => $this->dateValue($document->signed_at),
            'certificate_verified' => true,
        ]);
    }

    public function issueForm018A(Hc_Anestesiologia $record)
    {
        return $this->issue([
            'v' => self::VERSION,
            'type' => self::FORM_018A,
            'id' => (string) $record->id,
            'hash' => $this->form018AHash($record),
        ]);
    }

    public function issueForm019(Hc_Evolucion_Anestesiologo $record)
    {
        return $this->issue([
            'v' => self::VERSION,
            'type' => self::FORM_019,
            'id' => (string) $record->id,
            'hash' => $this->form019Hash($record),
        ]);
    }

    public function verify($encodedPayload, $providedSignature)
    {
        $encodedPayload = trim((string) $encodedPayload);
        $providedSignature = trim((string) $providedSignature);
        $claims = $this->decode($encodedPayload);
        if (! $claims || ! $this->validSignature($encodedPayload, $providedSignature)) {
            return $this->invalid('La firma criptográfica no coincide con el documento.');
        }
        if ((int) ($claims['v'] ?? 0) !== self::VERSION) {
            return $this->invalid('La versión de la firma no es compatible.', $claims);
        }

        if (($claims['type'] ?? '') === self::EVOLUTION) {
            $evolution = Hc_Evolucion::find($claims['id'] ?? null);
            if (! $evolution) {
                return $this->invalid('La evolución ya no existe.', $claims);
            }
            $currentHash = $this->evolutionHash($evolution);
            $valid = hash_equals((string) ($claims['hash'] ?? ''), $currentHash);

            return [
                'valid' => $valid,
                'reason' => $valid ? 'La huella y la firma criptográfica son válidas.' : 'La evolución cambió después de emitir la firma.',
                'claims' => $claims,
                'author_id' => $valid ? (string) $evolution->id_usuariocrea : null,
                'recorded_at' => $valid ? $this->dateValue($evolution->created_at) : null,
                'fingerprint' => $currentHash,
                'record_type' => self::EVOLUTION,
            ];
        }

        if (($claims['type'] ?? '') === self::EMERGENCY && Schema::hasTable('clinical_emergency_care_entries')) {
            $entry = DB::table('clinical_emergency_care_entries')->where('id', $claims['id'] ?? null)->first();
            if (! $entry) {
                return $this->invalid('La evolución de emergencia ya no existe.', $claims);
            }
            $currentHash = $this->emergencyHash($entry);
            $valid = hash_equals((string) ($claims['hash'] ?? ''), $currentHash);

            return [
                'valid' => $valid,
                'reason' => $valid ? 'La huella y la firma criptográfica son válidas.' : 'La evolución cambió después de emitir la firma.',
                'claims' => $claims,
                'author_id' => $valid ? (string) $entry->recorded_by : null,
                'recorded_at' => $valid ? $this->dateValue($entry->created_at) : null,
                'fingerprint' => $currentHash,
                'record_type' => self::EMERGENCY,
            ];
        }

        if (($claims['type'] ?? '') === self::FORM_008) {
            $form = Ho_Form008::find($claims['id'] ?? null);
            if (! $form) {
                return $this->invalid('El Formulario 008 ya no existe.', $claims);
            }
            $currentHash = $this->form008Hash($form);
            $valid = hash_equals((string) ($claims['hash'] ?? ''), $currentHash);

            return [
                'valid' => $valid,
                'reason' => $valid ? 'La huella y la firma criptográfica del Formulario 008 son válidas.' : 'El Formulario 008 cambió después de emitir la firma.',
                'claims' => $claims,
                'author_id' => $valid ? (string) $form->id_usuariocrea : null,
                'recorded_at' => $valid ? $this->dateValue($form->created_at) : null,
                'fingerprint' => $currentHash,
                'record_type' => self::FORM_008,
            ];
        }

        if (($claims['type'] ?? '') === self::FORM_006) {
            $epicrisis = Hc_Epicrisis_Final::find($claims['id'] ?? null);
            if (! $epicrisis) {
                return $this->invalid('El Formulario 006 ya no existe.', $claims);
            }
            $document = $this->signedEpicrisisDocument($epicrisis->id);
            if (! $document || (string) $document->id !== (string) ($claims['document_id'] ?? '')) {
                return $this->invalid('No existe una firma electrónica vigente para el Formulario 006.', $claims);
            }
            $currentHash = $this->epicrisisHash($epicrisis);
            $valid = hash_equals((string) ($claims['hash'] ?? ''), (string) $document->content_hash)
                && hash_equals((string) $document->content_hash, $currentHash)
                && hash_equals((string) ($claims['signed_by'] ?? ''), (string) $document->signed_by)
                && hash_equals((string) ($claims['signed_at'] ?? ''), $this->dateValue($document->signed_at));

            return [
                'valid' => $valid,
                'reason' => $valid ? 'La firma electrónica y la huella del Formulario 006 son válidas.' : 'La epicrisis no coincide con el contenido firmado.',
                'claims' => $claims,
                'author_id' => $valid ? (string) $document->signed_by : null,
                'recorded_at' => $valid ? $this->dateValue($document->signed_at) : null,
                'fingerprint' => $currentHash,
                'record_type' => self::FORM_006,
            ];
        }

        if (($claims['type'] ?? '') === self::FORM_018) {
            $record = Rc_pre_anestesica::find($claims['id'] ?? null);
            if (! $record) {
                return $this->invalid('El Formulario 018 ya no existe.', $claims);
            }
            $currentHash = $this->form018Hash($record);
            $valid = hash_equals((string) ($claims['hash'] ?? ''), $currentHash);
            $anesthesia = Hc_Anestesiologia::find($record->id_hc_anestesiologia);

            return $this->recordVerification($valid, $claims, $currentHash, self::FORM_018,
                $record->id_usuariocrea ?: optional($anesthesia)->id_anestesiologo,
                $record->updated_at, 'El Formulario 018 cambió después de emitir la firma.');
        }

        if (($claims['type'] ?? '') === self::FORM_017) {
            $record = hc_protocolo::find($claims['id'] ?? null);
            if (! $record) {
                return $this->invalid('El Formulario 017 ya no existe.', $claims);
            }
            $document = $this->signedProtocolDocument($record->id);
            $currentHash = $this->protocolHash($record);
            if (empty($claims['document_id'])) {
                $identity = $this->form017Identity($record);
                $valid = $identity['author_id'] && $identity['company_id']
                    && hash_equals((string) ($claims['hash'] ?? ''), $currentHash)
                    && hash_equals((string) ($claims['signed_by'] ?? ''), $identity['author_id'])
                    && app(UserElectronicCertificateService::class)->verifyClinicalHashForUser(
                        $identity['author_id'], $identity['company_id'], $currentHash,
                        (string) ($claims['certificate_signature'] ?? ''),
                        (string) ($claims['certificate_fingerprint'] ?? '')
                    );

                return $this->recordVerification($valid, $claims, $currentHash, self::FORM_017,
                    $identity['author_id'], $claims['signed_at'] ?? null,
                    'El Formulario 017 no coincide con la firma electrónica del profesional.');
            }
            if (! $document || (string) $document->id !== (string) ($claims['document_id'] ?? '')) {
                return $this->invalid('No existe una firma electrónica vigente para el Formulario 017.', $claims);
            }
            $effectiveHash = $this->effectiveDocumentHash($document);
            $valid = $effectiveHash && hash_equals((string) ($claims['hash'] ?? ''), $currentHash)
                && hash_equals($effectiveHash, $currentHash)
                && hash_equals((string) ($claims['signed_by'] ?? ''), (string) $document->signed_by)
                && hash_equals((string) ($claims['signed_at'] ?? ''), $this->dateValue($document->signed_at))
                && $this->validCertificateEvidence($document, $currentHash, $claims);

            return $this->recordVerification($valid, $claims, $currentHash, self::FORM_017,
                $document->signed_by, $document->signed_at,
                'El Formulario 017 no coincide con el protocolo firmado.');
        }

        if (($claims['type'] ?? '') === self::FORM_017_DRAFT) {
            $entry = HospitalEpisodeFormDraft::find($claims['id'] ?? null);
            if (! $entry || $entry->form_code !== '017') {
                return $this->invalid('El borrador firmado del Formulario 017 ya no existe.', $claims);
            }
            $document = Schema::hasTable('clinical_documents')
                ? ClinicalDocument::where('source_type', 'hospital_episode_form_draft')
                    ->where('source_id', (string) $entry->id)->whereNotNull('signed_at')->first()
                : null;
            $currentHash = $this->draftHash($entry);
            if (empty($claims['document_id'])) {
                $identity = $this->form017DraftIdentity($entry);
                $valid = $identity['author_id'] && $identity['company_id']
                    && hash_equals((string) ($claims['hash'] ?? ''), $currentHash)
                    && hash_equals((string) ($claims['signed_by'] ?? ''), $identity['author_id'])
                    && app(UserElectronicCertificateService::class)->verifyClinicalHashForUser(
                        $identity['author_id'], $identity['company_id'], $currentHash,
                        (string) ($claims['certificate_signature'] ?? ''),
                        (string) ($claims['certificate_fingerprint'] ?? '')
                    );

                return $this->recordVerification($valid, $claims, $currentHash, self::FORM_017_DRAFT,
                    $identity['author_id'], $claims['signed_at'] ?? null,
                    'El borrador del Formulario 017 no coincide con la firma electrónica del profesional.');
            }
            if (! $document || (string) $document->id !== (string) ($claims['document_id'] ?? '')) {
                return $this->invalid('No existe la firma electrónica del Formulario 017.', $claims);
            }
            $valid = hash_equals((string) ($claims['hash'] ?? ''), $currentHash)
                && hash_equals((string) $document->content_hash, $currentHash)
                && hash_equals((string) ($claims['signed_by'] ?? ''), (string) $document->signed_by)
                && hash_equals((string) ($claims['signed_at'] ?? ''), $this->dateValue($document->signed_at))
                && $this->validCertificateEvidence($document, $currentHash, $claims);

            return $this->recordVerification($valid, $claims, $currentHash, self::FORM_017_DRAFT,
                $document->signed_by, $document->signed_at,
                'El borrador del Formulario 017 cambió después de ser firmado.');
        }

        if (($claims['type'] ?? '') === self::FORM_018A) {
            $record = Hc_Anestesiologia::find($claims['id'] ?? null);
            if (! $record) {
                return $this->invalid('El Formulario 018A ya no existe.', $claims);
            }
            $currentHash = $this->form018AHash($record);
            $valid = hash_equals((string) ($claims['hash'] ?? ''), $currentHash);

            return $this->recordVerification($valid, $claims, $currentHash, self::FORM_018A,
                $record->id_anestesiologo ?: $record->id_usuariocrea,
                $record->updated_at, 'El Formulario 018A cambió después de emitir la firma.');
        }

        if (($claims['type'] ?? '') === self::FORM_019) {
            $record = Hc_Evolucion_Anestesiologo::find($claims['id'] ?? null);
            if (! $record) {
                return $this->invalid('El Formulario 019 ya no existe.', $claims);
            }
            $currentHash = $this->form019Hash($record);
            $valid = hash_equals((string) ($claims['hash'] ?? ''), $currentHash);
            $anesthesia = $record->id_hc_anestesiologia
                ? Hc_Anestesiologia::find($record->id_hc_anestesiologia)
                : Hc_Anestesiologia::where('id_hc_procedimientos', $record->hc_id_procedimiento)->first();

            return $this->recordVerification($valid, $claims, $currentHash, self::FORM_019,
                $record->id_usuariocrea ?: optional($anesthesia)->id_anestesiologo,
                $record->updated_at, 'El Formulario 019 cambió después de emitir la firma.');
        }

        return $this->invalid('El tipo de registro firmado no es válido.', $claims);
    }

    private function issue(array $claims)
    {
        $payload = $this->base64UrlEncode(json_encode($claims, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $signature = $this->base64UrlEncode(hash_hmac('sha256', $payload, $this->key(), true));

        return [
            'payload' => $payload,
            'signature' => $signature,
            'validation_url' => route('clinical-signatures.verify', ['p' => $payload, 's' => $signature]),
            'fingerprint' => $claims['hash'],
        ];
    }

    private function evolutionHash(Hc_Evolucion $evolution)
    {
        $physicalExam = hc_child_pugh::where('id_hc_evolucion', $evolution->id)->value('examen_fisico');
        $diagnoses = Hc_Cie10::where('hc_id_procedimiento', $evolution->hc_id_procedimiento)
            ->orderBy('id')->get(['cie10', 'presuntivo_definitivo', 'ingreso_egreso'])
            ->map(function ($diagnosis) {
                return $diagnosis->toArray();
            })->values()->all();
        $link = Ho_Hc_Receta_Evolucion::where('id_evolucion', $evolution->id)->first();
        $prescriptions = $link
            ? hc_receta_detalle::where('id_hc_receta', $link->id_receta)->orderBy('id')
                ->get(['id_medicina', 'nombre', 'cantidad', 'dosis'])
                ->map(function ($detail) {
                    return $detail->toArray();
                })->values()->all()
            : [];

        return hash('sha256', json_encode([
            'id' => (string) $evolution->id,
            'hcid' => (string) $evolution->hcid,
            'procedure_id' => (string) $evolution->hc_id_procedimiento,
            'reason' => (string) $evolution->motivo,
            'evolution' => (string) $evolution->cuadro_clinico,
            'author' => (string) $evolution->id_usuariocrea,
            'recorded_at' => $this->dateValue($evolution->created_at),
            'physical_exam' => (string) $physicalExam,
            'diagnoses' => $diagnoses,
            'prescriptions' => $prescriptions,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function emergencyHash($entry)
    {
        return hash('sha256', json_encode([
            'id' => (string) $entry->id,
            'request_id' => (string) $entry->solicitud_id,
            'section' => (string) $entry->section,
            'content' => (string) $entry->content,
            'author' => (string) $entry->recorded_by,
            'recorded_at' => $this->dateValue($entry->created_at),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function form008Hash(Ho_Form008 $form)
    {
        $document = [
            'form' => $this->canonicalRow($form->getAttributes()),
            'sections' => [],
        ];
        $sectionTables = [
            'ho_form008_acc_vio_int',
            'ho_form008_examen_fisico',
            'ho_form008_embarazo',
            'ho_form008_diagnostico',
            'ho_form008_glasgow',
        ];
        foreach ($sectionTables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'id_ho_form008')) {
                continue;
            }
            $document['sections'][$table] = DB::table($table)->where('id_ho_form008', $form->id)
                ->orderBy('id')->get()->map(function ($row) {
                    return $this->canonicalRow((array) $row);
                })->values()->all();
        }
        $requestSections = [
            'ho_lesiones008' => 'id_008',
            'ho_traspaso_sala008' => 'id_solicitud',
        ];
        foreach ($requestSections as $table => $foreignKey) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $foreignKey)) {
                continue;
            }
            $document['sections'][$table] = DB::table($table)->where($foreignKey, $form->id_solicitud)
                ->orderBy('id')->get()->map(function ($row) {
                    return $this->canonicalRow((array) $row);
                })->values()->all();
        }
        ksort($document['sections']);

        return hash('sha256', json_encode($document, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function form018Hash(Rc_pre_anestesica $record)
    {
        $anesthesia = Hc_Anestesiologia::find($record->id_hc_anestesiologia);

        return hash('sha256', json_encode([
            'record' => $this->canonicalRow($record->getAttributes()),
            'responsible' => $anesthesia ? [
                'anesthesia_id' => (string) $anesthesia->id,
                'anesthesiologist_id' => (string) $anesthesia->id_anestesiologo,
            ] : null,
            'antecedentes' => Rc_antecedentes::where('id_rc_pre_anestesica', $record->id)->where('estado', 1)
                ->orderBy('id')->get()->map(function ($row) {
                    return $this->canonicalRow($row->getAttributes());
                })->all(),
            'resultados' => Rc_resultados::where('id_rc_pre_anestesia', $record->id)->where('estado', 1)
                ->orderBy('id')->get()->map(function ($row) {
                    return $this->canonicalRow($row->getAttributes());
                })->all(),
            'indicaciones' => Rc_indicaciones::where('id_rc_pre_anestesica', $record->id)->where('estado', 1)
                ->orderBy('id')->get()->map(function ($row) {
                    return $this->canonicalRow($row->getAttributes());
                })->all(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function form018AHash(Hc_Anestesiologia $record)
    {
        $image = TransanestheticCanvasImage::dataUri($record->tran_anastesico_path)
            ?: TransanestheticCanvasImage::dataUri($record->url_imagen);

        return hash('sha256', json_encode([
            'record' => $this->canonicalRow($record->getAttributes()),
            'canvas_hash' => $image ? hash('sha256', $image) : null,
            'drogas' => Schema::hasTable('drogas_administradas')
                ? DB::table('drogas_administradas')->where('id_hc_anestesiologia', $record->id)->where('estado', 1)
                    ->orderBy('id')->get()->map(function ($row) {
                        return $this->canonicalRow((array) $row);
                    })->all()
                : [],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function form019Hash(Hc_Evolucion_Anestesiologo $record)
    {
        $anesthesia = $record->id_hc_anestesiologia
            ? Hc_Anestesiologia::find($record->id_hc_anestesiologia)
            : Hc_Anestesiologia::where('id_hc_procedimientos', $record->hc_id_procedimiento)->first();

        return hash('sha256', json_encode([
            'record' => $this->canonicalRow($record->getAttributes()),
            'responsible' => $anesthesia ? [
                'anesthesia_id' => (string) $anesthesia->id,
                'anesthesiologist_id' => (string) $anesthesia->id_anestesiologo,
                'postanesthetic_image' => (string) $anesthesia->post_anastesico_path,
            ] : null,
            'indicaciones' => Schema::hasTable('hc_evolucion_indicacion')
                ? DB::table('hc_evolucion_indicacion')->where('id_evolucion', $record->id)
                    ->orderBy('id')->get()->map(function ($row) {
                        return $this->canonicalRow((array) $row);
                    })->all()
                : [],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function recordVerification($valid, array $claims, $currentHash, $type, $authorId, $recordedAt, $changedReason)
    {
        return [
            'valid' => $valid,
            'reason' => $valid ? 'La firma electrónica y la huella del formulario son válidas.' : $changedReason,
            'claims' => $claims,
            'author_id' => $valid ? (string) $authorId : null,
            'recorded_at' => $valid ? $this->dateValue($recordedAt) : null,
            'fingerprint' => $currentHash,
            'record_type' => $type,
        ];
    }

    private function signedEpicrisisDocument($epicrisisId)
    {
        if (! Schema::hasTable('clinical_documents')) {
            return null;
        }

        return ClinicalDocument::where('source_type', 'hc_epicrisis_final')
            ->where('source_id', (string) $epicrisisId)
            ->whereNotNull('signed_at')->whereNotNull('signed_by')
            ->first();
    }

    private function signedProtocolDocument($protocolId)
    {
        if (! Schema::hasTable('clinical_documents')) {
            return null;
        }

        return ClinicalDocument::where('source_type', 'hc_protocolo')
            ->where('source_id', (string) $protocolId)
            ->whereNotNull('signed_at')->whereNotNull('signed_by')
            ->first();
    }

    private function effectiveDocumentHash(ClinicalDocument $document)
    {
        if (Schema::hasTable('clinical_document_amendments')) {
            $amendment = DB::table('clinical_document_amendments')
                ->where('clinical_document_id', $document->id)
                ->orderByDesc('version')->first();
            if ($amendment && ! empty($amendment->new_hash)) {
                return (string) $amendment->new_hash;
            }
        }

        return trim((string) $document->content_hash);
    }

    /**
     * Los documentos históricos conservan la validación HMAC existente. Para
     * las nuevas firmas con P12 también se comprueba la firma RSA y la huella
     * del certificado público almacenado con el documento.
     */
    private function validCertificateEvidence(ClinicalDocument $document, $contentHash, array $claims)
    {
        $signature = trim((string) $document->electronic_signature);
        if ($signature === '') {
            return true;
        }

        $fingerprint = strtolower(trim((string) $document->electronic_certificate_fingerprint));
        if ($fingerprint === '' || ! hash_equals($fingerprint,
            strtolower(trim((string) ($claims['certificate_fingerprint'] ?? ''))))) {
            return false;
        }

        return app(UserElectronicCertificateService::class)->verifyClinicalHash(
            $contentHash,
            $signature,
            (string) $document->electronic_certificate_pem
        );
    }

    private function form017Identity(hc_protocolo $record)
    {
        $procedure = $record->procedimiento;
        $history = $record->historiaclinica ?: ($procedure ? $procedure->historia : null);
        $encounter = $history ? $history->encounter : null;
        $authorId = trim((string) ($record->id_usuariocrea
            ?: ($procedure ? ($procedure->id_doctor_examinador2 ?: $procedure->id_doctor_examinador) : '')));
        $companyId = trim((string) ($encounter ? $encounter->id_empresa
            : ($procedure ? $procedure->id_empresa : session('id_empresa'))));

        return ['author_id' => $authorId, 'company_id' => $companyId];
    }

    private function protocolHash(hc_protocolo $record)
    {
        // Debe ser idéntico a ClinicalRecordService::canonicalContent().
        // toArray() respeta los casts del modelo usados cuando se registró el
        // documento; getAttributes() podía producir otra huella para fechas o
        // valores serializados aunque el contenido clínico fuera el mismo.
        $row = $record->toArray();
        foreach (['updated_at', 'created_at', 'ip_creacion', 'ip_modificacion', 'id_usuariomod'] as $field) {
            unset($row[$field]);
        }
        $normalize = function ($value) use (&$normalize) {
            if (! is_array($value)) {
                return $value;
            }
            ksort($value);
            foreach ($value as $key => $item) {
                $value[$key] = $normalize($item);
            }

            return $value;
        };
        $row = $normalize($row);

        return hash('sha256', json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function draftHash(HospitalEpisodeFormDraft $entry)
    {
        $payload = (array) $entry->payload;
        $normalize = function ($value) use (&$normalize) {
            if (! is_array($value)) {
                return $value;
            }
            ksort($value);
            foreach ($value as $key => $item) {
                $value[$key] = $normalize($item);
            }

            return $value;
        };

        return hash('sha256', json_encode($normalize($payload), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function form017DraftIdentity(HospitalEpisodeFormDraft $entry)
    {
        $encounter = ClinicalEncounter::find($entry->encounter_id);

        return [
            'author_id' => trim((string) ($entry->updated_by ?: $entry->created_by)),
            'company_id' => trim((string) ($encounter ? $encounter->id_empresa : '')),
        ];
    }

    /** Debe coincidir con la canonicalización usada por ClinicalRecordService. */
    private function epicrisisHash(Hc_Epicrisis_Final $epicrisis)
    {
        $row = $epicrisis->getAttributes();
        foreach (['updated_at', 'created_at', 'ip_creacion', 'ip_modificacion', 'id_usuariomod'] as $field) {
            unset($row[$field]);
        }
        ksort($row);

        return hash('sha256', json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function canonicalRow(array $row)
    {
        // La huella representa contenido clínico. Reabrir o volver a guardar
        // sin cambios no debe invalidar una impresión anterior por metadatos.
        unset($row['updated_at'], $row['id_usuariomod'], $row['ip_modificacion']);
        ksort($row);
        foreach ($row as $key => $value) {
            if ($value instanceof \DateTimeInterface) {
                $row[$key] = $this->dateValue($value);
            } elseif (is_bool($value)) {
                $row[$key] = $value ? 1 : 0;
            }
        }

        return $row;
    }

    private function validSignature($payload, $signature)
    {
        if ($payload === '' || $signature === '') {
            return false;
        }
        $expected = $this->base64UrlEncode(hash_hmac('sha256', $payload, $this->key(), true));

        return hash_equals($expected, $signature);
    }

    private function decode($payload)
    {
        $json = $this->base64UrlDecode($payload);
        $claims = $json !== false ? json_decode($json, true) : null;

        return is_array($claims) ? $claims : null;
    }

    private function key()
    {
        $key = (string) config('app.key');
        if ($key === '') {
            throw new RuntimeException('La aplicación no tiene una clave para firmar registros clínicos.');
        }
        if (strpos($key, 'base64:') === 0) {
            $decoded = base64_decode(substr($key, 7), true);
            if ($decoded !== false) {
                $key = $decoded;
            }
        }

        return hash_hmac('sha256', 'aitrol-clinical-record-signature-v1', $key, true);
    }

    private function invalid($reason, array $claims = [])
    {
        return [
            'valid' => false, 'reason' => $reason, 'claims' => $claims,
            'author_id' => null, 'recorded_at' => null, 'fingerprint' => null, 'record_type' => null,
        ];
    }

    private function dateValue($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return trim((string) $value);
    }

    private function base64UrlEncode($value)
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode($value)
    {
        $remainder = strlen($value) % 4;
        if ($remainder) {
            $value .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($value, '-_', '+/'), true);
    }
}
