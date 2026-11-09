<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class BudgetCommitment extends Model
{
    protected $table = 'v3_budget_commitments';
    protected $guarded = array();

    const STATUS_RESERVED = 'RESERVADO';
    const STATUS_ACCRUED = 'DEVENGADO';
    const STATUS_VOID = 'ANULADO';

    public function line()
    {
        return $this->belongsTo(BudgetLine::class, 'id_linea_presupuesto');
    }
}
