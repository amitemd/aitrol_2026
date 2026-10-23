<?php

namespace Sis_medico\Http\Requests;

use Sis_medico\Http\Requests\Request;

use Redirect;

class UsuarioRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
      * @param  array  $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */

   

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        

        return [
        'nombre1' => 'required|max:60',
        'nombre2' => 'max:60',
        'apellido1' => 'required|max:60',
        'apellido2' => 'required|max:60',
        'id' => 'required|max:10|unique:users',
        'id_pais' => 'required',
        'ciudad' => 'required|max:60',
        'direccion' => 'required|max:255',
        'telefono1' => 'required|numeric|max:9999999999',
        'telefono2' => 'required|numeric|max:9999999999',
        'ocupacion' => 'required|max:60',
        'fecha_nacimiento' => 'required|date',
        'id_tipo_usuario' => 'required',
        'email' => 'required|email|max:191|unique:users',
        'password' => 'required|min:6|confirmed',

        ];
    }


       public function messages()
    {
        return [
            
 
        'nombre1.required' => 'Agrega el primer nombre.',
        'nombre1.max' =>'El primer nombre no puede ser mayor a :max caracteres.',
        'nombre2.max' =>'El segundo nombre no puede ser mayor a :max caracteres.',
        'apellido1.required' => 'Agrega el primer apellido.',
        'apellido1.max' =>'El primer apellido no puede ser mayor a :max caracteres.',
        'apellido2.required' => 'Agrega el segundo apellido.',
        'apellido2.max' =>'El segundo apellido no puede ser mayor a :max caracteres.',
        'id.unique' => 'La cédula ya se encuentra registrada.',
        'id.required' => 'Agrega la cédula.',
        'id.max' =>'La cédula no puede ser mayor a :max caracteres.',
        'id_pais.required' => 'Agrega el país.',
        'ciudad.required' => 'Agrega la ciudad.',
        'ciudad.max' =>'La ciudad no puede ser mayor a :max caracteres.',
        'direccion.required' => 'Agrega la direccion.',
        'direccion.max' =>'La direccion no puede ser mayor a :max caracteres.',
        'telefono1.required' => 'Agrega el teléfono del domicilio',
        'telefono1.max' =>'El teléfono del domicilio no puede ser mayor a 10 caracteres.',
        'telefono1.numeric' =>'El telefono del domicilio debe ser numérico.',
        'telefono2.required' => 'Agrega el teléfono celular.',
        'telefono2.max' =>'El teléfono celular no puede ser mayor a 10 caracteres.',
        'telefono2.numeric' =>'El telefono celular debe ser numérico.',
        'ocupacion.required' => 'Agrega la ocupación.',
        'ocupacion.max' =>'La ocupación del usuario no puede ser mayor a :max caracteres.',
        'fecha_nacimiento.required' =>'Agrega la fecha de nacimiento.',
        'fecha_nacimiento.date' =>'La fecha de nacimiento tiene formato incorrecto.',
        'id_tipo_usuario.required' => 'Agrega el tipo del usuario.',
        'email.unique' => 'El Email ya se encuentra registrado.',
        'email.required' => 'Agrega el Email del usuario.',
        'email.max' =>'El Email no puede ser mayor a :max caracteres.',
        'email.email' =>'El Email tiene error en el formato.',
        'password.required' => 'Agrega el password.',
        'password.min' =>'El Password debe ser mayor a :min caracteres.',
        'password.confirmed' =>'El Password y su confirmación no coinciden.',


                        
          
        ];
    }



     /**
     * Get the proper failed validation response for the request.
     *
     * @param  array  $errors
     * @return \Symfony\Component\HttpFoundation\Response
     
    public function response(array $errors)
    {
        
        return Redirect::to("/mostrar_errores")
                                        ->withInput($this->except($this->dontFlash))
                                        ->withErrors($errors, $this->errorBag);
    }
    */







}
