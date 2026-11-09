<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\User;
use Sis_medico\v3\clinical\Order012;
use Sis_medico\v3\clinical\Order012Diagnosis;

class V3Order012Service
{
    private $booleanFields = array(
        'urgente', 'rutina', 'control', 'rx_convencional', 'rx_portatil',
        'tomografia', 'mamografia', 'resonancia', 'ecografia', 'procedimiento',
        'otros', 'paciente_contaminado', 'sedacion', 'puede_mover',
        'puede_retirar', 'medico_presente', 'toma_radio'
    );

    public function filters(Request $request)
    {
        $today = date('Y-m-d');
        $filters = array(
            'desde' => $this->date($request->input('desde', $today), $today),
            'hasta' => $this->date($request->input('hasta', $today), $today),
            'cedula' => trim((string) $request->input('cedula', '')),
            'paciente' => trim((string) $request->input('paciente', '')),
            'doctor' => trim((string) $request->input('doctor', '')),
        );
        if ($filters['desde'] > $filters['hasta']) {
            $start = $filters['hasta'];
            $filters['hasta'] = $filters['desde'];
            $filters['desde'] = $start;
        }
        return $filters;
    }

    public function paginate(array $filters, $companyId)
    {
        $query = Order012::query()->from('orden_012 as o12')
            ->leftJoin('paciente as p', 'p.id', '=', 'o12.id_paciente')
            ->leftJoin('empresa as e', 'e.id', '=', 'o12.id_empresa')
            ->leftJoin('users as d', 'd.id', '=', 'o12.id_doctor_firma')
            ->where('o12.id_empresa', $companyId)
            ->whereBetween('o12.created_at', array($filters['desde'].' 00:00:00', $filters['hasta'].' 23:59:59'))
            ->select(array(
                'o12.*', 'p.nombre1 as paciente_nombre1', 'p.nombre2 as paciente_nombre2',
                'p.apellido1 as paciente_apellido1', 'p.apellido2 as paciente_apellido2',
                'e.nombrecomercial as empresa_nombre', 'd.nombre1 as doctor_nombre1',
                'd.apellido1 as doctor_apellido1', 'd.apellido2 as doctor_apellido2'
            ));

        if ($filters['cedula'] !== '') {
            $query->where('o12.id_paciente', 'like', '%'.$this->escapeLike($filters['cedula']).'%');
        }
        if ($filters['doctor'] !== '') {
            $query->where('o12.id_doctor_firma', $filters['doctor']);
        }
        if ($filters['paciente'] !== '') {
            foreach (preg_split('/\s+/', $filters['paciente']) as $term) {
                if ($term === '') continue;
                $like = '%'.$this->escapeLike($term).'%';
                $query->where(function ($patient) use ($like) {
                    $patient->where('p.nombre1', 'like', $like)
                        ->orWhere('p.nombre2', 'like', $like)
                        ->orWhere('p.apellido1', 'like', $like)
                        ->orWhere('p.apellido2', 'like', $like);
                });
            }
        }

        return $query->orderBy('o12.created_at', 'desc')->paginate(25);
    }

    public function doctors()
    {
        return User::where('id_tipo_usuario', 3)->where('estado', 1)
            ->orderBy('apellido1')->orderBy('nombre1')->get();
    }

    public function find($id, $companyId)
    {
        return Order012::where('id', $id)->where('id_empresa', $companyId)->firstOrFail();
    }

