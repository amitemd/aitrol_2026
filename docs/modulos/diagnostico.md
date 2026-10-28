# Contexto: Diagnóstico

## Estado

En migración. El catálogo y la selección de diagnósticos CIE-10 ya atraviesan el contexto modular; laboratorio y procedimientos diagnósticos continúan en el legado.

La revisión de paridad del 28 de octubre de 2026 confirmó la presencia de los controladores y vistas del laboratorio base. Las entradas de recepción, control, resultados, exámenes, protocolos y agenda de laboratorio conservan sus nombres de ruta históricos.

## Uso

Administra órdenes, muestras, resultados, validaciones, diagnósticos CIE-10, procedimientos endoscópicos y sus evidencias.

### Módulos incluidos

- Laboratorio.
- Pentax y endoscopía.
- CIE-10.

## Límite técnico

Recibirá órdenes clínicas mediante contratos. Los resultados se expondrán como modelos de lectura y los cambios relevantes se comunicarán con eventos. Imágenes, videos y archivos seguirán utilizando los almacenamientos actuales hasta migrarlos de forma explícita.

## Operación migrada: búsqueda CIE-10

- **Entrada:** texto de hasta 100 caracteres desde las rutas heredadas `epicrisis.cie10_nombre` y `epicrisis.cie10_nombre2`.
- **Caso de uso:** `SearchDiagnosisCodes` busca por código o descripción y `FindDiagnosisCodeFromLabel` resuelve la selección.
- **Contrato:** `DiagnosisCatalogRepository` impide que la capa clínica dependa de Query Builder.
- **Adaptador:** `LaravelDiagnosisCatalogRepository` consulta `cie_10_3` y `cie_10_4` con parámetros enlazados.
- **Compatibilidad:** se conservan las rutas y los arreglos `value`/`id` consumidos por el autocompletado legado.
- **Datos:** usa la conexión predeterminada y no crea ni modifica tablas.

## Uso de la operación migrada

1. Escribir código o descripción en el selector CIE-10 existente.
2. Elegir una opción con formato `(CÓDIGO) DESCRIPCIÓN`.
3. El sistema valida nuevamente el código en ambos catálogos antes de devolver la descripción.

No se requiere una URL nueva: el acceso continúa desde las pantallas clínicas y de epicrisis existentes.

## Soporte

- Confirmar que la orden pertenezca al paciente y empresa correctos.
- Revisar estados de muestra, validación y publicación antes de corregir resultados.
- Para archivos faltantes, validar ruta, permisos y registro relacionado sin reemplazar evidencias originales.
- Si el autocompletado no devuelve resultados, comprobar el código directamente en `cie_10_3` y `cie_10_4` y revisar que la entrada no supere 100 caracteres.
- No concatenar texto recibido en SQL; toda nueva consulta de catálogo debe añadirse al contrato y conservar parámetros enlazados.

### Accesos verificados

- Recepción de órdenes: `orden.index`.
- Control de laboratorio: `orden.index_control`.
- Ingreso de resultados: `nuevoResultado.realizar_new_lista`.
- Catálogo de exámenes: `examen.index`.
- Protocolos: `protocolo.index`.
- Agenda de laboratorio: `agendalabs.agenda`.

Las rutas de recursos de laboratorio fueron limitadas a las acciones que existen en cada controlador. Las pantallas continúan usando las tablas y datos actuales; no se añadieron migraciones.

## Pruebas

- Unidad: normalización, límite por catálogo y extracción segura del código seleccionado.
- Integración HTTP: compatibilidad de los formatos de éxito, selección y resultado vacío.
- Contenedor: resolución del contrato hacia el adaptador de la base actual.

## Reversión

La reversión consiste en retirar la inyección de los casos de uso en `EpicrisisController`; no existe reversión de base de datos porque esta etapa no tiene migraciones.

## Criterio de migración

Extraer primero catálogos y consultas, después el ciclo orden-muestra-resultado y finalmente integraciones con equipos.
