# Contexto: Pacientes y atención clínica

## Estado

Mapeado. Sus pantallas y datos siguen operando desde el código heredado.

## Uso

Gestiona el ciclo de atención del paciente: turnos, admisión, consulta, historia clínica, enfermería, valoración preanestésica, procedimientos e indicaciones.

### Módulos incluidos

- Historia clínica y admisión.
- Historia clínica HC4.
- Turnero.
- Preanestésico.
- Consulta médica.
- Enfermería.
- Técnicas quirúrgicas.
- Preparaciones.

## Límite técnico

El contexto recibirá identidad del paciente y empresa desde contratos institucionales. Publicará eventos clínicos cuando una atención, orden o procedimiento cambie de estado, sin permitir que Facturación modifique directamente la historia clínica.

Se conservarán las tablas clínicas actuales y sus identificadores durante toda la transición.

## Soporte

- Validar paciente, agenda, procedimiento y empresa antes de corregir una atención.
- No eliminar registros clínicos para resolver inconsistencias; utilizar los estados y trazabilidad existentes.
- Revisar permisos y relación médico-empresa cuando una opción no sea visible.

## Criterio de migración

Iniciar por consultas de solo lectura y luego por operaciones clínicas pequeñas. Las escrituras críticas requieren pruebas de autorización, empresa y trazabilidad.
