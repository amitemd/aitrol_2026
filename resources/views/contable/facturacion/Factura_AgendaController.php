<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Agenda;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Ct_Orden_Venta_Detalle;
use Sis_medico\Ct_Orden_Venta_Pago;
use Sis_medico\Ct_Productos_Tarifario;
use Sis_medico\Ct_productos_paquete;
use Sis_medico\Ct_Orden_Venta_Detalle_Paquete;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_ventas;
use Sis_medico\Empresa;
use Sis_medico\EstimadoSeguros;
use Sis_medico\hc_procedimientos;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Nivel;
use Sis_medico\Ct_productos;
use Sis_medico\PrecioProducto;


class Factura_AgendaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    public function facturar($id_agenda, Request $request)
    {
        // despues cambiare que la empresa esta clavada
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $divisas = Ct_Divisas::where('estado', '1')->get();
        $agenda  = Agenda::findorfail($id_agenda);
        $empresa = Empresa::find('0992704152001');
        if (is_null($empresa)) {
            $empresa = Empresa::find('0992704152001');
        }
        $paciente     = Paciente::findorfail($agenda->id_paciente);

        $seguros      = Seguro::where('seguros.inactivo', '1')
            ->where('promo_seguro', '<>', 1)
            ->orderBy('nombre', 'asc')->get();

        $empresas     = Empresa::where('id', '0992704152001')->orWhere('id', '1314490929001')->get();
        $ct_cliente   = ct_clientes::where('identificacion', $paciente->id_usuario)->first();
        $tipo_pago    = Ct_Tipo_Pago::all();
        $lista_banco  = Ct_Bancos::all();
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        $pago         = EstimadoSeguros::where('id_seguro', $agenda->id_seguro)->where('id_doctor', $agenda->id_doctor_1)->first();
        //Id_empresa Obtenida de Inicio de Session
        $id_empresa = $agenda->id_empresa;
        $nombre_emp = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        $productos = Ct_productos::where('estado_tabla', '1')->where('id_empresa','0992704152001')->get();
        if (is_null($pago)) {
            $pago = EstimadoSeguros::where('id_seguro', $agenda->id_seguro)->where('id_doctor', 'GASTRO')->first();
        }

        $tipo = $agenda->proc_consul;
        $iva  = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();

        if ($agenda->proc_consul == 0) {

            return view('contable/facturacion/agendan', ['agenda' => $agenda,'productos'=>$productos, 'iva' => $iva, 'empresa' => $empresa, 'paciente' => $paciente, 'seguros' => $seguros, 'ct_cliente' => $ct_cliente, 'id_cliente' => $paciente->id_usuario, 'pago' => $pago, 'empresas' => $empresas, 'divisas' => $divisas, 'nombre_emp' => $nombre_emp, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'tipo_tarjeta' => $tipo_tarjeta, 'tipo' => $tipo]);
        }

        if ($agenda->proc_consul == 1) {
            $total_recibos = Ct_Orden_Venta::where('id_agenda', $id_agenda)->where('estado', 1)->count();
            if ($total_recibos > 0) {

                /*
                select *  from sis_medico_prb2.historiaclinica h, sis_medico_prb2.hc_procedimientos hc_pro,
                sis_medico_prb2.hc_procedimiento_final hc_pro_fin, sis_medico_prb2.procedimiento pro,
                sis_medico_prb2.agenda a, sis_medico_prb2.ct_productos_procedimiento prod_proc

                where hc_pro.id_hc=h.hcid and hc_pro_fin.id_hc_procedimientos=hc_pro.id and
                pro.id=hc_pro_fin.id_procedimiento and a.id=h.id_agenda and
                h.id_agenda = 21136 and prod_proc.id_procedimiento=pro.id;

                $proc= historiaclinica::where('historiaclinica.id_paciente',$paciente->id)
                ->join('hc_procedimientos as hc_pro','hc_pro.id_hc','historiaclinica.hcid')
                ->join('hc_procedimiento_final as hc_pro_fin','hc_pro_fin.id_hc_procedimientos','hc_pro.id')
                ->join('procedimiento as pro', 'pro.id' , 'hc_pro_fin.id_procedimiento')
                ->join('agenda as a','a.id','historiaclinica.id_agenda')
                ->join('seguros as s','s.id','hc_pro.id_seguro')
                ->join('empresa as emp','emp.id','hc_pro.id_empresa')
                ->whereNotNull('pro.id_grupo_procedimiento')
                ->orderBy('hc_pro_fin.created_at','desc')
                ->select('historiaclinica.hcid','hc_pro_fin.id_procedimiento','hc_pro.id_seguro','pro.nombre','a.fechaini','hc_pro.id_empresa', 's.nombre as nombre_seguro', 'emp.nombrecomercial', 'hc_pro.id as id_hc_proced','emp.nombre_corto')->get();
                 */

                //falta unir con producto para ver el iva y con precio producto
                $procedimientos = historiaclinica::where('historiaclinica.id_agenda', $id_agenda)
                    ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'historiaclinica.hcid')
                    ->join('hc_procedimiento_final as hc_pro_fin', 'hc_pro_fin.id_hc_procedimientos', 'hc_pro.id')
                    ->join('procedimiento as pro', 'pro.id', 'hc_pro_fin.id_procedimiento')
                    ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
                    ->join('ct_productos_procedimiento as prod_proc', 'prod_proc.id_procedimiento', 'pro.id')
                    ->join('ct_productos_paquete as pp', 'pp.id_producto', 'prod_proc.id_producto')

                    ->join('ct_productos as p', 'p.id', 'pp.id_paquete')

                    ->orderBy('hc_pro_fin.created_at', 'desc')
                    ->select('p.id', 'pp.cantidad as cantidad', 'p.nombre as procedimiento', 'p.iva', 'p.codigo as codproducto', 'prod_proc.id_producto as idproducto', 'historiaclinica.hcid', 'hc_pro_fin.id_procedimiento', 'hc_pro.id_seguro', 'p.nombre', 'a.fechaini', 'hc_pro.id_empresa', 'hc_pro.id as id_hc_proced')->get();

                /*
                select * from sis_medico_prb2.hc_procedimientos where id_hc = 13562;
                select * from sis_medico_prb2.movimiento_paciente where id_hc_procedimientos = 8329;
                select * from sis_medico_prb2.movimiento where id in (5542,5543, 5670, 5671);
                select * from sis_medico_prb2.producto where id in(14,28);

                 */
                $insumos_pistoleados = historiaclinica::where('historiaclinica.id_agenda', $id_agenda)
                    ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'historiaclinica.hcid')
                    ->join('movimiento_paciente as mov_pac', 'mov_pac.id_hc_procedimientos', 'hc_pro.id')
                    ->join('movimiento as mov', 'mov.id', 'mov_pac.id_movimiento')
                    ->join('ct_productos_insumos as prod_in', 'prod_in.id_insumo', 'mov.id_producto')
                    ->join('ct_productos as p', 'p.id', 'prod_in.id_producto')
                    ->select('p.id', 'p.nombre as procedimiento', 'p.iva', 'p.codigo as codproducto', 'prod_in.id_producto as idproducto', 'historiaclinica.hcid', 'hc_pro.id_seguro', 'p.nombre', 'hc_pro.id_empresa', 'hc_pro.id as id_hc_proced')->get();
                
                return view('contable/facturacion/agendan', ['agenda' => $agenda, 'iva' => $iva, 'empresa' => $empresa,'productos'=>$productos, 'paciente' => $paciente, 'seguros' => $seguros, 'ct_cliente' => $ct_cliente, 'id_cliente' => $paciente->id_usuario, 'pago' => $pago, 'empresas' => $empresas, 'divisas' => $divisas, 'nombre_emp' => $nombre_emp, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'tipo_tarjeta' => $tipo_tarjeta, 'tipo' => $tipo, 'procedimientos' => $procedimientos, 'insumos_pistoleados' => $insumos_pistoleados]);
            } else {

                $proced2 = agenda::where('agenda.id', $id_agenda)
                    ->join('agenda_procedimiento as ap', 'ap.id_agenda', 'agenda.id')
                    ->join('procedimiento as pro', 'pro.id', 'ap.id_procedimiento')
                    ->join('ct_productos_procedimiento as prod_proc', 'prod_proc.id_procedimiento', 'pro.id')
                    ->join('ct_productos as p', 'p.id', 'prod_proc.id_producto')
                    ->select(
                        'prod_proc.nombre as procedimiento',
                        'p.iva',
                        'p.codigo as codproducto',
                        'prod_proc.id_producto as idproducto',
                        'pro.nombre',
                        'agenda.fechaini'
                    );

                $procedimientos = agenda::where('agenda.id', $id_agenda)
                    //->join('agenda_procedimiento as ap', 'ap.id_agenda', 'agenda.id')
                    ->join('procedimiento as pro', 'pro.id', 'agenda.id_procedimiento')
                    ->join('ct_productos_procedimiento as prod_proc', 'prod_proc.id_procedimiento', 'pro.id')
                    ->join('ct_productos as p', 'p.id', 'prod_proc.id_producto')
                    ->select(
                        'prod_proc.nombre as procedimiento',
                        'p.iva',
                        'p.codigo as codproducto',
                        'prod_proc.id_producto as idproducto',
                        'pro.nombre',
                        'agenda.fechaini'
                    )->union($proced2)->get();

                //array_push($procedimientos, $proced2);

                $insumos_pistoleados = null;
                
                return view('contable/facturacion/agendan', ['agenda' => $agenda, 'iva' => $iva,'productos'=>$productos, 'empresa' => $empresa, 'paciente' => $paciente, 'seguros' => $seguros, 'ct_cliente' => $ct_cliente, 'id_cliente' => $paciente->id_usuario, 'pago' => $pago, 'empresas' => $empresas, 'divisas' => $divisas, 'nombre_emp' => $nombre_emp, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'tipo_tarjeta' => $tipo_tarjeta, 'tipo' => $tipo, 'procedimientos' => $procedimientos, 'insumos_pistoleados' => $insumos_pistoleados]);
            }
        }
    }

    public function valores_seguro(Request $request)
    {

        $valores = Ct_Productos_Tarifario::where('id_seguro', $request['id_seguro'])
            ->where('id_producto', $request['id_producto'])->get();
        return $valores;
    }

    public function buscar_cliente(Request $request)
    {
        $identificacion = $request['term'];

        $data      = array();
        $productos = ct_clientes::where('identificacion', 'like', '%' . $identificacion . '%')->get();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->identificacion, 'identificacion' => $product->identificacion);
        }
        if (count($data)>0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function cliente(Request $request)
    {

        $identificacion = $request['cedula'];
        $cliente        = ct_clientes::where('identificacion', 'like', $identificacion)->first();
        $paciente       = null;
        if (is_null($cliente)) {
            $paciente = Paciente::find($identificacion);
        }

        if (!is_null($cliente)) {
            return response()->json([
                'dato'      => 1,
                'nombre'    => $cliente->nombre,
                'ciudad'    => $cliente->ciudad_representante,
                'direccion' => $cliente->direccion_representante,
                'telefono'  => $cliente->telefono1_representante,
                'email'     => $cliente->email_representante,
            ]);
        } elseif (!is_null($paciente)) {
            return response()->json([
                'dato'      => 1,
                'nombre'    => $paciente->nombre1 . ' ' . $paciente->nombre2 . ' ' . $paciente->apellido1 . ' ' . $paciente->apellido2 . ' ',
                'ciudad'    => $paciente->ciudad,
                'direccion' => $paciente->direccion,
                'telefono'  => $paciente->telefono1,
                'email'     => $paciente->usuario->email,
            ]);
        } else {
            return ['dato' => 'no', 'value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function verificar_seguro($id_seguro)
    {
        $seguro = Seguro::find($id_seguro);
        return $seguro->copago;
    }

    public function verificar_pago($id_seguro, $id_doctor)
    {
        $pago = EstimadoSeguros::where('id_seguro', $id_seguro)->where('id_doctor', $id_doctor)->first();
        if (is_null($pago)) {
            $pago = EstimadoSeguros::where('id_seguro', $id_seguro)->where('id_doctor', 'GASTRO')->first();
        }

        if (!is_null($pago)) {
            return trim($pago->costo);
        }
        return trim(0);
    }

    public function guardar_orden(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;

        $ct_cliente = ct_clientes::where('identificacion', $request['cedula'])->first();
        if (is_null($ct_cliente)) {
            ct_clientes::create([
                'identificacion'          => $request['cedula'],
                'nombre'                  => $request['razon_social'],
                'tipo'                    => $request['tipo_identificacion'],
                'clase'                   => 'normal',
                'estado'                  => '1',
                'ciudad_representante'    => $request['ciudad'],
                'direccion_representante' => $request['direccion'],
                'telefono1_representante' => $request['telefono'],
                'email_representante'     => $request['email'],
                'pais'                    => 'Ecuador',
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
                'id_usuariocrea'          => $id_usuario,
                'id_usuariomod'           => $id_usuario,
            ]);
            $ct_cliente = ct_clientes::where('identificacion', $request['cedula'])->first();
        } else {
            ct_clientes::where('identificacion', $request['cedula'])
                ->update([
                    'nombre'                  => $request['razon_social'],
                    'tipo'                    => $request['tipo_identificacion'],
                    'ciudad_representante'    => $request['ciudad'],
                    'direccion_representante' => $request['direccion'],
                    'email_representante'     => $request['email'],
                    'telefono1_representante' => $request['telefono'],
                    'ip_modificacion'         => $ip_cliente,
                    'id_usuariocrea'          => $id_usuario,
                ]);
        }
        $valor_copago  = $request['valor_copago'];
        $valor_copago2 = $request['valor_copago2'];
        if (!is_null($valor_copago) && $valor_copago > 0) {
            $oda = 1;
        } else {
            $oda = 0;
        }
        /*if (!is_null($valor_copago2) && $valor_copago2 > 0) {
        $oda = 1;
        } else {
        $oda = 0;

        }*/

        if ($request['id_seguro'] == 4) {

            $id_orden = Ct_Orden_Venta::insertGetId([
                'id_agenda'           => $request['id_agenda'],
                'id_empresa'          => $request['id_empresa'],
                'fecha_emision'       => $request['fecha'],
                'oda'                 => $oda,
                'numero_oda'          => $request['numero_oda'],
                'valor_oda'           => 0,
                'id_seguro'           => $request['id_seguro'],
                'id_nivel'            => $request['id_nivel'],
                'tipo_identificacion' => $request['tipo_identificacion'],
                'identificacion'      => $request['cedula'],
                'razon_social'        => $request['razon_social'],
                'ciudad'              => $request['ciudad'],
                'direccion'           => $request['direccion'],
                'telefono'            => $request['telefono'],
                'total_sin_tarjeta'   => $request['total_sin_tarjeta'],
                'observacion'         => $request['observacion'],
                'caja'                => $request['caja'],
                'total'               => $request['total_pro'],
                'email'               => $request['email'],
                'iva'                 => '0',
                'subtotal_0'          => $request['total_pro'],
                'subtotal_12'         => '0',
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $id_usuario,
                'id_usuariomod'       => $id_usuario,
            ]);
        } else {

            $id_orden = Ct_Orden_Venta::insertGetId([
                'id_agenda'           => $request['id_agenda'],
                'id_empresa'          => $request['id_empresa'],
                'fecha_emision'       => $request['fecha'],
                'oda'                 => $oda,
                'numero_oda'          => $request['numero_oda'],
                'valor_oda'           => $valor_copago,
                'id_seguro'           => $request['id_seguro'],
                'id_nivel'            => $request['id_nivel'],
                'tipo_identificacion' => $request['tipo_identificacion'],
                'identificacion'      => $request['cedula'],
                'razon_social'        => $request['razon_social'],
                'ciudad'              => $request['ciudad'],
                'direccion'           => $request['direccion'],
                'telefono'            => $request['telefono'],
                'total_sin_tarjeta'   => $request['total_sin_tarjeta'],
                'observacion'         => $request['observacion'],
                'caja'                => $request['caja'],
                'total'               => $request['pago'],
                'email'               => $request['email'],
                'iva'                 => '0',
                'subtotal_0'          => $request['pago'],
                'subtotal_12'         => '0',
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $id_usuario,
                'id_usuariomod'       => $id_usuario,
            ]);
        }

        $valor_fi   = 0;
        $fi_adminis = 0;
        $consulta   = 0;
        $porcentaje = 0;

        for ($i = 0; $i < $request['contador_pago']; $i++) {
            $fi_ad_p = 0;
            if ($request['visibilidad_pago' . $i] == 1) {
                if (isset($request['fi' . $i])) {
                    $fi_adminis = 1;
                    $fi_ad_p    = 1;
                    if ($request['id_tip_pago' . $i] == 4) {
                        $porcentaje = 0.07;
                    } elseif ($request['id_tip_pago' . $i] == 6) {
                        $porcentaje = 0.02;
                    }
                    $valor_fi = $valor_fi + ($request['total' . $i] - $request['valor_base' . $i]);
                }
                $consulta = $consulta + $request['valor_base' . $i];
                Ct_Orden_Venta_Pago::create([
                    'id_orden'        => $id_orden,
                    'fecha'           => $request['fecha' . $i],
                    'tipo'            => $request['id_tip_pago' . $i],
                    'tipo_tarjeta'    => $request['tipo_tarjeta' . $i],
                    'numero'          => $request['numero' . $i],
                    'banco'           => $request['id_banco' . $i],
                    'valor'           => $request['total' . $i],
                    'posee_fi'        => $fi_ad_p,
                    'p_fi'            => $porcentaje,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                ]);
            }
        }
        //return $request->all();
        if ($request['tipo_dato'] == 1) {
            $total_oda       = 0;
            $total           = 0;
            $total_iva       = 0;
            $subtotal_0      = 0;
            $subtotal_12     = 0;
            $total_descuento = 0;
            $total_deducible = 0;

            if ($request['id_seguro'] == 4) {

                for ($i = 0; $i < $request['contador_procedimiento']; $i++) {
                    if ($request['visibilidad_procedimiento' . $i] == 1) {

                        $tipo_dcto = $request['tipo_desc' . $i];
                        $tipo_oda  = $request['tipo_cob_seguro' . $i];
                        $valor_oda = $request['copago' . $i];
                        $p_oda     = $request['p_oda' . $i];
                        $descuento = $request['desc' . $i];
                        $p_dcto    = $request['p_dcto' . $i];
                        $valor_oda_hum = $request['val_deducible' . $i];

                        $valor_iva = ($request['iva_item' . $i] == 1) ? (($request['valor_pro' . $i] * $request['cantidad' . $i]) - $valor_oda - $descuento) * $request["ivareal"] : 0;
                        $iva       = ($request['iva_item' . $i] == 1) ? $request["ivareal"] : 0;

                        Ct_Orden_Venta_Detalle::create([
                            'id_orden'          => $id_orden,
                            'cod_prod'          => $request['cod_prod' . $i],
                            'descripcion'       => $request['procedimiento' . $i],
                            'id_producto'       => $request['id_prod' . $i],
                            'cantidad'          => $request['cantidad' . $i],
                            'precio'            => $request['valor_pro' . $i],
                            'total'             => $request['total_procedimiento' . $i],
                            'tipo_oda'          => $tipo_oda,
                            'p_oda'             => $p_oda,
                            'valor_oda'         => $valor_oda,
                            'tipo_dcto'         => $tipo_dcto,
                            'p_dcto'            => $p_dcto,
                            'descuento'         => $descuento,
                            'iva'               => $iva,
                            'valor_iva'         => $valor_iva,
                            'ident_paquete'     => $request['ident_paquete' . $i],
                            'valor_deducible'   => $request['deducible_id' . $i],
                            'valor_oda_hum'     => $valor_oda_hum,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                            'id_usuariocrea'    => $id_usuario,
                            'id_usuariomod'     => $id_usuario,
                        ]);

                        //Identificamos al Producto como un paquete
                        /*$id_prd = $request['id_prod' . $i];
                        $act_prod = Ct_productos::find($id_prd);
                        
                        $arr_idp = [
                            'ident_paquete' => $request['ident_paquete' . $i],
                        ];
                        $act_prod->update($arr_idp);*/
                        $id_prd = $request['id_prod' . $i];
                        $act_prod = Ct_productos::where('id', $id_prd)->update(['ident_paquete' => $request['ident_paquete' . $i]]);

                        //$total_oda += $valor_oda;
                        $total_oda += $valor_oda_hum;
                        $total_iva += $valor_iva;
                        if ($request['iva_item' . $i] == 1) {
                            $subtotal_12 += (($request['cantidad' . $i] * $request['valor_pro' . $i]) - $valor_oda);
                        } else {
                            $subtotal_0 += (($request['cantidad' . $i] * $request['valor_pro' . $i]) - $valor_oda);
                        }
                        $total_descuento += $descuento;

                        //Verificamos si el Id_producto existe en la Tabla ct_productos_paquete
                        $existe_prod_paquete = Ct_productos_paquete::where('ct_productos_paquete.id_producto', $request['id_prod' . $i])
                            ->join('ct_producto_tarifario_paquete as ptq', 'ptq.id_producto_paquete', 'ct_productos_paquete.id')
                            ->where('ct_productos_paquete.estado', '1')
                            ->where('ptq.id_seguro', $request['id_seguro'])
                            ->where('ptq.id_nivel', $request['id_nivel'])
                            ->where('ptq.estado', '1')
                            ->select('ct_productos_paquete.id as id_prod_paquete', 'ptq.id as id_prod_tar_paquete', 'ptq.precio as precio', 'ptq.id_seguro as id_seguro', 'ptq.id_nivel as id_nivel', 'ct_productos_paquete.id_producto as id_producto', 'ct_productos_paquete.nombre as nomb_paquete', 'ct_productos_paquete.iva as iva')
                            ->get();

                        if (!is_null($existe_prod_paquete)) {

                            foreach ($existe_prod_paquete as $value) {

                                if ($value->iva == 1) {

                                    $nuev_iva = 0.12;

                                    $valor =  $value->precio / 1.12;

                                    $val_iva =  $valor * 0.12;
                                } else {

                                    $nuev_iva = 0.00;

                                    $valor =  $value->precio;

                                    $val_iva =  0.00;
                                }


                                $input_orden_detall_paquete = [
                                    'id_orden'              => $id_orden,
                                    'id_producto'           => $value->id_producto,
                                    'descripcion'           => $value->nomb_paquete,
                                    'id_producto_paquete'   => $value->id_prod_paquete,
                                    'cantidad'              => 1,
                                    'precio'                => $valor,
                                    'iva'                   => $nuev_iva,
                                    'valor_iva'             => $val_iva,
                                    //'valor_iva'             => $value->precio,
                                    //'valor_iva'             => 0.00,
                                    'id_usuariocrea'        => $id_usuario,
                                    'id_usuariomod'         => $id_usuario,
                                    'ip_creacion'           => $ip_cliente,
                                    'ip_modificacion'       => $ip_cliente,
                                ];

                                Ct_Orden_Venta_Detalle_Paquete::insert($input_orden_detall_paquete);
                            }
                        }
                    }
                }

                $total    = $subtotal_0 + $subtotal_12 + $total_iva - $total_descuento;
                $cabecera = Ct_Orden_Venta::find($id_orden);
                $arr_cab  = [
                    'valor_oda'   => $total_oda,
                    'total'       => $request['total_pro'],
                    'iva'         => $total_iva,
                    'subtotal_0'  => $request['total_pro'],
                    'subtotal_12' => $subtotal_12,
                    'descuento'   => $total_descuento,
                ];
                $cabecera->update($arr_cab);
            } else {

                for ($i = 0; $i < $request['contador_procedimiento']; $i++) {
                    if ($request['visibilidad_procedimiento' . $i] == 1) {

                        $tipo_dcto = $request['tipo_desc' . $i];
                        $tipo_oda  = $request['tipo_cob_seguro' . $i];
                        $valor_oda = $request['copago' . $i];
                        $p_oda     = $request['p_oda' . $i];
                        $descuento = $request['desc' . $i];
                        $p_dcto    = $request['p_dcto' . $i];

                        $valor_iva = ($request['iva_item' . $i] == 1) ? (($request['valor_pro' . $i] * $request['cantidad' . $i]) - $valor_oda - $descuento) * $request["ivareal"] : 0;
                        $iva       = ($request['iva_item' . $i] == 1) ? $request["ivareal"] : 0;
                        Ct_Orden_Venta_Detalle::create([
                            'id_orden'          => $id_orden,
                            'cod_prod'          => $request['cod_prod' . $i],
                            'descripcion'       => $request['procedimiento' . $i],
                            'id_producto'       => $request['id_prod' . $i],
                            'cantidad'          => $request['cantidad' . $i],
                            'precio'            => $request['valor_pro' . $i],
                            'total'             => $request['total_procedimiento' . $i],
                            'tipo_oda'          => $tipo_oda,
                            'p_oda'             => $p_oda,
                            'valor_oda'         => $valor_oda,
                            'tipo_dcto'         => $tipo_dcto,
                            'p_dcto'            => $p_dcto,
                            'descuento'         => $descuento,
                            'iva'               => $iva,
                            'valor_iva'         => $valor_iva,
                            'ident_paquete'     => $request['ident_paquete' . $i],
                            'valor_deducible'   => $request['deducible_id' . $i],
                            //'valor_oda_hum'     => $request['val_deducible' . $i],
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                            'id_usuariocrea'    => $id_usuario,
                            'id_usuariomod'     => $id_usuario,
                        ]);
                        //$total_oda += $valor_oda;

                        //Identificamos al Producto como un paquete
                        /*$id_prd = $request['id_prod' . $i];
                         $act_prod = Ct_productos::find($id_prd);
                         
                         $arr_idp = [
                             'ident_paquete' => $request['ident_paquete' . $i],
                         ];
                         $act_prod->update($arr_idp);*/

                        $id_prd = $request['id_prod' . $i];
                        $act_prod = Ct_productos::where('id', $id_prd)->update(['ident_paquete' => $request['ident_paquete' . $i]]);


                        $total_oda += $valor_oda - ($request['deducible_id' . $i]);
                        $total_iva += $valor_iva;
                        if ($request['iva_item' . $i] == 1) {
                            $subtotal_12 += (($request['cantidad' . $i] * $request['valor_pro' . $i]) - $valor_oda);
                        } else {
                            $subtotal_0 += (($request['cantidad' . $i] * $request['valor_pro' . $i]) - $valor_oda);
                        }
                        $total_descuento += $descuento;

                        //Cuando el Seguro es de Tipo Diferente al Particular y Humana 
                        if ($request['id_seguro'] != 1) {

                            //Verificamos si el Id_producto existe en la Tabla ct_productos_paquete
                            $existe_prod_paquete = Ct_productos_paquete::where('ct_productos_paquete.id_producto', $request['id_prod' . $i])
                                ->join('ct_producto_tarifario_paquete as ptq', 'ptq.id_producto_paquete', 'ct_productos_paquete.id')
                                ->where('ct_productos_paquete.estado', '1')
                                ->where('ptq.id_seguro', $request['id_seguro'])
                                ->where('ptq.id_nivel', $request['id_nivel'])
                                ->where('ptq.estado', '1')
                                ->select('ct_productos_paquete.id as id_prod_paquete', 'ptq.id as id_prod_tar_paquete', 'ptq.precio as precio', 'ptq.id_seguro as id_seguro', 'ptq.id_nivel as id_nivel', 'ct_productos_paquete.id_producto as id_producto', 'ct_productos_paquete.nombre as nomb_paquete', 'ct_productos_paquete.iva as iva')
                                ->get();

                            if (!is_null($existe_prod_paquete)) {

                                foreach ($existe_prod_paquete as $value) {

                                    if ($value->iva == 1) {

                                        $nuev_iva = 0.12;

                                        $valor =  $value->precio / 1.12;

                                        $val_iva =  $valor * 0.12;
                                    } else {

                                        $nuev_iva = 0.00;

                                        $valor =  $value->precio;

                                        $val_iva =  0.00;
                                    }

                                    $input_orden_detall_paquete = [
                                        'id_orden'              => $id_orden,
                                        'id_producto'           => $value->id_producto,
                                        'descripcion'           => $value->nomb_paquete,
                                        'id_producto_paquete'   => $value->id_prod_paquete,
                                        'cantidad'              => 1,
                                        'precio'                => $valor,
                                        'iva'                   => $nuev_iva,
                                        'valor_iva'             => $val_iva,
                                        //'valor_iva'             => $value->precio,
                                        //'valor_iva'             => 0.00,
                                        'id_usuariocrea'        => $id_usuario,
                                        'id_usuariomod'         => $id_usuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                    ];

                                    Ct_Orden_Venta_Detalle_Paquete::insert($input_orden_detall_paquete);
                                }
                            }
                        }

                        if ($request['id_seguro'] == 1) {

                            $existe_prod_paquete = Ct_productos_paquete::where('ct_productos_paquete.id_producto', $request['id_prod' . $i])
                                ->where('ct_productos_paquete.estado', '1')
                                ->select('ct_productos_paquete.id as id_prod_paquete', 'ct_productos_paquete.id_producto as id_producto', 'ct_productos_paquete.nombre as nomb_paquete', 'ct_productos_paquete.precio as precio_pvp', 'ct_productos_paquete.iva as iva', 'ct_productos_paquete.cantidad as cantidad')
                                ->get();

                            if (!is_null($existe_prod_paquete)) {

                                foreach ($existe_prod_paquete as $value) {

                                    if ($value->iva == 1) {

                                        $nuev_iva = 0.12;
                                    } else {

                                        $nuev_iva = 0.00;
                                    }

                                    $input_orden_detall_paquete = [
                                        'id_orden'              => $id_orden,
                                        'id_producto'           => $value->id_producto,
                                        'descripcion'           => $value->nomb_paquete,
                                        'id_producto_paquete'   => $value->id_prod_paquete,
                                        'cantidad'              => $value->cantidad,
                                        'precio'                => $value->precio_pvp,
                                        'iva'                   => $nuev_iva,
                                        'valor_iva'             => ($value->cantidad * $value->precio_pvp) * $nuev_iva,
                                        'id_usuariocrea'        => $id_usuario,
                                        'id_usuariomod'         => $id_usuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                    ];

                                    Ct_Orden_Venta_Detalle_Paquete::insert($input_orden_detall_paquete);
                                }
                            }
                        }
                        //Total Deducible
                        /*if($request['deducible_id' . $i] > 0){
                            $total_deducible += ($request['deducible_id' . $i]);
                        }*/
                    }
                }
                $total    = $subtotal_0 + $subtotal_12 + $total_iva - $total_descuento;
                $cabecera = Ct_Orden_Venta::find($id_orden);
                $arr_cab  = [
                    'valor_oda'   => $total_oda,
                    'total'       => $total,
                    'iva'         => $total_iva,
                    'subtotal_0'  => $subtotal_0,
                    'subtotal_12' => $subtotal_12,
                    'descuento'   => $total_descuento,
                ];
                $cabecera->update($arr_cab);
            }
        } else {
            Ct_Orden_Venta_Detalle::create([
                'id_orden'        => $id_orden,
                'descripcion'     => 'Consulta Medica Especializada',
                'cantidad'        => 1,
                'precio'          => $consulta,
                'total'           => $consulta,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
            ]);
            if ($fi_adminis == 1) {
                Ct_Orden_Venta_Detalle::create([
                    'id_orden'        => $id_orden,
                    'descripcion'     => 'FEE Administrativo',
                    'cantidad'        => 1,
                    'precio'          => $valor_fi,
                    'total'           => $valor_fi,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                ]);
            }
        }


        //Verificamos que el Precio Neto de Procedimiento , coincida con el Total de Forma de Pago

        /*$sum_prec_neto = 0;
         for ($i = 0; $i < $request['contador_procedimiento']; $i++){
             if ($request['visibilidad_procedimiento' . $i] == 1) {
               $sum_prec_neto = $sum_prec_neto+$request['total_procedimiento' . $i];
             }
        }
         
        $sum_form_pago = 0;
        for ($i = 0; $i < $request['contador_pago']; $i++) {
            if ($request['visibilidad_pago' . $i] == 1) {
               $sum_form_pago = $sum_form_pago+$request['total' . $i];
            }
        }*/

        $id_orde = Ct_Orden_Venta::where('id', $id_orden)->first();
        $suma_proced = round($id_orde->detalles->sum('total'), 2);
        $suma_form_pag = round($id_orde->pagos->sum('valor'), 2);
        $resta = $suma_proced - $suma_form_pag;

        if ($resta == 0) {

            $val_dif = 0;


            return ['val_dif' => $val_dif, 'id_orden' => $id_orden];
        } else {

            $val_dif = 1;
            $valor = 1;
            return ['val_dif' => $val_dif, 'id_orden' => $id_orden, 'valor' => $valor];
        }
    }

    public function guardar_factura(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $fecha_hoy  = Date('Y-m-d H:i:s');

        $agenda     = Agenda::find($request['id_agenda']);
        $ct_cliente = ct_clientes::where('identificacion', $request->cedula)->first();
        //ct_orden_ventas  = Ct_Orden_Venta::where('caja')->where('caja', $request[''])->first();

        $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
        $cod_caj    = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();

        $num_comprobante = $cod_sucurs->codigo_sucursal . '-' . $cod_caj->codigo_caja . '-' . $request['nfactura'];

        $historia      = Historiaclinica::where('id_agenda', $agenda->id)->first();
        $procedimiento = hc_procedimientos::where('id_hc', $historia->hcid)->first();

        $text = 'Fact #' . ':' . $num_comprobante . '-' . 'CONSULTA';

        //Insertamos en la Tabla Ct_Asientos_Cabecera
        $input_cabecera = [
            'fecha_asiento'   => $fecha_hoy,
            'fact_numero'     => $request['nfactura'],
            'id_empresa'      => $request['id_empresa'],
            'observacion'     => $text,
            'valor'           => $request['pago'],
            'id_usuariocrea'  => $id_usuario,
            'id_usuariomod'   => $id_usuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

        //Guardado de Factura
        $factura_consulta = [
            'sucursal'             => $cod_sucurs->codigo_sucursal,
            'punto_emision'        => $cod_caj->codigo_caja,
            'numero'               => $request['nfactura'],
            'nro_comprobante'      => $num_comprobante,
            'id_asiento'           => $id_asiento_cabecera,
            'id_empresa'           => $request['id_empresa'],
            'tipo'                 => $request['tipo'],
            'fecha'                => $request['fecha'],
            'divisas'              => $request['divisas'],
            //'id_cliente'           => $ct_cliente->identificacion,
            //'direccion_cliente'    => $request['direccion'],
            'id_cliente'           => '0922794102',
            'direccion_cliente'    => 'Consulta Medica',
            'ruc_id_cliente'       => $request['cedula'],
            'telefono_cliente'     => $request['telefono'],
            'email_cliente'        => $request['email'],
            'id_paciente'          => $request['idpaciente'],
            'nombres_paciente'     => $request['nombres'],
            'seguro_paciente'      => $request['id_seguro'],
            'procedimientos'       => 'Consulta Medica',
            'id_hc_procedimientos' => $procedimiento->id,
            'fecha_procedimiento'  => substr($agenda->fechaini, 0, -9),
            //'id_vendedor'          => '1784559964',
            'id_recaudador'        => '1203806094',
            'nota'                 => 'Consulta',
            'subtotal_0'           => $request['pago'],
            //'subtotal'             => $request['pago'],
            'base_imponible'       => $request['pago'],
            'total_final'          => $request['pago'],
            'ip_creacion'          => $ip_cliente,
            'ip_modificacion'      => $ip_cliente,
            'id_usuariocrea'       => $id_usuario,
            'id_usuariomod'        => $id_usuario,

        ];

        $id_venta = Ct_ventas::insertGetId($factura_consulta);

        //Insertamos en la ct_detalle_venta
        ct_detalle_venta::create([

            'id_ct_ventas'    => $id_venta,
            'id_ct_productos' => 'RCONSUL',
            'nombre'          => 'CONSULTA MEDICA',
            'cantidad'        => '1',
            'precio'          => $request['pago'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $id_usuario,
            'id_usuariomod'   => $id_usuario,
        ]);

        /*****************************
         ***MODULO CUENTA POR COBRAR***
        /******************************/

        //cUENTAS X COBRAR CLIENTES
        $plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
        Ct_Asientos_Detalle::create([

            'id_asiento_cabecera' => $id_asiento_cabecera,
            'id_plan_cuenta'      => '1.01.02.05.01',
            'descripcion'         => $plan_cuentas->nombre,
            'fecha'               => $fecha_hoy,
            'debe'                => $request['pago'],
            'haber'               => '0',
            'id_usuariocrea'      => $id_usuario,
            'id_usuariomod'       => $id_usuario,
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,

        ]);

        /**********************************
         ***VENTA DE MERCADERIA TARIFA 0%***
        /**********************************/
        $plan_cuentas = Plan_Cuentas::where('id', '4.1.01.01')->first();
        Ct_Asientos_Detalle::create([

            'id_asiento_cabecera' => $id_asiento_cabecera,
            'id_plan_cuenta'      => '4.1.01.01',
            'descripcion'         => $plan_cuentas->nombre,
            'fecha'               => $fecha_hoy,
            'debe'                => '0',
            'haber'               => $request['pago'],
            'id_usuariocrea'      => $id_usuario,
            'id_usuariomod'       => $id_usuario,
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,

        ]);

        if (isset($request["copago"])) {
            $procedimiento = hc_procedimientos::where('id_hc', $historia->hcid)->update(['pagado' => 1, 'copago' => 1]);
        } else {
            $procedimiento = hc_procedimientos::where('id_hc', $historia->hcid)->update(['pagado' => 1]);
        }

        return 'ok';
    }

    //Nueva Fucionalidad
    //Obtener Sucursales de la empresa seleccionada
    public function obtener_sucursal_empresa(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empre = $request['id_emp'];

        if (!is_null($id_empre)) {

            $suc_caja = DB::table('ct_sucursales as ct_s')
                ->where('ct_s.estado', 1)
                ->where('ct_s.id_empresa', $id_empre)
                ->get();
            return $suc_caja;
        } else {

            return 'no';
        }
    }

    //Obtener Caja de la Sucursal Seleccionada
    public function obtener_caja_sucursal(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_sucur = $request['id_sucur'];

        //return $id_sucur;

        if (!is_null($id_sucur)) {

            $caja = DB::table('ct_sucursales as ct_s')
                ->join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_s.id')
                ->where('ct_c.estado', 1)
                ->where('ct_s.id', $id_sucur)
                ->get();

            return $caja;
        } else {

            return 'no';
        }
    }

    public function obtener_numero_factura()
    {

        //Obtener el Total de Registros de la Tabla ct_ventas
        $contador_ctv = DB::table('ct_ventas')->get()->count();

        if ($contador_ctv == 0) {

            //return 'No Retorno nada';
            $num            = '1';
            $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            return $numero_factura;
        } else {

            //Obtener Ultimo Registro de la Tabla ct_ventas
            $max_id = DB::table('ct_ventas')->max('id');

            if (($max_id >= 1) && ($max_id < 10)) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                return $numero_factura;
            }

            if (($max_id >= 10) && ($max_id < 99)) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                return $numero_factura;
            }

            if (($max_id >= 100) && ($max_id < 1000)) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                return $numero_factura;
            }

            if ($max_id == 1000) {
                $numero_factura = $max_id;
                return $numero_factura;
            }
        }
    }

    public function imprimir_ride($id_orden)
    {
        $fact_venta = Ct_Orden_Venta::findorfail($id_orden);
        $ct_for_pag = Ct_Orden_Venta_Pago::where('id_orden', $id_orden)->get();

        $agenda = $fact_venta->agenda;
        if ($agenda->proc_consul == '1') {
            $vistaurl = "contable.facturacion.pdf_comprobante_tributario_proc";
        } else {
            $vistaurl = "contable.facturacion.pdf_comprobante_tributario";
        }
        $view = \View::make($vistaurl, compact('fact_venta', 'ct_for_pag'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Comprobante de Orden-' . $id_orden . '.pdf');
    }

    //Modal Recibo Cobro
    public function obtener_modal($id_orden)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $fact_venta = Ct_Orden_Venta::findorfail($id_orden);

        $obtener_recibos = Ct_Orden_Venta::where('id_agenda', $fact_venta->id_agenda)->where('estado', 1)->orderby('id', 'asc')->paginate(10);

        return view('contable/facturacion/modal_recibo_cobro', ['fact_venta' => $fact_venta, 'obtener_recibos' => $obtener_recibos]);
    }

    //Anular Recibo Cobro
    public function anular_recibo_cobro(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_orden = $request['id_orden'];

        //return $id_orden;

        //Anulado
        $act_estado = [

            'estado' => '0',

        ];

        $fact_venta = Ct_Orden_Venta::findorfail($id_orden);

        Ct_Orden_Venta::where('id', $id_orden)->update($act_estado);

        //return redirect()->intended('/contable/ventas');

    }

    public function facturar_editar($id, $valor)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $fact_venta   = Ct_Orden_Venta::findorfail($id);
        $empresas     = Empresa::where('id', '0992704152001')->orWhere('id', '1314490929001')->get();
        $seguros      = Seguro::where('seguros.inactivo', '1')
            ->where('promo_seguro', '<>', 1)
            ->orderBy('nombre', 'asc')->get();
        $ct_cliente   = Ct_Clientes::where('identificacion', $fact_venta->identificacion)->first();
        $tipo_pago    = Ct_Tipo_Pago::all();
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        $lista_banco  = Ct_Bancos::all();
        $empresa      = Empresa::find($fact_venta->id_empresa);
        $agenda       = Agenda::find($fact_venta->id_agenda);
        $xcantidad    = Ct_Orden_Venta_Pago::where('id_orden', $id)->get()->count();


        //Obtenemos todos los Niveles
        $lista_niveles  = Nivel::all();


        $fact_venta_detalle = Ct_Orden_Venta_Detalle::where('id_orden', $id);
        $proced2            = agenda::where('agenda.id', $fact_venta->id_agenda)
            ->join('agenda_procedimiento as ap', 'ap.id_agenda', 'agenda.id')
            ->join('procedimiento as pro', 'pro.id', 'ap.id_procedimiento')
            ->join('ct_productos_procedimiento as prod_proc', 'prod_proc.id_procedimiento', 'pro.id')
            ->join('ct_productos as p', 'p.id', 'prod_proc.id_producto')
            ->select(
                'prod_proc.nombre as procedimiento',
                'p.iva',
                'p.codigo as codproducto',
                'prod_proc.id_producto as idproducto',
                'pro.nombre',
                'agenda.fechaini'
            );

        $procedimientos = agenda::where('agenda.id', $fact_venta->id_agenda)
            //->join('agenda_procedimiento as ap', 'ap.id_agenda', 'agenda.id')
            ->join('procedimiento as pro', 'pro.id', 'agenda.id_procedimiento')
            ->join('ct_productos_procedimiento as prod_proc', 'prod_proc.id_procedimiento', 'pro.id')
            ->join('ct_productos as p', 'p.id', 'prod_proc.id_producto')
            ->select(
                'prod_proc.nombre as procedimiento',
                'p.iva',
                'p.codigo as codproducto',
                'prod_proc.id_producto as idproducto',
                'pro.nombre',
                'agenda.fechaini'
            )->union($proced2)->get();

        //array_push($procedimientos, $proced2);

        $insumos_pistoleados = null;
        $iva                 = Ct_Configuraciones::where('id_plan', '4.1.01.02')->where('estado', '1')->first();

        if ($agenda->proc_consul == '0') {
            return view('contable/facturacion/agenda_editar', ['orden' => $fact_venta, 'insumos_pistoleados' => $insumos_pistoleados, 'procedimientos' => $procedimientos, 'agenda' => $agenda, 'empresas' => $empresas, 'seguros' => $seguros, 'ct_cliente' => $ct_cliente, 'tipo_pago' => $tipo_pago, 'tipo_tarjeta' => $tipo_tarjeta, 'lista_banco' => $lista_banco, 'xcantidad' => $xcantidad, 'empresa' => $empresa]);
        }
        if ($agenda->proc_consul == '1') {
            return view('contable/facturacion/agenda_procedimiento_edit', ['orden' => $fact_venta, 'insumos_pistoleados' => $insumos_pistoleados, 'procedimientos' => $procedimientos, 'agenda' => $agenda, 'empresas' => $empresas, 'seguros' => $seguros, 'ct_cliente' => $ct_cliente, 'tipo_pago' => $tipo_pago, 'tipo_tarjeta' => $tipo_tarjeta, 'lista_banco' => $lista_banco, 'xcantidad' => $xcantidad, 'empresa' => $empresa, 'iva' => $iva, 'nivel' => $lista_niveles, 'valor' => $valor]);
        }
    }

    public function facturar_listado($id)
    {

        $fact_venta   = Ct_Orden_Venta::find($id);
        $venta_pago   = $fact_venta->pagos;
        $tipo_pago    = Ct_Tipo_Pago::all();
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        $lista_banco  = Ct_Bancos::all();
        //return $venta_pago;

        return view('contable/facturacion/listar', ['venta_pago' => $venta_pago, 'tipo_pago' => $tipo_pago, 'tipo_tarjeta' => $tipo_tarjeta, 'lista_banco' => $lista_banco]);
    }

    public function facturar_actualizar(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;

        $ct_cliente = ct_clientes::where('identificacion', $request['cedula'])->first();
        if (is_null($ct_cliente)) {
            ct_clientes::create([
                'identificacion'          => $request['cedula'],
                'nombre'                  => $request['razon_social'],
                'tipo'                    => $request['tipo_identificacion'],
                'clase'                   => 'normal',
                'estado'                  => '1',
                'ciudad_representante'    => $request['ciudad'],
                'direccion_representante' => $request['direccion'],
                'telefono1_representante' => $request['telefono'],
                'email_representante'     => $request['email'],
                'pais'                    => 'Ecuador',
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
                'id_usuariocrea'          => $id_usuario,
                'id_usuariomod'           => $id_usuario,
            ]);
            $ct_cliente = ct_clientes::where('identificacion', $request['cedula'])->first();
        } else {
            ct_clientes::where('identificacion', $request['cedula'])
                ->update([
                    'nombre'                  => $request['razon_social'],
                    'tipo'                    => $request['tipo_identificacion'],
                    'ciudad_representante'    => $request['ciudad'],
                    'direccion_representante' => $request['direccion'],
                    'telefono1_representante' => $request['telefono'],
                    'email_representante'     => $request['email'],
                    'ip_modificacion'         => $ip_cliente,
                    'id_usuariomod'           => $id_usuario,
                ]);
        }
        $valor_copago  = $request['valor_copago'];
        $valor_copago2 = $request['valor_copago2'];
        if (!is_null($valor_copago) && $valor_copago > 0) {
            $oda = 1;
        } else {
            $oda = 0;
        }

        $orden    = Ct_Orden_Venta::findorfail($request->id_orden);
        $id_orden = $request->id_orden;

        if ($request['id_seguro'] == 4) {

            $xarray = [

                'id_empresa'          => $request['id_empresa'],
                'fecha_emision'       => $request['fecha'],
                'oda'                 => $oda,
                'numero_oda'          => $request['numero_oda'],
                'valor_oda'           => 0,
                'id_seguro'           => $request['id_seguro'],
                'id_nivel'            => $request['id_nivel'],
                'tipo_identificacion' => $request['tipo_identificacion'],
                'identificacion'      => $request['cedula'],
                'razon_social'        => $request['razon_social'],
                'ciudad'              => $request['ciudad'],
                'direccion'           => $request['direccion'],
                'telefono'            => $request['telefono'],
                'total_sin_tarjeta'   => $request['total_sin_tarjeta'],
                'observacion'         => $request['observacion'],
                'caja'                => $request['caja'],
                'total'               => $request['total_pro'],
                'email'               => $request['email'],
                'iva'                 => '0',
                'subtotal_0'          => $request['total_pro'],
                'subtotal_12'         => '0',
                'ip_modificacion'     => $ip_cliente,
                'id_usuariomod'       => $id_usuario,
            ];

            $orden->update($xarray);
        } else {

            $xarray = [

                'id_empresa'          => $request['id_empresa'],
                'fecha_emision'       => $request['fecha'],
                'oda'                 => $oda,
                'numero_oda'          => $request['numero_oda'],
                'valor_oda'           => $valor_copago,
                'id_seguro'           => $request['id_seguro'],
                'id_nivel'            => $request['id_nivel'],
                'tipo_identificacion' => $request['tipo_identificacion'],
                'identificacion'      => $request['cedula'],
                'razon_social'        => $request['razon_social'],
                'ciudad'              => $request['ciudad'],
                'direccion'           => $request['direccion'],
                'telefono'            => $request['telefono'],
                'total_sin_tarjeta'   => $request['total_sin_tarjeta'],
                'observacion'         => $request['observacion'],
                'caja'                => $request['caja'],
                'total'               => $request['pago'],
                'email'               => $request['email'],
                'iva'                 => '0',
                'subtotal_0'          => $request['pago'],
                'subtotal_12'         => '0',
                'ip_modificacion'     => $ip_cliente,
                'id_usuariomod'       => $id_usuario,
            ];

            $orden->update($xarray);
        }

        $valor_fi   = 0;
        $fi_adminis = 0;
        $consulta   = 0;
        $porcentaje = 0;

        //$venta_pago = Ct_Orden_Venta_Pago::where('id_orden',$id_orden)->get();
        $venta_pago = $orden->pagos;

        foreach ($venta_pago as $value) {
            $value->delete();
        }

        for ($i = 0; $i < $request['contador_pago']; $i++) {
            $fi_ad_p = 0;
            if ($request['visibilidad_pago' . $i] == 1) {
                if (isset($request['fi' . $i])) {
                    $fi_adminis = 1;
                    $fi_ad_p    = 1;
                    if ($request['id_tip_pago' . $i] == 4) {
                        $porcentaje = 0.07;
                    } elseif ($request['id_tip_pago' . $i] == 6) {
                        $porcentaje = 0.02;
                    }
                    $valor_fi = $valor_fi + ($request['total' . $i] - $request['valor_base' . $i]);
                }
                $consulta = $consulta + $request['valor_base' . $i];
                Ct_Orden_Venta_Pago::create([
                    'id_orden'        => $id_orden,
                    'fecha'           => $request['fecha' . $i],
                    'tipo'            => $request['id_tip_pago' . $i],
                    'tipo_tarjeta'    => $request['tipo_tarjeta' . $i],
                    'numero'          => $request['numero' . $i],
                    'banco'           => $request['id_banco' . $i],
                    'valor'           => $request['total' . $i],
                    'posee_fi'        => $fi_ad_p,
                    'p_fi'            => $porcentaje,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                ]);
            }
        }

        $detalles = $orden->detalles;

        foreach ($detalles as $value) {
            $value->delete();
        }
        if (isset($request['tipo_dato'])) {
            if ($request['tipo_dato'] == 1) {

                $total_oda       = 0;
                $total           = 0;
                $total_iva       = 0;
                $subtotal_0      = 0;
                $subtotal_12     = 0;
                $total_descuento = 0;
                $total_deducible = 0;

                if ($request['id_seguro'] == 4) {

                    for ($i = 0; $i < $request['contador_procedimiento']; $i++) {
                        if ($request['visibilidad_procedimiento' . $i] == 1) {

                            $tipo_dcto = $request['tipo_desc' . $i];
                            $tipo_oda  = $request['tipo_cob_seguro' . $i];
                            $valor_oda = $request['copago' . $i];
                            $p_oda     = $request['p_oda' . $i];
                            $descuento = $request['desc' . $i];
                            $p_dcto    = $request['p_dcto' . $i];
                            $valor_oda_hum = $request['val_deducible' . $i];

                            $valor_iva = ($request['iva_item' . $i] == 1) ? (($request['valor_pro' . $i] * $request['cantidad' . $i]) - $valor_oda - $descuento) * $request["ivareal"] : 0;
                            $iva       = ($request['iva_item' . $i] == 1) ? $request["ivareal"] : 0;
                            Ct_Orden_Venta_Detalle::create([
                                'id_orden'         => $id_orden,
                                'cod_prod'         => $request['cod_prod' . $i],
                                'descripcion'      => $request['procedimiento' . $i],
                                'id_producto'      => $request['id_prod' . $i],
                                'cantidad'         => $request['cantidad' . $i],
                                'precio'           => $request['valor_pro' . $i],
                                'total'            => $request['total_procedimiento' . $i],
                                'tipo_oda'         => $tipo_oda,
                                'p_oda'            => $p_oda,
                                'valor_oda'        => $valor_oda,
                                'tipo_dcto'        => $tipo_dcto,
                                'p_dcto'           => $p_dcto,
                                'descuento'        => $descuento,
                                'iva'              => $iva,
                                'valor_iva'        => $valor_iva,
                                'ident_paquete'    => $request['ident_paquete' . $i],
                                'valor_deducible'  => $request['deducible_id' . $i],
                                'valor_oda_hum'    => $valor_oda_hum,
                                'ip_creacion'      => $ip_cliente,
                                'ip_modificacion'  => $ip_cliente,
                                'id_usuariocrea'   => $id_usuario,
                                'id_usuariomod'    => $id_usuario,
                            ]);

                            $id_prd = $request['id_prod' . $i];
                            $act_prod = Ct_productos::where('id', $id_prd)->update(['ident_paquete' => $request['ident_paquete' . $i]]);

                            $total_oda += $valor_oda_hum;
                            $total_iva += $valor_iva;
                            if ($request['iva_item' . $i] == 1) {
                                $subtotal_12 += (($request['cantidad' . $i] * $request['valor_pro' . $i]) - $valor_oda);
                            } else {
                                $subtotal_0 += (($request['cantidad' . $i] * $request['valor_pro' . $i]) - $valor_oda);
                            }
                            $total_descuento += $descuento;

                            //Verificamos si el Id_producto existe en la Tabla ct_productos_paquete
                            $existe_prod_paquete = Ct_productos_paquete::where('ct_productos_paquete.id_producto', $request['id_prod' . $i])
                                ->join('ct_producto_tarifario_paquete as ptq', 'ptq.id_producto_paquete', 'ct_productos_paquete.id')
                                ->where('ct_productos_paquete.estado', '1')
                                ->where('ptq.id_seguro', $request['id_seguro'])
                                ->where('ptq.id_nivel', $request['id_nivel'])
                                ->where('ptq.estado', '1')
                                ->select('ct_productos_paquete.id as id_prod_paquete', 'ptq.id as id_prod_tar_paquete', 'ptq.precio as precio', 'ptq.id_seguro as id_seguro', 'ptq.id_nivel as id_nivel', 'ct_productos_paquete.id_producto as id_producto', 'ct_productos_paquete.nombre as nomb_paquete', 'ct_productos_paquete.iva as iva')
                                ->get();

                            if (!is_null($existe_prod_paquete)) {

                                foreach ($existe_prod_paquete as $value) {

                                    if ($value->iva == 1) {

                                        $nuev_iva = 0.12;

                                        $valor =  $value->precio / 1.12;

                                        $val_iva =  $valor * 0.12;
                                    } else {

                                        $nuev_iva = 0.00;

                                        $valor =  $value->precio;

                                        $val_iva =  0.00;
                                    }


                                    $input_orden_detall_paquete = [
                                        'id_orden'              => $id_orden,
                                        'id_producto'           => $value->id_producto,
                                        'descripcion'           => $value->nomb_paquete,
                                        'id_producto_paquete'   => $value->id_prod_paquete,
                                        'cantidad'              => 1,
                                        'precio'                => $valor,
                                        'iva'                   => $nuev_iva,
                                        'valor_iva'             => $val_iva,
                                        'id_usuariocrea'        => $id_usuario,
                                        'id_usuariomod'         => $id_usuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                    ];

                                    Ct_Orden_Venta_Detalle_Paquete::insert($input_orden_detall_paquete);
                                }
                            }


                            //Verificamos si el Id_producto existe en la Tabla ct_productos_paquete
                            /*$existe_prod_paquete = Ct_productos_paquete::where('ct_productos_paquete.id_producto',$request['id_prod' . $i])
                                                                        ->join('ct_producto_tarifario_paquete as ptq', 'ptq.id_producto_paquete', 'ct_productos_paquete.id')
                                                                        ->where('ct_productos_paquete.estado','1')
                                                                        ->select('ct_productos_paquete.id as id_prod_paquete','ptq.id as id_prod_tar_paquete')
                                                                        ->get();

                            if (!is_null($existe_prod_paquete)){

                                foreach ($existe_prod_paquete as $value){

                                    $input_orden_detall_paquete = [
                                        'id_orden'              => $id_orden,
                                        'id_producto_paquete'   => $value->id_prod_paquete,
                                        'id_prod_tarif_paquete' => $value->id_prod_tar_paquete,
                                        'id_usuariocrea'        => $id_usuario,
                                        'id_usuariomod'         => $id_usuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                    ];

                                    Ct_Orden_Venta_Detalle_Paquete::insert($input_orden_detall_paquete);
                                
                                }

                            }*/
                        }
                    }
                    $total    = $subtotal_0 + $subtotal_12 + $total_iva - $total_descuento;
                    $cabecera = Ct_Orden_Venta::find($id_orden);
                    $arr_cab  = [
                        'valor_oda'   => $total_oda,
                        'total'       => $request['total_pro'],
                        'iva'         => $total_iva,
                        'subtotal_0'  => $request['total_pro'],
                        'subtotal_12' => $subtotal_12,
                        'descuento'   => $total_descuento,
                    ];
                    $cabecera->update($arr_cab);
                } else {

                    for ($i = 0; $i < $request['contador_procedimiento']; $i++) {
                        if ($request['visibilidad_procedimiento' . $i] == 1) {

                            $tipo_dcto = $request['tipo_desc' . $i];
                            $tipo_oda  = $request['tipo_cob_seguro' . $i];
                            $valor_oda = $request['copago' . $i];
                            $p_oda     = $request['p_oda' . $i];
                            $descuento = $request['desc' . $i];
                            $p_dcto    = $request['p_dcto' . $i];


                            $valor_iva = ($request['iva_item' . $i] == 1) ? (($request['valor_pro' . $i] * $request['cantidad' . $i]) - $valor_oda - $descuento) * $request["ivareal"] : 0;
                            $iva       = ($request['iva_item' . $i] == 1) ? $request["ivareal"] : 0;
                            Ct_Orden_Venta_Detalle::create([
                                'id_orden'         => $id_orden,
                                'cod_prod'          => $request['cod_prod' . $i],
                                'descripcion'      => $request['procedimiento' . $i],
                                'id_producto'      => $request['id_prod' . $i],
                                'cantidad'         => $request['cantidad' . $i],
                                'precio'           => $request['valor_pro' . $i],
                                'total'            => $request['total_procedimiento' . $i],
                                'tipo_oda'         => $tipo_oda,
                                'p_oda'            => $p_oda,
                                'valor_oda'        => $valor_oda,
                                'tipo_dcto'        => $tipo_dcto,
                                'p_dcto'           => $p_dcto,
                                'descuento'        => $descuento,
                                'iva'              => $iva,
                                'valor_iva'        => $valor_iva,
                                'ident_paquete'    => $request['ident_paquete' . $i],
                                'valor_deducible'  => $request['deducible_id' . $i],
                                //'valor_oda_hum'    => $valor_oda_hum,
                                'ip_creacion'      => $ip_cliente,
                                'ip_modificacion'  => $ip_cliente,
                                'id_usuariocrea'   => $id_usuario,
                                'id_usuariomod'    => $id_usuario,
                            ]);

                            $id_prd = $request['id_prod' . $i];
                            $act_prod = Ct_productos::where('id', $id_prd)->update(['ident_paquete' => $request['ident_paquete' . $i]]);

                            $total_oda += $valor_oda - ($request['deducible_id' . $i]);
                            $total_iva += $valor_iva;
                            if ($request['iva_item' . $i] == 1) {
                                $subtotal_12 += (($request['cantidad' . $i] * $request['valor_pro' . $i]) - $valor_oda);
                            } else {
                                $subtotal_0 += (($request['cantidad' . $i] * $request['valor_pro' . $i]) - $valor_oda);
                            }
                            $total_descuento += $descuento;



                            //Cuando el Seguro es de Tipo Diferente al Particular y Humana 
                            if ($request['id_seguro'] != 1) {

                                $existe_prod_paquete = Ct_productos_paquete::where('ct_productos_paquete.id_producto', $request['id_prod' . $i])
                                    ->join('ct_producto_tarifario_paquete as ptq', 'ptq.id_producto_paquete', 'ct_productos_paquete.id')
                                    ->where('ct_productos_paquete.estado', '1')
                                    ->where('ptq.id_seguro', $request['id_seguro'])
                                    ->where('ptq.id_nivel', $request['id_nivel'])
                                    ->where('ptq.estado', '1')
                                    ->select('ct_productos_paquete.id as id_prod_paquete', 'ptq.id as id_prod_tar_paquete', 'ptq.precio as precio', 'ptq.id_seguro as id_seguro', 'ptq.id_nivel as id_nivel', 'ct_productos_paquete.id_producto as id_producto', 'ct_productos_paquete.nombre as nomb_paquete', 'ct_productos_paquete.iva as iva')
                                    ->get();

                                if (!is_null($existe_prod_paquete)) {

                                    foreach ($existe_prod_paquete as $value) {

                                        if ($value->iva == 1) {

                                            $nuev_iva = 0.12;

                                            $valor =  $value->precio / 1.12;

                                            $val_iva =  $valor * 0.12;
                                        } else {

                                            $nuev_iva = 0.00;

                                            $valor =  $value->precio;

                                            $val_iva =  0.00;
                                        }

                                        $input_orden_detall_paquete = [
                                            'id_orden'              => $id_orden,
                                            'id_producto'           => $value->id_producto,
                                            'descripcion'           => $value->nomb_paquete,
                                            'id_producto_paquete'   => $value->id_prod_paquete,
                                            'cantidad'              => 1,
                                            'precio'                => $valor,
                                            'iva'                   => $nuev_iva,
                                            'valor_iva'             => $val_iva,
                                            'id_usuariocrea'        => $id_usuario,
                                            'id_usuariomod'         => $id_usuario,
                                            'ip_creacion'           => $ip_cliente,
                                            'ip_modificacion'       => $ip_cliente,
                                        ];

                                        Ct_Orden_Venta_Detalle_Paquete::insert($input_orden_detall_paquete);
                                    }
                                }
                            }

                            if ($request['id_seguro'] == 1) {

                                $existe_prod_paquete = Ct_productos_paquete::where('ct_productos_paquete.id_producto', $request['id_prod' . $i])
                                    ->where('ct_productos_paquete.estado', '1')
                                    ->select('ct_productos_paquete.id as id_prod_paquete', 'ct_productos_paquete.id_producto as id_producto', 'ct_productos_paquete.nombre as nomb_paquete', 'ct_productos_paquete.precio as precio_pvp', 'ct_productos_paquete.iva as iva', 'ct_productos_paquete.cantidad as cantidad')
                                    ->get();

                                if (!is_null($existe_prod_paquete)) {

                                    foreach ($existe_prod_paquete as $value) {

                                        if ($value->iva == 1) {

                                            $nuev_iva = 0.12;
                                        } else {

                                            $nuev_iva = 0.00;
                                        }

                                        $input_orden_detall_paquete = [
                                            'id_orden'              => $id_orden,
                                            'id_producto'           => $value->id_producto,
                                            'descripcion'           => $value->nomb_paquete,
                                            'id_producto_paquete'   => $value->id_prod_paquete,
                                            'cantidad'              => $value->cantidad,
                                            'precio'                => $value->precio_pvp,
                                            'iva'                   => $nuev_iva,
                                            'valor_iva'             => ($value->cantidad * $value->precio_pvp) * $nuev_iva,
                                            'id_usuariocrea'        => $id_usuario,
                                            'id_usuariomod'         => $id_usuario,
                                            'ip_creacion'           => $ip_cliente,
                                            'ip_modificacion'       => $ip_cliente,
                                        ];

                                        Ct_Orden_Venta_Detalle_Paquete::insert($input_orden_detall_paquete);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            Ct_Orden_Venta_Detalle::create([
                'id_orden'        => $id_orden,
                'descripcion'     => 'Consulta Medica Especializada',
                'cantidad'        => 1,
                'precio'          => $consulta,
                'total'           => $consulta,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
            ]);
            if ($fi_adminis == 1) {
                Ct_Orden_Venta_Detalle::create([
                    'id_orden'        => $id_orden,
                    'descripcion'     => 'FEE Administrativo',
                    'cantidad'        => 1,
                    'precio'          => $valor_fi,
                    'total'           => $valor_fi,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                ]);
            }
        }

        $id_orde = Ct_Orden_Venta::where('id', $id_orden)->first();
        $suma_proced = round($id_orde->detalles->sum('total'), 2);
        $suma_form_pag = round($id_orde->pagos->sum('valor'), 2);
        $resta = $suma_proced - $suma_form_pag;

        if ($resta == 0) {

            $val_dif = 0;
            return ['val_dif' => $val_dif, 'id_orden' => $id_orden];
        } else {

            $val_dif = 1;
            $valor = 1;
            return ['val_dif' => $val_dif, 'id_orden' => $id_orden, 'valor' => $valor];
        }

        //return $id_orden;

    }

    //Obtener Niveles por Seguro y Empresa
    public function  obtener_nivel_seguro(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request['id_empresa'];

        $id_seg = $request['id_seguro'];


        if (!is_null($id_seg)) {

            $convenios = DB::table('convenio as c')
                ->where('c.id_seguro', $id_seg)
                ->join('nivel as n', 'n.id', 'c.id_nivel')
                ->select('c.*', 'n.nombre', 'n.id as id_nivel')
                ->get();



            return $convenios;
        }

        return 'no';
    }

    //Obtener Productos por Nombre y Empresa
    public function buscar_producto_paquete(Request $request)
    {

        $nombre    = $request['term'];
        $identificador   = $request['posicion'];
        $id_empresa = $request['id_empresa'];
        $id_seguro = $request['id_seguro'];
        $id_nivel = $request['id_nivel'];

        $data      = array();
        $productos = DB::table('ct_productos')
            ->where('id_empresa', $id_empresa)
            ->where('estado_tabla', '1')
            ->where('nombre', 'like', '%' . $nombre . '%')->get();

        foreach ($productos as $product) {
            $data[] = array('id_producto' => $product->id, 'value' => $product->nombre, 'posicion' => $identificador, 'id_seguro' => $id_seguro, 'id_nivel' => $id_nivel, 'id_empresa' => $id_empresa);
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    //Obtener Precio Producto Tarifario por Nombre
    public function obtener_precio_prod_tarifario_nombre(Request $request)
    {
        $id_producto = $request['id_prod'];
        $id_empresa = $request['id_emp'];
        $id_seguro = $request['id_seg'];
        $id_nivel = $request['id_niv'];
        $identificador = $request['posicion'];
        $ct_prod_tarifario = null;


        //Verificamos que el Producto Sea un Paquete
        $inf_prod = Ct_productos::where('id', $id_producto)->first();


        if (($inf_prod->ident_paquete == 1) && ($id_seguro != 1)) {


            $ct_prod_tarifario = Ct_Productos_Tarifario::where('id_seguro', $id_seguro)
                ->where('nivel', $id_nivel)
                ->where('id_producto', $id_producto)
                ->where('estado', '1')
                ->first();
        }


        if (!is_null($ct_prod_tarifario)) {

            return ['id_producto' => $ct_prod_tarifario->id_producto, 'precio_prod' => $ct_prod_tarifario->precio_producto, 'posic' => $identificador, 'cod_prod' => $inf_prod->codigo];
        } else {


            if (($id_seguro == 1) && ($inf_prod->ident_paquete == 1)) {


                $obt_prec_part = Ct_productos::where('id', $id_producto)->where('estado_tabla', 1)->first();


                return ['id_producto' => $obt_prec_part->id, 'precio_prod' => $obt_prec_part->valor_total_paq, 'posic' => $identificador, 'cod_prod' => $obt_prec_part->codigo, 'nomb_prod' => $obt_prec_part->nombre];
            } else {

                $cod_prod = Ct_productos::where('id', $id_producto)->where('estado_tabla', 1)->first();

                $precio_prod = PrecioProducto::where('codigo_producto', $cod_prod->codigo)
                    ->where('nivel', '1')
                    ->where('estado', '1')
                    ->first();

                if (!is_null($precio_prod)) {

                    return ['id_producto' => $cod_prod->id, 'precio_prod' => $precio_prod->precio, 'posic' => $identificador, 'cod_prod' => $cod_prod->codigo];
                }
            }
        }

        //return ['no_data' => 'no'] ;

    }

    //Obtener Precio Producto Tarifario por Codigo
    public function obtener_precio_prod_tarifario_codigo(Request $request)
    {
        $id_producto = $request['id_prod'];
        $id_empresa = $request['id_emp'];
        $id_seguro = $request['id_seg'];
        $id_nivel = $request['id_niv'];
        $identificador = $request['posicion'];
        $ct_prod_tarifario = null;


        //Verificamos que el Producto Sea un Paquete
        $inf_prod = Ct_productos::where('codigo', $id_producto)->first();

        if(is_null($inf_prod)){
            return response()->json(['error'=>$id_producto]);
        }
        if (($inf_prod->ident_paquete == 1) && ($id_seguro != 1)) {

            $ct_prod_tarifario = Ct_Productos_Tarifario::where('id_seguro', $id_seguro)
                ->where('nivel', $id_nivel)
                ->where('id_producto', $id_producto)
                ->where('estado', '1')->first();
        }

        if (!is_null($ct_prod_tarifario)) {

            return ['id_producto' => $ct_prod_tarifario->id_producto, 'precio_prod' => $ct_prod_tarifario->precio_producto, 'posic' => $identificador, 'cod_prod' => $inf_prod->codigo, 'nomb_prod' => $inf_prod->nombre];
        } else {

            if (($id_seguro == 1) && ($inf_prod->ident_paquete == 1)) {


                $obt_prec_part = Ct_productos::where('id', $id_producto)->where('estado_tabla', 1)->first();


                return ['id_producto' => $obt_prec_part->id, 'precio_prod' => $obt_prec_part->valor_total_paq, 'posic' => $identificador, 'cod_prod' => $obt_prec_part->codigo, 'nomb_prod' => $obt_prec_part->nombre];
            } else {

                $cod_prod = Ct_productos::where('codigo', $id_producto)->where('estado_tabla', 1)->first();

                $precio_prod = PrecioProducto::where('codigo_producto', $cod_prod->codigo)
                    ->where('nivel', '1')
                    ->where('estado', '1')
                    ->first();

                if (!is_null($precio_prod)) {

                    return ['id_producto' => $cod_prod->id, 'precio_prod' => $precio_prod->precio, 'posic' => $identificador, 'cod_prod' => $cod_prod->codigo, 'nomb_prod' => $cod_prod->nombre];
                }
            }
        }

        //return ['no_data' => 'no'] ;

    }

    //Modal Orden Venta Detalle Paquete
    public function obtener_orden_detalle_paquete($id)
    {
        //Obtenemos el Id Usuario Logueado
        $id_usuario = Auth::user()->id;

        //Consultamos la Tabla CT_VENTA
        $fact_venta = Ct_ventas::findorfail($id);

        if ($fact_venta != null) {

            $orden = Ct_Orden_Venta::find($fact_venta->orden_venta);
            $detalles = $orden->detalles;
        }

        return view('contable/facturacion/modal_orden_detalle_paquete', ['id' => $id, 'detalles' => $detalles, 'orden' => $orden, 'id_usuario' => $id_usuario]);
    }

    //Obtener Productos por Nombre y Empresa
    public function buscar_codigo_paquete(Request $request)
    {

        //return $request['term'];

        $codigo_prod  = $request['term'];
        $identificador   = $request['posicion'];
        $id_empresa = $request['id_empresa'];
        $id_seguro = $request['id_seguro'];
        $id_nivel = $request['id_nivel'];

        $data = array();
        $productos = DB::table('ct_productos')
            ->where('id_empresa', $id_empresa)
            ->where('estado_tabla', '1')
            ->where('codigo', 'like', '%' . $codigo_prod . '%')->get();


        //dd($productos);

        foreach ($productos as $product) {
            $data[] = array('id_producto' => $product->id, 'value' => $product->codigo, 'prod_nombre' => $product->nombre, 'posicion' => $identificador, 'id_seguro' => $id_seguro, 'id_nivel' => $id_nivel, 'id_empresa' => $id_empresa);
        }

        //return $data;

        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    //Obtener precio producto por codigo
    public function obtener_precio_por_codigo(Request $request)
    {

        $cod_prod = $request['cod_prod'];
        $pos = $request['posicion'];

        $ct_prod_precio = PrecioProducto::where('codigo_producto', $cod_prod)
            ->where('estado', '1')->first();

        if (!is_null($ct_prod_precio)) {

            return ['posic' => $pos, 'prec_prod' => $ct_prod_precio->precio];
        }

        return ['no_data' => 'no'];
    }

    //Obtener precio producto por codigo
    public function obtener_precio(Request $request)
    {

        $cod_prod = $request['cod_prod'];

        $ct_prod_precio = PrecioProducto::where('codigo_producto', $cod_prod)
            ->where('nivel', '1')
            ->where('estado', '1')->first();

        if (!is_null($ct_prod_precio)) {

            return ['prec_prod' => $ct_prod_precio->precio];
        }

        return ['no_data' => 'no'];
    }


    /************************************************************
     *************IMPRIMIR DETALLE FACTURA VENTA PAQUETE*********
    /************************************************************/
    public function imprimir_comprobante_detalle_paquete($id_venta)
    {


        //dd($id_venta);

        $fact_venta = Ct_ventas::findorfail($id_venta);

        if ($fact_venta != null) {

            $orden = Ct_Orden_Venta::find($fact_venta->orden_venta);
            $detalles = $orden->detalles;

            //$deta_vent = Ct_detalle_venta::where('id_ct_ventas',$id_venta)->get();

        }

        $vistaurl = "contable.ventas.pdf_comprobante_detalle_paquete";
        $view     = \View::make($vistaurl, compact('fact_venta', 'detalles', 'orden'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id_venta . '.pdf');
    }


    //Verifica si el Producto es un Paquete
    public function verifica_producto_paquete(Request $request)
    {

        $id_producto = $request['id_prod'];
        $identificador = $request['posic'];

        $exist_prod_paq = Ct_productos_paquete::where('ct_productos_paquete.id_producto', $id_producto)
            ->where('ct_productos_paquete.estado', '1')
            ->get();

        if (count($exist_prod_paq) > 0) {

            $existe = 1;

            return ['existe_paq' => $existe, 'posic' => $identificador];
        }

        return ['no_data' => 'no'];
    }

    public function store(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;

        $ct_cliente = ct_clientes::where('identificacion', $request['cedula'])->first();
        if (is_null($ct_cliente)) {
            ct_clientes::create([
                'identificacion'          => $request['cedula'],
                'nombre'                  => $request['razon_social'],
                'tipo'                    => $request['tipo_identificacion'],
                'clase'                   => 'normal',
                'estado'                  => '1',
                'ciudad_representante'    => $request['ciudad'],
                'direccion_representante' => $request['direccion'],
                'telefono1_representante' => $request['telefono'],
                'email_representante'     => $request['email'],
                'pais'                    => 'Ecuador',
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
                'id_usuariocrea'          => $id_usuario,
                'id_usuariomod'           => $id_usuario,
            ]);
            $ct_cliente = ct_clientes::where('identificacion', $request['cedula'])->first();
        } else {
            ct_clientes::where('identificacion', $request['cedula'])
                ->update([
                    'nombre'                  => $request['razon_social'],
                    'tipo'                    => $request['tipo_identificacion'],
                    'ciudad_representante'    => $request['ciudad'],
                    'direccion_representante' => $request['direccion'],
                    'email_representante'     => $request['email'],
                    'telefono1_representante' => $request['telefono'],
                    'ip_modificacion'         => $ip_cliente,
                    'id_usuariocrea'          => $id_usuario,
                ]);
        }
        $valor_copago  = $request['valor_copago'];
        $valor_copago2 = $request['valor_copago2'];
        if (!is_null($valor_copago) && $valor_copago > 0) {
            $oda = 1;
        } else {
            $oda = 0;
        }
        /*if (!is_null($valor_copago2) && $valor_copago2 > 0) {
        $oda = 1;
        } else {
        $oda = 0;

        }*/

        if ($request['id_seguro'] == 4) {

            $id_orden = Ct_Orden_Venta::insertGetId([
                'id_agenda'           => $request['id_agenda'],
                'id_empresa'          => $request['id_empresa'],
                'fecha_emision'       => $request['fecha'],
                'oda'                 => $oda,
                'numero_oda'          => $request['numero_oda'],
                'valor_oda'           => 0,
                'id_seguro'           => $request['id_seguro'],
                'id_nivel'            => $request['id_nivel'],
                'tipo_identificacion' => $request['tipo_identificacion'],
                'identificacion'      => $request['cedula'],
                'razon_social'        => $request['razon_social'],
                'ciudad'              => $request['ciudad'],
                'direccion'           => $request['direccion'],
                'telefono'            => $request['telefono'],
                'total_sin_tarjeta'   => $request['total_sin_tarjeta'],
                'observacion'         => $request['observacion'],
                'caja'                => $request['caja'],
                'total'               => $request['total_pro'],
                'email'               => $request['email'],
                'iva'                 => '0',
                'subtotal_0'          => $request['total_pro'],
                'subtotal_12'         => '0',
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $id_usuario,
                'id_usuariomod'       => $id_usuario,
            ]);
        } else {

            $id_orden = Ct_Orden_Venta::insertGetId([
                'id_agenda'           => $request['id_agenda'],
                'id_empresa'          => $request['id_empresa'],
                'fecha_emision'       => $request['fecha'],
                'oda'                 => $oda,
                'numero_oda'          => $request['numero_oda'],
                'valor_oda'           => $valor_copago,
                'id_seguro'           => $request['id_seguro'],
                'id_nivel'            => $request['id_nivel'],
                'tipo_identificacion' => $request['tipo_identificacion'],
                'identificacion'      => $request['cedula'],
                'razon_social'        => $request['razon_social'],
                'ciudad'              => $request['ciudad'],
                'direccion'           => $request['direccion'],
                'telefono'            => $request['telefono'],
                'total_sin_tarjeta'   => $request['total_sin_tarjeta'],
                'observacion'         => $request['observacion'],
                'caja'                => $request['caja'],
                'total'               => $request['pago'],
                'email'               => $request['email'],
                'iva'                 => '0',
                'subtotal_0'          => $request['pago'],
                'subtotal_12'         => '0',
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $id_usuario,
                'id_usuariomod'       => $id_usuario,
            ]);
        }

        $valor_fi   = 0;
        $fi_adminis = 0;
        $consulta   = 0;
        $porcentaje = 0;

        for ($i = 0; $i < $request['contador_pago']; $i++) {
            $fi_ad_p = 0;
            if ($request['visibilidad_pago' . $i] == 1) {
                if (isset($request['fi' . $i])) {
                    $fi_adminis = 1;
                    $fi_ad_p    = 1;
                    if ($request['id_tip_pago' . $i] == 4) {
                        $porcentaje = 0.07;
                    } elseif ($request['id_tip_pago' . $i] == 6) {
                        $porcentaje = 0.02;
                    }
                    $valor_fi = $valor_fi + ($request['total' . $i] - $request['valor_base' . $i]);
                }
                $consulta = $consulta + $request['valor_base' . $i];
                Ct_Orden_Venta_Pago::create([
                    'id_orden'        => $id_orden,
                    'fecha'           => $request['fecha' . $i],
                    'tipo'            => $request['id_tip_pago' . $i],
                    'tipo_tarjeta'    => $request['tipo_tarjeta' . $i],
                    'numero'          => $request['numero' . $i],
                    'banco'           => $request['id_banco' . $i],
                    'valor'           => $request['total' . $i],
                    'posee_fi'        => $fi_ad_p,
                    'p_fi'            => $porcentaje,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                ]);
            }
        }
        //return $request->all();
        if ($request['tipo_dato'] == 1) {
            $total_oda       = 0;
            $total           = 0;
            $total_iva       = 0;
            $subtotal_0      = 0;
            $subtotal_12     = 0;
            $total_descuento = 0;
            $total_deducible = 0;

            if ($request['id_seguro'] == 4) {

                for ($i = 0; $i < $request['contador_procedimiento']; $i++) {
                    if ($request['visibilidad_procedimiento' . $i] == 1) {

                        $tipo_dcto = $request['tipo_desc' . $i];
                        $tipo_oda  = $request['tipo_cob_seguro' . $i];
                        $valor_oda = $request['copago' . $i];
                        $p_oda     = $request['p_oda' . $i];
                        $descuento = $request['desc' . $i];
                        $p_dcto    = $request['p_dcto' . $i];
                        $valor_oda_hum = $request['val_deducible' . $i];

                        $valor_iva = ($request['iva_item' . $i] == 1) ? (($request['valor_pro' . $i] * $request['cantidad' . $i]) - $valor_oda - $descuento) * $request["ivareal"] : 0;
                        $iva       = ($request['iva_item' . $i] == 1) ? $request["ivareal"] : 0;

                        Ct_Orden_Venta_Detalle::create([
                            'id_orden'          => $id_orden,
                            'cod_prod'          => $request['cod_prod' . $i],
                            'descripcion'       => $request['procedimiento' . $i],
                            'id_producto'       => $request['id_prod' . $i],
                            'cantidad'          => $request['cantidad' . $i],
                            'precio'            => $request['valor_pro' . $i],
                            'total'             => $request['total_procedimiento' . $i],
                            'tipo_oda'          => $tipo_oda,
                            'p_oda'             => $p_oda,
                            'valor_oda'         => $valor_oda,
                            'tipo_dcto'         => $tipo_dcto,
                            'p_dcto'            => $p_dcto,
                            'descuento'         => $descuento,
                            'iva'               => $iva,
                            'valor_iva'         => $valor_iva,
                            'ident_paquete'     => $request['ident_paquete' . $i],
                            'valor_deducible'   => $request['deducible_id' . $i],
                            'valor_oda_hum'     => $valor_oda_hum,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                            'id_usuariocrea'    => $id_usuario,
                            'id_usuariomod'     => $id_usuario,
                        ]);

                        //Identificamos al Producto como un paquete
                        /*$id_prd = $request['id_prod' . $i];
                        $act_prod = Ct_productos::find($id_prd);
                        
                        $arr_idp = [
                            'ident_paquete' => $request['ident_paquete' . $i],
                        ];
                        $act_prod->update($arr_idp);*/
                        $id_prd = $request['id_prod' . $i];
                        $act_prod = Ct_productos::where('id', $id_prd)->update(['ident_paquete' => $request['ident_paquete' . $i]]);

                        //$total_oda += $valor_oda;
                        $total_oda += $valor_oda_hum;
                        $total_iva += $valor_iva;
                        if ($request['iva_item' . $i] == 1) {
                            $subtotal_12 += (($request['cantidad' . $i] * $request['valor_pro' . $i]) - $valor_oda);
                        } else {
                            $subtotal_0 += (($request['cantidad' . $i] * $request['valor_pro' . $i]) - $valor_oda);
                        }
                        $total_descuento += $descuento;

                        //Verificamos si el Id_producto existe en la Tabla ct_productos_paquete
                        $existe_prod_paquete = Ct_productos_paquete::where('ct_productos_paquete.id_producto', $request['id_prod' . $i])
                            ->join('ct_producto_tarifario_paquete as ptq', 'ptq.id_producto_paquete', 'ct_productos_paquete.id')
                            ->where('ct_productos_paquete.estado', '1')
                            ->where('ptq.id_seguro', $request['id_seguro'])
                            ->where('ptq.id_nivel', $request['id_nivel'])
                            ->where('ptq.estado', '1')
                            ->select('ct_productos_paquete.id as id_prod_paquete', 'ptq.id as id_prod_tar_paquete', 'ptq.precio as precio', 'ptq.id_seguro as id_seguro', 'ptq.id_nivel as id_nivel', 'ct_productos_paquete.id_producto as id_producto', 'ct_productos_paquete.nombre as nomb_paquete', 'ct_productos_paquete.iva as iva')
                            ->get();

                        if (!is_null($existe_prod_paquete)) {

                            foreach ($existe_prod_paquete as $value) {

                                if ($value->iva == 1) {

                                    $nuev_iva = 0.12;

                                    $valor =  $value->precio / 1.12;

                                    $val_iva =  $valor * 0.12;
                                } else {

                                    $nuev_iva = 0.00;

                                    $valor =  $value->precio;

                                    $val_iva =  0.00;
                                }


                                $input_orden_detall_paquete = [
                                    'id_orden'              => $id_orden,
                                    'id_producto'           => $value->id_producto,
                                    'descripcion'           => $value->nomb_paquete,
                                    'id_producto_paquete'   => $value->id_prod_paquete,
                                    'cantidad'              => 1,
                                    'precio'                => $valor,
                                    'iva'                   => $nuev_iva,
                                    'valor_iva'             => $val_iva,
                                    //'valor_iva'             => $value->precio,
                                    //'valor_iva'             => 0.00,
                                    'id_usuariocrea'        => $id_usuario,
                                    'id_usuariomod'         => $id_usuario,
                                    'ip_creacion'           => $ip_cliente,
                                    'ip_modificacion'       => $ip_cliente,
                                ];

                                Ct_Orden_Venta_Detalle_Paquete::insert($input_orden_detall_paquete);
                            }
                        }
                    }
                }

                $total    = $subtotal_0 + $subtotal_12 + $total_iva - $total_descuento;
                $cabecera = Ct_Orden_Venta::find($id_orden);
                $arr_cab  = [
                    'valor_oda'   => $total_oda,
                    'total'       => $request['total_pro'],
                    'iva'         => $total_iva,
                    'subtotal_0'  => $request['total_pro'],
                    'subtotal_12' => $subtotal_12,
                    'descuento'   => $total_descuento,
                ];
                $cabecera->update($arr_cab);
            } else {

                for ($i = 0; $i < $request['contador_procedimiento']; $i++) {
                    if ($request['visibilidad_procedimiento' . $i] == 1) {

                        $tipo_dcto = $request['tipo_desc' . $i];
                        $tipo_oda  = $request['tipo_cob_seguro' . $i];
                        $valor_oda = $request['copago' . $i];
                        $p_oda     = $request['p_oda' . $i];
                        $descuento = $request['desc' . $i];
                        $p_dcto    = $request['p_dcto' . $i];

                        $valor_iva = ($request['iva_item' . $i] == 1) ? (($request['valor_pro' . $i] * $request['cantidad' . $i]) - $valor_oda - $descuento) * $request["ivareal"] : 0;
                        $iva       = ($request['iva_item' . $i] == 1) ? $request["ivareal"] : 0;
                        Ct_Orden_Venta_Detalle::create([
                            'id_orden'          => $id_orden,
                            'cod_prod'          => $request['cod_prod' . $i],
                            'descripcion'       => $request['procedimiento' . $i],
                            'id_producto'       => $request['id_prod' . $i],
                            'cantidad'          => $request['cantidad' . $i],
                            'precio'            => $request['valor_pro' . $i],
                            'total'             => $request['total_procedimiento' . $i],
                            'tipo_oda'          => $tipo_oda,
                            'p_oda'             => $p_oda,
                            'valor_oda'         => $valor_oda,
                            'tipo_dcto'         => $tipo_dcto,
                            'p_dcto'            => $p_dcto,
                            'descuento'         => $descuento,
                            'iva'               => $iva,
                            'valor_iva'         => $valor_iva,
                            'ident_paquete'     => $request['ident_paquete' . $i],
                            'valor_deducible'   => $request['deducible_id' . $i],
                            //'valor_oda_hum'     => $request['val_deducible' . $i],
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                            'id_usuariocrea'    => $id_usuario,
                            'id_usuariomod'     => $id_usuario,
                        ]);
                        //$total_oda += $valor_oda;

                        //Identificamos al Producto como un paquete
                        /*$id_prd = $request['id_prod' . $i];
                         $act_prod = Ct_productos::find($id_prd);
                         
                         $arr_idp = [
                             'ident_paquete' => $request['ident_paquete' . $i],
                         ];
                         $act_prod->update($arr_idp);*/

                        $id_prd = $request['id_prod' . $i];
                        $act_prod = Ct_productos::where('id', $id_prd)->update(['ident_paquete' => $request['ident_paquete' . $i]]);


                        $total_oda += $valor_oda - ($request['deducible_id' . $i]);
                        $total_iva += $valor_iva;
                        if ($request['iva_item' . $i] == 1) {
                            $subtotal_12 += (($request['cantidad' . $i] * $request['valor_pro' . $i]) - $valor_oda);
                        } else {
                            $subtotal_0 += (($request['cantidad' . $i] * $request['valor_pro' . $i]) - $valor_oda);
                        }
                        $total_descuento += $descuento;

                        //Cuando el Seguro es de Tipo Diferente al Particular y Humana 
                        if ($request['id_seguro'] != 1) {

                            //Verificamos si el Id_producto existe en la Tabla ct_productos_paquete
                            $existe_prod_paquete = Ct_productos_paquete::where('ct_productos_paquete.id_producto', $request['id_prod' . $i])
                                ->join('ct_producto_tarifario_paquete as ptq', 'ptq.id_producto_paquete', 'ct_productos_paquete.id')
                                ->where('ct_productos_paquete.estado', '1')
                                ->where('ptq.id_seguro', $request['id_seguro'])
                                ->where('ptq.id_nivel', $request['id_nivel'])
                                ->where('ptq.estado', '1')
                                ->select('ct_productos_paquete.id as id_prod_paquete', 'ptq.id as id_prod_tar_paquete', 'ptq.precio as precio', 'ptq.id_seguro as id_seguro', 'ptq.id_nivel as id_nivel', 'ct_productos_paquete.id_producto as id_producto', 'ct_productos_paquete.nombre as nomb_paquete', 'ct_productos_paquete.iva as iva')
                                ->get();

                            if (!is_null($existe_prod_paquete)) {

                                foreach ($existe_prod_paquete as $value) {

                                    if ($value->iva == 1) {

                                        $nuev_iva = 0.12;

                                        $valor =  $value->precio / 1.12;

                                        $val_iva =  $valor * 0.12;
                                    } else {

                                        $nuev_iva = 0.00;

                                        $valor =  $value->precio;

                                        $val_iva =  0.00;
                                    }

                                    $input_orden_detall_paquete = [
                                        'id_orden'              => $id_orden,
                                        'id_producto'           => $value->id_producto,
                                        'descripcion'           => $value->nomb_paquete,
                                        'id_producto_paquete'   => $value->id_prod_paquete,
                                        'cantidad'              => 1,
                                        'precio'                => $valor,
                                        'iva'                   => $nuev_iva,
                                        'valor_iva'             => $val_iva,
                                        //'valor_iva'             => $value->precio,
                                        //'valor_iva'             => 0.00,
                                        'id_usuariocrea'        => $id_usuario,
                                        'id_usuariomod'         => $id_usuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                    ];

                                    Ct_Orden_Venta_Detalle_Paquete::insert($input_orden_detall_paquete);
                                }
                            }
                        }

                        if ($request['id_seguro'] == 1) {

                            $existe_prod_paquete = Ct_productos_paquete::where('ct_productos_paquete.id_producto', $request['id_prod' . $i])
                                ->where('ct_productos_paquete.estado', '1')
                                ->select('ct_productos_paquete.id as id_prod_paquete', 'ct_productos_paquete.id_producto as id_producto', 'ct_productos_paquete.nombre as nomb_paquete', 'ct_productos_paquete.precio as precio_pvp', 'ct_productos_paquete.iva as iva', 'ct_productos_paquete.cantidad as cantidad')
                                ->get();

                            if (!is_null($existe_prod_paquete)) {

                                foreach ($existe_prod_paquete as $value) {

                                    if ($value->iva == 1) {

                                        $nuev_iva = 0.12;
                                    } else {

                                        $nuev_iva = 0.00;
                                    }

                                    $input_orden_detall_paquete = [
                                        'id_orden'              => $id_orden,
                                        'id_producto'           => $value->id_producto,
                                        'descripcion'           => $value->nomb_paquete,
                                        'id_producto_paquete'   => $value->id_prod_paquete,
                                        'cantidad'              => $value->cantidad,
                                        'precio'                => $value->precio_pvp,
                                        'iva'                   => $nuev_iva,
                                        'valor_iva'             => ($value->cantidad * $value->precio_pvp) * $nuev_iva,
                                        'id_usuariocrea'        => $id_usuario,
                                        'id_usuariomod'         => $id_usuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                    ];

                                    Ct_Orden_Venta_Detalle_Paquete::insert($input_orden_detall_paquete);
                                }
                            }
                        }
                        //Total Deducible
                        /*if($request['deducible_id' . $i] > 0){
                            $total_deducible += ($request['deducible_id' . $i]);
                        }*/
                    }
                }
                $total    = $subtotal_0 + $subtotal_12 + $total_iva - $total_descuento;
                $cabecera = Ct_Orden_Venta::find($id_orden);
                $arr_cab  = [
                    'valor_oda'   => $total_oda,
                    'total'       => $total,
                    'iva'         => $total_iva,
                    'subtotal_0'  => $subtotal_0,
                    'subtotal_12' => $subtotal_12,
                    'descuento'   => $total_descuento,
                ];
                $cabecera->update($arr_cab);
            }
        } else {
            Ct_Orden_Venta_Detalle::create([
                'id_orden'        => $id_orden,
                'descripcion'     => 'Consulta Medica Especializada',
                'cantidad'        => 1,
                'precio'          => $consulta,
                'total'           => $consulta,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
            ]);
            if ($fi_adminis == 1) {
                Ct_Orden_Venta_Detalle::create([
                    'id_orden'        => $id_orden,
                    'descripcion'     => 'FEE Administrativo',
                    'cantidad'        => 1,
                    'precio'          => $valor_fi,
                    'total'           => $valor_fi,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                ]);
            }
        }


        //Verificamos que el Precio Neto de Procedimiento , coincida con el Total de Forma de Pago

        /*$sum_prec_neto = 0;
         for ($i = 0; $i < $request['contador_procedimiento']; $i++){
             if ($request['visibilidad_procedimiento' . $i] == 1) {
               $sum_prec_neto = $sum_prec_neto+$request['total_procedimiento' . $i];
             }
        }
         
        $sum_form_pago = 0;
        for ($i = 0; $i < $request['contador_pago']; $i++) {
            if ($request['visibilidad_pago' . $i] == 1) {
               $sum_form_pago = $sum_form_pago+$request['total' . $i];
            }
        }*/

        $id_orde = Ct_Orden_Venta::where('id', $id_orden)->first();
        $suma_proced = round($id_orde->detalles->sum('total'), 2);
        $suma_form_pag = round($id_orde->pagos->sum('valor'), 2);
        $resta = $suma_proced - $suma_form_pag;

        if ($resta == 0) {

            $val_dif = 0;


            return ['val_dif' => $val_dif, 'id_orden' => $id_orden];
        } else {

            $val_dif = 1;
            $valor = 1;
            return ['val_dif' => $val_dif, 'id_orden' => $id_orden, 'valor' => $valor];
        }
    }

    public function store_new(Request $request){

        // preguntar diferente del nivel
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        //dd($request->all());
        $ct_cliente = ct_clientes::where('identificacion', $request['cedula'])->first();
        if (is_null($ct_cliente)) {
            ct_clientes::create([
                'identificacion'          => $request['cedula'],
                'nombre'                  => $request['razon_social'],
                'tipo'                    => $request['tipo_identificacion'],
                'clase'                   => 'normal',
                'estado'                  => '1',
                'ciudad_representante'    => $request['ciudad'],
                'direccion_representante' => $request['direccion'],
                'telefono1_representante' => $request['telefono'],
                'email_representante'     => $request['email'],
                'pais'                    => 'Ecuador',
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
                'id_usuariocrea'          => $id_usuario,
                'id_usuariomod'           => $id_usuario,
            ]);
            $ct_cliente = ct_clientes::where('identificacion', $request['cedula'])->first();
        } else {
            if(is_null($request['razon_social'])){
                $request['razon_social']=$ct_cliente->nombre;
            }
            ct_clientes::where('identificacion', $request['cedula'])
                ->update([
                    'nombre'                  => $request['razon_social'],
                    'tipo'                    => $request['tipo_identificacion'],
                    'ciudad_representante'    => $request['ciudad'],
                    'direccion_representante' => $request['direccion'],
                    'email_representante'     => $request['email'],
                    'telefono1_representante' => $request['telefono'],
                    'ip_modificacion'         => $ip_cliente,
                    'id_usuariocrea'          => $id_usuario,
                ]);
        }
        $oda= $request['oda'];
        if(is_null($request['numero_oda'])){
            $request['numero_oda']=0;
        }
        if(is_null($request['oda'])){
            $request['oda']=0;
        }
        $valor_copago= $request['totalc'];

        $id_orden = Ct_Orden_Venta::insertGetId([
            'id_agenda'           => $request['id_agenda'],
            'id_empresa'          => '0992704152001',
            'fecha_emision'       => $request['f_emision'],
            'oda'                 => $oda,
            'numero_oda'          => $request['oda'],
            'valor_oda'           => $valor_copago,
            'id_seguro'           => $request['id_seguro'],
            'id_nivel'            => $request['id_nivel'],
            'tipo_identificacion' => $request['tipo_identificacion'],
            'identificacion'      => $request['cedula'],
            'razon_social'        => $request['razon_social'],
            'ciudad'              => $request['ciudad'],
            'direccion'           => $request['direccion'],
            'telefono'            => $request['telefono'],
            'total_sin_tarjeta'   => $request['total_sin_tarjeta'],
            'observacion'         => $request['observacion'],
            'caja'                => $request['caja'],
            'email'               => $request['email'],
            'iva'                 => $request['tarifa_iva1'],
            'subtotal_0'          => $request['subtotal_01'],
            'subtotal_12'         => $request['subtotal_121'],
            'descuento'           => $request['descuento1'],
            'total'               => $request['total1'],
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
            'id_usuariocrea'      => $id_usuario,
            'id_usuariomod'       => $id_usuario,
        ]);
        $valor_fi   = 0;
        $fi_adminis = 0;
        $consulta=0;
        $porcentaje=0;
        for ($i = 0; $i < $request['contador_pago']; $i++) {
            $fi_ad_p = 0;
            if ($request['visibilidad_pago' . $i] == 1) {
                if (isset($request['fi' . $i])) {
                    $fi_adminis = 1;
                    $fi_ad_p    = 1;
                    if ($request['id_tip_pago' . $i] == 4) {
                        $porcentaje = 0.07;
                    } elseif ($request['id_tip_pago' . $i] == 6) {
                        $porcentaje = 0.02;
                    }
                    $valor_fi = $valor_fi + ($request['valor_base' . $i]);
                }
                $consulta = $consulta + $request['valor_base' . $i];
                Ct_Orden_Venta_Pago::create([
                    'id_orden'        => $id_orden,
                    'fecha'           => $request['fecha_pago' . $i],
                    'tipo'            => $request['id_tip_pago' . $i],
                    'tipo_tarjeta'    => $request['tipo_tarjeta' . $i],
                    'numero'          => $request['numero' . $i],
                    'banco'           => $request['id_banco_pago' . $i],
                    'valor'           => $request['valor_base' . $i],
                    'posee_fi'        => $fi_ad_p,
                    'p_fi'            => $porcentaje,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                ]);
            }
        }
        for($i=0; $i<count($request->input("nombre")); $i++){
            $tipo_oda= $request['tipo_oda'][$i];
            $producto= Ct_productos::where('codigo',$request['codigo'][$i])->first();
            //dd($request['codigo']);
            if(!is_null($producto)){
                $paquete= $producto->ident_paquete;
                    if($request['precio'][$i]>0){
                        $total= $request['cantidad'][$i] * $request['precio'][$i];
                        if($request['precioneto'][$i]!=0){
                            $total= $request['precioneto'][$i];
                        }
                        if(isset($request['copago'][$i])){
                            Ct_Orden_Venta_Detalle::create([
                                'id_orden'          => $id_orden,
                                'cod_prod'          => $request['codigo'][$i],
                                'descripcion'       => $request['descrip_prod'][$i],
                                'id_producto'       => $producto->id,
                                'cantidad'          => $request['cantidad'][$i],
                                'precio'            => $request['precio'][$i],
                                'total'             => $total,
                                'tipo_oda'          => $tipo_oda,
                                'p_oda'             => $request['copago'][$i],
                                'valor_oda'         => $request['copaged'][$i],
                                'tipo_dcto'         => '%',
                                'p_dcto'            => $request['descpor'][$i],
                                'descuento'         => $request['descuento'][$i],
                                'iva'               => $request['iva'][$i],
                                'valor_iva'         => $request['impuesto'][$i],
                                'ident_paquete'     => $paquete,
                                'valor_deducible'   => $request['deducible'][$i],
                                //'valor_oda_hum'     => $request['val_deducible' . $i],
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                                'id_usuariocrea'    => $id_usuario,
                                'id_usuariomod'     => $id_usuario,
                            ]);
                        }else{
                            Ct_Orden_Venta_Detalle::create([
                                'id_orden'          => $id_orden,
                                'cod_prod'          => $request['codigo'][$i],
                                'descripcion'       => $request['descrip_prod'][$i],
                                'id_producto'       => $producto->id,
                                'cantidad'          => $request['cantidad'][$i],
                                'precio'            => $request['precio'][$i],
                                'total'             => $total,
                                'tipo_oda'          => $tipo_oda,
                                'p_oda'             => '0',
                                'valor_oda'         => '0.00',
                                'tipo_dcto'         => '%',
                                'p_dcto'            => $request['descpor'][$i],
                                'descuento'         => $request['descuento'][$i],
                                'iva'               => $request['iva'][$i],
                                'valor_iva'         => $request['impuesto'][$i],
                                'ident_paquete'     => $paquete,
                                'valor_deducible'   => $request['deducible'][$i],
                                //'valor_oda_hum'     => $request['val_deducible' . $i],
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                                'id_usuariocrea'    => $id_usuario,
                                'id_usuariomod'     => $id_usuario,
                            ]);
                        }
                        
                    }
            }
            
        }
        return response()->json(['id'=>$id_orden]);
    }
    public function newUpdate($id,Request $request){
        if(!is_null($id)){
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $id_usuario = Auth::user()->id;
            $fact_venta   = Ct_Orden_Venta::findorfail($id);
            $empresas     = Empresa::where('id', '0992704152001')->orWhere('id', '1314490929001')->get();
            $seguros      = Seguro::where('seguros.inactivo', '1')
                ->where('promo_seguro', '<>', 1)
                ->orderBy('nombre', 'asc')->get();
            $ct_cliente   = Ct_Clientes::where('identificacion', $fact_venta->identificacion)->first();
            $tipo_pago    = Ct_Tipo_Pago::all();
            $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
            $lista_banco  = Ct_Bancos::all();
            $empresa      = Empresa::find($fact_venta->id_empresa);
            $agenda       = Agenda::find($fact_venta->id_agenda);
            $xcantidad    = Ct_Orden_Venta_Pago::where('id_orden', $id)->get()->count();
            $fact_venta->fecha_emision=$request['f_emision'];
            $fact_venta->id_seguro= $request['id_seguro'];
            $fact_venta->id_nivel= $request['id_nivel'];
            $fact_venta->tipo_identificacion= $request['tipo_identificacion'];
            $fact_venta->identificacion= $request['cedula'];
            $fact_venta->razon_social= $request['razonsocial'];
            $fact_venta->ciudad= $request['ciudad'];
            $fact_venta->caja= $request['caja'];
            $fact_venta->direccion= $request['direccion'];
            $fact_venta->telefono= $request['telefono'];
            $fact_venta->email= $request['email'];
            $fact_venta->total= $request['total1'];
            $fact_venta->iva= $request['tarifaiva1'];
            $fact_venta->subtotal_0= $request['subtotal_01'];
            $fact_venta->subtotal_12= $request['subtotal_121'];
            if($request['oda']!='0' && $request['oda']!=null){
                $fact_venta->valor_oda= $request['totalc'];
            }
            $fact_venta->observacion= $request['observacion'];
            $fact_venta->numero_oda= $request['oda'];
            $fact_venta->oda= $request['oda'];
            $fact_venta->total_sin_tarjeta= $request['total1'];
            $fact_venta->save();
            $detalles= Ct_Orden_Venta_Detalle::where('id_orden',$id)->delete();
            $d= Ct_Orden_Venta_Pago::where('id_orden',$id)->delete();
            $valor_fi=0;
            $consulta=0;
            $porcentaje=0;
            for ($i = 0; $i < $request['contador_pago']; $i++) {
                $fi_ad_p = 0;
                if ($request['visibilidad_pago' . $i] == 1) {
                    if (isset($request['fi' . $i])) {
                        $fi_adminis = 1;
                        $fi_ad_p    = 1;
                        if ($request['id_tip_pago' . $i] == 4) {
                            $porcentaje = 0.07;
                        } elseif ($request['id_tip_pago' . $i] == 6) {
                            $porcentaje = 0.02;
                        }
                        $valor_fi = $valor_fi + ($request['valor_base' . $i]);
                    }
                    $consulta = $consulta + $request['valor_base' . $i];
                    Ct_Orden_Venta_Pago::create([
                        'id_orden'        => $id,
                        'fecha'           => $request['fecha_pago' . $i],
                        'tipo'            => $request['id_tip_pago' . $i],
                        'tipo_tarjeta'    => $request['tipo_tarjeta' . $i],
                        'numero'          => $request['numero' . $i],
                        'banco'           => $request['id_banco_pago' . $i],
                        'valor'           => $request['valor_base' . $i],
                        'posee_fi'        => $fi_ad_p,
                        'p_fi'            => $porcentaje,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $id_usuario,
                        'id_usuariomod'   => $id_usuario,
                    ]);
                }
            }
            for($i=0; $i<count($request->input("codigo")); $i++){
                $tipo_oda= $request['tipo_oda'][$i];
                $producto= Ct_productos::where('codigo',$request['codigo'][$i])->first();
                //dd($request['codigo']);
                if(!is_null($producto)){
                    $paquete= $producto->ident_paquete;
                        if($request['precio'][$i]>0){
                            $total= $request['cantidad'][$i] * $request['precio'][$i];
                            if($request['precioneto'][$i]!=0){
                                $total= $request['precioneto'][$i];
                            }
                            $res=$request['descpor'];
                            $tre=false;
                            if($res>0){
                                $tre=true;
                            }
                            if(isset($request['copago'][$i])){
                                if($tre){
                                    Ct_Orden_Venta_Detalle::create([
                                        'id_orden'          => $id,
                                        'cod_prod'          => $request['codigo'][$i],
                                        'descripcion'       => $request['descrip_prod'][$i],
                                        'id_producto'       => $producto->id,
                                        'cantidad'          => $request['cantidad'][$i],
                                        'precio'            => $request['precio'][$i],
                                        'total'             => $total,
                                        'tipo_oda'          => $tipo_oda,
                                        'p_oda'             => $request['copago'][$i],
                                        'valor_oda'         => $request['copaged'][$i],
                                        'tipo_dcto'         => '%',
                                        'p_dcto'            => $request['descpor'][$i],
                                        'descuento'         => $request['desc'][$i],
                                        'iva'               => $request['iva'][$i],
                                        'valor_iva'         => $request['impuesto'][$i],
                                        'ident_paquete'     => $paquete,
                                        'valor_deducible'   => $request['deducible'][$i],
                                        //'valor_oda_hum'     => $request['val_deducible' . $i],
                                        'ip_creacion'       => $ip_cliente,
                                        'ip_modificacion'   => $ip_cliente,
                                        'id_usuariocrea'    => $id_usuario,
                                        'id_usuariomod'     => $id_usuario,
                                    ]);
                                }else{
                                    Ct_Orden_Venta_Detalle::create([
                                        'id_orden'          => $id,
                                        'cod_prod'          => $request['codigo'][$i],
                                        'descripcion'       => $request['descrip_prod'][$i],
                                        'id_producto'       => $producto->id,
                                        'cantidad'          => $request['cantidad'][$i],
                                        'precio'            => $request['precio'][$i],
                                        'total'             => $total,
                                        'tipo_oda'          => $tipo_oda,
                                        'p_oda'             => $request['copago'][$i],
                                        'valor_oda'         => $request['copaged'][$i],
                                        'tipo_dcto'         => '$',
                                        'p_dcto'            => $request['descpor'][$i],
                                        'descuento'         => $request['desc'][$i],
                                        'iva'               => $request['iva'][$i],
                                        'valor_iva'         => $request['impuesto'][$i],
                                        'ident_paquete'     => $paquete,
                                        'valor_deducible'   => $request['deducible'][$i],
                                        //'valor_oda_hum'     => $request['val_deducible' . $i],
                                        'ip_creacion'       => $ip_cliente,
                                        'ip_modificacion'   => $ip_cliente,
                                        'id_usuariocrea'    => $id_usuario,
                                        'id_usuariomod'     => $id_usuario,
                                    ]);
                                }
                               
                            }else{
                                if($tre){
                                    Ct_Orden_Venta_Detalle::create([
                                        'id_orden'          => $id,
                                        'cod_prod'          => $request['codigo'][$i],
                                        'descripcion'       => $request['descrip_prod'][$i],
                                        'id_producto'       => $producto->id,
                                        'cantidad'          => $request['cantidad'][$i],
                                        'precio'            => $request['precio'][$i],
                                        'total'             => $total,
                                        'tipo_oda'          => $tipo_oda,
                                        'p_oda'             => '0',
                                        'valor_oda'         => '0.00',
                                        'tipo_dcto'         => '%',
                                        'p_dcto'            => $request['descpor'][$i],
                                        'descuento'         => $request['desc'][$i],
                                        'iva'               => $request['iva'][$i],
                                        'valor_iva'         => $request['impuesto'][$i],
                                        'ident_paquete'     => $paquete,
                                        'valor_deducible'   => $request['deducible'][$i],
                                        //'valor_oda_hum'     => $request['val_deducible' . $i],
                                        'ip_creacion'       => $ip_cliente,
                                        'ip_modificacion'   => $ip_cliente,
                                        'id_usuariocrea'    => $id_usuario,
                                        'id_usuariomod'     => $id_usuario,
                                    ]);
                                }else{
                                    Ct_Orden_Venta_Detalle::create([
                                        'id_orden'          => $id,
                                        'cod_prod'          => $request['codigo'][$i],
                                        'descripcion'       => $request['descrip_prod'][$i],
                                        'id_producto'       => $producto->id,
                                        'cantidad'          => $request['cantidad'][$i],
                                        'precio'            => $request['precio'][$i],
                                        'total'             => $total,
                                        'tipo_oda'          => $tipo_oda,
                                        'p_oda'             => '0',
                                        'valor_oda'         => '0.00',
                                        'tipo_dcto'         => '$',
                                        'p_dcto'            => $request['descpor'][$i],
                                        'descuento'         => $request['desc'][$i],
                                        'iva'               => $request['iva'][$i],
                                        'valor_iva'         => $request['impuesto'][$i],
                                        'ident_paquete'     => $paquete,
                                        'valor_deducible'   => $request['deducible'][$i],
                                        //'valor_oda_hum'     => $request['val_deducible' . $i],
                                        'ip_creacion'       => $ip_cliente,
                                        'ip_modificacion'   => $ip_cliente,
                                        'id_usuariocrea'    => $id_usuario,
                                        'id_usuariomod'     => $id_usuario,
                                    ]);
                                }
                               
                            }
                            
                        }
                }
                
            }
            
        }
    }   
    public function newEdit($id, Request $request){
        $fact_venta   = Ct_Orden_Venta::findorfail($id);
        $empresas     = Empresa::where('id', '0992704152001')->orWhere('id', '1314490929001')->get();
        $seguros      = Seguro::where('seguros.inactivo', '1')
            ->where('promo_seguro', '<>', 1)
            ->orderBy('nombre', 'asc')->get();
        $ct_cliente   = Ct_Clientes::where('identificacion', $fact_venta->identificacion)->first();
        $tipo_pago    = Ct_Tipo_Pago::all();
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        $lista_banco  = Ct_Bancos::all();
        //dd($fact_venta);
        $empresax='0992704152001';
        
        $empresa      = Empresa::find($fact_venta->id_empresa);
        if(is_null($empresa)){
            $empresa = Empresa::find($empresax);
        }
        $agenda       = Agenda::find($fact_venta->id_agenda);
        $xcantidad    = Ct_Orden_Venta_Pago::where('id_orden', $id)->get()->count();
        $formapago= Ct_Orden_Venta_Pago::where('id_orden',$id)->get();
        //dd($formapago);
        //Obtenemos todos los Niveles
        $lista_niveles  = Nivel::all();
        $productos = Ct_productos::where('estado_tabla', '1')->where('id_empresa','0992704152001')->get();
        $detalles= Ct_Orden_Venta_Detalle::where('id_orden',$id)->get();
        $paciente     = Paciente::findorfail($agenda->id_paciente);
        return view('contable/facturacion/agendanedit',['recibo'=>$fact_venta,'detalle'=>$detalles,'cliente'=>$ct_cliente,'tipo_pago'=>$tipo_pago,'tipo_tarjeta'=>$tipo_tarjeta,'lista_banco'=>$lista_banco,'agenda'=>$agenda,'formapago'=>$formapago,'empresa'=>$empresa,'productos'=>$productos,'paciente'=>$paciente,'seguros'=>$seguros]);
    } 
    

}
