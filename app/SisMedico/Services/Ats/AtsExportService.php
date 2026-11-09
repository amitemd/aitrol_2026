<?php

namespace Sis_medico\Services\Ats;

use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Sis_medico\v3\contable\Utilidades;

class AtsExportService
{
    public function generate($request): array
    {
        try {
            if (!$request->isMethod('post')) throw new DomainException('La generación requiere POST.');
            $year = filter_var($request->anio,FILTER_VALIDATE_INT);
            $month = preg_match('/^(0?[1-9]|1[0-2])$/D',(string)$request->mes) ? (int)$request->mes : 0;
            if (!$year || $year < 2024 || !$month || $month < 1 || $month > 12 || $request->checkSemestral) {
                throw new DomainException('Seleccione un período mensual desde 2024. Los períodos históricos y semestrales requieren su propio catálogo.');
            }
            if ((string)$request->ats_confirmado !== '1') throw new DomainException('Confirme establecimientos, partes relacionadas y alcance de operaciones locales antes de generar.');
            $establishments = preg_split('/[\s,;]+/',trim((string)$request->ats_establecimientos),-1,PREG_SPLIT_NO_EMPTY);
            if (!$establishments || count($establishments) > 999 || count(array_unique($establishments)) !== count($establishments)) throw new DomainException('Indique los códigos únicos de establecimientos activos del RUC (ejemplo: 001,002).');
            foreach ($establishments as $code) if (!preg_match('/^\d{3}$/D',$code) || (int)$code < 1) throw new DomainException('Código de establecimiento inválido: '.$code);
            $related = preg_split('/[\s,;]+/',trim((string)$request->ats_relacionadas),-1,PREG_SPLIT_NO_EMPTY);
            foreach ($related as $id) if (!preg_match('/^(\d{10}|\d{10}001)$/D',$id)) throw new DomainException('Identificación de parte relacionada inválida: '.$id);
            if (!DB::getSchemaBuilder()->hasTable('ct_ats_exports')) throw new DomainException('Falta ejecutar la migración ct_ats_exports en el servidor.');
            $company = (string)session('id_empresa');
            if (!preg_match('/^\d{10}001$/D',$company)) throw new DomainException('Seleccione una empresa con RUC válido.');
            $id = DB::transaction(function () use ($company,$year,$month,$establishments,$related) {
                $data = (new AtsPeriodSource)->collect($company,$year,$month,$establishments,$related);
                $xml = (new AtsXmlBuilder)->build($data);
                $now = now(); $user = Auth::id(); $ip = Utilidades::getRealIP();
                $id = DB::table('ct_ats')->insertGetId(['periodo'=>sprintf('%04d/%02d',$year,$month),
                    'periodo1'=>null,'id_empresa'=>$company,'estado'=>1,'id_usuariocrea'=>$user,'id_usuariomod'=>$user,
                    'ip_creacion'=>$ip,'ip_modificacion'=>$ip,'created_at'=>$now,'updated_at'=>$now]);
                DB::table('ct_ats_exports')->insert(['ats_id'=>$id,'id_empresa'=>$company,
                    'filename'=>sprintf('AT%02d%04d.xml',$month,$year),'xml'=>$xml,'sha256'=>hash('sha256',$xml),
                    'manifest'=>json_encode(['policy'=>'all_transactions','source'=>$data,
                        'schema_sha256'=>hash_file('sha256',resource_path('sri/schemas/ats.xsd')),
                        'related'=>$related,'reviewed_by'=>$user],JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE),
                    'created_at'=>$now,'updated_at'=>$now]);
                return $id;
            });
            return ['status'=>'success','code'=>200,'message'=>'ATS generado y validado contra XSD. Descargue el ZIP y revise en DIMM/SRI antes de presentar.',
                'xml_url'=>url('v3/tributario/exportarXml/'.$id),'zip_url'=>url('v3/tributario/exportarAtsZip/'.$id)];
        } catch (DomainException $e) {
            return ['status'=>'error','code'=>422,'message'=>$e->getMessage()];
        } catch (\Throwable $e) {
            report($e);
            return ['status'=>'error','code'=>500,'message'=>'No se pudo generar el ATS. Revise el registro del servidor; no se guardó un archivo parcial.'];
        }
    }

    public function download($id, bool $zip = false)
    {
        abort_unless(ctype_digit((string)$id),404);
        abort_unless(DB::getSchemaBuilder()->hasTable('ct_ats_exports'),422,'Ejecute la migración y genere nuevamente el ATS.');
        $file = DB::table('ct_ats_exports as e')->join('ct_ats as a','a.id','=','e.ats_id')
            ->where('a.id',$id)->where('a.estado',1)->where('a.id_empresa',session('id_empresa'))
            ->where('e.id_empresa',session('id_empresa'))->first(['e.*']);
        abort_unless($file,404,'Este ATS no tiene archivo validado. Genérelo nuevamente para obtener XML y ZIP.');
        abort_unless(hash_equals($file->sha256,hash('sha256',$file->xml)),409,'La integridad del ATS no coincide. Genérelo nuevamente.');
        try { (new AtsXmlBuilder)->validate($file->xml); }
        catch (DomainException $e) { abort(422,$e->getMessage()); }
        if (!$zip) return response($file->xml,200,['Content-Type'=>'application/xml; charset=UTF-8',
            'Content-Disposition'=>'attachment; filename="'.$file->filename.'"','X-Content-Type-Options'=>'nosniff','Cache-Control'=>'private, no-store']);
        abort_unless(class_exists(\ZipArchive::class),503,'Habilite la extensión PHP zip en el servidor.');
        $path = tempnam(sys_get_temp_dir(),'ats-');
        if ($path === false) abort(503,'No se pudo crear el ZIP temporal.');
        $archive = new \ZipArchive();
        try {
            if ($archive->open($path,\ZipArchive::OVERWRITE) !== true) throw new \RuntimeException('No se pudo abrir el ZIP.');
            if (!$archive->addFromString($file->filename,$file->xml) || !$archive->close()) throw new \RuntimeException('No se pudo completar el ZIP.');
            return response()->download($path,str_replace('.xml','.zip',$file->filename),
                ['Content-Type'=>'application/zip','Cache-Control'=>'private, no-store'])->deleteFileAfterSend(true);
        } catch (\Throwable $e) { if (is_file($path)) unlink($path); throw $e; }
    }
}
