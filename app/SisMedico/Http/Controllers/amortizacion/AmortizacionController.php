<?php

namespace Sis_medico\Http\Controllers\amortizacion;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Sis_medico\AmortizacionPrestamo;
use Sis_medico\AmortizacionPrestamoDetalle;
use Sis_medico\AmortizacionPrestamoLinea;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\LogAsiento;

class AmortizacionController extends Controller
{
    public function index()
    {
        $prestamos = AmortizacionPrestamo::where('id_empresa', session('id_empresa'))
            ->with(['detalles.linea', 'lineas'])
            ->orderBy('id', 'desc')->get();
        $prestamos->each(function ($prestamo) {
            $prestamo->cuotas_pagadas = $prestamo->detalles->where('estado', 'pagado')->count();
            $prestamo->cuotas_pendientes = $prestamo->detalles->where('estado', 'pendiente')->count();
            $prestamo->cuotas_totales = $prestamo->detalles->count();
        });
        return view('amortizacion.index', compact('prestamos'));
    }

    public function create()
    {
        $lineasAnteriores = old('lineas', []);
        return view('amortizacion.create', [
            'cuentasSeleccionadas' => collect(),
            'previewRows' => [],
            'previewData' => ['lineas_json' => json_encode(is_array($lineasAnteriores) ? $lineasAnteriores : []), 'usar_lineas' => old('usar_lineas')],
        ]);
    }

    public function cuentas(Request $request)
    {
        $term = trim((string) $request->get('term', ''));
        $cuentas = DB::table('plan_cuentas_empresa as pe')
            ->join('plan_cuentas as p', 'p.id', '=', 'pe.id_plan')
            ->where('pe.id_empresa', session('id_empresa'))
            ->where('pe.estado', 2);
        if ($term !== '') {
            $cuentas->where(function ($q) use ($term) {
                $q->where('pe.plan', 'like', '%' . $term . '%')
                    ->orWhere('pe.nombre', 'like', '%' . $term . '%');
            });
        }
        $cuentas = $cuentas->orderBy('pe.plan')->limit(30)->get(['p.id as plan_id', 'pe.plan', 'pe.nombre']);
        return response()->json(['results' => $cuentas->map(function ($cuenta) {
            return ['id' => $cuenta->plan, 'text' => $cuenta->plan . ' | ' . $cuenta->nombre];
        })->values()]);
    }

