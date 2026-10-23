<section class="content-header">
    <div class="container-fluid">
        <h2 class="text-center display-4">Resultado</h2>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <form action="{{url('v3/')}}/utilidades/buscarTodo">
                    <div class="input-group input-group-lg"><input type="search" name="textBuscador" class="form-control form-control-lg" placeholder="Type your keywords here" value="{{config('data.text')}}">
                        <div class="input-group-append"><button type="submit" class="btn btn-lg btn-default"><i class="fa fa-search"></i></button></div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-10 offset-md-1">
                <?php
                $id = null;
                $num = null;
                if (config('data.busqueda') != null && count(config('data.busqueda')) > 0) {
                    foreach (config('data.busqueda') as $item) {
                        $descripcion = 'Descripción: ' . $item->descripcion;
                        $nombre = 'Nombre: ' . $item->nombre;
                        $codigo = 'Código: ' . $item->codigo;
                        if ($item->modulo == 'ventas') {
                            $id = $item->id;
                            $num = $item->numero_comprobante;
                            $descripcion = 'No. Documento: ' . $item->numero_comprobante;
                            $codigo = 'Cliente: ' . $item->nombre;
                            $nombre = 'Producto: ' . $item->producto;
                        } elseif ($item->modulo == 'clientes') {
                            $codigo = 'Identificación: ' . $item->identificacion;
                            $descripcion = 'E-mail: ' . $item->email;
                        }
                        echo '<div class="list-group"><div class="list-group-item"><a href="javascript:;" class="list-group-item-action" onclick="abrirModulo(\'' . $item->modulo . '\',\'' . $codigo . '\',\'' . $id . '\',\'' . $num . '\')"><div class="row"><div class="col px-4"><div><div class="float-right">Creado/Emitido: ' . $item->created_at . '</div><h3>Módulo: ' . $item->modulo . '</h3>' . $codigo . '<p class="mb-0">' . $nombre . '</p><p class="mb-0">' . $descripcion . '</p></div></div></div></a></div></div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</section>
<div class="modal fade modalTotal" id="myModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal"><i class="fas fa-edit"></i> {{trans('v3/new_varios.cargando')}}...</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="card card-default">
                    <div class="card-body" id="cuerpoBusqueda"></div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> {{trans('v3/new_varios.cerrar')}}</button>
            </div>
        </div>
    </div>
</div>