<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mail;
use Response;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Detalle_Rol;
use Sis_medico\Ct_Nomina;
use Sis_medico\Ct_Rh_Cuotas_Hipotecarios;
use Sis_medico\Ct_Rh_Cuotas_Quirografario;
use Sis_medico\Ct_Rh_Otros_Anticipos;
use Sis_medico\Ct_Rh_Prestamos;
use Sis_medico\Ct_Rh_Saldos_Iniciales;
use Sis_medico\Ct_Rh_Tipo_Cuenta;
use Sis_medico\Ct_Rh_Tipo_Pago;
use Sis_medico\Ct_Rh_Valores;
use Sis_medico\Ct_Rh_Valor_Anticipos;
use Sis_medico\Ct_Rol_Forma_Pago;
use Sis_medico\Ct_Rol_Pagos;
use Sis_medico\Ct_Tipo_Rol;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_ventas;
use Sis_medico\User;
use Sis_medico\Log_horas_extras;
use Sis_medico\Ct_Rh_Detalle_Horas_Extras;

class PlantillasNominaController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth');
	}

	private function rol()
	{
		$rolUsuario = Auth::user()->id_tipo_usuario;
		$id_auth    = Auth::user()->id;
		if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
			return true;
		}
	}

	public function plantillas_prestamos(Request $request)
	{
		// if ($this->rol()) {
		 	return response()->view('errors.404');
		// }
		// $anio = date('Y');
		// $mes = date('m');
		// $id_empresa		 = $request->session()->get('id_empresa');
		// $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();

		// return view('contable/plantillas_prestamos/index', ['anio' => $anio, 'mes' => $mes, 'empresa' => $empresa]);
	}


	public function plantillas_horas_extras(Request $request)
	{
		// if ($this->rol()) {
		 	return response()->view('errors.404');
		// }
		// $anio = date('Y');
		// $mes = date('m');
		// $id_empresa		 = $request->session()->get('id_empresa');
		// $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();

		// return view('contable/plantillas_horas_extras/index', ['anio' => $anio, 'mes' => $mes, 'empresa' => $empresa]);
	}

	public function subir_prestamos(Request $request)
	{
		//dd($request->all());
		$idusuario       = Auth::user()->id;
		$ip_cliente   = $_SERVER["REMOTE_ADDR"];
		$id_empresa		 = $request->session()->get('id_empresa');
		$empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
		$anio = $request['anio'];
		$mes = $request['mes'];
		$fecha_actual = Date('Y-m-d H:i:s');
		$prestamo = $request['prestamos'];
		$nombre_original = $request['archivo']->getClientOriginalName();
		$extension       = $request['archivo']->getClientOriginalExtension();
		$nuevo_nombre    = "plantilla_prestamo" . rand(0, 999999) . "." . $extension;

		$r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));
		//dd($r1);
		$rutadelaimagen = base_path() . '/storage/app/avatars/' . $nuevo_nombre;

		if ($r1) {
			Excel::filter('chunk')
				->formatDates(true, 'Y-m-d')
				->load($rutadelaimagen)
				->chunk(250, function ($reader) use ($idusuario, $anio, $mes, $prestamo, $id_empresa, $fecha_actual, $ip_cliente, $nombre_original) {
					//dd($reader);
					$cant = 0;
					foreach ($reader as $book) {


						if (!is_null($book)) {
							if (!is_null($book->cedula)) {
								$rol = Ct_Rol_Pagos::where('id_user', $book->cedula)->where('mes', $mes)->where('anio', $anio)->where('estado', '1')->where('id_empresa', $id_empresa)->first();
								/*if ($cant == 1) {
                						dd($book, $rol);
                					}*/
								//dd($book, $rol);
								//$nomina = Ct_Nomina::where('id_user',$book->cedula)->where('estado','1')->where('id_empresa',$id_empresa)->first();
								//dd($rol, $nomina);
								$detalle = $book->detalle ?? $book->observacion;
								if (!is_null($rol)) {
									//$detalle_rol = $rol->detalle;
									//dd("entra1", $rol, $nomina, $book);
									//$nomina = $rol->ct_nomina;

									if ($prestamo == 1) {

										$arr_qui = [
											'id_rol' 			=> $rol->id,
											'valor_cuota'		=> $book->valor,
											//'detalle_cuota'		=> $book->detalle,
											'detalle_cuota'		=> $detalle,
											'id_usuariocrea'	=> $idusuario,
											'id_usuariomod'		=> $idusuario,
											'ip_creacion'       => $ip_cliente,
											'ip_modificacion'   => $ip_cliente,
										];

										Ct_Rh_Cuotas_Quirografario::create($arr_qui);

										$log = [

											'id_usuario'     	=> $idusuario,
											'nombre_archivo' 	=> $nombre_original,
											'id_usuariomod'		=> $idusuario,
											'id_usuariomod'		=> $idusuario,
											'id_usuariocrea'    => $idusuario,
											'ip_creacion'		=> $ip_cliente,
											'ip_modificacion'	=> $ip_cliente,
											'tipo_plantilla'    => 'prestamos',
										];
										Log_horas_extras::create($log);
									} else {
										$arr_hip = [
											'id_rol' 			=> $rol->id,
											'valor_cuota'		=> $book->valor,
											//'detalle_cuota'		=> $book->detalle,
											'detalle_cuota'		=> $detalle,
											'id_usuariocrea'	=> $idusuario,
											'id_usuariomod'		=> $idusuario,
											'ip_creacion'       => $ip_cliente,
											'ip_modificacion'   => $ip_cliente,
										];

										Ct_Rh_Cuotas_Hipotecarios::create($arr_hip);

										$log = [

											'id_usuario'     	=> $idusuario,
											'nombre_archivo' 	=> $nombre_original,
											'id_usuariomod'		=> $idusuario,
											'id_usuariomod'		=> $idusuario,
											'id_usuariocrea'    => $idusuario,
											'ip_creacion'		=> $ip_cliente,
											'ip_modificacion'	=> $ip_cliente,
											'tipo_plantilla'    => 'prestamos',
										];
										Log_horas_extras::create($log);
									}
								} else {
									//dd($rol, $book, "entra 2");
									$ct_nomina = Ct_Nomina::where('id_user', $book->cedula)->where('estado', '1')->where('id_empresa', $id_empresa)->first();
									//dd($ct_nomina, $book, "aqui");

									$arr_rol = [
										'id_nomina'			=> $ct_nomina->id,
										'id_user'			=> $book->cedula,
										'id_empresa'		=> $id_empresa,
										'anio'				=> $anio,
										'mes'				=> $mes,
										'id_tipo_rol'		=> 1,
										'fecha_elaboracion'	=> $fecha_actual,
										'id_usuariocrea'	=> $idusuario,
										'id_usuariomod'		=> $idusuario,
										'ip_creacion'       => $ip_cliente,
										'ip_modificacion'   => $ip_cliente,

									];

									$id_rol = Ct_Rol_Pagos::insertGetId($arr_rol);
									//dd($id_rol);

									$arr_det = [
										'id_rol'			=> $id_rol,
										'dias_laborados'	=> 30,
										'sueldo_mensual'	=> $ct_nomina->sueldo_neto,
										'bonificacion'		=> $ct_nomina->bono,
										'bono_imputable'	=> $ct_nomina->bono_imputable,
										'alimentacion'		=> $ct_nomina->alimentacion,
										'seguro_privado'	=> $ct_nomina->seguro_privado,
										'impuesto_renta'	=> $ct_nomina->impuesto_renta,
										'id_usuariocrea'	=> $idusuario,
										'id_usuariomod'		=> $idusuario,
										'ip_creacion'       => $ip_cliente,
										'ip_modificacion'   => $ip_cliente,
									];

									Ct_Detalle_Rol::create($arr_det);

									if ($prestamo == 1) {
										//dd($prestamo);
										$arr_qui = [
											'id_rol' 			=> $id_rol,
											'valor_cuota'		=> $book->valor,
											//'detalle_cuota'		=> $book->detalle,
											'detalle_cuota'		=> $detalle,
											'id_usuariocrea'	=> $idusuario,
											'id_usuariomod'		=> $idusuario,
											'ip_creacion'       => $ip_cliente,
											'ip_modificacion'   => $ip_cliente,
										];

										Ct_Rh_Cuotas_Quirografario::create($arr_qui);
									} else {
										//dd($prestamo);
										$arr_hip = [
											'id_rol' 			=> $id_rol,
											'valor_cuota'		=> $book->valor,
											//'detalle_cuota'		=> $book->detalle,
											'detalle_cuota'		=> $detalle,
											'id_usuariocrea'	=> $idusuario,
											'id_usuariomod'		=> $idusuario,
											'ip_creacion'       => $ip_cliente,
											'ip_modificacion'   => $ip_cliente,
										];

										Ct_Rh_Cuotas_Hipotecarios::create($arr_hip);
									}

									$log = [

										'id_usuario'     	=> $idusuario,
										'nombre_archivo' 	=> $nombre_original,
										'id_usuariomod'		=> $idusuario,
										'id_usuariomod'		=> $idusuario,
										'id_usuariocrea'    => $idusuario,
										'ip_creacion'		=> $ip_cliente,
										'ip_modificacion'	=> $ip_cliente,
										'tipo_plantilla'    => 'prestamos',
									];
									Log_horas_extras::create($log);

									//dd($nomina, $rol, $book, "entra 2");
								}
							}
						}


						$cant++;
					}
				});
		}
		return "ok";
	}

	public function subir_horas_extras(Request $request)
	{
		$idusuario       = Auth::user()->id;
		$ip_cliente   = $_SERVER["REMOTE_ADDR"];
		$id_empresa		 = $request->session()->get('id_empresa');
		$empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
		$anio = $request['anio'];
		$mes = $request['mes'];
		$fecha_actual = Date('Y-m-d H:i:s');
		$nombre_original = $request['archivo']->getClientOriginalName();
		$extension       = $request['archivo']->getClientOriginalExtension();
		$nuevo_nombre    = "plantilla_horas" . rand(0, 999999) . "." . $extension;

		$r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));
		$rutadelaimagen = base_path() . '/storage/app/avatars/' . $nuevo_nombre;
		if ($r1) {
			Excel::filter('chunk')
				->formatDates(true, 'Y-m-d')
				->load($rutadelaimagen)
				->chunk(250, function ($reader) use ($idusuario, $anio, $mes, $id_empresa, $fecha_actual, $ip_cliente, $nombre_original) {
					//dd($reader);
					foreach ($reader as $book) {
						if (!is_null($book)) {

							$log = [

								'id_usuario'     	=> $idusuario,
								'nombre_archivo' 	=> $nombre_original,
								'id_usuariomod'		=> $idusuario,
								'ip_modificacion'   => $ip_cliente,
								'ip_creacion'		=> $ip_cliente,
								'ip_modificacion'	=> $ip_cliente,
								'id_usuariocrea'	=> $idusuario,
								'tipo_plantilla'    => 'horas extras',
							];
							$id_log = Log_horas_extras::insertGetId($log);


							if (!is_null($book->cedula)) {
								$rol = Ct_Rol_Pagos::where('id_user', $book->cedula)->where('mes', $mes)->where('anio', $anio)->where('estado', '1')->where('id_empresa', $id_empresa)->first();
								$nomina = Ct_Nomina::where('id_user', $book->cedula)->where('estado', '1')->where('id_empresa', $id_empresa)->first();

								$val_aport_pers = Ct_Rh_Valores::where('id_empresa', $id_empresa)->where('tipo', 1)->where('id', $nomina->aporte_personal)->first();

								$base_iess = ($book->sueldo) + ($book->total50) + ($book->total100) + ($nomina->bono_imputable);
								//dd($base_iess);
								$calculo_aporte = ($base_iess*$val_aport_pers->valor)/100;
								//dd($calculo_aporte);
								$calculo_aporte = round($calculo_aporte,2);

								//dd($calculo_aporte);

								$arr_det_log =[
									'id_empleado'			=> $book->cedula,
									'nombre'				=> $book->colaborador,
									'sueldo'				=> $book->sueldo,
									'valor_horas50'			=> $book->valor_horas50,
									'valor_horas100'		=> $book->valor_horas100,
									'num_horas50'			=> $book->num_horas50,
									'num_horas100'			=> $book->num_horas100,
									'total50'				=> $book->total50,
									'total100'				=> $book->total100,
									'total'					=> $book->total,
									'id_usuariocrea'		=> $idusuario,
									'id_usuariomod'			=> $idusuario,
									'ip_creacion'			=> $ip_cliente,
									'ip_modificacion'		=> $ip_cliente,
									'id_log_horas'			=> $id_log,
								];

								$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);

								if (!is_null($rol)) {

									$detalle_rol =  Ct_Detalle_Rol::where('id_rol', $rol->id)->where('estado', '1')->first();

									if (!is_null($detalle_rol)) {

										

										$arr_det = [
											'sueldo_mensual'	=> $book->sueldo,
											'cantidad_horas50'	=> $book->num_horas50,
											'cantidad_horas100'	=> $book->num_horas100,
											'sobre_tiempo50'	=> $book->total50,
											'sobre_tiempo100'	=> $book->total100,
											'base_iess'         => $base_iess,
											'porcentaje_iess'   => $calculo_aporte,
											'id_usuariomod'		=> $idusuario,
											'ip_modificacion'   => $ip_cliente,
										];

										$detalle_rol->update($arr_det);

										/*$log = [

											'id_usuario'     	=> $idusuario,
											'nombre_archivo' 	=> $nombre_original,
											'id_usuariomod'		=> $idusuario,
											'ip_modificacion'   => $ip_cliente,
											'ip_creacion'		=> $ip_cliente,
											'ip_modificacion'	=> $ip_cliente,
											'id_usuariocrea'	=> $idusuario,
											'tipo_plantilla'    => 'horas extras',
										];
										Log_horas_extras::create($log);*/
									}
								} else {
									$arr_rol = Ct_Rol_Pagos::insertGetId([
										'id_nomina'			=> $nomina->id,
										'id_user'			=> $book->cedula,
										'id_empresa'		=> $id_empresa,
										'anio'				=> $anio,
										'mes'				=> $mes,
										'id_tipo_rol'		=> 1,
										'fecha_elaboracion'	=> $fecha_actual,
										'id_usuariocrea'	=> $idusuario,
										'id_usuariomod'		=> $idusuario,
										'ip_creacion'       => $ip_cliente,
										'ip_modificacion'   => $ip_cliente,
									]);

									$arr_det = [
										'id_rol'			=> $arr_rol,
										'dias_laborados'	=> 30,
										'sueldo_mensual'	=> $book->sueldo,
										'bonificacion'		=> $nomina->bono,
										'bono_imputable'	=> $nomina->bono_imputable,
										'alimentacion'		=> $nomina->alimentacion,
										'seguro_privado'	=> $nomina->seguro_privado,
										'impuesto_renta'	=> $nomina->impuesto_renta,
										'cantidad_horas50'	=> $book->num_horas50,
										'cantidad_horas100'	=> $book->num_horas100,
										'sobre_tiempo50'	=> $book->total50,
										'sobre_tiempo100'	=> $book->total100,
										'base_iess'         => $base_iess,
										'porcentaje_iess'   => $calculo_aporte,
										'fond_reserv_cobrar' => '0,00',
										'otros_egresos'		=> '0,00',
										'id_usuariocrea'	=> $idusuario,
										'id_usuariomod'		=> $idusuario,
										'ip_creacion'       => $ip_cliente,
										'ip_modificacion'   => $ip_cliente,
									];

									Ct_Detalle_Rol::create($arr_det);

									/*$log = [

										'id_usuario'     	=> $idusuario,
										'nombre_archivo' 	=> $nombre_original,
										'id_usuariomod'		=> $idusuario,
										'id_usuariomod'		=> $idusuario,
										'id_usuariocrea'    => $idusuario,
										'ip_creacion'		=> $ip_cliente,
										'ip_modificacion'	=> $ip_cliente,
										'tipo_plantilla'    => 'horas extras',
									];
									Log_horas_extras::create($log);*/
								}
							}
						}
					}
				});
		}

		return "ok";
	}

	public function excel_plantilla_prestamo()
	{
		Excel::create('Prestamos', function ($excel) {
			$excel->sheet('Prestamos', function ($sheet) {
				$sheet->cell('A1', function ($cell) {
					$cell->setValue('no');
					$cell->setAlignment('center');
					$cell->setFontWeight('bold');
					$cell->setBorder('thin', 'thin', 'thin', 'thin');
				});
				$sheet->cell('B1', function ($cell) {
					$cell->setValue('cedula');
					$cell->setAlignment('center');
					$cell->setFontWeight('bold');
					$cell->setBorder('thin', 'thin', 'thin', 'thin');
				});

				$sheet->cell('C1', function ($cell) {
					$cell->setValue('nombres');
					$cell->setAlignment('center');
					$cell->setFontWeight('bold');
					$cell->setBorder('thin', 'thin', 'thin', 'thin');
				});

				$sheet->cell('D1', function ($cell) {
					$cell->setValue('detalle');
					$cell->setAlignment('center');
					$cell->setFontWeight('bold');
					$cell->setBorder('thin', 'thin', 'thin', 'thin');
				});

				$sheet->cell('E1', function ($cell) {
					$cell->setValue('valor');
					$cell->setAlignment('center');
					$cell->setFontWeight('bold');
					$cell->setBorder('thin', 'thin', 'thin', 'thin');
				});
			});

			$excel->getActiveSheet()->getColumnDimension("A")->setWidth(7)->setAutosize(false);
			$excel->getActiveSheet()->getColumnDimension("B")->setWidth(12)->setAutosize(false);
			$excel->getActiveSheet()->getColumnDimension("C")->setWidth(45)->setAutosize(false);
			$excel->getActiveSheet()->getColumnDimension("D")->setWidth(30)->setAutosize(false);
			$excel->getActiveSheet()->getColumnDimension("E")->setWidth(11)->setAutosize(false);
		})->export('xlsx');
	}

	public function excel_plantilla_horas(Request $request)
	{
		$id_empresa = $request->session()->get('id_empresa');
		$nominas = Ct_Nomina::where('estado', '1')->where('id_empresa', $id_empresa)->get();

		Excel::create('Horas_Extras', function ($excel) use ($nominas) {
			$excel->sheet('Horas_Extras', function ($sheet) use($nominas) {
				$sheet->cell('A1', function ($cell) {
					$cell->setValue('cedula');
					$cell->setAlignment('center');
					$cell->setFontWeight('bold');
					$cell->setBorder('thin', 'thin', 'thin', 'thin');
				});
				$sheet->cell('B1', function ($cell) {
					$cell->setValue('colaborador');
					$cell->setAlignment('center');
					$cell->setFontWeight('bold');
					$cell->setBorder('thin', 'thin', 'thin', 'thin');
				});

				$sheet->cell('C1', function ($cell) {
					$cell->setValue('sueldo');
					$cell->setAlignment('center');
					$cell->setFontWeight('bold');
					$cell->setBorder('thin', 'thin', 'thin', 'thin');
				});

				$sheet->cell('D1', function ($cell) {
					$cell->setValue('valor_horas50');
					$cell->setAlignment('center');
					$cell->setFontWeight('bold');
					$cell->setBorder('thin', 'thin', 'thin', 'thin');
				});

				$sheet->cell('E1', function ($cell) {
					$cell->setValue('valor_horas100');
					$cell->setAlignment('center');
					$cell->setFontWeight('bold');
					$cell->setBorder('thin', 'thin', 'thin', 'thin');
				});

				$sheet->cell('F1', function ($cell) {
					$cell->setValue('num_horas50');
					$cell->setAlignment('center');
					$cell->setFontWeight('bold');
					$cell->setBorder('thin', 'thin', 'thin', 'thin');
				});

				$sheet->cell('G1', function ($cell) {
					$cell->setValue('num_horas100');
					$cell->setAlignment('center');
					$cell->setFontWeight('bold');
					$cell->setBorder('thin', 'thin', 'thin', 'thin');
				});

				$sheet->cell('H1', function ($cell) {
					$cell->setValue('total50');
					$cell->setAlignment('center');
					$cell->setFontWeight('bold');
					$cell->setBorder('thin', 'thin', 'thin', 'thin');
				});

				$sheet->cell('I1', function ($cell) {
					$cell->setValue('total100');
					$cell->setAlignment('center');
					$cell->setFontWeight('bold');
					$cell->setBorder('thin', 'thin', 'thin', 'thin');
				});

				$sheet->cell('J1', function ($cell) {
					$cell->setValue('total');
					$cell->setAlignment('center');
					$cell->setFontWeight('bold');
					$cell->setBorder('thin', 'thin', 'thin', 'thin');
				});
				$i = 2;
				foreach($nominas as $nomina){

					$sheet->cell('A'.$i, function ($cell) use($nomina) {
						$cell->setValue($nomina->id_user);
						$cell->setAlignment('center');
						$cell->setFontWeight('bold');
						$cell->setBorder('thin', 'thin', 'thin', 'thin');
					});
					$sheet->cell('B'.$i, function ($cell) use($nomina) {
						$cell->setValue($nomina->user->apellido1.' '.$nomina->user->apellido2.' '.$nomina->user->nombre1.' '.$nomina->user->nombre2);
						$cell->setAlignment('center');
						$cell->setFontWeight('bold');
						$cell->setBorder('thin', 'thin', 'thin', 'thin');
					});

					$sheet->cell('C'.$i, function ($cell) use($nomina) {
						$cell->setValue($nomina->sueldo_neto);
						$cell->setAlignment('center');
						$cell->setFontWeight('bold');
						$cell->setBorder('thin', 'thin', 'thin', 'thin');
					});

					$sheet->cell('D'.$i, function ($cell) use($nomina) {
						$cell->setValue(' ');
						$cell->setAlignment('center');
						$cell->setFontWeight('bold');
						$cell->setBorder('thin', 'thin', 'thin', 'thin');
					});

					$sheet->cell('E'.$i, function ($cell) use($nomina) {
						$cell->setValue(' ');
						$cell->setAlignment('center');
						$cell->setFontWeight('bold');
						$cell->setBorder('thin', 'thin', 'thin', 'thin');
					});

					$sheet->cell('F'.$i, function ($cell) use($nomina) {
						$cell->setValue(' ');
						$cell->setAlignment('center');
						$cell->setFontWeight('bold');
						$cell->setBorder('thin', 'thin', 'thin', 'thin');
					});

					$sheet->cell('G'.$i, function ($cell) use($nomina) {
						$cell->setValue(' ');
						$cell->setAlignment('center');
						$cell->setFontWeight('bold');
						$cell->setBorder('thin', 'thin', 'thin', 'thin');
					});

					$sheet->cell('H'.$i, function ($cell) use($nomina) {
						$cell->setValue(' ');
						$cell->setAlignment('center');
						$cell->setFontWeight('bold');
						$cell->setBorder('thin', 'thin', 'thin', 'thin');
					});

					$sheet->cell('I'.$i, function ($cell) use($nomina) {
						$cell->setValue(' ');
						$cell->setAlignment('center');
						$cell->setFontWeight('bold');
						$cell->setBorder('thin', 'thin', 'thin', 'thin');
					});

					$sheet->cell('J'.$i, function ($cell) use($nomina) {
						$cell->setValue(' ');
						$cell->setAlignment('center');
						$cell->setFontWeight('bold');
						$cell->setBorder('thin', 'thin', 'thin', 'thin');
					});	
					$i++;

				}
			});

			$excel->getActiveSheet()->getColumnDimension("A")->setWidth(11)->setAutosize(false);
			$excel->getActiveSheet()->getColumnDimension("B")->setWidth(40)->setAutosize(false);
			$excel->getActiveSheet()->getColumnDimension("C")->setWidth(10)->setAutosize(false);
			$excel->getActiveSheet()->getColumnDimension("D")->setWidth(13)->setAutosize(false);
			$excel->getActiveSheet()->getColumnDimension("E")->setWidth(14)->setAutosize(false);
			$excel->getActiveSheet()->getColumnDimension("F")->setWidth(13)->setAutosize(false);
			$excel->getActiveSheet()->getColumnDimension("G")->setWidth(13)->setAutosize(false);
			$excel->getActiveSheet()->getColumnDimension("H")->setWidth(8)->setAutosize(false);
			$excel->getActiveSheet()->getColumnDimension("I")->setWidth(9)->setAutosize(false);
			$excel->getActiveSheet()->getColumnDimension("J")->setWidth(11)->setAutosize(false);
		})->export('xlsx');
	}

	public function modalsubir(Request $request)
	{
		$id = $request['id'];
		return view("contable/ventas/modalpdf", ['id' => $id]);
	}

	public function subir_pdf(Request $request)
	{

		$id = $request['id'];
		$nombre_original = $request['file']->getClientOriginalName();
		$extension       = $request['file']->getClientOriginalExtension();
		$r1 = Storage::disk('public')->put($nombre_original, \File::get($request['file']));
		$rutadelaimagen = $nombre_original;
		$editar = DB::table('ct_ventas')->where('id', $id)->update(['rutapdf' => $rutadelaimagen]);
		$id_empresa = $request->session()->get('id_empresa');
		$empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
		$ventas = Ct_ventas::where('estado', '<', 2)
			->where('id_empresa', $id_empresa)
			->where('tipo', "VEN-FA")
			->orderby('id', 'desc')->paginate(10);
		return view('contable/ventas/index', ['ventas' => $ventas, 'empresa' => $empresa]);
	}

	public function pdf_visualizar(Request $request, $id)
	{
		$visualizar = Ct_ventas::where('id', $id)->first();
		$ver = $visualizar->rutapdf;
		$path = base_path() . "/storage/app/avatars/" . $visualizar->rutapdf;
		//dd($path);
		return Response::make(file_get_contents($path), 200, [
			'Content-Type' => 'application/pdf',
			'Content-Disposition' => 'inline; filename="' . $ver . '"'
		]);
	}

	
}
