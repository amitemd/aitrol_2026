<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class BudgetLine extends Model
{
    protected $table = 'v3_budget_lines';
    protected $guarded = array();

    const TYPE_INCOME = 'INGRESO';
    const TYPE_EXPENSE = 'GASTO';

    public function budget()
    {
        return $this->belongsTo(Budget::class, 'id_presupuesto');
    }

    public function months()
    {
        return $this->hasMany(BudgetLineMonth::class, 'id_linea_presupuesto')->orderBy('mes');
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class, 'id_centro_costo');
    }

    public function commitments()
    {
        return $this->hasMany(BudgetCommitment::class, 'id_linea_presupuesto');
    }
}
