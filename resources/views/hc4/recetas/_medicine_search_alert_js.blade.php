window.hc4MedicineNotFound = function (medicineName) {
    var name = $.trim(medicineName || '');
    var message = name
        ? 'No se encontró "' + name + '" en el catálogo de medicinas. Revise el nombre o seleccione una opción de la lista.'
        : 'Escriba el nombre de la medicina que desea agregar.';

    if (window.Swal && typeof window.Swal.fire === 'function') {
        window.Swal.fire({
            icon: name ? 'info' : 'warning',
            title: name ? 'Medicina no encontrada' : 'Ingrese una medicina',
            text: message,
            confirmButtonText: 'Entendido'
        });
        return;
    }
    window.alert(message);
};

window.hc4MedicineSearchError = function (xhr) {
    var response = xhr && xhr.responseJSON ? xhr.responseJSON : {};
    var message = response.message || response.error || 'No se pudo consultar el catálogo de medicinas. Intente nuevamente.';
    if (window.Swal && typeof window.Swal.fire === 'function') {
        window.Swal.fire({
            icon: 'error',
            title: 'No se pudo buscar la medicina',
            text: message,
            confirmButtonText: 'Entendido'
        });
        return;
    }
    window.alert(message);
};
