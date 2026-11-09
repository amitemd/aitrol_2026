<?php

namespace Sis_medico\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Paciente;
use Sis_medico\v3\clinical\PendingBiopsy;

class V3PendingBiopsyService
{
    private $extensions = array('pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx');

    public function paginate($search, $companyId)
    {
        $search = trim((string) $search);
        $query = PendingBiopsy::query()->from('paciente_biopsia as pb')
            ->leftJoin('paciente as p', 'p.id', '=', 'pb.id_paciente')
            ->leftJoin('users as uc', 'uc.id', '=', 'pb.id_usuariocrea')
            ->where(function ($state) { $state->where('pb.estado', 0)->orWhereNull('pb.estado'); })
            ->where(function ($company) use ($companyId) {
                $company->where('pb.id_empresa', $companyId)->orWhereNull('pb.id_empresa');
            })
            ->select(array(
                'pb.*', 'p.nombre1 as paciente_nombre1', 'p.nombre2 as paciente_nombre2',
                'p.apellido1 as paciente_apellido1', 'p.apellido2 as paciente_apellido2',
                'p.fecha_nacimiento', 'uc.nombre1 as creador_nombre1', 'uc.apellido1 as creador_apellido1'
            ));
        if ($search !== '') {
            foreach (preg_split('/\s+/', $search) as $term) {
                if ($term === '') continue;
                $like = '%'.$this->escapeLike($term).'%';
                $query->where(function ($patient) use ($like) {
                    $patient->where('pb.id_paciente', 'like', $like)->orWhere('p.nombre1', 'like', $like)
                        ->orWhere('p.nombre2', 'like', $like)->orWhere('p.apellido1', 'like', $like)
                        ->orWhere('p.apellido2', 'like', $like);
                });
            }
        }
        return $query->orderBy('pb.created_at', 'desc')->paginate(25);
    }

    public function upload(array $files, $fixedPatientId, $companyId, $userId, $ip)
    {
        if (!$files) throw new \InvalidArgumentException('Seleccione al menos un archivo para cargar.');
        if (count($files) > 30) throw new \InvalidArgumentException('Puede cargar un máximo de 30 archivos por operación.');
        $fixedPatientId = trim((string) $fixedPatientId);
        $result = array('saved' => 0, 'errors' => array());
        foreach ($files as $file) {
            try {
                $this->storeOne($file, $fixedPatientId, $companyId, $userId, $ip);
                $result['saved']++;
            } catch (\Exception $exception) {
                $name = $file instanceof UploadedFile ? $file->getClientOriginalName() : 'Archivo desconocido';
                $result['errors'][] = $name.': '.$exception->getMessage();
            }
        }
        return $result;
    }

    public function findPending($id, $companyId)
    {
        $record = PendingBiopsy::where('id', $id)
            ->where(function ($state) { $state->where('estado', 0)->orWhereNull('estado'); })
            ->where(function ($company) use ($companyId) { $company->where('id_empresa', $companyId)->orWhereNull('id_empresa'); })
            ->first();
        if (!$record) abort(404, 'La biopsia pendiente no existe para la empresa activa.');
        return $record;
    }

    public function download($id, $companyId)
    {
        $record = $this->findPending($id, $companyId);
        if (!$record->nombre || !Storage::disk('hc_ima')->exists($record->nombre)) abort(404, 'El archivo físico de la biopsia no está disponible.');
        $downloadName = basename(str_replace('\\', '/', $record->nombre_original ?: $record->nombre));
        $path = storage_path('app/hc_ima/'.basename($record->nombre));
        return response()->download($path, $downloadName);
    }

    public function discard($id, $companyId, $userId, $ip)
    {
        $record = $this->findPending($id, $companyId);
        $record->update(array('estado' => 2, 'id_usuariomod' => $userId, 'ip_modificacion' => $ip));
        return $record;
    }

    private function storeOne($file, $fixedPatientId, $companyId, $userId, $ip)
    {
        if (!$file instanceof UploadedFile || !$file->isValid()) throw new \InvalidArgumentException('El archivo no llegó correctamente al servidor.');
        if ($file->getSize() <= 0 || $file->getSize() > 15728640) throw new \InvalidArgumentException('El archivo debe pesar menos de 15 MB.');
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $this->extensions, true)) throw new \InvalidArgumentException('Formato no permitido. Use PDF, JPG, PNG, DOC o DOCX.');
        $originalName = basename(str_replace('\\', '/', $file->getClientOriginalName()));
        $patientId = $fixedPatientId !== '' ? $fixedPatientId : trim(pathinfo($originalName, PATHINFO_FILENAME));
        if ($patientId === '' || !Paciente::where('id', $patientId)->exists()) throw new \InvalidArgumentException('No se encontró el paciente. Use su cédula como nombre del archivo o indíquela en el formulario.');
        $hash = hash_file('sha256', $file->getRealPath());
        if (PendingBiopsy::where('id_paciente', $patientId)->where('id_empresa', $companyId)->where('archivo_hash', $hash)->where('estado', '<>', 2)->exists()) {
            throw new \InvalidArgumentException('Este mismo documento ya fue cargado para el paciente.');
        }
        $storedName = 'hc_biopsias_'.$this->safeId($patientId).'_'.date('Ymd_His').'_'.uniqid().'.'.$extension;
        if (!Storage::disk('hc_ima')->put($storedName, file_get_contents($file->getRealPath()))) throw new \RuntimeException('No se pudo guardar el archivo en el almacenamiento clínico.');
        try {
            return DB::transaction(function () use ($patientId, $companyId, $userId, $ip, $storedName, $originalName, $hash) {
                return PendingBiopsy::create(array(
                    'id_paciente' => $patientId, 'id_empresa' => $companyId, 'nombre' => $storedName,
                    'nombre_original' => mb_substr($originalName, 0, 255), 'archivo_hash' => $hash,
                    'estado' => 0, 'id_usuariocrea' => $userId, 'id_usuariomod' => $userId,
                    'ip_creacion' => $ip, 'ip_modificacion' => $ip,
                ));
            });
        } catch (\Exception $exception) {
            Storage::disk('hc_ima')->delete($storedName);
            throw $exception;
        }
    }

    private function safeId($value)
    {
        return preg_replace('/[^A-Za-z0-9_-]/', '', $value);
    }

    private function escapeLike($value)
    {
        return str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $value);
    }
}
