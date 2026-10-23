<?php

namespace Sis_medico\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Sis_medico\User;
use Sis_medico\Ct_Acreedores;
use Sis_medico\Observers\Ct_AcreedoresObserver;
use Sis_medico\Observers\UserObserver;

use Sis_medico\AfDepreciacionCabecera;
use Sis_medico\AfFacturaActivoCabecera;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_Comprobante_Egreso_Masivo;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Sis_medico\AfActivo;
use Sis_medico\Ct_Cruce_Valores;
use Sis_medico\Nota_Debito;
use Sis_medico\Ct_Cruce_Cuentas_Clientes;

use Sis_medico\Observers\AfDepreciacionCabeceraObserver;
use Sis_medico\Observers\AfFacturaActivoCabeceraObserver;
use Sis_medico\Observers\ComprobanteEgresoCabeceraObserver;
use Sis_medico\Observers\ComprobanteEgresoMasivoCabeceraObserver;
use Sis_medico\Observers\ComprobanteEgresoVariosCabeceraObserver;
use Sis_medico\Observers\CruceValoresProveedoresCabeceraObserver;
use Sis_medico\Observers\AfActivoObserver;
use Sis_medico\Observers\NotaDebitoObserver;
use Sis_medico\Observers\CtCruceCuentasClientesObserver;
use Sis_medico\Ct_Nota_Credito;
use Sis_medico\Observers\NotaCreditoObserver;
use Sis_medico\Observers\DepositoBancarioObserver;
use Sis_medico\Ct_Deposito_Bancario;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        $this->app->validator->extendImplicit('max_consulta', function ($attribute, $value, $parameters) {
            

            return $parameters[0]<$parameters[1];
        }, 'Doctor ha superado el máximo de consultas por día!');
        $this->app->validator->extendImplicit('comparamayor', function ($attribute, $value, $parameters) {
            
            
            return $parameters[0]<$parameters[1];
        }, 'comparamayor');

        $this->app->validator->extendImplicit('max_procedimiento', function ($attribute, $value, $parameters) {
            

            return $parameters[0]<$parameters[1];
        }, 'Doctor ha superado el máximo de procedimientos por día!');

        $this->app->validator->extendImplicit('unique_doctor', function ($attribute, $value, $parameters) {
            
            return $parameters[0]< 1;
        }, 'Doctor ya se encuentra con paciente asignado');

        $this->app->validator->extendImplicit('edad_fecha', function ($attribute, $value, $parameters) {
            
            $date2 = date('Y-m-d');//
            $diff = abs(strtotime($date2) - strtotime($value));
            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
            
            return $years > 17;
        }, 'Es menor de Edad');

        $this->app->validator->extendImplicit('caducidad_codigo', function ($attribute, $value, $parameters) {
            
            $flag = true;
            $date2 = date('Y-m-d');//

            if($parameters[2]<$date2){
                if($parameters[1]!=$parameters[2]){
                    if($value==$parameters[0]){
                        $flag=false;
                    }
                }
            }

            return $flag;
        }, 'Debe cambiar el código de validación');

        $this->app->validator->extendImplicit('comparahoras', function ($attribute, $value, $parameters) {
           
            $hora=substr($attribute, 0, 8);
            
             
           if($hora=='hora_ini'){
                return $value<$parameters[0]; 
           }else{
                return $parameters[0]<$value;
           }

            
        }, 'hora de inicio debe ser menor a hora fin ');

         $this->app->validator->extendImplicit('son_iguales', function ($attribute, $value, $parameters) {
            
            return $parameters[0]===$parameters[1];
        }, 'son_iguales');

          $this->app->validator->extendImplicit('aux', function ($attribute, $value, $parameters) {
            
            $aux = substr($value,0,3);
            return $aux!='AUX';
        }, 'aux');

        User::observe(UserObserver::class);
        AfDepreciacionCabecera::observe(AfDepreciacionCabeceraObserver::class);
        AfFacturaActivoCabecera::observe(AfFacturaActivoCabeceraObserver::class);
        Ct_Comprobante_Egreso::observe(ComprobanteEgresoCabeceraObserver::class);
        Ct_Comprobante_Egreso_Masivo::observe(ComprobanteEgresoMasivoCabeceraObserver::class);
        Ct_Comprobante_Egreso_Varios::observe(ComprobanteEgresoVariosCabeceraObserver::class);
        Ct_Cruce_Valores::observe(CruceValoresProveedoresCabeceraObserver::class);
        AfActivo::observe(AfActivoObserver::class);
        Nota_Debito::observe(NotaDebitoObserver::class);
        Ct_Cruce_Cuentas_Clientes::observe(CtCruceCuentasClientesObserver::class);
        Ct_Nota_Credito::observe(NotaCreditoObserver::class);
        Ct_Deposito_Bancario::observe(DepositoBancarioObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
