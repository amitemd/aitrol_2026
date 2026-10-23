<?php


function unidad($numuero)
{
	switch ($numuero) {
		case 9: {
				$numu = trans('v3/new_varios.NUEVE');
				break;
			}
		case 8: {
				$numu = trans('v3/new_varios.OCHO');
				break;
			}
		case 7: {
				$numu = trans('v3/new_varios.SIETE');
				break;
			}
		case 6: {
				$numu = trans('v3/new_varios.SEIS');
				break;
			}
		case 5: {
				$numu = trans('v3/new_varios.CINCO');
				break;
			}
		case 4: {
				$numu = trans('v3/new_varios.CUATRO');
				break;
			}
		case 3: {
				$numu = trans('v3/new_varios.TRES');
				break;
			}
		case 2: {
				$numu = trans('v3/new_varios.DOS');
				break;
			}
		case 1: {
				$numu = trans('v3/new_varios.UN');
				break;
			}
		case 0: {
				$numu = "";
				break;
			}
	}
	return $numu;
}
function decena($numdero)
{
	if ($numdero >= 90 && $numdero <= 99) {
		$numd = trans('v3/new_varios.NOVENTA');
		if ($numdero > 90)
			$numd = $numd . "Y " . (unidad($numdero - 90));
	} else if ($numdero >= 80 && $numdero <= 89) {
		$numd = trans('v3/new_varios.OCHENTA');
		if ($numdero > 80)
			$numd = $numd . "Y " . (unidad($numdero - 80));
	} else if ($numdero >= 70 && $numdero <= 79) {
		$numd = trans('v3/new_varios.SETENTA');
		if ($numdero > 70)
			$numd = $numd . "Y " . (unidad($numdero - 70));
	} else if ($numdero >= 60 && $numdero <= 69) {
		$numd = trans('v3/new_varios.SESENTA');
		if ($numdero > 60)
			$numd = $numd . "Y " . (unidad($numdero - 60));
	} else if ($numdero >= 50 && $numdero <= 59) {
		$numd = trans('v3/new_varios.CINCUENTA');
		if ($numdero > 50)
			$numd = $numd . "Y " . (unidad($numdero - 50));
	} else if ($numdero >= 40 && $numdero <= 49) {
		$numd = trans('v3/new_varios.CUARENTA');
		if ($numdero > 40)
			$numd = $numd . "Y " . (unidad($numdero - 40));
	} else if ($numdero >= 30 && $numdero <= 39) {
		$numd = trans('v3/new_varios.TREINTA');
		if ($numdero > 30)
			$numd = $numd . "Y " . (unidad($numdero - 30));
	} else if ($numdero >= 20 && $numdero <= 29) {
		if ($numdero == 20)
			$numd = trans('v3/new_varios.VEINTE');
		else
			$numd = trans('v3/new_varios.VEINTI') . (unidad($numdero - 20));
	} else if ($numdero >= 10 && $numdero <= 19) {
		switch ($numdero) {
			case 10: {
					$numd = trans('v3/new_varios.DIEZ');
					break;
				}
			case 11: {
					$numd = trans('v3/new_varios.ONCE');
					break;
				}
			case 12: {
					$numd = trans('v3/new_varios.DOCE');
					break;
				}
			case 13: {
					$numd = trans('v3/new_varios.TRECE');
					break;
				}
			case 14: {
					$numd = trans('v3/new_varios.CATORCE');
					break;
				}
			case 15: {
					$numd = trans('v3/new_varios.QUINCE');
					break;
				}
			case 16: {
					$numd = trans('v3/new_varios.DIECISEIS');
					break;
				}
			case 17: {
					$numd = trans('v3/new_varios.DIESICIETE');
					break;
				}
			case 18: {
					$numd = trans('v3/new_varios.DICEIOCHO');
					break;
				}
			case 19: {
					$numd = trans('v3/new_varios.DIECINUEVE');
					break;
				}
		}
	} else
		$numd = unidad($numdero);
	return $numd;
}
function centena($numc)
{
	if ($numc >= 100) {
		if ($numc >= 900 && $numc <= 999) {
			$numce = trans('v3/new_varios.NOVECIENTOS');
			if ($numc > 900)
				$numce = $numce . (decena($numc - 900));
		} else if ($numc >= 800 && $numc <= 899) {
			$numce = trans('v3/new_varios.OCHOCIENTOS');
			if ($numc > 800)
				$numce = $numce . (decena($numc - 800));
		} else if ($numc >= 700 && $numc <= 799) {
			$numce = trans('v3/new_varios.SETECIENTOS');
			if ($numc > 700)
				$numce = $numce . (decena($numc - 700));
		} else if ($numc >= 600 && $numc <= 699) {
			$numce = trans('v3/new_varios.SEISCIENTOS');
			if ($numc > 600)
				$numce = $numce . (decena($numc - 600));
		} else if ($numc >= 500 && $numc <= 599) {
			$numce = trans('v3/new_varios.QUINIENTOS');
			if ($numc > 500)
				$numce = $numce . (decena($numc - 500));
		} else if ($numc >= 400 && $numc <= 499) {
			$numce = trans('contableM.CUATROCIENTOS');
			if ($numc > 400)
				$numce = $numce . (decena($numc - 400));
		} else if ($numc >= 300 && $numc <= 399) {
			$numce = trans('v3/new_varios.TRESCIENTOS');
			if ($numc > 300)
				$numce = $numce . (decena($numc - 300));
		} else if ($numc >= 200 && $numc <= 299) {
			$numce = trans('v3/new_varios.DOSCIENTOS');
			if ($numc > 200)
				$numce = $numce . (decena($numc - 200));
		} else if ($numc >= 100 && $numc <= 199) {
			if ($numc == 100)
				$numce = trans('v3/new_varios.CIEN');
			else
				$numce = trans('v3/new_varios.CIENTO') . (decena($numc - 100));
		}
	} else
		$numce = decena($numc);
	return $numce;
}
function miles($nummero)
{
	if ($nummero >= 1000 && $nummero < 2000) {
		$numm = trans('v3/new_varios.MIL') . (centena($nummero % 1000));
	}
	if ($nummero >= 2000 && $nummero < 10000) {
		$numm = unidad(Floor($nummero / 1000)) . trans('v3/new_varios.MIL') . (centena($nummero % 1000));
	}
	if ($nummero < 1000)
		$numm = centena($nummero);

	return $numm;
}
function decmiles($numdmero)
{
	if ($numdmero == 10000)
		$numde = trans('v3/new_varios.DIEZMIL');
	if ($numdmero > 10000 && $numdmero < 20000) {
		$numde = decena(Floor($numdmero / 1000)) . trans('v3/new_varios.MIL') . (centena($numdmero % 1000));
	}
	if ($numdmero >= 20000 && $numdmero < 100000) {
		$numde = decena(Floor($numdmero / 1000)) . trans('v3/new_varios.MIL') . (miles($numdmero % 1000));
	}
	if ($numdmero < 10000)
		$numde = miles($numdmero);
	return $numde;
}
function cienmiles($numcmero)
{
	if ($numcmero == 100000)
		$num_letracm = trans('v3/new_varios.CIENMIL');
	if ($numcmero >= 100000 && $numcmero < 1000000) {
		$num_letracm = centena(Floor($numcmero / 1000)) . trans('v3/new_varios.MIL') . (centena($numcmero % 1000));
	}
	if ($numcmero < 100000)
		$num_letracm = decmiles($numcmero);
	return $num_letracm;
}
function millon($nummiero)
{
	if ($nummiero >= 1000000 && $nummiero < 2000000) {
		$num_letramm = trans('v3/new_varios.UNMILLON') . (cienmiles($nummiero % 1000000));
	}
	if ($nummiero >= 2000000 && $nummiero < 10000000) {
		$num_letramm = unidad(Floor($nummiero / 1000000)) . trans('v3/new_varios.MILLONES') . (cienmiles($nummiero % 1000000));
	}
	if ($nummiero < 1000000)
		$num_letramm = cienmiles($nummiero);
	return $num_letramm;
}
function decmillon($numerodm)
{
	if ($numerodm == 10000000)
		$num_letradmm = trans('v3/new_varios.DIEZMILLONES');
	if ($numerodm > 10000000 && $numerodm < 20000000) {
		$num_letradmm = decena(Floor($numerodm / 1000000)) . trans('v3/new_varios.MILLONES') . (cienmiles($numerodm % 1000000));
	}
	if ($numerodm >= 20000000 && $numerodm < 100000000) {
		$num_letradmm = decena(Floor($numerodm / 1000000)) . trans('v3/new_varios.MILLONES') . (millon($numerodm % 1000000));
	}
	if ($numerodm < 10000000)
		$num_letradmm = millon($numerodm);
	return $num_letradmm;
}
function cienmillon($numcmeros)
{
	if ($numcmeros == 100000000)
		$num_letracms = trans('v3/new_varios.CIENMILLONES');
	if ($numcmeros >= 100000000 && $numcmeros < 1000000000) {
		$num_letracms = centena(Floor($numcmeros / 1000000)) . trans('v3/new_varios.MILLONES') . (millon($numcmeros % 1000000));
	}
	if ($numcmeros < 100000000)
		$num_letracms = decmillon($numcmeros);
	return $num_letracms;
}
function milmillon($nummierod)
{
	if ($nummierod >= 1000000000 && $nummierod < 2000000000) {
		$num_letrammd = trans('v3/new_varios.MIL') . (cienmillon($nummierod % 1000000000));
	}
	if ($nummierod >= 2000000000 && $nummierod < 10000000000) {
		$num_letrammd = unidad(Floor($nummierod / 1000000000)) . trans('v3/new_varios.MIL') . (cienmillon($nummierod % 1000000000));
	}
	if ($nummierod < 1000000000)
		$num_letrammd = cienmillon($nummierod);
	return $num_letrammd;
}
function convertir($numero, $centavos)
{
	if ($centavos >= 0 && $centavos < 100) {
		$centavos = $centavos * 100;
		$strctv = " $centavos/100 ";
		if ($centavos < 10) {
			$strctv = " 0$centavos/100 ";
		}
	} else {
		$strctv = " ";
	}
	$numf = milmillon($numero);
	return $numf . $strctv . trans('v3/new_varios.DOLARES');
}
?>