<script type="text/javascript">
    const modal_activo = (id, e) => {
        e.preventDefault();
        let modal = document.getElementById("modal_datos");
        modal.style.display = "block";
        modal.classList.add("in");

        let modal_hecha = document.getElementById("modal_datos_a" + id);
        if (modal_hecha == null) {
            $("#datos_activo").append(modal_crear(id));
        } else {
            modal_hecha.classList.remove("none");
            modal_hecha.classList.add("block");
        }

        $('.select2_cuentas2').select2({
            tags: false
        });

        $('.select2_color').select2({
            tags: true
        });

    }

    const eliminarModal = id => {
        let modal_eli = document.getElementById('modal_datos_a' + id);

        //oculta la modal donde ingresar los datos
        let modal_datos_a = document.getElementById("modal_datos_a" + id);
        modal_datos_a.classList.remove("block");
        modal_datos_a.classList.add("none");

        //Oculta la modl principal
        let modal = document.getElementById("modal_datos");
        modal.style.display = "none";
        modal.classList.remove("in");

        let check = document.getElementById("af" + id);
        let check_af = document.getElementById("check_af" + id);
        let btn_ac = document.getElementById("btn_ac" + id);

        if (check != null) {
            check.checked = false;
        }

        if (check_af != null) {
            check_af.value = 0;
        }

        if (btn_ac != null) {
            muestra_boton(id)
        }

    }

    const modal_crear = (id, req = 0, data = {}) => {
        //console.log("creando modal", data);
        let codigos = []
        let mod = `
        <div id="modal_padre${id}">
        <div class="block" id="modal_datos_a${id}" class="modal-content" >
                        <input type="hidden" name="mdtiposid[]" value="${id}">
                        <div class="modal-header">
                            <h3 style="margin:0;">{{trans('contableM.activosfijos')}}</h3>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">{{trans('contableM.codigo')}}*</label>
                                <div class="col-xs-4">
                                    <input class="form-control" type="text" id="mdcodigo${id}" name="mdcodigo${id}" value="${req === 1 ? data.codigo_text : ''}"  placeholder="Codigo" maxlength="16">
                                    <input type="hidden" id="mdid${id}" name="mdid${id}[]">
                                </div>
                                <div class="col-xs-1"><span>-</span></div>
                                <div class="col-xs-4">
                                    <input class="form-control" type="text" id="mdcodigo_num${id}" name="mdcodigo_num${id}" placeholder="Codigo" maxlength="16" value="${req === 1 ? data.codigo_num : ''}" onchange="ingresar_cero('mdcodigo_num${id}', 6);">
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">{{trans('contableM.nombre')}}*</label>
                                <div class="col-xs-10">
                                    <input class="form-control" type="text" id="mdnombre${id}" value="${req === 1 ? data.nombre : ''}" ${req === 1 ? 'readonly' : ''} name="mdnombre${id}" placeholder="Nombre">
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">{{trans('contableM.Descripcion')}}</label>
                                <div class="col-xs-10">
                                    <input class="form-control" type="text" id="mddescripcion${id}" name="mddescripcion${id}" value="${req === 1 ? data.descripcion : ''}" ${req === 1 ? 'readonly' : ''} placeholder="Descripción">
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Tipo*</label>
                                <div class="col-xs-10">
                                    <select id="mdtipo${id}" name="mdtipo${id}" class="form-control form-control-sm select2_cuentas2" style="width: 100%;" onchange="buscar_categoria(${id});" >
                                        <option value="">{{trans('contableM.seleccione')}}...</option>
                                        @foreach($tipos as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">{{trans('contableM.categoria')}}*</label>
                                <div class="col-xs-10">
                                    <select id="mdgrupo${id}" name="mdgrupo${id}" class="form-control select2_cuentas2" style="width: 100%;" >
                                        <option value="">{{trans('contableM.seleccione')}}...</option> 
                                    </select>
                                </div>
                            </div> <br>

                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">{{trans('contableM.responsable')}}</label>
                                <div class="col-xs-10">
                                    <select id="mdresponsable${id}" name="mdresponsable${id}" class="form-control form-control-sm select2_color" style="width: 100%;" onchange="guardar_responsable(${id});">
                                        <option value="">{{trans('contableM.seleccione')}}...</option>
                                        @foreach($af_responsables as $responsable)
                                        <option value="{{$responsable->nombre}}">{{$responsable->nombre}}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">{{trans('contableM.ubicacion')}}</label>
                                <div class="col-xs-10">
                                    <input type="text" name="mdubicacion${id}" id="mdubicacion${id}" class="form-control" value="${req === 1 ? data.ubicacion : '' }" placeholder="Ubicación">
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">{{trans('contableM.marca')}}</label>
                                <div class="col-xs-10">
                                    <select id="mdmarca${id}" name="mdmarca${id}" class="form-control select2_color" style="width: 100%;"  onchange="guardar_marca(${id});">
                                        <option value="">{{trans('contableM.seleccione')}}...</option>
                                        @foreach($marcas as $value)
                                        <option value="{{$value->nombre}}">{{$value->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">{{trans('contableM.color')}}</label>
                                <div class="col-xs-10">
                                    <select id="mdcolor${id}" name="mdcolor${id}" class="form-control select2_color" style="width:100%;"  onchange="guardar_color(${id});">
                                        <option value="">{{trans('contableM.seleccione')}}...</option>
                                        @foreach($af_colores as $colores)
                                        <option value="{{$colores->nombre}}">{{$colores->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">{{trans('contableM.modelo')}}</label>
                                <div class="col-xs-10">
                                    <input class="form-control" type="text" id="mdmodelo${id}"  value="${req === 1 ? data.modelo : ''}" name="mdmodelo${id}" placeholder="Modelo">
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">{{trans('contableM.serie')}}</label>
                                <div class="col-xs-10">
                                    <select id="mdserie${id}" name="mdserie${id}" class="form-control select2_color" style="width:100%;"  onchange="guardar_serie(${id});">
                                        <option value="">{{trans('contableM.seleccione')}}...</option>
                                        @foreach($af_series as $series)
                                        <option value="{{$series->nombre}}">{{$series->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> <br>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">{{trans('contableM.procedencia')}}</label>
                                <div class="col-xs-10">
                                    <input class="form-control" type="text" id="mdprocedencia${id}" name="mdprocedencia${id}" value="${req === 1 ? data.procedencia : '' }" placeholder="Procedencia">
                                </div>
                            </div> <br>
                            <input type="hidden" name="fotoactivo${id}" id="fotoactivo${id}" class="form-control px-1 text-right fotoactivo" value="${req === 1 ? data.foto_activo : ''}">
                            <input type="hidden" name="fotoacc${id}" id="fotoacc${id}" class="form-control px-1 text-right fotoacc" value="${req === 1 ? data.foto_acc : ''}">
                        
                            <div class="form-group">
                                <input name='contador_items_accesorios${id}' id='contador_items_accesorios${id}' type='hidden' value="1">
                                <div class="col-md-12 table-responsive">
                                    <table id="items${id}" class="table table table-bordered table-hover dataTable"  role="grid" aria-describedby="example2_info" style="width: 100%;">
                                        <thead class="thead-dark">
                                            <tr class='well-darks'>
                                                <th width="30%" tabindex="0">{{trans('contableM.nombre')}}</th>
                                                <th width="20%" tabindex="0">{{trans('contableM.marca')}}</th>
                                                <th width="20%" tabindex="0">{{trans('contableM.modelo')}}</th>
                                                <th width="20%" tabindex="0">{{trans('contableM.serie')}}</th>
                                                <th width="10%" tabindex="0">
                                                    <button type="button" class="btn btn-success btn-gray" onclick="agregar_items(${id})">
                                                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="fila-fija">
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div> <br> 
                        </div>
                        <br>
                        <div class="modal-footer">
                            <button onclick="guardar_moda(${id})" type="button" class="btn btn-success pull-right" data-dismiss="modal">{{trans('contableM.guardar')}}</button>
                            <button onclick="eliminarModal(${id})" type="button" class="btn btn-danger pull-left" data-dismiss="modal">{{trans('contableM.cerrar')}}</button>
                        </div>
                    </div>
                </div>`
       

        return mod;
    }

    const eliminarModalUni = id => {
        let divCargando = document.getElementById("modal_padre" + id); //padre
        let hijo = document.getElementById("modal_datos_a" + id);
        divCargando.removeChild(hijo)
    }

    const guardar_moda = (id) => {

        let codigo = document.getElementById("codigo" + id);
        let mdcodigo = document.getElementById("mdcodigo" + id).value;
        let mdcodigo_num = document.getElementById("mdcodigo_num" + id).value;

        let mdtipo = document.getElementById("mdtipo" + id).value;
        let mdgrupo = document.getElementById("mdgrupo" + id).value;

        if (mdcodigo.trim() == "") {
            alertas('error', 'Error...', 'Ingrese el codigo')
        } else if (mdcodigo_num.trim() == "") {
            alertas('error', 'Error...', 'Ingrese el Numero de codigo')
        } else if (mdtipo.trim() == '') {
            alertas('error', 'Error...', 'Ingrese el Tipo')
        } else if (mdgrupo.trim() == '') {
            alertas('error', 'Error...', 'Ingrese la Categoria')
        } else {
            codigo.value = mdcodigo + "-" + mdcodigo_num;

            let mdnombre = document.getElementById("mdnombre" + id).value
            document.getElementById("descrip_prod" + id).value = mdnombre;


            //oculta la modal donde ingresar los datos
            let modal_datos_a = document.getElementById("modal_datos_a" + id);
            modal_datos_a.classList.remove("block");
            modal_datos_a.classList.add("none");

            //Oculta la modl principal
            let modal = document.getElementById("modal_datos");
            modal.style.display = "none";
            modal.classList.remove("in");

            //Colocar accesorios en descripcion
            let nombre_ac = document.querySelectorAll("#nombre_ac" + id);
            let desc = "";
            for (let i = 0; i < nombre_ac.length; i++) {
                desc = desc + nombre_ac[i].value + "\n"
            }
            document.getElementById("observacion" + id).value = desc
        }



    }

    function agregar_items(id, req = 0, data = {}) {
       // console.log(data)
        var id_accesorio = document.getElementById('contador_items_accesorios' + id).value;
        var tr = `<tr class="columnas"> 
                            <td>
                                <input required name="nombre_ac${id}[]" value="${req === 1 ? data.nombre : ''}" id="nombre_ac${id}" class="form-control" style="height:25px;width:90%;" autocomplete="off">
                            </td>

                            <td>
                                <select id="marca_ac${id}_${id_accesorio}" name="marca_ac${id}[]"  class="form-control select2_color"  onchange="guardar_marca(${id});" style="width:80%; height:25px;">
                                    <option value="">Seleccione..</option>
                                    @foreach($marcas as $value)
                                        <option value="{{$value->nombre}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                            </td>

                            <td> 
                                <input type="text" name="modelo_ac${id}[]" id="modelo_ac${id}_${id_accesorio}" value="${req === 1 ? data.modelo : '' }" class="form-control cant" style="height:25px;width:75%;" autocomplete="off">
                            </td>

                            <td>
                                <select id="serie_ac${id}_${id_accesorio}" name="serie_ac${id}[]" class="form-control select2_color" onchange="guardar_serie(${id});" style="width:80%; height:25px;">
                                    <option value="">Seleccione</option>
                                    @foreach($af_series as $series)
                                        <option value="{{$series->nombre}}">{{$series->nombre}}</option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <button  type="button" onclick="deleteRow(this)" class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button>
                                
                            </td>
                            <input type="hidden" name="accesorio${id}[]" value="${id_accesorio}" />               
                        </tr> `;
        $('#items' + id).append(tr);
        $('.select2_color').select2({
            tags: true
        });

        if(req === 1){
            let marca  = document.getElementById(`marca_ac${id}_${id_accesorio}`);
            $(`#marca_ac${id}_${id_accesorio} option[value="${data.marca}"]`).attr("selected", true);
            $(`#serie_ac${id}_${id_accesorio} option[value="${data.serie}"]`).attr("selected", true);
        }

        var ids = id_accesorio;
        ids++;
        document.getElementById('contador_items_accesorios' + id).value = ids;

    }

    function deleteRow(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        //calcular();
    }


    function buscar_categoria(id) {
        var valor = $("#mdtipo" + id).val();
        //alert(valor);
        $.ajax({
            type: 'post',
            url: "{{route('documentofactura.buscar_categoria')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'opcion': valor
            },
            success: function(data) {
                $("#mdgrupo" + id).empty();
                $.each(data, function(key, registro) {
                    $("#mdgrupo" + id).append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                });

            },
            error: function(data) {
            }
        })
    }
</script>