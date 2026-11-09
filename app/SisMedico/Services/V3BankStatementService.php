<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\contable\CajaBanco;

class V3BankStatementService
{
    private $files;

    public function __construct(V3FileSecurityService $files)
    {
        $this->files = $files;
    }

    public function import(Request $request)
    {
        list($year, $month) = $this->period($request->periodo);
        $company = session('id_empresa');
        $bank = CajaBanco::where('id', $request->id_banco)->where('id_empresa', $company)->first();
        if (!$bank) abort(404);
        $file = $this->files->validateUpload($request, 'extracto', array('csv', 'ofx'), 5242880);
        $path = $file->getRealPath();
        $hash = hash_file('sha256', $path);
        $extension = strtolower($file->getClientOriginalExtension());
        $existing = DB::table('ct_conciliacion_extracto')->where('id_empresa', $company)
            ->where('id_caja_banco', $bank->id)->where('anio', $year)->where('mes', $month)->first();
        if ($existing && hash_equals($existing->archivo_hash, $hash)) return $this->summary($existing->id, true);
        if ($existing) throw new \RuntimeException('Ya existe un extracto diferente para este banco y periodo. Revísele antes de reemplazarlo.');

        $lines = $extension === 'ofx' ? $this->parseOfx($path) : $this->parseCsv($path);
        if (empty($lines)) throw new \InvalidArgumentException('El extracto no contiene movimientos reconocibles.');
        foreach ($lines as $line) {
            if ((int) date('Y', strtotime($line['fecha'])) !== (int) $year || (int) date('m', strtotime($line['fecha'])) !== (int) $month) {
                throw new \InvalidArgumentException('El extracto contiene movimientos fuera del periodo seleccionado.');
            }
        }

        $directory = storage_path('app/v3/reconciliation');
        if (!is_dir($directory) && !mkdir($directory, 0770, true) && !is_dir($directory)) {
            throw new \RuntimeException('No se pudo preparar el almacenamiento seguro del extracto.');
        }
        $storedName = $this->files->randomName($file, 'bank_statement');
        $file->move($directory, $storedName);
        try {
            $batchId = DB::transaction(function () use ($lines, $company, $bank, $year, $month, $extension, $file, $storedName, $hash) {
                $debits = 0; $credits = 0;
                foreach ($lines as $line) { if ($line['monto'] < 0) $debits += abs($line['monto']); else $credits += $line['monto']; }
                $batchId = DB::table('ct_conciliacion_extracto')->insertGetId(array(
                    'id_empresa' => $company, 'id_caja_banco' => $bank->id, 'anio' => $year, 'mes' => $month,
                    'formato' => strtoupper($extension), 'nombre_original' => basename($file->getClientOriginalName()),
                    'nombre_guardado' => $storedName, 'archivo_hash' => $hash, 'total_lineas' => count($lines),
                    'total_debitos' => round($debits, 2), 'total_creditos' => round($credits, 2),
                    'estado' => 'IMPORTED', 'importado_por' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
                ));
                $rows = array();
                foreach ($lines as $index => $line) {
                    $fingerprint = hash('sha256', ($index + 1).'|'.$line['fecha'].'|'.$line['referencia'].'|'.$line['detalle'].'|'.number_format($line['monto'], 2, '.', '').'|'.number_format((float) $line['saldo'], 2, '.', ''));
                    $rows[] = array(
                        'extracto_id' => $batchId, 'numero_linea' => $index + 1, 'fecha' => $line['fecha'],
                        'referencia' => $line['referencia'], 'detalle' => $line['detalle'], 'monto' => $line['monto'],
                        'saldo' => $line['saldo'], 'fingerprint' => $fingerprint, 'estado' => 'UNMATCHED',
                        'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
                    );
                }
                foreach (array_chunk($rows, 500) as $chunk) DB::table('ct_conciliacion_extracto_linea')->insert($chunk);
                return $batchId;
            }, 3);
        } catch (\Throwable $exception) {
            @unlink($directory.DIRECTORY_SEPARATOR.$storedName);
            throw $exception;
        }
        $this->autoMatch($bank->id, sprintf('%04d-%02d', $year, $month));
        return $this->summary($batchId, false);
    }

