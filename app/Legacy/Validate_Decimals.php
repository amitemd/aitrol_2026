<?php

namespace Sis_medico;


class Validate_Decimals 
{
   public function set_round($paremeters){
      
        return number_format(round($paremeters,2),2,'.','');
   }
   public function set_round_decimals($parameters,$decimals=""){
      if($decimals==null){
         $decimals=2;
      }
      $numbr= number_format(round($parameters,$decimals),$decimals);
      return $numbr;
   }
}
