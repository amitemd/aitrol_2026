<div class="card card-default">
    <div class="card-body">
        <div class="tab-content" id="custom-content-above-tabContent">
            <div class="tab-pane fade show active" id="custom-content-above-general" role="tabpanel" aria-labelledby="custom-content-above-general-tab">
                <div class="tab-pane fade show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>{{trans('v3/new_varios.codigo')}}</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                    </div>
                                    <input type="text" id="codigo" name="codigo" class="form-control text-right" placeholder="{{trans('v3/new_varios.codigo')}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>&nbsp</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                    </div>
                                    <input type="number" id="codigo1" name="codigo1" class="form-control" placeholder="{{trans('v3/new_varios.codigo')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>{{trans('v3/new_varios.nombre')}}</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                    </div>
                                    <input type="text" id="nombre" name="nombre" class="form-control text-right" placeholder="{{trans('v3/new_varios.nombre')}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>{{trans('v3/new_tabla.descripcion')}}</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                    </div>
                                    <input type="text" id="descripcion" name="descripcion" class="form-control" placeholder="{{trans('v3/new_tabla.descripcion')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>{{trans('v3/new_tabla.tipo')}}</label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2"  id="tipo" name="tipo" width="100%">
                                        <option value="">--Seleccionar--</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Subtipo</label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2" id="subtipo" name="subtipo" width="100%">
                                        <option value="">--Seleccionar--</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>{{trans('v3/new_varios.responsable')}}</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                    </div>
                                    <input type="text" id="responsable" name="responsable" class="form-control text-right" placeholder="{{trans('v3/new_varios.responsable')}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>{{trans('v3/new_tabla.acreedor')}}</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                    </div>
                                    <input type="text" id="acreedor" name="acreedor" class="form-control" placeholder="{{trans('v3/new_tabla.acreedor')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>{{trans('v3/new_varios.marca')}}</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                    </div>
                                    <input type="text" id="marca" name="marca" class="form-control text-right" placeholder="{{trans('v3/new_varios.marca')}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>{{trans('v3/new_varios.color')}}</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                    </div>
                                    <input type="text" id="color" name="color" class="form-control" placeholder="{{trans('v3/new_tabla.color')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>{{trans('v3/new_varios.grupo')}}</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                    </div>
                                    <input type="text" id="grupo_id" name="grupo_id" class="form-control text-right" placeholder="{{trans('v3/new_varios.grupo')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>