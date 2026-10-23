<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BdPagoEnLinea extends Model
{
    public static function checkId($data)
    {
        $empresa = Empresa::where('prioridad', 1)->where('estado', 1)->first();
        if (is_null($empresa->id_entidad) or is_null($empresa->access_token) or is_null($empresa->shopper_mid) or is_null($empresa->shopper_tid)) {
            return ['status' => 'error', 'msj' => 'No se ha configurado correctamente la empresa'];
        }
        $items =  $data["cart_items"];
        unset($data["cart_items"]);
        $arr['entityId'] = $empresa->id_entidad; //
        $arr['amount'] = $data['total'];
        $arr['currency'] = "USD"; //
        $arr['paymentType'] = "DB"; //
        $arr['customer.givenName'] = $data['nombre1'];
        $arr['customer.middleName'] = $data['nombre2'];
        $arr['customer.surname'] = $data['apellido'];
        $arr['customer.ip'] = $data['ip_cliente'];
        $arr['customer.merchantCustomerId'] = $data['identificacion']; //
        $arr['merchantTransactionId'] = 'transaction_' . $data['id_transaccion'];
        $arr['customer.email'] = $data['email'];
        $arr['customer.identificationDocType'] = 'IDCARD'; //
        $arr['customer.identificationDocId'] = $data['identificacion'];
        $arr['customer.phone'] = $data['telefono'];
        $arr['billing.street1'] = $data['direccion_cliente'];
        $arr['billing.country'] = $data['pais_compra'];
        $arr['shipping.street1'] = $data['direccion_envio'];
        $arr['shipping.country'] = $data['pais_entrega'];
        $arr['customParameters[SHOPPER_ECI]'] = '0103910'; //
        $arr['customParameters[SHOPPER_PSERV]'] = '17913101'; //
        $arr['customParameters[SHOPPER_VAL_BASE0]'] = $data['subtotal_0'];
        $arr['customParameters[SHOPPER_VAL_BASEIMP]'] = $data['subtotal_12'];
        $arr['customParameters[SHOPPER_VAL_IVA]'] = $data['valor_iva'];
        $arr['customParameters[SHOPPER_MID]'] = $empresa->shopper_mid; // DEBEN DARNOS UNO DATAFAST
        $arr['customParameters[SHOPPER_TID]'] = $empresa->shopper_tid; //DEBEN DARNOS UNO DATAFAST
        $arr['risk.parameters[USER_DATA2]'] = $empresa->risk; //
        $arr['customParameters[SHOPPER_VERSIONDF]'] = '2'; // ESTO ES FIJO
        if ($empresa->test_mode == 1) {
            $arr['testMode'] = 'EXTERNAL'; // ELIMINAR CUANDO ESTEMOS EN PROD,
        }

        $data = http_build_query($arr);
        for ($i = 0; $i < count($items); $i++) {
            $data .= "&cart.items[{$i}].name={$items[$i]['name']}";
            $data .= "&cart.items[{$i}].description={$items[$i]['description']}";
            $data .= "&cart.items[{$i}].price={$items[$i]['price']}";
            $data .= "&cart.items[{$i}].quantity={$items[$i]['quantity']}";
        }
        $url = $empresa->url_datafast . "/v1/checkouts";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . $empresa->access_token
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if ($empresa->test_mode == 1) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
        } else if ($empresa->test_mode == 0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // this should be set to true in production
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        // dd($responseData);
        return $responseData;
    }

    public static function confirmarPago($resourcePath)
    {
        $empresa = Empresa::where('prioridad', 1)->where('estado', 1)->first();
        if (is_null($empresa->id_entidad) or is_null($empresa->access_token) or is_null($empresa->shopper_mid) or is_null($empresa->shopper_tid)) {
            return ['status' => 'error', 'msj' => 'No se ha configurado correctamente la empresa'];
        }
        $url = $empresa->url_datafast . $resourcePath;
        $url .= "?entityId=" . $empresa->id_entidad;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . $empresa->access_token
        ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        if ($empresa->test_mode == 1) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
        } else if ($empresa->test_mode == 0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // this should be set to true in production
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return $responseData;
    }
}
