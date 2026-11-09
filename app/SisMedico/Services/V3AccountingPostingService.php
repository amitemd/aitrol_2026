<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Illuminate\Database\QueryException;
use InvalidArgumentException;
use RuntimeException;

class V3AccountingPostingService
{
    private $integrity;

    public function __construct(V3AccountingIntegrityService $integrity)
    {
        $this->integrity = $integrity;
    }

    public function post(array $header, array $lines, array $source)
    {
        foreach (array('id_empresa', 'fecha_asiento', 'observacion') as $required) {
            if (!isset($header[$required]) || $header[$required] === '') throw new InvalidArgumentException('Falta '.$required.' para registrar el asiento.');
        }
        foreach (array('modulo', 'tipo_origen', 'id_origen') as $required) {
            if (!isset($source[$required]) || trim((string) $source[$required]) === '') throw new InvalidArgumentException('Falta la identidad contable '.$required.'.');
        }
        if (!Schema::hasTable('v3_accounting_posting_sources')) throw new RuntimeException('Falta ejecutar la migración de gobierno contable V3.');
        if (!$lines) throw new InvalidArgumentException('El asiento debe contener al menos una línea.');
        if (array_key_exists('valor', $header)) $header['valor'] = $this->money($header['valor']);
        foreach ($lines as $index => $line) {
            $lines[$index]['debe'] = $this->money(isset($line['debe']) ? $line['debe'] : 0);
            $lines[$index]['haber'] = $this->money(isset($line['haber']) ? $line['haber'] : 0);
        }
        $legacyIdentityHash = $this->identityHash($source['modulo'], $source['tipo_origen'], $source['id_origen']);
        $source = $this->normalizeSource($source);
        $payload = array('header' => $header, 'lines' => $lines);
        $hash = hash('sha256', json_encode($payload));
        return DB::transaction(function () use ($header, $lines, $source, $hash, $legacyIdentityHash) {
            $this->integrity->assertPeriodOpen($header['id_empresa'], $header['fecha_asiento']);
            $identityHash = $this->identityHash($source['modulo'], $source['tipo_origen'], $source['id_origen']);
            $identityHashes = array_unique(array($identityHash, $legacyIdentityHash));
            $identity = $this->lockedPostingSource($header['id_empresa'], $identityHashes);
            if ($identity) {
                $this->assertSamePayload($identity, $hash);
                if ($identity->id_asiento) return (int) $identity->id_asiento;
                throw new RuntimeException('La contabilización anterior permanece en proceso.');
            }
            try {
                $sourceId = DB::table('v3_accounting_posting_sources')->insertGetId(array(
                    'id_empresa' => $header['id_empresa'], 'modulo' => $source['modulo'], 'tipo_origen' => $source['tipo_origen'],
                    'id_origen' => $source['id_origen'], 'identity_hash' => $identityHash,
                    'payload_hash' => $hash, 'estado' => 'RESERVED',
                    'creado_por' => $this->authenticatedUserId(), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
                ));
            } catch (QueryException $exception) {
                if (!$this->isUniqueConstraintViolation($exception)) throw $exception;
                $identity = $this->lockedPostingSource($header['id_empresa'], $identityHashes);
                if (!$identity) throw $exception;
                $this->assertSamePayload($identity, $hash);
                if ($identity->id_asiento) return (int) $identity->id_asiento;
                throw new RuntimeException('La contabilización anterior permanece en proceso.');
            }
            $userId = $this->authenticatedUserId();
            $now = date('Y-m-d H:i:s');
            $entryId = DB::table('ct_asientos_cabecera')->insertGetId(array_merge($header, array(
                'estado' => isset($header['estado']) ? $header['estado'] : 1,
                'id_usuariocrea' => isset($header['id_usuariocrea']) ? $header['id_usuariocrea'] : $userId,
                'id_usuariomod' => isset($header['id_usuariomod']) ? $header['id_usuariomod'] : $userId,
                'created_at' => $now, 'updated_at' => $now,
            )));
            $detailRows = array();
            foreach ($lines as $line) {
                $detailRows[] = array_merge($line, array(
                    'id_asiento_cabecera' => $entryId, 'estado' => isset($line['estado']) ? $line['estado'] : 1,
                    'id_usuariocrea' => isset($line['id_usuariocrea']) ? $line['id_usuariocrea'] : $userId,
                    'id_usuariomod' => isset($line['id_usuariomod']) ? $line['id_usuariomod'] : $userId,
                    'created_at' => $now, 'updated_at' => $now,
                ));
            }
            $this->insertRowsInChunks('ct_asientos_detalle', $detailRows);
            $this->integrity->assertBalancedEntry($entryId);
            DB::table('v3_accounting_posting_sources')->where('id', $sourceId)->update(array(
                'id_asiento' => $entryId, 'estado' => 'POSTED', 'updated_at' => date('Y-m-d H:i:s')));
            return $entryId;
        }, 3);
    }

    public function legacySourceId(array $header, $request, $explicitId = '')
    {
        if ($explicitId !== '' && !is_null($explicitId)) return 'explicit:'.(string) $explicitId;
        if (isset($header['source_id']) && $header['source_id'] !== '') return 'source:'.(string) $header['source_id'];
        if (!isset($header['fact_numero']) || trim((string) $header['fact_numero']) === '') return null;
        $party = array();
        foreach (array('id_proveedor', 'id_cliente', 'identificacion', 'id_caja_banco') as $key) {
            try { $value = is_object($request) ? $request->input($key) : null; } catch (\Exception $e) { $value = null; }
            if (!is_null($value) && $value !== '') $party[$key] = $value;
        }
        return implode('|', array(isset($header['sucursal']) ? $header['sucursal'] : '',
            isset($header['punto_emision']) ? $header['punto_emision'] : '', $header['fact_numero'], hash('sha256', json_encode($party))));
    }

