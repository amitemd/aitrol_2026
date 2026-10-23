<?php

namespace Sis_medico\Http\Controllers\contable;

use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use DateTime;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Conciliacion_Bancaria;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Ct_Debito_Bancario;
use Sis_medico\Ct_divisas;
use Sis_medico\Ct_Nota_Credito;
use Sis_medico\Ct_Tipo_pago;
use Sis_medico\Ct_Transferencia_Bancaria;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Nota_Debito;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_Comprobante_Egreso_Masivo;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Deposito_Bancario;
use Sis_medico\Ct_Conciliacion_Mes;
use Sis_medico\Ct_Conciliacion_Pendientes;
use Sis_medico\Http\Controllers\excelCreate;
use Sis_medico\Http\Controllers\ImportacionesController;
use Sis_medico\v3\contable\CajaBanco;

class NewConciliacionBancariaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $id_empresa = Session::get('id_empresa');
        $conciliaciones = Ct_Conciliacion_Mes::join('ct_caja_banco as b', 'ct_conciliacion_mes.id_caja_banco', 'b.id')
            ->join('users as uc', 'ct_conciliacion_mes.id_usuariocrea', '=', 'uc.id')
            ->leftJoin('users as um', 'ct_conciliacion_mes.id_usuariomod', '=', 'um.id')
            ->where('b.id_empresa', $id_empresa)
            ->where('ct_conciliacion_mes.estado', 1)
            ->groupBy('ct_conciliacion_mes.anio', 'ct_conciliacion_mes.mes', 'ct_conciliacion_mes.id_caja_banco')
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->get([
                'ct_conciliacion_mes.id',
                'ct_conciliacion_mes.id_caja_banco',
                'b.nombre',
                'ct_conciliacion_mes.estado',
                'ct_conciliacion_mes.anio',
                'ct_conciliacion_mes.mes',
                DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                DB::raw('IF(ct_conciliacion_mes.estado=0, CONCAT(SUBSTRING(um.nombre1, 1, 1), ". ", um.apellido1), "") AS usuariomod'),
                DB::raw('GROUP_CONCAT(saldo_actual SEPARATOR \'|\') as valores'),
                'ct_conciliacion_mes.created_at'
            ]);
        return view('contable.conciliacion_bancaria.new_vistas.index', ['conciliaciones' => $conciliaciones]);
    }
    public function index_conciliacion(Request $request)
    {
        $id_empresa = Session::get('id_empresa');
        $bancos = Ct_Caja_Banco::where('estado', '1')->where('clase', 1)->where('id_empresa', $id_empresa)->get();
        $periodo = explode('-', $request->fecha_periodo);
        $periodoCompleto = $request->fecha_periodo;
        $fecha = new DateTime($periodoCompleto . '-01');
        $fecha->modify('-1 month');
        $periodoCompleto = $fecha->format('Y-m');
        $periodoCompleto = explode('-', $periodoCompleto);
        $mesAnterior = (int)$periodoCompleto[1];
        $mesAnterior = str_pad($mesAnterior, 2, 0, STR_PAD_LEFT);
        $banco = $request->id_banco;
        $saldoAnteriorLibros = 0;
        $saldoAnteriorBancos = 0;
        $debito = 0;
        $credito = 0;
        $transferencia = 0;
        $transferenciaDebitada = 0;
        $deposito = 0;
        $egreso = 0;
        $egresoVarios = 0;
        $egresoMasivo = 0;
        $notasDebito = 0;
        $tot_depositos = 0;
        $tot_valor_acreditado = 0;
        $cheques_girados = 0;
        $valores_debitados = 0;
        $saldo_actuallb = 0;

        $registros = [];
        $saldoAnteriorLibros = Ct_Conciliacion_Mes::where('anio', $periodoCompleto[0])
            ->where('id_caja_banco', $banco)
            ->where('mes', $mesAnterior)
            ->where('tipo', 1)
            ->where('estado', 1)
            ->first(['saldo_actual']);
        $saldoAnteriorBancos = Ct_Conciliacion_Mes::where('anio', $periodoCompleto[0])
            ->where('id_caja_banco', $banco)
            ->where('mes', $mesAnterior)
            ->where('tipo', 2)
            ->where('estado', 1)
            ->first(['saldo_actual']);
        $salLi = isset($saldoAnteriorLibros->saldo_actual) ? $saldoAnteriorLibros->saldo_actual : 0;
        $salBa = isset($saldoAnteriorBancos->saldo_actual) ? $saldoAnteriorBancos->saldo_actual : 0;

        if (!is_null($banco)) {
            $notasDebito           = $this->getNotasDebito($banco, $periodo);
            $debito                = $this->getDebitoBancario($banco, $periodo);
            $deposito              = $this->getDepositoBancario($banco, $periodo); //*/
            $credito               = $this->getNotasCredito($banco, $periodo);
            $transferencia         = $this->getTransfBancarias($banco, $periodo); //
            $transferenciaDebitada = $this->getTransfBancariasDebitadas($banco, $periodo);
            $egreso                = $this->getEgresos($banco, $periodo); //
            $egresoVarios          = $this->getEgresosVarios($banco, $periodo); //
            $egresoMasivo          = $this->getEgresosMasivos($banco, $periodo);

            $arrays = [
                $debito,
                $credito,
                $transferencia,
                $transferenciaDebitada,
                $deposito,
                $egreso,
                $egresoVarios,
                $egresoMasivo,
                $notasDebito
            ];

            foreach ($arrays as $resultado) {
                if (!empty($resultado) && is_iterable($resultado)) {
                    foreach ($resultado as $item) {
                        $registros[] = $item;
                    }
                }
            }

            $tot_depositos = $deposito->sum('valor');
            $tot_valor_acreditado = $transferencia->sum('valor') + $credito->sum('valor');
            $cheques_girados = $egreso->sum('valor') + $egresoVarios->sum('valor');
            $valores_debitados = $notasDebito->sum('valor') + $debito->sum('valor') + $egresoMasivo->sum('valor') + $transferenciaDebitada->sum('valor');
            $saldo_actuallb = $salLi + ($tot_depositos + $tot_valor_acreditado - $cheques_girados - $valores_debitados);
        }

        $tblsaldo_lib = [
            'saldo_anterior' => $salLi,
            'depositos' => $tot_depositos,
            'valor_acreditado' => $tot_valor_acreditado,
            'cheques_girados' => $cheques_girados,
            'valores_debitados' => $valores_debitados,
            'saldo_actual' => $saldo_actuallb
        ];

        $pendientes = Ct_Conciliacion_Pendientes::where('id_empresa', $id_empresa)->where('estado', '-1')->where('id_caja_banco', $banco)->get();
        $fecha = $request->fecha_periodo ?? date('Y-m');

        return view('contable.conciliacion_bancaria.new_vistas.indexc', ['bancos' => $bancos, 'registros' => $registros, 'banco' => $banco, 'pendientes' => $pendientes, 'tblsaldo_lib' => $tblsaldo_lib, 'salBa' => $salBa, 'fecha' => $fecha]);
    }

    public function getTransfBancarias($banco, $periodo)
    {
        $transferencia = Ct_Transferencia_Bancaria::leftjoin('ct_caja_banco as cb', 'ct_transferencia_bancaria.id_cuenta_destino', 'cb.cuenta_mayor')
            ->leftjoin('ct_conciliacion_bancaria as con', function ($q) {
                $q->on('con.id_tipo',  'ct_transferencia_bancaria.id');
                $q->on('con.tipo', 'ct_transferencia_bancaria.tipo');
            })
            ->whereYear('ct_transferencia_bancaria.fecha_asiento', $periodo[0])
            ->whereMonth('ct_transferencia_bancaria.fecha_asiento', $periodo[1])
            ->where('ct_transferencia_bancaria.empresa', Session::get('id_empresa'))
            ->where(function ($q) use ($banco) {
                if ($banco != 0)
                    $q->where('cb.id', $banco);
            })
            ->orderBy('ct_transferencia_bancaria.fecha_asiento', 'asc')
            ->where('ct_transferencia_bancaria.estado', '!=', 0)
            ->get([
                DB::Raw('CONCAT(ct_transferencia_bancaria.tipo, "-:-", ct_transferencia_bancaria.id) as id_consiliacion'),
                'ct_transferencia_bancaria.id',
                'ct_transferencia_bancaria.tipo',
                'ct_transferencia_bancaria.id_cuenta_destino',
                'ct_transferencia_bancaria.fecha_asiento as fecha',
                'ct_transferencia_bancaria.id_asiento',
                'ct_transferencia_bancaria.concepto as detalle',
                'ct_transferencia_bancaria.valor_destino as valor',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_'),
                DB::raw('IF(con.estado IS NULL, 0, 1) as conciliado')
            ]);

        //$data['id_consiliacion'] = "$row->tipo-:-$row->id";

        return $transferencia;
    }
    public function getTransfBancariasDebitadas($banco, $periodo)
    {
        $transferencia_debitada = Ct_Transferencia_Bancaria::leftjoin('ct_caja_banco as cb', 'ct_transferencia_bancaria.id_cuenta_origen', 'cb.cuenta_mayor')
            ->leftjoin('ct_conciliacion_bancaria as con', function ($q) {
                $q->on('con.id_tipo',  'ct_transferencia_bancaria.id');
                $q->on('con.tipo', 'ct_transferencia_bancaria.tipo');
            })
            ->whereYear('ct_transferencia_bancaria.fecha_asiento', $periodo[0])
            ->whereMonth('ct_transferencia_bancaria.fecha_asiento', $periodo[1])
            ->where('ct_transferencia_bancaria.empresa', Session::get('id_empresa'))
            ->where(function ($q) use ($banco) {
                if ($banco != 0)
                    $q->where('cb.id', $banco);
            })
            ->orderBy('ct_transferencia_bancaria.fecha_asiento', 'asc')
            ->where('ct_transferencia_bancaria.estado', '!=', 0)
            ->get([
                DB::Raw('CONCAT("BAN-TR-DB", "-:-", ct_transferencia_bancaria.id) as id_consiliacion'),
                'ct_transferencia_bancaria.id',
                DB::raw('"BAN-TR-DB" as tipo'),
                'ct_transferencia_bancaria.id_cuenta_origen',
                'ct_transferencia_bancaria.fecha_asiento as fecha',
                'ct_transferencia_bancaria.id_asiento',
                'ct_transferencia_bancaria.concepto as detalle',
                'ct_transferencia_bancaria.valor_origen as valor',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_'),
                DB::raw('IF(con.estado IS NULL, 0, 1) as conciliado')
            ]);
        return $transferencia_debitada;
    }
    public function getEgresos($banco, $periodo)
    {
        $egresos = Ct_Comprobante_Egreso::leftjoin('ct_conciliacion_bancaria as con', function ($q) {
            $q->on('con.id_tipo',  'ct_comprobante_egreso.id')
                ->where('con.tipo', 'EG');
        })
            ->whereYear('ct_comprobante_egreso.fecha_comprobante', $periodo[0])
            ->whereMonth('ct_comprobante_egreso.fecha_comprobante', $periodo[1])
            ->where('ct_comprobante_egreso.id_empresa', Session::get('id_empresa'))
            ->where(function ($q) use ($banco) {
                if ($banco != 0)
                    $q->where('ct_comprobante_egreso.id_caja_banco', $banco);
            })
            ->orderBy('ct_comprobante_egreso.fecha_comprobante', 'asc')
            ->where('ct_comprobante_egreso.estado', '!=', 0)
            ->get([
                DB::Raw('CONCAT("EG", "-:-", ct_comprobante_egreso.id) as id_consiliacion'),
                'ct_comprobante_egreso.id',
                DB::raw('"EG" as tipo'),
                'ct_comprobante_egreso.fecha_comprobante as fecha',
                'ct_comprobante_egreso.id_asiento_cabecera as id_asiento',
                'ct_comprobante_egreso.descripcion as detalle',
                'ct_comprobante_egreso.valor',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_'),
                DB::raw('IF(con.estado IS NULL, 0, 1) as conciliado')
            ]);
        return $egresos;
    }

    public function getEgresosVarios($banco, $periodo)
    {
        $egresosVarios = Ct_Comprobante_Egreso_Varios::leftjoin('ct_conciliacion_bancaria as con', function ($q) {
            $q->on('con.id_tipo',  'ct_comprobante_egreso_varios.id')
                ->where('con.tipo', 'EGV');
        })
            ->whereYear('ct_comprobante_egreso_varios.fecha_comprobante', $periodo[0])
            ->whereMonth('ct_comprobante_egreso_varios.fecha_comprobante', $periodo[1])
            ->where('ct_comprobante_egreso_varios.id_empresa', Session::get('id_empresa'))
            ->where(function ($q) use ($banco) {
                if ($banco != 0)
                    $q->where('ct_comprobante_egreso_varios.id_caja_banco', $banco);
            })
            ->orderBy('ct_comprobante_egreso_varios.fecha_comprobante', 'asc')
            ->where('ct_comprobante_egreso_varios.estado', '!=', 0)
            ->get([
                DB::Raw('CONCAT("EGV", "-:-", ct_comprobante_egreso_varios.id) as id_consiliacion'),
                'ct_comprobante_egreso_varios.id',
                DB::raw('"EGV" as tipo'),
                'ct_comprobante_egreso_varios.fecha_comprobante as fecha',
                'ct_comprobante_egreso_varios.id_asiento_cabecera as id_asiento',
                'ct_comprobante_egreso_varios.descripcion as detalle',
                'ct_comprobante_egreso_varios.valor',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_'),
                DB::raw('IF(con.estado IS NULL, 0, 1) as conciliado')
            ]);
        return $egresosVarios;
    }
    public function getEgresosMasivos($banco, $periodo)
    {
        $egresosMasivos = Ct_Comprobante_Egreso_Masivo::leftjoin('ct_conciliacion_bancaria as con', function ($q) {
            $q->on('con.id_tipo',  'ct_comprobante_egreso_masivo.id')
                ->where('con.tipo', 'EGM');
        })
            ->whereYear('ct_comprobante_egreso_masivo.fecha_comprobante', $periodo[0])
            ->whereMonth('ct_comprobante_egreso_masivo.fecha_comprobante', $periodo[1])
            ->where('ct_comprobante_egreso_masivo.id_empresa', Session::get('id_empresa'))
            ->where(function ($q) use ($banco) {
                if ($banco != 0)
                    $q->where('ct_comprobante_egreso_masivo.id_caja_banco', $banco);
            })
            ->orderBy('ct_comprobante_egreso_masivo.fecha_comprobante', 'asc')
            ->where('ct_comprobante_egreso_masivo.estado', '!=', 0)
            ->get([
                DB::Raw('CONCAT("EGM", "-:-", ct_comprobante_egreso_masivo.id) as id_consiliacion'),
                'ct_comprobante_egreso_masivo.beneficiario',
                'ct_comprobante_egreso_masivo.secuencia',
                'ct_comprobante_egreso_masivo.no_cheque',
                'ct_comprobante_egreso_masivo.id',
                DB::raw('"EGM" as tipo'),
                'ct_comprobante_egreso_masivo.fecha_comprobante as fecha',
                'ct_comprobante_egreso_masivo.id_asiento_cabecera as id_asiento',
                'ct_comprobante_egreso_masivo.descripcion as detalle',
                'ct_comprobante_egreso_masivo.valor_pago as valor',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_'),
                DB::raw('IF(con.estado IS NULL, 0, 1) as conciliado')

            ]);
        return $egresosMasivos;
    }

    public function getNotasCredito($banco, $periodo)
    {
        $notaCredito = Ct_Nota_Credito::leftjoin('ct_conciliacion_bancaria as con', function ($q) {
            $q->on('con.id_tipo',  'ct_nota_credito_cabecera.id');
            $q->on('con.tipo', 'ct_nota_credito_cabecera.tipo');
        })
            ->whereYear('ct_nota_credito_cabecera.fecha', $periodo[0])
            ->whereMonth('ct_nota_credito_cabecera.fecha', $periodo[1])
            ->where('ct_nota_credito_cabecera.empresa', Session::get('id_empresa'))
            ->where(function ($q) use ($banco) {
                if ($banco != 0)
                    $q->where('ct_nota_credito_cabecera.id_banco', $banco);
            })
            ->orderBy('ct_nota_credito_cabecera.fecha', 'asc')
            ->where('ct_nota_credito_cabecera.estado', '!=', 0)
            ->get([
                DB::Raw('CONCAT(ct_nota_credito_cabecera.tipo, "-:-", ct_nota_credito_cabecera.id) as id_consiliacion'),
                'ct_nota_credito_cabecera.id',
                'ct_nota_credito_cabecera.tipo',
                'ct_nota_credito_cabecera.fecha',
                'ct_nota_credito_cabecera.id_asiento',
                'ct_nota_credito_cabecera.concepto as detalle',
                'ct_nota_credito_cabecera.valor',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_'),
                DB::raw('IF(con.estado IS NULL, 0, 1) as conciliado')

            ]);
        return $notaCredito;
    }

    public function getNotasDebito($banco, $periodo)
    {
        $notasDebito = Nota_Debito::leftjoin('ct_conciliacion_bancaria as con', function ($q) {
            $q->on('con.id_tipo',  'ct_nota_debito_cabecera.id');
            $q->on('con.tipo', 'ct_nota_debito_cabecera.tipo');
        })
            ->whereYear('ct_nota_debito_cabecera.fecha', $periodo[0])
            ->whereMonth('ct_nota_debito_cabecera.fecha', $periodo[1])
            ->where('ct_nota_debito_cabecera.empresa', Session::get('id_empresa'))
            ->where(function ($q) use ($banco) {
                if ($banco != 0)
                    $q->where('ct_nota_debito_cabecera.id_banco', $banco);
            })
            ->orderBy('ct_nota_debito_cabecera.fecha', 'asc')
            ->where('ct_nota_debito_cabecera.estado', '!=', 0)
            ->get([
                DB::Raw('CONCAT(ct_nota_debito_cabecera.tipo, "-:-", ct_nota_debito_cabecera.id) as id_consiliacion'),
                'ct_nota_debito_cabecera.id',
                'ct_nota_debito_cabecera.tipo',
                'ct_nota_debito_cabecera.fecha',
                'ct_nota_debito_cabecera.id_asiento',
                'ct_nota_debito_cabecera.concepto as detalle',
                'ct_nota_debito_cabecera.valor',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_'),
                DB::raw('IF(con.estado IS NULL, 0, 1) as conciliado')

            ]);
        return $notasDebito;
    }

    public function getDebitoBancario($banco, $periodo)
    {
        $notasDebitoBancaria = Ct_Debito_Bancario::leftjoin('ct_conciliacion_bancaria as con', function ($q) {
            $q->on('con.id_tipo',  'ct_debito_bancario.id');
            $q->on('con.tipo', 'ct_debito_bancario.tipo');
        })
            ->whereYear('ct_debito_bancario.fecha', $periodo[0])
            ->whereMonth('ct_debito_bancario.fecha', $periodo[1])
            ->where('ct_debito_bancario.id_empresa', Session::get('id_empresa'))
            ->where(function ($q) use ($banco) {
                if ($banco != 0)
                    $q->where('ct_debito_bancario.id_banco', $banco);
            })
            ->orderBy('ct_debito_bancario.fecha', 'asc')
            ->where('ct_debito_bancario.estado', '!=', 0)
            ->get([
                // DB::Raw('CONCAT(ct_debito_bancario.tipo, "-:-", ct_debito_bancario.id) as id_consiliacion'),
                'ct_debito_bancario.id',
                'ct_debito_bancario.tipo',
                'ct_debito_bancario.fecha',
                'ct_debito_bancario.id_asiento',
                'ct_debito_bancario.concepto as detalle',
                'ct_debito_bancario.valor',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_'),
                DB::raw('IF(con.estado IS NULL, 0, 1) as conciliado')

            ]);
        return $notasDebitoBancaria;
    }

    public function getDepositoBancario($banco, $periodo)
    {
        $depositos = Ct_Deposito_Bancario::leftjoin('ct_caja_banco as cb', 'ct_deposito_bancario.id_cuenta_destino', 'cb.cuenta_mayor')
            ->leftjoin('ct_conciliacion_bancaria as con', function ($q) {
                $q->on('con.id_tipo',  'ct_deposito_bancario.id');
                $q->on('con.tipo', 'ct_deposito_bancario.tipo')->where('con.empresa', Session::get('id_empresa'));
            })
            ->where('ct_deposito_bancario.estado', '!=', 0)
            ->whereYear('ct_deposito_bancario.fecha_asiento', $periodo[0])
            ->whereMonth('ct_deposito_bancario.fecha_asiento', $periodo[1])
            ->where('ct_deposito_bancario.empresa', session('id_empresa'))
            ->where(function ($q) use ($banco) {
                if ($banco != 0) {
                    $q->where('cb.id', $banco);
                }
            })
            ->orderBy('ct_deposito_bancario.fecha_asiento', 'asc')
            ->get([
                DB::Raw('CONCAT(ct_deposito_bancario.tipo, "-:-", ct_deposito_bancario.id) as id_consiliacion'),
                'ct_deposito_bancario.id',
                'ct_deposito_bancario.tipo',
                'ct_deposito_bancario.id_cuenta_destino',
                'ct_deposito_bancario.fecha_asiento as fecha',
                'ct_deposito_bancario.id_asiento',
                'ct_deposito_bancario.concepto as detalle',
                'ct_deposito_bancario.valor_destino as valor',
                DB::Raw('IF(IFNULL(con.estado,0)=1,"checked","") as check_'),
                DB::raw('IF(con.estado IS NULL, 0, 1) as conciliado')
            ]);
        return $depositos;
    }

    public function conciliacion_create(Request $request)
    {
        $id_empresa = Session::get('id_empresa');
        $bancos = Ct_Caja_Banco::where('estado', '1')->where('clase', 1)->where('id_empresa', $id_empresa)->get();
        $periodo = explode('-', $request->fecha_periodo);
        $periodoCompleto = $request->fecha_periodo;
        $fecha = new DateTime($periodoCompleto . '-01');
        $fecha->modify('-1 month');
        $periodoCompleto = $fecha->format('Y-m');
        $periodoCompleto = explode('-', $periodoCompleto);
        $mesAnterior = (int)$periodoCompleto[1];
        $mesAnterior = str_pad($mesAnterior, 2, 0, STR_PAD_LEFT);
        $banco = $request->id_banco;
        $fecha_periodo = $request->fecha_periodo ?? date('Y-m');
        $idMes = null;
        $parametros = null;
        if ($request->id != '') {
            $idMes = $request->id;
            $parametros = CajaBanco::verConciliacion($request->id);
        }

        return view('contable.conciliacion_bancaria.new_vistas.index_conciliacion', ['bancos' => $bancos, 'banco' => $banco, 'fecha_periodo' => $fecha_periodo, 'idMes' => $idMes, 'parametros' => $parametros]);
    }
    public function infoConciliacion(Request $request)
    {
        $data = json_decode(json_encode($request->all()));
        $info = CajaBanco::traerConciliacionPeriodo($data);
        return $info;
    }
    public function verificarConciliacionTransferencia(Request $request)
    {
        $data = json_decode(json_encode($request->all()));
        $info = CajaBanco::verificarConciliacionTransferencia($data);
        return $info;
    }
    public function verificarConciliacionDeposito(Request $request)
    {
        $data = json_decode(json_encode($request->all()));
        $info = CajaBanco::verificarConciliacionDeposito($data);
        return $info;
    }
    public function verificarConciliacionNotaDebito(Request $request)
    {
        $data = json_decode(json_encode($request->all()));
        $info = CajaBanco::verificarConciliacionNotaDebito($data);
        return $info;
    }
    public function verificarConciliacionNotaCredito(Request $request)
    {
        $data = json_decode(json_encode($request->all()));
        $info = CajaBanco::verificarConciliacionNotaCredito($data);
        return $info;
    }
    public function verificarConciliacionDebitoBancario(Request $request)
    {
        $data = json_decode(json_encode($request->all()));
        $info = CajaBanco::verificarConciliacionDebitoBancario($data);
        return $info;
    }
    public function verificarConciliacionEgresos(Request $request)
    {
        $data = json_decode(json_encode($request->all()));
        $info = CajaBanco::verificarConciliacionEgresos($data);
        return $info;
    }
    public function verificarConciliacionEgresosVarios(Request $request)
    {
        $data = json_decode(json_encode($request->all()));
        $info = CajaBanco::verificarConciliacionEgresosVarios($data);
        return $info;
    }
    public function verificarConciliacionEgresosMasivos(Request $request)
    {
        $data = json_decode(json_encode($request->all()));
        $info = CajaBanco::verificarConciliacionEgresosMasivos($data);
        return $info;
    }
    public function verificarConciliacionPendientes(Request $request)
    {
        $data = json_decode(json_encode($request->all()));
        $info = CajaBanco::verificarConciliacionPendientes($data);
        return $info;
    }
    public function conciliacion_store(Request $request)
    {
        $data = $request->all();
        $info = CajaBanco::saveConciliacion($request);
        return $info;
    }

    public function anularConciliacion($anio, $mes, $banco)
    {
        DB::beginTransaction();
        try {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $conciliacion_mes = Ct_Conciliacion_Mes::where('anio', $anio)->where('mes', $mes)
                ->where('id_caja_banco', $banco)
                ->where('id_empresa', Session::get('id_empresa'))
                ->get();
            if (!is_null($conciliacion_mes)) {
                foreach ($conciliacion_mes as $key => $value) {
                    $value->estado = 0;
                    $value->id_usuariomod = $idusuario;
                    $value->ip_modificacion = $ip_cliente;
                    $value->save();
                }
            }
            $pendientes = Ct_Conciliacion_Pendientes::whereMonth('fecha', $mes)->whereYear('fecha', $anio)
                ->where('id_caja_banco', $banco)
                ->where('id_empresa', Session::get('id_empresa'))
                ->get();
            if (!is_null($pendientes)) {
                foreach ($pendientes as $key => $val) {
                    $val->estado = 0;
                    $val->id_usuariomod = $idusuario;
                    $val->ip_modificacion = $ip_cliente;
                    $val->save();
                }
            }
            DB::commit();
            return ['status' => 'success', 'msj' => 'Anulado Exitosamente'];
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['status' => "error", 'msj' => $e->getMessage(), 'liena' => $e->getLine(), 'archivo' => $e->getFile()];
        }
    }
    public function pdfDatosConciliacion(Request $request)
    {
        $data = $request->all();
        $info = CajaBanco::pdfDatosConciliacion($request);
        return $info;
    }
    public function excelDatosConciliacion(Request $request)
    {
        $data = $request->all();
        $info = CajaBanco::excelDatosConciliacion($request);
        return $info;
    }
}
