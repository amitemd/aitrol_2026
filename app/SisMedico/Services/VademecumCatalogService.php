<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use RuntimeException;

class VademecumCatalogService
{
    const AUDIT_IP = 'VADEMECUM_INICIAL';

    /**
     * Carga la fuente clinica del vademecum sin depender de usuarios o empresas.
     *
     * @return int
     */
    public function seedCanonicalCatalog()
    {
        if (!Schema::hasTable('vade_producto')
            || !Schema::hasTable('vade_principio')
            || !Schema::hasTable('vade_principio_producto')) {
            return 0;
        }

        $count = 0;
        foreach ($this->catalog() as $item) {
            $principle = DB::table('vade_principio')
                ->whereRaw('UPPER(nombre) = ?', array($this->upper($item['active_ingredient'])))
                ->first();

            if (!$principle) {
                $principleId = DB::table('vade_principio')->insertGetId(array(
                    'nombre' => $this->upper($item['active_ingredient']),
                ));
            } else {
                $principleId = $principle->id;
            }

            $product = DB::table('vade_producto')
                ->where('id_atc', $item['atc'])
                ->whereRaw('UPPER(producto) = ?', array($this->upper($item['name'])))
                ->first();

            $values = array(
                'producto' => $this->upper($item['name']),
                'laboratorio' => $this->upper($item['laboratory']),
                'presentacion' => $this->upper($item['presentation']),
                'precio_unitario' => $item['unit_price'],
                'precio_total' => round($item['unit_price'] * $item['units'], 2),
                'id_atc' => $item['atc'],
                'atc' => $item['atc'],
                'dosificacion' => 'DOSIFICACION SEGUN PRESCRIPCION Y VALORACION CLINICA.',
                'indicaciones' => $item['indications'],
                'contraindicaciones' => $item['contraindications'],
                'dosis_pediatrica' => 'CALCULAR SEGUN PESO, EDAD Y CRITERIO MEDICO; NO AUTOMATIZAR.',
                'consep' => $item['code'],
            );

            if ($product) {
                DB::table('vade_producto')->where('id', $product->id)->update($values);
                $productId = $product->id;
            } else {
                $productId = DB::table('vade_producto')->insertGetId($values);
            }

            $link = DB::table('vade_principio_producto')
                ->where('id_producto', $productId)
                ->where('id_prin_activo', $principleId)
                ->first();

            if (!$link) {
                DB::table('vade_principio_producto')->insert(array(
                    'id_producto' => $productId,
                    'id_prin_activo' => $principleId,
                    'nombre' => $this->upper($item['active_ingredient']),
                ));
            }

            $count++;
        }

        return $count;
    }

    /**
     * Sincroniza el vademecum con prescripcion, farmacia, inventario y venta.
     * La existencia no se inventa: los productos nacen con stock cero hasta
     * que una bodega registre el ingreso correspondiente.
     *
     * @param string|null $userId
     * @param string|null $companyId
     * @return int
     */
    public function syncOperationalCatalogs($userId = null, $companyId = null)
    {
        $this->seedCanonicalCatalog();
        $this->syncLegacyPharmacyCatalog();

        $userId = $this->resolveUserId($userId);
        if (!$userId) {
            return 0;
        }

        $companies = $this->companies($companyId);
        $count = 0;

        foreach ($this->catalog() as $item) {
            $medicineId = $this->syncMedicine($item, $userId);
            $supplyId = $this->syncSupply($item, $userId);
            $this->syncMedicineSupplyLink($medicineId, $supplyId, $userId);

            foreach ($companies as $company) {
                $accountingProductId = $this->syncAccountingProduct($item, $company->id, $userId);
                $this->syncSupplyLink($item, $supplyId, $accountingProductId, $userId);
                $this->syncPrice($item, $userId);
            }

            if ($medicineId && $supplyId) {
                $count++;
            }
        }

        return $count;
    }

    private function syncMedicineSupplyLink($medicineId, $supplyId, $userId)
    {
        if (!$medicineId || !$supplyId || !Schema::hasTable('producto_medicina')) return;

        $now = date('Y-m-d H:i:s');
        DB::table('producto_medicina')->updateOrInsert(
            array('id_medicina' => $medicineId),
            array(
                'id_producto' => $supplyId,
                'id_usuariocrea' => $userId,
                'id_usuariomod' => $userId,
                'ip_creacion' => self::AUDIT_IP,
                'ip_modificacion' => self::AUDIT_IP,
                'created_at' => $now,
                'updated_at' => $now,
            )
        );
    }

