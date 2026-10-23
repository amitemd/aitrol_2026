# Checklist de despliegue v3

Este formato debe completarse para cada publicacion. No contiene comandos destructivos ni valores sensibles.

## Identificacion

- [ ] Cambio o ticket:
- [ ] Fecha y ventana:
- [ ] Responsable tecnico:
- [ ] Aprobador:
- [ ] Modulos v3 afectados:
- [ ] Empresas afectadas:

## Compatibilidad obligatoria

- [ ] PHP del servidor: `7.1.33`.
- [ ] Laravel del servidor: `5.4.x`.
- [ ] El codigo no usa sintaxis posterior a PHP 7.1.
- [ ] `php artisan v3:deploy-check` finaliza aprobado en el servidor.

El comando es de solo lectura. No instala, copia, elimina, migra ni limpia cache.

## Alcance de archivos

- [ ] Existe una lista exacta de archivos que se publicaran.
- [ ] El diff contiene exclusivamente cambios autorizados de v3.
- [ ] `.env` queda fuera del traslado.
- [ ] `composer.json`, `composer.lock` y `vendor` no se sobrescriben sin plan expreso.
- [ ] `bootstrap`, `public`, `storage`, cargas y logs del servidor quedan protegidos.
- [ ] Cualquier cambio de dependencia esta documentado y aprobado por separado.
- [ ] Cualquier cambio MySQL esta documentado, respaldado y aprobado por separado.

## Pruebas previas

- [ ] Sintaxis PHP validada.
- [ ] Suite `--group v3` ejecutada en un ambiente con dependencias correctas.
- [ ] `php artisan v3:api-check --json` devuelve `estado: aprobado` cuando cambia la API tributaria.
- [ ] Caso correcto, acceso denegado y error esperado verificados.
- [ ] No se utilizaron datos ni credenciales de produccion en pruebas.
- [ ] Existe prueba de humo para cada modulo afectado.

## Respaldo y retorno

- [ ] Respaldo de archivos identificado y comprobado.
- [ ] Respaldo de datos cuando el cambio lo requiere.
- [ ] Punto exacto de retorno definido.
- [ ] Responsable autorizado para decidir el retorno.
- [ ] Tiempo maximo de observacion acordado.

## Validacion posterior

- [ ] Inicio de sesion y acceso v3 correctos.
- [ ] `/v3/operacion-monitoreo` sin alertas nuevas.
- [ ] `php artisan v3:health-check` saludable.
- [ ] Pruebas de humo aprobadas.
- [ ] Logs y auditoria sin errores nuevos.
- [ ] Saldos, asientos, inventario o documentos revisados cuando corresponda.
- [ ] Evidencia y hora de cierre registradas.

## Resultado

- [ ] Aprobado.
- [ ] Retornado.
- [ ] Pendiente de correccion.

Observaciones:
