<style>
    .notification .badge {
        position: absolute;
        top: -0px;
        right: -0px;
        /* padding: 8px 10px; */
        border-radius: 50%;
        background: red;
        color: white;
    }

    .text-notificacion:hover {
        cursor: pointer;
    }

    .text-notificacion>span {
        color: black;
    }
</style>

<ul class="nav navbar-nav navbar-left">
    <li class="dropdown">
        <a href="#" class="dropdown-toggle notification" data-toggle="dropdown" role="button" aria-expanded="false">
            <i class="fa fa-bell-o" aria-hidden="true"></i>
            <span class="badge" id="number_notificacion">0</span>
        </a>
        <ul class="dropdown-menu" style="width: 300px;" aria-labelledby="dropdownMenu1">
                <li>
                    <div id="content_notificacion" style="display: block;width: 100%;padding-left: 1rem;padding-right: 1rem;font-size: 14px;" id="headlessui-menu-item-:r1h:" role="menuitem" tabindex="-1" data-headlessui-state="active">
                        <a href="##">
                            <div>
                                <div >
                                    <div style="font-size: 14px;margin-bottom: 0px;font-weight: 500;">Cargando...</div>
                                    <div style="font-size: 12px;margin-bottom: 0px;">Cargando...</div>
                                    <div style="font-size: 12px;">....</div>
                                </div>
                                <div><span >1</span></div>
                            </div>
                        </a>
                    </div>
                </li>
        </ul>
    </li>
</ul>


<script>
    const notificacionHeader = () => {
        $.ajax({
            type: 'get',
            dataType: 'json',
            url: ` {{ route('notificacion.global') }} `,
            success: function(data) {
                let contenido = "";
                $("#number_notificacion").empty().append(data.cantidad)
                data.data.map(value => {
                    contenido += messageNotificacion(value)
                })
                $("#content_notificacion").empty().append(contenido)
            },
            error: function(error) {

            }
        })
    }

    const messageNotificacion = (data) => `
    <a href="${data.url}">
        <div>
            <div style="flex: 1 1 0%;">
                <div style="font-size: 14px;margin-bottom: 0px;font-weight: 500;">${data.name}</div>
                <div style="font-size: 12px;margin-bottom: 0px;">${data.message}</div>
                <div style="font-size: 12px;">${data.time}</div>
            </div>
            <div ><span>1</span></div>
        </div>
    </a>
    <hr>
    `
    setInterval(() => {
        notificacionHeader();
    }, 240000)
    // }, 1000)

    window.onload = notificacionHeader()
</script>