    private function syncMedicine(array $item, $userId)
    {
        if (!Schema::hasTable('medicina') || !Schema::hasTable('principio_activo') || !Schema::hasTable('medicina_principio')) {
            return null;
        }

        $now = date('Y-m-d H:i:s');
        $medicine = DB::table('medicina')->where('codigo_atc', $item['atc'])
            ->whereRaw('UPPER(nombre) = ?', array($this->upper($item['name'])))
            ->first();
        $values = array(
            'nombre' => $this->upper($item['name']),
            'codigo_atc' => $item['atc'],
            'concentracion' => $this->upper($item['presentation']),
            'presentacion' => $this->upper($item['presentation']),
            'cantidad' => (string) $item['units'],
            'nombre_generico' => $this->upper($item['active_ingredient']),
            'estado' => 1,
            'laboratorio' => $this->upper($item['laboratory']),
            'precio_unitario' => $item['unit_price'],
            'precio_total' => round($item['unit_price'] * $item['units'], 2),
            'atc' => $item['atc'],
            'indicaciones' => $item['indications'],
            'contraindicaciones' => $item['contraindications'],
            'dosis_pediatrica' => 'CALCULAR SEGUN PESO, EDAD Y CRITERIO MEDICO; NO AUTOMATIZAR.',
            'consep' => $item['code'],
            'id_usuariomod' => $userId,
            'ip_modificacion' => self::AUDIT_IP,
            'updated_at' => $now,
        );

        if ($medicine) {
            DB::table('medicina')->where('id', $medicine->id)->update($values);
            $medicineId = $medicine->id;
        } else {
            $values['id_usuariocrea'] = $userId;
            $values['ip_creacion'] = self::AUDIT_IP;
            $values['created_at'] = $now;
            $medicineId = DB::table('medicina')->insertGetId($values);
        }

        $principle = DB::table('principio_activo')
            ->whereRaw('UPPER(nombre) = ?', array($this->upper($item['active_ingredient'])))
            ->first();
        if (!$principle) {
            $principleId = DB::table('principio_activo')->insertGetId(array(
                'nombre' => $this->upper($item['active_ingredient']),
                'descripcion' => 'PRINCIPIO ACTIVO DEL VADEMECUM INICIAL',
                'estado' => 1,
                'id_usuariocrea' => $userId,
                'id_usuariomod' => $userId,
                'ip_creacion' => self::AUDIT_IP,
                'ip_modificacion' => self::AUDIT_IP,
                'created_at' => $now,
                'updated_at' => $now,
            ));
        } else {
            $principleId = $principle->id;
        }

        $link = DB::table('medicina_principio')
            ->where('id_medicina', $medicineId)
            ->where('id_principio_activo', $principleId)
            ->first();
        if (!$link) {
            DB::table('medicina_principio')->insert(array(
                'id_medicina' => $medicineId,
                'id_principio_activo' => $principleId,
                'id_usuariocrea' => $userId,
                'id_usuariomod' => $userId,
                'ip_creacion' => self::AUDIT_IP,
                'ip_modificacion' => self::AUDIT_IP,
                'created_at' => $now,
                'updated_at' => $now,
            ));
        }

        return $medicineId;
    }

    private function syncSupply(array $item, $userId)
    {
        if (!Schema::hasTable('producto')) {
            return null;
        }

        $now = date('Y-m-d H:i:s');
        $product = DB::table('producto')->where('codigo', $item['code'])->first();
        $values = array(
            'nombre' => $this->upper($item['name']),
            'descripcion' => $this->upper($item['presentation']),
            'estado' => 1,
            'medida' => 'UNID',
            'minimo' => 0,
            'precio_compra' => $item['unit_price'],
            'precio_venta' => $item['unit_price'],
            'codigo' => $item['code'],
            'despacho' => 0,
            'cantidad' => 0,
            'cantidad_unidad' => $item['units'],
            'registro_sanitario' => 'POR REGISTRAR',
            // Los medicamentos se consumen por unidad; no son equipos reutilizables.
            'usos' => 0,
            'iva' => 0,
            'farmacia' => 1,
            'ficha_tecnica' => $item['indications'] . ' Contraindicaciones: ' . $item['contraindications'],
            'id_usuariomod' => $userId,
            'ip_modificacion' => self::AUDIT_IP,
            'updated_at' => $now,
        );

        if ($product) {
            DB::table('producto')->where('id', $product->id)->update($values);
            return $product->id;
        }

        $values['id_usuariocrea'] = $userId;
        $values['ip_creacion'] = self::AUDIT_IP;
        $values['created_at'] = $now;
        return DB::table('producto')->insertGetId($values);
    }

