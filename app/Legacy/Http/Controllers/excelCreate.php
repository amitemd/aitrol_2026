<?php
namespace Sis_medico\Http\Controllers;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Ct_Rol_Pagos;
use Sis_medico\User;
use Sis_medico\xavier;

class excelCreate extends Controller
{
    public static function details($sheet, $data){
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
                    "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ",
                    "BA", "BB", "BC", "BD", "BE", "BF", "BG", "BH", "BI", "BJ", "BK", "BL", "BM", "BN", "BO", "BP", "BQ", "BR", "BS", "BT", "BU", "BV", "BW", "BX", "BY", "BZ");
        
        $datos = $data["data"];
        $comienzo = isset($data["comienzo"])? $data["comienzo"] : 1;
        $background = isset($data['background-color']) ? $data['background-color'] : "#FCFCFC";
        $fuente = isset($data['color']) ? $data["color"]: "#000000";
        $font_weight = "";

        if(isset($data['mergue'])){
            $sheet->mergeCells($data["columna"]);
        }

        if(isset($data["font-weight"])){
            $font_weight = "bold";
        }
        $com = isset($data["letra"]) ?  array_search($data["letra"],$posicion,false) : 0;
        if(isset($data["letra"])){
            dd($com);
        }
        $contDatos = 0;

        for ($i = $com; $i < count($datos); $i++){
            
            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos, $i, $fuente, $background, $contDatos, $font_weight) {
                $cell->setValue($datos[$contDatos]);
                if($font_weight != ""){
                    $cell->setFontWeight('bold');
                }
                $cell->setBackground($background);
                $cell->setFontColor($fuente);
                $cell->setAlignment('center');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
            $contDatos ++;
        }
      
    }


    public static function mergeCells($sheet, $data){
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
        "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ",
        "BA", "BB", "BC", "BD", "BE", "BF", "BG", "BH", "BI", "BJ", "BK", "BL", "BM", "BN", "BO", "BP", "BQ", "BR", "BS", "BT", "BU", "BV", "BW", "BX", "BY", "BZ",
        "CA", "CB", "CC", "CD", "CE", "CF", "CG", "CH", "CI", "CJ", "CK", "CL", "CM", "CN", "CO", "CP", "CQ", "CR", "CS", "CT", "CU", "CV", "CW", "CX", "CY", "CZ");

        $letra_inical = $data["letra_inicial"];
        $letra_final = $data["letra_final"];
        $num_inicial = $data["num_inicial"];
        $num_final = $data["num_final"];
        $datos = $data["data"];
        

        $contDatos = 0;

        $comienzo = isset($data["comienzo"])? $data["comienzo"] : 1;
        $background = isset($data['background-color']) ? $data['background-color'] : "#FCFCFC";
        $fuente = isset($data['color']) ? $data["color"]: "#000000";
        $font_size = isset($data['font-size']) ? $data["font-size"] : '10';
        $font_weight = "";

        if(isset($data["direccion"]) and $data["direccion"] == "normal"){
            if(gettype($letra_inical) == "integer"){
                $key_inicial = $letra_inical;
            }else{
                $key_inicial = array_search($letra_inical, $posicion);

            }
            if(gettype($letra_final) == "integer"){
                $key_final = $letra_final;
            }else{
                $key_final = array_search($letra_final, $posicion);
            }

            for ($i = 0; $i < count($datos); $i++){
                
                $sheet->mergeCells($posicion[$key_inicial] . $num_inicial . ':' . $posicion[$key_final] . $num_final);
                $sheet->getStyle($posicion[$key_inicial] . $num_inicial . ':' . $posicion[$key_final] . $num_final)->getAlignment()->setWrapText(true);
                $sheet->cell($posicion[$key_inicial] . $num_inicial, function ($cell) use ($datos, $i, $fuente, $background, $font_size, $font_weight){
                    $cell->setValue($datos[$i]);
                    $cell->setFontColor($fuente);
                    $cell->setBackground($background);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize($font_size);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;
                $aux_inical = $key_inicial;
                $key_inicial = $key_final + 1;
                $key_final   = $key_final + ($key_final - $aux_inical) + 1;
                
            }
            $letra_final = $posicion[$key_inicial];
            $num_posicion = $key_inicial;
        }else{
            for ($i = 0; $i < count($datos); $i++){

                $sheet->mergeCells($letra_inical . $num_inicial . ':' . $letra_final . $num_final);
                $sheet->getStyle($letra_inical . $num_inicial . ':' . $letra_final . $num_final)->getAlignment()->setWrapText(true);
                $sheet->cell($letra_inical . $num_inicial, function ($cell) use ($datos, $i, $fuente, $background, $contDatos, $font_size){
                    $cell->setValue($datos[$i]);
                    $cell->setFontColor($fuente);
                    $cell->setBackground($background);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize($font_size);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;
                $aux_inical = $num_inicial;
                $num_inicial = $num_final + 1;
                $num_final   = $num_final + ($num_final - $aux_inical) + 1;
                $num_posicion = $num_final;

                $letra_final = $letra_inical;
                $num_posicion = $num_inicial;
            }
        }
        
        return ["letra_final" => $letra_final, "posicion" => $num_posicion];
    }

}