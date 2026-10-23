<?php

namespace Sis_medico\Http\Controllers\contable;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_ventas;
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
        $clientes = DB::table('ct_clientes as cc')
        ->join('ct_ventas as ven', 'cc.identificacion', 'ven.id_cliente')
        ->select('ven.id','cc.nombre')
        ->groupBy("cc.identificacion")
        ->select('cc.*')
        ->get(); 


        // $producto = DB::table('ct_productos as cp')
        // ->join('ct_detalle_venta as ctv', 'cp.codigo', 'ctv.id_ct_productos')
        // ->select('ctv.id','cp.nombre')
        // ->groupBy("ctv.id_ct_productos")


        // ->get(); 

        $producto =Ct_productos::join('ct_detalle_venta as ctv', 'ct_productos.codigo', 'ctv.id_ct_productos')
        ->select('ct_productos.*')->groupBy('ct_productos.codigo')->get();


        return view('contable.ultimo_precio.index',compact('clientes','producto'));
        

        // $data = PrecioProductoController::query();
        // dd($data);
        return view('contable.ultimo_precio.index');
    }

    public function buscador(Request $request)
    {
        $data = PrecioProductoController::query($request->all());

        return ['thead' => $data['thead'], 'tbody' => $data['tbody']];
    }


    public static function query($data = [])

    {
        $title = "";
        $tbody = "";

        $arrTitle = ["Productos"];
        $arrDetalle = [];

        $id_producto="";
        if (isset($data["producto"])) { 
            $prod_query = Ct_productos::find($data["producto"]);

            //dd($prod_query);
            foreach ($prod_query as $key => $value) {
                $id_producto.= "'".$value->codigo."'".',';
            }
            
    
            $id_producto = substr($id_producto,0,strlen($id_producto)-1);
            //dd($id_producto);}
        
        }
        /*if(!is_null($prod_query)){
            $id_producto = $prod_query[0]->codigo;
            
        }*/
     $query = "SELECT  cdv.id, cdv.id_ct_productos AS codigo, cdv.nombre, cdv.precio, cdv.cantidad, cv.fecha
     FROM ct_detalle_venta cdv
     INNER JOIN ct_ventas cv ON cdv.id_ct_ventas = cv.id
     INNER JOIN ct_asientos_cabecera cac ON cv.id_asiento = cac.id
     INNER JOIN (
       SELECT id, MAX(id) AS ultima_fecha
       FROM ct_detalle_venta
       GROUP BY id_ct_productos
     ) ultima_venta
     ON cdv.id = ultima_venta.id
     WHERE cv.id_empresa LIKE '" . Session::get('id_empresa') . "'";
     if($id_producto!="")
     $query = $query." AND cdv.id_ct_productos in ({$id_producto})";
        $productos = DB::select($query);
              
              

              
        /*$id_cliente = "";
        if(isset($data["cliente"]) and !is_null($data["cliente"])){
        $id_cliente = $data["cliente"];
        }*/

        $id_cliente="";
        if (isset($data["cliente"])) { 
        $clien_query = Ct_Clientes::find($data["cliente"]);
        
        foreach ($clien_query as $key => $value) {
            $id_cliente.= "'".$value->identificacion."'".',';
            
            
        }
        //dd(strlen($id_cliente));
        $id_cliente = substr($id_cliente,0,strlen($id_cliente)-1);
    }

        $query1= "SELECT cc.identificacion, cc.nombre
        FROM ct_ventas cv 
        INNER JOIN ct_clientes cc ON cv.id_cliente = cc.identificacion";
         if($id_cliente!="")
         $query1 = $query1 ." AND cc.identificacion in ({$id_cliente})
        GROUP BY cc.identificacion";
        $clientes = DB::select($query1);
        //dd($clientes);
        
              
        // <!DOCTYPE html>
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
                      AND cv.id_empresa LIKE "'. Session::get('id_empresa') .'"');
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
