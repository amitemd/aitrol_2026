<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ClinicalRecord extends Model
{
    protected $table = 'clinical_records';

    protected $guarded = [];

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = ['opened_at' => 'datetime'];

    public function documents()
    {
        return $this->hasMany(ClinicalDocument::class, 'clinical_record_id');
    }
}
