# Seguridad y permisos de v3

## Modelo observado

Las rutas internas de v3 usan sesion web y el middleware `general`. Este middleware confirma que exista un usuario, carga empresa, usuario, menu y permisos. Los permisos se almacenan en `new_db_permisos_usuario` y se relacionan con menu/submenu.

Los paneles tecnicos de documentacion y monitoreo requieren adicionalmente `id_tipo_usuario = 1`.

## Principio de menor privilegio

Un usuario debe tener acceso solamente a los procesos necesarios para su funcion. Ocultar una opcion del menu no sustituye la autorizacion en servidor.

Separar al menos las capacidades de:

- Consulta.
- Creacion.
- Modificacion.
- Anulacion.
- Aprobacion o cierre.
- Configuracion y administracion.
- Consulta de auditoria y monitoreo.

## Revision de acceso

1. Identificar usuario, empresa, cargo y responsable aprobador.
2. Revisar rol y submenus asignados en Configuraciones.
3. Probar acceso permitido y denegado directamente por URL.
4. Confirmar restricciones para anulaciones, cierres y configuraciones.
5. Registrar aprobacion, fecha y responsable.
6. Retirar accesos cuando cambia la funcion o termina la relacion laboral.

La revision debe repetirse periodicamente y ante cambios de cargo.

## Datos sensibles

No deben exponerse en Git, logs, auditorias, capturas o tickets:

- Claves y tokens.
- Contenido de `.env`.
- Certificados y claves de firma.
- Credenciales MySQL, correo o servicios externos.
- Cookies y sesiones.
- XML o documentos con informacion personal/tributaria completa.

Los logs operativos deben usar identificadores y patrones de ruta, no cuerpos completos de solicitudes.

## Altas y cambios de usuario

- Validar identidad y correo.
- Aplicar politica de clave definida por la organizacion.
- Asignar empresa y rol expresamente.
- Evitar copiar permisos amplios sin revision.
- Registrar creador, fecha y motivo.

## Incidentes de seguridad

Ante acceso indebido o posible exposicion:

1. Preservar logs y auditoria.
2. Revocar sesion, acceso o secreto comprometido.
3. Identificar alcance, empresa, usuarios y periodo.
4. Notificar al responsable autorizado.
5. Corregir sin borrar evidencia.
6. Documentar causa y control preventivo.

## Brecha pendiente

El catalogo definitivo de roles, operaciones permitidas y segregacion de funciones debe ser aprobado por responsables funcionales. La autorizacion por accion necesita una revision tecnica separada.
