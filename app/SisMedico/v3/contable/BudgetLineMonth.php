<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class BudgetLineMonth extends Model
{
    protected $table = 'v3_budget_line_months';
    protected $guarded = array();

    public function line()
    {
        return $this->belongsTo(BudgetLine::class, 'id_linea_presupuesto');
    }
}
