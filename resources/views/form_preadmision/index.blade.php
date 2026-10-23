<!DOCTYPE html>
<html>

<head>
    <title>Formulario Pre Admisión</title>
</head>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.0/flowbite.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{ asset ('/hc4/js/jquery.js') }}" type="text/javascript"></script>
<script src="https://cdn.tailwindcss.com"></script>
<style>
    * {
        font-family: 'Poppins', sans-serif;
    }
</style>

<body class="bg-white my-0 mx-auto w-[95%] p-5">
    <div class="border border-gray-500 p-3 rounded-2xl">

        <div class="w-full mb-4">
            <div class="grid grid-cols-3 gap-4 place-items-center">
                <div>
                    @if(!is_null($empresa->logo))
                    <!-- <img src="{{asset('storage/app/logo/'.$empresa->logo)}}" style="width: 100%;"> -->
                    <img style="width: 100%;" src="data:image/png;base64,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" alt="">
                    @endif
                </div>
                <div class="text-center">
                    <h3 class="font-semibold">FORMULARIO DE PRE-ADMISIÓN</h3>
                </div>
                <div class="text-center">
                    <h4 class="font-semibold">Vers. 0.1</h4>
                </div>
            </div>
        </div>

        <div>
            <form class="form-vertical" id="form_guardar">
                {{ csrf_field() }}
                <div class="mb-3 border-b-2 border-gray-300">
                    <b>DATOS DEL PACIENTE</b>
                </div>
                <div class="w-full md:w-[50%] flex gap-5 place-items-center">
                    <p class="whitespace-nowrap font-bold">1. CÉDULA*:</p>
                    <div class="relative z-0 w-full mb-6 group">
                        <input autocomplete="off" onchange="buscar_paciente()" type="text" name="cedula" id="cedula" class="validar block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none  dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required="">
                        <label for="cedula" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Cedula</label>
                    </div>
                </div>


                <div class="mb-4 border-b-2 border-gray-300">
                    <b>2. NOMBRES COMPLETOS*:</b>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="relative z-0 w-full mb-6 group">
                        <input type="text" autocomplete="off" name="nombre1" id="nombre1" class="validar block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required="">
                        <label for="nombre1" class="peer-focus:font-medium absolute text-sm text-gray-600 font-semibold duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600  peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Nombre 1*</label>
                    </div>
                    <div class="relative z-0 w-full mb-6 group">
                        <input type="text" autocomplete="off" name="nombre2" id="nombre2" class="validar block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required="">
                        <label for="nombre2" class="peer-focus:font-medium absolute text-sm text-gray-600 font-semibold duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600  peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Nombre 2*</label>
                    </div>
                    <div class="relative z-0 w-full mb-6 group">
                        <input type="text" autocomplete="off" name="apellido1" id="apellido1" class="validar block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required="">
                        <label for="apellido1" class="peer-focus:font-medium absolute text-sm text-gray-600 font-semibold duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600  peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Apellido 1*</label>
                    </div>
                    <div class="relative z-0 w-full mb-6 group">
                        <input type="text" autocomplete="off" name="apellido2" id="apellido2" class="validar block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required="">
                        <label for="apellido2" class="peer-focus:font-medium absolute text-sm text-gray-600 font-semibold duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-blue-600  peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Apellido 2*</label>
                    </div>
                </div>


                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                    <div class="">
                        <label for="sexo" class="block mb-2 text-sm font-bold text-gray-900 whitespace-nowrap">3. SEXO*:</label>
                        <select id="sexo" name="sexo" class="validar bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                            <option value="">Seleccione...</option>
                            <option value="1">Masculino</option>
                            <option value="2">Femenino</option>
                        </select>
                    </div>
                    <div class="">
                        <label for="fecha_nacimiento" class="block mb-2 text-sm font-bold text-gray-900">4. FECHA DE NACIMIENTO*:</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="validar block w-full p-2 text-gray-900 border border-gray-400 rounded-lg bg-gray-50 sm:text-xs focus:ring-blue-500 focus:border-blue-500 ">
                    </div>
                    <div class="">
                        <label for="edad" class="block mb-2 text-sm font-bold text-gray-900">Edad</label>
                        <input readonly type="text" id="edad" class="block w-full p-2 text-gray-900 border border-gray-400 rounded-lg bg-gray-50 sm:text-xs focus:ring-blue-500 focus:border-blue-500 ">
                    </div>
                </div>


                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="ciudad_nacimiento" class="block mb-2 text-sm font-bold text-gray-900">5. CIUDAD DE NACIMIENTO*:</label>
                        <input type="text" id="ciudad_nacimiento" name="ciudad_nacimiento" placeholder="Ciudad" class="validar block w-full p-2 text-gray-900 border border-gray-400 rounded-lg bg-gray-50 sm:text-xs focus:ring-blue-500 focus:border-blue-500 ">
                    </div>

                    <div>
                        <label for="direccion" class="block mb-2 text-sm font-bold text-gray-900">6. DIRECCIÓN DE DOMICILIO*:</label>
                        <input type="text" autocomplete="off" id="direccion" name="direccion" placeholder="Ciudad" class="validar block w-full p-2 text-gray-900 border border-gray-400 rounded-lg bg-gray-50 sm:text-xs focus:ring-blue-500 focus:border-blue-500 ">
                    </div>

                    <div class="">
                        <label for="estado_civil" class="block mb-2 text-sm font-bold text-gray-900 whitespace-nowrap">7. ESTADO CIVIL*:</label>
                        <select id="estado_civil" name="estado_civil" class="validar bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                            <option value="">Seleccione...</option>
                            <option value="1">Soltero</option>
                            <option value="2">Casado</option>
                            <option value="3">Viudo</option>
                            <option value="4">Divorciado</option>
                            <option value="5">Union Libre</option>
                            <option value="6">Union de Hecho</option>

                        </select>
                    </div>
                </div>


                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="ocupacion" class="block mb-2 text-sm font-bold text-gray-900">8. OCUPACIÓN*:</label>
                        <input type="text" autocomplete="off" id="ocupacion" name="ocupacion" placeholder="Ciudad" class="validar block w-full p-2 text-gray-900 border border-gray-400 rounded-lg bg-gray-50 sm:text-xs focus:ring-blue-500 focus:border-blue-500 ">
                    </div>

                    <div>
                        <label for="lugar_trabajo" class="block mb-2 text-sm font-bold text-gray-900">9. LUGAR DE TRABAJO:</label>
                        <input type="text" autocomplete="off" id="lugar_trabajo" name="lugar_trabajo" placeholder="Ciudad" class="block w-full p-2 text-gray-900 border border-gray-400 rounded-lg bg-gray-50 sm:text-xs focus:ring-blue-500 focus:border-blue-500 ">
                    </div>

                    <div>
                        <label for="telefono1" class="block mb-2 text-sm font-bold text-gray-900">10. TELÉFONO*:</label>
                        <input type="text" autocomplete="off" id="telefono1" name="telefono1" placeholder="Telefono" class="validar block w-full p-2 text-gray-900 border border-gray-400 rounded-lg bg-gray-50 sm:text-xs focus:ring-blue-500 focus:border-blue-500 ">
                    </div>
                </div>



                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="telefono2" class="block mb-2 text-sm font-bold text-gray-900">11. CELULAR*:</label>
                        <input type="text" autocomplete="off" id="telefono2" name="telefono2" placeholder="Celular" class="validar block w-full p-2 text-gray-900 border border-gray-400 rounded-lg bg-gray-50 sm:text-xs focus:ring-blue-500 focus:border-blue-500 ">
                    </div>

                    <div>
                        <label for="correo" class="block mb-2 text-sm font-bold text-gray-900">12. CORREO ELECTRÓNICO*:</label>
                        <input type="text" autocomplete="off" id="correo" name="correo" placeholder="Ciudad" class="validar block w-full p-2 text-gray-900 border border-gray-400 rounded-lg bg-gray-50 sm:text-xs focus:ring-blue-500 focus:border-blue-500 ">
                    </div>

                    <div class="">
                        <label for="grupo_sanguineo" class="block mb-2 text-sm font-bold text-gray-900 whitespace-nowrap">13. GRUPO SANGUINEO*:</label>
                        <select id="grupo_sanguineo" name="grupo_sanguineo" class="validar bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                            <option value="">Seleccione...</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>


                </div>

                <div class="grid grid-cols-5 gap-4 mb-4">
                    <div class="col-span-3">
                        <label for="transfusiones" class="block mb-2 text-sm font-bold text-gray-900">14. HA RECIBIDO TRANSFUSIONES SANGUÍNEAS ANTERIORMENTE:</label>
                        <select id="transfusiones" name="transfusiones" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                            <option value="">Seleccione...</option>
                            <option value="SI">SI</option>
                            <option value="NO">NO</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <!-- <form id="frm"> -->
                        <input type="hidden" name="id_paciente" id="id_paciente" value="">

                        <label for="ale_list" class="block mb-2 text-sm font-bold text-gray-900">15. ALERGIA A MEDICAMENTOS:</label>

                        <select id="ale_list" name="ale_list[]" multiple class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                        </select>
                        <!-- </form> -->
                    </div>
                </div>


                <div class="w-full">
                    <p><b>16. HÁBITOS:</b></p>
                    <div class="flex justify-center">
                        <div class="w-full md:w-[75%] lg:w-[50%]">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="">
                                    <label for="fuma" class="block mb-2 text-sm font-bold text-gray-900">Fuma:</label>
                                    <select id="fuma" name="fuma" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                        <option value="">Seleccione...</option>
                                        @foreach($lrespuesta as $resp)
                                        <option value="{{$resp->id}}">{{$resp->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="">
                                    <label for="frec_fuma" class="block mb-2 text-sm font-bold text-gray-900">Frecuencia:</label>
                                    <select id="frec_fuma" name="frec_fuma" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                        <option value="">Seleccione...</option>
                                        @foreach($lfrecuencia2 as $f2)
                                        <option value="{{$f2->id}}">{{$f2->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <div class="w-full md:w-[75%] lg:w-[50%]">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="">
                                    <label for="alcohol" class="block mb-2 text-sm font-bold text-gray-900">CONSUME ALCOHOL:</label>
                                    <select id="alcohol" name="alcohol" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                        <option value="">Seleccione...</option>
                                        @foreach($lrespuesta as $resp)
                                        <option value="{{$resp->id}}">{{$resp->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="">
                                    <label for="frec_alcohol" class="block mb-2 text-sm font-bold text-gray-900">Frecuencia:</label>
                                    <select id="frec_alcohol" name="frec_alcohol" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                        <option value="">Seleccione...</option>
                                        @foreach($lfrecuencia2 as $f2)
                                        <option value="{{$f2->id}}">{{$f2->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <div class="w-full md:w-[75%] lg:w-[50%]">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="">
                                    <label for="drogas" class="block mb-2 text-sm font-bold text-gray-900">CONSUME DROGAS:</label>
                                    <select id="drogas" name="drogas" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                        <option value="">Seleccione...</option>
                                        @foreach($lrespuesta as $resp)
                                        <option value="{{$resp->id}}">{{$resp->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="">
                                    <label for="frec_drogas" class="block mb-2 text-sm font-bold text-gray-900">Frecuencia:</label>
                                    <select id="frec_drogas" name="frec_drogas" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                        <option value="">Seleccione...</option>
                                        @foreach($lfrecuencia2 as $f2)
                                        <option value="{{$f2->id}}">{{$f2->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <div class="w-full md:w-[75%] lg:w-[50%]">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="">
                                    <label for="actividad_fisica" class="block mb-2 text-sm font-bold text-gray-900">ACTIVIDAD FÍSICA:</label>
                                    <select id="actividad_fisica" name="actividad_fisica" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                        <option value="">Seleccione...</option>
                                        @foreach($lrespuesta as $resp)
                                        <option value="{{$resp->id}}">{{$resp->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="">
                                    <label for="frec_actividad_fisica" class="block mb-2 text-sm font-bold text-gray-900">Frecuencia:</label>
                                    <select id="frec_actividad_fisica" name="frec_actividad_fisica" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                        <option value="">Seleccione...</option>
                                        @foreach($lfrecuencia2 as $f2)
                                        <option value="{{$f2->id}}">{{$f2->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="flex justify-center">
                        <div class="w-full md:w-[75%] lg:w-[50%]">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="">
                                    <label for="alimentacion" class="block mb-2 text-sm font-bold text-gray-900">ALIMENTACIÓN:</label>
                                    <select id="alimentacion" name="alimentacion" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                        <option value="">Seleccione...</option>
                                        @foreach($lrespuesta as $resp)
                                        <option value="{{$resp->id}}">{{$resp->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="">
                                    <label for="frec_alimentacion" class="block mb-2 text-sm font-bold text-gray-900">Frecuencia:</label>
                                    <select id="frec_alimentacion" name="frec_alimentacion" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                        <option value="">Seleccione...</option>
                                        @foreach($lfrecuencia2 as $f2)
                                        <option value="{{$f2->id}}">{{$f2->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <div class="w-full md:w-[75%] lg:w-[50%]">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="">
                                    <label for="habito_defecatorio" class="block mb-2 text-sm font-bold text-gray-900">HÁBITO DEFECATORIO:</label>
                                    <select id="habito_defecatorio" name="habito_defecatorio" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                        <option value="">Seleccione...</option>
                                        @foreach($lrespuesta as $resp)
                                        <option value="{{$resp->id}}">{{$resp->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="">
                                    <label for="frec_habito_defecatorio" class="block mb-2 text-sm font-bold text-gray-900">Frecuencia:</label>
                                    <select id="frec_habito_defecatorio" name="frec_habito_defecatorio" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                        <option value="">Seleccione...</option>
                                        @foreach($lfrecuencia2 as $f2)
                                        <option value="{{$f2->id}}">{{$f2->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>




                <div class="w-full">
                    <div class="flex gap-[25px] mb-4 justify-between">
                        <p><b>17. ANTECEDENTES PATOLÓGICOS:</b></p>
                        <button class="bg-green-500 hover:bg-green-600 text-white font-semibold py-1 px-4 rounded transation-all duration-500" onclick="crear_fila_patologicos(0, {})" type="button">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            Agregar
                        </button>
                        <input name='contador_antecedentes_patologicos' id='contador_antecedentes_patologicos' type='hidden' value="1">

                    </div>

                    <div class="grid place-items-center">
                        <div class="w-full sm:w-[60%] md:w-[40%]">
                            <div id="agregar_antecedentes_patologicos">

                            </div>
                        </div>
                    </div>
                </div>


                <div class="w-full">
                    <div class="flex gap-[25px] mb-4 justify-between">
                        <p><b>18. ANTECEDENTES FAMILIARES:</b></p>
                        <button class="bg-green-500 hover:bg-green-600 text-white font-semibold py-1 px-4 rounded transation-all duration-500" onclick="crear_fila_familiares(0, {})" type="button">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            Agregar
                        </button>
                        <input name='contador_antecedentes_familiares' id='contador_antecedentes_familiares' type='hidden' value="1">
                    </div>

                    <div class="flex justify-center">
                        <div class="w-full md:w-[75%] lg:w-[50%]">
                            <div class="grid grid-cols-2 gap-4 mb-4" id="agregar_antecedentes_familiares">

                            </div>
                        </div>
                    </div>
                </div>


                <div class="w-full">
                    <div class="flex gap-[25px] mb-4 justify-between">
                        <p><b>19. ANTECEDENTES QUIRURGICOS (operaciones previas):</b></p>
                        <button class="bg-green-500 hover:bg-green-600 text-white font-semibold py-1 px-4 rounded transation-all duration-500" onclick="crear_fila_quirurgicos(0, {})" type="button">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            Agregar
                        </button>
                        <input name='contador_antecedentes_quirurgicos' id='contador_antecedentes_quirurgicos' type='hidden' value="1">
                    </div>
                    <div id="agregar_antecedentes_quirurgicos" class="grid col-span-2 grid-cols-2 gap-4 mb-4">
                    </div>
                </div>

                <div class="w-full">
                    <div class="flex gap-[25px] mb-4 justify-between">
                        <p><b>20. MEDICAMENTOS HABITUALES:</b></p>
                        <button class="bg-green-500 hover:bg-green-600 text-white font-semibold py-1 px-4 rounded transation-all duration-500" onclick="crear_fila_medicamento()" type="button">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            Agregar
                        </button>
                        <input name='contador_medicamentos' id='contador_medicamentos' type='hidden' value="1">
                    </div>
                    <!-- <div id="agregar_medicamento"> -->
                    <div class="flex justify-center">
                        <div class="w-full md:w-[75%] lg:w-[50%]" id="agregar_medicamento">

                        </div>
                    </div>
                    <!-- </div> -->
                </div>

                <div class="w-full">
                    <div class="flex gap-[25px] mb-4 justify-between">
                        <p><b>21. SÍNTOMAS ACTUALES:</b></p>
                        <button class="bg-green-500 hover:bg-green-600 text-white font-semibold py-1 px-4 rounded transation-all duration-500" onclick="crear_fila_sintoma()" type="button">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            Agregar
                        </button>
                        <input name='contador_sintomas' id='contador_sintomas' type='hidden' value="1">
                    </div>
                    <div class="flex justify-center">
                        <div class="w-full md:w-[75%] lg:w-[50%]">
                            <div class="grid grid-cols-2 gap-4 mb-4" id="agregar_sintoma">

                            </div>
                        </div>
                    </div>

                </div>


                <div class="w-full">
                    <p><b>22. ALIMENTOS QUE CAUSAN SÍNTOMAS</b></p>
                    <div class="mt-2 mb-4">
                        <textarea id="alimentos_sintomas" name="alimentos_sintomas" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-400 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <p class="text-gray-400 font-semibold">* Los campos son obligatorios</p>
                </div>

                <br>

                <br>

            </form>
            <div class="mb-4 flex justify-center">
                <button class="bg-black text-white py-2 px-8 font-semibold rounded" type="button" id="boton_enviar" onclick="guardar(event)">{{trans('newEncuestas.guardar')}}</button>
            </div>
        </div>
    </div>
</body>

</html>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.0/flowbite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>


<script>
    $(document).ready(function() {
        crear_fila_sintoma();
        crear_fila_medicamento();
        crear_fila_familiares(0, {});
        crear_fila_patologicos(0, {});
        crear_fila_quirurgicos(0, {});
    });

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    function validar_campos() {
        let campo = document.querySelectorAll(".validar")
        let validar = false;

        for (let i = 0; i < campo.length; i++) {

            if (campo[i].value.trim() <= 0) {
                campo[i].style.border = '2px solid #CD6155';
                campo[i].style.borderRadius = '4px';
                validar = true;
            } else {
                campo[i].style.border = '1px solid #d2d6de';
                campo[i].style.borderRadius = '0px';
            }
        }
        return validar;
    }



    function buscar_paciente() {
        var id_paciente = document.getElementById('cedula').value;
        $.ajax({
            type: 'get',
            url: "{{ url('formato_preadmision/buscar_paciente')}}/" + id_paciente,
            success: function(data) {
                console.log(data);
                $('#nombre1').val(data.nombre1);
                $('#nombre2').val(data.nombre2);
                $('#apellido1').val(data.apellido1);
                $('#apellido2').val(data.apellido2);
                $('#fecha_nacimiento').val(data.fecha_nacimiento);
                $('#ciudad_nacimiento').val(data.ciudad);
                $('#telefono1').val(data.telefono1);
                $('#telefono2').val(data.telefono2);
                $('#direccion').val(data.direccion);
                $('#ocupacion').val(data.ocupacion);
                $('#correo').val(data.email);
                $('#id_paciente').val(data.id);
                $('#lugar_trabajo').val(data.trabajo);
                $(`#sexo option[value="${data.sexo}"]`).attr("selected", true);
                $(`#estado_civil option[value="${data.estado_civil}"]`).attr("selected", true);
                $(`#grupo_sanguineo option[value="${data.grupo_sanguineo}"]`).attr("selected", true);
                $(`#transfusiones option[value="${data.transfusion}"]`).attr("selected", true);
                var edad = calcularEdad(data.fecha_nacimiento);
                $('#edad').val(edad);
                $("#ale_list").empty();
                $.each(data.alergias, function(key, registro) {
                    $("#ale_list").append('<option selected value=' + registro.id_principio_activo + '>' +
                        registro.nombre + '</option>');

                });

                $(`#fuma option[value="${data.fuma}"]`).attr("selected", true);
                $(`#frec_fuma option[value="${data.frecuencia_fuma}"]`).attr("selected", true);
                $(`#alcohol option[value="${data.alcohol}"]`).attr("selected", true);
                $(`#frec_alcohol option[value="${data.frecuencia_alcohol}"]`).attr("selected", true);
                $(`#drogas option[value="${data.drogas}"]`).attr("selected", true);
                $(`#frec_drogas option[value="${data.frecuencia_drogas}"]`).attr("selected", true);
                $(`#actividad_fisica option[value="${data.actividad_fisica}"]`).attr("selected", true);
                $(`#frec_actividad_fisica option[value="${data.frecuencia_actividad}"]`).attr("selected", true);
                $(`#alimentacion option[value="${data.alimentacion}"]`).attr("selected", true);
                $(`#frec_alimentacion option[value="${data.frecuencia_alimentacion}"]`).attr("selected", true);
                $(`#habito_defecatorio option[value="${data.habito_defecatorio}"]`).attr("selected", true);
                $(`#frec_habito_defecatorio option[value="${data.frecuencia_defecatorio}"]`).attr("selected", true);

                if (data.familiares.length > 0) {
                    $('#agregar_antecedentes_familiares').empty();

                    const familiares = data.familiares.map(detalle => {
                        let id_fam = crear_fila_familiares(1, detalle);
                        $(`#enfermedad_antcfam${id_fam} option[value="${detalle.antecedente}"]`).attr("selected", true);
                        $(`#parentesco_antcfam${id_fam} option[value="${detalle.parentesco}"]`).attr("selected", true);
                    });
                }

                if (data.patologicos.length > 0) {
                    $('#agregar_antecedentes_patologicos').empty();
                    const patologicos = data.patologicos.map(detallep => {
                        let id_pat = crear_fila_patologicos(1, detallep);
                        $(`#antecedentes_patologicos${id_pat} option[value="${detallep.antecedente}"]`).attr("selected", true);
                    });
                }

                if (data.quirurgicos.length > 0) {
                    $('#agregar_antecedentes_quirurgicos').empty();
                    const quirurgicos = data.quirurgicos.map(detalleq => {
                        let id_qui = crear_fila_quirurgicos(1, detalleq);
                        $(`#antecedentes_quirurgicos${id_qui}`).html(detalleq.antecedente);
                    });
                }

                if (data.medicamentos.length > 0) {
                    $('#agregar_medicamento').empty();
                    const medicamentos = data.medicamentos.map(detallem => {
                        let id_med = crear_fila_medicamento(1, detallem);
                        $(`#medicamento${id_med}`).html(detallem.medicamento);
                        $(`#detalle_medicamento${id_med}`).html(detallem.detalle);
                    });
                }

            }
        });
    }

    function calcularEdad(fecha) {
        var hoy = new Date();
        var cumpleanos = new Date(fecha);
        var edad = hoy.getFullYear() - cumpleanos.getFullYear();
        var m = hoy.getMonth() - cumpleanos.getMonth();

        if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
            edad--;
        }

        return edad;
    }

    const crear_fila_sintoma = () => {
        var id = document.getElementById('contador_sintomas').value;
        let fila = `
            <div class="">
                <label for="sintomas${id}" class="block mb-2 text-sm font-bold text-gray-900">SÍNTOMA:</label>
                <select id="sintomas${id} validar" name="sintomas[]" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" onchange="mostrar_otros_sintomas(${id})">
                    <option value="">Seleccione.. </option>
                    @foreach($lsintomas as $sintoma)
                    <option value="{{$sintoma->id}}">{{$sintoma->nombre}}</option>
                    @endforeach
                </select>
            </div>

            <div class="" id="otros${id}" style="display: none">
                <label for="especifique${id}" class="block mb-2 text-sm font-bold text-gray-900">ESPECIFIQUE:</label>
                <input type="text" id="otros_sintomas${id}" name="otros_sintomas[]" class="block p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-400">
            </div>

            <div class="">
                <label for="tiempo_sintomas${id}" class="block mb-2 text-sm font-bold text-gray-900">TIEMPO DE SÍNTOMA:</label>
                <select id="tiempo_sintomas${id} validar" name="tiempo_sintomas[]" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                    <option value="">Seleccione.. </option>
                    @foreach($ltiempo_sintoma as $tiempo)
                    <option value="{{$tiempo->id}}">{{$tiempo->nombre}}</option>
                    @endforeach
                </select>
            </div>`

        $('#agregar_sintoma').append(fila);
        id++;
        document.getElementById('contador_sintomas').value = id;
    }

    function mostrar_otros_sintomas(id) {
        var id_tipo = $(`#sintomas${id}`).val();
        if (id_tipo == 87) {
            document.getElementById(`otros${id}`).style.display = 'block';
        }
    }

    const crear_fila_medicamento = () => {
        var id = document.getElementById('contador_medicamentos').value;

        let fila = `
            <div class="grid grid-cols-2 gap-4 mb-2">
                <div class="">
                    <label for="medicamento${id}" class="block mb-2 text-sm font-bold text-gray-900">Medicamento:</label>
                    <textarea id="medicamento${id}" name="medicamento[]" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-400 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div class="">
                    <label for="detalle_medicamento${id}" class="block mb-2 text-sm font-bold text-gray-900">Detalle:</label>
                    <textarea id="detalle_medicamento${id}" name="detalle_medicamento[]" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-400 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

            </div>`
        $('#agregar_medicamento').append(fila);
        let aux = id;
        id++;
        document.getElementById('contador_medicamentos').value = id;
        return aux;
    }

    const crear_fila_familiares = (req = 0, data = {}) => {
        var id = document.getElementById('contador_antecedentes_familiares').value;

        let fila = `
            <div class="">
                <label for="enfermedad_antcfam${id}" class="block mb-2 text-sm font-bold text-gray-900">ENFERMEDAD:</label>
                <input type="hidden" name="tipof" id="tipof" value="1">
                <select id="enfermedad_antcfam${id}" validar" name="enfermedad_antcfam[]" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                    <option value="">Seleccione...</option>
                    @foreach($lantecedentes as $antecedentes)
                    <option value="{{$antecedentes->nombre}}">{{$antecedentes->nombre}}</option>
                    @endforeach
                </select>
            </div>

            <div class="">
                <label for="parentesco_antcfam${id}" class="block mb-2 text-sm font-bold text-gray-900">PARENTESCO:</label>
                <select id="parentesco_antcfam${id}" validar" name="parentesco_antcfam[]" class="bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                    <option value="">Seleccione...</option>
                    @foreach($lparentesco as $parentesco)
                    <option value="{{$parentesco->id}}">{{$parentesco->nombre}}</option>
                    @endforeach
                </select>
            </div>`

        $('#agregar_antecedentes_familiares').append(fila);
        let aux = id;
        id++;
        document.getElementById('contador_antecedentes_familiares').value = id;
        return aux;
    }

    const crear_fila_patologicos = (req = 0, data = {}) => {
        var id = document.getElementById('contador_antecedentes_patologicos').value;

        let fila2 = `<select id="antecedentes_patologicos${id}" name="antecedentes_patologicos[]" class="mb-4 bg-gray-50 border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                        <option value="">Seleccione...</option>
                        @foreach($lantecedentes as $antecedentes)
                        <option value="{{$antecedentes->nombre}}">{{$antecedentes->nombre}}</option>
                        @endforeach
                     </select>
                <input type="hidden" name="tipop" id="tipop" value="2">`
        $('#agregar_antecedentes_patologicos').append(fila2);
        let aux = id;
        id++;
        document.getElementById('contador_antecedentes_patologicos').value = id;
        return aux;
    }

    const crear_fila_quirurgicos = (req = 0, data = {}) => {
        var id = document.getElementById('contador_antecedentes_quirurgicos').value;
        let fila = `<textarea autocomplete="off" id="antecedentes_quirurgicos${id}" name="antecedentes_quirurgicos[]" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-400 focus:ring-blue-500 focus:border-blue-500"></textarea>
        <input type="hidden" name="tipoq" id="tipoq" value="3">`
        $('#agregar_antecedentes_quirurgicos').append(fila);
        let aux = id;
        id++;
        document.getElementById('contador_antecedentes_quirurgicos').value = id;
        return aux;

    }

    $('body').on('click', '.delete', function() {


        $(this).parent().parent().remove();
        //totales(0);
    });

    $('#ale_list').select2({
        placeholder: "Seleccione Medicamento...",
        minimumInputLength: 2,
        ajax: {
            url: `{{route('sinlogin.find_medicamento_alergia')}}`,
            dataType: 'json',
            data: function(params) {
                //console.log(params);
                return {
                    q: $.trim(params.term)
                };
            },
            processResults: function(data) {
                //console.log(data);
                return {
                    results: data
                };
            },
            cache: true
        },
    });

    $('#ale_list').on('change', function(e) {
        guardar_alergia();
    });

    function guardar_alergia() {
        $.ajax({
            type: 'post',
            url: "{{route('sinlogin.guardar_alergia')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#form_guardar").serialize(),
            success: function(data) {
                console.log(data);

            },
            error: function(data) {

                console.log(data.responseJSON);

            }
        });
    }

    function guardar(e) {
        e.preventDefault();
        if (!validar_campos()) {
            $.ajax({
                type: 'post',
                url: "{{ route('sinlogin.form_admision_guardar')}}",
                datatype: 'json',
                data: $("#form_guardar").serialize(),
                success: function(data) {
                    alertas(data.status, data.status, data.msj);
                    if (data.status == 'success') {

                    }
                },
                error: function(data) {

                }
            })
        } else {
            alertas('error', 'ERROR', `{{trans('proforma.camposvacios')}}`)
        }

    }
</script>