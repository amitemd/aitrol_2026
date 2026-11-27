(() => {
    'use strict';

    const moneda = new Intl.NumberFormat('es-EC', { style: 'currency', currency: 'USD' });

    function actualizarFactura(formulario) {
        if (!formulario?.matches('[data-factura]')) return;
        const totales = { bruto: 0n, descuento: 0n, base: 0n, iva: 0n, propina: 0n, total: 0n };
        for (const linea of formulario.querySelectorAll('[data-comercial-linea]')) {
            const valor = (campo, escala) => {
                const entrada = linea.querySelector('[data-factura-campo="' + campo + '"]');
                const unidades = Math.round(entrada.valueAsNumber * escala);
                return Number.isSafeInteger(unidades) && unidades > 0 ? BigInt(unidades) : 0n;
            };
            const bruto = (valor('cantidad', 1000) * valor('precio', 10000) + 50000n) / 100000n;
            const rebaja = valor('descuento', 100);
            const descuento = rebaja > bruto ? bruto : rebaja;
            const base = bruto - descuento;
            const tasa = BigInt(linea.querySelector('[data-factura-iva]').selectedOptions[0]?.dataset.tasa || 0);
            const iva = (base * tasa + 50n) / 100n;
            totales.bruto += bruto;
            totales.descuento += descuento;
            totales.base += base;
            totales.iva += iva;
            totales.total += base + iva;
            linea.querySelector('[data-factura-linea-total]').textContent = moneda.format(Number(base + iva) / 100);
        }
        if (formulario.querySelector('#factura-propina').checked) {
            totales.propina = (totales.base + 5n) / 10n;
            totales.total += totales.propina;
        }
        for (const [campo, centavos] of Object.entries(totales)) {
            formulario.querySelector('[data-factura-total="' + campo + '"]').textContent = moneda.format(Number(centavos) / 100);
        }
    }

    function actualizarProducto(linea) {
        const producto = linea.querySelector('[data-factura-producto]');
        const opcion = producto.selectedOptions[0]?.dataset;
        const fisico = producto.value && opcion?.tipoProducto !== 'servicio';
        const bodega = linea.querySelector('[data-factura-bodega]');
        const iva = linea.querySelector('[data-factura-iva]');
        const origenIva = linea.querySelector('[data-factura-iva-origen]');
        bodega.closest('[data-factura-bodega-bloque]').hidden = !fisico;
        bodega.required = Boolean(fisico);
        bodega.disabled = !fisico;
        if (!fisico) bodega.value = '';
        iva.disabled = Boolean(producto.value);
        iva.required = !producto.value;
        origenIva.hidden = !producto.value;
        if (producto.value) {
            iva.value = opcion?.ivaCodigo ?? '';
            origenIva.textContent = iva.value === '' ? 'Configura el IVA en la ficha.' : 'IVA de la ficha';
        }
    }

    function actualizarRecurrencia(formulario) {
        const frecuencia = formulario.querySelector('[data-factura-frecuencia]');
        if (!frecuencia) return;
        const bloque = formulario.querySelector('[data-factura-programacion]');
        bloque.hidden = !frecuencia.value;
        bloque.querySelector('input').required = !bloque.hidden;
        bloque.querySelector('input').disabled = bloque.hidden;
    }

    for (const formulario of document.querySelectorAll('[data-factura]')) {
        formulario.querySelectorAll('[data-comercial-linea]').forEach(actualizarProducto);
        actualizarFactura(formulario);
        actualizarRecurrencia(formulario);
        formulario.addEventListener('input', () => actualizarFactura(formulario));
        formulario.addEventListener('change', (event) => {
            if (event.target.matches('[data-factura-producto]')) {
                actualizarProducto(event.target.closest('[data-comercial-linea]'));
            }
            actualizarFactura(formulario);
            actualizarRecurrencia(formulario);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        window.jQuery?.('[data-factura]')
            .on('select2:select', '[data-factura-producto]', function (event) {
                const linea = this.closest('[data-comercial-linea]');
                const producto = event.params.data;
                this.selectedOptions[0].dataset.tipoProducto = producto.tipo_producto;
                this.selectedOptions[0].dataset.ivaCodigo = producto.iva_codigo ?? '';
                if (producto.precio != null) linea.querySelector('[data-factura-campo="precio"]').value = producto.precio;
                actualizarProducto(linea);
                actualizarFactura(this.closest('[data-factura]'));
            })
            .on('select2:clear', '[data-factura-producto]', function () {
                const linea = this.closest('[data-comercial-linea]');
                linea.querySelector('[data-factura-iva]').value = '';
                actualizarProducto(linea);
                actualizarFactura(this.closest('[data-factura]'));
            });
    });

    document.addEventListener('click', (event) => {
        const boton = event.target.closest('[data-comercial-agregar], [data-comercial-quitar]');
        const formulario = boton?.closest('[data-comercial-documento]');
        if (!formulario) return;

        const contenedor = formulario.querySelector('[data-comercial-lineas]');
        if (boton.hasAttribute('data-comercial-agregar')) {
            if (contenedor.children.length >= 100) return;

            const plantilla = formulario.querySelector('[data-comercial-plantilla]');
            const indice = Number(formulario.dataset.siguienteLinea ?? contenedor.children.length);
            formulario.dataset.siguienteLinea = String(indice + 1);
            const linea = plantilla.content.cloneNode(true);
            for (const campo of linea.querySelectorAll('[id], [for], [name]')) {
                for (const atributo of ['id', 'for', 'name']) {
                    const valor = campo.getAttribute(atributo);
                    if (valor) campo.setAttribute(atributo, valor.replace('__INDICE__', indice));
                }
            }
            contenedor.append(linea);
            if (formulario.matches('[data-factura]')) actualizarProducto(contenedor.lastElementChild);
            contenedor.lastElementChild.querySelector('input')?.focus();
        } else {
            if (contenedor.children.length <= Number(formulario.dataset.minLineas ?? 1)) return;

            formulario.dataset.siguienteLinea ??= String(contenedor.children.length);
            boton.closest('[data-comercial-linea]').remove();
            formulario.querySelector('[data-comercial-agregar]').focus();
        }

        Array.from(contenedor.children).forEach((linea, indice) => {
            for (const campo of linea.querySelectorAll('[name]')) {
                campo.name = campo.name.replace(/^lineas\[\d+\]/, 'lineas[' + indice + ']');
            }
        });
        actualizarFactura(formulario);
    });
})();
