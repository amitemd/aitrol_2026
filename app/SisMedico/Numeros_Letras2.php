<?php

/**
 * Clase que implementa un coversor de números
 * a letras.
 *
 * Soporte para PHP >= 5.4
 * Para soportar PHP 5.3, declare los arreglos
 * con la función array.
 *
 * @author AxiaCore S.A.S
 *
 */

namespace Sis_medico;

use NumberFormatter;

class Numeros_Letras2
{
    private $unidades = [
        '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'
    ];

    private $decenas = [
        '', 'diez', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'
    ];

    private $centenas = [
        '', 'cien', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'
    ];

    private $especiales = [
        11 => 'once', 12 => 'doce', 13 => 'trece', 14 => 'catorce', 15 => 'quince',
        16 => 'dieciséis', 17 => 'diecisiete', 18 => 'dieciocho', 19 => 'diecinueve'
    ];

    public function convertir($numero, $moneda = '', $centimos = '')
    {
        $partes = explode('.', $numero);
        $entero = (int)$partes[0];
        $decimal = isset($partes[1]) ? (int)$partes[1] : 0;

        $letras_entero = $this->convertirParteEntera($entero);
        $letras_decimal = $this->convertirParteEntera($decimal);

        $resultado = ucfirst($letras_entero) . " $moneda";
        if ($decimal > 0) {
            $resultado .= " con $letras_decimal $centimos";
        }

        return $resultado;
    }

    private function convertirParteEntera($numero)
    {
        if ($numero == 0) {
            return 'cero';
        }

        if ($numero >= 1000) {
            return $this->convertirParteEntera(intval($numero / 1000)) . " mil " . $this->convertirParteEntera($numero % 1000);
        }

        if ($numero >= 100) {
            return $this->centenas[intval($numero / 100)] . " " . $this->convertirParteEntera($numero % 100);
        }

        if ($numero >= 20) {
            return $this->decenas[intval($numero / 10)] . " y " . $this->convertirParteEntera($numero % 10);
        }

        if ($numero >= 10) {
            return $this->especiales[$numero];
        }

        return $this->unidades[$numero];
    }
}
