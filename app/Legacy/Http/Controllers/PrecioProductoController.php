<?php

namespace Sis_medico\Http\Controllers\contable;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\PrecioProducto;

class PrecioProductoController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // $data = PrecioProductoController::query();
        // dd($data);
        return view('contable.ultimo_precio.index', /*['thead' => $data['thead'], 'tbody' => $data['tbody']]*/);
    }

    public function buscador(Request $request)
    {
        $data = PrecioProductoController::query();

        return ['thead' => $data['thead'], 'tbody' => $data['tbody']];
    }


    public static function query()
    {
        $title = "";
        $tbody = "";

        $arrTitle = ["Productos"];
        $arrDetalle = [];

        $productos = DB::select("SELECT  cdv.id, cdv.id_ct_productos AS codigo, cdv.nombre, cdv.precio, cdv.cantidad, cv.fecha
              FROM ct_detalle_venta cdv
              INNER JOIN ct_ventas cv ON cdv.id_ct_ventas = cv.id
              INNER JOIN ct_asientos_cabecera cac ON cv.id_asiento = cac.id
              INNER JOIN (
                SELECT id, MAX(id) AS ultima_fecha
                FROM ct_detalle_venta
                GROUP BY id_ct_productos
              ) ultima_venta
              ON cdv.id = ultima_venta.id
              WHERE cv.id_empresa LIKE '" . Session::get('id_empresa') . "'");
        $clientes = DB::select('SELECT cc.identificacion, cc.nombre
              FROM ct_ventas cv 
              INNER JOIN ct_clientes cc ON cv.id_cliente = cc.identificacion
              GROUP BY cc.identificacion');
        // echo "<!DOCTYPE html>
        //       <html>
        //       <head>
        //       <style>
        //       table {
        //         font-family: arial, sans-serif;
        //         border-collapse: collapse;
        //         width: 100%;
        //       }

        //       td, th {
        //         border: 1px solid #dddddd;
        //         text-align: left;
        //         padding: 8px;
        //       }

        //       tr:nth-child(even) {
        //         background-color: #dddddd;
        //       }
        //       </style>
        //       </head>
        //       <body>";
        // echo "<table>
        //       <tr>
        $title .= "<td>Productos</td>";
        foreach ($clientes as $value) {
            $title .= "<td>" . $value->nombre . "</td>";
            array_push($arrTitle, $value->nombre);
        }
        // echo "</tr>";
        foreach ($productos as $producto) {
            $lineArr = [];
            $lineArr = ["({$producto->codigo}) {$producto->nombre}"];
            $tbody .= "<tr><td> (" . $producto->codigo . ") " . $producto->nombre . " </td>";

            foreach ($clientes as $cliente) {
                $valor = DB::select('SELECT  cdv.id, cdv.id_ct_productos AS codigo, cdv.nombre, cdv.precio, cdv.cantidad, cv.fecha
                      FROM ct_detalle_venta cdv
                      INNER JOIN ct_ventas cv ON cdv.id_ct_ventas = cv.id
                      INNER JOIN ct_asientos_cabecera cac ON cv.id_asiento = cac.id
                      INNER JOIN (
                        SELECT cdv.id, MAX(cdv.id) AS ultima_fecha
                        FROM ct_detalle_venta  cdv
                        INNER JOIN ct_ventas cv ON cv.id = cdv.id_ct_ventas
                        WHERE cv.id_cliente like "' . $cliente->identificacion . '"  
                        GROUP BY cdv.id_ct_productos
                      ) ultima_venta
                      ON cdv.id = ultima_venta.id
                      WHERE cv.id_cliente LIKE "' . $cliente->identificacion . '" 
                      AND cdv.id_ct_productos LIKE "' . $producto->codigo . '"
                      AND cv.id_empresa LIKE "0993069299001"');
                $tbody .= "<td style='text-align:center;'>";
                if (count($valor) > 0) {
                    foreach ($valor as $mostrar) {
                        $tbody .= '$  ' . number_format($mostrar->precio, 2);
                        array_push($lineArr, '$ ' . number_format($mostrar->precio, 2));
                    }
                } else {
                    $tbody .= '-';
                    array_push($lineArr, '-');
                }

                $tbody .= "</td>";
            }
            $tbody .= "</tr>";
            array_push($arrDetalle, $lineArr);
        }
        // echo "
        //     </table> 
        //     </body>
        //     </html>";
        // dd('okay gracuas anugi');
        return ["thead" => $title, "tbody" => $tbody, "arrTitle" => $arrTitle, "arrDetalle" => $arrDetalle];
    }
}
