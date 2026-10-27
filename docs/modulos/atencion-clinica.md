# Contexto: Pacientes y atención clínica

## Estado

Migración incremental en curso. La búsqueda de pacientes por nombre ya utiliza el nuevo contexto; las demás pantallas y operaciones continúan en el legado.

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

## Caso de uso migrado

`SearchPatientsByName` atiende el autocompletado utilizado por la ruta histórica `paciente/nombre`. El controlador conserva la respuesta con las claves `value` e `id`, por lo que las pantallas existentes no necesitan cambios.

### Adaptador actual

`LaravelPatientSearchRepository` consulta la tabla existente `paciente` mediante la conexión predeterminada. Excluye el paciente genérico `9999999999`, limita la respuesta a 100 resultados y aplica un orden determinista por apellidos, nombres e identificación.

La búsqueda utiliza parámetros enlazados. El texto escrito por el usuario ya no se concatena dentro de la sentencia SQL.

## Soporte

- Validar paciente, agenda, procedimiento y empresa antes de corregir una atención.
- No eliminar registros clínicos para resolver inconsistencias; utilizar los estados y trazabilidad existentes.
- Revisar permisos y relación médico-empresa cuando una opción no sea visible.
- Si un paciente no aparece en el autocompletado, buscarlo por sus nombres en el mismo orden registrado y confirmar que no sea el registro genérico.
- Un término vacío o mayor a 100 caracteres se rechaza para evitar consultas amplias o abusivas.

## Criterio de migración

La primera consulta de solo lectura ya fue extraída. Se continuará con la ficha del paciente y después con operaciones clínicas pequeñas. Las escrituras críticas requieren pruebas de autorización, empresa y trazabilidad.

## Reversión segura

La reversión consiste en devolver el método `PacienteController::nombre` a su consulta anterior y retirar el binding del repositorio. No necesita migraciones ni cambios sobre los datos.
