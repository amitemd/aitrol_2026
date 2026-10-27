# Contexto: Diagnóstico

## Estado

Mapeado. Laboratorio y procedimientos diagnósticos continúan en el legado.

## Uso

Administra órdenes, muestras, resultados, validaciones, diagnósticos CIE-10, procedimientos endoscópicos y sus evidencias.

### Módulos incluidos

- Laboratorio.
- Pentax y endoscopía.
- CIE-10.

## Límite técnico

Recibirá órdenes clínicas mediante contratos. Los resultados se expondrán como modelos de lectura y los cambios relevantes se comunicarán con eventos. Imágenes, videos y archivos seguirán utilizando los almacenamientos actuales hasta migrarlos de forma explícita.

## Soporte

- Confirmar que la orden pertenezca al paciente y empresa correctos.
- Revisar estados de muestra, validación y publicación antes de corregir resultados.
- Para archivos faltantes, validar ruta, permisos y registro relacionado sin reemplazar evidencias originales.

## Criterio de migración

Extraer primero catálogos y consultas, después el ciclo orden-muestra-resultado y finalmente integraciones con equipos.
