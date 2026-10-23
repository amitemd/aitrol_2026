# Matriz de madurez de v3

## Objetivo

La matriz mide que tan controlado y sostenible es el sistema. No representa solamente el porcentaje de pantallas terminadas.

Ruta de consulta: `/v3/matriz-madurez`.

## Escala

| Rango | Interpretacion |
|---:|---|
| 0-20 | Inicial o critico |
| 21-40 | En desarrollo o en riesgo |
| 41-60 | Control parcial |
| 61-80 | Gestionado |
| 81-100 | Optimizado |

## Linea base actual

| Dimension | Peso | Madurez | Aporte ponderado |
|---|---:|---:|---:|
| Cobertura funcional | 25% | 74% | 18,50% |
| Logica de negocio y datos | 15% | 62% | 9,30% |
| Seguridad y autorizacion | 15% | 32% | 4,80% |
| Arquitectura y mantenibilidad | 15% | 36% | 5,40% |
| Pruebas y calidad | 10% | 20% | 2,00% |
| Instalacion y despliegue | 10% | 55% | 5,50% |
| Documentacion | 5% | 74% | 3,70% |
| Operacion y monitoreo | 5% | 70% | 3,50% |
| **Total** | **100%** |  | **52,70% (53%)** |

## Formula

Para cada dimension:

`aporte = peso * madurez / 100`

La madurez global es la suma de todos los aportes. La interfaz redondea el resultado al entero mas cercano.

## Auditoria de modulos existentes

La ruta de la matriz tambien presenta el inventario de 23 modulos y servicios publicados dentro de `v3`.
La revision del 15/07/2026 fue estatica y se limito a rutas, middleware, controladores, modelos, vistas,
transacciones, validaciones, pruebas y patrones de riesgo del repositorio. No se modificaron los flujos
auditados ni se considero una pantalla como certificada solo por existir.

| Modulo | Madurez | Dictamen | Prioridad | Primer paso |
|---|---:|---|:---:|---|
| Administracion y menu | 60% | Sirve con controles | P2 | Probar acceso por rol y errores. |
| Registro y planes | 52% | Sirve con restricciones | P1 | Probar CRUD y accesos con perfiles permitidos y denegados. |
| Configuraciones contables | 48% | Sirve con riesgo | P1 | Proteger firma, secuenciales, roles y cuentas. |
| Configuracion del sistema | 52% | Sirve con controles | P2 | Exigir perfil administrador en cambios sensibles. |
| Utilidades | 42% | Sirve con riesgo | P1 | Endurecer carga, lectura y descarga de archivos. |
| Contabilidad | 38% | Sirve con riesgo | P1 | Probar asientos, mayor, cierre y anulacion. |
| Proveedores | 46% | Sirve con riesgo | P2 | Conciliar compras, pagos, retenciones y saldo. |
| Clientes | 50% | Sirve con riesgo | P2 | Conciliar cartera, anticipos, cruces y notas. |
| Ventas | 40% | Sirve con riesgo | P1 | Certificar venta, asiento, inventario y anulacion. |
| Compras | 38% | Sirve con riesgo | P1 | Certificar compra y anulacion integral. |
| Caja y bancos | 40% | Sirve con riesgo | P1 | Probar saldos, conciliacion y reversos. |
| Inventario | 44% | Sirve con riesgo | P1 | Cuadrar existencia, kardex y contabilidad. |
| Activos fijos | 46% | Sirve con riesgo | P2 | Conciliar auxiliar, depreciacion y mayor. |
| Guias de remision | 50% | Sirve con riesgo | P1 | Probar XSD, firma, SRI, recuperacion y anulacion. |
| Tributario | 42% | Sirve con riesgo | P1 | Estabilizar secuenciales, idempotencia y reproceso. |
| Reportes | 38% | Sirve con riesgo | P1 | Certificar totales contra contabilidad. |
| Recursos humanos | 35% | Parcial | P2 | Definir alcance y certificar una nomina completa. |
| Importaciones | 40% | Sirve con riesgo | P2 | Probar reimportacion sin duplicados. |
| Opciones de documentos | 38% | Sirve con riesgo | P1 | Unificar estados y recuperacion electronica. |
| APIs v3 | 55% | Sirve con restricciones | P1 | Ejecutar `v3:api-check` y PHPUnit v3 en el servidor. |
| Documentacion interna | 78% | Sirve | P3 | Mantener responsables y evidencias. |
| Operacion y monitoreo | 75% | Sirve | P3 | Ensayar alertas, respaldo e incidente. |
| Matriz de madurez | 78% | Sirve | P3 | Reevaluar solo con evidencia reproducible. |

Resumen modular actualizado: promedio simple aproximado 49%; 0 modulos P0, 13 P1, 7 P2 y 3 P3.
El promedio modular es informativo y no reemplaza la madurez global ponderada por dimensiones.

## Orden de mejora aprobado

1. **APIs v3 (P1):** probar el contrato autenticado con dos empresas y definir tokens solamente si aparecen consumidores externos.
2. **Registro y planes (P0):** proteger rutas, autorizar operaciones y separar consultas de escrituras.
3. **Tributario (P1):** certificar secuenciales, idempotencia, XSD, consulta SRI y reproceso.
4. **Nucleo financiero (P1):** probar Contabilidad, Ventas, Compras y Caja/Bancos antes de refactorizar.

Seleccionar un modulo como prioridad no autoriza a cambiar su comportamiento sin conocer dependencias y consumidores.
Primero se documenta el contrato actual, luego se crean pruebas y finalmente se realiza el cambio minimo reversible.

## Actualizacion obligatoria por cada cambio

Toda revision o modificacion dentro de `v3` debe agregar un elemento a `MatrizMadurez::revisiones()` con:

- Fecha y modulo afectado.
- Tipo: auditoria, revision, correccion, mejora o modificacion.
- Cambio revisado.
- Evidencia obtenida: prueba, diff, documento, metrica o validacion reproducible.
- Resultado y siguiente limitacion conocida.
- Madurez anterior y nueva. Si no existe evidencia suficiente, el porcentaje se mantiene.

La matriz muestra la ultima revision de cada modulo y un historial visible. El cambio no se considera terminado si
la matriz, su evidencia y la documentacion afectada no fueron actualizadas.

### Significado del dictamen modular

- **Sirve:** implementacion identificable con controles y pruebas parciales; debe mantenerse.
- **Sirve con controles:** utilizable, sujeto a validar permisos y escenarios operativos.
- **Sirve con riesgo:** tiene flujo implementado, pero faltan controles o pruebas criticas.
- **Parcial:** el alcance esta incompleto o no puede certificarse con la evidencia disponible.
- **No apto para produccion:** existe un riesgo inmediato que debe cerrarse antes de usarlo.

## Evidencia para actualizar un valor

Un porcentaje solo debe subir cuando existe evidencia comprobable, por ejemplo:

- Codigo implementado y revisado.
- Prueba automatizada o acta de prueba reproducible.
- Documento vigente y accesible.
- Configuracion versionada sin secretos.
- Metrica operativa con periodo definido.
- Procedimiento ejecutado con resultado registrado.

Una intencion, tarea pendiente o pantalla sin datos reales no cuenta como control implementado.

## Historial

| Fecha | Cambio |
|---|---|
| 15/07/2026 | Linea base inicial: 42%. |
| 15/07/2026 | Panel y middleware de operacion: operacion sube de 25% a 60%; total 43%. |
| 15/07/2026 | Centro y conjunto documental: documentacion sube de 5% a 55%; total 46%. |
| 15/07/2026 | Umbrales y comando de salud: operacion sube de 60% a 70%; total redondeado 46%. |
| 15/07/2026 | Se confirma separacion intencional Git-servidor: despliegue sube de 12% a 45%; total 50%. |
| 15/07/2026 | Manuales funcionales, seguridad, despliegue, soporte y datos: documentacion sube de 55% a 70%; total 50%. |
| 15/07/2026 | Suite aislada de v3 iniciada: pruebas suben de 8% a 20%; total 52%. |
| 15/07/2026 | Preflight y checklist seguros: instalacion/despliegue sube de 45% a 55%; total 53%. |
| 15/07/2026 | Auditoria estatica de 23 modulos agregada con dictamen, brechas y prioridades P0-P3; la ponderacion global no cambia. |
| 15/07/2026 | Manual funcional contrastado con 110 entradas reales: documentacion sube de 70% a 74%; total 52,70% (53%). |
| 15/07/2026 | Se aprueba iniciar por APIs v3 sin cambiar endpoints hasta identificar consumidores y crear pruebas de contrato. |
| 15/07/2026 | Se agrega historial obligatorio por revision/modificacion y ultima revision por modulo. |
| 15/07/2026 | Revision API: solo `/api/v1/tributario` esta cargado; permanece en 20% hasta aislar empresa, autenticar y probar contratos. |
| 15/07/2026 | Registro y planes: rutas protegidas, alta solo por POST, validacion y respuesta sin campos sensibles; sube de 28% a 45%. |
| 15/07/2026 | API tributaria: sesion v3 y aislamiento por empresa en todos los documentos; sube de 20% a 35%, pendiente pruebas de contrato. |
| 15/07/2026 | Registro y planes: las URL antiguas reutilizan las pantallas reales de Planes, Usuarios y Empresas; sube de 45% a 52%. |
| 15/07/2026 | APIs v3: 401 JSON, aislamiento por empresa y tipo documental, prototipos inactivos sin efectos; sube de 35% a 50% y pasa a P1. |
| 15/07/2026 | APIs v3: certificador de solo lectura para contrato, esquema, maestros y dos empresas; sube de 50% a 55%, pendiente ejecucion en servidor. |
| 15/07/2026 | APIs v3: ficha visible sincronizada con la ultima revision; la pantalla muestra 55% y una prueba evita futuras diferencias. |
