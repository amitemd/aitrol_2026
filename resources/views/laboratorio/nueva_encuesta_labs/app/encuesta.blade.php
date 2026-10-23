<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta Labs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
</head>

@php
$size = 50;
$colors = [
[ "class" => "super-happy", "bg" => "#d4f5ff","color"=> "#40d5ff" ],
[ "class" => "happy","bg" => "#d7ffe6","color"=> "#33f57e" ],
[ "class" => "neutral","bg" => "#f9ffc1","color"=> "#a47f04" ],
[ "class" => "neutral-2","bg" => "#ffe1e1","color"=> "#ff6a6a" ],
[ "class" => "super-sad","bg" => "#ffe0dd","color"=> "#ff6857" ]
];

$className= "rounded-[10px] w-auto mt-4 bg-gray-100/50 ";
@endphp

<body id="body" class="">
   

    <div class="absolute top-0 z-[-2] h-screen w-[100%] bg-white bg-[radial-gradient(100%_50%_at_50%_0%,rgba(0,163,255,0.13)_0,rgba(0,163,255,0)_50%,rgba(0,163,255,0)_100%)]"></div>
    <main class="max-w-[800px] my-0 mx-auto">
    <form  method="post" id="enviar2">
        {{ csrf_field()  }}
        <input type="hidden" value="{{$cedula}}" name="cedula" id="cedula">
        <input type="hidden" id="id_master" name="id_master" value="3">
        <input type="hidden" id="page" name="page" value="1">
        <input type="hidden" id="id_orden" name="id_orden" value="{{$id_orden}}">
        <header>
            <div class="flex justify-start">
                <img class="" src="https://ieced.siaam.ec/sis_medico/public/imagenes/login2.png">
            </div>
            <div class="text-center mt-2">
                <h2 class="font-semibold md:text-[2vw] text-[4vw]">En Labs nos interesa tu opinión</h2>
            </div>
        </header>

        <div class="px-4 max-w-[500px] my-0 mx-auto">
            <div class="shadow-lg rounded-lg p-2 ">
                <div id="current_page1" class="py-4">
                    @include("laboratorio.nueva_encuesta_labs.app.page1")
                </div>

                <div id="current_page2" class="py-4">
                    @include("laboratorio.nueva_encuesta_labs.app.page2")
                </div>

                <div id="current_page3" class="py-4">
                    @include("laboratorio.nueva_encuesta_labs.app.page3")
                </div>

                <div id="current_page4" class="py-4">
                    @include("laboratorio.nueva_encuesta_labs.app.page4")
                </div>

                <div id="current_page5" class="py-4">
                    @include("laboratorio.nueva_encuesta_labs.app.page5")
                </div>
                <div id="current_pagefinsih" class="py-4">
                    @include("laboratorio.nueva_encuesta_labs.app.finsih")
                </div>
            </div>
        </div>
    </form>
    </main>

    <div class="button-container">
        <button id="anterior" class="text-sm font-semibold border border-[#45b5b2] rounded-full w-[200px!important] text-[#45b5b2] py-2 shadow-md">
            Anterior
        </button>

        <button id="siguiente" class="text-sm font-semibold border border-[#45b5b2] bg-[#45b5b2] rounded-full w-[200px!important] text-[#f2fbfa] py-2 shadow-md">
            Siguiente
        </button>

        <button onclick="validateQuestions()" id="finalizar" class="text-sm font-semibold border border-[#45b5b2] bg-[#45b5b2] rounded-full w-[200px!important] text-[#f2fbfa] py-2 shadow-md hidden">
            Guardar y enviar
        </button>
    </div>
</body>

</html>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset ("/hc4/js/jquery.js") }}" type="text/javascript"></script>

