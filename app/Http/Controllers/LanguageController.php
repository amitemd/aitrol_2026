<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class LanguageController extends Controller
{
    public function __invoke(Request $request, string $lang): RedirectResponse
    {
        abort_unless(
            in_array($lang, config('localization.supported_locales', ['es']), true),
            404,
        );

        $request->session()->put([
            'lang' => $lang,
            'new_system.locale_explicitly_selected' => true,
        ]);

        app()->setLocale($lang);

        return back();
    }
}
