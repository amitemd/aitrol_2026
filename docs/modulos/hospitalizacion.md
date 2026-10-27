# Contexto: Hospitalización

## Estado

Mapeado. La operación hospitalaria permanece en el legado.

## Uso

Controla ingresos, emergencia, camas, habitaciones, atención intrahospitalaria, farmacia y prestaciones hospitalarias asociadas al IESS.

### Módulos incluidos

- Hospitalización.
- Hospital IESS.

## Límite técnico

Hospitalización coordinará pacientes, disponibilidad, órdenes clínicas e inventario a través de contratos. Sus episodios y asignaciones de cama no podrán modificarse desde Facturación o Contabilidad.

Las tablas y relaciones actuales se conservarán; el nuevo código agregará límites transaccionales alrededor de ellas.

## Soporte

- Revisar episodio, paciente, cama, área y estado antes de realizar ajustes.
- Comprobar movimientos y fechas cuando una cama aparezca ocupada o disponible de forma incorrecta.
- No corregir saldos hospitalarios alterando directamente los registros clínicos.

## Criterio de migración

Empezar por disponibilidad y consulta de episodios. Continuar con asignaciones y altas únicamente después de cubrir concurrencia y transacciones.
