<?php

namespace Sis_medico\Http\Controllers\v3\clinical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Http\Controllers\Controller;

class PatientAgendaArchiveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, $patientId)
    {
        $companyId = $this->companyId($request);
        $patient = $this->patientForCompany($patientId, $companyId);
        $archives = DB::table('agenda_archivo as archive')
            ->join('agenda as appointment', 'appointment.id', '=', 'archive.id_agenda')
            ->leftJoin('users as doctor', 'doctor.id', '=', 'appointment.id_doctor1')
            ->where('appointment.id_paciente', $patient->id)->where('appointment.id_empresa', $companyId)
            ->where(function ($query) {
                $query->where(function ($file) {
                    $file->whereNotNull('archive.archivo')->where('archive.archivo', '<>', '');
                })->orWhere(function ($text) {
                    $text->whereNotNull('archive.texto')->where('archive.texto', '<>', '');
                });
            })
            ->select('archive.id', 'archive.id_agenda', 'archive.tipo_documento', 'archive.descripcion',
                'archive.archivo', 'archive.texto', 'archive.created_at', 'appointment.fechaini',
                'doctor.nombre1 as doctor_name', 'doctor.apellido1 as doctor_lastname')
            ->orderBy('appointment.fechaini', 'desc')->orderBy('archive.id', 'desc')
            ->paginate(16);

        return $this->page('v3.clinical.registry.patient_agenda_archives', array(
            'patient' => $patient, 'archives' => $archives,
        ));
    }

    public function legacy(Request $request, $patientId)
    {
        $this->patientForCompany($patientId, $this->companyId($request));
        return redirect()->route('v3.patient-agenda-archives.index', array('patient' => $patientId));
    }

    public function file(Request $request, $patientId, $archiveId)
    {
        $archive = $this->archiveForCompany($patientId, $archiveId, $this->companyId($request));
        $name = basename((string) $archive->archivo);
        if ($name === '' || !Storage::disk('hc_agenda')->exists($name)) abort(404, 'El archivo ya no está disponible.');
        $path = storage_path('app/hc_agenda/'.$name);
        return response()->file($path, array(
            'Content-Type' => $this->mimeType($path),
            'Content-Disposition' => 'inline; filename="'.$this->safeDownloadName($name).'"',
            'Content-Security-Policy' => "default-src 'none'; img-src 'self' data:; media-src 'self'; style-src 'unsafe-inline'",
            'X-Content-Type-Options' => 'nosniff',
        ));
    }

    public function download(Request $request, $patientId, $archiveId)
    {
        $archive = $this->archiveForCompany($patientId, $archiveId, $this->companyId($request));
        $name = basename((string) $archive->archivo);
        if ($name === '' || !Storage::disk('hc_agenda')->exists($name)) abort(404, 'El archivo ya no está disponible.');
        return response()->download(storage_path('app/hc_agenda/'.$name), $this->safeDownloadName($name));
    }

    public function text(Request $request, $patientId, $archiveId)
    {
        $archive = $this->archiveForCompany($patientId, $archiveId, $this->companyId($request));
        if (trim((string) $archive->texto) === '') abort(404, 'El contenido ya no está disponible.');
        return response((string) $archive->texto, 200, array(
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Security-Policy' => "default-src 'none'; img-src data:; style-src 'unsafe-inline'; font-src data:",
            'X-Content-Type-Options' => 'nosniff',
        ));
    }

    public function destroy(Request $request, $patientId, $archiveId)
    {
        $companyId = $this->companyId($request);
        $archive = $this->archiveForCompany($patientId, $archiveId, $companyId);
        $name = basename((string) $archive->archivo);

        if ($name === '') {
            return response()->json(array(
                'message' => 'Este registro no corresponde a un documento subido.',
            ), 422);
        }

        DB::transaction(function () use ($archive) {
            DB::table('agenda_archivo')->where('id', $archive->id)->delete();
        });

        try {
            if (Storage::disk('hc_agenda')->exists($name) && !Storage::disk('hc_agenda')->delete($name)) {
                Log::warning('No se pudo eliminar el archivo físico de una agenda.', array(
                    'agenda_archivo_id' => $archive->id,
                    'archivo' => $name,
                ));
            }
        } catch (\Exception $exception) {
            Log::warning('El documento de agenda fue eliminado del historial, pero no del almacenamiento.', array(
                'agenda_archivo_id' => $archive->id,
                'archivo' => $name,
                'error' => $exception->getMessage(),
            ));
        }

        return response()->json(array(
            'message' => 'Documento eliminado correctamente.',
            'remaining' => $this->archiveCountForCompany($patientId, $companyId),
        ));
    }

    private function patientForCompany($patientId, $companyId)
    {
        $patient = DB::table('paciente as patient')->join('agenda as appointment', 'appointment.id_paciente', '=', 'patient.id')
            ->where('patient.id', trim((string) $patientId))->where('appointment.id_empresa', $companyId)
            ->select('patient.id', 'patient.nombre1', 'patient.nombre2', 'patient.apellido1', 'patient.apellido2')
            ->first();
        if (!$patient) abort(404, 'El paciente no tiene agendas en la empresa activa.');
        return $patient;
    }

    private function archiveForCompany($patientId, $archiveId, $companyId)
    {
        $archive = DB::table('agenda_archivo as archive')->join('agenda as appointment', 'appointment.id', '=', 'archive.id_agenda')
            ->where('archive.id', (int) $archiveId)->where('appointment.id_paciente', trim((string) $patientId))
            ->where('appointment.id_empresa', $companyId)
            ->select('archive.id', 'archive.archivo', 'archive.texto')->first();
        if (!$archive) abort(404, 'El documento no pertenece al paciente y empresa activos.');
        return $archive;
    }

    private function archiveCountForCompany($patientId, $companyId)
    {
        return DB::table('agenda_archivo as archive')
            ->join('agenda as appointment', 'appointment.id', '=', 'archive.id_agenda')
            ->where('appointment.id_paciente', trim((string) $patientId))
            ->where('appointment.id_empresa', $companyId)
            ->where(function ($query) {
                $query->where(function ($file) {
                    $file->whereNotNull('archive.archivo')->where('archive.archivo', '<>', '');
                })->orWhere(function ($text) {
                    $text->whereNotNull('archive.texto')->where('archive.texto', '<>', '');
                });
            })
            ->count();
    }

    private function companyId(Request $request)
    {
        $id = trim((string) $request->session()->get('id_empresa'));
        if ($id === '') abort(403, 'Seleccione una empresa activa.');
        return $id;
    }

    private function mimeType($path)
    {
        $mime = function_exists('mime_content_type') ? @mime_content_type($path) : null;
        return $mime ?: 'application/octet-stream';
    }

    private function safeDownloadName($name)
    {
        $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', (string) $name);
        return $safe !== '' ? $safe : 'documento';
    }

    private function page($view, array $payload)
    {
        $current = config('data');
        $data = array('controlador' => 'pacientes', 'carpeta' => 'clinical', 'submodulo' => 'archivos-agenda', 'submodulo_' => 'archivos-agenda',
            'menu_id' => 0, 'titulo' => 'Documentos de agenda', 'title' => 'Documentos de agenda | Aitrol Hospitalario', 'vista' => $view,
            'obseraudi' => 'Consulta de documentos agregados en agenda V3', 'contenido' => view($view, $payload));
        config(array('data' => array_merge(is_array($current) ? $current : array(), $data)));
        return view('v3.layouts.view_child');
    }
}
