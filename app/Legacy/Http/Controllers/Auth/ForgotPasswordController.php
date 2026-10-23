<?php

namespace Sis_medico\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
     */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    //Shows form to request password reset
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function user()
    {
        return view('auth.passwords.user');
    }

    public function recover(Request $request)
    {
        $usuario = User::where('id', $request['cedula'])->where('fecha_nacimiento', $request['fecha'])->first();
        return view('auth.passwords.response', ['usuario' => $usuario]);
    }
}
