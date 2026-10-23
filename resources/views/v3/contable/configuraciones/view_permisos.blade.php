<?php

use Sis_medico\v3\contable\Menu;

if (!function_exists('getE')) {
    function getE($id_menu = null, $combo = '')
    {
        // Obtener menús principales o submenús
        $menus = is_null($id_menu) ? config('data.menu') : Menu::where('id_pertenece_menu', $id_menu)->get();

        foreach ($menus as $rowm) {
            $submenu = Menu::where('id_pertenece_menu', $rowm->id_menu)->get();
            if (count($submenu) > 0) {
                $combo .= '<optgroup label="' . $rowm->nombre_menu . '">';
                $combo .= getE($rowm->id_menu);
                $combo .= '</optgroup>';
            } else {
                $combo .= '<option value="' . $rowm->id_menu . '">' . $rowm->nombre_menu . '</option>';
            }
        }

        return $combo;
    }


}
// MENU
?>

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
                <form id="frm_">
                    {{ csrf_field() }}
                    <input type="hidden" id="id_permiso" name="id_permiso" />
                    <input type="hidden" id="d" name="d" />
                    <input type="hidden" id="action" value="{{url('v3/'.config('data.controlador')).'/savePermisos'}}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{trans('v3/new_tabla.usuarios')}}</label>
                                <select class="form-control select2 inicio_todo" id="id_usuario_permiso" name="id_usuario_permiso" style="width: 100%;">
                                    <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{trans('v3/new_tabla.permisos')}}</label>
                                <select class="select2bs4" multiple="multiple" id="id_submenu_permiso" name="id_submenu_permiso[]" disabled data-placeholder="--{{trans('v3/new_varios.seleccionar')}}--" style="width: 100%;">
                                    <option value="">--{{trans('v3/new_varios.seleccionar')}}--</option>
                                    <?php
                                    if (config('data.menu') != '' && count(config('data.menu')) > 0) {
                                        echo getE();
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-6">
                        <button class="btn btn-primary" id="btnGuardarPermisos"><i class="fas fa-save"></i> {{trans('v3/new_tabla.guardar')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>