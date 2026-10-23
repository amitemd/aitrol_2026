<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Detalle_Acreedores;
use Sis_medico\Ct_Rh_Tipo_Cuenta;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Ct_rubros;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_Contable;
use Sis_medico\Plan_Cuentas;
use Sis_medico\SubidaTarifarios;
use Sis_medico\Proveedor;
use Sis_medico\TipoProveedor;
use Sis_medico\Ct_Orden_Venta_Detalle_Paquete;
use Excel;

class SubirTarifarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, [1, 4, 5, 20, 22]) == false) {
            return true;
        }
    }

    public function importar_tarifario($id_seguro)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }


        ini_set('max_execution_time', 400);

        Excel::filter('chunk')->load('tarifario.xlsx')->chunk(250, function ($reader) use($id_seguro){

            foreach ($reader as $book) {

                if($book != null){

                	$descripcion = "CARGA PARTICULARES";

                	$tipo					= $book->tipo;
                	$codigo					= $book->codigo;	
                	$insumo					= $book->insumo;	
                	$precio_particular		= $book->precio_particular;
                	$precio_para_paquete	= $book->precio_para_paquete;
                	$precio_antes		    = '';
                	$id_producto 			= null;

                	$empresa  = Empresa::where('prioridad',1)->first();	
                	$producto = Ct_productos::where('codigo',$codigo)->where('id_empresa', $empresa->id)->first();
                	if(is_null($producto)){
                		$proceso = "NO SE ENCONTRO EL PRODUCTO POR EL CODIGO";
                	}else{
                		$id_producto = $producto->id;
                		if($id_seguro == '1'){ //PARTICULAR
                			if( $producto->valor_total_paq != null ){
	                			$precio_antes =	$producto->valor_total_paq; 						
	                		}	
	                		/*$producto->update([
	                			'valor_total_paq' => $precio_particular,		
	                		]);*/
	                		$proceso = "X ACTUALIZAR ".$precio_antes;
                		}
	                				
                	}

                	

                }

                SubidaTarifarios::create([
                	'fecha' => date('Y-m-d'),
					'descripcion'	=> $descripcion,
					'tipo'			=> $tipo,
					'codigo'		=> $codigo,
					'insumo'		=> $insumo,
					'id_seguro'		=> $id_seguro,
					'valor'			=> $precio_particular,
					'valor_paquete' => $precio_para_paquete,
					'id_producto'	=> $id_producto,
					'id_usuariocrea'=> '0922290697',
					'id_usuariomod' => '0922290697',
					'ip_creacion'	=> '1',
					'ip_modificacion'=>'1',
					'proceso'		=> $proceso,
                ]);
            }
        });

        return "ok";
    }

    public function crear_producto()
    {

    	$subidas = SubidaTarifarios::whereNull('id_producto')->where('cargado',0)->get();

    	$empresa  = Empresa::where('prioridad',1)->first();	

    	foreach ($subidas as $value) {
    		$producto = Ct_productos::where('codigo',$value->codigo)->where('id_empresa', $empresa->id)->first();

    		$iva = 0;
    		if($value->grupo == 1){
    			$iva = 1;
    		}	

    		if(is_null($producto)){
    			$id_producto = Ct_productos::insertGetId([

					'codigo' => $value->codigo,
					'nombre' => $value->insumo,
					'tipo'	 => 0,
					'descripcion' => $value->insumo,
					'grupo'		  => $value->grupo,
					'cta_gastos'  => $value->cta_gastos,
					'cta_ventas'  => $value->cta_ventas,
					'cta_costos'  => $value->cta_costos,
					'cta_devolucion' => $value->cta_devolucion,
					'impuesto_iva_compras' => $value->iva_compras,
					'impuesto_iva_ventas'  => $value->iva_ventas,
					'valor_total_paq'      => $value->valor,
					'id_usuariocrea'=> '0922290697',
					'id_usuariomod' => '0922290697',
					'ip_creacion'	=> 'MASIVO_MAYO',
					'ip_modificacion'=>'MASIVO_MAYO',
					'iva'		=> $iva,
					'id_empresa'=> $empresa->id,
    								
    			]);

    			$value->update([
    				'id_producto' => $id_producto,
    				'cargado'     => 1,
    			]);

    		}		
    	}	
                	

    }

    public function actualizar_valor()
    {

    	/*$subidas = SubidaTarifarios::whereNotNull('id_producto')->where('cargado',0)->get();

    	$empresa  = Empresa::where('prioridad',1)->first();	

    	foreach ($subidas as $value) {
    		$producto = Ct_productos::find($value->id_producto);

    		if(!is_null($producto)){
    			$producto->update([
					
					'valor_total_paq'      => $value->valor,
    								
    			]);

    			$value->update([
    			
    				'cargado'     => 1,
    			]);

    		}		
    	}*/

        $subidas = SubidaTarifarios::where('proceso','NO SE ENCONTRO EL PRODUCTO POR EL CODIGO')->get();

        foreach ($subidas as $value) {
            $producto = Ct_productos::find($value->id_producto);

            if(!is_null($producto)){
                $producto->update([
                    
                    'cta_gastos'      => $value->gasto,
                    'cta_ventas'      => $value->ventas,
                    'cta_costos'      => $value->costos,
                    'cta_devolucion'  => $value->devolucion,
                                    
                ]);

            }       
        }	
                	

    }

    public function carga_masiva_paquetes()
    {

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;

        $ventas   = Ct_Orden_Venta::where('estado', 1)->where('leido', 0)->orderBy('id', 'asc')->get(); //dd($ventas);

        foreach ($ventas as $venta) {
            $id_nivel          = $venta->id_nivel;
            $detalles          = $venta->detalles; //dd($detalles);
            $detalles_paquetes = $venta->paquetes_detalles;
            $seguro            = $venta->seguro;
         //   dd($seguro, $venta);
            if ($seguro->id_seguro_tipos == 3 or $seguro->id_seguro_tipos == 4 or $seguro->id_seguro_tipos == 5) {
                //dd($detalles);
                foreach ($detalles as $detalle) {
                    $producto = $detalle->producto; //dd($producto);
                    $paquetes = $producto->paquetes->where('estado',1); //dd($paquetes);
                    $acum_descuento = 0;

                    foreach ($paquetes as $paquete) {
                        //dd( $paquete, $producto, $seguro->tipo );
                        // $tarifarios = $paquete->tarifarios;
                        $descuento = $detalle->p_dcto * $paquete->precio / 100;
                        $descuento = round($descuento, 2);
                        $acum_descuento += $descuento;

                        if($acum_descuento > $detalle->descuento){
                            $diferencia = $acum_descuento - $detalle->descuento;
                            $descuento -= $diferencia;
                            if($descuento < 0){
                                $descuento = 0;
                                $detalle->update([ 'cuadre' => '1' ]);
                            }
                        }
                        
                        $orden_venta_paquete = $detalles_paquetes->where('id_producto_paquete', $paquete->id_paquete)->first();
                        if (is_null($orden_venta_paquete)) { //dd("crea");
                            Ct_Orden_Venta_Detalle_Paquete::create([
                                'id_orden'             => $venta->id,
                                'id_producto'          => $paquete->id_producto,
                                'descripcion'          => $paquete->nombre,
                                'id_producto_paquete'  => $paquete->id_paquete,
                                'cantidad'             => $paquete->cantidad,
                                'descuento'            => $descuento,
                                'precio'               => $paquete->precio,
                                'iva'                  => 0,
                                'valor_iva'            => 0,
                                'estado'               => 1,
                                'id_usuariocrea'       => $idusuario,
                                'id_usuariomod'        => $idusuario,
                                'ip_creacion'          => $ip_cliente,
                                'ip_modificacion'      => $ip_cliente,

                            ]);
                        }
                    }
                }

                $venta->update([
                    'leido' => '1',
                ]);
            }
        }



        return "ok";
    }

    
}
