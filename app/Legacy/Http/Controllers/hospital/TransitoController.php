<?php

namespace Sis_medico\Http\Controllers\Hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Hospital_Log_Movimiento;
use Sis_medico\Hospital_Movimiento;
use Sis_medico\Hospital_Producto;
use Sis_medico\Http\Controllers\Controller;

class TransitoController extends Controller
{
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 7)) == false) {
            return true;
        }
    }

    public function transito()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $productos = DB::table('hospital_log_movimiento as lm')
            ->where('lm.tipo', '2')
            ->join('hospital_producto as p', 'p.id', 'lm.id_producto')
            ->join('hospital_movimiento as m', 'm.id', 'lm.id_movimiento')
            ->join('hospital_bodega as b', 'b.id', 'm.id_bodega')
            ->join('users as u', 'u.id', 'lm.id_encargado')
            ->select('lm.cantidad', 'm.id', 'lm.tipo', 'm.serie', 'p.nombre as producto_nombre', 'u.nombre1 as nombre', 'u.apellido1 as apellido1', 'u.apellido2 as apellido2', 'lm.created_at as fecha')
            ->OrderBy('lm.created_at', 'desc')->paginate(15);

        //dd($productos);
        return view('hospital_admin/producto/transito', ['productos' => $productos]);
    }

    public function transitoag()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('hospital_admin/producto/transitoag');
    }
    public function nombre(Request $request)
    {
        $nombre_encargado = $request['term'];
        $data             = null;
        $nuevo_nombre     = explode(' ', $nombre_encargado);
        $seteo            = "%";

        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM users
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' AND
                  id_tipo_usuario != 2
                  ";

        $nombres = DB::select($query);
        foreach ($nombres as $product) {
            $data[] = array('value' => $product->completo, 'id' => $product->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
        return $productos;
    }

    public function nombre2(Request $request)
    {

        $nombre_encargado = $request['nombre_encargado'];

        $data         = null;
        $nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM users
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' AND
                  id_tipo_usuario != 2;
                  ";
        $nombres = DB::select($query);
        if ($nombres != array()) {
            $data = $nombres[0]->id;
            return $data;
        } else {
            return 'No se encontraron resultados';
        }

    }

    public function codigo(Request $request)
    {

        $nombre = $request['serie'];
        $data   = null;
        $query  = "SELECT m.*, p.*
              FROM hospital_movimiento m, hospital_producto p
              WHERE m.serie LIKE '" . $nombre . "' AND
              m.tipo = '1' AND
              m.id_producto = p.id AND m.usos >= 1 AND m.cantidad >= 1";

        //return $query;
        $productos = DB::select($query);

        //return $productos;

        if ($productos != array()) {
            $data = $productos[0]->nombre;
            return $data;
        } else {
            return 'No se encontraron resultados';
        }
    }

    public function agregartransito(Request $request)
    {

        $serie = $request['serie'];
        //dd($serie);

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $id_encargado = $request['id_encargado'];

        $query = "SELECT m.*, p.*, m.usos as usos_producto, p.cantidad as cantidad1, p.id as producto_id
              FROM hospital_movimiento m, hospital_producto p
              WHERE m.serie LIKE '" . $serie . "' AND
               m.tipo = '1' AND
             m.id_producto = p.id AND m.usos >= 1 AND m.cantidad >= 1";

        $productos = DB::select($query);

        if ($productos != array()) {

            $calculo = $productos[0]->usos_producto - 1;
            //dd($calculo);
            //$cantidad  = 0;
            if ($calculo >= 1) {
                $cantidad_productos = $productos[0]->cantidad;
            } else {
                $anterior = Hospital_Movimiento::where('serie', $serie)->first();
                //$nueva_cantidad  = $anterior->cantidad -1;
                $cantidad_productos = $productos[0]->cantidad1 - 1;
                if ($cantidad_productos > 0) {
                    $calculo = $productos[0]->usos;
                }
            }

            $id          = $productos[0]->id;
            $producto_id = $productos[0]->producto_id;
            $movimiento  = Hospital_Movimiento::where('serie', $serie)->get();
            // dd($cantidad);
            $id_movimiento = $movimiento[0]->id;

            $input = [
                'cantidad'        => '1',
                'id_encargado'    => $id_encargado,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'tipo'            => '2',
            ];
            $movimiento_cambio = Hospital_Movimiento::where('serie', $serie)->where('tipo', '1')->where('usos', '>=', '1')->first();
            $movimiento_cambio->update($input);

            $input2 = [
                'cantidad'        => $cantidad_productos,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];

            Hospital_Producto::where('id', $producto_id)->update($input2);

            Hospital_Log_Movimiento::create([
                'id_producto'     => $id,
                'id_encargado'    => $id_encargado,
                'id_movimiento'   => $id_movimiento,
                'observacion'     => "Producto en Transito",
                'tipo'            => '2',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);

        }
        return back();

    }

}
