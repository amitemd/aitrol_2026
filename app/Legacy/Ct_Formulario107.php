<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Formulario107 extends Model
{
    protected $table = 'ct_formulario_107';
    protected $guarded = [];

    public static function generarXML($r)
    {
        $formularios = Ct_Formulario107::where('periodo', $r->periodo)
            ->where('id_empresa', session('id_empresa'))
            ->get();
        $xml = new \DOMDocument('1.0', 'ISO-8859-1');
        $xml->xmlStandalone = true;
        $rdep = Ct_Formulario107::generarDocumento($xml, $formularios);

        $xml->appendChild($rdep);

        $xml->formatOutput = true;
        $fileName = 'RDEP' . $r->periodo . '.xml';
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");
        //output the XML data
        $comprobante = $xml->saveXml();
        echo $comprobante;
        exit();
    }
    private static function generarDocumento($xml, $formulario)
    {
        $rdep = $xml->createElement('rdep');
        $rdep->appendChild($xml->createElement('numRuc', session('id_empresa')));
        $rdep->appendChild($xml->createElement('anio', $formulario[0]->periodo));
        $retRelDep = $xml->createElement('retRelDep');

        if (count($formulario) > 0) {
            foreach ($formulario as $row) {
                $dempleado = Ct_Nomina::where('id_user', $row->identificacion)->first();
                $datos = json_decode($row->datos);
                $datRetRelDep = $xml->createElement('datRetRelDep');
                $empleado = $xml->createElement('empleado');
                $empleado->appendChild($xml->createElement('benGalpg', 'NO'));
                $empleado->appendChild($xml->createElement('tipIdRet', 'C'));
                $empleado->appendChild($xml->createElement('idRet', $row->identificacion));
                $empleado->appendChild($xml->createElement('apellidoTrab', $datos->apellido));
                $empleado->appendChild($xml->createElement('nombreTrab', $datos->nombre));
                $empleado->appendChild($xml->createElement('estab', '001'));
                $empleado->appendChild($xml->createElement('residenciaTrab', '01'));
                $empleado->appendChild($xml->createElement('paisResidencia', '593'));
                $empleado->appendChild($xml->createElement('aplicaConvenio', 'NA'));
                $empleado->appendChild($xml->createElement('tipoTrabajDiscap', $dempleado->check_discapacidad == 1 ? '01' : '02'));
                $empleado->appendChild($xml->createElement('porcentajeDiscap', $dempleado->porcentaje_discapacidad > 0 ? $dempleado->porcentaje_discapacidad : 0));
                $empleado->appendChild($xml->createElement('tipIdDiscap', 'N'));
                $empleado->appendChild($xml->createElement('idDiscap', '999'));
                $datRetRelDep->appendChild($empleado);
                $datRetRelDep->appendChild($xml->createElement('suelSal', $datos->sueldo));
                $datRetRelDep->appendChild($xml->createElement('sobSuelComRemu', $datos->sobresueldos));
                $datRetRelDep->appendChild($xml->createElement('partUtil', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('intGrabGen', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('impRentEmpl', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('decimTer', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('decimCuar', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('fondoReserva', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('salarioDigno', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('otrosIngRenGrav', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('ingGravConEsteEmpl', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('apoPerIess', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('aporPerIessConOtrosEmpls', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('deducVivienda', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('deducSalud', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('deducEducartcult', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('deducAliement', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('deducVestim', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('deduccionTurismo', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('exoDiscap', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('exoTerEd', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('basImp', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('impRentCaus', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('rebajaGastosPersonales', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('impuestoRentaRebajaGastosPersonales', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('valRetAsuOtrosEmpls', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('valImpAsuEsteEmpl', $datos->participacion));
                $datRetRelDep->appendChild($xml->createElement('valRet', $datos->participacion));
                $retRelDep->appendChild($datRetRelDep);
            }
        }
        $rdep->appendChild($retRelDep);
        return $rdep;
    }
}