<script>
    document?.getElementById('anterior')?.addEventListener('click', function() {
        let page = document.getElementById('page').value;
        if (page > 1) {
            page--;
            document.getElementById('page').value = page;
            reloadPage();
        }
    });

    document?.getElementById('siguiente')?.addEventListener('click', function() {
        let page = document.getElementById('page').value;
        page++;
        document.getElementById('page').value = page;
        reloadPage();
    });

    window.onload = function() {
        let page = document.getElementById('page').value;

        if (page == 1) {
            document.getElementById('anterior').style.display = 'none';
        } else {
            document.getElementById('anterior').style.display = 'block';
        }
        reloadPage()
    }

    const reloadPage = () => {
        let page = document.getElementById('page').value;

        if (page == 1) {
            document.getElementById('anterior').style.display = 'none';
        } else {
            document.getElementById('anterior').style.display = 'block';
        }

        document.getElementById('current_page1').style.display = 'none';
        document.getElementById('current_page2').style.display = 'none';
        document.getElementById('current_page3').style.display = 'none';
        document.getElementById('current_page4').style.display = 'none';
        document.getElementById('current_page5').style.display = 'none';
        document.getElementById('current_pagefinsih').style.display = 'none';

        if (page == 1) {
            document.getElementById('current_page1').style.display = 'block';
        }

        if (page == 2) {
            document.getElementById('current_page2').style.display = 'block';
        }
        if (page == 3) {
            document.getElementById('current_page3').style.display = 'block';
        }
        if (page == 4) {
            document.getElementById('current_page4').style.display = 'block';
        }
        if (page == 5) {
            document.getElementById('current_page5').style.display = 'block';
        }
        if (page == 5) {
            document.getElementById('siguiente').style.display = 'none';
            document.getElementById('finalizar').style.display = 'block';
        } else {
            document.getElementById('siguiente').style.display = 'block';
            document.getElementById('finalizar').style.display = 'none';
        }
    }
    const validateQuestions = () => {
        const preguntas = document.querySelectorAll(".pregunta");
        let verificar = false
        preguntas.forEach(pregunta => {
            let radios = document.querySelectorAll(`.check_${pregunta.value}`)
            let check = Array.from(radios).filter(radio => radio.checked)
            if (check.length == 0) {
                pregunta.parentElement.style.color = "red"
                verificar = true
            } else {
                pregunta.parentElement.style.color = "#111827"
            }
        })
        if (verificar) return messageAlert({
            text: "Complete el formulario"
        })

        guardarEncuesta()

    }
    const guardarEncuesta = () => {
        $.ajax({
            type: 'POST',
            url: "{{ route('nuevo_formato_guardar.guardar') }}",
            datatype: 'json',
            data: $("#enviar2").serialize(),
            success: function(data) {
                if (data.status) {
                    // messageAlert({icon: 'success', text: data.message});
                    document.querySelector(".button-container").style.display="none"
                    document.getElementById('current_page5').style.display = 'none';
                    document.getElementById('current_pagefinsih').style.display = 'block';
                } else {
                    $('#boton_enviar').attr("disabled", false);
                    messageAlert({ text:  data.message});
                }
            },
            error: function(data) {
                $('#boton_enviar').attr("disabled", false);
                messageAlert('error', 'Error de Conexion', data.message);
            }
        });
    }

    const messageAlert = ({icon = "error" , text}) => {
        return Swal.fire({
            icon: icon ,
            title: icon == "error" ? "Oops..." : "Excelente!" ,
            text: text,
        });
    }
</script>

<style>
    * {
        font-family: "Poppins", sans-serif;
    }

    .button-container {
        gap: 10px;
        position: fixed;
        bottom: 20px;
        left: 0;
        width: 100%;
        text-align: center;
        padding: 10px;
        display: flex;
        justify-content: center;
    }

    input[type="radio"] {
        display: none;
    }

    label {
        cursor: pointer;
    }

    svg {
        border-radius: 50%;
        padding: 5px;
    }
    @foreach($colors as $color)
			input[type="radio"]:checked+.{{$color["class"]}},
			input[type="radio"]:hover+.{{$color["class"]}}>div {
				background-color: {{$color["bg"]}};
                border-radius: 10px;
				color: {{$color["color"]}};
				transition: all 0.2s ease-in-out;
			}
		@endforeach
</style>