    public function preview(Request $request)
    {
        $rules = [
            'institucion' => 'required|string|max:150',
            'numero_contrato' => 'nullable|string|max:80',
            'fecha_desembolso' => 'required|date',
            'fecha_primer_pago' => 'required|date|after_or_equal:fecha_desembolso',
            'monto' => 'required|numeric|min:0.01',
            'tasa_anual' => 'required|numeric|min:0|max:100',
            'plazo_meses' => 'required|integer|min:1|max:600',
            'metodo' => 'required|in:frances,aleman',
            'seguro_tipo' => 'required|in:ninguno,porcentaje_saldo,fijo',
            'seguro_valor' => 'nullable|numeric|min:0',
            'cuenta_banco' => 'nullable|string|max:50',
            'cuenta_deuda' => 'nullable|string|max:50',
            'cuenta_interes' => 'nullable|string|max:50',
            'cuenta_seguro' => 'nullable|string|max:50',
            'usar_lineas' => 'nullable|boolean',
            'lineas' => 'nullable|array',
        ];
        $input = $request->all();
        foreach (['cuenta_banco', 'cuenta_deuda', 'cuenta_interes', 'cuenta_seguro'] as $campo) {
            if (isset($input[$campo]) && trim((string) $input[$campo]) === '') $input[$campo] = null;
        }
        $validator = Validator::make($input, $rules, [
            'fecha_primer_pago.after_or_equal' => 'La fecha del primer pago no puede ser anterior a la fecha de desembolso.',
            'monto.min' => 'El monto del préstamo debe ser mayor que cero.',
            'cuenta_banco.string' => 'Seleccione una cuenta contable válida para el banco.',
            'cuenta_deuda.string' => 'Seleccione una cuenta contable válida para la obligación financiera.',
            'cuenta_interes.string' => 'Seleccione una cuenta contable válida para el gasto financiero.',
            'cuenta_seguro.string' => 'Seleccione una cuenta contable válida para el gasto de seguro.',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $lineas = !empty($input['usar_lineas']) ? $this->prepararLineas($input['lineas'] ?? []) : [];
        $erroresLineas = $this->validarLineas($lineas);
        if ($erroresLineas) return redirect()->back()->withErrors(['lineas' => $erroresLineas])->withInput();
        $data = $request->only(array_keys($rules));
        $data['usar_lineas'] = !empty($lineas) ? 1 : 0;
        $data['lineas_json'] = json_encode($lineas);
        $prestamo = new AmortizacionPrestamo($data);
        $ids = array_values(array_filter([$data['cuenta_banco'] ?? null, $data['cuenta_deuda'] ?? null, $data['cuenta_interes'] ?? null, $data['cuenta_seguro'] ?? null]));
        $cuentasSeleccionadas = empty($ids) ? collect() : $this->cuentasDisponibles()->whereIn('plan', $ids);
        return view('amortizacion.create', [
            'cuentasSeleccionadas' => $cuentasSeleccionadas,
            'previewRows' => $lineas ? $this->calcularTablaLineas($lineas) : $this->calcularTabla($prestamo),
            'previewData' => $data,
        ]);
    }

    public function show($id)
    {
        $prestamo = AmortizacionPrestamo::where('id_empresa', session('id_empresa'))
            ->with(['detalles.linea', 'lineas'])->findOrFail($id);
        return view('amortizacion.show', compact('prestamo'));
    }

    public function store(Request $request)
    {
        $rules = [
            'institucion' => 'required|string|max:150',
            'numero_contrato' => 'nullable|string|max:80',
            'fecha_desembolso' => 'required|date',
            'fecha_primer_pago' => 'required|date|after_or_equal:fecha_desembolso',
            'monto' => 'required|numeric|min:0.01',
            'tasa_anual' => 'required|numeric|min:0|max:100',
            'plazo_meses' => 'required|integer|min:1|max:600',
            'metodo' => 'required|in:frances,aleman',
            'seguro_tipo' => 'required|in:ninguno,porcentaje_saldo,fijo',
            'seguro_valor' => 'nullable|numeric|min:0',
            'cuenta_banco' => 'required|string|max:50',
            'cuenta_deuda' => 'required|string|max:50',
            'cuenta_interes' => 'required|string|max:50',
            'cuenta_seguro' => 'nullable|string|max:50',
            'usar_lineas' => 'nullable|boolean',
            'lineas_json' => 'nullable|string',
        ];
        $input = $request->all();
        foreach (['cuenta_banco', 'cuenta_deuda', 'cuenta_interes', 'cuenta_seguro'] as $campo) {
            if (isset($input[$campo]) && trim((string) $input[$campo]) === '') $input[$campo] = null;
        }
        $validator = Validator::make($input, $rules, [
            'institucion.required' => 'Debe indicar el banco, cooperativa o institución acreedora.',
            'fecha_desembolso.required' => 'Debe indicar la fecha en que recibió el dinero.',
            'fecha_primer_pago.required' => 'Debe indicar la fecha programada del primer pago.',
            'fecha_primer_pago.after_or_equal' => 'La fecha del primer pago no puede ser anterior a la fecha de desembolso.',
            'monto.required' => 'Debe indicar el monto del préstamo.',
            'monto.numeric' => 'El monto debe ser un valor numérico.',
            'monto.min' => 'El monto del préstamo debe ser mayor que cero.',
            'tasa_anual.required' => 'Debe indicar la tasa anual del préstamo.',
            'plazo_meses.required' => 'Debe indicar el plazo en meses.',
            'cuenta_banco.required' => 'Seleccione la cuenta de banco que recibirá el desembolso.',
            'cuenta_banco.string' => 'La cuenta de banco seleccionada no es válida.',
            'cuenta_deuda.required' => 'Seleccione la cuenta de obligación financiera o cuenta por pagar.',
            'cuenta_deuda.string' => 'La cuenta de obligación financiera seleccionada no es válida.',
            'cuenta_interes.required' => 'Seleccione la cuenta de gasto financiero por intereses.',
            'cuenta_interes.string' => 'La cuenta de gasto financiero seleccionada no es válida.',
            'cuenta_seguro.string' => 'La cuenta de gasto de seguro seleccionada no es válida.',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $lineas = !empty($input['lineas_json']) ? json_decode($input['lineas_json'], true) : [];
        $lineas = !empty($input['usar_lineas']) ? $this->prepararLineas($lineas ?: []) : [];
        $erroresLineas = $this->validarLineas($lineas);
        if ($erroresLineas) return back()->withErrors(['lineas' => $erroresLineas])->withInput();
        $data = $request->only(array_keys($rules));
        unset($data['usar_lineas'], $data['lineas_json']);
        if ($lineas) {
            $data['monto'] = round(collect($lineas)->sum('monto'), 2);
            $data['tasa_anual'] = $lineas[0]['tasa_anual'];
            $data['plazo_meses'] = collect($lineas)->max('plazo_meses');
            $data['fecha_primer_pago'] = collect($lineas)->min('fecha_primer_pago');
            $data['metodo'] = $lineas[0]['metodo'];
            $data['seguro_tipo'] = 'ninguno';
            $data['seguro_valor'] = 0;
        }
        if ($data['seguro_tipo'] !== 'ninguno' && empty($data['cuenta_seguro'])) {
            return back()->withErrors(['cuenta_seguro' => 'Seleccione la cuenta contable del seguro.'])->withInput();
        }
        foreach (['cuenta_banco', 'cuenta_deuda', 'cuenta_interes'] as $campo) {
            $cuenta = $this->resolverCuentaPlan($data[$campo]);
            if ($cuenta === null) {
                return back()->withErrors([$campo => 'La cuenta seleccionada para ' . str_replace('_', ' ', $campo) . ' no existe en el plan de cuentas de la empresa activa.'])->withInput();
            }
            $data[$campo] = $cuenta;
        }
        if (!empty($data['cuenta_seguro'])) {
            $cuenta = $this->resolverCuentaPlan($data['cuenta_seguro']);
            if ($cuenta === null) {
                return back()->withErrors(['cuenta_seguro' => 'La cuenta seleccionada para cuenta_seguro no existe en el plan de cuentas de la empresa activa.'])->withInput();
            }
            $data['cuenta_seguro'] = $cuenta;
        }

        DB::beginTransaction();
        try {
            $empresa = session('id_empresa');
            $now = Carbon::now();
            $prestamo = AmortizacionPrestamo::create(array_merge($data, [
                'id_empresa' => $empresa,
                'tasa_anual' => $data['tasa_anual'],
                'seguro_valor' => $data['seguro_valor'] ?: 0,
                'id_usuariocrea' => auth()->id(),
                'id_usuariomod' => auth()->id(),
            ]));
            if ($lineas) {
                foreach ($lineas as $lineaData) {
                    $linea = AmortizacionPrestamoLinea::create(array_merge($lineaData, ['id_prestamo' => $prestamo->id]));
                    foreach ($this->calcularTablaValores($lineaData) as $row) {
                        $row['id_prestamo'] = $prestamo->id;
                        $row['id_linea'] = $linea->id;
                        AmortizacionPrestamoDetalle::create($row);
                    }
                }
            } else {
                foreach ($this->calcularTabla($prestamo) as $row) {
                    $row['id_prestamo'] = $prestamo->id;
                    AmortizacionPrestamoDetalle::create($row);
                }
            }
            DB::commit();
            return redirect()->route('amortizacion.index')->with('success', 'Préstamo y tabla de amortización guardados correctamente. Revise las opciones del préstamo en el listado.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['general' => 'No se pudo guardar el préstamo: ' . $e->getMessage()])->withInput();
        }
    }

    public function generateDisbursementEntry($id)
    {
        $prestamo = AmortizacionPrestamo::where('id_empresa', session('id_empresa'))->findOrFail($id);
        if ($prestamo->estado !== 'activo') {
            return back()->withErrors(['general' => 'No se puede generar el asiento porque el préstamo está anulado.']);
        }
        if ($prestamo->id_asiento_desembolso) {
            return back()->with('success', 'El asiento de desembolso ya fue generado: #' . $prestamo->id_asiento_desembolso . '.');
        }
        $cuentaBanco = $this->resolverIdPlanCuenta($prestamo->cuenta_banco);
        $cuentaDeuda = $this->resolverIdPlanCuenta($prestamo->cuenta_deuda);

        if ($cuentaBanco === null || $cuentaDeuda === null) {
            $faltantes = [];
            if ($cuentaBanco === null)
                $faltantes[] = 'Banco';
            if ($cuentaDeuda === null)
                $faltantes[] = 'Obligación financiera';
            return back()->withErrors(['general' => 'No se puede generar el asiento. La(s) cuenta(s) no existe(n) o no están asociadas a la empresa activa: ' . implode(', ', $faltantes) . '.']);
        }
        DB::beginTransaction();
        try {
            $codigoBanco = $this->resolverCuentaPlan($prestamo->cuenta_banco);
            $codigoDeuda = $this->resolverCuentaPlan($prestamo->cuenta_deuda);
            $asiento = $this->crearAsiento($prestamo->fecha_desembolso, $prestamo->monto, 'Desembolso préstamo ' . $prestamo->institucion, [
                [$cuentaBanco, 'Ingreso del préstamo al banco', $prestamo->monto, 0],
                [$cuentaDeuda, 'Reconocimiento de deuda con la institución', 0, $prestamo->monto],
            ]);
            $prestamo->update(['cuenta_banco' => $codigoBanco, 'cuenta_deuda' => $codigoDeuda, 'id_asiento_desembolso' => $asiento, 'id_usuariomod' => auth()->id()]);
            DB::commit();
            return back()->with('success', 'Asiento de desembolso generado correctamente: #' . $asiento . '.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['general' => 'No se pudo generar el asiento de desembolso: ' . $e->getMessage()]);
        }
    }

    public function cancel($id)
    {
        $prestamo = AmortizacionPrestamo::where('id_empresa', session('id_empresa'))->with('detalles')->findOrFail($id);
        if ($prestamo->estado !== 'activo') {
            return back()->with('success', 'El préstamo ya se encuentra anulado.');
        }
        if ($prestamo->detalles->where('estado', 'pagado')->count() > 0) {
            return back()->withErrors(['general' => 'No se puede anular el préstamo porque ya tiene cuotas pagadas.']);
        }
        DB::beginTransaction();
        try {
            $reverso = null;
            if ($prestamo->id_asiento_desembolso) {
                $resultado = LogAsiento::anularAsiento($prestamo->id_asiento_desembolso, 'AMORTIZACION', 'REVERSO DESEMBOLSO PRÉSTAMO ' . $prestamo->institucion);
                if (!isset($resultado['status']) || $resultado['status'] !== 'success') {
                    throw new \Exception(isset($resultado['exp']) ? $resultado['exp'] : 'No se pudo generar el asiento reverso.');
                }
                $reverso = $resultado['asiento'];
            }
            $prestamo->update(['estado' => 'cancelado', 'id_asiento_anulacion' => $reverso, 'id_usuariomod' => auth()->id()]);
            DB::commit();
            return back()->with('success', 'Préstamo anulado correctamente. La tabla se conservó como historial' . ($reverso ? ' y se generó el asiento reverso #' . $reverso . '.' : '.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['general' => 'No se pudo anular el préstamo: ' . $e->getMessage()]);
        }
    }

    public function pay(Request $request, $id)
    {
        $validator = Validator::make($request->all(), ['detalle_id' => 'required|integer', 'fecha_pago_real' => 'required|date'], [
            'detalle_id.required' => 'No se indicó la cuota que se desea pagar.',
            'fecha_pago_real.required' => 'Debe indicar la fecha real del pago.',
        ]);
        if ($validator->fails()) return back()->withErrors($validator);
        $prestamo = AmortizacionPrestamo::where('id_empresa', session('id_empresa'))->findOrFail($id);
        if ($prestamo->estado !== 'activo') {
            return back()->withErrors(['general' => 'No se puede registrar el pago porque el préstamo está anulado.']);
        }
        if (!$prestamo->id_asiento_desembolso) {
            return back()->withErrors(['general' => 'No se puede registrar el pago hasta generar el asiento contable del desembolso.']);
        }
        $detalle = $prestamo->detalles()->where('id', $request->detalle_id)->where('estado', 'pendiente')->firstOrFail();
        $cuentaBanco = $this->resolverIdPlanCuenta($prestamo->cuenta_banco);
        $cuentaDeuda = $this->resolverIdPlanCuenta($prestamo->cuenta_deuda);
        $cuentaInteres = $this->resolverIdPlanCuenta($prestamo->cuenta_interes);
        $cuentaSeguro = $this->resolverIdPlanCuenta($prestamo->cuenta_seguro);
        if ($cuentaBanco === null || $cuentaDeuda === null || $cuentaInteres === null || ((float) $detalle->seguro > 0 && $cuentaSeguro === null)) {
            $faltantes = [];
            if ($cuentaBanco === null) $faltantes[] = 'Banco';
            if ($cuentaDeuda === null) $faltantes[] = 'Obligación financiera';
            if ($cuentaInteres === null) $faltantes[] = 'Intereses';
            if ((float) $detalle->seguro > 0 && $cuentaSeguro === null) $faltantes[] = 'Seguro';
            return back()->withErrors(['general' => 'No se puede generar el asiento del pago. Revise estas cuentas: ' . implode(', ', $faltantes) . '.']);
        }
        DB::beginTransaction();
        try {
            $identificadorLinea = $detalle->linea ? ' línea ' . $detalle->linea->identificador : '';
            $lineas = [
                [$cuentaDeuda, 'Abono a capital cuota ' . $detalle->numero_cuota . $identificadorLinea, $detalle->capital, 0],
                [$cuentaInteres, 'Interés cuota ' . $detalle->numero_cuota . $identificadorLinea, $detalle->interes, 0],
            ];
            if ((float) $detalle->seguro > 0) {
                $lineas[] = [$cuentaSeguro, 'Seguro cuota ' . $detalle->numero_cuota . $identificadorLinea, $detalle->seguro, 0];
            }
            $lineas[] = [$cuentaBanco, 'Pago cuota ' . $detalle->numero_cuota . $identificadorLinea, 0, $detalle->total_cuota];
            $asiento = $this->crearAsiento($request->fecha_pago_real, $detalle->total_cuota, 'Pago cuota ' . $detalle->numero_cuota . $identificadorLinea . ' préstamo ' . $prestamo->institucion, $lineas);
            $detalle->update(['estado' => 'pagado', 'fecha_pago_real' => $request->fecha_pago_real, 'id_asiento_pago' => $asiento]);
            DB::commit();
            return back()->with('success', 'Pago registrado y asiento contable generado.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['general' => 'No se pudo registrar el pago: ' . $e->getMessage()]);
        }
    }

    private function cuentasDisponibles()
    {
        return DB::table('plan_cuentas_empresa as pe')->join('plan_cuentas as p', 'p.id', '=', 'pe.id_plan')
            ->where('pe.id_empresa', session('id_empresa'))->where('pe.estado', 2)
            ->orderBy('pe.plan')->get(['p.id as plan_id', 'pe.plan', 'pe.nombre']);
    }

    private function resolverCuentaPlan($valor)
    {
        if ($valor === null || trim((string) $valor) === '') return null;
        // El formulario recibe el código contable de plan_cuentas_empresa.plan.
        // ct_asientos_detalle usa el mismo código contable de plan_cuentas.id.
        // La relación correcta con la empresa es plan_cuentas_empresa.id_plan.
        $porCodigo = DB::table('plan_cuentas_empresa as pe')
            ->join('plan_cuentas as p', 'p.id', '=', 'pe.id_plan')
            ->where('pe.id_empresa', session('id_empresa'))
            ->where('pe.plan', $valor)
            ->first(['pe.plan']);
        if ($porCodigo) return $porCodigo->plan;
        // Compatibilidad con préstamos antiguos que guardaron el ID interno.
        $planDirecto = DB::table('plan_cuentas as p')
            ->join('plan_cuentas_empresa as pe', 'pe.id_plan', '=', 'p.id')
            ->where('pe.id_empresa', session('id_empresa'))
            ->where('p.id', $valor)
            ->first(['pe.plan']);
        if ($planDirecto) return $planDirecto->plan;
        $directa = DB::table('plan_cuentas as p')
            ->join('plan_cuentas_empresa as pe', 'pe.id_plan', '=', 'p.id')
            ->where('pe.id_empresa', session('id_empresa'))
            ->where('p.id', $valor)
            ->first(['pe.plan']);
        if ($directa) return $directa->plan;
        $asociada = DB::table('plan_cuentas_empresa as pe')
            ->join('plan_cuentas as p', 'p.id', '=', 'pe.id_plan')
            ->where('pe.id_empresa', session('id_empresa'))
            ->where(function ($q) use ($valor) {
                $q->where('pe.id', $valor)->orWhere('pe.plan', $valor);
            })
            ->first(['pe.plan']);
        return $asociada ? $asociada->plan : null;
    }

    private function resolverIdPlanCuenta($valor)
    {
        if ($valor === null || trim((string) $valor) === '') return null;
        $cuenta = DB::table('plan_cuentas_empresa as pe')
            ->join('plan_cuentas as p', 'p.id', '=', 'pe.id_plan')
            ->where('pe.id_empresa', session('id_empresa'))
            ->where('pe.plan', $valor)
            ->first(['p.id as id_plan_cuenta']);
        if ($cuenta) return $cuenta->id_plan_cuenta;

        // Compatibilidad con préstamos antiguos que guardaron pe.id o pe.id_plan.
        $cuentaAntigua = DB::table('plan_cuentas_empresa as pe')
            ->join('plan_cuentas as p', 'p.id', '=', 'pe.id_plan')
            ->where('pe.id_empresa', session('id_empresa'))
            ->where(function ($q) use ($valor) {
                $q->where('pe.id', $valor)->orWhere('pe.id_plan', $valor);
            })
            ->first(['p.id as id_plan_cuenta']);
        return $cuentaAntigua ? $cuentaAntigua->id_plan_cuenta : null;
    }

    private function prepararLineas(array $lineas)
    {
        $resultado = [];
        foreach ($lineas as $linea) {
            if (!is_array($linea)) continue;
            $identificador = trim((string) ($linea['identificador'] ?? ''));
            if ($identificador === '' && empty($linea['monto'])) continue;
            $resultado[] = [
                'identificador' => $identificador,
                'monto' => $linea['monto'] ?? 0,
                'tasa_anual' => $linea['tasa_anual'] ?? 0,
                'plazo_meses' => $linea['plazo_meses'] ?? 0,
                'numero_cuotas' => $linea['numero_cuotas'] ?? 0,
                'fecha_primer_pago' => $linea['fecha_primer_pago'] ?? null,
                'metodo' => $linea['metodo'] ?? 'frances',
                'seguro_tipo' => $linea['seguro_tipo'] ?? 'ninguno',
                'seguro_valor' => $linea['seguro_valor'] ?? 0,
            ];
        }
        return $resultado;
    }

    private function validarLineas(array $lineas)
    {
        if (!$lineas) return null;
        $identificadores = [];
        foreach ($lineas as $indice => $linea) {
            $validator = Validator::make($linea, [
                'identificador' => 'required|string|max:80',
                'monto' => 'required|numeric|min:0.01',
                'tasa_anual' => 'required|numeric|min:0|max:100',
                'plazo_meses' => 'required|integer|min:1|max:600',
                'numero_cuotas' => 'required|integer|min:1|max:600',
                'fecha_primer_pago' => 'required|date',
                'metodo' => 'required|in:frances,aleman',
                'seguro_tipo' => 'required|in:ninguno,porcentaje_saldo,fijo',
                'seguro_valor' => 'nullable|numeric|min:0',
            ]);
            if ($validator->fails()) {
                return 'Revise la línea ' . ($indice + 1) . ': ' . implode(' ', $validator->errors()->all());
            }
            if (in_array(strtolower($linea['identificador']), $identificadores)) {
                return 'El identificador de la línea ' . ($indice + 1) . ' está repetido.';
            }
            $identificadores[] = strtolower($linea['identificador']);
            if ($linea['seguro_tipo'] !== 'ninguno' && (float) $linea['seguro_valor'] <= 0) {
                return 'Indique el valor del seguro en la línea ' . ($indice + 1) . '.';
            }
        }
        return null;
    }

    private function calcularTablaLineas(array $lineas)
    {
        $rows = [];
        foreach ($lineas as $linea) {
            foreach ($this->calcularTablaValores($linea) as $row) {
                $row['identificador'] = $linea['identificador'];
                $rows[] = $row;
            }
        }
        return $rows;
    }

    private function calcularTablaValores(array $valores)
    {
        $saldo = (float) $valores['monto'];
        $tasa = ((float) $valores['tasa_anual'] / 100) / 12;
        $n = (int) $valores['numero_cuotas'];
        $cuotaFrancesa = $tasa == 0 ? $saldo / $n : $saldo * $tasa / (1 - pow(1 + $tasa, -$n));
        $fecha = Carbon::parse($valores['fecha_primer_pago']);
        $rows = [];
        for ($i = 1; $i <= $n; $i++) {
            $inicial = $saldo;
            $interes = $inicial * $tasa;
            $capital = $valores['metodo'] === 'aleman' ? (float) $valores['monto'] / $n : $cuotaFrancesa - $interes;
            if ($i === $n || $capital > $saldo) $capital = $saldo;
            $cuota = $capital + $interes;
            $seguro = $valores['seguro_tipo'] === 'porcentaje_saldo' ? $inicial * ((float) $valores['seguro_valor'] / 100) : ($valores['seguro_tipo'] === 'fijo' ? (float) $valores['seguro_valor'] : 0);
            $rows[] = ['numero_cuota' => $i, 'fecha_pago' => $fecha->copy(), 'saldo_inicial' => round($inicial, 2), 'cuota_base' => round($cuota, 2), 'interes' => round($interes, 2), 'capital' => round($capital, 2), 'seguro' => round($seguro, 2), 'total_cuota' => round($cuota + $seguro, 2), 'saldo_final' => round(max(0, $saldo - $capital), 2), 'estado' => 'pendiente'];
            $saldo = max(0, $saldo - $capital);
            $fecha->addMonth();
        }
        return $rows;
    }

    private function calcularTabla(AmortizacionPrestamo $p)
    {
        $saldo = (float) $p->monto;
        $tasa = ((float) $p->tasa_anual / 100) / 12;
        $n = (int) $p->plazo_meses;
        $cuotaFrancesa = $tasa == 0 ? $saldo / $n : $saldo * $tasa / (1 - pow(1 + $tasa, -$n));
        $fecha = Carbon::parse($p->fecha_primer_pago);
        $rows = [];
        for ($i = 1; $i <= $n; $i++) {
            $inicial = $saldo;
            $interes = $inicial * $tasa;
            $capital = $p->metodo === 'aleman' ? (float) $p->monto / $n : $cuotaFrancesa - $interes;
            if ($i === $n || $capital > $saldo) $capital = $saldo;
            $cuota = $capital + $interes;
            $seguro = $p->seguro_tipo === 'porcentaje_saldo' ? $inicial * ((float) $p->seguro_valor / 100) : ($p->seguro_tipo === 'fijo' ? (float) $p->seguro_valor : 0);
            $final = max(0, $saldo - $capital);
            $rows[] = ['numero_cuota' => $i, 'fecha_pago' => $fecha->copy(), 'saldo_inicial' => round($inicial, 2), 'cuota_base' => round($cuota, 2), 'interes' => round($interes, 2), 'capital' => round($capital, 2), 'seguro' => round($seguro, 2), 'total_cuota' => round($cuota + $seguro, 2), 'saldo_final' => round($final, 2), 'estado' => 'pendiente'];
            $saldo = $final;
            $fecha->addMonth();
        }
        return $rows;
    }

    private function crearAsiento($fecha, $valor, $observacion, array $lineas)
    {
        $ip = request()->ip();
        $usuario = auth()->id();
        $ahora = Carbon::now();
        $id = DB::table('ct_asientos_cabecera')->insertGetId(['fecha_asiento' => $fecha, 'id_empresa' => session('id_empresa'), 'observacion' => $observacion, 'valor' => $valor, 'modulo' => 'Amortización de préstamos', 'aparece_sri' => 0, 'estado' => 1, 'id_usuariocrea' => $usuario, 'id_usuariomod' => $usuario, 'ip_creacion' => $ip, 'ip_modificacion' => $ip, 'created_at' => $ahora, 'updated_at' => $ahora]);
        foreach ($lineas as $linea) DB::table('ct_asientos_detalle')->insert(['id_asiento_cabecera' => $id, 'id_plan_cuenta' => $linea[0], 'descripcion' => $linea[1], 'fecha' => $fecha, 'debe' => $linea[2], 'haber' => $linea[3], 'estado' => 1, 'id_usuariocrea' => $usuario, 'id_usuariomod' => $usuario, 'ip_creacion' => $ip, 'ip_modificacion' => $ip, 'created_at' => $ahora, 'updated_at' => $ahora]);
        return $id;
    }
}
