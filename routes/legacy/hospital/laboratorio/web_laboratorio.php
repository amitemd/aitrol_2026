<?php

Route::match(['get', 'post'], 'hospital/laboratorio/search', 'hospital\LaboratorioController@index')->name('hospital_laboratorio.index');
