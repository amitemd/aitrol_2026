<?php

namespace Sis_medico\Http\Controllers\sri_electronico;

use SplFixedArray;

class ClaveAcceso
{
    public $fecha_emision = "";
    public $tipo_comprobante = "";
    public $ruc = "";
    public $tipo_ambiente = "";
    public $punto_establecimiento = "";
    public $punto_emision = "";
    public $numero_comprobante = "";
    public $codigo_numerico = "";
    public $tipo_emision = "";
    public $digito = "";
    public $clave_acceso = "";
    public $clave_contigencia = "";
    public $isError = false;
    public $errores = array();

    public function llenarDatos($campos)
    {
        $fecha_emision = null;
        $tipo_comprobante = null;
        $ruc = null;
        $tipo_ambiente = null;
        $punto_establecimiento = null;
        $punto_emision = null;
        $numero_comprobante = null;
        $codigo_numerico = null;
        $tipo_emision = null;
        if (!is_null($campos)) {
            if (is_array($campos)) {
                $fecha = date('d-m-Y');
                if (isset($campos["fecha_emision"])) {
                    $fecha = str_replace('/', '-', $campos["fecha_emision"]);
                    $fecha = date('d-m-Y', strtotime($fecha));
                }
                $tipo_comprobante = $campos["tipo_comprobante"];
                $fecha_emision = isset($campos["fecha_emision"]) ? $campos["fecha_emision"] : $fecha; //date('d-m-Y');
                $ruc = isset($campos["ruc"]) ? $campos["ruc"] : null;
                $tipo_ambiente = isset($campos["tipo_ambiente"]) ? $campos["tipo_ambiente"] : null;
                $punto_establecimiento = isset($campos["punto_establecimiento"]) ? $campos["punto_establecimiento"] : null;
                $punto_emision = isset($campos["punto_emision"]) ? $campos["punto_emision"] : null;
                $numero_comprobante = isset($campos["numero_comprobante"]) ? $campos["numero_comprobante"] : null;
                $codigo_numerico = isset($campos["numero_comprobante"]) ? $campos["numero_comprobante"] : null;
                $tipo_emision = isset($campos["tipo_emision"]) ? $campos["tipo_emision"] : null;
            }
        }
        $this->isError = false;
        $this->fecha_emision = date("dmY", strtotime($fecha_emision ? $fecha_emision : date("d-m-Y")));
        $this->tipo_comprobante = str_pad($tipo_comprobante, 2, "0", STR_PAD_LEFT);
        $this->ruc = str_pad(trim($ruc), 13, "0", STR_PAD_LEFT);
        $this->tipo_ambiente = $tipo_ambiente;
        $this->punto_establecimiento = trim($punto_establecimiento);
        $this->punto_emision = trim($punto_emision);
        $this->numero_comprobante = trim($numero_comprobante) != "" ? str_pad($numero_comprobante, 9, "0", STR_PAD_LEFT) : "";
        $this->codigo_numerico = str_pad((int)$codigo_numerico, 8, "0", STR_PAD_LEFT);
        $this->tipo_emision = trim($tipo_emision) != "" ? $tipo_emision : 1;
        $this->ruc = str_pad(trim($ruc), 13, "0", STR_PAD_LEFT);
        $this->tipo_ambiente = $tipo_ambiente;
        $this->generaClave();
    }
    public function toArray()
    {
        $array = get_object_vars($this);
        if (!$this->isError) {
            unset($array["errores"]);
        }
        return $array;
    }

