<?php

namespace Sis_medico\Http\Controllers\hospital;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Response;
use Sis_medico\Plato;
use Sis_medico\Items_plato;
use Sis_medico\Datos_item;
use Sis_medico\Ho_Parametros;
use Sis_medico\Opcion_Usuario;

class ServiciosAdminController extends Controller
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

    /**LISTA DE MENU */
    public function listamenu(){

        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $plato = Plato::paginate(10);
        ///print_r($plato);
        return view('hospital_admin/servicios/menu',['plato'=>$plato]);

    }

    //Para visualizar la vista de crear menu
    public function crearmenu(Request $request){

        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        return view('hospital_admin/servicios/crearmenu');

    }

    //Editar el menu del plato
    public function editar($id){

        $plato = Plato::find($id);
        return view('hospital_admin/servicios/editar',['plato'=>$plato]);

    }

    public function actualizarplato(Request $request, $id){

        $plato = Plato::findOrFail($id);

        $plato->nombre = $request->nombre;
        $plato->costo  = $request->costo;
        $plato->tipo   = $request->tipo;
       
        
        $plato->save();
        //dd($request);

        $input['estado'] = 0;
        $item = Datos_item::where('id_plato', $id)->update($input);
        //dd($request->input("ingredientes"));
        foreach($request->input("ingredientes") as $value){
            $item = Datos_item::where('id_item_plato', $value)->where('id_plato', $id)->get();
            if($item->count()>0){
                $input['estado'] = 1;
                Datos_item::where('id_item_plato', $value)->where('id_plato', $id)->update($input);
            }else{
                Datos_item::create([
                    'id_plato'      => $request['id_plato'],
                    'id_item_plato' => $request['id_item_plato'],
                    'estado'        => 1
                ]);
            }
            //print_r($value);
            //$variable1 = Datos_item::where(id_plato, $id)->get();

            //$as = array_diff($request['ingredientes'], $variable1);
            //$as1 = array_diff($variable1, $request['ingredientes']);

            //$as;
            //foreach($as1 as $ingrediente){
                //Datos_Item::create([
                    //'id_plato' => $id,
                    //'id_item_plato' => $ingrediente
                //]);
            //}

            //$as1;

            /*Datos_Item::create([
                'id_plato' => $id,
                'id_item_plato' => $value
            ]);*/

        }
        //return redirect('/');

        // Plato::find($id)->update($request->all());
        return redirect()->intended('hospital/admin/servicios/lista');
        //return redirect()->route('hospital_admin/servicios/menu');
    }
    
    public function agregarigrediente(Request $request){

        $item=[

            'item'     => $request['valor'],
           
        ];
        $idplato = Items_plato::insertGetId($item);
        return $idplato;

    }

    public function buscaringrediente(Request $request){

        $buscar=Items_plato::where('item',$request['valor'])->where('estado', 1)->first();
            //OrCreate(['item' => $request['valor']]);
            //print_r($buscar);
            //return 0;
        return $buscar;
        
            //print_r($request['valor']);
            //print_r("asdasd");
            //return 0;
    }

    public function fetch(Request $request){
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = DB::table('items_plato')->where('item', 'like','%'.$query.'%')->get();
            $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
            foreach ($data as $row) {
                $output .= '<li data-name="'.$row->item. '" data-id="'.$row->id. '" class="newitem">'.$row->item. '</li>';
            }
            $output .= '</u>';
            return $output;
        }
    }

    //GUARDA EL PLATO EN MI TABLA DE BASE DE DATOS UNA VES CREADO
    public function guardar(Request $request){
        DB::beginTransaction();
        try{
            //dd($request);
        /*foreach($request->input("ingredientes") as $value){
            print_r($value);
        }*/

        //$ing = $request['ingredientes'];
        //dd($ing);
        //dd($request->all());

        $plato = Plato::insertGetId([
            'nombre' => $request['nombre'],
            'costo'  => $request['costo'],
            'tipo'   => $request['tipo']
        ]);
        //print_r($plato);

        foreach($request->input("ingredientes") as $value){
            //print_r($value);
            Datos_Item::create([
                'id_plato' => $plato,
                'id_item_plato' => $value
            ]);

        }

        DB::commit();
        /*Plato::create(
            nombre $
        )*/
        
        }catch(\Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
        return redirect()->intended('hospital/admin/servicios/lista');
       // return view('hospital_admin/servicios/menu');
    }
    public function enable(Request $request){
        $idusuario  = Auth::user()->id;
        $class= $request['class'];
        $classes="sun";
        if($class=='dark-layout'){
            $classes="moon";
        }
        $valid= Ho_Parametros::where('id_usuario',$idusuario)->first();
        if(!is_null($valid)){
          
            $valid->class=$class;
            $valid->noclass=$classes;
            $valid->save();
            return response()->json(['response'=>'0','class'=>$valid->class,'classes'=>$valid->noclass]);
        }else{
            Ho_Parametros::create([
                'class'=>$class,
                'id_usuario'=>$idusuario,
                'noclass'=>$classes
            ]);
            return response()->json(['response'=>'1','class'=>$class,'classes'=>$classes]);
        }
        return response()->json('error');
    }
    public function login(){
        return view('layouts.loginnew');
    }
    
    

}