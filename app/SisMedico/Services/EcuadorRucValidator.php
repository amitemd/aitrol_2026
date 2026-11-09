<?php

namespace Sis_medico\Services;

class EcuadorRucValidator
{
    public function isValid($value)
    {
        $ruc = preg_replace('/\D+/', '', (string) $value);
        if (strlen($ruc) !== 13) {
            return false;
        }

        $third = (int) $ruc[2];
        if ($third < 6) {
            if (substr($ruc, 10, 3) !== '001') {
                return false;
            }
            $sum = 0;
            for ($i = 0; $i < 9; $i++) {
                $digit = (int) $ruc[$i];
                if ($i % 2 === 0) {
                    $digit *= 2;
                    if ($digit > 9) {
                        $digit -= 9;
                    }
                }
                $sum += $digit;
            }
            return ((10 - ($sum % 10)) % 10) === (int) $ruc[9];
        }

        if ($third === 6) {
            return $this->weightedCheck($ruc, array(3, 2, 7, 6, 5, 4, 3, 2), 8)
                && substr($ruc, 9, 4) === '0001';
        }

        if ($third === 9) {
            return $this->weightedCheck($ruc, array(4, 3, 2, 7, 6, 5, 4, 3, 2), 9)
                && substr($ruc, 10, 3) === '001';
        }

        return false;
    }

    private function weightedCheck($ruc, array $weights, $checkPosition)
    {
        $sum = 0;
        foreach ($weights as $index => $weight) {
            $sum += ((int) $ruc[$index]) * $weight;
        }
        $check = 11 - ($sum % 11);
        if ($check === 11) {
            $check = 0;
        } elseif ($check === 10) {
            return false;
        }
        return $check === (int) $ruc[$checkPosition];
    }
}
