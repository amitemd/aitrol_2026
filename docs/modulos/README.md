# Documentación de módulos

Esta carpeta documenta los contextos funcionales definidos en `config/modular_architecture.php`. El estado **mapeado** significa que el alcance y las dependencias están definidos, pero la lógica continúa en el legado. El estado **en migración** indica que ya existen casos de uso o adaptadores en `app/Modules`.

## Índice

- [Identidad e institucional](identidad-institucional.md) — en migración.
- [Pacientes y atención clínica](atencion-clinica.md) — en migración.
- [Diagnóstico](diagnostico.md) — en migración.
- [Hospitalización](hospitalizacion.md) — en migración.
- [Inventario y operaciones](inventario-operaciones.md) — mapeado.
- [Facturación de ventas](facturacion-ventas.md) — en migración.
- [Contabilidad y finanzas](contabilidad-finanzas.md) — mapeado.
- [Talento humano](talento-humano.md) — mapeado.
- [Soporte e integraciones](soporte-integraciones.md) — en migración; logs, diagnóstico y auditoría disponibles.

Todos los contextos utilizan la conexión predeterminada y la estructura actual de la base. La documentación debe actualizarse junto con cada operación migrada.
