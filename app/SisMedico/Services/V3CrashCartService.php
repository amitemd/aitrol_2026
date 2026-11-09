<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class V3CrashCartService
{
    const CONTROLS = 'v3_crash_cart_controls';
    const DETAILS = 'v3_crash_cart_details';
    const EVENTS = 'v3_crash_cart_events';

    public function warehouses($company)
    {
        $this->requireCompany($company);
        return DB::table('ct_bodegas')->where('id_empresa', $company)->where('estado', 1)
            ->orderBy('nombre')->get(array('id', 'nombre'));
    }

    public function responsibleUsers($company)
    {
        $this->requireCompany($company);
        return DB::table('users as u')->where('u.estado', 1)->whereExists(function ($query) use ($company) {
            $query->select(DB::raw(1))->from('usuario_empresa as ue')->whereColumn('ue.id_usuario', 'u.id')->where('ue.id_empresa', $company);
        })->orderBy('u.apellido1')->get(array('u.id', 'u.nombre1', 'u.apellido1', 'u.apellido2'));
    }

    public function filters(array $input)
    {
        $data = array_merge(array('mes' => date('Y-m'), 'bodega' => null, 'tipo' => null, 'servicio' => null, 'responsable' => null), $input);
        if (isset($data['servicio']) && is_string($data['servicio'])) $data['servicio'] = $this->serviceName($data['servicio']);
        Validator::make($data, array(
            'mes' => 'required|date_format:Y-m', 'bodega' => 'nullable|integer|min:1',
            'tipo' => 'nullable|in:1,2', 'servicio' => 'nullable|string|max:100', 'responsable' => 'nullable|string|max:13',
        ))->validate();
        return array_intersect_key($data, array_flip(array('mes', 'bodega', 'tipo', 'servicio', 'responsable')));
    }

    public function records($company, array $filters)
    {
        return $this->filtered($company, $filters)->orderBy('fecha', 'desc')->orderBy('id', 'desc')->paginate(20)->appends($filters);
    }

    public function inventory($company, $warehouse, $type, $historical = false)
    {
        $warehouse = $this->warehouse($company, $warehouse);
        return $this->inventoryQuery($company, $warehouse->inventory_warehouse_id, $type)
            ->where('i.existencia', $historical ? '>=' : '>', 0)->orderBy('p.nombre')->orderBy('i.id')->get();
    }

    private function inventoryQuery($company, $warehouse, $type)
    {
        return DB::table('inv_inventario_serie as i')->join('producto as p', 'p.id', '=', 'i.id_producto')
            ->where('i.id_empresa', $company)->where('i.id_bodega', $warehouse)
            ->where('i.estado', 1)->whereNull('i.deleted_at')->where('p.estado', 1)
            ->whereIn('p.tipo', (int) $type === 1 ? array(2) : array(3, 8, 0))
            ->select(array('i.id as inventory_id', 'p.id as producto_id', 'p.nombre as producto_nombre',
                'p.codigo as producto_codigo', 'i.lote', 'i.serie', 'i.fecha_vence as fecha_caducidad', 'i.existencia as referencia_cantidad'));
    }

    public function detail($company, $id, $lock = false)
    {
        $query = $this->companyQuery($company)->where('id', $id);
        if ($lock) $query->lockForUpdate();
        $record = $query->first();
        if (!$record) abort(404, 'Control no encontrado en la empresa activa.');
        return array('record' => $record, 'details' => DB::table(self::DETAILS)->where('control_id', $id)->orderBy('id')->get());
    }

    public function events($company, $id)
    {
        $this->detail($company, $id);
        return DB::table(self::EVENTS)->where('id_empresa', $company)->where('control_id', $id)->orderBy('id', 'desc')->get();
    }

    public function existing($company, array $data)
    {
        return $this->companyQuery($company)->where('id_bodega', $data['id_bodega'])->where('fecha', $data['fecha'])
            ->where('tipo', $data['tipo'])->where('servicio', $data['servicio'])->where('active_slot', 1)->value('id');
    }

    public function save($company, $actor, $ip, array $data, $id = null)
    {
        $this->requireCompany($company);
        $data = $this->validate($data, $id !== null);
        return DB::transaction(function () use ($company, $actor, $ip, $data, $id) {
            $old = $id === null ? null : $this->detail($company, $id, true);
            if ($old) {
                $this->assertEditable($old['record'], $data['version']);
                $record = $old['record'];
                // Identity and original reference are immutable when correcting a control.
                foreach (array('id_bodega', 'fecha', 'tipo', 'servicio') as $field) {
                    if ((string) $record->$field !== (string) $data[$field]) $this->invalid($field, 'No se puede cambiar la fecha, bodega, servicio o tipo de un control existente.');
                }
            }
            $warehouse = $this->warehouse($company, $data['id_bodega'], true);
            $responsible = $this->responsibleUsers($company)->firstWhere('id', $data['responsable_id']);
            if (!$responsible) $this->invalid('responsable_id', 'El responsable debe estar activo y vinculado a esta empresa.');
            if (!$old && $this->existing($company, $data)) $this->invalid('fecha', 'Ya existe un control activo para esa fecha, bodega, servicio y tipo. Ábralo desde el listado.');
            $historical = $old ? $old['record']->reference_basis === 'declared' : $data['fecha'] < date('Y-m-d');
            if ($historical && empty($data['observacion'])) $this->invalid('observacion', 'Explique el origen de las cantidades de referencia del registro anterior.');

            $lines = $old ? $this->updatedLines($old['details'], $data['items'], $data['tipo']) : $this->newLines($company, $data, $historical, $warehouse->inventory_warehouse_id);
            $now = date('Y-m-d H:i:s');
            $header = array(
                'responsable_id' => $responsible->id,
                'responsable_nombre' => trim($responsible->nombre1.' '.$responsible->apellido1.' '.$responsible->apellido2),
                'observacion' => $data['observacion'] ?? null, 'id_usuariomod' => $actor, 'ip_modificacion' => $ip,
                'version' => $old ? $old['record']->version + 1 : 1, 'updated_at' => $now,
            );
            if ($old) {
                DB::table(self::CONTROLS)->where('id', $id)->where('id_empresa', $company)->update($header);
                foreach ($lines as $line) {
                    $line['updated_at'] = $now;
                    DB::table(self::DETAILS)->where('control_id', $id)->where('inventory_id', $line['inventory_id'])->update($line);
                }
            } else {
                $id = DB::table(self::CONTROLS)->insertGetId(array_merge($header, array(
                    'id_empresa' => $company, 'id_bodega' => $warehouse->id, 'bodega_nombre' => $warehouse->nombre,
                    'fecha' => $data['fecha'], 'tipo' => $data['tipo'], 'servicio' => $data['servicio'],
                    'reference_basis' => $historical ? 'declared' : 'inventory', 'estado' => 1, 'active_slot' => 1,
                    'id_usuariocrea' => $actor, 'ip_creacion' => $ip, 'created_at' => $now,
                )));
                foreach ($lines as $line) {
                    DB::table(self::DETAILS)->insert(array_merge($line, array('control_id' => $id, 'created_at' => $now, 'updated_at' => $now)));
                }
            }
            $this->event($company, $id, $old ? 'update' : 'create', $actor, $ip);
            return $id;
        }, 3);
    }

    public function cancel($company, $id, $actor, $ip, array $data)
    {
        Validator::make($data, array('version' => 'required|integer|min:1', 'motivo' => 'required|string|min:5|max:1000'))->validate();
        return DB::transaction(function () use ($company, $id, $actor, $ip, $data) {
            $record = $this->detail($company, $id, true)['record'];
            $this->assertEditable($record, $data['version']);
            DB::table(self::CONTROLS)->where('id', $id)->where('id_empresa', $company)->update(array(
                'estado' => 0, 'active_slot' => null, 'motivo_anulacion' => trim($data['motivo']),
                'version' => $record->version + 1, 'id_usuariomod' => $actor, 'ip_modificacion' => $ip, 'updated_at' => date('Y-m-d H:i:s'),
            ));
            $this->event($company, $id, 'cancel', $actor, $ip);
        }, 3);
    }

    private function validate(array $data, $editing)
    {
        if (isset($data['servicio']) && is_string($data['servicio'])) $data['servicio'] = $this->serviceName($data['servicio']);
        if (isset($data['observacion']) && is_string($data['observacion'])) $data['observacion'] = trim($data['observacion']);
        $rules = array(
            'id_bodega' => 'required|integer|min:1', 'fecha' => 'required|date_format:Y-m-d|before_or_equal:today',
            'tipo' => 'required|in:1,2', 'servicio' => 'required|string|max:100', 'responsable_id' => 'required|string|max:13',
            'observacion' => 'nullable|string|max:2000', 'items' => 'required|array|min:1|max:300',
            'items.*.inventory_id' => 'required|integer|min:1|distinct',
            'items.*.stock' => 'required|integer|min:0|max:2147483647',
            'items.*.referencia_cantidad' => 'required|integer|min:0|max:2147483647',
            'items.*.fecha_caducidad' => 'nullable|date_format:Y-m-d',
            'items.*.presentacion' => 'nullable|string|max:100', 'items.*.concentracion' => 'nullable|string|max:100',
            'items.*.observacion' => 'nullable|string|max:500',
        );
        if ($editing) $rules['version'] = 'required|integer|min:1';
        Validator::make($data, $rules)->validate();
        foreach ($data['items'] as &$item) {
            foreach (array('presentacion', 'concentracion', 'observacion') as $field) {
                if (isset($item[$field])) $item[$field] = trim($item[$field]);
            }
        }
        unset($item);
        return $data;
    }

    private function newLines($company, array $data, $historical, $inventoryWarehouse)
    {
        $items = collect($data['items'])->keyBy('inventory_id');
        $stock = $this->inventoryQuery($company, $inventoryWarehouse, $data['tipo'])->where('i.existencia', $historical ? '>=' : '>', 0)
            ->orderBy('i.id')->lockForUpdate()->get();
        if ($stock->count() !== $items->count() || array_diff($stock->pluck('inventory_id')->all(), $items->keys()->all())) {
            $this->invalid('items', 'El control debe incluir todos los productos de la bodega y tipo seleccionados. Recargue si el inventario cambió.');
        }
        $lines = array();
        foreach ($stock as $row) {
            $item = $items[$row->inventory_id];
            if (!$historical && ((int) $row->referencia_cantidad !== (int) $item['referencia_cantidad'] || $row->referencia_cantidad <= 0)) {
                $this->invalid('items', 'El inventario cambió mientras registraba el control. Recargue los productos y verifique las cantidades.');
            }
            $reference = $historical ? (int) $item['referencia_cantidad'] : (int) $row->referencia_cantidad;
            $lines[] = array_merge(array(
                'inventory_id' => $row->inventory_id, 'producto_id' => $row->producto_id, 'producto_nombre' => $row->producto_nombre,
                'producto_codigo' => $row->producto_codigo, 'lote' => $row->lote, 'serie' => $row->serie, 'referencia_cantidad' => $reference,
            ), $this->countFields($item, $reference, $data['tipo']));
        }
        return $lines;
    }

    private function updatedLines($oldLines, array $items, $type)
    {
        $input = collect($items)->keyBy('inventory_id');
        if ($input->count() !== $oldLines->count()) $this->invalid('items', 'La corrección debe conservar todos los lotes del control original.');
        $lines = array();
        foreach ($oldLines as $row) {
            if (!$input->has($row->inventory_id)) $this->invalid('items', 'No se puede sustituir un lote de un control existente.');
            $item = $input[$row->inventory_id];
            if ((int) $item['referencia_cantidad'] !== (int) $row->referencia_cantidad) $this->invalid('items', 'La cantidad de referencia del control original no se puede modificar.');
            $lines[] = array_merge(array('inventory_id' => $row->inventory_id), $this->countFields($item, $row->referencia_cantidad, $type));
        }
        return $lines;
    }

    private function countFields(array $item, $reference, $type = null)
    {
        if ((int) $item['stock'] > $reference) $this->invalid('items', 'El conteo no puede superar la cantidad de referencia.');
        if ((int) $type === 1 && empty($item['presentacion'])) $this->invalid('items', 'Indique la presentación de cada medicamento.');
        return array('stock' => (int) $item['stock'], 'fecha_caducidad' => $item['fecha_caducidad'] ?? null,
            'presentacion' => $item['presentacion'] ?? null, 'concentracion' => $item['concentracion'] ?? null, 'observacion' => $item['observacion'] ?? null);
    }

    private function assertEditable($record, $version)
    {
        if ((int) $record->estado !== 1) $this->invalid('control', 'El control está anulado y conserva su historial de solo lectura.');
        if ((int) $record->version !== (int) $version) abort(409, 'Otro usuario modificó este control. Recargue antes de guardar.');
    }

    private function event($company, $id, $action, $actor, $ip)
    {
        $snapshot = $this->detail($company, $id);
        DB::table(self::EVENTS)->insert(array('control_id' => $id, 'id_empresa' => $company, 'accion' => $action,
            'version' => $snapshot['record']->version, 'actor_id' => $actor, 'ip' => $ip,
            'snapshot' => json_encode($snapshot, JSON_UNESCAPED_UNICODE), 'created_at' => date('Y-m-d H:i:s')));
    }

    public function monthlyReport($company, array $filters)
    {
        $records = $this->filtered($company, $filters)->where('estado', 1)->orderBy('fecha')->orderBy('id')->get()->keyBy('id');
        $details = $records->isEmpty() ? collect() : DB::table(self::DETAILS)->whereIn('control_id', $records->keys()->all())->orderBy('producto_nombre')->orderBy('id')->get();
        $matrix = array(); $usage = array();
        foreach ($details as $line) {
            $record = $records[$line->control_id];
            // Group by the identity saved in each control, including its expiry.
            // Current inventory metadata must never relabel an earlier count.
            $key = json_encode(array((string) $record->id_bodega, $record->servicio, (string) $record->tipo,
                (string) $line->inventory_id, (string) $line->producto_id, $line->lote, $line->serie, $line->fecha_caducidad));
            if (!isset($matrix[$key])) $matrix[$key] = array('record' => $record, 'line' => $line, 'days' => array());
            $day = (int) substr($record->fecha, 8, 2);
            $matrix[$key]['days'][$day] = array('count' => $line->stock, 'reference' => $line->referencia_cantidad, 'responsible' => $record->responsable_nombre, 'expiry' => $line->fecha_caducidad);
            $difference = $line->referencia_cantidad - $line->stock;
            if ($difference > 0) $usage[] = array('record' => $record, 'line' => $line, 'difference' => $difference);
        }
        return array('filters' => $filters, 'records' => $records, 'matrix' => array_values($matrix), 'usage' => $usage,
            'daysInMonth' => (int) date('t', strtotime($filters['mes'].'-01')));
    }

    public function expiryStatus($date)
    {
        if (!$date || $date === '0000-00-00') return 'Sin fecha';
        $today = new \DateTimeImmutable('today');
        $expiry = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if (!$expiry || $expiry->format('Y-m-d') !== $date) return 'Sin fecha';
        if ($expiry < $today) return 'Vencido';
        if ($expiry <= $today->modify('+7 days')) return 'Vence en 7 días';
        if ($expiry <= $today->modify('+21 days')) return 'Vence en 21 días';
        return 'Vigente';
    }

    private function filtered($company, array $filters)
    {
        $start = $filters['mes'].'-01'; $end = date('Y-m-d', strtotime($start.' +1 month'));
        $query = $this->companyQuery($company)->where('fecha', '>=', $start)->where('fecha', '<', $end);
        foreach (array('bodega' => 'id_bodega', 'tipo' => 'tipo', 'servicio' => 'servicio', 'responsable' => 'responsable_id') as $key => $column) {
            if (!empty($filters[$key])) $query->where($column, $filters[$key]);
        }
        return $query;
    }

    private function companyQuery($company)
    {
        $this->requireCompany($company);
        return DB::table(self::CONTROLS)->where('id_empresa', $company);
    }

    private function warehouse($company, $id, $lock = false)
    {
        $this->requireCompany($company);
        $query = DB::table('ct_bodegas')->where('id', $id)->where('id_empresa', $company)->where('estado', 1);
        if ($lock) $query->lockForUpdate();
        $warehouse = $query->first(array('id', 'nombre'));
        if (!$warehouse) $this->invalid('id_bodega', 'Seleccione una bodega contable activa de esta empresa.');

        $mappingQuery = DB::table('union_bodega')->where('id_bodega_cont', $warehouse->id)
            ->where('id_empresa', $company)->whereNotNull('id_bodega_compra');
        if ($lock) $mappingQuery->lockForUpdate();
        $inventoryWarehouses = $mappingQuery->distinct()->pluck('id_bodega_compra');
        if ($inventoryWarehouses->count() !== 1) {
            $this->invalid('id_bodega', 'La bodega contable debe tener una sola correspondencia con la bodega de inventario.');
        }
        $warehouse->inventory_warehouse_id = (int) $inventoryWarehouses->first();
        return $warehouse;
    }

    private function requireCompany($company) { if (trim((string) $company) === '') abort(403, 'Seleccione una empresa activa.'); }
    private function serviceName($value) { return mb_strtoupper(preg_replace('/\s+/u', ' ', trim($value)), 'UTF-8'); }
    private function invalid($key, $message) { throw ValidationException::withMessages(array($key => $message)); }
}
