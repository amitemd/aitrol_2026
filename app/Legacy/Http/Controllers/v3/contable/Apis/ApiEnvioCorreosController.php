<?php

namespace Sis_medico\Http\Controllers\v3\contable\Apis;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;

class ApiEnvioCorreosController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'status' => 'disabled',
            'code' => 410,
            'message' => 'El prototipo de envio de correos v3 no esta habilitado.',
        ], 410);
    }
}
