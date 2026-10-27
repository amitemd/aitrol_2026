<?php

namespace Sis_medico;

use Excel;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ct_ventas extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_ventas';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function getVentasExamenes($anio = '')
    {
        return Ct_ventas::join('seguros as s', 'ct_ventas.seguro_paciente', 's.id')
            ->join('ct_detalle_venta as dv', 'ct_ventas.id', 'dv.id_ct_ventas')
            ->whereYear('ct_ventas.fecha', $anio)
            ->orderBy('ct_ventas.fecha')
            ->orderBy('ct_ventas.id_cliente')
            ->get([
                'ct_ventas.fecha',
                'ct_ventas.id_cliente',
                'ct_ventas.nombres_paciente',
                'ct_ventas.nombre_cliente',
                'ct_ventas.sucursal',
                'dv.id_ct_productos',
                'ct_ventas.nro_comprobante',
                's.nombre as seguro',
                'dv.precio',
                'dv.descuento',
                'dv.nombre as producto',
                'ct_ventas.nota_array'
            ]);
    }
    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }

    public function cliente()
    {
        return $this->belongsTo('Sis_medico\Ct_Clientes', 'id_cliente', 'identificacion');
    }
    public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente', 'id_paciente');
    }

    public function retenciones()
    {
        return $this->belongsTo('Sis_medico\Ct_Cliente_Retencion', 'id', 'id_factura');
    }

    public function retenciones_2()
    {
        return $this->hasMany('Sis_medico\Ct_Cliente_Retencion', 'id_factura');
    }
    public function comp_ingreso()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Comprobante_Ingreso', 'id_factura');
    }
    public function cruce()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Cruce_Clientes', 'id_factura');
    }
    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_detalle_venta', 'id_ct_ventas');
    }
    public function chequepost()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Cheque_Post', 'id_factura');
    }
    public function cab_retenciones()
    {
        return $this->hasOne(Ct_Cliente_Retencion::class, 'id_factura');
    }
    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function usuarios_()
    {
        return $this->hasMany('Sis_medico\User', 'id_usuariocrea');
    }
    public function usuariomod()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod');
    }
    public function cruce_cuentas()
    {
        return $this->hasMany('Sis_medico\Ct_Cruce_Cuentas_Clientes', 'id_factura');
    }
    public function credito()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Credito_Clientes', 'id_factura');
    }
    public function ct_orden_venta()
    {
        return $this->belongsTo('Sis_medico\Ct_Orden_Venta', 'orden_venta', 'id');
    }
    public function detalle_nota_credito_parcial()
    {
        return $this->hasMany('Sis_medico\Ct_Devolucion_Productos', 'id_factura');
    }
    public function pdetalle_nota_credito_parcial()
    {
        return $this->belongsTo('Sis_medico\Ct_Devolucion_Productos', 'id_factura', 'id');
    }
    public function rnota_credito()
    { //notas credito
        return $this->belongsTo('Sis_medico\Ct_Nota_Credito_Clientes', 'id', 'id_factura');
    }

    public function seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro', 'seguro_paciente');
    }
    public static function setLogDocElectronico($dato)
    {
        try {
            DB::table('de_log_facturacion')->insert($dato);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
    public static function getVentasCabecera($id = '')
    {
        try {
            if ($id == '') {

                return Ct_ventas::join('de_empresa as em', 'ct_ventas.id_empresa', '=', 'em.id_empresa')
                    ->where('ct_ventas.electronica', 1)
                    ->where('ct_ventas.doc_electronico', 0)
                    ->where('ct_ventas.estado', '<>', 0)
                    //->where('ct_ventas.punto_emision', '<>', 002)
                    ->where('ct_ventas.emisor', 'MD')
                    //->where('estado_electronica', 0)
                    ->limit(1)
                    ->get([
                        'ct_ventas.id',
                        'ct_ventas.id_empresa',
                        'ct_ventas.created_at',
                        'sucursal',
                        'punto_emision',
                        'ct_ventas.numero',
                        'fecha_envio',
                        'ct_ventas.fecha',
                        'nro_comprobante',
                        'nro_autorizacion',
                        'direccion_cliente',
                        'ruc_id_cliente',
                        'telefono_cliente',
                        'email_cliente',
                        'nombres_paciente',
                        'subtotal_0',
                        'subtotal_12',
                        'subtotal_total',
                        'descuento',
                        'base_imponible',
                        'impuesto',
                        'iva_total',
                        'total_final',
                        'ventas_netas',
                        'dias_plazo',
                        'nombre_cliente',
                        'nota_electronica',
                        'nota_array'
                    ]);
            } else {
                return Ct_ventas::where('ct_ventas.id', $id)
                    ->get([
                        'ct_ventas.id',
                        'ct_ventas.id_empresa',
                        'ct_ventas.created_at',
                        'sucursal',
                        'punto_emision',
                        'ct_ventas.numero',
                        'fecha_envio',
                        'ct_ventas.fecha',
                        'nro_comprobante',
                        'nro_autorizacion',
                        'direccion_cliente',
                        'ruc_id_cliente',
                        'telefono_cliente',
                        'email_cliente',
                        'nombres_paciente',
                        'subtotal_0',
                        'subtotal_12',
                        'subtotal_total',
                        'descuento',
                        'base_imponible',
                        'impuesto',
                        'iva_total',
                        'total_final',
                        'ventas_netas',
                        'dias_plazo',
                        'nombre_cliente',
                        'nota_electronica',
                        'nota_array'
                    ]);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
    public static function updateSinGenerarXML($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 7,
        ];
        Ct_ventas::where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateGenerarXML($idG, $clave)
    {
        $esta = substr($clave, 24, 3);
        $emis = substr($clave, 27, 3);
        $num = substr($clave, 30, 9);
        $arrayDoc = [
            //'numero' => $num,
            //'nro_comprobante' => $esta . '-' . $emis . '-' . $num,
            'doc_electronico' => 1,
            'nro_autorizacion' => $clave,
            //'clave_acceso' => $clave
        ];
        Ct_ventas::where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateValidacionXSD($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 2,
        ];
        Ct_ventas::where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateXmlFirmado($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 3,
        ];
        Ct_ventas::where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateRecibidoSri($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 4,
        ];
        Ct_ventas::where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateXmlAutorizacion($idG, $est, $emi, $nro, $clave)
    {
        $arrayDoc = [
            'estado_electronica' => 3,
            'doc_electronico' => 5,
            'numero' => $nro,
            'nro_comprobante' => $est . '-' . $emi . '-' . $nro,
            'nro_autorizacion' => $clave,
            'fecha_envio' => date('Y-m-d H:i:s')
        ];
        Ct_ventas::where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateXmlNoRecepcion($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 9,
        ];
        Ct_ventas::where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateXmlNoAutorizacion($id)
    {
        $arrayDoc = [
            'doc_electronico' => 10,
        ];
        Ct_ventas::where('id', $id)
            ->update($arrayDoc);
    }
    public function nota_credito()
    {
        return $this->hasMany('Sis_medico\Ct_Nota_Credito_Clientes', 'id_factura');
    }
    public function nota_debito()
    {
        return $this->hasMany('Sis_medico\Ct_Nota_Debito_Cliente', 'id_factura');
    }
    public static function getBusqueda($req)
    {

        $id_empresa  = $req->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $constraints = [
            'ct_ventas.id'               => $req['id'],
            'nro_comprobante'  => $req['numero'],
            'id_asiento'       => $req['id_asiento'],
            'fecha'            => $req['fecha'],
            'nombre_cliente'   => $req['nombre_cliente'],
            'nombres_paciente' => $req['nombres_paciente'],
            'omni'             => $req['omni'],
        ];


        $query = Ct_ventas::query();

        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {

                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                if ($fields[$index] == "ct_ventas.id" || $fields[$index] == "id_asiento") {
                    $query = $query->where($fields[$index], $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
            }
            $index++;
        }
        return $query->leftjoin('seguros as se', 'ct_ventas.seguro_paciente', '=', 'se.id')
            ->join('users as uc', 'ct_ventas.id_usuariocrea', '=', 'uc.id')
            ->join('users as um', 'ct_ventas.id_usuariomod', '=', 'um.id')
            ->where('ct_ventas.estado', '<', '2')
            ->where('ct_ventas.tipo', "VEN-FA")
            ->where('id_empresa', $id_empresa)
            ->orderby('ct_ventas.id', 'desc')
            ->select(
                'ct_ventas.rutapdf',
                'ct_ventas.electronica',
                'ct_ventas.id',
                'ct_ventas.orden_venta',
                'ct_ventas.ip_creacion',
                'ct_ventas.nro_comprobante',
                'ct_ventas.id_asiento',
                'ct_ventas.created_at',
                'ct_ventas.fecha',
                'ct_ventas.estado',
                'ct_ventas.nombre_cliente',
                'ct_ventas.nombres_paciente',
                'ct_ventas.seguro_paciente',
                'ct_ventas.procedimientos',
                'ct_ventas.fecha_procedimiento',
                'ct_ventas.estado_electronica',
                'ct_ventas.doc_electronico',
                'ct_ventas.nro_autorizacion',
                'ct_ventas.id_empresa',
                'uc.nombre1',
                'uc.nombre2',
                'uc.apellido1',
                'uc.apellido2',
                'um.nombre1 as nombremod1',
                'um.nombre2 as nombremod2',
                'um.apellido1 as apellidomod1',
                'um.apellido2 as apellidomod2',
                'se.nombre',
                'ct_ventas.emisor',
                DB::raw('ROUND((COALESCE(ct_ventas.subtotal_0, 0) + COALESCE(ct_ventas.subtotal_12, 0)) - COALESCE(ct_ventas.descuento, 0), 2) AS valor_sin_iva')
            )->paginate(10);
    }
    public static function getCorteaFecha($r)
    {
        $idEmpresa = session('id_empresa');
        $sql = "select * from ((select v.fecha,c.identificacion,c.nombre,v.nro_comprobante,0 as debe,IFNULL(v.total_final,0) as haber,'VENTA' as tipo 
        from ct_ventas as v 
        join ct_clientes c on v.id_cliente=c.identificacion
        where id_empresa=$idEmpresa
        and v.estado!=0
        and v.tipo!='N-D'
        order by c.identificacion) 
        UNION ALL
        (select dci.fecha,c.identificacion,c.nombre,cci.secuencia,dci.total as debe, 0 as haber,'COMP INGRESO' as tipo 
        from ct_detalle_comprobante_ingreso as dci
        join ct_comprobante_ingreso as cci on dci.id_comprobante=cci.id
        join ct_clientes c on cci.id_cliente=c.identificacion
        where cci.id_empresa=$idEmpresa
        and cci.estado!=0
        ORDER by c.identificacion
         )
         UNION ALL
         (select nc.fecha,c.identificacion,c.nombre,nc.secuencia,nc.total_credito as debe,0 as haber,'NOTA CREDITO' as tipo
         from ct_nota_credito_clientes as nc
         join ct_clientes c on nc.id_cliente=c.identificacion
         where nc.id_empresa=$idEmpresa
         and nc.estado!=0
         ORDER by c.identificacion
         )
         UNION ALL
        (select r.fecha,c.identificacion,c.nombre,r.secuencia,IFNULL(r.valor_fuente,0)+IFNULL(valor_iva,0) as debe,0 as haber,'RETENCION' as tipo
        from ct_cliente_retencion as r
         join ct_clientes c on r.id_cliente=c.identificacion
        where r.id_empresa=$idEmpresa
         and r.estado!=0
         ORDER by c.identificacion
        )
        UNION ALL
        (        
        select ccc.fecha,c.identificacion,c.nombre,ccc.secuencia,IFNULL(ccc.total,0) as debe,0 as haber,'CRUCE CUENTAS' as tipo 
        from ct_cruce_cuentas_clientes as ccc
         join ct_clientes c on ccc.id_cliente=c.identificacion
        where ccc.id_empresa=$idEmpresa
         and ccc.estado!=0
         ORDER by c.identificacion
        )
         ) as cuenta 
         where cuenta.fecha<='$r->fecha_hasta'
         ORDER by cuenta.identificacion,cuenta.fecha ASC";
        return DB::select($sql);
    }
    public function rvendedor()
    {
        return $this->belongsTo('Sis_medico\User', 'ci_vendedor');
    }
    public static function excelInformeNeto($data)
    {
        $titulos = ["No", "Fecha", "Comprobante", "Identificación", "Cliente", "Vendedor", "Ventas", "Descuento", "Ventas neta", "NC", "Ventas netas final", "Estado", "Creado", "Anulado"];
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P");

        Excel::create('Informe de ventas netas', function ($excel) use ($titulos, $posicion, $data) {
            $excel->sheet('Informe de ventas netas', function ($sheet) use ($titulos, $posicion, $data) {


                $sheet->mergeCells('A1:N1');

                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('INFORME VENTAS NETA');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(12);
                    $cell->setFontColor('#050404');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo = 2;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $contador = 1;
                $comienzo++;
                $subtotal0_ = 0;
                $subtotal12_ = 0;
                $descuentos_ = 0;
                $subtotales_ = 0;
                $totalNC_ = 0;
                $notaC12_ = 0;
                $notaC0_ = 0;
                $ventaNetas_ = 0;
                foreach ($data["ventas"] as $soporte) {

                    $datos_excel = [];
                    $estado = 'Inactiva';
                    if ($soporte->estado != 0) {
                        $estado = 'Activa';
                    }

                    $subtotal = ($soporte->subtotal_0 + $soporte->subtotal_12) - $soporte->descuento;
                    $totalNC = $soporte->subtotal0 + $soporte->subtotal12;
                    $ventaNeta = $subtotal - $totalNC;

                    if ($soporte->estado != 0) {
                        $subtotal0_ += $soporte->subtotal_0;
                        $subtotal12_ += $soporte->subtotal_12;
                        $descuentos_ += $soporte->descuento;
                        $subtotales_ += ($soporte->subtotal_0 + $soporte->subtotal_12) - $soporte->descuento;
                        $notaC0_ += $soporte->subtotal0;
                        $notaC12_ += $soporte->subtotal12;
                        $totalNC_ += $soporte->subtotal0 + $soporte->subtotal12;
                        $ventaNetas_ += $ventaNeta;
                    }

                    array_push($datos_excel, $contador, $soporte->fecha, $soporte->nro_comprobante, $soporte->id_cliente, $soporte->nombre_cliente, $soporte->vendedor, $soporte->subtotal_12 + $soporte->subtotal_0, $soporte->descuento, $subtotal, $totalNC, $ventaNeta, $estado, $soporte->creado_por, "");



                    //dd($datos_excel);
                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                    $contador++;
                }

                $sheet->mergeCells('E' . $comienzo . ':F' . $comienzo);
                $sheet->cell('E' . $comienzo, function ($cell) {
                    $cell->setValue("TOTALES");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(12);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $sheet->cell('G' . $comienzo, function ($cell) use ($subtotal12_, $subtotal0_) {
                    $cell->setValue('$ ' . number_format($subtotal12_ + $subtotal0_, 2));
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H' . $comienzo, function ($cell) use ($descuentos_) {
                    $cell->setValue('$ ' . number_format($descuentos_, 2));
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I' . $comienzo, function ($cell) use ($subtotales_) {
                    $cell->setValue('$ ' . number_format($subtotales_, 2));
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J' . $comienzo, function ($cell) use ($totalNC_) {
                    $cell->setValue('$ ' . number_format($totalNC_, 2));
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K' . $comienzo, function ($cell) use ($ventaNetas_) {
                    $cell->setValue('$ ' . number_format($ventaNetas_, 2));
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            });
        })->export('xlsx');
    }
}
