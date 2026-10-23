<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ ucwords(strtolower(config('data.submodulo_'))) }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">{{ ucwords(strtolower(config('data.submodulo_'))) }}</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <label title="Editar" for="usuarioFoto" style="cursor: pointer;">
                                <div id="fotoUsuario">
                                    <?php

                                    use Sis_medico\v3\contable\Utilidades;

                                    $url = url('/') . config('data.usuario')->foto;
                                    $url = str_replace('public/', '', $url);
                                    ?>
                                    <img class="profile-user-img img-fluid img-circle" src="<?= config('data.usuario')->foto == '' ? url('v3/') . '/dist/img/sinfoto.png' : $url; ?>" alt="User profile picture">
                                </div>
                                <input type="file" name="usuarioFoto" id="usuarioFoto" style="display:none">
                                <i class="fas fa-edit"></i>
                            </label>
                        </div>
                        <h3 class="profile-username text-center">{{ucwords(strtolower(config('data.usuario')->nombre1.' '.config('data.usuario')->apellido1))}}</h3>
                        <p class="text-muted text-center">{{ucwords(strtolower(config('data.usuario')->ocupacion))}}</p>
                        {{-- <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Followers</b> <a class="float-right">1,322</a>
                            </li>
                            <li class="list-group-item">
                                <b>Following</b> <a class="float-right">543</a>
                            </li>
                            <li class="list-group-item">
                                <b>Friends</b> <a class="float-right">13,287</a>
                            </li>
                        </ul>
                        <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> --}}
                    </div>

                </div>
                <!-- div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">About Me</h3>
                    </div>

                    <div class="card-body">
                        <strong><i class="fas fa-book mr-1"></i> Education</strong>
                        <p class="text-muted">
                            B.S. in Computer Science from the University of Tennessee at Knoxville
                        </p>
                        <hr>
                        <?php
                        if (config('data.usuario')->pais != '') {
                        ?>
                            <strong><i class="fas fa-map-marker-alt mr-1"></i> Dirección</strong>
                            <p class="text-muted">{{config('data.usuario')->pais}}, {{config('data.usuario')->ciudad}}</p>
                            <hr>
                        <?php
                        }
                        ?>
                        <strong><i class="fas fa-pencil-alt mr-1"></i> Skills</strong>
                        <p class="text-muted">
                            <span class="tag tag-danger">UI Design</span>
                            <span class="tag tag-success">Coding</span>
                            <span class="tag tag-info">Javascript</span>
                            <span class="tag tag-warning">PHP</span>
                            <span class="tag tag-primary">Node.js</span>
                        </p>
                        <hr>
                        <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>
                        <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum enim neque.</p>
                    </div>

                </div -->

            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <!--<li class="nav-item"><a class="nav-link" href="#activity" data-toggle="tab">Activity</a></li>
                            <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Timeline</a></li>-->
                            <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Settings</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane" id="activity">
                                <div class="post">
                                    <div class="user-block">
                                        <img class="img-circle img-bordered-sm" src="{{url('v3/')}}/dist/img/user1-128x128.jpg" alt="user image">
                                        <span class="username">
                                            <a href="#">Jonathan Burke Jr.</a>
                                            <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                                        </span>
                                        <span class="description">Shared publicly - 7:30 PM today</span>
                                    </div>

                                    <p>
                                        Lorem ipsum represents a long-held tradition for designers,
                                        typographers and the like. Some people hate it and argue for
                                        its demise, but others ignore the hate as they create awesome
                                        tools to help create filler text for everyone from bacon lovers
                                        to Charlie Sheen fans.
                                    </p>
                                    <p>
                                        <a href="#" class="link-black text-sm mr-2"><i class="fas fa-share mr-1"></i> Share</a>
                                        <a href="#" class="link-black text-sm"><i class="far fa-thumbs-up mr-1"></i> Like</a>
                                        <span class="float-right">
                                            <a href="#" class="link-black text-sm">
                                                <i class="far fa-comments mr-1"></i> Comments (5)
                                            </a>
                                        </span>
                                    </p>
                                    <input class="form-control form-control-sm" type="text" placeholder="Type a comment">
                                </div>


                                <div class="post clearfix">
                                    <div class="user-block">
                                        <img class="img-circle img-bordered-sm" src="{{url('v3/')}}/dist/img/user7-128x128.jpg" alt="User Image">
                                        <span class="username">
                                            <a href="#">Sarah Ross</a>
                                            <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                                        </span>
                                        <span class="description">Sent you a message - 3 days ago</span>
                                    </div>

                                    <p>
                                        Lorem ipsum represents a long-held tradition for designers,
                                        typographers and the like. Some people hate it and argue for
                                        its demise, but others ignore the hate as they create awesome
                                        tools to help create filler text for everyone from bacon lovers
                                        to Charlie Sheen fans.
                                    </p>
                                    <form class="form-horizontal">
                                        <div class="input-group input-group-sm mb-0">
                                            <input class="form-control form-control-sm" placeholder="Response">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-danger">Send</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>


                                <div class="post">
                                    <div class="user-block">
                                        <img class="img-circle img-bordered-sm" src="{{url('v3/')}}/dist/img/user6-128x128.jpg" alt="User Image">
                                        <span class="username">
                                            <a href="#">Adam Jones</a>
                                            <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                                        </span>
                                        <span class="description">Posted 5 photos - 5 days ago</span>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-6">
                                            <img class="img-fluid" src="{{url('v3/')}}/dist/img/photo1.png" alt="Photo">
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <img class="img-fluid mb-3" src="{{url('v3/')}}/dist/img/photo2.png" alt="Photo">
                                                    <img class="img-fluid" src="{{url('v3/')}}/dist/img/photo3.jpg" alt="Photo">
                                                </div>

                                                <div class="col-sm-6">
                                                    <img class="img-fluid mb-3" src="{{url('v3/')}}/dist/img/photo4.jpg" alt="Photo">
                                                    <img class="img-fluid" src="{{url('v3/')}}/dist/img/photo1.png" alt="Photo">
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <p>
                                        <a href="#" class="link-black text-sm mr-2"><i class="fas fa-share mr-1"></i> Share</a>
                                        <a href="#" class="link-black text-sm"><i class="far fa-thumbs-up mr-1"></i> Like</a>
                                        <span class="float-right">
                                            <a href="#" class="link-black text-sm">
                                                <i class="far fa-comments mr-1"></i> Comments (5)
                                            </a>
                                        </span>
                                    </p>
                                    <input class="form-control form-control-sm" type="text" placeholder="Type a comment">
                                </div>

                            </div>
                            <div class="tab-pane" id="timeline">
                                <div class="timeline timeline-inverse">
                                    <div class="time-label">
                                        <span class="bg-danger">
                                            10 Feb. 2014
                                        </span>
                                    </div>
                                    <div>
                                        <i class="fas fa-envelope bg-primary"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="far fa-clock"></i> 12:05</span>
                                            <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>
                                            <div class="timeline-body">
                                                Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                                                weebly ning heekya handango imeem plugg dopplr jibjab, movity
                                                jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                                                quora plaxo ideeli hulu weebly balihoo...
                                            </div>
                                            <div class="timeline-footer">
                                                <a href="#" class="btn btn-primary btn-sm">Read more</a>
                                                <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <i class="fas fa-user bg-info"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="far fa-clock"></i> 5 mins ago</span>
                                            <h3 class="timeline-header border-0"><a href="#">Sarah Young</a> accepted your friend request
                                            </h3>
                                        </div>
                                    </div>
                                    <div>
                                        <i class="fas fa-comments bg-warning"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="far fa-clock"></i> 27 mins ago</span>
                                            <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>
                                            <div class="timeline-body">
                                                Take me to your leader!
                                                Switzerland is small and neutral!
                                                We are more like Germany, ambitious and misunderstood!
                                            </div>
                                            <div class="timeline-footer">
                                                <a href="#" class="btn btn-warning btn-flat btn-sm">View comment</a>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="time-label">
                                        <span class="bg-success">
                                            3 Jan. 2014
                                        </span>
                                    </div>


                                    <div>
                                        <i class="fas fa-camera bg-purple"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="far fa-clock"></i> 2 days ago</span>
                                            <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>
                                            <div class="timeline-body">
                                                <img src="https://placehold.it/150x100" alt="...">
                                                <img src="https://placehold.it/150x100" alt="...">
                                                <img src="https://placehold.it/150x100" alt="...">
                                                <img src="https://placehold.it/150x100" alt="...">
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <i class="far fa-clock bg-gray"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="active tab-pane" id="settings">
                                <form id="frm_" class="form-horizontal">
                                    {{ csrf_field() }}
                                    <input type="hidden" id="d" name="d" value="|">
                                    <input type="hidden" id="estado" name="estado" value="{{config('data.usuario')->estado}}">
                                    <input type="hidden" id="action" value="{{ url('v3/') }}/{{ config('data.controlador') }}/saveUsuario">
                                    <div class="form-group row">
                                        <label for="nombre1" class="col-sm-2 col-form-label">Primer nombre</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="nombre1" name="nombre1" placeholder="Primer nombre" value="{{config('data.usuario')->nombre1}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="nombre2" class="col-sm-2 col-form-label">Segundo nombre</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="nombre2" name="nombre2" placeholder="Segundo nombre" value="{{config('data.usuario')->nombre2}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="apellido1" class="col-sm-2 col-form-label">Primer apellido</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="apellido1" name="apellido1" placeholder="Primer apellido" value="{{config('data.usuario')->apellido1}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="apellido2" class="col-sm-2 col-form-label">Segundo apellido</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="apellido2" name="apellido2" placeholder="Segundo apellido" value="{{config('data.usuario')->apellido2}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="id" class="col-sm-2 col-form-label">Cédula</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control text-right" id="id" name="id" placeholder="Cédula" value="{{config('data.usuario')->id}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="email" class="col-sm-2 col-form-label">E-mail</label>
                                        <div class="col-sm-10">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="E-mail" value="{{config('data.usuario')->email}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="telefono1" class="col-sm-2 col-form-label">Teléfono</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control text-right" id="telefono1" name="telefono1" placeholder="Teléfono" value="{{config('data.usuario')->telefono1}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="telefono2" class="col-sm-2 col-form-label">Mobil</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control text-right" id="telefono2" name="telefono2" placeholder="Mobil" value="{{config('data.usuario')->telefono2}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="pais" class="col-sm-2 col-form-label">País</label>
                                        <div class="col-sm-10">
                                            <select class="form-control select2 pais" id="pais" name="pais">
                                                <option value="">--Seleccione--</option>
                                                <?php
                                                if (config('data.paises') != null && count(config('data.paises')) > 0) {
                                                    foreach (config('data.paises') as $row) {
                                                        $select = '';
                                                        if ($row->id == config('data.usuario')->id_pais) {
                                                            $select = 'selected';
                                                        }
                                                        echo '<option value="' . $row->id . '" ' . $select . '>' . ucwords(strtolower(Utilidades::sanear_string_tildes($row->nombre))) . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="provincia" class="col-sm-2 col-form-label">Provincia</label>
                                        <div class="col-sm-10">
                                            <input type="hidden" id="provincia" value="{{config('data.usuario')->id_provincia_ciudad}}" />
                                            <select class="form-control select2 provincia" id="provincia" name="provincia">
                                                <option value="">--Seleccione--</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="ciudad" class="col-sm-2 col-form-label">Ciudad</label>
                                        <div class="col-sm-10">
                                            <input type="hidden" id="id_ciudad_perfil_usuario" value="{{config('data.usuario')->id_ciudad_usuario}}" />
                                            <select class="form-control select2 ciudad" id="id_ciudad" name="id_ciudad">
                                                <option value="">--Seleccione--</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="direccion" class="col-sm-2 col-form-label">Dirección</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección" value="{{config('data.usuario')->direccion}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="urbanizacion" class="col-sm-2 col-form-label">Urbanización</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="urbanizacion" name="urbanizacion" placeholder="Urbanización" value="{{config('data.usuario')->urbanizacion}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="etapa" class="col-sm-2 col-form-label">Etapa</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="etapa" name="etapa" placeholder="Etapa" value="{{config('data.usuario')->etapa}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="manzana" class="col-sm-2 col-form-label">Manzana</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="manzana" name="manzana" placeholder="Manzana" value="{{config('data.usuario')->manzana}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="villa" class="col-sm-2 col-form-label">Villa</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="villa" name="villa" placeholder="Villa" value="{{config('data.usuario')->villa}}">
                                        </div>
                                    </div>
                                    @if(config('data.permisos')->id_submenu_permiso != '')
                                    <div class="form-group row">
                                        <label for="id_tipo_usuario" class="col-sm-2 col-form-label">Tipo de usuario</label>
                                        <div class="col-sm-10">
                                            <select class="form-control select2" id="id_tipo_usuario" name="id_tipo_usuario" disabled>
                                                <option value="">--Seleccione--</option>
                                                <?php
                                                if (config('data.tipousuario') != null && count(config('data.tipousuario')) > 0) {
                                                    foreach (config('data.tipousuario') as $row) {
                                                        $select = '';
                                                        if (config('data.usuario') != null || config('data.usuario') != '') {
                                                            if (config('data.usuario')->id_tipo_usuario == $row->id) {
                                                                $select = 'selected';
                                                            }
                                                        }
                                                        echo '<option value="' . $row->id . '" ' . $select . '>' . $row->nombre . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <input type="hidden" name="id_tipo_usuario" value="{{ config('data.usuario')->id_tipo_usuario ?? '' }}">
                                        </div>
                                    </div>
                                    @endif
                                    <div class="form-group row">
                                        <label for="ocupacion" class="col-sm-2 col-form-label">Cargo</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="especialidad" name="ocupacion" placeholder="Cargo" value="{{config('data.usuario')->ocupacion}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="fecha_nacimiento" class="col-sm-2 col-form-label">Fecha de nacimiento</label>
                                        <div class="col-sm-10">
                                            <input type="date" class="form-control text-right" id="fecha_nacimiento" name="fecha_nacimiento" placeholder="Fecha de nacimiento" value="{{config('data.usuario')->fecha_nacimiento}}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputSkills" class="col-sm-2 col-form-label">Skills</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="inputSkills" placeholder="Skills">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="offset-sm-2 col-sm-10">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                    <input type="checkbox" class="custom-control-input" <?= config('data.usuario')->estado == 1 ? 'checked' : ''; ?> value="{{config('data.usuario')->estado}}" id="cheestado">
                                                    <label class="custom-control-label" for="cheestado">Estado</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="offset-sm-2 col-sm-10">
                                            <button type="button" class="btn btn-primary" id="btnGuardarPerfil"><i class="fa fa-save"></i> Guardar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>