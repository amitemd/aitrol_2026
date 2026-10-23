<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Cama;
use Sis_medico\CamaTransaccion;
use Sis_medico\Habitacion;
use Sis_medico\Hospital_Producto;
use Sis_medico\Hospital_Marca;
use Sis_medico\Hospital_Movimiento;
use Sis_medico\Hospital_Proveedor;
use Sis_medico\Hospital_Log_Movimiento;
use Sis_medico\Hospital_Tipo;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;

class ProductoAdminController extends Controller
{
public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion){ 
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion',$opcion)->where('id_tipo_usuario',$rolUsuario)->first();
        if(is_null($opcion_usuario)){
          return true;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
 public function producto(){
     $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $producto= Hospital_Producto::paginate(10);
        $movimiento= Hospital_Log_Movimiento::all();
        /*$consulta = DB::table('hospital_producto')->join('hospital_tipo', 'hospital_producto.tipo_producto', '=', 'hospital_tipo.id')->get();*/
        return view('hospital_admin/producto/producto',['producto'=>$producto, 'movimiento'=>$movimiento]);

    }
 public function agregarprodu(Request $request){
    $opcion = '1';
    if ($this->rol_new($opcion)) {
        return redirect('/');
    }
    $ip_cliente = $_SERVER["REMOTE_ADDR"];
    $idusuario  = Auth::user()->id;
    $marcas= Hospital_Marca::all();
    $productod=[
        'codigo'=>$request['codigo'],
        'nombre'=>$request['nombre'],
        'indicaciones_medicina'=>$request['indicaciones_medicina'],
        'estado'=>$request['estado'],
        'descripcion'=>$request['descripcion'],
        'medida'=>$request['medida'],
        'minimo'=>$request['minimo'],
        'despacho'=>$request['despacho'],
        'cantidad'=>$request['cantidad'],
        'registro_sanitario'=>$request['registro'],
        'id_marca'=>$request['marcas'],
        'tipo_producto'=>$request['tipop'],
        'usos'=>$request['usos'],
        'iva' =>$request['iva'],
        'id_usuariomod'   => $idusuario,
        'id_usuariocrea'  => $idusuario,
        'ip_modificacion' => $ip_cliente,
        'ip_creacion'     => $ip_cliente,
    ];
    $producto= Hospital_Producto::insertGetId($productod);

    return back();
 }
    
    public function modalproducto(){
      $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');

        }
        $marcas= Hospital_Marca::all();
        $tipop= Hospital_Tipo::all();
    
     return view('hospital_admin/producto/modalproducto',['marcas'=>$marcas,'tipop'=>$tipop]);
    }

    public function modaleditarp($id){
        $opcion = '1';
          if ($this->rol_new($opcion)) {
              return redirect('/');
  
          }
          
          $productoid= Hospital_Producto::find($id);
       return view('hospital_admin/producto/modaleditarp',['productoid'=>$productoid]);
      }
       public function modalbaja($cant, $tipo, $id_pro, $serie, $bodega, $pedido, $f_vencimiento, $lote){
        $opcion = '1';
          if ($this->rol_new($opcion)) {
              return redirect('/');
  
          }
        return view('hospital_admin/producto/modalbaja', ['cantidad' => $cant, 'tipo' => $tipo, 'id_producto' => $id_pro, 'serie' => $serie, 'bodega' => $bodega, 'pedido' => $pedido, 'f_venci' => $f_vencimiento, 'lote' => $lote]);
    }
       public function borrar(Request $request)
    {
        //return $request->all();
       $opcion = '1';
          if ($this->rol_new($opcion)) {
              return redirect('/');
  
          }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $cantidad          = $request['cantidad'];
        $tipo              = $request['tipo'];
        $id_producto       = $request['id_producto'];
        $serie             = $request['serie'];
        $id_bodega         = $request['id_bodega'];
        $id_pedido         = $request['id_pedido'];
        $fecha_vencimiento = $request['f_venci'];
        $lote              = $request['lote'];

        for ($x = 0; $x < $cantidad; $x++) {
            if ($tipo == '1') {
                $producto          = hospital_producto::find($id_producto);
                $cantidad_producto = $producto->cantidad;
                $nueva_cantidad    = $cantidad_producto - 1;

                $input3 = [
                    'cantidad'        => $nueva_cantidad,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ];

                hospital_producto::where('id', $id_producto)->update($input3);

                $input = [
                    'cantidad'        => '1',
                    'usos'            => '1',
                    'id_encargado'    => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'tipo'            => '4',
                ];

                $movimiento_cambio = hospital_movimiento::where('serie', $serie)->where('tipo', '1')->where('usos', '>=', '1')->first();
                $movimiento_cambio->update($input);
                $id_movimiento = $movimiento_cambio->id;

                Hospital_Log_Movimiento::create([
                    'id_producto'     => $id_producto,
                    'id_movimiento'   => $id_movimiento,
                    'id_encargado'    => $idusuario,
                    'observacion'     => "Producto dado de baja",
                    'motivo'          => $request['motivo'],
                    'tipo'            => '4',
                    'cantidad'        => '1',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            } elseif ($tipo == '2') {

                $input = [
                    'cantidad'        => '1',
                    'usos'            => '1',
                    'id_encargado'    => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'tipo'            => '4',
                ];
                $movimiento_cambio = hospital_movimiento::where('serie', $serie)->where('tipo', '2')->where('usos', '>=', '1')->first();
                $movimiento_cambio->update($input);
                $id_movimiento = $movimiento_cambio->id;

                Hospital_Log_Movimiento::create([
                    'id_producto'     => $id_producto,
                    'id_movimiento'   => $id_movimiento,
                    'id_encargado'    => $idusuario,
                    'observacion'     => "Producto dado de baja",
                    'motivo'          => $request['motivo'],
                    'tipo'            => '4',
                    'cantidad'        => '1',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            }
        }
        return back();
    }
   public function darbaja(Request $request){
           $opcion = '1';
         if($this->rol_new($opcion)) {
            return redirect('/');

         }
   
        $serie      = $request['serie'];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $motivo     = $request['motivo'];
        $cant_baja  = $request['cant_baja'];
       //dd($cant_baja);
        $query = "SELECT m.*, p.*, m.usos as usos_producto, p.cantidad as cantidad1, p.id as producto_id
              FROM hospital_movimiento m, producto p
              WHERE m.serie LIKE '" . $serie . "' AND
             m.id_producto = p.id AND m.usos >= 1 AND m.cantidad >= 1";
            $productos = DB::select($query);
            // dd($productos);
         //if ($productos != array()) {
            //if (true) {
            $anterior           = Hospital_Movimiento::where('serie', $serie)->first();
             if ($anterior  != null) {    
            $cantidad_productos = $anterior->cantidad - $cant_baja;
            $calculo            = $anterior->usos;
            $id                 = $anterior->id;
            $producto_id        = $anterior->producto_id;
            $movimiento         = Hospital_Movimiento::where('serie', $serie)->get();
            //dd($cantidad_productos);
            $id_movimiento = $movimiento[0]->id;

            $input = [
                'cantidad'        => $cantidad_productos,
                'usos'            => $calculo,
                'id_encargado'    => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            Hospital_Movimiento::where('serie', $serie)->update($input);

            $input2 = [
                'cantidad'        => $cantidad_productos,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];

            Hospital_Producto::where('id', $producto_id)->update($input2);

            Hospital_Log_Movimiento::create([
                'id_producto'     => $id,
                'id_encargado'    => $idusuario,
                'id_movimiento'   => $id_movimiento,
                'observacion'     => $motivo,
                'tipo'            => '4',
                'cantidad'        => $cant_baja,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
         }
          
       return view('hospital_admin/producto/darbaja');
    }
       public function descuento ($cantidad){
        $opcion = '1';
          if ($this->rol_new($opcion)) {
              return redirect('/');
  
          }
          
       return view('hospital_admin/producto/descuento',['cantidad'=>$cantidad]);
      }


      public function tablap(Request $request){
        $opcion = '1';
          if ($this->rol_new($opcion)) {
              return redirect('/');
  
          }
          $codigo = $request['codigo'];
          $producto = DB::table('hospital_movimiento as m')
          ->where('m.serie', $codigo)
          ->join('hospital_pedido as p', 'p.id', 'm.id_pedido')
          ->where('m.estado', '1')
          ->join('hospital_producto as pro', 'pro.id', 'm.id_producto')
          ->groupBy('m.tipo')
          ->select(DB::raw('count(*) as cantidad_total, m.tipo'), 'm.serie', 'pro.nombre as nombre_producto', 'm.*', 'pro.codigo', 'pro.descripcion')
          ->where(function ($query) {
              $query->where('m.tipo', '2')
                  ->orWhere('m.tipo', '1');
          })->get();
          
         return view('hospital_admin/producto/tablap',['producto'=>$producto]);
      }


      
    public function updatepro($id, Request $request){
        $opcion = '1';
          if ($this->rol_new($opcion)) {
              return redirect('/');
          }

          $updates= [
            'codigo'=>$request['codigo'],
            'nombre'=>$request['nombre'],
            'descripcion'=>$request['descripcion'],
            'estado'=>$request['estado'],
            'medida'=>$request['medida'],
            'minimo'=>$request['minimo'],
            'despacho'=>$request['despacho'],
            'registro_sanitario'=>$request['registro'],
            'id_marca'=>$request['marcas'],
            'usos'=>$request['usos'],
          ];

          $updatex= Hospital_Producto::find($id);
          $updatex->update($updates);
          
          return back();
      }

    public function movientop($id){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');

        }

        $productos = DB::table('hospital_log_movimiento as lm')
        ->where('lm.id_producto', $id)
        ->join('hospital_producto as p', 'p.id', 'lm.id_producto')
        ->join('hospital_movimiento as m', 'm.id', 'lm.id_movimiento')
        ->join('hospital_bodega as b', 'b.id', 'm.id_bodega')
        ->groupBy('lm.tipo')
        ->groupBy('m.serie')
        ->select(DB::raw('count(*) as cantidad_total, lm.tipo'), 'lm.*', 'p.nombre', 'p.cantidad as ptotal', 'm.serie', 'm.fecha_vencimiento', 'b.nombre as nombre_bodega')
        ->OrderBy('lm.created_at', 'desc')->groupBy('lm.id_movimiento')->paginate(15);
         //dd($productos);
         $producto = Hospital_Producto::find($id);
         return view('hospital_admin/producto/movimientop',['productos'=>$productos,'producto'=>$producto]);
    }
    public function transito(){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');

        }
        $transito=0;
        return view('hospital_admin/producto/transito',['transito'=>$transito]);
    }
    public function transitoag(){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');

        }
        $transito=0;
        return view('hospital_admin/producto/transitoag',['transito'=>$transito]);
    }
    public function codigo(Request $request){
        $opcion= '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
        $pedido = $request['numerodepedido'];
        $constraints = ['numerodepedido' => $request['numerodepedido']];
        $pedidos = DB::table('hospital_pedido as p')
            ->where('p.pedido', 'like', '%' . $pedido . '%')
            ->join('hospital_proovedor as pro', 'pro.id', 'p.id_proveedor')
            ->join('users as u', 'u.id', 'p.id_usuariocrea')
            ->select('p.*', 'u.nombre1', 'u.apellido1', 'pro.nombrecomercial')
            ->OrderBy('p.created_at', 'desc')->paginate(10);
        //dd($pedidos);
        $i = 0;
        $cantidades = array();
        foreach ($pedidos as $value) {

            $busqueda = DB::table('hospital_pedido as p')
                ->where('p.id', $value->id)
                ->join('hospital_movimiento as m', 'm.id_pedido', 'p.id')
                ->where('m.estado', '1')
                ->OrderBy('m.updated_at', 'desc')
                ->groupBy('m.serie')
                ->get();
            //dd($busqueda);

            $busqueda2 = DB::table('hospital_pedido as p')
                ->where('p.id', $value->id)
                ->join('hospital_movimiento as m', 'm.id_pedido', 'p.id')
                ->where('m.estado', '1')
                ->where('m.tipo', '1')
                ->OrderBy('m.updated_at', 'desc')
                ->select(DB::raw('count(*) as cantidad_total, m.tipo'))
                ->get();

            //dd($busqueda);
            $cantidades[$i][0] = $busqueda->count();
            $cantidades[$i][1] = $busqueda2[0]->cantidad_total;

            $i = $i + 1;
        }
        //dd($cantidades);
        return view('hospital_admin/pedidos/codigobarra', ['pedidos' => $pedidos, 'cantidades' => $cantidades, 'searchingVals' => $constraints]);

    }
    public function codigo2(){
        $opcion= '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
        $codigo    = $request['codigo'];
        $data      = null;
        $productos = DB::table('hospital_producto')->where('codigo', 'like', '%' . $codigo . '%')->get();

        if ($productos != array()) {
            //dd($productos);
            $data = $productos[0]->nombre;
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }


    }

    public function nombre(Request $request)
    {

        $nombre = $request['term'];

        $data      = array();
        $productos = DB::table('hospital_producto')->where('nombre', 'like', '%' . $nombre . '%')->get();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->nombre, 'id' => $product->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

    }

    public function nombre2(Request $request)
    {

        $nombre = $request['nombre'];

        $data      = null;
        $productos = DB::table('hospital_producto')->where('nombre', 'like', '%' . $nombre . '%')->get();

        if ($productos != array()) {
            $data = $productos[0]->codigo;
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

    }
    public function seguimientom(Request $request){

        $opcion= '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        }


        return view('hospital_admin/producto/tablam',['productos'=>$productos]);

    }
    
      

}