<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ClinicalDocumentCompleteness extends Model
{
    protected $table = 'clinical_document_completeness';

    protected $guarded = [];

    protected $casts = ['assessed_at' => 'datetime'];

    public function document()
    {
        return $this->belongsTo(ClinicalDocument::class, 'clinical_document_id');
    }

    public function requiredFields()
    {
        return (array) json_decode($this->required_fields, true);
    }

    public function missingFields()
    {
        return (array) json_decode($this->missing_fields, true);
    }
}
