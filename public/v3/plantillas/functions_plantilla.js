function mensajes(icon, title, frm = 'frm_') {
    if (icon == 'question') {
        Swal.fire({
            title: title,
            text: $('#informa').val(),
            icon: icon,
            showCancelButton: true,
            cancelButtonText: 'No',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'utilidades/savePlanillaPrincipal',
                    type: 'get',
                    headers: {
                        'X-CSRF-TOKEN': $('#token').val()
                    },
                    data: {
                        plantilla: $('#plantilla').val(),
                    },
                    dataType: 'json',
                    success: function (json) {
                        if (json.status == 'success') {
                            mensajes('success', json.message);
                            console.log($('#base_url').val());
                            window.open($('#base_url').val(), '_self');
                        } else {
                            mensajes('error', json.message);
                        }
                       
                    }
                });
            }
        });
    } else if (icon == 'warning' || icon == 'success' || icon == 'error' || icon == 'info') {
        Swal.fire({
            title: title,
            text: $('#informa').val(),
            icon: icon,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok'
        })
    }
}