    public function periodSummary($bankId, $period)
    {
        list($year, $month) = $this->period($period);
        $batch = DB::table('ct_conciliacion_extracto')->where('id_empresa', session('id_empresa'))
            ->where('id_caja_banco', $bankId)->where('anio', $year)->where('mes', $month)
            ->orderBy('id', 'desc')->first();
        return $batch ? $this->summary($batch->id, false) : array('exists' => false, 'lines' => array());
    }

    public function autoMatch($bankId, $period)
    {
        list($year, $month) = $this->period($period);
        $company = session('id_empresa');
        $batchIds = DB::table('ct_conciliacion_extracto')->where('id_empresa', $company)
            ->where('id_caja_banco', $bankId)->where('anio', $year)->where('mes', $month)->pluck('id')->all();
        if (empty($batchIds)) return 0;
        $matched = 0;
        DB::transaction(function () use ($batchIds, $company, $bankId, $year, $month, &$matched) {
            $lines = DB::table('ct_conciliacion_extracto_linea')->whereIn('extracto_id', $batchIds)
                ->where('estado', 'UNMATCHED')->lockForUpdate()->get();
            foreach ($lines as $line) {
                $direction = ((float) $line->monto < 0) ? 'D' : 'C';
                $candidates = DB::table('ct_conciliacion_bancaria')->where('empresa', $company)
                    ->where('id_caja_banco', $bankId)->where('anio', $year)->where('mes', $month)
                    ->where('estado', 1)->where('naturaleza', $direction)
                    ->whereRaw('ABS(valor - ?) < 0.005', array(abs((float) $line->monto)))
                    ->whereNotExists(function ($q) {
                        $q->select(DB::raw(1))->from('ct_conciliacion_extracto_linea as used')
                            ->whereRaw('used.conciliacion_id = ct_conciliacion_bancaria.id');
                    })->get();
                $candidates = $candidates->filter(function ($candidate) use ($line) {
                    if (!$candidate->fecha_movimiento) return true;
                    return abs((strtotime($candidate->fecha_movimiento) - strtotime($line->fecha)) / 86400) <= 3;
                });
                if ($candidates->count() === 1) {
                    $candidate = $candidates->first();
                    DB::table('ct_conciliacion_extracto_linea')->where('id', $line->id)->update(array(
                        'estado' => 'MATCHED', 'conciliacion_id' => $candidate->id, 'confianza' => 100,
                        'emparejado_por' => Auth::user()->id, 'emparejado_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ));
                    $matched++;
                }
            }
        });
        return $matched;
    }

    public function manualMatch($lineId, $reconciliationId)
    {
        return DB::transaction(function () use ($lineId, $reconciliationId) {
            $line = $this->ownedLine($lineId, true);
            $batch = DB::table('ct_conciliacion_extracto')->where('id', $line->extracto_id)->first();
            $movement = DB::table('ct_conciliacion_bancaria')->where('id', $reconciliationId)
                ->where('empresa', $batch->id_empresa)->where('id_caja_banco', $batch->id_caja_banco)
                ->where('anio', $batch->anio)->where('mes', $batch->mes)->where('estado', 1)->lockForUpdate()->first();
            if (!$movement) abort(404);
            $expected = $movement->naturaleza === 'D' ? -abs($movement->valor) : abs($movement->valor);
            if (abs($expected - $line->monto) >= 0.005) throw new \InvalidArgumentException('Los valores del extracto y del movimiento no coinciden.');
            $used = DB::table('ct_conciliacion_extracto_linea')->where('conciliacion_id', $movement->id)->where('id', '!=', $line->id)->exists();
            if ($used) throw new \RuntimeException('El movimiento interno ya esta emparejado con otra linea bancaria.');
            DB::table('ct_conciliacion_extracto_linea')->where('id', $line->id)->update(array(
                'estado' => 'MATCHED', 'conciliacion_id' => $movement->id, 'confianza' => 100,
                'motivo_ignorado' => null, 'emparejado_por' => Auth::user()->id,
                'emparejado_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ));
            return true;
        });
    }

    public function ignore($lineId, $reason)
    {
        if (strlen(trim((string) $reason)) < 10) throw new \InvalidArgumentException('Debe explicar por que la linea no corresponde a un movimiento contable.');
        $line = $this->ownedLine($lineId, true);
        DB::table('ct_conciliacion_extracto_linea')->where('id', $line->id)->update(array(
            'estado' => 'IGNORED', 'conciliacion_id' => null, 'confianza' => null,
            'motivo_ignorado' => trim($reason), 'emparejado_por' => Auth::user()->id,
            'emparejado_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ));
        return true;
    }

    public function unmatch($lineId)
    {
        $line = $this->ownedLine($lineId, true);
        DB::table('ct_conciliacion_extracto_linea')->where('id', $line->id)->update(array(
            'estado' => 'UNMATCHED', 'conciliacion_id' => null, 'confianza' => null,
            'motivo_ignorado' => null, 'emparejado_por' => null, 'emparejado_at' => null,
            'updated_at' => date('Y-m-d H:i:s'),
        ));
        return true;
    }

    private function summary($batchId, $duplicate)
    {
        $batch = DB::table('ct_conciliacion_extracto')->where('id', $batchId)
            ->where('id_empresa', session('id_empresa'))->first();
        if (!$batch) abort(404);
        $lines = DB::table('ct_conciliacion_extracto_linea')->where('extracto_id', $batch->id)
            ->orderBy('numero_linea')->get();
        $candidates = DB::table('ct_conciliacion_bancaria')->where('empresa', $batch->id_empresa)
            ->where('id_caja_banco', $batch->id_caja_banco)->where('anio', $batch->anio)->where('mes', $batch->mes)
            ->where('estado', 1)->orderBy('fecha_movimiento')->get(array('id', 'fecha_movimiento', 'tipo', 'detalle', 'valor', 'naturaleza'));
        return array(
            'exists' => true, 'duplicate' => $duplicate, 'batch' => $batch,
            'matched' => $lines->where('estado', 'MATCHED')->count(),
            'ignored' => $lines->where('estado', 'IGNORED')->count(),
            'unmatched' => $lines->where('estado', 'UNMATCHED')->count(),
            'lines' => $lines, 'candidates' => $candidates,
        );
    }

    private function ownedLine($lineId, $lock)
    {
        $query = DB::table('ct_conciliacion_extracto_linea as line')
            ->join('ct_conciliacion_extracto as batch', 'batch.id', '=', 'line.extracto_id')
            ->where('line.id', $lineId)->where('batch.id_empresa', session('id_empresa'))
            ->select('line.*');
        if ($lock) $query->lockForUpdate();
        $line = $query->first();
        if (!$line) abort(404);
        return $line;
    }

    private function parseCsv($path)
    {
        $handle = fopen($path, 'rb');
        if (!$handle) throw new \RuntimeException('No se pudo leer el archivo CSV.');
        $first = fgets($handle); rewind($handle);
        $delimiter = substr_count($first, ';') >= substr_count($first, ',') ? ';' : ',';
        if (substr_count($first, "\t") > substr_count($first, $delimiter)) $delimiter = "\t";
        $headers = fgetcsv($handle, 0, $delimiter);
        if (!$headers) { fclose($handle); return array(); }
        $map = array();
        foreach ($headers as $index => $header) $map[$this->normalizeHeader($header)] = $index;
        $dateKey = $this->headerIndex($map, array('fecha', 'date', 'fecha movimiento'));
        $amountKey = $this->headerIndex($map, array('monto', 'importe', 'amount', 'valor'));
        $debitKey = $this->headerIndex($map, array('debito', 'debe', 'debit'));
        $creditKey = $this->headerIndex($map, array('credito', 'haber', 'credit'));
        if ($dateKey === null || ($amountKey === null && $debitKey === null && $creditKey === null)) {
            fclose($handle); throw new \InvalidArgumentException('CSV invalido. Use fecha y monto, o fecha/debito/credito.');
        }
        $rows = array();
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count(array_filter($row, function ($value) { return trim((string) $value) !== ''; })) === 0) continue;
            $amount = $amountKey !== null ? $this->money(isset($row[$amountKey]) ? $row[$amountKey] : 0) : 0;
            if ($amountKey === null) $amount = $this->money($creditKey !== null && isset($row[$creditKey]) ? $row[$creditKey] : 0) - $this->money($debitKey !== null && isset($row[$debitKey]) ? $row[$debitKey] : 0);
            $rows[] = array(
                'fecha' => $this->date(isset($row[$dateKey]) ? $row[$dateKey] : ''),
                'referencia' => $this->column($row, $map, array('referencia', 'numero', 'fitid', 'documento')),
                'detalle' => $this->column($row, $map, array('detalle', 'descripcion', 'concepto', 'memo', 'name')),
                'monto' => round($amount, 2),
                'saldo' => $this->nullableMoney($this->column($row, $map, array('saldo', 'balance'))),
            );
        }
        fclose($handle);
        return $rows;
    }

    private function parseOfx($path)
    {
        $content = file_get_contents($path);
        if ($content === false) throw new \RuntimeException('No se pudo leer el archivo OFX.');
        preg_match_all('/<STMTTRN>(.*?)(?:<\/STMTTRN>|(?=<STMTTRN>|<\/BANKTRANLIST>))/is', $content, $matches);
        $rows = array();
        foreach ($matches[1] as $block) {
            $date = substr($this->ofxTag($block, 'DTPOSTED'), 0, 8);
            $rows[] = array(
                'fecha' => $this->date($date), 'referencia' => $this->ofxTag($block, 'FITID'),
                'detalle' => trim($this->ofxTag($block, 'NAME').' '.$this->ofxTag($block, 'MEMO')),
                'monto' => round($this->money($this->ofxTag($block, 'TRNAMT')), 2), 'saldo' => null,
            );
        }
        return $rows;
    }

    private function ofxTag($block, $tag)
    {
        return preg_match('/<'.preg_quote($tag, '/').'>\s*([^<\r\n]+)/i', $block, $match) ? trim($match[1]) : '';
    }

    private function period($period)
    {
        if (!preg_match('/^(20[0-9]{2})-(0[1-9]|1[0-2])$/', (string) $period, $match)) throw new \InvalidArgumentException('Periodo invalido.');
        return array((int) $match[1], (int) $match[2]);
    }

    private function date($value)
    {
        $value = trim((string) $value); $formats = array('Y-m-d', 'd/m/Y', 'd-m-Y', 'Ymd', 'm/d/Y');
        foreach ($formats as $format) { $date = \DateTime::createFromFormat('!'.$format, $value); if ($date && $date->format($format) === $value) return $date->format('Y-m-d'); }
        throw new \InvalidArgumentException('Fecha no reconocida en el extracto: '.$value);
    }

    private function money($value)
    {
        $value = preg_replace('/[^0-9,.-]/', '', (string) $value);
        $comma = strrpos($value, ','); $dot = strrpos($value, '.');
        if ($comma !== false && $dot !== false) { $decimal = $comma > $dot ? ',' : '.'; $value = str_replace($decimal === ',' ? '.' : ',', '', $value); if ($decimal === ',') $value = str_replace(',', '.', $value); }
        elseif ($comma !== false) $value = str_replace(',', '.', $value);
        return (float) $value;
    }

    private function nullableMoney($value) { return trim((string) $value) === '' ? null : $this->money($value); }
    private function normalizeHeader($value) { $value = preg_replace('/^\xEF\xBB\xBF/', '', (string) $value); return trim(strtolower(strtr($value, array('á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n')))); }
    private function headerIndex(array $map, array $names) { foreach ($names as $name) if (array_key_exists($name, $map)) return $map[$name]; return null; }
    private function column(array $row, array $map, array $names) { $index = $this->headerIndex($map, $names); return $index !== null && isset($row[$index]) ? trim((string) $row[$index]) : ''; }
}
