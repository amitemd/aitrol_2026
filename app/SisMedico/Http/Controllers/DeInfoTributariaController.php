<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Sis_medico\De_Info_Tributaria;
use Illuminate\Http\Request;
use Sis_medico\Ct_Caja;
use Sis_medico\De_Maestro_Documentos;

class DeInfoTributariaController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $id_empresa = $request->session()->get('id_empresa');
    $id_caja = $request->id;
    $caja = Ct_Caja::find($id_caja);
    $de_maestro_documentos = De_Maestro_Documentos::where('estado', '1')->get();
    foreach ($de_maestro_documentos as $documento) {
      $info_tributaria = De_Info_Tributaria::where('id_empresa', $id_empresa)
        ->where('id_caja', $id_caja)
        ->where('id_maestro_documentos', $documento->id)->get();
      //dd($info_tributaria);
      if (count($info_tributaria) == 0) {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $arr_tributaria = [
          'id_maestro_documentos' => $documento->id,
          'id_empresa' => $id_empresa,
          'id_caja' => $id_caja,
          'id_sucursal' => $caja->sucursal->id,
          'numero_factura' => $caja->sucursal->codigo_sucursal . '-' . $caja->codigo_caja . '-' . '000000000',
          'secuencial_nro' => 0,
          'cod_sucursal' => $caja->sucursal->codigo_sucursal,
          'cod_caja' => $caja->codigo_caja,
          'estado' => 1,
          'id_usuariocrea'       => $idusuario,
          'id_usuariomod'        => $idusuario,
          'ip_creacion'          => $ip_cliente,
          'ip_modificacion'      => $ip_cliente,
        ];
        De_Info_Tributaria::create($arr_tributaria);
      }
    }
    $de_info_tributaria = De_Info_Tributaria::where('estado', '1')
      ->where('id_empresa', $id_empresa)
      ->get();
    return view('sri_electronico/infotributaria/index', ['de_maestro_documentos' => $de_maestro_documentos, 'de_info_tributaria' => $de_info_tributaria]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */


  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  /**
   * Display the specified resource.
   *
   * @param  \Sis_medico\de_info_tributaria  $de_info_tributaria
   * @return \Illuminate\Http\Response
   */


  /**
   * Show the form for editing the specified resource.
   *
   * @param  \Sis_medico\de_info_tributaria  $de_info_tributaria
   * @return \Illuminate\Http\Response
   */
  public function edit(de_info_tributaria $de_info_tributaria, $id)
  {
    $de_info_tributaria = De_Info_Tributaria::find($id);

    return view('sri_electronico/infotributaria/edit', ['de_info_tributaria' => $de_info_tributaria, 'id' => $id]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Sis_medico\de_info_tributaria  $de_info_tributaria
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request)
  {
    //dd($request);
    $ip_cliente = $_SERVER["REMOTE_ADDR"];
    $idusuario  = Auth::user()->id;
    $id = $request->id;
    $num_factura = $request['numero_factura'];
    $parte_num_factura = explode('-', $num_factura);
    $de_info_tributaria = De_Info_Tributaria::find($id);
    if (count($parte_num_factura) > 1) {
      $num_factura = $parte_num_factura[0] . '-' . $parte_num_factura[1] . '-' . str_pad($request['secuencial'], 9, 0, STR_PAD_LEFT);
    } else {
      $caja = Ct_Caja::find($de_info_tributaria->id_caja);
      $num_factura = $request['numero_factura'];
      $num_factura = $caja->sucursal->codigo_sucursal . '-' . $caja->codigo_caja . '-' . str_pad($request['secuencial'], 9, 0, STR_PAD_LEFT);
    }
    // dd($num_factura); 
    $arr_info = [
      'numero_factura'   => $num_factura,
      'secuencial_nro'   => $request['secuencial'],
      'id_usuariocrea'       => $idusuario,
      'id_usuariomod'        => $idusuario,
      'ip_creacion'          => $ip_cliente,
      'ip_modificacion'      => $ip_cliente,
    ];
    $de_info_tributaria->where('id', $id)->update($arr_info);
    return redirect(route('deinfotributaria.index', ['id' => $de_info_tributaria->id_caja]));
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \Sis_medico\de_info_tributaria  $de_info_tributaria
   * @return \Illuminate\Http\Response
   */
}
