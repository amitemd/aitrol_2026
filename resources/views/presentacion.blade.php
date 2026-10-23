<style>
   li {
      list-style: none;
   }

   .card_images {
      object-fit: cover;
      width: 100% !important;

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

   /* .data-card.h3:hover{
      color: #FFFFFF;
      border-bottom-color: #A754C4;
   }

   h3:hover {
      color: #FFFFFF;
      border-bottom-color: #A754C4;
   }

   h4:hover {
      color: #FFFFFF;
   }

   p:hover {
      opacity: 1;
      transform: none;
   }

   path:hover {
      fill: #FFFFFF;
   } */

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

   .fa-thumb-tack:hover {
      color: #0d6efd !important;
      cursor: pointer;
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


   .box.box-primary {
      border-top-color: transparent !important;
   }

   .btn-colapse {
      border: 1px solid #3c8dbc;
      color: #3c8dbc;
      background-color: transparent;
      transition: ease .4s all;

   }

   .btn-colapse:hover {
      /* border: 1px solid #3c8dbc; */
      color: white;
      background-color: #3c8dbc;
   }
</style>
<div class="box box-primary collapsed-box" style="margin-bottom: 0px;">
   <div class="box-header estilo_box" style="border-top: 3px solid #3c8dbc;">
      <div class="col-md-12" style="padding-left: 0px;">
         <h3 class="box-title">
            <b style="color:#3c8dbc">Insumos</b>
         </h3>
      </div>
      <!-- tools box -->
      <div class="pull-right box-tools">
         <button type="button" class="btn btn-colapse" data-widget="collapse" title="" data-original-title="Collapse" id="cabecera">
            <i class="fa fa-plus"></i>
         </button>
      </div>
      <!-- /. tools -->
   </div>
   <div class="box-body" style="padding: 5px;">
      @foreach($presentacion as $pres)
      <div class="col-md-12" style="background: #FFFFFF" id="content_card">

         <h3><b>{{$pres->titulo}}</b></h3>

         @foreach($pres->detalle as $det)
         <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <section class="page-contain">
               <div class="data-card contendor-card">
                  <h3 class="titulo"> {{$det->descripcion}}</h3>
                  <h4></h4>
                  <span class="link-text" style="display: flex;justify-content: space-evenly;">
                     <a class="enlace" href="{{url($det->url)}}">Ir <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
                  </span>
               </div>
            </section>
         </div>
         @endforeach
      </div>
      @endforeach
   </div>
</div>
