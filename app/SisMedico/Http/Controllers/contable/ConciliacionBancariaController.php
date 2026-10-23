<?php

namespace Sis_medico\Http\Controllers\contable;

use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
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


class ConciliacionBancariaController extends Controller
{
    //
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

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');

        // dd($pendientes);
        $bancos = Ct_Caja_Banco::where('estado', '1')->where('clase', 1)->where('id_empresa', $id_empresa)->get();
        //dd($bancos);
        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = date('Y/m/d', strtotime($request['fecha_desde']));
            $fecha_hasta = date('Y/m/d', strtotime($request['fecha_hasta']));
        } else {
            $fecha_desde = date('Y/m/01');
            $fecha_hasta = date('Y/m/t');
        }

        $registros = array();

        $banco = "";

        $tipo = $request->tipo;

        $estado =  "";

        //dd($request->all());

        if (!is_null($request['banco'])) {
            $banco = $request['banco'];
        }

        $anterior  = $this->getSaldoAnterior($request, $fecha_desde, $banco, "");

        if ($tipo != "" || !is_null($tipo)) {
            //dd("hay algo");
            if ($tipo == "BAN-ND") {
                $registros = $this->getNotasDebito($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            } else if ($tipo == "BAN-NC") {
                $registros = $this->getNotasCredito($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            } else if ($tipo == "BAN-TR") {
                $registros = $this->getTransfBancarias($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            } else if ($tipo == "BAN-TR-DB") {
                $registros = $this->getTransfBancariasDebitadas($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            } else if ($tipo == "BAN-ND-AC") {
                $registros = $this->getDebitoBancario($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            } else if ($tipo == 'EG') {
                $registros = $this->getEgresos($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            } else if ($tipo == 'EGV') {
                $registros = $this->getEgresosVarios($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            } else if ($tipo == 'EGM') {
                $registros = $this->getEgresosMasivos($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            } else if ($tipo == 'BAN-DP') {
                $registros = $this->getDepositoBancario($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            }
        } else {
            //dd("no hay");
            $registros = $this->getNotasDebito($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            $registros = $this->getNotasCredito($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            $registros = $this->getTransfBancarias($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            $registros = $this->getTransfBancariasDebitadas($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            $registros = $this->getDebitoBancario($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            $registros = $this->getEgresos($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            $registros = $this->getEgresosVarios($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            $registros = $this->getEgresosMasivos($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
            $registros = $this->getDepositoBancario($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
        }
        $empresa = Empresa::find($id_empresa);
        $tipo_mes = 1;
        $anterior = $this->saldo_ant($fecha_hasta, $tipo_mes, $banco);
        $pendientes = Ct_Conciliacion_Pendientes::where('id_empresa', $id_empresa)->where('estado', '-1')->where('id_caja_banco', $banco)->get();
        //dd($pendientes);

        return view('contable/conciliacion_bancaria/index', ['registros' => $registros, 'pendientes' => $pendientes, 'anterior' => $anterior, 'fecha_desde' => $fecha_desde, 'banco' => $banco, 'tipo' => $tipo, 'fecha_hasta' => $fecha_hasta, 'estado' => $estado, 'tipo' => $tipo, 'bancos' => $bancos, 'empresa' => $empresa, 'anterior' => $anterior, 'id_empresa' => $id_empresa]);
    }
    public function array_sort_by_column($array, $column, $direction = SORT_ASC)
    {
        $reference_array = array();
        foreach ($array as $key => $row) {
            $reference_array[$key] = $row[$column];
        }
        //dd($reference_array);
        array_multisort($reference_array, $direction, $array);
    }
    public function getPendientes($id_empresa)
    {
        return $pendientes;
    }
    public function getTransfBancarias($id_empresa, $registros, $desde, $hasta, $estado, $banco)
    {
        $transbanc = Ct_Transferencia_Bancaria::where('estado', '!=', 0)
            ->whereBetween('fecha_asiento', [date('Y/m/d', strtotime($desde)), date('Y/m/d', strtotime($hasta))])
            ->where('empresa', $id_empresa)
            ->orderby('fecha_asiento', 'desc');

        if ($banco != null) {
            // $transbanc = $transbanc->where('id_cuenta_destino', '=', $banco);
            $cb = Ct_Caja_Banco::find($banco);
            $transbanc = $transbanc->where('id_cuenta_destino', $cb->cuenta_mayor);
        } else {
            $cajaban = Ct_Caja_Banco::where('estado', '!=', '0')->where('clase', '1')->get();
            $ids = array();
            foreach ($cajaban as $value) {
                array_push($ids, $value->cuenta_mayor);
            }
            $transbanc = $transbanc->whereIn('id_cuenta_destino', $ids);
        }

        $transbanc = $transbanc->get();

        foreach ($transbanc as $row) {
            $data                    = array();
            $data['id_consiliacion'] = "$row->tipo-:-$row->id";
            $data['fecha']           = date('Y/m/d', strtotime($row->fecha_asiento));
            $data['fecha_banco']     = $row->fecha_asiento;
            $data['fecha_cheque']    = $row->fecha_cheque;
            $data['tipo']            = $row->tipo;
            $data['numero']          = $row->numero;
            $data['id_asiento']      = $row->id_asiento;
            $data['numcheque']       = $row->numcheque;
            $data['beneficiario']    = $row->beneficiario;
            $data['detalle']         = $row->concepto;
            $data['valor']           = number_format($row->valor_destino, 2, '.', '');
            if ($this->getConciliado($row->tipo, $row->id, $banco)) {
                $data['conciliado'] = 1;
            } else {
                $data['conciliado'] = 0;
            }

            $registros[] = $data;
        }
        return $registros;
    }
    public function getTransfBancariasDebitadas($id_empresa, $registros, $desde, $hasta, $estado, $banco)
    {
        $transbanc = Ct_Transferencia_Bancaria::where('estado', '!=', 0)
            ->whereBetween('fecha_asiento', [date('Y/m/d', strtotime($desde)), date('Y/m/d', strtotime($hasta))])
            ->where('empresa', $id_empresa)
            ->orderby('fecha_asiento', 'desc');

        if ($banco != null) {
            // $transbanc = $transbanc->where('id_cuenta_destino', '=', $banco);
            $cb = Ct_Caja_Banco::find($banco);
            $transbanc = $transbanc->where('id_cuenta_origen', $cb->cuenta_mayor);
        } else {
            $cajaban = Ct_Caja_Banco::where('estado', '!=', '0')->where('clase', '1')->get();
            $ids = array();
            foreach ($cajaban as $value) {
                array_push($ids, $value->cuenta_mayor);
            }
            $transbanc = $transbanc->whereIn('id_cuenta_origen', $ids);
        }

        $transbanc = $transbanc->get();

        foreach ($transbanc as $row) {
            $data                    = array();
            $data['id_consiliacion'] = "$row->tipo-:-$row->id";
            $data['fecha']           = date('Y/m/d', strtotime($row->fecha_asiento));
            $data['fecha_banco']     = $row->fecha_asiento;
            $data['fecha_cheque']    = $row->fecha_cheque;
            // $data['tipo']            = $row->tipo;
            $data['tipo']            = 'BAN-TR-DB';
            $data['numero']          = $row->numero;
            $data['id_asiento']      = $row->id_asiento;
            $data['numcheque']       = $row->numcheque;
            $data['beneficiario']    = $row->beneficiario;
            $data['detalle']         = $row->concepto;
            $data['valor']           = number_format($row->valor_destino, 2, '.', '');
            if ($this->getConciliado($row->tipo, $row->id, $banco)) {
                $data['conciliado'] = 1;
            } else {
                $data['conciliado'] = 0;
            }

            $registros[] = $data;
        }
        return $registros;
    }
    public function getEgresos($id_empresa, $registros, $desde, $hasta, $estado, $banco)
    {
        $query = Ct_Comprobante_Egreso::where('estado', 1)
            ->whereBetween('fecha_comprobante', [date('Y/m/d', strtotime($desde)), date('Y/m/d', strtotime($hasta))])
            ->where('id_empresa', $id_empresa)
            ->whereNotNull('id_caja_banco');

        if ($banco != null) {
            $query = $query->where('id_caja_banco', '=', $banco);
        } else {
            $cajaban = Ct_Caja_Banco::where('estado', '!=', '0')->where('clase', '1')->get();
            $ids = array();
            foreach ($cajaban as $value) {
                array_push($ids, $value->id);
            }
            $query = $query->whereIn('id_caja_banco', $ids);
        }

        $query = $query->get();

        foreach ($query as $row) {
            $data = [];
            $data['id_consiliacion'] = "EG-:-$row->id";
            $data['fecha'] = $row->fecha_comprobante;
            $data['tipo'] = 'EG';
            $data['fecha_banco'] = $row->fecha_cheque;
            $data['fecha_cheque'] = $row->fecha_cheque;
            $data['numcheque'] = $row->no_cheque;
            $data['numero'] = $row->secuencia;
            $data['id_asiento'] = $row->id_asiento_cabecera;
            $data['beneficiario'] = $row->beneficiario;
            $data['detalle'] = $row->descripcion;
            $data['valor'] = $row->valor;
            if ($this->getConciliado('EG', $row->id, $banco)) {
                $data['conciliado'] = 1;
            } else {
                $data['conciliado'] = 0;
            }
            $registros[] = $data;
        }
        return $registros;
    }

    public function getEgresosVarios($id_empresa, $registros, $desde, $hasta, $estado, $banco)
    {
        $query = Ct_Comprobante_Egreso_Varios::where('estado', 1)
            ->whereBetween('fecha_comprobante', [date('Y/m/d', strtotime($desde)), date('Y/m/d', strtotime($hasta))])
            ->where('id_empresa', $id_empresa);

        if ($banco != null) {
            $query = $query->where('id_caja_banco', '=', $banco);
        } else {
            $cajaban = Ct_Caja_Banco::where('estado', '!=', '0')->where('clase', '1')->get();
            $ids = array();
            foreach ($cajaban as $value) {
                array_push($ids, $value->id);
            }
            $query = $query->whereIn('id_caja_banco', $ids);
        }

        $query = $query->get();

        foreach ($query as $row) {
            $data = [];
            $data['id_consiliacion'] = "EGV-:-$row->id";
            $data['fecha'] = $row->fecha_comprobante;
            $data['tipo'] = 'EGV';
            $data['fecha_banco'] = $row->fecha_cheque;
            $data['fecha_cheque'] = $row->fecha_cheque;
            $data['numcheque'] = $row->nro_cheque;
            $data['numero'] = $row->secuencia;
            $data['beneficiario'] = $row->beneficiario;
            $data['id_asiento'] = $row->id_asiento_cabecera;
            $data['detalle'] = $row->descripcion;
            $data['valor'] = $row->valor;
            if ($this->getConciliado('EGV', $row->id, $banco)) {
                $data['conciliado'] = 1;
            } else {
                $data['conciliado'] = 0;
            }
            $registros[] = $data;
        }
        return $registros;
    }
    public function getEgresosMasivos($id_empresa, $registros, $desde, $hasta, $estado, $banco)
    {
        $query = Ct_Comprobante_Egreso_Masivo::where('estado', 1)
            ->whereBetween('fecha_comprobante', [date('Y/m/d', strtotime($desde)), date('Y/m/d', strtotime($hasta))])
            ->where('id_empresa', $id_empresa);

        if ($banco != null) {
            $query = $query->where('id_caja_banco', '=', $banco);
        } else {
            $cajaban = Ct_Caja_Banco::where('estado', '!=', '0')->where('clase', '1')->get();
            $ids = array();
            foreach ($cajaban as $value) {
                array_push($ids, $value->id);
            }
            $query = $query->whereIn('id_caja_banco', $ids);
        }

        $query = $query->get();

        foreach ($query as $row) {
            $data = [];
            $data['id_consiliacion'] = "EGM-:-$row->id";
            $data['fecha'] = $row->fecha_comprobante;
            $data['tipo'] = 'EGM';
            $data['fecha_cheque'] = $row->fecha_cheque;
            $data['fecha_banco'] = $row->fecha_cheque;
            $data['numcheque'] = $row->no_cheque;
            $data['beneficiario'] = $row->beneficiario;
            $data['numero'] = $row->secuencia;
            $data['detalle'] = $row->descripcion;
            $data['valor'] = $row->valor_pago;
            $data['id_asiento'] = $row->id_asiento_cabecera;
            if ($this->getConciliado('EGM', $row->id, $banco)) {
                $data['conciliado'] = 1;
            } else {
                $data['conciliado'] = 0;
            }
            $registros[] = $data;
        }
        return $registros;
    }

    public function getNotasCredito($id_empresa, $registros, $desde, $hasta, $estado, $banco = "")
    {
        $query = Ct_Nota_Credito::where('estado', '!=', 0)
            ->whereBetween('fecha', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->where('empresa', $id_empresa)
            ->orderby('id', 'desc');
        if ($banco != null) {
            $query = $query->where('id_banco', '=', $banco);
        } else {
            $cajaban = Ct_Caja_Banco::where('estado', '!=', '0')->where('clase', '1')->get();
            $ids = array();
            foreach ($cajaban as $value) {
                array_push($ids, $value->id);
            }
            $query = $query->whereIn('id_banco', $ids);
        }

        $query = $query->get();



        foreach ($query as $row) {
            $data                    = array();
            $data['id_consiliacion'] = "$row->tipo-:-$row->id";
            $data['fecha']           = date('Y/m/d', strtotime($row->fecha));
            $data['fecha_banco']     = date('Y/m/d', strtotime($row->fecha));
            $data['fecha_cheque']    = date('Y/m/d', strtotime($row->fecha));
            $data['tipo']            = $row->tipo;
            $data['id_asiento']      = $row->id_asiento;
            $data['numero']          = "";
            $data['numcheque']       = "";
            $data['beneficiario']    = "";
            $data['detalle']         = $row->concepto;
            $data['valor']           = number_format($row->valor, 2, '.', '');
            if ($this->getConciliado($row->tipo, $row->id, $banco)) {
                $data['conciliado'] = 1;
            } else {
                $data['conciliado'] = 0;
            }

            $registros[] = $data;
        }
        return $registros;
    }

    public function getNotasDebito($id_empresa, $registros, $desde, $hasta, $estado, $banco = "")
    {
        $query = Nota_Debito::where('estado', '!=', 0)
            ->whereBetween('fecha', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->where('empresa', $id_empresa)
            ->orderby('id', 'desc');

        if ($banco != null) {
            $query = $query->where('id_banco', '=', $banco);
        } else {
            $cajaban = Ct_Caja_Banco::where('estado', '!=', '0')->where('clase', '1')->get();
            $ids = array();
            foreach ($cajaban as $value) {
                array_push($ids, $value->id);
            }
            $query = $query->whereIn('id_banco', $ids);
        }

        $query = $query->get();

        //dd($query);
        foreach ($query as $row) {
            $data                    = array();
            $data['id_consiliacion'] = "$row->tipo-:-$row->id";
            $data['fecha']           = date('Y/m/d', strtotime($row->fecha));
            $data['fecha_banco']     = date('Y/m/d', strtotime($row->fecha));
            $data['fecha_cheque']    = date('Y/m/d', strtotime($row->fecha));
            $data['tipo']            = $row->tipo;
            $data['id_asiento']      = $row->id_asiento;
            $data['numero']          = "";
            $data['numcheque']       = "";
            $data['beneficiario']    = "";
            $data['detalle']         = $row->concepto;
            $data['valor']           = number_format($row->valor, 2, '.', '');
            if ($this->getConciliado($row->tipo, $row->id, $banco)) {
                $data['conciliado'] = 1;
            } else {
                $data['conciliado'] = 0;
            }

            $registros[] = $data;
        }
        //dd($registros);
        return $registros;
    }

    public function getDebitoBancario($id_empresa, $registros, $desde, $hasta, $estado, $banco = "")
    {
        $query = Ct_Debito_Bancario::where('estado', '!=', 0)
            ->whereBetween('fecha', [date('Y/m/d', strtotime($desde)), date('Y/m/d', strtotime($hasta))])
            ->where('id_empresa', $id_empresa)
            ->orderby('id', 'desc');

        if ($banco != null) {
            $query = $query->where('id_banco', '=', $banco);
        } else {
            $cajaban = Ct_Caja_Banco::where('estado', '!=', '0')->where('clase', '1')->get();
            $ids = array();
            foreach ($cajaban as $value) {
                array_push($ids, $value->id);
            }
            $query = $query->whereIn('id_banco', $ids);
        }

        $query = $query->get();

        foreach ($query as $row) {
            $data                    = array();
            $data['id_consiliacion'] = "$row->tipo-:-$row->id";
            $data['fecha']           = date('Y/m/d', strtotime($row->fecha));
            $data['fecha_banco']     = date('Y/m/d', strtotime($row->fecha));
            $data['tipo']            = $row->tipo;
            $data['numero']          = "";
            $data['numcheque']       = "";
            $data['beneficiario']    = "";
            $data['id_asiento']      = $row->id_asiento;
            $data['detalle']         = $row->concepto;
            $data['valor']           = number_format($row->valor, 2, '.', '');
            if ($this->getConciliado($row->tipo, $row->id, $banco)) {
                $data['conciliado'] = 1;
            } else {
                $data['conciliado'] = 0;
            }

            $registros[] = $data;
        }
        return $registros;
    }

    public function getDepositoBancario($id_empresa, $registros, $desde, $hasta, $estado, $banco = "")
    {
        $query = Ct_Deposito_Bancario::where('estado', '!=', 0)
            ->whereBetween('fecha_asiento', [date('Y/m/d', strtotime($desde)), date('Y/m/d', strtotime($hasta))])
            ->where('empresa', $id_empresa)
            ->orderby('id', 'desc');

        if ($banco != null) {
            $cb = Ct_Caja_Banco::find($banco);
            $query = $query->where('id_cuenta_destino', '=', $cb->cuenta_mayor);
        } else {
            $cajaban = Ct_Caja_Banco::where('estado', '!=', '0')->where('clase', '1')->get();
            $ids = array();
            foreach ($cajaban as $value) {
                array_push($ids, $value->cuenta_mayor);
            }
            $query = $query->whereIn('id_cuenta_destino', $ids);
        }

        $query = $query->get();

        foreach ($query as $row) {
            $data                    = array();
            $data['id_consiliacion'] = "$row->tipo-:-$row->id";
            $data['fecha']           = date('Y/m/d', strtotime($row->fecha_asiento));
            $data['fecha_banco']     = $row->fecha_asiento;
            $data['fecha_cheque']    = $row->fecha_asiento;
            $data['tipo']            = $row->tipo;
            $data['numero']          = $row->numero;
            $data['id_asiento']      = $row->id_asiento;
            $data['numcheque']       = "";
            $data['beneficiario']    = "";
            $data['detalle']         = $row->concepto;
            $data['valor']           = number_format($row->valor_destino, 2, '.', '');
            if ($this->getConciliado($row->tipo, $row->id, $banco)) {
                $data['conciliado'] = 1;
            } else {
                $data['conciliado'] = 0;
            }

            $registros[] = $data;
        }
        return $registros;
    }

    public function getConciliado($tipo, $id, $banco)
    {
        $empresa  = Session::get('id_empresa');
        $consulta = Ct_Conciliacion_Bancaria::where('tipo', $tipo)
            ->where('id_tipo', $id)
            ->where('empresa', $empresa)
            ->where('estado', 1)
            ->where('id_caja_banco', $banco)
            ->first();
        if (isset($consulta->id)) {
            return true;
        }
        return false;
    }

    public function actualizar(Request $request)
    {
        //dd($request->all());
        $id_empresa    = Session::get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        DB::beginTransaction();
        try {
            $dato     = explode('-:-', $request['id']);
            //dd($dato);
            // $consulta_tabla = Ct_Conciliacion_Bancaria::where('tipo', $dato[0])
            //     ->where('id_tipo', $dato[1])
            //     ->where('empresa', $id_empresa)
            //     ->first();

            // if (!is_null($request['fecha'])) {

            $anioc = date("Y", strtotime($request['fecha_hasta']));
            $mesc = date("m", strtotime($request['fecha_hasta']));
            $consulta_mes = Ct_Conciliacion_Mes::where('anio', $anioc)->where('mes', $mesc)->where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo', $request['tipo'])->where('id_caja_banco', $request['banco'])->first();
            if (is_null(($consulta_mes))) {

                $consulta = Ct_Conciliacion_Bancaria::where('tipo', $dato[0])
                    ->where('id_tipo', $dato[1])
                    ->where('empresa', $id_empresa)
                    ->where('id_caja_banco', $request['banco'])
                    ->first();


                if (isset($consulta->id)) {
                    //dd("aqui", $consulta);
                    if (!is_null($request['fecha'])) {
                        if ($consulta->estado == 1) {
                            $estado = 0;
                        } else {
                            $estado = 1;
                        }
                        //$conciliacion         = Ct_Conciliacion_Bancaria::find($consulta->id);
                        $consulta->id_caja_banco        = $request['banco'];
                        $consulta->anio                 = $anioc;
                        $consulta->mes                  = $mesc;
                        $consulta->fecha_conciliacion   = $request['fecha'];
                        $consulta->estado               = $estado;
                        $consulta->id_usuariomod        = $idusuario;
                        $consulta->ip_modificacion      = $ip_cliente;
                        $consulta->save();
                        DB::commit();
                        return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente'];
                    } else {
                        DB::rollBack();
                        return ['respuesta' => 'error', 'msj' => 'Seleccione fecha de conciliacion', 'titulos' => 'Error'];
                    }
                } else {
                    //dd("aqui no");
                    $anio = date("Y", strtotime($request['fecha']));
                    $mes  = date("m", strtotime($request['fecha']));
                    if (!is_null($request['fecha'])) {

                        $conciliacion                       = new Ct_Conciliacion_Bancaria;
                        $conciliacion->tipo                 = $dato[0];
                        $conciliacion->id_tipo              = $dato[1];
                        $conciliacion->empresa              = $id_empresa;
                        $conciliacion->anio                 = $anio;
                        $conciliacion->mes                  = $mes;
                        $conciliacion->fecha_conciliacion   = $request['fecha'];
                        $conciliacion->id_caja_banco        = $request['banco'];
                        $conciliacion->valor                = $request['valor'];
                        $conciliacion->detalle              = $request['detalle'];
                        $conciliacion->estado               = 1;
                        $conciliacion->id_usuariocrea       = $idusuario;
                        $conciliacion->id_usuariomod        = $idusuario;
                        $conciliacion->ip_creacion          = $ip_cliente;
                        $conciliacion->ip_modificacion      = $ip_cliente;
                        $conciliacion->save();
                        DB::commit();
                        return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente'];
                    } else {
                        DB::rollBack();
                        return ['respuesta' => 'error', 'msj' => 'Seleccione fecha de conciliacion', 'titulos' => 'Error'];
                    }
                }
            } else {
                DB::rollBack();
                return ['respuesta' => 'error', 'msj' => 'Este mes ya fue conciliado', 'titulos' => 'Error'];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage()];
        }

        $input['msg'] = "actualizacion ok";
        return response()->json($input);
    }

    public function actualizarmasivo(Request $request)
    {
        $empresa    = Session::get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $dato       = explode('-:-', $request['id']);
        $accion     = $request['accion'];
        $consulta   = Ct_Conciliacion_Bancaria::where('tipo', $dato[0])
            ->where('id_tipo', $dato[1])
            ->first();
        if ($accion == "true") {
            $estado = 1;
        } else {
            $estado = 0;
        }
        if (isset($consulta->id)) {
            $conciliacion         = Ct_Conciliacion_Bancaria::find($consulta->id);
            $conciliacion->estado = $estado;
            $conciliacion->save();
        } else {
            $conciliacion                  = new Ct_Conciliacion_Bancaria;
            $conciliacion->tipo            = $dato[0];
            $conciliacion->id_tipo         = $dato[1];
            $conciliacion->empresa         = $empresa;
            $conciliacion->estado          = $estado;
            $conciliacion->id_usuariocrea  = $idusuario;
            $conciliacion->id_usuariomod   = $idusuario;
            $conciliacion->ip_creacion     = $ip_cliente;
            $conciliacion->ip_modificacion = $ip_cliente;
            $conciliacion->save();
        }
        // dd($request);
        $input['msg'] = "actualizacion ok";
        return response()->json($input);
    }

    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $constraints = [
            'id_asiento'    => $request['buscar_asiento'],
            'fecha_asiento' => $request['fecha_asiento'],
            'concepto'      => $request['concepto'],
            'id'            => $request['numero'],
        ];
        $registros = $this->doSearchingQuery($constraints);

        return view('contable/transferencia_bancaria/index', ['request' => $request, 'registros' => $registros, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Transferencia_Bancaria::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->orderby('id', 'desc')->paginate(5);
    }

    public function show($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $registro    = Ct_Transferencia_Bancaria::findorfail($id);
        $id_empresa  = Session::get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $formas_pago = Ct_Tipo_pago::where('estado', '1')->get();
        $divisas     = Ct_divisas::where('estado', '1')->get();
        $banco       = Ct_Caja_banco::where('estado', '1')->where('clase', '1')->get();
        $bancos      = Ct_Caja_Banco::where('estado', '1')->get();
        $cuentas     = plan_cuentas::all();
        return view('contable/transferencia_bancaria/show', [
            'divisas' => $divisas,
            'empresa'    => $empresa,
            'banco'       => $banco,
            'bancos'                                                       => $bancos,
            'formas_pago' => $formas_pago,
            'cuentas' => $cuentas,
            'registro' => $registro
        ]);
    }

    public function imprimir($id)
    {
        $registro = Ct_Transferencia_Bancaria::findorfail($id);

        $vistaurl = "contable.transferencia_bancaria.pdf";
        $view     = \View::make($vistaurl, compact('registro'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Trasnferencia Bancaria-' . $id . '.pdf');
        //return view('contable/nota_debito/pdf_nota', compact('registro', 'detalle'));

    }

    public function getSaldoAnterior(Request $request, $fecha_desde, $banco, $asiento_id = "")
    {
        $id_empresa  = $request->session()->get('id_empresa');
        //asientos 34658 - 34652
        $fecha_desde = str_replace('/', '-', $fecha_desde);
        $timestamp   = \Carbon\Carbon::parse($fecha_desde)->timestamp;
        $fecha_desde = date('Y-m-d', $timestamp);

        //dd($fecha_desde);

        $detalles = Ct_Asientos_Detalle::where('ct_asientos_detalle.fecha', '<=', strtotime($fecha_desde) . " 00:00:00")
            ->where('fecha', '<>', '0000-00-00 00:00:00')
            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
            ->where('c.id_empresa', $id_empresa)
            ->where('ct_asientos_detalle.estado', '<>', 0);

        if ($asiento_id != "") {
            $detalles = $detalles->where('c.id', '=', $asiento_id);
        }

        if ($banco != "") {
            $ban = Ct_Caja_Banco::where('id', $banco)->where('clase', '1')->where('id_empresa', $id_empresa)->first();
            if (isset($ban->cuenta_mayor)) {
                $detalles = $detalles->where('id_plan_cuenta', '=', $ban->cuenta_mayor);
            }
        } else {
            $bancos    = Ct_Caja_Banco::where('estado', '1')->where('clase', '1')->where('id_empresa', $id_empresa)->get();
            $condicion = "";
            foreach ($bancos as $row) {
                $condicion .= "$row->cuenta_mayor,";
            }
            if ($condicion != "") {
                $cuentasbancos = explode(',', $condicion);
                $detalles      = $detalles->whereIn('id_plan_cuenta', $cuentasbancos);
            }
        }

        $detalles = $detalles->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))->first();
        return $detalles->saldo;
    }

    public function _getSaldoAnterior(Request $request, $fecha_desde, $asiento_id = "", $banco = "")
    {
        $id_empresa  = $request->session()->get('id_empresa');

        $empresa     = Empresa::where('id', $id_empresa)->first();
        $bancos      = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $fecha_desde = str_replace('/', '-', $fecha_desde);
        $timestamp   = \Carbon\Carbon::parse($fecha_desde)->timestamp;
        $fecha_desde = date('Y/m/d', $timestamp);
        //dd($fecha_desde);
        $detalles = Ct_Asientos_Detalle::where('fecha', '<=', $fecha_desde . " 00:00:00")
            ->where('fecha', '<>', '0000-00-00 00:00:00')
            ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
            ->where('c.id_empresa', $id_empresa)
            ->where('ct_asientos_detalle.estado', '<>', 0);

        //dd($detalles->get());
        if ($asiento_id != "") {
            $detalles = $detalles->where('c.id', '=', $asiento_id);
        }

        if ($banco != "") {
            $ban = Ct_Caja_Banco::where('id', $banco)->where('clase', '1')->where('id_empresa', $id_empresa)->first();
            if (isset($ban->cuenta_mayor)) {
                $detalles = $detalles->where('id_plan_cuenta', '=', $ban->cuenta_mayor);
            }
        } else {
            $bancos    = Ct_Caja_Banco::where('estado', '1')->where('clase', '1')->where('id_empresa', $id_empresa)->get();
            $condicion = "";
            foreach ($bancos as $row) {
                $condicion .= "$row->cuenta_mayor,";
            }
            if ($condicion != "") {
                $cuentasbancos = explode(',', $condicion);
                $detalles      = $detalles->whereIn('id_plan_cuenta', $cuentasbancos);
            }
        }

        $detalles =  $detalles->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))->first();
        // dd($detalles);
        return $detalles->saldo;
    }

    public function exportar_excel(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa  = $request->session()->get('id_empresa');
        $fecha_desde = null;
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $bancos      = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        if (!is_null($request['fecha_desde2']) && !is_null($request['fecha_hasta2'])) {
            $fecha_desde = date('Y/m/d', strtotime($request['fecha_desde2']));
            $fecha_hasta = date('Y-m-d', strtotime($request['fecha_hasta2']));
        } else {
            $fecha_desde = date('Y/m/d');
            $fecha_hasta = date('Y/m/d');
        }
        if (!is_null($request['tipo2'])) {
            $tipo = $request['tipo2'];
        } else {
            $tipo = "";
        }
        if (!is_null($request['estado2'])) {
            $estado = $request['estado2'];
        } else {
            $estado = "";
        }
        if (!is_null($request['banco2'])) {
            $banco = $request['banco2'];
        } else {
            $banco = "";
        }
        // dd($tipo);
        // $principales = Ct_Debito_Bancario::where('estado', '!=', null)->orderby('id', 'desc')->paginate(5);
        $registros     = array();
        $saldoanterior = $this->getSaldoAnterior($request, $fecha_desde, "", $banco);
        if ($tipo == "BAN-ND" or $tipo == "") {
            $registros = $this->getNotasDebito($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
        }

        if ($tipo == "BAN-NC" or $tipo == "") {
            $registros = $this->getNotasCredito($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
        }

        if ($tipo == "BAN-TR" or $tipo == "") {
            $registros = $this->getTransfBancarias($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
        }

        if ($tipo == "BAN-ND-AC" or $tipo == "") {
            $registros = $this->getDebitoBancario($id_empresa, $registros, $fecha_desde, $fecha_hasta, $estado, $banco);
        }
        foreach ($registros as $key => $part) {
            $sort[$key] = strtotime($part['fecha']);
        }
        array_multisort($sort, SORT_ASC, $registros);
        //dd($registros);

        Excel::create('Ct_Conciliacion_Bancaria-' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $registros, $saldoanterior) {
            $excel->sheet('Ct_Conciliacion_Bancaria', function ($sheet) use ($empresa, $registros, $saldoanterior) {
                $sheet->mergeCells('A1:H1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });

                $sheet->mergeCells('A2:A2');
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B2:B2');
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha Banco ');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C2:C2');
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Tipo');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('D2:D2');
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Número');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('E2:E2');
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cheque');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F2:F2');
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Beneficiario');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('G2:G2');
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Detalle');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('H2:H2');
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $i = 3;

                $totaldebito  = 0;
                $totalcredito = 0;
                $saldofinal   = 0;
                foreach ($registros as $value) {

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(date("d-m-Y", strtotime($value['fecha'])));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(date("d-m-Y", strtotime($value['fecha_banco'])));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value['tipo']);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value['numero']);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value['numcheque']);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value['beneficiario']);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value['detalle']);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value['valor']);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // $this->setSangria($cont, $cell);
                    });
                    if ($value['tipo'] != "BAN-NC") {
                        $totaldebito += $value['valor'];
                    } else {
                        $totalcredito += $value['valor'];
                    }

                    $i++;
                }
                $i++;
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Saldo Anterior');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });
                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Total Debitos');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Total Creditos');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('E' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Saldo Final');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });
                $i++;
                $sheet->cell('B' . $i, function ($cell) use ($saldoanterior) {
                    // manipulate the cel
                    $cell->setValue($saldoanterior);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });
                $sheet->cell('C' . $i, function ($cell) use ($totaldebito) {
                    // manipulate the cel
                    $cell->setValue($totaldebito);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('D' . $i, function ($cell) use ($totalcredito) {
                    // manipulate the cel
                    $cell->setValue($totalcredito);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });

                $sheet->cell('E' . $i, function ($cell) use ($totalcredito, $totaldebito) {
                    // manipulate the cel
                    $final = round($totalcredito - $totaldebito, 2);
                    $cell->setValue($final);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // $this->setSangria($cont, $cell);
                });
                // $sheet->cell('H' . $i, function ($cell) use ($value) {
                // manipulate the cel
                // $cell->setValue($value['valor']);
                //$cell->setBorder('thin', 'thin', 'thin', 'thin');
                // $this->setSangria($cont, $cell);
                //});

                $sheet->setWidth(array(
                    'A' => 12,
                    'B' => 12,
                    'C' => 12,
                    'D' => 12,
                    'E' => 12,
                    'F' => 12,
                    'G' => 12,
                    'H' => 12,
                ));
            });
            $excel->getActiveSheet()->getStyle('A2:A2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('B2:B2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C2:C2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('D2:D2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('E2:E2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('F2:F2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('G2:G2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('H2:H2')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(28)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(50)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(50)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(20)->setAutosize(false);
        })->export('xlsx');
    }

    public function setDetalles($consulta, $sheet, $i)
    {

        foreach ($consulta as $value) {

            $sheet->cell('A' . $i, function ($cell) use ($value) {
                // manipulate the cel
                $cell->setValue($value->numero2);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // $this->setSangria($cont, $cell);
            });

            return $i;
        }
    }

    public function saldo_bancos(Request $request)
    {
        //dd($request->all());
        $id_empresa    = Session::get('id_empresa');
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $banco = $request['banco'];
        $conciliados = Ct_Conciliacion_Bancaria::where('estado', '1')
            ->whereBetween('fecha_conciliacion', [$fecha_desde, $fecha_hasta])
            ->where('empresa', $id_empresa)
            ->where('id_caja_banco', $banco)
            ->get();

        //dd($conciliados);
        $tipo_mes = 2; //cambiar algun dia 
        $anterior = $this->saldo_ant($fecha_hasta, $tipo_mes, $banco);

        return view('contable/conciliacion_bancaria/saldo_bancos', ['conciliados' => $conciliados, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'anterior' => $anterior, 'id_empresa' => $id_empresa, 'banco' => $banco]);
    }

    public function pendientes(Request $request)
    {
        $id_empresa    = Session::get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($request->check_conc); $i++) {
                if ($request['check_conc'][$i] == '0') {
                    $arr_p = [
                        'id_caja_banco'  =>  $request['banco'],
                        'fecha'          => $request['fecha_b'][$i],
                        'tipo'           => $request['tip'][$i],
                        'id_asiento'     => $request['id_asiento'][$i],
                        'id_concilia'    => $request['id_conc'][$i],
                        'detalle'        => $request['det'][$i],
                        'valor'          => $request['valor_con'][$i],
                        'secuencia'      => $request['numsec'][$i],
                        'cheque'         => $request['num_ch'][$i],
                        'beneficiario'   => $request['benef'][$i],
                        'id_empresa'     => $id_empresa,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod'  => $idusuario,
                        'ip_creacion'    => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                    Ct_Conciliacion_Pendientes::create($arr_p);
                }
            }

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function guardar_mes(Request $request)
    {
        //dd($request->all());
        $id_empresa    = Session::get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        DB::beginTransaction();
        try {

            $anio = date('Y', strtotime($request['fecha_hasta']));
            $mes = date('m', strtotime($request['fecha_hasta']));

            $consulta_mes = Ct_Conciliacion_Mes::where('anio', $anio)->where('mes', $mes)->where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo', $request['tipo'])->where('id_caja_banco', $request['banco'])->first();

            if (is_null($consulta_mes)) {

                $arr = [
                    'id_caja_banco'         => $request['banco'],
                    'anio'                  => $anio,
                    'mes'                   => $mes,
                    'id_empresa'            => $id_empresa,
                    'fecha'                 => $request['fecha_hasta'],
                    'tipo'                  => $request['tipo'],
                    'saldo_anterior'        => $request['saldo_anterior'],
                    'valor_depositos'       => $request['depositos'],
                    'valor_acreditado'      => $request['valor_acreditado'],
                    'valor_cheques'         => $request['cheques_pag'],
                    'valor_debitado'        => $request['valor_debitado'],
                    'saldo_actual'          => $request['saldo_actual'],
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                ];

                Ct_Conciliacion_Mes::create($arr);
                DB::commit();
                return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
            } else {
                DB::rollBack();
                return ['respuesta' => 'error', 'msj' => 'Este mes ya fue conciliado', 'titulos' => 'Error'];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function saldo_ant($fecha_hasta, $tipo_mes, $id_banco)
    {
        //dd($fecha_hasta);
        $id_empresa    = Session::get('id_empresa');
        $anio = date('Y', strtotime($fecha_hasta));
        $mes = date('m', strtotime($fecha_hasta));
        if ($mes == 1) {
            $mes_anterior = 12;
        } else {
            $mes_anterior = $mes - 1;
        }

        $consulta_mes = Ct_Conciliacion_Mes::where('id_empresa', $id_empresa)->where('mes', $mes_anterior)->where('estado', '1')->where('tipo', $tipo_mes)->where('id_caja_banco', $id_banco)->latest()->first();

        if (!is_null($consulta_mes)) {
            $anterior = $consulta_mes->saldo_actual;
        } else {
            $anterior = "0.00";
        }

        return $anterior;
    }

    public function update_pendiente($id)
    {

        $id_empresa    = Session::get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $pendiente = Ct_Conciliacion_Pendientes::find($id);

        $arr = [
            'estado'            => 1,
            'id_usuariomod'     => $idusuario,
            'ip_modificacion'   => $ip_cliente,
        ];

        $pendiente->update($arr);
    }

    public function excel_pendientes(Request $request)
    {
        Excel::create('Conciliacion Bancaria', function ($excel) use ($request) {
            $excel->sheet('Conciliacion', function ($sheet) use ($request) {
                $id_empresa    = Session::get('id_empresa');
                $empresa = Empresa::find($id_empresa);
                $fecha_desde = $request['fecha_desde'];
                $fecha_hasta = $request['fecha_hasta'];

                $anio = date('Y', strtotime($fecha_desde));
                $mes = date('m', strtotime($fecha_hasta));

                $ani2 = (int)date('Y', strtotime($fecha_desde));
                $mes2 = date('F', strtotime($fecha_hasta));
                setlocale(LC_TIME, 'es_ES');
                $mes2 = mb_convert_case(strftime('%B', strtotime($fecha_hasta)), MB_CASE_UPPER, 'UTF-8');



                $consulta = Ct_Conciliacion_Mes::where('mes', $mes)->where('anio', $anio)->where('id_empresa', $id_empresa)->where('estado', '1')->where('id_caja_banco', $request['banco'])->first();


                $pendientes = Ct_Conciliacion_Pendientes::where('estado', '-1')->where('id_empresa', $id_empresa)->where('id_caja_banco', $request['banco'])->get();


                if (!is_null($empresa) && file_exists(storage_path("app/logo/{$empresa->logo}"))) {
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setPath(storage_path("app/logo/{$empresa->logo}"));
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setWidth(150);
                    $objDrawing->setOffsetX(5);
                    $objDrawing->setOffsetY(10);
                    $objDrawing->setWorksheet($sheet);
                }

                $sheet->mergeCells('B3:H3');
                $sheet->cell('B3', function ($cell) use ($empresa, $id_empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial . ' - ' . $id_empresa);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setBackground('#CDE4F1');
                });
                $nombre_banco = isset($consulta->caja_banco) ? $consulta->caja_banco->nombre : '';
                $sheet->mergeCells('A4:H4');
                $sheet->cell('A4', function ($cell) use ($nombre_banco) {
                    // manipulate the cel
                    $cell->setValue('CONCILIACION BANCARIA ' . $nombre_banco);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setBackground('#CDE4F1');
                });

                $sheet->mergeCells('A5:H5');
                $sheet->cell('A5', function ($cell) use ($ani2, $mes2) {
                    // manipulate the cel
                    $cell->setValue($mes2 . ' ' . $ani2);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setBackground('#CDE4F1');
                });


                $sheet->mergeCells('A6:B6');
                $sheet->cell('A6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SALDO EN BANCOS');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setBackground('#CDE4F1');
                });
                $sheet->mergeCells('G6:H6');
                $sheet->cell('G6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SALDO EN LIBROS');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setBackground('#CDE4F1');
                });


                $array2 = array('Saldo Anterior', '(+) Depositos', '(+) Valor Areditado', '(-) Cheques Pagado', '(-) Valores Debitados', 'Saldo Según Bancos');
                $array3 = array('Saldo Anterior', '(+) Depositos', '(+) Valor Areditado', '(-) Cheques Girados', '(-) Valores Debitados', 'Saldo Según Libros');


                $datos["comienzo"] = 7;
                for ($i = 0; $i < count($array2); $i++) {
                    ImportacionesController::excelDetalles($sheet, $datos["comienzo"]++, ["A"], [$array2[$i]]);
                }
                $y = 7;
                for ($i = 0; $i < count($array3); $i++) {
                    ImportacionesController::excelDetalles($sheet, $y++, ["G"], [$array3[$i]]);
                }
                $consulta_mes = Ct_Conciliacion_Mes::where('mes', $mes)->where('anio', $anio)->where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo', 2)->where('id_caja_banco', $request['banco'])->first();



                $libro_mes = Ct_Conciliacion_Mes::where('mes', $mes)->where('anio', $anio)->where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo', 1)->where('id_caja_banco', $request['banco'])->first();






                if (!is_null($consulta_mes)) {
                    $arr_d = array('$' . str_repeat(' ', 5) . $consulta_mes->saldo_anterior, '$' . str_repeat(' ', 5) . $consulta_mes->valor_depositos, '$' . str_repeat(' ', 5) . $consulta_mes->valor_acreditado, '$' . str_repeat(' ', 5) . $consulta_mes->valor_cheques, '$' . str_repeat(' ', 5) . $consulta_mes->valor_debitado, '$' . str_repeat(' ', 5) . $consulta_mes->saldo_actual);
                    $x = 7;
                    for ($i = 0; $i < count($arr_d); $i++) {

                        ImportacionesController::excelDetalles($sheet, $x++, ["B"], [$arr_d[$i]]);
                    }
                }

                if (!is_null($libro_mes)) {
                    $arr_lib = array('$' . str_repeat(' ', 5) . $libro_mes->saldo_anterior, '$' . str_repeat(' ', 5) . $libro_mes->valor_depositos, '$' . str_repeat(' ', 5) . $libro_mes->valor_acreditado, '$' . str_repeat(' ', 5) . $libro_mes->valor_cheques, '$' . str_repeat(' ', 5) . $libro_mes->valor_debitado, '$' . str_repeat(' ', 5) . $libro_mes->saldo_actual);
                    $a = 7;
                    for ($i = 0; $i < count($arr_lib); $i++) {

                        ImportacionesController::excelDetalles($sheet, $a++, ["H"], [$arr_lib[$i]]);
                    }
                }

                $datos["comienzo"] = 16;

                $sheet->mergeCells('A' . $datos["comienzo"] . ':H' . $datos["comienzo"]);
                $sheet->cell('A' . $datos["comienzo"], function ($cell) {
                    // manipulate the cell
                    $cell->setValue('CHEQUES GIRADOS Y NO COBRADOS');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setBackground('#CDE4F1');
                });

                $datos["comienzo"]++;

                $datos["background-color"] = "#CDE4F1";
                $datos["data"] = ["Fecha", "Tipo", "Id Asiento", "Numero de comprobante", "Cheque", "Beneficiario", "Detalle", "Valor"];

                excelCreate::details($sheet, $datos);
                $datos["comienzo"]++;
                foreach ($pendientes as $p) {
                    $datos["data"] = [$p->fecha, $p->tipo, $p->id_asiento, $p->detalle, $p->valor, $p->secuencia, $p->cheque, $p->beneficiario];
                    excelCreate::details($sheet, $datos);
                    $datos["comienzo"]++;
                }


                $sheet->mergeCells('B23:D23');
                $sheet->cell('B23', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ELABORADO POR:');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', null, null, null);
                });
                $nombre = '';
                if (!is_null($consulta_mes)) {
                    $nombre = isset($consulta_mes->nombre) ? null : $consulta_mes->nombre->apellido1 . ' ' . $consulta_mes->nombre->apellido2 . ' ' . $consulta_mes->nombre->nombre1 . ' ' . $consulta_mes->nombre->nombre2;
                }

                $sheet->mergeCells('B24:D24');
                $sheet->cell('B24', function ($cell) use ($nombre) {

                    $cell->setValue($nombre);

                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                });
            });
        })->export('xlsx');
    }

    public function meses_conciliados(Request $request)
    {

        //dd($request->all());    

        $id_empresa    = Session::get('id_empresa');


        $anio = $request->input('anio');
        $month = $request->input('mes');
        $idBanco = $request->input('bancos');

        $libro = Ct_Conciliacion_Mes::where('id_empresa', $id_empresa)
            ->where('estado', '1')
            ->where('tipo', '1')
            ->where(function ($q) use ($anio, $month, $idBanco) {
                if (!is_null($anio)) {
                    $q->where('anio', $anio);
                }
                if (!is_null($month)) {
                    $q->where('mes', $month);
                } 
                if (!is_null($idBanco)) {
                    $q->where('id_caja_banco', $idBanco);
                }
            })
            ->get();
        $banco = Ct_Conciliacion_Mes::where('id_empresa', $id_empresa)
            ->where('estado', '1')
            ->where('tipo', '2')
            ->where(function ($q) use ($anio, $month, $idBanco) {
                if (!is_null($anio)) {
                    $q->where('anio', $anio);
                }
                if (!is_null($month)) {
                    $q->where('mes', $month);
                } 
                if (!is_null($idBanco)) {
                    $q->where('id_caja_banco', $idBanco);
                }
            })
            ->get();
        $bancos_nombre = Ct_Conciliacion_Mes::where('id_empresa', $id_empresa)->where('estado', '1')->groupBy('id_caja_banco')->get();

        return view('contable/conciliacion_bancaria/index_meses', ['libro' => $libro, 'banco' => $banco, 'bancos_nombre' => $bancos_nombre, 'anio' => $anio, 'month' => $month, 'idBanco' => $idBanco]);
    }

    public function anular_mes($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        DB::beginTransaction();
        try {
            $mes_conciliado = Ct_Conciliacion_Mes::find($id);

            $arr = [
                'estado'            => 0,
                'id_usuariomod'     => $idusuario,
                'ip_modificacion'   => $ip_cliente,
            ];

            $mes_conciliado->update($arr);
            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function pdf_pendientes(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa); //
        $fecha_desde = $request['fecha_desde']; //
        $fecha_hasta = $request['fecha_hasta']; //
        $banco  = $request['banco'];

        $anio = date('Y', strtotime($fecha_desde)); //
        $mes = date('m', strtotime($fecha_hasta)); //



        $ani2 = (int)date('Y', strtotime($fecha_desde));
        $mes2 = date('F', strtotime($fecha_hasta));
        setlocale(LC_TIME, 'es_ES');
        $mes2 = mb_convert_case(strftime('%B', strtotime($fecha_hasta)), MB_CASE_UPPER, 'UTF-8');



        $pendientes = Ct_Conciliacion_Pendientes::where('estado', '-1')->where('id_empresa', $id_empresa)->where('id_caja_banco', $request['banco'])->get(); //

        $x = ($empresa->nombrecomercial . ' - ' . $id_empresa); //
        $y = ('CONCILIACION BANCARIA'); //

        $array2 = array('Saldo Anterior', '(+) Depositos', '(+) Valor Areditado', '(-) Cheques Pagado', '(-) Valores Debitados', 'Saldo Según Bancos'); //
        $array3 = array('Saldo Anterior', '(+) Depositos', '(+) Valor Areditado', '(-) Cheques Girados', '(-) Valores Debitados', 'Saldo Según Libros'); //
        $consulta = Ct_Conciliacion_Mes::where('mes', $mes)->where('anio', $anio)->where('id_empresa', $id_empresa)->where('estado', '1')->where('id_caja_banco', $banco)->first();
        $consulta_mes = Ct_Conciliacion_Mes::where('mes', $mes)->where('anio', $anio)->where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo', 2)->where('id_caja_banco', $banco)->get(); //
        //dd($consulta_mes[0]->nombre);
        $libro_mes = Ct_Conciliacion_Mes::where('mes', $mes)->where('anio', $anio)->where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo', 1)->where('id_caja_banco', $banco)->get(); //
        //dd($consulta_mes,$libro_mes);
        $z = ('LISTADO DE DOCUMENTOS PENDIENTES'); //

        $datos["data"] = ["Fecha", "Tipo", "Id Asiento", "Detalle", "Valor", "Numero", "Cheque", "Beneficiario"]; //

        $view = \View::make('contable.conciliacion_bancaria.pdf_pendientes', compact('mes2', 'ani2', 'empresa', 'fecha_desde', 'fecha_hasta', 'anio', 'mes', 'x', 'libro_mes', 'pendientes', 'array2', 'consulta_mes', 'consulta'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');
        return $pdf->stream('ConciliacionBancaria.pdf');
    }

    public function all_pendientes(Request $request)
    {

        $id_empresa  = $request->session()->get('id_empresa');
        $empresa     = Empresa::where('id', $id_empresa)->first();
        $pendientes = Ct_Conciliacion_Pendientes::where('id_empresa', $empresa->id)->where('estado', '-1')->where('id_caja_banco', $request['banco'])->get();
        return view('contable/conciliacion_bancaria/all_pendientes', ['pendientes' => $pendientes]);
    }

    public function anular_conciliacion_bancaria_pendiente(Request $request)
    {

        $id_empresa  = $request->session()->get('id_empresa');
        $conciliacion = Ct_Conciliacion_Pendientes::find($request['id']);
        if (!is_null($conciliacion)) {
            $arrayConciliacion = [
                'estado' => 0,
            ];
            $conciliacion = $conciliacion->update($arrayConciliacion);
        }
    }

    public function conciliacion_mes()
    {

        ini_set('max_execution_time', 100);

        Excel::filter('chunk')->load('conciliacion_mes.xlsx')->chunk(250, function ($reader) {

            foreach ($reader as $book) {


                $arr = [
                    'mes'               => $book->mes,
                    'anio'              => $book->anio,
                    'id_empresa'        => Session::get('id_empresa'),
                    'saldo_anterior'    => $book->saldo_anterior,
                    'valor_depositos'   => $book->valor_depositos,
                    'valor_acreditado'  => $book->valor_acreditado,
                    'valor_cheques'     => $book->valor_cheques,
                    'valor_debitado'    => $book->valor_debitado,
                    'saldo_actual'      => $book->saldo_actual,
                    'pago_servicios'    => $book->pago_servicios,
                    'fecha'             => $book->fecha,
                    'tipo'              => $book->id_tipo,
                    'observacion'       => $book->nombre,
                ];

                Ct_Conciliacion_Mes::create($arr);
            }
        });

        return "listo";
    }

    public function conciliacion_pendientes()
    {
        ini_set('max_execution_time', 100);

        Excel::filter('chunk')->load('pendientes.xlsx')->chunk(250, function ($reader) {

            foreach ($reader as $book) {


                $arr = [
                    'id_empresa'        => Session::get('id_empresa'),
                    'id_asiento'        => $book->id_asiento,
                    'fecha'             => $book->fecha,
                    'tipo'                => $book->tipo,
                    'detalle'           => $book->detalle,
                    'valor'                => $book->valor,
                    'secuencia'            => $book->secuencia,
                    'cheque'            => $book->cheque,
                    'beneficiario'        => $book->beneficiario,
                    'observacion'       => $book->observacion,
                    'id_usuariocrea'        => Auth::user()->id,
                    'id_usuariomod'        => Auth::user()->id,
                    'ip_creacion'        => $_SERVER["REMOTE_ADDR"],
                    'ip_modificacion'        => $_SERVER["REMOTE_ADDR"],
                ];

                Ct_Conciliacion_Pendientes::create($arr);
            }
        });

        return "listo";
    }






    public function conciliacion_pendientes_excel(Request $request)
    {

        //dd($request->all());
        $id_empresa    = Session::get('id_empresa');
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $banco = $request['banco'];


        //dd($fecha_desde,$fecha_hasta,$banco);
        $conciliados = Ct_Conciliacion_Bancaria::where('estado', '1')
            ->whereBetween('fecha_conciliacion', [$fecha_desde, $fecha_hasta])
            ->where('empresa', $id_empresa)
            ->where('id_caja_banco', $banco)
            ->get();

        //dd($conciliados);
        $tipo_mes = 2; //cambiar algun dia 
        $anterior = $this->saldo_ant($fecha_hasta, $tipo_mes, $banco);
        //dd($anterior);



        $anio = date('Y', strtotime($request['fecha_hasta']));
        $mes = date('m', strtotime($request['fecha_hasta']));
        //dd($anio,$mes);

        $consulta_mes = Ct_Conciliacion_Mes::where('anio', $anio)->where('mes', $mes)->where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo', $request['tipo'])->where('id_caja_banco', $request['banco'])->first();



        $titulos = array("", "Fecha Banco", "Tipo", "Detalle", "Valor");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");


        Excel::create('Excel Caja Bancos', function ($excel) use ($titulos, $posicion, $conciliados, $consulta_mes) {
            $excel->sheet('caja bancos', function ($sheet) use ($titulos, $posicion, $conciliados, $consulta_mes) {
                $sheet->mergeCells('B2:E2');
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Conciliar Cuenta Bancaria');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('A5:F5');
                $sheet->cell('A5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONCILIACIÓN BANCARIA - Saldo Banco');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#92CFEF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $comienzo = 6;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }

                $comienzo++;

                $comienzo = 7;

                foreach ($conciliados as $soporte) {

                    $banco = '';
                    if (isset($soporte->caja_banco)) {
                        $banco = $soporte->caja_banco->nombre;
                    }

                    $datos_excel = [];
                    array_push($datos_excel, $banco, substr($soporte->fecha_conciliacion, 0, 10), $soporte->tipo, $soporte->detalle, $soporte->valor);


                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            $cell->setAlignment('center');
                        });
                    }
                    $comienzo++;
                }

                $sheet->mergeCells('A' . $comienzo . ':A' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) {
                    $cell->setValue('Saldo Anterior');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#92CFEF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B' . $comienzo . ':B' . $comienzo);
                $sheet->cell('B' . $comienzo, function ($cell) {
                    $cell->setValue('Depositos');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#92CFEF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $sheet->mergeCells('C' . $comienzo . ':C' . $comienzo);
                $sheet->cell('C' . $comienzo, function ($cell) {
                    $cell->setValue('Valor Acreditado');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#92CFEF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('D' . $comienzo . ':D' . $comienzo);
                $sheet->cell('D' . $comienzo, function ($cell) {
                    $cell->setValue('Cheques Pagados');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#92CFEF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $sheet->mergeCells('E' . $comienzo . ':E' . $comienzo);
                $sheet->cell('E' . $comienzo, function ($cell) {
                    $cell->setValue('Valores Debitados');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#92CFEF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $sheet->mergeCells('F' . $comienzo . ':F' . $comienzo);
                $sheet->cell('F' . $comienzo, function ($cell) {
                    $cell->setValue('Saldo Actual');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#92CFEF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $comienzo++;

                $sheet->mergeCells('A' . $comienzo . ':A' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) use ($consulta_mes) {
                    if (!is_null($consulta_mes)) {
                        $cell->setValue($consulta_mes->saldo_anterior);
                    } else {
                        $cell->setValue('');
                    }

                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $sheet->mergeCells('B' . $comienzo . ':B' . $comienzo);
                $sheet->cell('B' . $comienzo, function ($cell) use ($consulta_mes) {
                    if (!is_null($consulta_mes)) {
                        $cell->setValue($consulta_mes->valor_depositos);
                    } else {
                        $cell->setValue('');
                    }
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C' . $comienzo . ':C' . $comienzo);
                $sheet->cell('C' . $comienzo, function ($cell) use ($consulta_mes) {
                    if (!is_null($consulta_mes)) {
                        $cell->setValue($consulta_mes->valor_acreditado);
                    } else {
                        $cell->setValue('');
                    }
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('D' . $comienzo . ':D' . $comienzo);
                $sheet->cell('D' . $comienzo, function ($cell) use ($consulta_mes) {
                    if (!is_null($consulta_mes)) {
                        $cell->setValue($consulta_mes->valor_cheques);
                    } else {
                        $cell->setValue('');
                    }
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $sheet->mergeCells('E' . $comienzo . ':E' . $comienzo);
                $sheet->cell('E' . $comienzo, function ($cell) use ($consulta_mes) {
                    if (!is_null($consulta_mes)) {
                        $cell->setValue($consulta_mes->valor_debitado);
                    } else {
                        $cell->setValue('');
                    }
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $sheet->mergeCells('F' . $comienzo . ':F' . $comienzo);
                $sheet->cell('F' . $comienzo, function ($cell) use ($consulta_mes) {
                    if (!is_null($consulta_mes)) {
                        $cell->setValue($consulta_mes->saldo_actual);
                    } else {
                        $cell->setValue('');
                    }
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            });
        })->export('xlsx');
    }



    public function meses_conciliados_buscar(Request $request)
    {

        //dd($request->all());
        $id_empresa = Session::get('id_empresa');

        $anio = $request->input('anio');
        $month = $request->input('mes');
        $idBanco = $request->input('bancos');



        $bancos_nombre = Ct_Conciliacion_Mes::where('id_empresa', $id_empresa)->where('estado', '1')->groupBy('id_caja_banco')->get();
        //dd($bancos_nombre[0]->caja_banco);


        $libro = Ct_Conciliacion_Mes::where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo', '2');

        if (!is_null($anio)) {
            $fecha_inicio_ano = Carbon::createFromFormat('Y', $anio)->startOfYear();

            $libro = $libro->where("anio", $fecha_inicio_ano->year);
        } elseif (!is_null($month)) {
            $libro = $libro->where('mes', $month);
        } elseif (!is_null($libro)) {
            $libro = $libro->where('id_caja_banco', $idBanco);
        }


        $libro = $libro->get();


        $banco = Ct_Conciliacion_Mes::where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo', '1');

        if (!is_null($anio)) {
            $fecha_inicio_ano = Carbon::createFromFormat('Y', $anio)->startOfYear();
            $banco = $banco->where("anio", $fecha_inicio_ano->year);
        } elseif (!is_null($month)) {
            $banco = $banco->where('mes', $month);
        } elseif (!is_null($banco)) {
            $banco = $banco->where('id_caja_banco', $idBanco);
        }

        $banco = $banco->get();


        return view('contable/conciliacion_bancaria/index_meses', compact('libro', 'banco', 'bancos_nombre', 'anio', 'month', 'idBanco'));
    }
}
