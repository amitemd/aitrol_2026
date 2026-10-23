<?php

namespace Sis_medico\Http\Controllers\hospital;
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Habitacion;
use Sis_medico\Cama;
use Sis_medico\Empresa;
use Sis_medico\Piso;
use Sis_medico\Hospital_Pedido;
use Sis_medico\Hospital_Producto;
use Sis_medico\Hospital_Log_Movimiento;
use Sis_medico\Hospital_Movimiento;
use Sis_medico\CamaTransaccion;
use Sis_medico\Hospital_Proovedor;
use Sis_medico\Hospital_Bodega;
class IngresoController extends Controller
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

    public function ingresopedido()
    { 
         $opcion = '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
        $proveedores= Hospital_Proovedor::all();
        $empresa= Empresa::all();
        $estado= "1";
        $bodegas= Hospital_Bodega::all();
        return view('hospital_admin/pedidos/ingresopedido',['proveedores'=>$proveedores,'empresa'=>$empresa,'bodegas'=>$bodegas]);
    }
    public function formulario(Request $request)
    { 
         $opcion = '1';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
        $codigo = $request['codigo'];
        $nombre = $request['nombre'];

        $producto = DB::table('hospital_producto')->where('codigo', 'LIKE', '%' . $codigo . '%')->where('nombre', 'like', '%' . $nombre . '%')->get();
        //dd($producto);
        return $producto;
    }

    private function ValidatePedido(Request $request)
    {

        $prules = [
            'pedido'       => 'required|unique:pedido',
            'id_proveedor' => 'required',
        ];
        $pmsn = [
            'pedido.required'       => 'Ingrese el numero del pedido.',
            'pedido.unique'         => 'El numero de pedido ya esta registrado.',
            'id_proveedor.required' => 'El proveedor es requerido',

        ];

        $this->validate($request, $prules, $pmsn);

    }


    public function guardar(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $variable = $request['contador'];
        $input    = [
            'id_proveedor'    => $request['id_proveedor'],
            'pedido'          => $request['pedido'],
            'fecha'           => $request['fecha'],
            'vencimiento'     => $request['vencimiento'],
            'observaciones'   => $request['observaciones'],
            'subtotal_12'     => $request['subtotal_12'],
            'subtotal_0'      => $request['subtotal_0'],
            'iva'             => $request['iva'],
            'total'           => $request['total'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $this->validatePedido($request);

        for ($i = 0; $i < $variable; $i++) {
            $visibilidad = $request['visibilidad' . $i];
            if ($visibilidad == 1) {
                $prules = [
                    'lote' . $i => 'required',
                ];
                $pmsn = [
                    'lote' . $i . '.required' => 'Ingrese el numero de Lote.',

                ];

                $this->validate($request, $prules, $pmsn);
            }
        }

        $id_pedido = Hospital_Pedido::insertGetId($input);

        for ($i = 0; $i < $variable; $i++) {
            $visibilidad = $request['visibilidad' . $i];
            if ($visibilidad == 1) {
                $cantidad = $request['cantidad' . $i];

                for ($x = 0; $x < $cantidad; $x++) {
                    $input2 = [
                        'id_producto'       => $request['id' . $i],
                        'cantidad'          => '1',
                        'estado'            => '1',
                        'id_encargado'      => $idusuario,
                        'serie'             => $request['serie' . $i],
                        'id_bodega'         => $request['id_bodega' . $i],
                        'tipo'              => '1',
                        'fecha_vencimiento' => $request['fecha_vencimiento' . $i],
                        'lote'              => $request['lote' . $i],
                        'usos'              => $request['usos' . $i],
                        'precio'            => $request['precio' . $i],
                        'id_pedido'         => $id_pedido,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                        'id_usuariocrea'    => $idusuario,
                        'id_usuariomod'     => $idusuario,
                    ];

                    $id_movimiento = DB::table('hospital_movimiento')->insertGetId($input2);

                    $id_producto       = $request['id' . $i];
                    $producto          = Hospital_Producto::find($id_producto);
                    $cantidad_producto = $producto->cantidad;
                    $nueva_cantidad    = $cantidad_producto + 1;

                    $input3 = [
                        'cantidad'        => $nueva_cantidad,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ];

                    Hospital_Log_Movimiento::create([
                        'id_producto'     => $request['id' . $i],
                        'id_movimiento'   => $id_movimiento,
                        'id_encargado'    => $idusuario,
                        'observacion'     => "Ingreso del producto",
                        'tipo'            => '1',
                        'ip_creacion'     => $ip_cliente,
                        'cantidad'        => '1',
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                    Hospital_Producto::where('id', $id_producto)->update($input3);
                }
            }
        }

        $url = route('hospital_admin.codigobarra');
        return $url;
    }

    public function eliminar_pedido($id)
    {
        $productos = Hospital_Movimiento::where('id_pedido', $id)
            ->where(function ($query) {
                $query->where('tipo', 0)
                    ->orWhere('tipo', 2);})->get();
        //dd($productos);

        return view('hospital/pedido/eliminar', ['productos' => $productos, 'id' => $id]);
    }

    public function eliminar_clave(Request $request)
    {
        $ingresada      = $request['password'];
        $id             = $request['id'];
        $hashedPassword = Auth::user()->password;
        if (Hash::check($ingresada, $hashedPassword)) {
            $productos = Hospital_Movimiento::where('id_pedido', $id)->get();
            foreach ($productos as $value) {
                Hospital_Log_Movimiento::where('id_movimiento', $value->id)->delete();
                $producto           = Hospital_Producto::find($value->id_producto);
                $cantidad           = $producto->cantidad - 1;
                $producto->cantidad = $cantidad;
                $producto->save();
                $movimiento = Hospital_Movimiento::find($value->id);
                $movimiento->delete();
            }
            $pedido = Hospital_Pedido::find($id);
            $pedido->delete();
            return "okay";
        }
        return "no";
    }
    
    


}
