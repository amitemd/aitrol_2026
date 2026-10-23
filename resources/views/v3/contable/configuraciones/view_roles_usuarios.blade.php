<style>
    .avatar-group .avatar img,
    .avatar-group .avatar .avatar-initial {
        border: 2px solid #fff
    }

    .avatar-group .avatar .avatar-initial {
        color: #6f6b7d;
        background-color: #f6f6f7
    }

    .avatar-group .avatar:hover {
        z-index: 30;
        transition: all .25s ease
    }

    .avatar-group .avatar {
        margin-left: -0.8rem
    }

    .avatar-group .avatar:first-child {
        margin-left: 0
    }

    .avatar-group .avatar-xs {
        margin-left: -0.65rem
    }

    .avatar-group .avatar-sm {
        margin-left: -0.75rem
    }

    .avatar-group .avatar-md {
        margin-left: -0.9rem
    }

    .avatar-group .avatar-lg {
        margin-left: -1.5rem
    }

    .avatar-group .avatar-xl {
        margin-left: -1.75rem
    }

    [dir=rtl] .avatar-group .avatar {
        margin-right: -0.8rem;
        margin-left: 0
    }

    [dir=rtl] .avatar-group .avatar-xs {
        margin-right: -0.65rem
    }

    [dir=rtl] .avatar-group .avatar-sm {
        margin-right: -0.75rem
    }

    [dir=rtl] .avatar-group .avatar-md {
        margin-right: -0.9rem
    }

    [dir=rtl] .avatar-group .avatar-lg {
        margin-right: -1.5rem
    }

    [dir=rtl] .avatar-group .avatar-xl {
        margin-right: -1.75rem
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{trans('v3/new_submodulo.'.config('data.submodulo'))}}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{url('v3/admin')}}">{{trans('v3/new_menu.inicio')}}</a></li>
                    <li class="breadcrumb-item active">{{trans('v3/new_submodulo.'.config('data.submodulo'))}}</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">{{ config('data.titulo') }}</h3>
            </div>           
            <div class="card-body">
                <div class="row g-4">
                    <?php
                    if (config('data.rolesUsuarios') != '' && count(config('data.rolesUsuarios')) > 0) {
                        foreach (config('data.rolesUsuarios') as $rol) {
                            echo '<div class="col-xl-2 col-lg-4 col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h6 class="fw-normal mb-2">Tipo rol:</h6>
                                    <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                                        <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="Vinnie Mostowy" class="avatar avatar-sm pull-up">
                                            <img class="rounded-circle" src="' . url('v3/') . '/dist/img/user2-160x160.jpg" style="width: 28px;height: 28px;" alt="Avatar">
                                        </li>
                                    </ul>
                                </div>
                                <div class="d-flex justify-content-between align-items-end mt-1">
                                    <div class="role-heading">
                                        <h4 class="mb-1">' . $rol->nombre_rol . '</h4>
                                        <a href="javascript:;" onclick="editarRolUsuario(\'' . $rol->id_rol . '\')" class="role-edit-modal btn btn-primary"><span>Editar Rol</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                        }
                    }
                    ?>
                    <div class="col-xl-2 col-lg-4 col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h6 class="fw-normal mb-2">&nbsp;</h6>
                                    <ul class="list-unstyled d-flex align-items-center avatar-group mb-0"></ul>
                                </div>
                                <div class="d-flex justify-content-between align-items-end mt-1">
                                    <div class="role-heading">
                                        <h4 class="mb-1">Crear un rol</h4>
                                        <a href="javascript:;" onclick="agregarRol()" class="role-edit-modal btn btn-primary"><span>Agregar rol</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class=" modal fade modalTotal" id="myModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal"><i class="fas fa-edit"></i> Editar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_">
                    {{ csrf_field() }}
                    <input type="hidden" id="d" name="d" value="|" />
                    <input type="hidden" id="id_rol" name="id_rol"  />
                    <input type="hidden" id="action" value="{{url('v3/')}}/{{config('data.controlador')}}/saveRolUsuarios" />
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-outline card-info">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Nombre</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                                        </div>
                                                        <input type="text" id="nombre_rol" name="nombre_rol"  onblur="validarNombreRol()" class="form-control" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="tableRolesUsuarios" class="table table-bordered table-striped tablaDatos" width="100%">
                        <thead>
                            <tr>
                                <th>Módulo</th>
                                <th>Submódulo</th>
                                <th>Crear</th>
                                <th>Actualizar</th>
                                <th>Leer</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th>Módulo</th>
                                <th>Submódulo</th>
                                <th>Crear</th>
                                <th>Actualizar</th>
                                <th>Leer</th>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times-circle"></i> Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarRol"><i class="fas fa-save"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>