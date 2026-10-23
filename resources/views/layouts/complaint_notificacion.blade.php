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

    .btn-success {
        background: #5cb85c !important;
        color: #ffffff;
    }

    .btn-primary {
        background-color: #3c8dbc !important;
        color: #ffffff;

    }

    .btn-primary:hover {
        color: #fff;
        background-color: #286090 !important;
        border-color: #204d74 !important;
    }

    .btn-danger {
        color: #fff;
        background-color: #d9534f!important;
        border-color: #d43f3a!important;
    }
</style>
<script src="https://cdn.tailwindcss.com"></script>

<ul class="nav navbar-nav navbar-left">
    <li class="dropdown">
        <a href="#" class="dropdown-toggle notification" data-toggle="dropdown" role="button" aria-expanded="false">
            <i class="fa fa-bell-o" aria-hidden="true"></i>
            <span class="badge" id="number_notificacion">0</span>
        </a>
        <ul class="dropdown-menu w-[300px]" role="menu">
            <li>
                <div id="content_notificacion" class="bg-slate-100 text-slate-800 block w-full px-4 py-2 text-sm  cursor-pointer" id="headlessui-menu-item-:r1h:" role="menuitem" tabindex="-1" data-headlessui-state="active">
                    <a href="##">
                        <div class="flex ltr:text-left rtl:text-right space-x-3 rtl:space-x-reverse">
                            <div class="flex-1">
                                <div class="text-slate-800 text-sm font-medium mb-1">Cargando...</div>
                                <div class="text-xs hover:text-[#68768A] text-slate-600 mb-1">Cargando...</div>
                                <div class="text-slate-400 text-xs">....</div>
                            </div>
                            <div class="flex-0"><span class="h-4 w-4 bg-danger-500 border border-white rounded-full text-[10px] flex items-center justify-center text-white">1</span></div>
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
        <div class="flex ltr:text-left rtl:text-right space-x-3 rtl:space-x-reverse">
            <div class="flex-1">
                <div class="text-slate-800 text-sm font-medium mb-1">${data.name}</div>
                <div class="text-xs hover:text-[#68768A] text-slate-600 mb-1">${data.message}</div>
                <div class="text-slate-400 text-xs">${data.time}</div>
            </div>
            <div class="flex-0"><span class="h-4 w-4 bg-danger-500 border border-white rounded-full text-[10px] flex items-center justify-center text-white">1</span></div>
        </div>
    </a>
    <hr>
    `
    setInterval(() => {
        notificacionHeader();
    }, 240000)

    window.onload = notificacionHeader()
</script>