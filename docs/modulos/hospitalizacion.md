# Contexto: Hospitalización

## Estado

En migración. La consulta de pacientes hospitalizados ya atraviesa el contexto modular; asignaciones, altas y el resto de la operación permanecen en el legado.

## Uso

Controla ingresos, emergencia, camas, habitaciones, atención intrahospitalaria, farmacia y prestaciones hospitalarias asociadas al IESS.

### Módulos incluidos

- Hospitalización.
- Hospital IESS.

## Límite técnico

Hospitalización coordinará pacientes, disponibilidad, órdenes clínicas e inventario a través de contratos. Sus episodios y asignaciones de cama no podrán modificarse desde Facturación o Contabilidad.

Las tablas y relaciones actuales se conservarán; el nuevo código agregará límites transaccionales alrededor de ellas.

## Operación migrada: pacientes hospitalizados

- **Entrada:** fechas, sala, cédula y nombre desde `hospitalizacion.master` y `hospitalizacion.buscar_hospitalizado`.
- **Caso de uso:** `ListHospitalizedPatients` normaliza los filtros y delega la lectura.
- **Contrato:** `HospitalizedPatientRepository` separa la consulta hospitalaria del controlador legado.
- **Adaptador:** `LaravelHospitalizedPatientRepository` consulta `ho_traspaso_sala008`, `paciente` y `sala` en una sola lectura.
- **Compatibilidad:** se conserva la vista `hospital/habitacion/master`, sus rutas y los campos que utiliza.
- **Rendimiento:** el paciente y la sala se obtienen en la misma consulta, evitando una consulta adicional por cada fila.
- **Datos:** se usa la conexión predeterminada; no se crean tablas ni se alteran episodios.

## Uso de la operación migrada

1. Ingresar desde la opción existente de Hospitalización.
2. La pantalla inicial muestra los pacientes en habitación del día actual.
3. Para filtrar, indicar un intervalo válido y opcionalmente sala, cédula o nombres.
4. Las acciones de atención y formularios conservan los mismos enlaces del sistema legado.

Los nombres aceptan búsquedas parciales con uno o varios términos. Los comodines escritos por el usuario se tratan como texto para evitar consultas amplias accidentales.

## Soporte

- Revisar episodio, paciente, cama, área y estado antes de realizar ajustes.
- Comprobar movimientos y fechas cuando una cama aparezca ocupada o disponible de forma incorrecta.
- No corregir saldos hospitalarios alterando directamente los registros clínicos.
- Ante un paciente ausente, revisar `ho_traspaso_sala008.estado = 2`, la relación con `paciente` y la sala asociada.
- Si el filtro no responde, validar que las fechas utilicen `AAAA-MM-DD` y que la fecha final no sea anterior a la inicial.
- No eliminar el adaptador para consultar modelos directamente desde nuevas pantallas; las siguientes operaciones deben reutilizar el contrato o declarar uno específico.

## Pruebas

- Unidad: normalización de filtros sin cambiar los criterios clínicos.
- Contrato: transformación del resultado modular al objeto esperado por la vista heredada.
- Contenedor: resolución del contrato hacia el adaptador de la base actual.

## Reversión

Puede revertirse la inyección del caso de uso en `HospitalizacionController` sin modificar datos. Esta etapa no incluye migraciones ni escrituras.

## Criterio de migración

Empezar por disponibilidad y consulta de episodios. Continuar con asignaciones y altas únicamente después de cubrir concurrencia y transacciones.
