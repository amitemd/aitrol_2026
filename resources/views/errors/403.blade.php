@extends('layouts.errors')

@section('htmlheader_title')
    Página no encontrada
@endsection
<style>

/*======================
    404 page
=======================*/


.page_404{ padding:40px 0; background:#fff; font-family: 'Arvo', serif;
}

.page_404  img{ width:100%;}

.four_zero_four_bg{
 
 background-image: url(https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif);
    height: 400px;
    background-position: center;
 }
 
 
 .four_zero_four_bg h1{
 font-size:80px;
 }
 
  .four_zero_four_bg h3{
			 font-size:80px;
			 }
			 
			 .link_404{			 
	color: #fff!important;
    padding: 10px 20px;
    background: #39ac31;
    margin: 20px 0;
    display: inline-block;}
	.contant_box_404{ margin-top:-50px;}
</style>
@section('main-content')

    {{--<div class="error-page">
        <h2 class="headline text-yellow"> 404</h2>
        <div class="error-content">
            <h3><i class="fa fa-warning text-yellow"></i> Oops! Página no encontrada.</h3>
            <p>
                No hemos podido encontrar la página que estabas buscando.
                Mientras tanto, es posible <a style="font-size: 40px;" href='{{ url('/dashboard') }}'>volver al panel</a> 
            </p>
        </div><!-- /.error-content -->
    </div><!-- /.error-page -->--}}

    <section class="page_404">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 ">
                    <div class="col-sm-10 col-sm-offset-1  text-center">
                        <div class="four_zero_four_bg">
                            <h1 class="text-center ">403</h1>
                        </div>

                        <div class="contant_box_404">
                            <h3 class="h2">
                                Look like you're lost
                            </h3>

                            <p>La pagina no esta disponible en este momento.</p>

                            <a href="" class="link_404">Ir Inicio</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection