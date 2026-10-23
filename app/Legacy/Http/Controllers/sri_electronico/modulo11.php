<?php
class modulo{
    function getMod11Dv($num){
        $digits = str_replace(array('.',','), array('',''), strrev($num));
        if(!ctype_digit($digits)){
            return false;
        }

        $sum = 0;
        $factor = 2;
        for ($i=0; $i < strlen($digits); $i++) { 
            $sum += substr($digits,$i,1)*$factor;
            if($factor == 7){
                $factor = 2;
            }else{
                $factor++;
            }
        }

        $dv = 11 - ($sum%11);
        if($dv == 10){
            return 1;
        }

        if($dv == 11){
            return 0;
        }

        return $dv;
    }
}

$dig = new modulo();

$estructura = '280620220109227295870011001001000000002123456781';//4
echo '<br>';
echo $estructura;
echo '<br>';
echo 'el digito verificador es '.$dig->getMod11Dv($estructura);
echo '<br>';
echo 'La clave de acceso es '.$estructura.$dig->getMod11Dv($estructura);