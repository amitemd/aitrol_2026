<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;

class RouteCompatibilityController extends Controller
{
    public function currentUser(Request $request)
    {
        return $request->user();
    }

    public function changeLanguage(string $lang)
    {
        if (! in_array($lang, ['es', 'en', 'pt'], true)) {
            abort(404);
        }

        session()->put('lang', $lang);
        session()->put('locale', $lang);

        return redirect()->back();
    }

    public function unavailable()
    {
        abort(404);
    }

    public function loginRedirect()
    {
        return redirect('/');
    }

    public function passwordRecoveryRedirect()
    {
        return redirect()->route('password.request');
    }

    public function pdfBrandingRedirect()
    {
        return redirect()->route('v3.pdf-branding.index');
    }

    public function taxDeclarationsRedirect()
    {
        return redirect()->to(url('v3/tributario/declaraciones'));
    }

    public function taxForm107Redirect()
    {
        return redirect()->route('formulario_107');
    }
}
