(() => {
    'use strict';

    const moneda = new Intl.NumberFormat('es-EC', { style: 'currency', currency: 'USD' });

    function unidades(entrada, escala) {
        const valor = Math.round(entrada.valueAsNumber * escala);
        return Number.isSafeInteger(valor) && valor > 0 ? BigInt(valor) : 0n;
    }

    function actualizarCompra(formulario) {
        if (!formulario.matches('[data-compra]')) return;
        let subtotal = 0n;
        let impuesto = 0n;
        const bodega = formulario.querySelector('[data-compra-bodega]');
        const requiereBodega = [...formulario.querySelectorAll('[data-compra-producto]')].some((campo) =>
            campo.value && campo.selectedOptions[0]?.dataset.tipoProducto === 'producto');
        bodega.value = requiereBodega ? bodega.dataset.bodegaPrincipal : 'No aplica';
        for (const linea of formulario.querySelectorAll('[data-comercial-linea]')) {
            const cantidad = unidades(linea.querySelector('[data-compra-campo="cantidad"]'), 1000);
            const precio = unidades(linea.querySelector('[data-compra-campo="precio"]'), 10000);
            const tasa = unidades(linea.querySelector('[data-compra-campo="tasa"]'), 100);
            const descuento = unidades(linea.querySelector('[data-compra-campo="descuento"]'), 100);
            const bruto = (cantidad * precio + 50000n) / 100000n;
            const base = descuento <= bruto ? bruto - descuento : 0n;
            const tributo = (base * tasa + 5000n) / 10000n;
            subtotal += base;
            impuesto += tributo;
            linea.querySelector('[data-compra-linea-total]').textContent = moneda.format(Number(base + tributo) / 100);
        }
        for (const [campo, centavos] of Object.entries({ subtotal, impuesto, total: subtotal + impuesto })) {
            formulario.querySelector('[data-compra-total="' + campo + '"]').textContent = moneda.format(Number(centavos) / 100);
        }
    }

    function prepararAltaProducto() {
        const dialogo = document.querySelector('[data-compra-producto-dialogo]');
        if (!dialogo) return;

        const formulario = dialogo.querySelector('form');
        const guardar = formulario.querySelector('button[type="submit"]');
        const error = formulario.querySelector('[data-compra-producto-error]');
        let producto;

        document.querySelector('[data-compra]').addEventListener('click', (evento) => {
            const boton = evento.target.closest('[data-compra-crear-producto]');
            if (!boton) return;

            const linea = boton.closest('[data-comercial-linea]');
            producto = linea.querySelector('select');
            formulario.reset();
            formulario.elements.codigo.value = boton.dataset.codigo;
            formulario.elements.nombre.value = linea.querySelector('input[name$="[descripcion]"]').value.slice(0, 160);
            formulario.elements.costo_unitario_referencia.value = linea.querySelector('[data-compra-campo="precio"]').value;
            formulario.elements.iva_codigo.value = boton.dataset.iva;
            error.hidden = true;
            dialogo.showModal();
            formulario.elements.codigo.focus();
        });

        dialogo.querySelectorAll('[data-compra-cerrar-producto]').forEach((boton) => {
            boton.addEventListener('click', () => {
                if (!guardar.disabled) dialogo.close();
            });
        });
        dialogo.addEventListener('cancel', (evento) => {
            if (guardar.disabled) evento.preventDefault();
        });

        formulario.addEventListener('submit', async (evento) => {
            evento.preventDefault();
            if (guardar.disabled) return;
            guardar.disabled = true;
            error.hidden = true;

            try {
                const respuesta = await fetch(formulario.action, {
                    method: 'POST',
                    headers: { Accept: 'application/json' },
                    body: new FormData(formulario),
                });
                const datos = await respuesta.json();
                if (!respuesta.ok || !datos.data?.id) {
                    throw new Error(Object.values(datos.errors ?? {}).flat().join(' ') || datos.message);
                }

                const creado = datos.data;
                const opcion = new Option(creado.codigo + ' · ' + creado.nombre, creado.id, true, true);
                opcion.dataset.tipoProducto = creado.tipo_producto;
                producto.add(opcion);
                producto.dispatchEvent(new Event('change', { bubbles: true }));
                dialogo.close();
            } catch (fallo) {
                error.textContent = fallo instanceof SyntaxError || fallo instanceof TypeError
                    ? 'No se pudo guardar el producto. Revisa la conexión e inténtalo nuevamente.'
                    : fallo.message || 'No se pudo guardar el producto.';
                error.hidden = false;
            } finally {
                guardar.disabled = false;
            }
        });
    }

    function actualizarFactura(formulario) {
        if (!formulario?.matches('[data-factura]')) return;
        const totales = { bruto: 0n, descuento: 0n, base: 0n, iva: 0n, propina: 0n, total: 0n };
        for (const linea of formulario.querySelectorAll('[data-comercial-linea]')) {
            const valor = (campo, escala) => {
                const entrada = linea.querySelector('[data-factura-campo="' + campo + '"]');
                return unidades(entrada, escala);
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
        const fisico = producto.value && opcion?.tipoProducto === 'producto';
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

    function prepararContactoFactura(formulario) {
        const cliente = formulario.querySelector('[data-factura-cliente]');
        const bloque = formulario.querySelector('[data-factura-contacto]');
        const campos = [...bloque.querySelectorAll('[data-contacto-campo]')];
        const guardar = bloque.querySelector('[data-contacto-guardar]');
        const estado = bloque.querySelector('[data-contacto-estado]');
        let solicitud;
        let guardando = false;

        const hayCambios = () => campos.some(campo => campo.value !== campo.defaultValue);
        const url = () => cliente.dataset.contactoUrl.replace('__CLIENTE__', encodeURIComponent(cliente.value));

        cliente.onchange = async () => {
            solicitud?.abort();
            bloque.hidden = !cliente.value;
            if (guardar) guardar.disabled = true;
            for (const campo of campos) {
                campo.value = campo.defaultValue = '';
                campo.disabled = true;
            }
            if (!cliente.value) return;

            const actual = new AbortController();
            solicitud = actual;
            estado.textContent = 'Cargando datos del cliente…';
            try {
                const respuesta = await fetch(url(), { headers: { Accept: 'application/json' }, signal: actual.signal });
                if (!respuesta.ok) throw new Error();
                const datos = await respuesta.json();
                if (actual.signal.aborted) return;
                for (const campo of campos) {
                    campo.value = campo.defaultValue = datos[campo.dataset.contactoCampo] ?? '';
                    campo.disabled = false;
                }
                estado.textContent = '';
            } catch {
                if (!actual.signal.aborted) estado.textContent = 'No se pudieron cargar los datos. Vuelve a seleccionar el cliente.';
            }
        };

        if (!guardar) return;
        bloque.addEventListener('input', () => { guardar.disabled = guardando || !hayCambios(); });
        guardar.addEventListener('click', async () => {
            if (!cliente.value || !hayCambios() || guardando || !campos.every(campo => campo.reportValidity())) return;
            guardando = true;
            guardar.disabled = true;
            estado.textContent = 'Guardando datos…';
            const contactoUrl = url();
            const carga = solicitud;
            const datos = Object.fromEntries(campos.map(campo => [campo.dataset.contactoCampo, campo.value.trim() || null]));
            try {
                const respuesta = await fetch(contactoUrl, {
                    method: 'PATCH',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': formulario.elements._token.value,
                    },
                    body: JSON.stringify(datos),
                });
                const resultado = await respuesta.json();
                if (bloque.hidden || carga !== solicitud) return;
                if (!respuesta.ok) {
                    estado.textContent = Object.values(resultado.errors ?? {}).flat()[0] ?? 'No se pudieron actualizar los datos del cliente.';
                    return;
                }
                for (const campo of campos) {
                    const valorGuardado = datos[campo.dataset.contactoCampo] ?? '';
                    if (campo.value.trim() === valorGuardado) campo.value = valorGuardado;
                    campo.defaultValue = valorGuardado;
                }
                estado.textContent = resultado.message;
            } catch {
                if (!bloque.hidden && carga === solicitud) estado.textContent = 'No se pudieron actualizar los datos. Intenta de nuevo.';
            } finally {
                guardando = false;
                guardar.disabled = !hayCambios();
            }
        });
        formulario.addEventListener('submit', (evento) => {
            if (!guardando && !hayCambios()) return;
            evento.preventDefault();
            estado.textContent = guardando ? 'Espera a que se guarden los datos.' : 'Pulsa Actualizar datos del cliente antes de registrar la factura.';
            guardar.focus();
        });
    }

    for (const formulario of document.querySelectorAll('[data-factura]')) {
        formulario.querySelectorAll('[data-comercial-linea]').forEach(actualizarProducto);
        actualizarFactura(formulario);
        actualizarRecurrencia(formulario);
        prepararContactoFactura(formulario);
        formulario.addEventListener('input', () => actualizarFactura(formulario));
        formulario.addEventListener('change', (event) => {
            if (event.target.matches('[data-factura-producto]')) {
                actualizarProducto(event.target.closest('[data-comercial-linea]'));
            }
            actualizarFactura(formulario);
            actualizarRecurrencia(formulario);
        });
    }

    for (const formulario of document.querySelectorAll('[data-compra]')) {
        actualizarCompra(formulario);
        formulario.addEventListener('input', () => actualizarCompra(formulario));
        formulario.addEventListener('change', () => actualizarCompra(formulario));
    }
    prepararAltaProducto();

    document.addEventListener('DOMContentLoaded', () => {
        window.jQuery?.('[data-compra]')
            .on('select2:select', '[data-compra-producto]', function (evento) {
                this.selectedOptions[0].dataset.tipoProducto = evento.params.data.tipo_producto;
                actualizarCompra(this.form);
            })
            .on('select2:clear', '[data-compra-producto]', function () {
                actualizarCompra(this.form);
            });
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
        const boton = event.target.closest('[data-factura-adicional-agregar], [data-factura-adicional-quitar]');
        const formulario = boton?.closest('[data-factura]');
        if (!formulario) return;

        const contenedor = formulario.querySelector('[data-factura-adicionales]');
        const agregar = formulario.querySelector('[data-factura-adicional-agregar]');
        if (boton === agregar) {
            if (contenedor.children.length >= 5) return;
            contenedor.append(formulario.querySelector('[data-factura-adicional-plantilla]').content.cloneNode(true));
            contenedor.lastElementChild.querySelector('input').focus();
        } else {
            boton.closest('[data-factura-adicional]').remove();
            agregar.disabled = false;
            agregar.focus();
        }
        [...contenedor.children].forEach((fila, indice) => {
            for (const entrada of fila.querySelectorAll('input')) {
                entrada.name = entrada.name.replace(/^informacion_adicional\[[^\]]+\]/, 'informacion_adicional[' + indice + ']');
            }
        });
        agregar.disabled = contenedor.children.length >= 5;
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
        actualizarCompra(formulario);
    });
})();
