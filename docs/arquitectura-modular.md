# Arquitectura modular de AITROL

## Objetivo

AITROL se mantiene como una sola aplicación Laravel 13 y utiliza la misma base de datos existente. La transición modular organiza el código sin cambiar rutas, pantallas, tablas ni datos mientras cada operación se migra y prueba.

## Principios obligatorios

- La base activa configurada en `.env` continúa siendo la única fuente de datos.
- No se duplican tablas para los módulos nuevos.
- Cada módulo accede a las tablas heredadas mediante adaptadores en `Infrastructure`.
- Los controladores conservan las rutas públicas actuales durante la transición.
- Un módulo no debe usar controladores, vistas ni modelos internos de otro módulo.
- La comunicación síncrona se realiza mediante interfaces; los efectos secundarios se publicarán mediante eventos y trabajos en cola.
- Cada cambio debe conservar el filtro por empresa y las reglas de autorización existentes.

## Capas

```text
Interfaces/Http -> Application -> Domain
                         |
                         v
                 Contracts (puertos)
                         ^
                         |
              Infrastructure (adaptadores)
```

`Application` contiene casos de uso y consultas. `Domain` contendrá reglas puras del negocio. `Infrastructure` adapta Eloquent, Query Builder, SRI, archivos y servicios externos. `Interfaces` recibirá HTTP, consola o procesos programados.

## Orden de migración

El orden ejecutable se encuentra en `config/modular_architecture.php`:

1. Identidad e institucional.
2. Pacientes y atención clínica.
3. Diagnóstico.
4. Hospitalización.
5. Inventario y operaciones.
6. Facturación electrónica.
7. Contabilidad y finanzas.
8. Talento humano.
9. Soporte e integraciones.

Los 34 módulos inventariados en `config/modules.php` están asignados exactamente una vez a estos contextos. Una prueba local valida esa cobertura y evita módulos duplicados u olvidados.

## Atlas operativo

La ruta autenticada `/modulos-operativos` materializa el inventario completo mediante un puerto de aplicación y un adaptador de configuración. No consulta ni modifica tablas: utiliza `config/modules.php` y `config/modular_architecture.php` como catálogo técnico y comprueba las rutas registradas en Laravel.

Los primeros puntos de entrada verticales son:

| Área operativa | Ruta nueva | Contextos relacionados |
| --- | --- | --- |
| Parte médica, agenda y preagenda | `/modulos-operativos/parte-medica` | Pacientes y atención clínica |
| Pacientes y biopsias | `/modulos-operativos/pacientes-biopsias` | Pacientes y atención clínica, Diagnóstico |
| Laboratorio | `/modulos-operativos/laboratorio` | Diagnóstico |
| Administración, empresas y usuarios | `/modulos-operativos/administracion` | Identidad e institucional |
| Caja, bancos y cierres | `/modulos-operativos/caja-bancos-cierres` | Contabilidad y finanzas, Facturación |

Cada entrada sigue el flujo `Controlador -> Consulta de aplicación -> Definición de dominio -> Puerto -> Adaptador Laravel`. El botón final abre la ruta heredada correspondiente; por tanto, no se duplican formularios, escrituras ni datos durante esta primera etapa.

El atlas diferencia dos conceptos:

- **Contexto delimitado:** frontera estable de reglas de negocio. Hay nueve y cubren todo el legado.
- **Área operativa:** agrupación de accesos que el usuario reconoce en su trabajo diario. Puede cruzar más de un contexto sin acoplar sus reglas internas.

La comprobación marca un acceso como listo únicamente cuando el nombre de ruta existe y su controlador o acción es invocable. Esto permite avanzar módulo por módulo sin declarar una migración completa antes de que sea verificable.

## Estrategia de convivencia

1. Identificar una operación del controlador legado.
2. Crear un contrato de lectura o escritura en el módulo correspondiente.
3. Implementar el contrato usando las tablas actuales.
4. Crear el caso de uso en `Application`.
5. Inyectar el caso de uso en el controlador existente.
6. Ejecutar pruebas del contrato y de compatibilidad de la ruta.
7. Retirar únicamente el bloque legado sustituido y verificado.

## Estado inicial

- **Identidad e institucional:** la empresa activa y las empresas accesibles ya atraviesan casos de uso y un adaptador que consulta `empresa` y `usuario_empresa` en la conexión predeterminada. La fachada histórica se conserva para compatibilidad.
- **Pacientes y atención clínica:** el autocompletado y el directorio paginado de pacientes atraviesan contratos y adaptadores seguros sobre `paciente`; el listado obtiene creador y correo sin consultas desde Blade.
- **Diagnóstico:** la búsqueda y selección CIE-10 atraviesa un contrato de catálogo que consulta `cie_10_3` y `cie_10_4` con parámetros enlazados, manteniendo las respuestas del autocompletado existente.
- **Hospitalización:** la pantalla de pacientes hospitalizados obtiene episodios, paciente y sala mediante un único adaptador sobre las tablas actuales, conservando rutas, filtros y vista.
- **Inventario y operaciones:** la consulta de existencias atraviesa un puerto de lectura y un adaptador sobre `inv_inventario`, `producto`, `marca` y `bodega`. Conserva los filtros existentes y elimina las consultas repetidas por fila.
- **Facturación de ventas:** la consulta de una factura para edición ya atraviesa un puerto de lectura y un adaptador que consulta `ct_ventas` en la conexión predeterminada. La vista recibe el mismo objeto compatible que utilizaba antes.
- **Contabilidad y finanzas:** la edición de asientos obtiene el resumen por cuenta mediante un puerto de lectura compatible con `ONLY_FULL_GROUP_BY`, usando `ct_asientos_detalle` y `plan_cuentas_empresa` sin modificar datos.
- **Talento humano:** el directorio de empleados usa un caso de uso paginado y un adaptador sobre `empleado`. La búsqueda validada se procesa por POST y conserva filtros en sesión sin exponerlos en la URL.
- **Soporte e integraciones:** los visores administrativos de logs y auditoría atraviesan puertos de lectura. Los adaptadores consultan `storage/logs`, `log_usuario` y `new_db_auditorias` sin alterar la estructura ni los datos existentes.

## Regla para documentación futura

Cada operación migrada debe actualizar el documento de su módulo con: finalidad, usuarios, tablas, contratos, eventos, permisos, procedimiento de soporte, pruebas y riesgos de reversión.
