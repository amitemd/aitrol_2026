<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\hc_protocolo;

class ClinicalProtocolAttachmentService
{
    const DOCUMENT = 2;
    const STUDY = 3;

    public function store(Request $request, $type)
    {
        if (!in_array((int) $type, array(self::DOCUMENT, self::STUDY), true)) {
            throw new RuntimeException('El tipo de adjunto clínico no es válido.');
        }
        $request->validate(array(
            'id_hc_protocolo' => 'required|integer',
            'foto' => 'required|array|min:1|max:20',
            'foto.*' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,gif,bmp,dcm,doc,docx,xls,xlsx',
        ));

        $protocol = hc_protocolo::findOrFail($request->input('id_hc_protocolo'));
        $patientId = $protocol->historiaclinica->id_paciente;
        $actor = Auth::id();
        $ip = $request->ip();
        $stored = array();

        try {
            return DB::transaction(function () use ($request, $type, $patientId, $actor, $ip, &$stored) {
                $ids = array();
                foreach ($request->file('foto') as $file) {
                    $attachment = hc_imagenes_protocolo::create(array(
                        'id_hc_protocolo' => $request->input('id_hc_protocolo'),
                        'estado' => (int) $type,
                        'id_usuariocrea' => $actor,
                        'id_usuariomod' => $actor,
                        'ip_creacion' => $ip,
                        'ip_modificacion' => $ip,
                    ));
                    $extension = strtolower($file->getClientOriginalExtension());
                    $prefix = (int) $type === self::DOCUMENT ? 'hc_doc_' : 'hc_estudios_';
                    $filename = $prefix.$patientId.'_'.$request->input('id_hc_protocolo').'_'.date('Ymd').'_'.$attachment->id
                        .'_'.bin2hex(random_bytes(8)).'.'.$extension;
                    if (!Storage::disk('hc_ima')->put($filename, file_get_contents($file->getRealPath()))) {
                        throw new RuntimeException('No fue posible almacenar el adjunto clínico.');
                    }
                    $stored[] = $filename;
                    $attachment->nombre = $filename;
                    $attachment->nombre_anterior = basename($file->getClientOriginalName());
                    $attachment->save();
                    $ids[] = $attachment->id;
                }
                return $ids;
            });
        } catch (\Throwable $exception) {
            foreach ($stored as $filename) {
                Storage::disk('hc_ima')->delete($filename);
            }
            throw $exception;
        }
    }
}
