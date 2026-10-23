(function (root) {
  'use strict';
  root.EmergencyDestination = {
    mount: function (form, steps, bedsBySala, bedStatus) {
      var $ = root.jQuery;
      var get = function (id) { return form.querySelector('#' + id); };
      var disposition = get('destinationDisposition'), sala = get('destinationSala'), bed = get('destinationBed');
      var submit = get('destinationSubmit');
      var salas = Array.from(sala.options).slice(1).map(function (option) {
        return {id: option.value, name: option.text, step: option.getAttribute('data-step')};
      });
      function refresh(select) { $(select).trigger('change.select2'); }
      function options(select, placeholder, items) {
        select.innerHTML = '';
        select.add(new Option(placeholder, ''));
        items.forEach(function (item) {
          var option = new Option(item.name, item.id);
          option.disabled = !!item.disabled;
          select.add(option);
        });
        select.value = '';
        select.disabled = items.length === 0;
        refresh(select);
      }
      function label() {
        var selected = Array.from(bed.options).find(function (option) { return option.value === bed.value; });
        if (selected && selected.disabled) { bed.value = ''; refresh(bed); }
        submit.querySelector('span').textContent = bed.value ? 'Continuar a asignar cama' : 'Registrar destino';
      }
      function loadBeds() {
        var needsBed = steps[disposition.value] && steps[disposition.value] !== 5;
        var beds = needsBed ? (bedsBySala[sala.value] || []).slice() : [];
        beds.sort(function (a, b) { return (a.floor + ' ' + a.room + ' ' + a.code).localeCompare(b.floor + ' ' + b.room + ' ' + b.code, 'es', {numeric: true}); });
        options(bed, 'Asignar cama después', beds.map(function (item) {
          return {id: item.id, name: 'Habitación ' + item.room + ' · Cama ' + item.code + ' · ' + item.floor
            + ' · ' + item.status, disabled: !item.available};
        }));
        var status = bedStatus[sala.value];
        var freeCount = beds.filter(function (item) { return item.available; }).length;
        get('destinationBedHelp').textContent = !sala.value ? 'Seleccione primero la sala receptora.'
          : (status === 'UNCONFIGURED_ROOMS' ? 'Falta vincular las habitaciones de esta sala. Solicite a Sistemas completar la configuración.'
            : (!beds.length ? 'No hay camas registradas en esta sala. Puede dejar el traslado pendiente y asignar la cama después.'
              : (!freeCount ? 'No hay camas libres. Se muestran con su estado; puede dejar el traslado pendiente y asignar la cama después.'
                : freeCount + (freeCount === 1 ? ' cama libre.' : ' camas libres.') + ' Las demás se muestran con su estado y no se pueden elegir.')));
        get('destinationConfigurationHelp').style.display = needsBed && status === 'UNCONFIGURED_ROOMS' ? '' : 'none';
        label();
      }
      function reset() {
        var step = steps[disposition.value];
        var matching = salas.filter(function (item) { return item.step === String(step); });
        options(sala, 'Seleccione sala receptora', matching);
        sala.required = !!step;
        if (matching.length === 1) { sala.value = matching[0].id; refresh(sala); }
        get('destinationSalaGroup').style.display = step ? '' : 'none';
        get('destinationBedGroup').style.display = step && step !== 5 ? '' : 'none';
        get('destinationSalaHelp').textContent = step && !matching.length
          ? 'No hay salas habilitadas para este destino. Solicite a Sistemas configurar el servicio receptor.' : '';
        submit.disabled = !!step && !matching.length;
        get('destinationTransferHelp').textContent = step
          ? (step === 5 ? 'Seleccione la sala quirúrgica. El traslado quedará pendiente de recepción.'
            : 'Elija la habitación y cama. En el siguiente paso confirme la recepción del paciente.')
          : (disposition.value === 'OBSERVATION' ? 'El paciente continúa en Emergencia para observación.'
            : (disposition.value ? 'Registre el motivo para continuar con este destino.' : 'Seleccione el destino del paciente.'));
        loadBeds();
        if (step && !matching.length) get('destinationConfigurationHelp').style.display = '';
        refresh(disposition);
      }
      // Select2 emite eventos jQuery; también se reciben cambios de selects nativos.
      $(disposition).on('change.emergencyDestination', reset);
      $(sala).on('change.emergencyDestination', loadBeds);
      $(bed).on('change.emergencyDestination', label);
      reset();
      return {reset: reset};
    }
  };
}(window));
