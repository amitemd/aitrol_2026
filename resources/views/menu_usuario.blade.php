<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">



<style>
   * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
   }

   li {
      list-style: none;
   }

   .card_images {
      object-fit: cover;
      width: 100% !important;

   }

   .page-contain {
      display: flex;
      /* min-height: 100vh; */
      align-items: center;
      justify-content: center;
      background: transparent;
      /* border: .75em solid white; */
      /* padding: 2em; */
      font-family: 'Open Sans', sans-serif;
      flex: 1 1 auto;
      flex-direction: column;
   }

   .data-card {
      display: flex;
      flex-direction: column;
      width: 17.75em;
      min-height: 16.75em;
      overflow: hidden;
      border-radius: .5em;
      text-decoration: none;
      background: white;
      margin: 1em;
      padding: 2.75em 2.5em;
      box-shadow: 0 1.5em 2.5em -.5em rgba(#000000, .1);
      transition: transform .45s ease, background .45s ease;
   }

   .titulo {
      color: #2E3C40;
      font-size: 2em;
      font-weight: 600;
      line-height: 1;
      padding-bottom: .5em;
      margin: 0 0 0.142857143em;
      border-bottom: 2px solid #753BBD;
      transition: color .45s ease, border .45s ease;
   }

   h4 {
      color: #627084;
      text-transform: uppercase;
      font-size: 1.125em;
      font-weight: 700;
      line-height: 1;
      letter-spacing: 0.1em;
      margin: 0 0 1.777777778em;
      transition: color .45s ease;
   }

   p {
      opacity: 0;
      color: #000;
      font-weight: 600;
      line-height: 1.8;
      margin: 0 0 1.25em;
      transform: translateY(-1em);
      transition: opacity .45s ease, transform .5s ease;
   }

   .link-text {
      display: block;
      color: #753BBD;
      font-size: 1.125em;
      font-weight: 600;
      line-height: 1.2;
      margin: auto 0 0;
      transition: color .45s ease;


   }

   path {
      transition: fill .45s ease;
   }

   svg {
      margin-left: .5em;
      transition: transform .6s ease;


   }


   .data-card:hover {
      /* background: #753BBD; */
      transform: scale(1.02);

   }

   svg:hover {
      animation: point 1.25s infinite alternate;
   }

   /* 
   .link-text:hover {
      color: #FFFFFF;
   } */


   @keyframes point {
      0% {
         transform: translateX(0);
      }

      100% {
         transform: translateX(.125em);
      }

   }


   .enlace {
      border: 1px solid var(--colorPrimario);
      padding: 5px 33px;
      border-radius: 20px;
      transition: ease .2s all;
   }

   .enlace:hover {
      background-color: var(--colorPrimario);
      color: white;
   }

   .enlace-delete {
      border: 1px solid #dd4b39;
      padding: 5px 25px;
      border-radius: 20px;
      color: #dd4b39;
      font-weight: bold;
      transition: ease .3s all;
      background-color: transparent !important;
   }

   .enlace-delete:hover {
      background-color: #dd4b39!important;
      color: white;
   }

   .box.box-primary {
      border-top-color: transparent !important;
   }

   .btn-colapse{
      border: 1px solid #3c8dbc;
      color: #3c8dbc;
      background-color: transparent;
      transition: ease .4s all;

   }

   .btn-colapse:hover{
      /* border: 1px solid #3c8dbc; */
      color: white;
      background-color: #3c8dbc;
   }

   .buscador_target{
      border-radius: 20px;
      margin-bottom: 15px;
      width: 40%;
   }
   .grid-cards{
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(17.75em, 1fr));
      gap: 20px;
      margin-top: 0px;
      padding: 0px 20px;
   }

   @media (max-width: 768px) {
      .buscador_target{
         width: 100%;
      }
   }


</style>
<div class="box box-primary " style="margin-bottom: 0px;">
   <div class="box-header estilo_box" style="border-top: 3px solid #3c8dbc;">
      <div class="col-md-12" style="padding-left: 0px;">
         <h3 class="box-title">
            <b style="color:#3c8dbc">Accesos Directos</b>
         </h3>
      </div>
      <!-- tools box -->
      <div class="pull-right box-tools">
         <button type="button" class="btn btn-colapse" data-widget="collapse" title="" data-original-title="Collapse" id="cabecera">
            <i class="fa fa-minus"></i>
         </button>
      </div>
      <!-- /. tools -->
   </div>
   <div class="box-body" style="padding: 5px;">

      <div class="" style="display:flex; justify-content: center; padding: 0px;">
            <input style="margin-bottom: 15px;" type="text" name="buscador_target" onkeyup="buscadorTarget(1, 'buscador_target')"  class="form-control buscador_target contendor-card" placeholder="Busca en los accesos directos...">
      </div>

      <div class="grid-cards" style="background: #ecf0f5; height: 62vh;overflow-y: scroll;" id="content_card">
         @foreach($menu_usuarios as $menu)
         <div class="menu_{{$menu->id}}">
            <section class="page-contain">
               <div class="data-card contendor-card">
                  <h3 class="titulo"> {{$menu->titulo}}</h3>
                  <h4>{{$menu->descripcion}}</h4>
                  <span class="link-text" style="display: flex;justify-content: space-evenly;">
                     <a class="enlace" href="{{$menu->url}}">Ir <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
                     <button onclick="eliminarTarget({{$menu->id}})" class="enlace-delete" >
                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                     </button>
                  </span>
               </div>
            </section>
         </div>
         @endforeach
      </div>
   </div>
</div>

<script>
   

   const eliminarTarget = id => {
       
      $.ajax({
         url: `{{ route("menu.eliminar") }}`,
         type: "get",
         datatype: 'json',
         data: {
            'id': id
         },
         success: function(data) {
            // crearCard(e, ruta, titulo, descripcion);
            location.reload();
         },
         error: function(data) {

         }
      })
   }

   function crearCard(e, ruta, titulo, descripcion) {
      let cards = ` 
      <div class="">
         <section class="page-contain">
            <div  class="data-card contendor-card">
               <h3 class="titulo"> ${titulo}</h3>
               <h4>${descripcion}</h4>
               <span class="link-text" style="display: flex;justify-content: space-evenly;">
                  <a class="enlace" href="${ruta}">Ir <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
                  <a class="enlace-delete" href=""><i class="fa fa-trash-o" aria-hidden="true"></i></a>
               </span>
            </div>
         </section>
      </div>`

      $("#content_card").append(cards)
   }


   /*const buscadorTarget = () => {
      let buscador_target = document.querySelector(".buscador_target").value;
      // if(buscador_target.length < 3) {
      //    return ;
      // }
      $.ajax({
         url: `{{ route('menu.buscadorTarget') }}`,
         type: "get",
         dataType: "json",
         data: {
            'buscador' : buscador_target,
         }, success: function (data) {
            // console.log(data);
            $("#content_card").empty();
            $("#content_card").append(data.targets);
         },error: function (data){

         }
      })
      console.log(buscador_target);

   }*/

</script>