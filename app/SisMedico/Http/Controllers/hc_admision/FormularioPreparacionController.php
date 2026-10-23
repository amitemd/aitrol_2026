<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Sis_medico\Firma_Usuario;
use Sis_medico\hc_procedimientos;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\excelCreate;
use Sis_medico\Pentax;
use Sis_medico\Pentax_log;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Empresa;
use Sis_medico\Aud_Hc_Procedimientos;
use Illuminate\Support\Facades\Session;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Aud_Hc_Protocolo;
use Sis_medico\Agenda;
use Sis_medico\hc_protocolo;
class FormularioPreparacionController extends Controller
{

   public function __construct()
   {
      $this->middleware('auth');
   }

   public static function historiaPaciente($id_hc_procedimiento,$id_protocolo)
   {
      $protocolo = hc_protocolo::find($id_protocolo);
      $evolucion = Hc_Evolucion::where('hc_id_procedimiento', $id_hc_procedimiento)->orderBy('secuencia')->get();
      $hc_procedimiento = hc_procedimientos::find($id_hc_procedimiento);
      $historia = Historiaclinica::find($hc_procedimiento->id_hc);

      $paciente = isset($historia->paciente) ? $historia->paciente : null;
      $doctor = isset($historia->doctor_1) ? $historia->doctor_1 : null;
      $pentax = Pentax::where('hcid', $historia->hcid)->first();
      $agenda = Agenda::where('id',$pentax->id_agenda)->first();
      $hora_media = strtotime('+60 minute', strtotime($protocolo->hora_inicio));
      $hora_media = date('H:i', $hora_media);
      $hora_fin   = strtotime('+120 minute', strtotime($protocolo->hora_inicio));
      $hora_fin   = date('H:i', $hora_fin);
      $pentax_log = null;
      $data = [];
      $data["extubado"] = "";
      $data["intubado"] = "";
      $data["hosp"] = "";
      $data["intensivo"] = "";
      $data["emergencia"] = "";
      $data["domicilio"] = "";
      $data["morgue"] = "";
      $data["fecha_nacimiento"] = [];
      $data['doctor_dato'] = [];
      if (!is_null($pentax)) {
         $pentax_log = Pentax_log::where('estado_pentax', 3)->where("id_pentax", $pentax->id)->first();
         $data["fecha_recuperacion"] = date("Y-m-d ", strtotime($protocolo->hora_inicio));

         $data["hora_recuperacion"] = $hora_media;
         $data["hora_alta"] = $hora_fin;

         $fechaInicio = new DateTime(date("Y-m-d H:i:s", strtotime($paciente->fecha_nacimiento)));
         $fechaFormat = explode("-", $fechaInicio->format('Y-m-d'));
         $data["fecha_nacimiento"] =  $fechaFormat;
         $data['doctor_dato'] = $doctor;
         $fechaFin = new DateTime(date("Y-m-d H:i:s"));
         $intervalo = $fechaInicio->diff($fechaFin);
         $tipo_sexo = "MASCULINO";
         if ($paciente->sexo == 2) {
            $tipo_sexo = "FEMENINO";
         }


         $data["texto_recuperacion"] = "PACIENTE DE SEXO {$tipo_sexo} DE {$intervalo->y} AÑOS DE EDAD EGRESA DE QUIROFANO HEMODINAMICAMENTE ESTABLES, LUEGO DE REALIZACION DE ESTUFIO ENDOSCOPICO . PACIENTE EVOLUCIONA FAVORABLENETE POSTERIOR AL PROCEDIMIENTO";
         $data["texto_rec_prescripcion"] = "KETOROLACO 30 MG IV PRN";

         $data["texto_alta"] = "PACIENTE SE MANTIENE ESTABLE NO REFIERE DOLOR";
         $data["texto_alta_rec_prescripcion"] = "";
      }

      $hc_egreso = DB::table('hc_egreso')->where('id_hc_procedimientos', $hc_procedimiento->id)->first();
      $data["ta"] = "TA";
      $data["fc"] = "FC";
      $data["fr"] = "FR";
      $data["sat"] = "SAT";
      $data["t"] = "T°";
      $data["firma"] = " ";
      $data["id_seguro"] = $historia->id_seguro;
      if (!is_null($hc_procedimiento->id_empresa)) {
         $data["id_empresa"] = $hc_procedimiento->id_empresa;
      } else {
         $empresa = Empresa::where('prioridad', 1)->first();
         $data["id_empresa"] = $empresa->id;
      }



      if (!is_null($hc_egreso)) {
         $data["ta"] = "TA {$hc_egreso->ta}";
         $data["fc"] = "FC {$hc_egreso->fc}";
         $data["fr"] = "FR {$hc_egreso->fr}";
         $data["sat"] = "SAT {$hc_egreso->sato2}";
         $data["t"] = "T° {$hc_egreso->t}";




         if ($hc_egreso->condiciones == "1") {
            $data["extubado"] = "X";
         } else if ($hc_egreso->condiciones == "2") {
            $data["intubado"] = "X";
         }

         //'1: Domicilio; 
         //2: Hospitalizacion; 
         //3: UCI; 
         //4: critico er; 
         //5: Morgue'
         if ($hc_egreso->conducido == 1) {
            $data["domicilio"] = "X";
         } else if ($hc_egreso->conducido == 2) {
            $data["hosp"] = "X";
         } else if ($hc_egreso->conducido == 3) {
            $data["intensivo"] = "X";
         } else if ($hc_egreso->conducido == 4) {
            $data["emergencia"] = "X";
         } else if ($hc_egreso->conducido == 5) {
            $data["morgue"] = "X";
         }


         $firma_usuario = null;

         $usuario_Verificar = Aud_Hc_Procedimientos::where("id_procedimientos_org", $hc_procedimiento->id)->first();
         if (!is_null($usuario_Verificar)) {
            /* id_doctor_examinador */
            $firma_usuario = Firma_Usuario::where("id_usuario", $usuario_Verificar->id_doctor_examinador)->first();
         } else {
            $firma_usuario = Firma_Usuario::where("id_usuario", $hc_egreso->id_doctor_egreso)->first();
         }
         if (!is_null($firma_usuario)) {
            $data["firma"] = base_path() . '/storage/app/avatars/' . $firma_usuario->nombre;
         }
      }

      return ["paciente" => $paciente, "pentax_log" => $pentax_log, "data" => $data, "hc_egreso" => $hc_egreso];
   }


