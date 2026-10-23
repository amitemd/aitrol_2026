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
</head>
@php
$colors = [
	[ "class" => "super-happy", "bg" => "#d4f5ff","color"=> "#40d5ff" ],
	[ "class" => "happy","bg" => "#d7ffe6","color"=> "#33f57e" ],
	[ "class" => "neutral","bg" => "#f9ffc1","color"=> "#a47f04" ],
	[ "class" => "neutral-2","bg" => "#ffe1e1","color"=> "#ff6a6a" ],
	[ "class" => "super-sad","bg" => "#ffe0dd","color"=> "#ff6857" ]
];
@endphp
<style>
	* {
		font-family: "Poppins", sans-serif;
	}
</style>


<body class="p-4 sm:p-0">
	<div class="absolute top-0 z-[-2] h-screen w-[100%] bg-white bg-[radial-gradient(100%_50%_at_50%_0%,rgba(0,163,255,0.13)_0,rgba(0,163,255,0)_50%,rgba(0,163,255,0)_100%)]"></div>
	<header>
		<div class="flex justify-end">
			<img class="" src="{{asset('/imagenes/login2.png')}}">
		</div>

		<div class="text-center">
			<h2 class="font-semibold">En Labs nos interesa tu opinión</h2>
		</div>
	</header>
	<main class="max-w-[1200px] mx-auto my-8">
		<form  method="post" id="enviar2">
			{{ csrf_field() }}
			<div class="w-full sm:w-[250px]">
				<div class="relative z-0 w-full mb-5 group">
					<input type="number" name="cedula" id="cedula" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
					<label for="cedula" class="peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">* Cedula</label>
					<input type="hidden" id="id_master" name="id_master" value="{{$id_master_tipo->id}}">
				</div>
			</div>

			@php
			$titleTrueOrFalse = 0;
			$size = 50;
			@endphp

			<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
				<table class="w-full text-sm text-left rtl:text-right text-gray-500">
					<thead class="text-xs text-gray-50 uppercase bg-gray-50 bg-[#45b5b2]">
						<tr class="bg-[#45b5b2]">
							<th scope="col" colspan="2" class="px-6 py-3 w-[400px]">
								Criterios de Evaluación
							</th>
							<th scope="col" class="px-4 py-2 sm:px-6 sm:py-3 w-[105px]">
								Muy Bueno
							</th>
							<th scope="col" class="px-4 py-2 sm:px-6 sm:py-3 w-[105px]">
								Bueno
							</th>
							<th scope="col" class="px-4 py-2 sm:px-6 sm:py-3 w-[105px]">
								Ni Bueno ni Malo
							</th>
							<th scope="col" class="px-4 py-2 sm:px-6 sm:py-3 w-[105px]">
								Malo
							</th>
							<th scope="col" class="px-4 py-2 sm:px-6 sm:py-3 w-[105px]">
								Muy Malo
							</th>
						</tr>
					</thead>
					<tbody>
						@foreach($arreglo as $arr)
						@foreach($arr as $item)

						<!-- Cuando las preguntas sean de bueno malo..... etc -->
						@if(count($item["valores"]) > 2)
						@include("laboratorio.nueva_encuesta_labs.TableFiveComponent")
						@endif
						<!-- Finish -->

						<!-- Se entiende que sera un de si y no -->
						@if(count($item["valores"]) == 2)
						@include("laboratorio.nueva_encuesta_labs.TableTrueOrFalseComponent")
						@php
						$titleTrueOrFalse = 1;
						@endphp
						@endif
						<!-- Finisih -->

						<!-- Se entiende que sera un comentario -->
						@if(count($item["valores"]) == 0)
						@include("laboratorio.nueva_encuesta_labs.TableCommentComponent")
						@endif
						<!-- Finisih -->

						@endforeach
						@endforeach
					</tbody>
				</table>
			</div>
		</form>


		<div class="mt-5 text-center">
			<button onclick="validateQuestions()" type="button" class="button text-white bg-[#45b5b2] hover:bg-[#237d7e] transition-all duration-500 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
				Enviar
			</button>
		</div>
	</main>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="{{ asset ("/hc4/js/jquery.js") }}" type="text/javascript"></script>

	<script>
		const validateQuestions = () => {
			const cedula = document.getElementById("cedula")
			if(cedula.value=="") return messageAlert({text: "Ingrese su cedula"})
			
			const preguntas = document.querySelectorAll(".pregunta");
			let verificar= false 
			preguntas.forEach(pregunta => {
				let radios = document.querySelectorAll(`.check_${pregunta.value}`)
				let check = Array.from(radios).filter(radio => radio.checked)
				if(check.length == 0){
					pregunta.parentElement.style.color = "red"
					verificar= true
				}
				else{
					pregunta.parentElement.style.color = "#111827"
				}
			})
			if(verificar) return messageAlert({text: "Complete el formulario"})

			// return messageAlert({icon:"success",text: "Formulario enviado correctamente"})
			guardarEncuesta()
			
		}

		const guardarEncuesta = () => {

			$.ajax({
					type: 'post',
					url: "{{ route('nuevo_formato_guardar.guardar')}}",
					datatype: 'json',
					data: $("#enviar2").serialize(),
					success: function(data) {

						if (data.status) {
							messageAlert({icon: 'success', text: data.message});
							// location.reload();
							// window.location.href="{{route('nuevo.encuestalabs')}}";

						} else {
							$('#boton_enviar').attr("disabled", false);
							messageAlert({ text:  data.message});
							//location.reload();
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
		input[type="radio"] {
			display: none;
		}

		label {
			cursor: pointer;
		} 
		svg{
			border-radius: 50%;
			padding: 5px;
		}

		@foreach($colors as $color)
			input[type="radio"]:checked+.{{$color["class"]}}>svg,
			input[type="radio"]:hover+.{{$color["class"]}}>svg {
				background-color: {{$color["bg"]}};
				border-radius: 50%;
				color: {{$color["color"]}};
				transition: all 0.3s ease-in-out;
			}
		@endforeach
	</style>
</body>

</html>