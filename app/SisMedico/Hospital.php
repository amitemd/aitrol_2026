<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospital extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function salas(): HasMany
    {
        return $this->hasMany(Sala::class, 'id_hospital');
    }

    public function scopeForCompany(Builder $query, string $companyId): Builder
    {
        return $query->where($this->qualifyColumn('id_empresa'), $companyId);
    }
}