   public function excelPreparacion($id,$id_protocolo)
   {
      // $id_empresa   = $request->session()->get('id_empresa');

      Excel::create('Formulario Reparacion', function ($excel) use ($id,$id_protocolo) {
         $excel->sheet('Formulario Reparacion', function ($sheet) use ($id,$id_protocolo) {

            $final_excel = "BF";

            $consulta = FormularioPreparacionController::historiaPaciente($id,$id_protocolo);
            $empresa      = Empresa::where('id', $consulta["data"]["id_empresa"])->first();

            if ($empresa->logo != null && $consulta["data"]["id_seguro"] != "5") {
               $sheet->mergeCells('A1:D2');
               $objDrawing = new PHPExcel_Worksheet_Drawing;
               $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
               $objDrawing->setCoordinates('A1');
               $objDrawing->setHeight(100);
               $objDrawing->setWidth(150);
               $objDrawing->setWorksheet($sheet);
            }

            //"SALA DE RECUPERACION "
            $cabecera["data"] = ["SALA DE RECUPERACION"];
            $cabecera["num_inicial"]    = 3;
            $cabecera["num_final"]      = 3;
            $cabecera["letra_inicial"]  = "Q";
            $cabecera["letra_final"]    = $final_excel;
            $cabecera["background-color"]     = "#FFFFFF";
            excelCreate::mergeCells($sheet, $cabecera);

            $data["data"] = ["DATOS DEL ESTABLECIMIENTO Y USUARIO / PACIENTE"];
            $data["num_inicial"]    = 6;
            $data["num_final"]      = 6;
            $data["letra_inicial"]  = "A";
            $data["letra_final"]    = $final_excel;
            $data["background-color"]     = "#D6BEF6";
            //dd($data);
            excelCreate::mergeCells($sheet, $data);

            $cab1["data"] = ["PRIMER APELLIDO", "SEGUNDO APELLIDO", "PRIMER NOMBRE", "SEGUNDO NOMBRE"];
            $cab1["background-color"] = "#CBF6A9";
            $cab1["num_inicial"]    = 7;
            $cab1["num_final"]      = 8;
            $cab1["letra_inicial"]  = "A";
            $cab1["letra_final"]    = "J";
            $cab1["direccion"]      = "normal";
            $cab1["font-size"]      = 10;
            $excel_body = excelCreate::mergeCells($sheet, $cab1);

            if (!is_null($consulta["paciente"])) {
               $datos["data"] = [$consulta["paciente"]->apellido1, $consulta["paciente"]->apellido2, $consulta["paciente"]->nombre1, $consulta["paciente"]->nombre2];
               $datos["num_inicial"]    = 9;
               $datos["num_final"]      = 9;
               $datos["letra_inicial"]  = "A";
               $datos["letra_final"]    = "J";
               $datos["direccion"]      = "normal";
               $datos["font-size"]      = 10;
               $excel_datos = excelCreate::mergeCells($sheet, $datos);

               $fechaInicio = new DateTime(date("Y-m-d H:i:s", strtotime($consulta["paciente"]->fecha_nacimiento)));
               $fechaFin = new DateTime(date("Y-m-d H:i:s"));
               $intervalo = $fechaInicio->diff($fechaFin);

               $datos["data"]           = [$consulta["paciente"]->sexo == 2 ? "F" : "H", $intervalo->y];
               $datos["letra_inicial"]  = $excel_datos["letra_final"];
               $datos["letra_final"]    = $excel_datos["posicion"] + 3;
               $excel_datos = excelCreate::mergeCells($sheet, $datos);
            }


            $cab1["data"] = ["SEXO", "EDAD"];
            $cab1["letra_inicial"]  = $excel_body["letra_final"];
            $cab1["letra_final"]    = $excel_body["posicion"] + 3;
            $excel_body = excelCreate::mergeCells($sheet, $cab1);

            $letra_sexo = $excel_body["letra_final"];
            $posicion_sexo = $excel_body["posicion"];

            $cab1["data"] = ["FECHA NACIMIENTO"];
            $cab1["letra_inicial"]  = $excel_body["letra_final"];
            $cab1["letra_final"]    = $final_excel;
            $cab1["num_inicial"]    = 7;
            $cab1["num_final"]      = 7;


            $excel_body = excelCreate::mergeCells($sheet, $cab1);

            $cab1["data"] = ["Y", "M", "D"];
            $cab1["letra_inicial"]  = $letra_sexo;
            $cab1["letra_final"]    = $posicion_sexo + 2;
            $cab1["num_inicial"]    = 8;
            $cab1["num_final"]      = 8;
            $excel_body = excelCreate::mergeCells($sheet, $cab1);


            /**********CONDICION DE EDAD**********/

            $datos["data"] = [$consulta["data"]["fecha_nacimiento"][0], $consulta["data"]["fecha_nacimiento"][1], $consulta["data"]["fecha_nacimiento"][2]];
            $datos["letra_inicial"]  = $letra_sexo;
            $datos["letra_final"]    = $posicion_sexo + 2;
            $datos["num_inicial"]    = 9;
            $datos["num_final"]      = 9;
            $excel_body = excelCreate::mergeCells($sheet, $datos);

            /************************/


            $comienzo = 11;
            $data["data"] = ["EVOLUCIÓN POSTANESTÉSICA"];
            $data["num_inicial"]    = $comienzo;
            $data["num_final"]      = $comienzo;
            excelCreate::mergeCells($sheet, $data);
            $comienzo++;


            $cab1["data"] = ["FECHA", "HORA"];
            $cab1["num_inicial"]    = $comienzo;
            $cab1["num_final"]      = $comienzo;
            $cab1["letra_inicial"]  = "A";
            $cab1["letra_final"]    = "D";
            $excel_body = excelCreate::mergeCells($sheet, $cab1);
            $comienzo++;


            $cab1["data"] = ["EVOLUCIÓN", "PRESCRIPCION"];
            $cab1["letra_inicial"]  = $excel_body["letra_final"];
            $cab1["letra_final"]    = "AD";
            $excel_body = excelCreate::mergeCells($sheet, $cab1);

            $cab1["data"] = ["ADMINISTRACIÓN FÁRMACOS"];
            $cab1["letra_inicial"]  = $excel_body["letra_final"];
            $cab1["letra_final"]    = $final_excel;
            $excel_body = excelCreate::mergeCells($sheet, $cab1);
            //$sheet->getStyle($cab1)->getAlignment()->setWrapText(true);


            $datos["data"] = [$consulta["data"]["fecha_recuperacion"], $consulta["data"]["hora_recuperacion"]];
            $datos["num_inicial"]    = $comienzo;
            $datos["num_final"]      = 15;
            $datos["letra_inicial"]  = "A";
            $datos["letra_final"]    = "D";
            $recuperacion = excelCreate::mergeCells($sheet, $datos);

            $datos["data"] = [$consulta["data"]["texto_recuperacion"], $consulta["data"]["texto_rec_prescripcion"]];
            $datos["letra_inicial"]  = $recuperacion["letra_final"];
            $datos["letra_final"]    = "AD";
            $recuperacion = excelCreate::mergeCells($sheet, $datos);
            $comienzo++;

            $datos["data"] = ["", $consulta["data"]["hora_alta"]];
            $datos["num_inicial"]    = 16;
            $datos["num_final"]      = 18;
            $datos["letra_inicial"]  = "A";
            $datos["letra_final"]    = "D";
            $recuperacion = excelCreate::mergeCells($sheet, $datos);

            $datos["data"]           = [$consulta["data"]["texto_alta"], $consulta["data"]["texto_alta_rec_prescripcion"]];
            $datos["letra_inicial"]  = $recuperacion["letra_final"];
            $datos["letra_final"]    = "AD";
            $recuperacion = excelCreate::mergeCells($sheet, $datos);
            $comienzo++;



            $comienzo += 10;
            $data["data"] = ["CONDICIÓN DE EGRESO"];
            $data["num_inicial"]    = $comienzo;
            $data["num_final"]      = $comienzo;
            excelCreate::mergeCells($sheet, $data);
            $comienzo++;

            $text["data"] = ["CONSTANTES VITALES DE ENTREGA"];
            $text["num_inicial"]    = $comienzo;
            $text["num_final"]      = $comienzo;
            $text["letra_inicial"]  = "A";
            $text["letra_final"]    = "J";
            $excel_body = excelCreate::mergeCells($sheet, $text);


            $datos["data"] = [$consulta["data"]["ta"], $consulta["data"]["fc"], $consulta["data"]["fr"], $consulta["data"]["sat"], $consulta["data"]["t"]];
            $datos["num_inicial"]    = $comienzo;
            $datos["num_final"]      = $comienzo;
            $datos["letra_inicial"]  = "K";
            $datos["letra_final"]    = "S";
            $excel_body = excelCreate::mergeCells($sheet, $datos);



            $comienzo++;


            $text2["data"] = ["CONDICIONES AL SALIR"];
            $text2["num_inicial"]    = $comienzo;
            $text2["num_final"]      = $comienzo + 1;
            $text2["letra_inicial"]  = "A";
            $text2["letra_final"]    = "J";
            $excel_body = excelCreate::mergeCells($sheet, $text2);

            $text2["data"] = ["EXTUBADO"];
            $text2["num_inicial"]    = $comienzo;
            $text2["num_final"]      = $comienzo;
            $text2["letra_inicial"]  = "K";
            $text2["letra_final"]    = "O";
            $excel_body = excelCreate::mergeCells($sheet, $text2);

            $text2["data"] = [$consulta["data"]["extubado"]];
            $text2["num_inicial"]    = $comienzo;
            $text2["num_final"]      = $comienzo;
            $text2["letra_inicial"]  = "P";
            $text2["letra_final"]    = "Q";
            $excel_body = excelCreate::mergeCells($sheet, $text2);

            $text["data"] = ["CONDUCIDO A:"];
            $text["num_inicial"]    = $comienzo;
            $text["num_final"]      = $comienzo + 1;
            $text["letra_inicial"]  = "R";
            $text["letra_final"]    = "U";
            $excel_body = excelCreate::mergeCells($sheet, $text);

            $text["data"] = ["HOSPITALIZACIÓN"];
            $text["num_inicial"]    = $comienzo;
            $text["num_final"]      = $comienzo + 1;
            $text["letra_inicial"]  = "V";
            $text["letra_final"]    = "AA";
            $excel_body = excelCreate::mergeCells($sheet, $text);

            $text["data"] = [$consulta["data"]["hosp"]];
            $text["num_inicial"]    = $comienzo;
            $text["num_final"]      = $comienzo + 1;
            $text["letra_inicial"]  = "AB";
            $text["letra_final"]    = "AC";
            $excel_body = excelCreate::mergeCells($sheet, $text);

            $text["data"] = ["UNIDAD CUIDADOS INTENSIVOS"];
            $text["num_inicial"]    = $comienzo;
            $text["num_final"]      = $comienzo + 1;
            $text["letra_inicial"]  = "AD";
            $text["letra_final"]    = "AI";
            $excel_body = excelCreate::mergeCells($sheet, $text);

            $text["data"] = [$consulta["data"]["intensivo"]];
            $text["num_inicial"]    = $comienzo;
            $text["num_final"]      = $comienzo + 1;
            $text["letra_inicial"]  = "AJ";
            $text["letra_final"]    = "AK";
            $excel_body = excelCreate::mergeCells($sheet, $text);

            $text["data"] = ["CRÍTICOS DE EMERGENCIA"];
            $text["num_inicial"]    = $comienzo;
            $text["num_final"]      = $comienzo + 1;
            $text["letra_inicial"]  = "AL";
            $text["letra_final"]    = "AQ";
            $excel_body = excelCreate::mergeCells($sheet, $text);

            $text["data"] = [$consulta["data"]["emergencia"]];
            $text["num_inicial"]    = $comienzo;
            $text["num_final"]      = $comienzo + 1;
            $text["letra_inicial"]  = "AR";
            $text["letra_final"]    = "AS";
            $excel_body = excelCreate::mergeCells($sheet, $text);

            $text["data"] = ["DOMICILIO"];
            $text["num_inicial"]    = $comienzo;
            $text["num_final"]      = $comienzo + 1;
            $text["letra_inicial"]  = "AT";
            $text["letra_final"]    = "AV";
            $excel_body = excelCreate::mergeCells($sheet, $text);

            $text["data"] = [$consulta["data"]["domicilio"]];
            $text["num_inicial"]    = $comienzo;
            $text["num_final"]      = $comienzo + 1;
            $text["letra_inicial"]  = "AW";
            $text["letra_final"]    = "AX";
            $excel_body = excelCreate::mergeCells($sheet, $text);


            $text["data"] = ["MORGUE"];
            $text["num_inicial"]    = $comienzo;
            $text["num_final"]      = $comienzo + 1;
            $text["letra_inicial"]  = "AY";
            $text["letra_final"]    = "BD";
            $excel_body = excelCreate::mergeCells($sheet, $text);

            $text["data"] = [$consulta["data"]["morgue"]];
            $text["num_inicial"]    = $comienzo;
            $text["num_final"]      = $comienzo + 1;
            $text["letra_inicial"]  = "BE";
            $text["letra_final"]    = "BF";
            $excel_body = excelCreate::mergeCells($sheet, $text);

            $comienzo++;

            $text2["data"] = ["INTUBADO"];
            $text2["num_inicial"]    = $comienzo;
            $text2["num_final"]      = $comienzo;
            $text2["letra_inicial"]  = "K";
            $text2["letra_final"]    = "O";
            $excel_body = excelCreate::mergeCells($sheet, $text2);

            $text2["data"] = [$consulta["data"]["intubado"]];
            $text2["num_inicial"]    = $comienzo;
            $text2["num_final"]      = $comienzo;
            $text2["letra_inicial"]  = "P";
            $text2["letra_final"]    = "Q";
            $excel_body = excelCreate::mergeCells($sheet, $text2);


            $comienzo += 3;
            $data["data"] = ["DATOS DEL PROFESIONAL QUE AUTORIZA EL EGRESO"];
            $data["num_inicial"]    = $comienzo;
            $data["num_final"]      = $comienzo;
            $excel_body  = excelCreate::mergeCells($sheet, $data);
            $comienzo++;

            $cab1["data"] = ["FECHA (aaaa-mm-dd)", "HORA (hh:mm)"];
            $cab1["num_inicial"]    = $comienzo;
            $cab1["num_final"]      = $comienzo;
            $cab1["letra_inicial"]  = "A";
            $cab1["letra_final"]    = "E";
            $excel_body  = excelCreate::mergeCells($sheet, $cab1);
            $abajo = $excel_body["letra_final"];

            $cab1["data"] = [$consulta["data"]["fecha_recuperacion"], $consulta["data"]["hora_alta"]];
            $cab1["num_inicial"]    = $comienzo + 1;
            $cab1["num_final"]      = $comienzo + 1;
            $cab1["letra_inicial"]  = "A";
            $cab1["letra_final"]    = "E";
            $excel_body  = excelCreate::mergeCells($sheet, $cab1);



            $cab1["data"] = ["PRIMER NOMBRE", "PRIMER APELLIDO", "SEGUNDO APELLIDO"];
            $cab1["num_inicial"]    = $comienzo;
            $cab1["num_final"]      = $comienzo;
            $cab1["letra_inicial"]  = $excel_body["letra_final"];
            $cab1["letra_final"]    = $excel_body["posicion"] + 15;

            if (!is_null($consulta["paciente"])) {
               $datos["data"] = [$consulta["data"]['doctor_dato']->nombre1, $consulta["data"]["doctor_dato"]->apellido1, $consulta["data"]["doctor_dato"]->apellido2];
               $datos["num_inicial"]    = $comienzo + 1;
               $datos["num_final"]      = $comienzo + 1;
               $datos["letra_inicial"]  = $excel_body["letra_final"];
               $datos["letra_final"]    = $excel_body["posicion"] + 15;
               $excel_body  = excelCreate::mergeCells($sheet, $datos);
            }

            $excel_body  = excelCreate::mergeCells($sheet, $cab1);



            $comienzo += 2;
            $cab1["data"]           = [""];
            $cab1["num_inicial"]    = $comienzo;
            $cab1["num_final"]      = $comienzo;
            $cab1["letra_inicial"]  = "A";
            $cab1["letra_final"]    = "J";
            $excel_body  = excelCreate::mergeCells($sheet, $cab1);


            $cab1["data"]           = ["FIRMA Y SELLO DE MEDICO RESPONSABLE "];
            $cab1["num_inicial"]    = $comienzo;
            $cab1["num_final"]      = $comienzo;
            $cab1["letra_inicial"]  = "K";
            $cab1["letra_final"]    = $final_excel;
            $excel_body  = excelCreate::mergeCells($sheet, $cab1);
            $comienzo++;

            $text["data"] = [""];
            $text["num_inicial"]    = $comienzo;
            $text["num_final"]      = $comienzo + 3;
            $text["letra_inicial"]  = "A";
            $text["letra_final"]    = "J";
            $excel_body = excelCreate::mergeCells($sheet, $text);

            // $text["data"] = [$consulta["data"]["firma"]];
            // $text["num_inicial"]    = $comienzo;
            // $text["num_final"]      = $comienzo +1;
            // $text["letra_inicial"]  = "K";
            // $text["letra_final"]    = "BF";
            // $excel_body = excelCreate::mergeCells($sheet, $text);

            $firma_num = $comienzo + 3;

            $sheet->mergeCells("K" . $comienzo . ':' . "BF" . $firma_num);
            // $sheet->cell("K" . $comienzo, function ($cell) use ($consulta){
            //     $cell->setPath($consulta["data"]["firma"]);
            //     $cell->setFontColor($ );
            //     $cell->setBackground($background);
            //     $cell->setFontWeight('bold');
            //     $cell->setFontSize($font_size);
            //     $cell->setAlignment('center');
            //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
            // });

            // $sheet->cell('K' . $comienzo);
            // $objDrawing = new PHPExcel_Worksheet_Drawing;
            // if(!is_null($firma)){
            //    $objDrawing->setPath(base_path() . '/storage/app/avatars/' . $firma->nombre);
            //    $objDrawing->setCoordinates('K'.$i);
            //    $objDrawing->setWidthAndHeight(130,40);
            //    $objDrawing->setWorksheet($sheet);
            // }

            $objDrawing = new PHPExcel_Worksheet_Drawing;

            if (isset($consulta["data"]["firma"]) && trim($consulta["data"]["firma"]) != "") {

               $sheet->mergeCells("K" . $comienzo . ':' . "BF" . $firma_num);
               $objDrawing = new PHPExcel_Worksheet_Drawing;
               $objDrawing->setPath($consulta["data"]["firma"]);
               $objDrawing->setCoordinates('K' . $comienzo);
               $objDrawing->setWidthAndHeight(300, 230);
               $objDrawing->setWorksheet($sheet);
            }
         });

         $posicion = array(
            "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
            "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ",
            "BA", "BB", "BC", "BD", "BE", "BF", "BG", "BH", "BI", "BJ", "BK", "BL", "BM", "BN", "BO", "BP", "BQ", "BR", "BS", "BT", "BU", "BV", "BW", "BX", "BY", "BZ",
            "CA", "CB", "CC", "CD", "CE", "CF", "CG", "CH", "CI", "CJ", "CK", "CL", "CM", "CN", "CO", "CP", "CQ", "CR", "CS", "CT", "CU", "CV", "CW", "CX", "CY", "CZ"
         );

         for ($i = 0; $i < count($posicion); $i++) {
            $excel->getActiveSheet()->getColumnDimension($posicion[$i])->setWidth(2.8)->setAutosize(false);
         }
      })->export('xlsx');
   }
}
