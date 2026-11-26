<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ClinicalDocumentAmendment extends Model
{
    protected $table = 'clinical_document_amendments';

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::updating(function () {
            throw new \RuntimeException('Una adenda clínica es inmutable.');
        });
        static::deleting(function () {
            throw new \RuntimeException('Una adenda clínica no puede eliminarse.');
        });
    }
}
