<?php

//$conectar= new mysqli("localhost","root","","sis_medico_prb");

use Illuminate\Database\Eloquent\Model;  
use Sis_medico\Batch;
	


for($i=0;$i<10;$i++)
{
	
	//$conectar->query("INSERT INTO batch (descripcion) VALUES ('prueba $i')");
	$batch=Sis_medico\Batch::create(['pruebaxxx']);
}

        

        
?>   