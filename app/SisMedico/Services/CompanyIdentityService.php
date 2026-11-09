<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Empresa;

class CompanyIdentityService
{
    public function resolve($internalId, $mode, $ruc)
    {
        if (!preg_match('/^[0-9]{13}$/', (string) $ruc)) {
            throw new \RuntimeException('El RUC de la empresa debe contener 13 digitos.');
        }
        if (!in_array($mode, array('create', 'update'), true)) {
            throw new \RuntimeException('Recargue la pagina de empresas antes de guardar.');
        }
        $company = null;
        if ($mode === 'update') {
            if (!preg_match('/^[1-9][0-9]*$/', (string) $internalId)) {
                throw new \RuntimeException('Falta el identificador interno de la empresa. Recargue la pagina.');
            }
            $company = Empresa::where('id_interno', $internalId)->lockForUpdate()->first();
            if (!$company) {
                throw new \RuntimeException('La empresa que intenta actualizar ya no existe.');
            }
        } elseif ((string) $internalId !== '') {
            throw new \RuntimeException('Una empresa nueva no debe enviar un identificador interno.');
        }
        $duplicate = Empresa::where('id', $ruc);
        if ($company) {
            $duplicate->where('id_interno', '<>', $company->id_interno);
        }
        if ($duplicate->exists()) {
            throw new \RuntimeException('El RUC ya se encuentra registrado en otra empresa.');
        }
        return $company;
    }

    /** Caller owns the transaction, including tax settings and audit. */
    public function update(Empresa $company, array $values)
    {
        $old = (string) $company->id;
        $new = (string) $values['id'];
        unset($values['id_interno']);
        $references = $old !== $new ? $this->unconstrainedReferences() : array();
        foreach ($references as $reference) {
            if (isset($reference->ENGINE) && strtoupper($reference->ENGINE) !== 'INNODB'
                && DB::table($reference->TABLE_NAME)->where($reference->COLUMN_NAME, $old)->exists()) {
                throw new \RuntimeException('No se puede cambiar el RUC: la tabla ' . $reference->TABLE_NAME . ' no admite transacciones.');
            }
        }
        if ($old !== $new && Schema::hasTable('de_empresa') && Schema::hasColumn('de_empresa', 'ruta_firma')) {
            foreach (DB::table('de_empresa')->where('id_empresa', $old)->get() as $settings) {
                DB::table('de_empresa')->where('id', $settings->id)->update(array(
                    'ruta_firma' => $this->signaturePath($old, $settings->ruta_firma),
                ));
            }
        }
        Empresa::where('id_interno', $company->id_interno)->update($values);
        foreach ($references as $reference) {
            DB::table($reference->TABLE_NAME)->where($reference->COLUMN_NAME, $old)
                ->update(array($reference->COLUMN_NAME => $new));
        }
    }

    public function signaturePath($oldRuc, $path)
    {
        // Preserve the existing file; all readers support the historical root.
        if (preg_match('/^[0-9]+$/', (string) $oldRuc) && $path
            && basename($path) === $path
            && is_file(storage_path('app/facturaelectronica/p12/' . $oldRuc . '/' . $path))) {
            return $oldRuc . '/' . $path;
        }
        return $path;
    }

    protected function unconstrainedReferences()
    {
        // Legacy company columns without a declared FK need explicit updates.
        // Do not touch a similarly named column referencing a different entity.
        return DB::select("SELECT c.TABLE_NAME, c.COLUMN_NAME, t.ENGINE FROM information_schema.COLUMNS c
            JOIN information_schema.TABLES t ON t.TABLE_SCHEMA=c.TABLE_SCHEMA AND t.TABLE_NAME=c.TABLE_NAME
            WHERE c.TABLE_SCHEMA=DATABASE() AND t.TABLE_TYPE='BASE TABLE' AND c.TABLE_NAME <> 'empresa'
              AND (c.COLUMN_NAME IN ('id_empresa', 'empresa_id')
                OR (c.COLUMN_NAME='empresa' AND c.TABLE_NAME IN ('ap_agrupado', 'ap_orden_venta', 'ct_nota_credito_cabecera')))
              AND NOT EXISTS (SELECT 1 FROM information_schema.KEY_COLUMN_USAGE k
                WHERE k.TABLE_SCHEMA=c.TABLE_SCHEMA AND k.TABLE_NAME=c.TABLE_NAME
                  AND k.COLUMN_NAME=c.COLUMN_NAME AND k.REFERENCED_TABLE_NAME IS NOT NULL)");
    }
}
