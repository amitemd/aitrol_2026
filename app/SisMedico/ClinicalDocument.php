<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Services\ClinicalDocumentGuard;

class ClinicalDocument extends Model
{
    protected $table = 'clinical_documents';

    protected $guarded = [];

    protected $casts = ['authored_at' => 'datetime', 'signed_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::updating(function ($document) {
            ClinicalDocumentGuard::assertMutation($document);
        });
        static::deleting(function ($document) {
            if ($document->signed_at) {
                throw new \RuntimeException('Un documento firmado no puede eliminarse.');
            }
        });
    }

    public function record()
    {
        return $this->belongsTo(ClinicalRecord::class, 'clinical_record_id');
    }

    public function encounter()
    {
        return $this->belongsTo(ClinicalEncounter::class, 'encounter_id');
    }

    public function amendments()
    {
        return $this->hasMany(ClinicalDocumentAmendment::class, 'clinical_document_id');
    }

    public function completeness()
    {
        return $this->hasOne(ClinicalDocumentCompleteness::class, 'clinical_document_id');
    }
}
