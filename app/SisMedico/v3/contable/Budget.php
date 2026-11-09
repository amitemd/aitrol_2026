<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $table = 'v3_budgets';
    protected $guarded = array();

    const STATUS_DRAFT = 'BORRADOR';
    const STATUS_APPROVED = 'APROBADO';
    const STATUS_CLOSED = 'CERRADO';

    public function lines()
    {
        return $this->hasMany(BudgetLine::class, 'id_presupuesto');
    }

    public function adjustments()
    {
        return $this->hasMany(BudgetAdjustment::class, 'id_presupuesto');
    }

    public function commitments()
    {
        return $this->hasMany(BudgetCommitment::class, 'id_presupuesto');
    }
}