    public function update($id, Request $request, $companyId, $userId, $ip)
    {
        $order = $this->find($id, $companyId);
        $doctorId = trim((string) $request->input('id_doctor_firma'));
        if ($doctorId === '' || !User::where('id', $doctorId)->where('estado', 1)->exists()) {
            throw new \InvalidArgumentException('Seleccione un médico activo para firmar la orden.');
        }
        $requestingDoctorId = trim((string) $request->input('id_doctor_solicita', $doctorId));
        if ($requestingDoctorId === '' || !User::where('id', $requestingDoctorId)->where('estado', 1)->exists()) {
            throw new \InvalidArgumentException('Seleccione un médico solicitante activo.');
        }

        $date = str_replace('T', ' ', trim((string) $request->input('fecha_orden')));
        if (!$this->validDateTime($date)) {
            throw new \InvalidArgumentException('La fecha de la orden no es válida.');
        }

        $data = array(
            'id_doctor_firma' => $doctorId,
            'id_doctor_solicita' => $requestingDoctorId,
            'fecha_orden' => $date,
            'referido' => $this->text($request->input('referido'), 250),
            'servicio' => $this->text($request->input('servicio'), 150),
            'texto_otros' => $this->text($request->input('texto_otros'), 255),
            'descripcion' => $this->text($request->input('descripcion'), 255),
            'motivo' => $this->text($request->input('motivo'), 4000),
            'cuadro_clinico' => $this->text($request->input('cuadro_clinico'), 10000),
            'id_usuariomod' => $userId,
            'ip_modificacion' => $ip,
        );
        foreach ($this->booleanFields as $field) {
            $data[$field] = $request->input($field) ? 1 : 0;
        }

        DB::transaction(function () use ($order, $data) {
            $order->update($data);
        });

        return $order->fresh();
    }

    public function diagnoses($orderId)
    {
        return Order012Diagnosis::where('id_orden_012', $orderId)->orderBy('id')->get()->map(function ($diagnosis) {
            $diagnosis->catalog_description = $this->diagnosisDescription($diagnosis->cie10);
            return $diagnosis;
        });
    }

    public function addDiagnosis($orderId, Request $request, $companyId, $userId, $ip)
    {
        $this->find($orderId, $companyId);
        $code = strtoupper(trim((string) $request->input('cie10')));
        $type = strtoupper(trim((string) $request->input('tipo')));
        if ($code === '' || $this->diagnosisDescription($code) === '') {
            throw new \InvalidArgumentException('El código CIE-10 indicado no existe.');
        }
        if (!in_array($type, array('PRESUNTIVO', 'DEFINITIVO'), true)) {
            throw new \InvalidArgumentException('Seleccione si el diagnóstico es presuntivo o definitivo.');
        }
        if (Order012Diagnosis::where('id_orden_012', $orderId)->where('cie10', $code)->exists()) {
            throw new \InvalidArgumentException('El diagnóstico ya está registrado en esta orden.');
        }

        return DB::transaction(function () use ($orderId, $code, $type, $userId, $ip) {
            return Order012Diagnosis::create(array(
                'id_orden_012' => $orderId,
                'cie10' => $code,
                'presuntivo_definitivo' => $type,
                'id_usuariocrea' => $userId,
                'id_usuariomod' => $userId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ));
        });
    }

    public function deleteDiagnosis($diagnosisId, $companyId)
    {
        $diagnosis = Order012Diagnosis::findOrFail($diagnosisId);
        $this->find($diagnosis->id_orden_012, $companyId);
        return DB::transaction(function () use ($diagnosis) {
            return $diagnosis->delete();
        });
    }

    public function searchDiagnoses($term)
    {
        $term = trim((string) $term);
        if (strlen($term) < 2) return array();
        $like = '%'.$this->escapeLike($term).'%';
        $rows3 = Cie_10_3::where('id', 'like', $like)->orWhere('descripcion', 'like', $like)->limit(12)->get(array('id', 'descripcion'));
        $rows4 = Cie_10_4::where('id', 'like', $like)->orWhere('descripcion', 'like', $like)->limit(12)->get(array('id', 'descripcion'));
        return $rows3->merge($rows4)->unique('id')->take(15)->values()->map(function ($row) {
            return array('id' => $row->id, 'text' => $row->id.' — '.$row->descripcion);
        })->all();
    }

    private function diagnosisDescription($code)
    {
        $row = Cie_10_3::find($code);
        if (!$row) $row = Cie_10_4::find($code);
        return $row ? (string) $row->descripcion : '';
    }

    private function date($value, $default)
    {
        $date = str_replace('/', '-', trim((string) $value));
        $parsed = \DateTime::createFromFormat('Y-m-d', $date);
        return $parsed && $parsed->format('Y-m-d') === $date ? $date : $default;
    }

    private function validDateTime($value)
    {
        foreach (array('Y-m-d H:i:s', 'Y-m-d H:i') as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date && $date->format($format) === $value) return true;
        }
        return false;
    }

    private function text($value, $length)
    {
        return mb_substr(trim(strip_tags((string) $value)), 0, $length);
    }

    private function escapeLike($value)
    {
        return str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $value);
    }
}
