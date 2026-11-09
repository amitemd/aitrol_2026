<?php

namespace Sis_medico\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class V3TenantScope
{
    public function companyId()
    {
        $id = (string) session('id_empresa');
        if ($id === '') throw new \RuntimeException('No existe una empresa V3 activa.');
        return $id;
    }

    public function query($table, $companyColumn = 'id_empresa')
    {
        return DB::table($table)->where($companyColumn, $this->companyId());
    }

    public function owns($table, $id, $companyColumn = 'id_empresa', $idColumn = 'id')
    {
        return $this->query($table, $companyColumn)->where($idColumn, $id)->exists();
    }

    public function requireOwned($table, $id, $companyColumn = 'id_empresa', $idColumn = 'id')
    {
        if (!$this->owns($table, $id, $companyColumn, $idColumn)) abort(404);
        return true;
    }
}
