<?php

namespace Sis_medico\Http\Controllers\servicios;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Response;
use Sis_medico\Contable;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Agrupador_labs;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Parametro;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_Api;
use Sis_medico\Log_Apps;
use Sis_medico\Log_usuario;
use Sis_medico\Membresia;
use Sis_medico\MembresiaDetalle;
use Sis_medico\Paciente;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\UserMembresia;
use Sis_medico\FarmaCategorias;
use Sis_medico\FarmaSubcategoria;

class FarmaController extends Controller
{
    
    public function obtener_subcategorias(Request $request)
    {
        //dd($request);
        /* json invocacion
        {
          "token"    : "8c0a00ec19933215dc29225e645ea714",
          "empresa"  : "0993069299001"
        }  */
        $data = json_decode($request->getContent(), true);
        $token = $data['token'];
        if ($token != '8c0a00ec19933215dc29225e645ea714') {
            return response()->json([
                'result' => 'Invalid Token',
            ]);
        }

        $empresa = $data['empresa'];

        $array_subcategorias = [];
        $categorias = FarmaCategorias::where('estado','1')->where('id_empresa',$empresa)->get();
        foreach ($categorias as $value) {
            //dd($value);
            $subcategorias = $value->subcategorias;
            foreach ($subcategorias as $subcategoria) {
                $array_subcategorias[$subcategoria->id] = [
                    'id'            => $subcategoria->id,
                    'nombre'        => $subcategoria->nombre,
                    'categoria'     => $subcategoria->categoria->nombre,
                    'descripcion'   => $subcategoria->descripcion,
                    'imagen'        => $subcategoria->imagen,
                ];    
            }
            
            //dd($subcategorias);
        }

        $djson    = json_encode($array_subcategorias);
        
        return response()->json([
                'result' => $djson,
            ]);

    }

    public function obtener_productos_categorias(Request $request)
    {
        //dd($request);
        /* json invocacion
        {
          "token"    : "8c0a00ec19933215dc29225e645ea714",
          "empresa"  : "0993069299001",
          "subcategoria": "1"
          
        }*/
        $data = json_decode($request->getContent(), true);
        $token = $data['token'];
        if ($token != '8c0a00ec19933215dc29225e645ea714') {
            return response()->json([
                'result' => 'Invalid Token',
            ]);
        }

        $empresa = $data['empresa'];
        $id_subcategoria = $data['subcategoria'];

        $subcategoria = FarmaSubcategoria::find($id_subcategoria);

        $array_productos = [];
        if(!is_null($subcategoria)){
            $productos = $subcategoria->productos;
            foreach ($productos as $producto) {
                $array_productos[$producto->id] = ['id' => $producto->id, 'nombre' => $producto->producto->nombre, 'imagen' => $producto->imagen];    
            }
            
        }
        dd($array_productos);

        $categorias = FarmaCategorias::where('estado','1')->where('id_empresa',$empresa)->get();
        foreach ($categorias as $value) {
            //dd($value);
            $subcategorias = $value->subcategorias;
            foreach ($subcategorias as $subcategoria) {
                $array_subcategorias[$subcategoria->id] = [
                    'id'            => $subcategoria->id,
                    'nombre'        => $subcategoria->nombre,
                    'categoria'     => $subcategoria->categoria->nombre,
                    'descripcion'   => $subcategoria->descripcion,
                    'imagen'        => $subcategoria->imagen,
                ];    
            }
            
            //dd($subcategorias);
        }

        $djson    = json_encode($array_subcategorias);
        
        return response()->json([
                'result' => $djson,
            ]);

    }


}
