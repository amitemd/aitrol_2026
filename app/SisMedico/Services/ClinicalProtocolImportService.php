<?php

namespace Sis_medico\Services;

use Excel;
use Sis_medico\Excel_Valores;
use Sis_medico\Paciente;

class ClinicalProtocolImportService
{
    public function import()
    {

        ini_set('max_execution_time', 100);

        Excel::filter('chunk')->load('subevalor.xlsx')->chunk(250, function ($reader) {

            foreach ($reader as $book) {

                //dd($book);
                //$anio = date("Y",strtotime($book->fecha_proced));
                //$mes = date("d",strtotime($book->fecha_proced));
                $anio = $book->anio;
                $mes  = $book->mes;

                $arrf = explode('/', $book->fecha_proced);

                $nombre = ltrim($book->paciente);
                $nombre = rtrim($nombre);
                $nombre = trim($nombre, " \t\n\r");
                $nombre = str_replace('_x000D_', '', $nombre);

                /*if($book->id == 8058){
                dd($book, $arrf);
                }*/
                $array = [
                    'anio'                => $anio,
                    'mes'                 => $mes,
                    'id_subida'           => $book->id,
                    'fecha_procedimiento' => $arrf[2] . '/' . $arrf[1] . '/' . $arrf[0],
                    'fecha'               => $arrf[2] . '/' . $arrf[1] . '/' . $arrf[0],
                    'procedimiento'       => $book->proced,
                    'tipo'                => $book->tipo,
                    'paciente'            => $nombre,
                    'seguro'              => $book->seguro,
                    'id_seguro'           => $book->id_seguro,
                    'valor'               => $book->valor,
                    'grupo'               => $book->proc,
                    'id_usuariocrea'      => '0922290697',
                    'id_usuariomod'       => '0922290697',
                    'ip_creacion'         => 'SISTEMAS',
                    'ip_modificacion'     => 'SISTEMAS',

                ];

                //dd($array);

                $excel = Excel_Valores::where('anio', $anio)->where('mes', $mes)->where('id_subida', $book->id)->first();
                //dd($excel);
                if (is_null($excel)) {

                    Excel_Valores::create($array);
                } else {

                    $nombre      = ltrim($book->paciente);
                    $nombre      = rtrim($nombre);
                    $nombre      = trim($nombre, " \t\n\r");
                    $nombre      = str_replace('_x000D_', '', $nombre);
                    $e_nombre    = explode(' ', $nombre);
                    $nombres_sql = '';
                    $cantidad    = count($e_nombre);
                    foreach ($e_nombre as $n) {
                        $nombres_sql = $nombres_sql . '%' . $n;
                    }
                    $nombres_sql = $nombres_sql . '%';
                    //dd($nombres_sql);

                    if ($cantidad == '2' || $cantidad == '3') {
                        $paciente = Paciente::where(function ($jq1) use ($nombres_sql) {
                            $jq1->orwhereraw('CONCAT(apellido1," ",apellido2," ",nombre1," ",nombre2) LIKE ?', [$nombres_sql])
                                ->orwhereraw('CONCAT(nombre1," ",nombre2," ",apellido1," ",apellido2) LIKE ?', [$nombres_sql])
                                ->orwhereraw('CONCAT(nombre1," ",apellido1," ",apellido2) LIKE ?', [$nombres_sql])
                                ->orwhereraw('CONCAT(apellido1," ",apellido2," ",nombre1) LIKE ?', [$nombres_sql]);
                        })->get();

                    } else {

                        $paciente = Paciente::where(function ($jq1) use ($nombres_sql) {
                            $jq1->orwhereraw('CONCAT(apellido1," ",apellido2," ",nombre1," ",nombre2) LIKE ?', [$nombres_sql])
                                ->orwhereraw('CONCAT(nombre1," ",nombre2," ",apellido1," ",apellido2) LIKE ?', [$nombres_sql]);
                        })->get();
                    }

                    if ($paciente->count() == 1) {
                        $id_cedula = $paciente[0]->id;

                        $arr2 = [

                            'paciente'    => $nombre,
                            'id_paciente' => $id_cedula,
                            'ip_creacion' => 'SISTEMAS2',
                        ];

                        $excel->update($arr2);
                    }

                }
            }
        });

        return "ok";
    }

}
