<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Masivo Plan de Cuentas</a></li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-body dobra">
            <div class="form-group col-md-12" id="table">
                <form method="post" enctype="multipart/form-data" action="{{ url('/') }}/apipacienteopciones/subirArchivo">
                    {{ csrf_field() }}
                    <div class="content" id="contenedor">
                        <div class="row">
                            <input type="file" name="archivoExcel">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger">
                        Enviar
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>