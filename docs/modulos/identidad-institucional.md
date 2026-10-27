# Contexto: Identidad e institucional

## Estado

Mapeado. La implementación continúa en el legado hasta que sus operaciones sean extraídas con pruebas.

## Uso

Administra el acceso al sistema y los datos maestros que identifican la operación: usuarios, perfiles, empresas, sucursales, médicos, seguros y configuración hospitalaria.

### Módulos incluidos

- Núcleo institucional.
- Administración hospitalaria.
- Plataforma V3.

## Límite técnico

Este contexto será propietario de autenticación, autorización, empresa activa y catálogos institucionales. Los demás contextos deberán solicitar estos datos mediante contratos y no consultar controladores o vistas institucionales.

La implementación usará las tablas actuales mediante repositorios. No se duplicarán usuarios, empresas ni permisos.

## Soporte

- Ante un acceso denegado, revisar usuario, estado, tipo de usuario y permisos asignados.
- Ante datos de otra empresa, validar la empresa activa de la sesión y los filtros `id_empresa`.
- Antes de modificar catálogos maestros, comprobar qué módulos los consumen.

## Criterio de migración

Primero deben aislarse la empresa activa, la consulta de usuario y la autorización. Después se extraerán los mantenimientos de catálogos, conservando las rutas actuales.
