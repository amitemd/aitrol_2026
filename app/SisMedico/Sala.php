<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sala extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sala';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'id_hospital');
    }

    public function scopeForHospitalCompany(Builder $query, int $hospitalId, string $companyId): Builder
    {
        return $query
            ->where($this->qualifyColumn('id_hospital'), $hospitalId)
            ->where($this->qualifyColumn('id_empresa'), $companyId);
    }
}