    private function syncAccountingProduct(array $item, $companyId, $userId)
    {
        if (!Schema::hasTable('ct_productos')) {
            return null;
        }

        $now = date('Y-m-d H:i:s');
        $product = DB::table('ct_productos')
            ->where('codigo', $item['code'])
            ->where('id_empresa', $companyId)
            ->first();
        $values = array(
            'codigo' => $item['code'],
            'nombre' => $this->upper($item['name']),
            'descripcion' => $this->upper($item['presentation']),
            'grupo' => '2',
            'stock_minimo' => 0,
            'precio1' => $item['unit_price'],
            'estado_tabla' => 1,
            'iva' => 0,
            'unidades' => $item['units'],
            'tipo_objeto' => 'P',
            // En V3: 2 = producto fisico inventariable, 1 = servicio.
            'tipo_producto' => 2,
            'id_empresa' => $companyId,
            'id_usuariomod' => $userId,
            'ip_modificacion' => self::AUDIT_IP,
            'updated_at' => $now,
        );

        if ($product) {
            DB::table('ct_productos')->where('id', $product->id)->update($values);
            return $product->id;
        }

        $values['id_usuariocrea'] = $userId;
        $values['ip_creacion'] = self::AUDIT_IP;
        $values['created_at'] = $now;
        return DB::table('ct_productos')->insertGetId($values);
    }

    private function syncSupplyLink(array $item, $supplyId, $accountingProductId, $userId)
    {
        if (!$supplyId || !$accountingProductId || !Schema::hasTable('ct_productos_insumos')) {
            return;
        }

        $link = DB::table('ct_productos_insumos')
            ->where('id_insumo', $supplyId)
            ->where('id_producto', $accountingProductId)
            ->first();
        if (!$link) {
            DB::table('ct_productos_insumos')->insert(array(
                'codigo_producto' => $item['code'],
                'id_insumo' => $supplyId,
                'id_producto' => $accountingProductId,
                'id_usuariocrea' => $userId,
                'id_usuariomod' => $userId,
                'ip_creacion' => self::AUDIT_IP,
                'ip_modificacion' => self::AUDIT_IP,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ));
        }
    }

    private function syncPrice(array $item, $userId)
    {
        if (!Schema::hasTable('precio_producto')) {
            return;
        }

        $price = DB::table('precio_producto')
            ->where('codigo_producto', $item['code'])
            ->where('nivel', '1')
            ->first();
        $values = array(
            'precio' => $item['unit_price'],
            'estado' => 1,
            'id_usuariomod' => $userId,
            'ip_modificacion' => self::AUDIT_IP,
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if ($price) {
            DB::table('precio_producto')->where('id', $price->id)->update($values);
            return;
        }

        $values['codigo_producto'] = $item['code'];
        $values['nivel'] = '1';
        $values['id_usuariocrea'] = $userId;
        $values['ip_creacion'] = self::AUDIT_IP;
        $values['created_at'] = date('Y-m-d H:i:s');
        DB::table('precio_producto')->insert($values);
    }

    private function syncLegacyPharmacyCatalog()
    {
        if (!Schema::hasTable('hospital_producto')) {
            return;
        }

        foreach ($this->catalog() as $item) {
            $product = DB::table('hospital_producto')->where('codigo', $item['code'])->first();
            $values = array(
                'nombre' => $this->upper($item['name']),
                'descripcion' => $this->upper($item['presentation']),
                'estado' => 1,
                'medida' => 'UNIDAD',
                'minimo' => 0,
                'precio_compra' => $item['unit_price'],
                'precio_venta' => $item['unit_price'],
                'codigo' => $item['code'],
                'despacho' => 0,
                'cantidad' => 0,
                'cantidad_unidad' => $item['units'],
                'registro_sanitario' => 'POR REGISTRAR',
                'usos' => 1,
                'iva' => 0,
                'indicaciones_medicina' => substr($item['indications'], 0, 100),
                'ip_modificacion' => self::AUDIT_IP,
                'updated_at' => date('Y-m-d H:i:s'),
            );

            if ($product) {
                DB::table('hospital_producto')->where('id', $product->id)->update($values);
            } else {
                $values['ip_creacion'] = self::AUDIT_IP;
                $values['created_at'] = date('Y-m-d H:i:s');
                DB::table('hospital_producto')->insert($values);
            }
        }
    }

    private function resolveUserId($userId)
    {
        if (!Schema::hasTable('users')) {
            return null;
        }

        if ($userId && DB::table('users')->where('id', $userId)->first()) {
            return $userId;
        }

        $user = DB::table('users')->where('estado', 1)->orderBy('id')->first();
        return $user ? $user->id : null;
    }

    private function companies($companyId)
    {
        if (!Schema::hasTable('empresa')) {
            return array();
        }

        $query = DB::table('empresa')->where('estado', 1);
        if ($companyId) {
            $query->where('id', $companyId);
        }

        return $query->get(array('id'));
    }

    private function catalog()
    {
        $path = database_path('data/initial_vademecum.json');
        if (!is_file($path)) {
            throw new RuntimeException('No se encontro el catalogo inicial del vademecum: ' . $path);
        }

        $catalog = json_decode(file_get_contents($path), true);
        if (!is_array($catalog) || count($catalog) < 20) {
            throw new RuntimeException('El catalogo inicial del vademecum esta vacio o incompleto.');
        }

        return $catalog;
    }

    private function upper($value)
    {
        return function_exists('mb_strtoupper') ? mb_strtoupper($value, 'UTF-8') : strtoupper($value);
    }
}