    public function generaClave()
    {
        $this->ValidarDatosClave();
        if ($this->isError) return null;
        $verificador = 0;
        $clave = $this->fecha_emision;
        $clave .= $this->tipo_comprobante;
        $clave .= $this->ruc;
        $clave .= $this->tipo_ambiente;
        $clave .= $this->punto_establecimiento;
        $clave .= $this->punto_emision;
        $clave .= $this->numero_comprobante;
        $clave .= $this->codigo_numerico;
        $clave .= $this->tipo_emision;
        $verificador = $this->generaDigitoModulo11($clave);
        $this->digito = $verificador;
        $clave .= $verificador;
        if (strlen($clave) != 49) {
            $this->clave_acceso = null;
        }
        $this->clave_acceso = trim($clave);
        return $this->clave_acceso;
    }
    public function generaClaveContingencia($fecha_emision, $tipo_comprobante, $clave_contigencia, $tipo_emision)
    {
        $this->ValidarDatosClaveContigencia();
        if ($this->isError) return null;
        $verificador = 0;
        $clave = $this->fecha_emision;
        $clave .= $this->tipo_comprobante;
        $clave .= $this->clave_contigencia;
        $clave .= $this->tipo_emision;
        $verificador = $this->generaDigitoModulo11($clave);
        if ($verificador != 10) {
            $this->digito = $verificador;
            $clave .= $verificador;
        }
        if (strlen($clave) != 49) {
            $this->clave_acceso = null;
        }
        $this->clave_acceso = trim($clave);
        return $this->clave_acceso;
    }
    public function generaDigitoModulo11($cadena)
    {
        $cadena = trim($cadena);
        $baseMultiplicador = 7;
        $aux = new SplFixedArray(strlen($cadena));
        $aux = $aux->toArray();
        $multiplicador = 2;
        $total = 0;
        $verificador = 0;
        for ($i = count($aux) - 1; $i >= 0; --$i) {
            $aux[$i] = substr($cadena, $i, 1);
            $aux[$i] *= $multiplicador;
            ++$multiplicador;
            if ($multiplicador > $baseMultiplicador) {
                $multiplicador = 2;
            }
            $total += $aux[$i];
        }

        if (($total == 0) || ($total == 1)) $verificador = 0;
        else {
            $verificador = (11 - ($total % 11) == 11) ? 0 : 11 - ($total % 11);
        }

        if ($verificador == 10) {
            $verificador = 1;
        }

        return $verificador;
    }

    public function getClaveAcceso()
    {
        return $this->clave_acceso;
    }

    private function ValidarDatosClave()
    {
        $error = array();
        if (strlen($this->fecha_emision) != 8) $error += array("fecha_emision" => "{$this->fecha_emision}");
        if (strlen($this->tipo_comprobante) != 2) $error += array("tipo_comprobante" => "{$this->tipo_comprobante}");
        if (strlen($this->ruc) != 13) $error += array("ruc" => "{$this->ruc}");
        if (strlen($this->tipo_ambiente) != 1) $error += array("tipo_ambiente" => "{$this->tipo_ambiente}");
        if (strlen($this->punto_establecimiento) != 3) $error += array("punto_establecimiento" => "{$this->punto_establecimiento}");
        if (strlen($this->punto_emision) != 3) $error += array("punto_emision" => "{$this->punto_emision}");
        if (strlen($this->numero_comprobante) != 9) $error += array("numero_comprobante" => "{$this->numero_comprobante}");
        if (strlen($this->codigo_numerico) != 8) $error += array("codigo_numerico" => "{$this->codigo_numerico}");
        if (strlen($this->tipo_emision) != 1) $error += array("tipo_emision" => "{$this->tipo_emision}");
        if (count($error) > 0) {
            $this->isError = true;
            $this->errores = $error;
        } else {
            $this->isError = false;
        }
    }

    private function ValidarDatosClaveContigencia()
    {
        $error = array();
        if (strlen($this->fecha_emision) != 8) $error += array("fecha_emision" => "{$this->fecha_emision}");
        if (strlen($this->tipo_comprobante) != 2) $error += array("tipo_comprobante" => "{$this->tipo_comprobante}");
        if (strlen($this->clave_contigencia) != 37) $error += array("clave_contigencia" => "{$this->clave_contigencia}");
        if (strlen($this->tipo_emision) != 1) $error += array("tipo_emision" => "{$this->tipo_emision}");

        if (count($error) > 0) {
            $this->isError = true;
            $this->errores = $error;
        }
    }
}
