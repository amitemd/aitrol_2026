# Contexto: Identidad e institucional

## Estado

Migración incremental en curso. La selección de empresa ya atraviesa el nuevo contexto; usuarios, permisos y demás catálogos continúan en el legado.

## Uso

Administra el acceso al sistema y los datos maestros que identifican la operación: usuarios, perfiles, empresas, sucursales, médicos, seguros y configuración hospitalaria.

### Módulos incluidos

- Núcleo institucional.
- Administración hospitalaria.
- Plataforma V3.

## Límite técnico

Este contexto será propietario de autenticación, autorización, empresa activa y catálogos institucionales. Los demás contextos deberán solicitar estos datos mediante contratos y no consultar controladores o vistas institucionales.

La implementación usará las tablas actuales mediante repositorios. No se duplicarán usuarios, empresas ni permisos.

## Caso de uso migrado

`ListAccessibleCompanies` consulta las empresas habilitadas para el usuario. Los administradores reciben todas las empresas activas y los demás usuarios únicamente las relacionadas en `usuario_empresa`.

`FindCurrentCompany` valida que el identificador guardado en la sesión pertenezca al conjunto accesible. La clase histórica `CompanyAccess` se conserva como fachada de compatibilidad para que controladores, middleware y vistas sigan funcionando sin cambios.

### Adaptador actual

`LaravelCompanyAccessRepository` utiliza la conexión predeterminada y consulta las tablas existentes `empresa` y `usuario_empresa`. Mantiene el orden por prioridad, nombre comercial e identificador usado anteriormente.

## Soporte

- Ante un acceso denegado, revisar usuario, estado, tipo de usuario y permisos asignados.
- Ante datos de otra empresa, validar la empresa activa de la sesión y los filtros `id_empresa`.
- Antes de modificar catálogos maestros, comprobar qué módulos los consumen.
- Si la empresa activa desaparece del selector, validar `empresa.estado` y la relación en `usuario_empresa`.
- Si un administrador no ve una empresa, comprobar que la empresa se encuentre activa; el administrador no depende de `usuario_empresa`.

## Criterio de migración

La empresa activa y el acceso por usuario ya están aislados. El siguiente paso es extraer autorización y mantenimientos de catálogos, conservando las rutas actuales.

## Reversión segura

La reversión consiste en devolver las consultas a la fachada `CompanyAccess` y retirar el binding del repositorio. No requiere migraciones ni cambios de datos.