    public function reserveLegacy($companyId, $module, $type, $sourceId, array $header)
    {
        if (!$sourceId || !Schema::hasTable('v3_accounting_posting_sources')) return array('reservation' => null, 'entry' => null);
        $legacyIdentityHash = $this->identityHash($module, $type, $sourceId);
        $source = $this->normalizeSource(array('modulo' => $module, 'tipo_origen' => $type, 'id_origen' => $sourceId));
        $hash = hash('sha256', json_encode($header));
        $identityHash = $this->identityHash($source['modulo'], $source['tipo_origen'], $source['id_origen']);
        $identityHashes = array_unique(array($identityHash, $legacyIdentityHash));
        $existing = $this->lockedPostingSource($companyId, $identityHashes);
        if ($existing) {
            $this->assertSamePayload($existing, $hash, 'El documento ya posee una contabilización con información diferente.');
            if ($existing->id_asiento) return array('reservation' => $existing->id, 'entry' => (int) $existing->id_asiento);
            throw new RuntimeException('La contabilización del documento ya se encuentra en proceso.');
        }
        try {
            $id = DB::table('v3_accounting_posting_sources')->insertGetId(array('id_empresa' => $companyId,
                'modulo' => $source['modulo'], 'tipo_origen' => $source['tipo_origen'],
                'id_origen' => $source['id_origen'], 'identity_hash' => $identityHash,
                'payload_hash' => $hash, 'estado' => 'RESERVED',
                'creado_por' => $this->authenticatedUserId(), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')));
        } catch (QueryException $exception) {
            if (!$this->isUniqueConstraintViolation($exception)) throw $exception;
            $existing = $this->lockedPostingSource($companyId, $identityHashes);
            if (!$existing) throw $exception;
            $this->assertSamePayload($existing, $hash, 'El documento ya posee una contabilización con información diferente.');
            if ($existing->id_asiento) return array('reservation' => $existing->id, 'entry' => (int) $existing->id_asiento);
            throw new RuntimeException('La contabilización del documento ya se encuentra en proceso.');
        }
        return array('reservation' => $id, 'entry' => null);
    }

    public function completeLegacy($reservationId, $entryId)
    {
        if (!$reservationId) return;
        if (!$entryId) throw new RuntimeException('No se puede completar una reserva contable sin asiento.');
        DB::transaction(function () use ($reservationId, $entryId) {
            $reservation = DB::table('v3_accounting_posting_sources')->where('id', $reservationId)->lockForUpdate()->first();
            if (!$reservation) throw new RuntimeException('No existe la reserva contable que se intenta completar.');
            if ($reservation->id_asiento && (int) $reservation->id_asiento !== (int) $entryId) {
                throw new RuntimeException('La reserva contable ya fue completada con otro asiento.');
            }
            if ($reservation->id_asiento) return;
            DB::table('v3_accounting_posting_sources')->where('id', $reservationId)->whereNull('id_asiento')->update(array(
                'id_asiento' => $entryId, 'estado' => 'POSTED', 'updated_at' => date('Y-m-d H:i:s')));
        }, 3);
    }

    private function normalizeSource(array $source)
    {
        return array(
            'modulo' => substr(trim((string) $source['modulo']), 0, 60),
            'tipo_origen' => substr(trim((string) $source['tipo_origen']), 0, 80),
            'id_origen' => substr(trim((string) $source['id_origen']), 0, 100),
        );
    }

    private function identityHash($module, $type, $sourceId)
    {
        return hash('sha256', $module.'|'.$type.'|'.$sourceId);
    }

    private function lockedPostingSource($companyId, array $identityHashes)
    {
        foreach ($identityHashes as $identityHash) {
            $source = DB::table('v3_accounting_posting_sources')->where('id_empresa', $companyId)
                ->where('identity_hash', $identityHash)->lockForUpdate()->first();
            if ($source) return $source;
        }
        return null;
    }

    private function assertSamePayload($identity, $hash, $message = 'El documento ya fue contabilizado con información diferente.')
    {
        if (!hash_equals((string) $identity->payload_hash, $hash)) throw new RuntimeException($message);
    }

    private function authenticatedUserId()
    {
        return Auth::check() ? Auth::user()->id : null;
    }

    private function money($value)
    {
        return round((float) $value, 2);
    }

    private function isUniqueConstraintViolation(QueryException $exception)
    {
        $errorInfo = isset($exception->errorInfo) ? $exception->errorInfo : array();
        return (isset($errorInfo[0]) && (string) $errorInfo[0] === '23000')
            && (!isset($errorInfo[1]) || in_array((int) $errorInfo[1], array(19, 1062, 1555, 2067), true));
    }

    private function insertRowsInChunks($table, array $rows)
    {
        $groups = array();
        foreach ($rows as $row) {
            ksort($row);
            $signature = implode('|', array_keys($row));
            if (!isset($groups[$signature])) $groups[$signature] = array();
            $groups[$signature][] = $row;
        }
        foreach ($groups as $group) {
            foreach (array_chunk($group, 500) as $chunk) DB::table($table)->insert($chunk);
        }
    }
}
